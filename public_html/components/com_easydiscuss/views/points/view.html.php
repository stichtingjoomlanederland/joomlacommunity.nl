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

require_once(DISCUSS_ROOT . '/views/views.php');

class EasyDiscussViewPoints extends EasyDiscussView
{
	/**
	 * Displays the user's points achievement history
	 *
	 * @since	2.0
	 * @access	public
	 */
	public function display($tmpl = null)
	{
		if (!$this->config->get('main_points')) {
			ED::getErrorRedirection();
		}

		$id = $this->input->get('id');
		$dateContainer = '';

		if (!$id) {
			ED::setMessage(JText::_('COM_EASYDISCUSS_NOT_LOCATED_USER_ID'), 'error');
			return $this->app->redirect(EDR::_('view=index'));
		}

		$model = ED::model('Points', true);
		$history = $model->getPointsHistory($id);
		$userObj = ED::user($id);

		$pageTitle = JText::sprintf('COM_EASYDISCUSS_TITLE_POINTS', $userObj->user->name);

		// set page title here
		ED::setPageTitle($pageTitle);

		// Add canonical tag for this page
		$this->canonical('index.php?option=com_easydiscuss&view=points&id=' . $id);

		foreach ($history as $item) {
			$points = ED::points()->getPoints($item->command);

			if ($points) {

				if ($points[0]->rule_limit < 0) {
					$item->class = 'badge-important';
					$item->points = $points[0]->rule_limit;
				} else {
					$item->class = 'badge-info';
					$item->points = '+'.$points[0]->rule_limit;
				}
			} else {
				$item->class = 'badge-info';
				$item->points = '+';
			}
		}

		$history = ED::points()->group($history);

		$this->set('history', $history);
		$this->set('dateContainer', $dateContainer);
		$this->set('user', $userObj);

		parent::display('points/default');
	}
}
