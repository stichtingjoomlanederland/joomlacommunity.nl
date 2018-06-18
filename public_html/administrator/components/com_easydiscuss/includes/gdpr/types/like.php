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

class EasyDiscussGdprLike extends EasyDiscussGdprAbstract
{
	public $type = 'like';

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

		$model = ED::model('likes');
		$items = $model->getLikesGDPR($this->userId, $options);

		if (!$items) {
			$adapter->tab->finalize();
			return true;
		}

		if ($items) {
			foreach ($items as $like) {

				$item = $adapter->getTemplate($like->id, $adapter->type);

				// Load the post
				$post = ED::post($like->content_id);

				if (!$post->id) {
					continue;
				}

				$item->created = $like->created;
				$item->intro = $this->getIntro($post, $like->created);

				$adapter->tab->addItem($item);
			}
		}

		return true;
	}

	/**
	 * Main function to process user like data for GDPR download.
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

		$model = ED::model('likes');
		$items = $model->getLikesGDPR($this->userId, $options);

		if (!$items) {
			return $this->tab->finalize();
		}

		foreach ($items as $like) {
			$item = $this->getTemplate($like->id, $this->type);

			// Load the post
			$post = ED::post($like->content_id);

			if (!$post->id) {
				continue;
			}

			$item->created = $like->created;
			$item->intro = $this->getIntro($post, $like->created);

			$this->tab->addItem($item);
		}
	}

	/**
	 * Generate intro for likes item
	 *
	 * @since   4.1
	 * @access  public
	 */
	public function getIntro($post, $created)
	{
		$config = ED::config();

		$date = ED::date($created);
		$intro = JText::sprintf('COM_ED_GDPR_LIKED_' . strtoupper($post->getPostItemType()), $post->getTitle(), $post->id);

		$postType = $post->isReply() ? JText::_('COM_ED_GDPR_POSTTYPE_REPLY') : JText::_('COM_ED_GDPR_POSTTYPE_POST');

		ob_start();
		?>
		<div class="gdpr-item__desc">
			<?php echo $intro; ?>
		</div>
		<div class="gdpr-item__meta">
			<?php echo $date->format($this->getDateFormat());?>
		</div>
		<div class="gdpr-item__label">
			<span class="gdpr-label"><?php echo $postType;?></span>
		</div>
		<?php
		$contents = ob_get_contents();
		ob_end_clean();

		return $contents;
	}
}
