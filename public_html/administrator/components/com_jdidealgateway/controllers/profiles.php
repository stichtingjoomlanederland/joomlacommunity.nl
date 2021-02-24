<?php
/**
 * @package    JDiDEAL
 *
 * @author     Roland Dalmulder <contact@rolandd.com>
 * @copyright  Copyright (C) 2009 - 2021 RolandD Cyber Produksi. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link       https://rolandd.com
 */

use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Controller\AdminController;

defined('_JEXEC') or die;

/**
 * RO Payments Profiles Controller.
 *
 * @package  JDiDEAL
 * @since    4.0.0
 */
class JdidealgatewayControllerProfiles extends AdminController
{
	protected $text_prefix = 'COM_ROPAYMENTS_PROFILES';

	/**
	 * Task to set the default profile.
	 *
	 * @return  void
	 *
	 * @since   5.3.0
	 */
	public function setDefault(): void
	{
		// Check for request forgeries.
		$this->checkToken();

		$cid  = $this->input->get('cid', '');
		$msg  = Text::_('COM_ROPAYMENTS_DEFAULT_PROFILE_SAVED');
		$type = 'message';

		/** @var JdidealgatewayModelProfiles $model */
		$model = $this->getModel('profiles');

		if ($model->publish($cid[0]) === false)
		{
			$msg  = $this->getError();
			$type = 'error';
		}

		$this->setredirect('index.php?option=com_jdidealgateway&view=profiles', $msg, $type);
	}

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
	public function getModel($name = 'Profile', $prefix = 'JdidealgatewayModel', $config = array())
	{
		return parent::getModel($name, $prefix, $config);
	}
}
