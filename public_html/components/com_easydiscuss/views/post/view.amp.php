<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

class EasyDiscussViewPost extends EasyDiscussView
{
	/**
	 * Renders the post view for a discussion
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public function display($tpl = null)
	{
		$id = $this->input->get('id', 0, 'int');

		if (!$id) {
			throw ED::exception(JText::_('COM_EASYDISCUSS_INVALID_ID_PROVIDED'), ED_MSG_ERROR);
		}

		if (!$this->config->get('main_amp')) {
			return $this->app->redirect(EDR::_('index.php?option=com_easydiscuss&view=post&id=' . $id, false));
		}

		$post = ED::post($id);

		// Ensure that the viewer can view the post
		if (!$post->canView($this->my->id) || !$post->isPublished() || !$post->isQuestion()) {
			throw ED::exception(JText::_('COM_EASYDISCUSS_SYSTEM_POST_NOT_FOUND'), ED_MSG_ERROR);
		}

		// Determine if user are allowed to view the discussion item that belong to another cluster.
		if ($post->isCluster()) {
			$easysocial = ED::easysocial();

			if (!$easysocial->isGroupAppExists()) {
				throw ED::exception(JText::_('COM_EASYDISCUSS_SYSTEM_INSUFFICIENT_PERMISSIONS'), ED_MSG_ERROR);
			}

			$cluster = $easysocial->getCluster($post->cluster_id, $post->getClusterType());

			if (!$cluster->canViewItem()) {
				throw ED::exception(JText::_('COM_EASYDISCUSS_SYSTEM_INSUFFICIENT_PERMISSIONS'), ED_MSG_ERROR);
			}
		}

		// Get the post created date
		$date = ED::date($post->created);
		$dateFormat = $date->getDateFormat(JText::_('DATE_FORMAT_LC1'));

		$post->date = JHtml::date($post->created, $dateFormat);

		// Get the tags for this discussion
		$tags = $post->getTags();

		// Get the answer for this discussion.
		$answer = $post->getAcceptedReply();

		$url = EDR::_('index.php?option=com_easydiscuss&view=post&id=' . $post->id, false);

		$ampContent = $post->getContent(false, false, true, true, true);

		// Simulate onAfterRender since amp page does not trigger this
		// Replace index.php URI by SEF URI.
		if (strpos($ampContent, 'href="index.php?') !== false) {
			preg_match_all('#href="index.php\?([^"]+)"#m', $ampContent, $matches);

			foreach ($matches[1] as $urlQueryString) {
				$ampContent = str_replace(
					'href="index.php?' . $urlQueryString . '"',
					'href="' . trim('', '/') . JRoute::_('index.php?' . $urlQueryString) . '"',
					$ampContent
				);
			}
		}

		$options = [];
		$options['sort'] = $this->config->get('layout_replies_sorting');
		$options['limit'] = $this->config->get('layout_replies_list_limit');
		$options['limitstart'] = $this->app->input->get('limitstart', 0);

		$replies = $post->getReplies($options);

		$post->getAssignment();

		// Retrieve Google Adsense codes
		$adsense = ED::adsense()->ampHtml($post);

		$jConfig = ED::jConfig();

		// RTL compatibility
		$lang = JFactory::getLanguage();

		$socialEnabled = $this->socialIsEnabled();

		$themes = ED::themes();
		$themes->set('ampContent', $ampContent);
		$themes->set('jConfig', $jConfig);
		$themes->set('url', $url);
		$themes->set('answer', $answer);
		$themes->set('post', $post);
		$themes->set('langTag', $lang->getTag());
		$themes->set('isRtl', $lang->isRTL());
		$themes->set('tags', $tags);
		$themes->set('socialEnabled', $socialEnabled);
		$themes->set('replies', $replies);
		$themes->set('adsense', $adsense);

		$html = $themes->output('site/post/item/amp');

		echo $html;
		exit;
	}

	/**
	 * Determine if the social buttons enabled or not
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	private function socialIsEnabled()
	{
		$type = $this->config->get('social_buttons_type');
		$facebook = $this->config->get('integration_facebook_like_send') && $this->config->get('integration_facebook_like_appid');
		$twitter = $this->config->get('integration_twitter_button');
		$linkedIn = $this->config->get('integration_linkedin');

		// Consider as enabled if one of it is enabled
		if ($type == 'default' && ($facebook || $twitter || $linkedIn)) {
			return true;
		}

		$code = $this->config->get('addthis_pub_id');
		$inlineId = $this->config->get('inline_widget_id');
		$floatingId = $this->config->get('floating_widget_id');

		if ($type == 'addthis' && $code && ($inlineId || $floatingId)) {
			return true;
		}

		return false;
	}
}