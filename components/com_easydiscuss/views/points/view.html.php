<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Restricted access');

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
		$id = $this->input->get('id');
		$dateContainer = '';

		if (!$id) {
			ED::setMessage(JText::_('COM_EASYDISCUSS_NOT_LOCATED_USER_ID'), 'error');
			return $this->app->redirect(EDR::_('view=index'));
		}

		$model = ED::model('Points', true);
		$history = $model->getPointsHistory($id);
		$user = ED::user($id);

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
		$this->set('user', $user);

		parent::display('points/default');
	}
}
