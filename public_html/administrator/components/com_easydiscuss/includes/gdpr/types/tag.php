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

class EasyDiscussGdprTag extends EasyDiscussGdprAbstract
{
	public $type = 'tag';

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

		$model = ED::model('tags');
		$items = $model->getTagsGDPR($this->userId, $options);

		if (!$items) {
			// for comments, we always finalize.
			$adapter->tab->finalize();
			return true;
		}

		if ($items) {
			foreach ($items as $tag) {

				$item = $adapter->getTemplate($tag->id, $adapter->type);

				$item->created = $tag->created;
				$item->title =  $tag->title;
				$item->view = false;

				$adapter->tab->addItem($item);
			}
		}

		return true;
	}


	/**
	 * Main function to process user tag data for GDPR download.
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

		$model = ED::model('tags');
		$items = $model->getTagsGDPR($this->userId, $options);

		if (!$items) {
			return $this->tab->finalize();
		}

		foreach ($items as $tag) {
			$item = $this->getTemplate($tag->id, $this->type);

			$item->created = $tag->created;
			$item->title =  $tag->title;

			$this->tab->addItem($item);
		}

	}
}
