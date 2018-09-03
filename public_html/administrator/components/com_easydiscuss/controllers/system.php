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

class EasyDiscussControllerSystem extends EasyDiscussController
{
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Single click updater to update EasyDiscuss to the latest version
	 *
	 * @since	4.2.0
	 * @access	public
	 */
	public function upgrade()
	{
		$model = ED::model('System');
		$state = $model->update();

		if ($state === false) {
			ED::setMessage($model->getError(), 'error');
			return $this->app->redirect('index.php?option=com_easydiscuss');
		}

		ED::setMessage('EasyDiscuss updated to the latest version successfully', 'success');
		return $this->app->redirect('index.php?option=com_easydiscuss');
	}
}
