<?php
/**
 * @package    JDiDEAL
 *
 * @author     Roland Dalmulder <contact@rolandd.com>
 * @copyright  Copyright (C) 2009 - 2021 RolandD Cyber Produksi. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link       https://rolandd.com
 */

use Joomla\CMS\MVC\Controller\AdminController;

defined('_JEXEC') or die;

/**
 * RO Payments Messages controller.
 *
 * @package  JDiDEAL
 * @since    4.0.0
 */
class JdidealgatewayControllerMessages extends AdminController
{
	protected $text_prefix = 'COM_ROPAYMENTS_MESSAGES';

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
	public function getModel($name = 'Message', $prefix = 'JdidealgatewayModel', $config = array())
	{
		return parent::getModel($name, $prefix, $config);
	}
}
