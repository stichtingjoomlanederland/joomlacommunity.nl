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

class EasyDiscussGdprComment extends EasyDiscussGdprAbstract
{
	public $type = 'comment';

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
		$items = $model->getCommentsGDPR($this->userId, $options);

		if (!$items) {
			// for comments, we always finalize.
			$adapter->tab->finalize();
			return true;
		}

		if ($items) {
			foreach ($items as $comment) {

				$item = $adapter->getTemplate($comment->id, $adapter->type);

				$item->created = $comment->created;

				$item->view = false;
				$item->title =  $this->getTitle($this->userId, $comment);
				$item->intro = $this->getIntro($comment);

				$adapter->tab->addItem($item);
			}
		}

		return true;
	}


	/**
	 * Main function to process user comment data for GDPR download.
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
		$items = $model->getCommentsGDPR($this->userId, $options);

		if (!$items) {
			return $this->tab->finalize();
		}

		foreach ($items as $comment) {
			$item = $this->getTemplate($comment->id, $this->type);

			$item->created = $comment->created;
			$item->title =  $this->getTitle($this->userId, $comment);
			$item->intro = $this->getIntro($comment);

			$this->tab->addItem($item);
		}
	}

	public function getTitle($userid, $comment)
	{

		$actor = ED::user($userid);
		$actor = $actor->user->name;

		// Load post library
		$post = ED::post($comment->post_id);

		$title = JText::sprintf('COM_ED_GDPR_COMMENTED_ON_' . strtoupper($post->getPostItemType()), $post->getTitle());

		$title = strip_tags($title);

		return $title;
	}

	public function getIntro($comment)
	{
		$date = ED::date($comment->created);

		ob_start();
		?>
		<div class="gdpr-item__desc">
			<?php echo $comment->comment; ?>
		</div>
		<div class="gdpr-item__meta">
			<?php echo JText::sprintf('COM_ED_GDPR_COMMENTED_ON', $date->format($this->getDateFormat())); ?>
		</div>
		<?php
		$contents = ob_get_contents();
		ob_end_clean();

		return $contents;
	}
}
