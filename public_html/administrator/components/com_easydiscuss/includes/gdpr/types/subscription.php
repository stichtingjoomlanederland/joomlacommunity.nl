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

class EasyDiscussGdprSubscription extends EasyDiscussGdprAbstract
{
	public $type = 'subscription';

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

		$model = ED::model('subscribe');
		$items = $model->getSubscribeGDPR($options);

		if (!$items) {
			// for comments, we always finalize.
			$adapter->tab->finalize();
			return true;
		}

		if ($items) {
			foreach ($items as $subscription) {

				$item = $adapter->getTemplate($subscription->id, $adapter->type);

				if ($subscription->type == 'category') {
					$title = $subscription->categoryTitle;
				} else {
					$title = $subscription->postTitle;
				}

				$item->created = $subscription->created;
				$item->title =  $title;

				$item->intro = $this->getIntro($subscription);
				$item->content = false;
				$item->view = false;

				$adapter->tab->addItem($item);
			}
		}

		return true;
	}

	/**
	 * Main function to process user post data for GDPR download.
	 *
	 * @since	4.1.0
	 * @access	public
	 */
	public function execute(EasyDiscussGdprSection &$section)
	{
		$this->tab = $section->createTab($this);

		$limit = $this->getLimit();

		// Get a list of ids that are already processed
		$ids = $this->tab->getProcessedIds();

		$options = array('userId' => $this->userId, 'limit' => $limit);

		if ($ids) {
			$options['exclude'] = $ids;
		}

		$model = ED::model('subscribe');
		$items = $model->getSubscribeGDPR($options);

		if (!$items) {
			return $this->tab->finalize();
		}

		foreach ($items as $subscription) {
			$item = $this->getTemplate($subscription->id, $this->type);

			if ($subscription->type == 'category') {
				$title = $subscription->categoryTitle;
			} else {
				$title = $subscription->postTitle;
			}

			$item->created = $subscription->created;
			$item->title =  $title;

			$item->intro = $this->getIntro($subscription);
			$item->content = false;
			$item->view = false;

			$this->tab->addItem($item);
		}

	}

	private function getIntro($subscription)
	{
		$date = ED::date($subscription->created);

		ob_start();
		?>
		<div class="gdpr-item__meta">
			<?php echo $date->format($this->getDateFormat());?>
		</div>
		<div class="gdpr-item__label">
			<span class="gdpr-label"><?php echo JString::strtoupper($subscription->type);?></span>
		</div>
		<?php
		$contents = ob_get_contents();
		ob_end_clean();

		return $contents;
	}
}
