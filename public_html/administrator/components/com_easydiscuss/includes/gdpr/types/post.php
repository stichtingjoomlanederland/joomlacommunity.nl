<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 - 2018 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

class EasyDiscussGdprPost extends EasyDiscussGdprAbstract
{
	public $type = 'post';

	/**
	 * Event trigger to process user's comments for GDPR download on EasySocial
	 *
	 * @since 4.1
	 * @access public
	 */
	public function onEasySocialGdprExport(SocialGdprSection &$section, SocialGdprItem $adapter)
	{
		// manually set type here.
		$adapter->type = $section->key . '_' . $this->type;

		// create tab in section
		$adapter->tab = $section->createTab($adapter);

		$limit = $this->getLimit();

		// Get a list of ids that are already processed
		$ids = $adapter->tab->getProcessedIds();

		$options = array('limit' => $limit);
		if ($ids) {
			$options['exclude'] = $ids;
		}

		$model = ED::model('posts');
		$items = $model->getPostsGDPR($this->userId, $options);

		if (!$items) {
			// for comments, we always finalize.
			$adapter->tab->finalize();
			return true;
		}

		if ($items) {
			foreach ($items as $post) {

				$item = $adapter->getTemplate($post->id, $adapter->type);

				$item->created = $post->created;
				$item->title =  $post->getTitle();
				$item->intro = $this->getIntro($post);
				$item->content = $this->getContent($post, $item);
				$item->view = true;

				$adapter->tab->addItem($item);
			}
		}

		return true;
	}


	/**
	 * Main function to process user post data for GDPR download.
	 *
	 * @since	4.1
	 * @access	public
	 */
	public function execute(EasyDiscussGdprSection &$section)
	{
		$this->tab = $section->createTab($this);

		$limit = $this->getLimit();

		// Get a list of ids that are already processed
		$ids = $this->tab->getProcessedIds();

		$options = array('limit' => $limit);
		if ($ids) {
			$options['exclude'] = $ids;
		}

		$model = ED::model('posts');
		$items = $model->getPostsGDPR($this->userId, $options);

		if (!$items) {
			return $this->tab->finalize();
		}

		foreach ($items as $post) {
			$item = $this->getTemplate($post->id, $this->type);

			$item->created = $post->created;
			$item->title =  $post->getTitle();
			$item->intro = $this->getIntro($post);
			$item->content = $this->getContent($post, $item);
			$item->view = true;

			$this->tab->addItem($item);
		}
	}

	/**
	 * Method to construct the intro
	 *
	 * @since	4.1.0
	 * @access	private
	 */
	private function getIntro($post)
	{
		$config = ED::config();

		$intro = $post->getContent();
		$intro = strip_tags($intro);
		$intro = JString::substr($intro, 0, $config->get('layout_introtextlength')) . JText::_('COM_EASYDISCUSS_ELLIPSES');

		$postType = $post->isReply() ? JText::_('COM_ED_GDPR_POSTTYPE_REPLY') : JText::_('COM_ED_GDPR_POSTTYPE_POST');
		$date = ED::date($post->created);

		ob_start();
		?>
		<div class="gdpr-item__desc">
			<?php echo $intro; ?>
		</div>
		<div class="gdpr-item__meta">
			<?php echo $date->format($this->getDateFormat());?>
		</div>
		<div class="gdpr-item__label">
			<span class="gdpr-label"><?php echo JString::strtoupper($postType);?></span>
		</div>
		<?php
		$contents = ob_get_contents();
		ob_end_clean();

		return $contents;
	}

	/**
	 * Method to construct the content of the gdpr content
	 *
	 * @since	4.1.0
	 * @access	private
	 */
	private function getContent($post, $item)
	{
		$content = $this->processContent($post, $item);

		ob_start();
		?>
		<div class="gdpr-item__desc">
			<?php echo $content; ?>

			<?php if (isset($item->attachments) && $item->attachments) { ?>
			<hr />

			<ul>
			<?php foreach ($item->attachments as $key => $attachment) { ?>
				<li>
					<a href="{%MEDIA%}"><?php echo $attachment; ?></a>
				</li>
			<?php } ?>
			</ul>
			<?php } ?>
		</div>
		<?php
		$contents = ob_get_contents();
		ob_end_clean();

		return $contents;
	}

	/**
	 * Method to process and normalize the content
	 *
	 * @since	4.1.0
	 * @access	private
	 */
	private function processContent($post, &$item)
	{
		// We do not want to preprocessing the attachment
		$content = $post->getContent(false, true, false);
		$content = ED::parser()->convert2validImgLink($content);
		$content = ED::parser()->normliseBBCode($content);

		$content = $this->processAttachments($content, $post, $item);

		return $content;
	}

	/**
	 * Process the attachments and attachments path within the content
	 *
	 * @since	4.1.0
	 * @access	private
	 */
	private function processAttachments($content, $post, &$item)
	{
		$source = array();
		$sourceFilename = array();
		$processedMedia = array();

		$item->attachments = array();

		// We replace the media in the content first to ensure the ordering is correct.
		preg_match_all('/\[attachment\](.*?)\[\/attachment\]/ims', $content, $matches);

		if ($matches && isset($matches[0]) && $matches[0]) {
			$codes = $matches[0];
			$files = $matches[1];
			$i = 0;

			foreach ($files as $title) {
				$table = ED::table('Attachments');
				$table->load(array('uid' => $post->id, 'title' => $title));

				if ($table->id) {
					$attachment = ED::attachment($table);
					$storagePath = $attachment->getStoragePath(true);
					$filepath = $storagePath . '/' . $attachment->table->path;
					$filename = $attachment->table->title;

					$source[] = $attachment->table->storage . ':' . $filepath;
					$sourceFilename[] = $filename;
					$processedMedia[] = $attachment->table->path;

					$code = $codes[$i];

					$content = JString::str_ireplace($code, '<img src="{%MEDIA%}" alt="' . $filename . '" height="auto" width="100%">', $content);
				}

				$i++;
			}
		}

		$attachments = $post->getAttachments();

		// Process the rest of attachment
		foreach ($attachments as $attachment) {
			$filename = $attachment->table->title;
			$storagePath = $attachment->getStoragePath(true);
			$filepath = $storagePath . '/' . $attachment->table->path;

			$source[] = $attachment->table->storage . ':' . $filepath;
			$sourceFilename[] = $filename;

			$item->attachments[] = $filename;
		}

		$item->source = $source;
		$item->sourceFilename = $sourceFilename;

		return $content;
	}
}
