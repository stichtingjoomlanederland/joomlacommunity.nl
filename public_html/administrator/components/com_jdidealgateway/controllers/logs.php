<?php
/**
 * @package    JDiDEAL
 *
 * @author     Roland Dalmulder <contact@rolandd.com>
 * @copyright  Copyright (C) 2009 - 2021 RolandD Cyber Produksi. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link       https://rolandd.com
 */

defined('_JEXEC') or die;

use Joomla\CMS\MVC\Controller\AdminController;

/**
 * Logs controller.
 *
 * @package  JDiDEAL
 * @since    2.0.0
 */
class JdidealgatewayControllerLogs extends AdminController
{
	protected $text_prefix = 'COM_ROPAYMENTS_LOGS';

	/**
	 * Method to get a model object, loading it if required.
	 *
	 * @param   string  $name    The model name. Optional.
	 * @param   string  $prefix  The class prefix. Optional.
	 * @param   array   $config  Configuration array for model. Optional.
	 *
	 * @return  \JModelLegacy|boolean  Model object on success; otherwise false on failure.
	 *
	 * @since   5.0.0
	 */
	public function getModel($name = 'Log', $prefix = 'JdidealgatewayModel', $config = [])
	{
		return parent::getModel($name, $prefix, $config);
	}

	/**
	 * Read a logfile and show it in a popup.
	 *
	 * @return  void
	 *
	 * @throws  Exception
	 *
	 * @since   2.0.0
	 */
	public function history(): void
	{
		// Create the view object
		/** @var JdidealgatewayViewLogs $view */
		$view = $this->getView('logs', 'html');

		// Standard model
		$logsModel = $this->getModel('Logs', 'JdidealgatewayModel');
		$view->setModel($logsModel, true);
		$view->setLayout('history');

		// Now display the view
		$view->display();
	}
}
