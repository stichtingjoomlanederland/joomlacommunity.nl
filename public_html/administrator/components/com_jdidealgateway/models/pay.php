<?php
/**
 * @package    JDiDEAL
 *
 * @author     Roland Dalmulder <contact@rolandd.com>
 * @copyright  Copyright (C) 2009 - 2020 RolandD Cyber Produksi. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link       https://rolandd.com
 */

use Joomla\CMS\MVC\Model\AdminModel;

defined('_JEXEC') or die;

/**
 * Log model.
 *
 * @package  JDiDEAL
 * @since    2.0.0
 */
class JdidealgatewayModelPay extends AdminModel
{
	/**
	 * Get the form.
	 *
	 * @param   array    $data      Data for the form.
	 * @param   boolean  $loadData  True if the form is to load its own data (default case), false if not.
	 *
	 * @return  mixed  A JForm object on success | False on failure.
	 *
	 * @since   2.0.0
	 */
	public function getForm($data = array(), $loadData = true): bool
	{
		return false;
	}

	/**
	 * Method to get the data that should be injected in the form..
	 *
	 * @return  array  The data for the form.
	 *
	 * @since   2.0.0
	 *
	 * @throws  Exception
	 */
	protected function loadFormData(): array
	{
		return array();
	}
}
