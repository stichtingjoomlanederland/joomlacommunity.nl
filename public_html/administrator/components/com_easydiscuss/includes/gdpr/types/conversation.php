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

class EasyDiscussGdprConversation extends EasyDiscussGdprAbstract
{
	public $type = 'conversation';

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

		$options = array('userId' => $this->userId, 'limit' => $limit);
		if ($ids) {
			$options['exclude'] = $ids;
		}
		$model = ED::model('conversation');
		$items = $model->getConversationGDPR($options);

		if (!$items) {
			// for comments, we always finalize.
			$adapter->tab->finalize();
			return true;
		}

		if ($items) {
			foreach ($items as $conversation) {

				$item = $adapter->getTemplate($conversation->id, $adapter->type);

				$participant = $conversation->getParticipant();

				$item->created = $conversation->created;
				$item->title =  $participant->getName();

				// Get message from the conversation
				$message = $conversation->getUserMessagesOnly($this->userId);

				$item->intro = $this->getIntro($conversation);
				$item->content = $this->getContent($message);
				$item->view = true;

				$adapter->tab->addItem($item);
			}
		}

		return true;
	}

	/**
	 * Main function to process user votes data for GDPR download.
	 *
	 * @since	4.1.0
	 * @access	public
	 */
	public function execute(EasyDiscussGdprSection &$section)
	{
		$this->tab = $section->createTab($this);

		$limit = $this->getLimit(2);

		// Get a list of ids that are already processed
		$ids = $this->tab->getProcessedIds();

		$options = array('userId' => $this->userId, 'limit' => $limit);

		if ($ids) {
			$options['exclude'] = $ids;
		}

		$model = ED::model('conversation');
		$items = $model->getConversationGDPR($options);

		if (!$items) {
			return $this->tab->finalize();
		}

		foreach ($items as $conversation) {
			$item = $this->getTemplate($conversation->id, $this->type);
			$participant = $conversation->getParticipant();

			$item->created = $conversation->created;
			$item->title =  $participant->getName();

			// Get message from the conversation
			$message = $conversation->getUserMessagesOnly($this->userId);

			$item->intro = $this->getIntro($conversation);
			$item->content = $this->getContent($message);
			$item->view = true;

			$this->tab->addItem($item);
		}

	}

	private function getIntro($conversation)
	{
		$date = ED::date($conversation->created);
		$intro = JString::substr(strip_tags($conversation->message), 0, 10) . JText::_('COM_EASYDISCUSS_ELLIPSES');;

		ob_start();
		?>
		<div class="gdpr-item__desc">
			<?php echo $intro; ?>
		</div>
		<div class="gdpr-item__meta">
			<?php echo $date->format($this->getDateFormat());?>
		</div>
		<?php
		$contents = ob_get_contents();
		ob_end_clean();

		return $contents;
	}

	private function getContent($messages)
	{
		ob_start();
		?>
		<?php foreach ($messages as $message) { ?>
		<?php $date = ED::date($message->table->created); ?>
		<div class="gdpr-item">
			<div class="gdpr-item__intro">
				<div class="gdpr-item__desc"><?php echo nl2br($message->table->message); ?></div>
				<div class="gdpr-item__meta"><?php echo $date->format($this->getDateFormat()); ?></div>
			</div>
		</div>
		<?php } ?>
		<?php
		$contents = ob_get_contents();
		ob_end_clean();

		return $contents;
	}
}
