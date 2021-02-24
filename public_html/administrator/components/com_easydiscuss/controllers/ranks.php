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

class EasyDiscussControllerRanks extends EasyDiscussController
{
	public function __construct()
	{
		parent::__construct();

		$this->checkAccess('discuss.manage.settings');

		$this->registerTask('publish', 'unpublish');
	}

	public function save()
	{
		ED::checkToken();

		$post = $this->input->post->getArray();
		$ids = isset($post['id']) ? $post['id'] : '';
		$starts	= isset($post['start']) ? $post['start'] : '';
		$ends = isset($post['end']) ? $post['end'] : '';
		$titles	= isset($post['title']) ? $post['title'] : '';
		$removal = isset($post['itemRemove']) ? $post['itemRemove'] : '';

		$model = ED::model('Ranks');

		if (!empty($removal)) {
			$rids = explode(',', $removal);
			$model->removeRanks($rids);
		}

		if (!empty($ids)) {
			
			if (count($ids) > 0) {
				
				// log the current action into database.
				$actionlog = ED::actionlog();

				for ($i = 0; $i < count($ids); $i++) {
					$data = array();
					$data['id']	= $ids[$i];
					$data['start'] = $starts[$i];
					$data['end'] = $ends[$i];
					$data['title'] = $titles[$i];

					// determine whether this rank is the new created or not
					$isNew = $data['id'] ? false : true;

					$ranks = ED::table('Ranks');
					$ranks->bind($data);
					$ranks->store();

					$actionlogMsg = $isNew ? 'COM_ED_ACTIONLOGS_RANKS_CREATED' : 'COM_ED_ACTIONLOGS_RANKS_UPDATED';
					$actionlog->log($actionlogMsg, 'rank', array(
						'link' => 'index.php?option=com_easydiscuss&view=ranks',
						'rankTitle' => JText::_($ranks->title)
					));
				}
			}
		}

		$message = JText::_('COM_EASYDISCUSS_RANKING_SUCCESSFULLY_UPDATED');

		ED::setMessage($message, 'success');
		ED::redirect('index.php?option=com_easydiscuss&view=ranks');
	}
}
