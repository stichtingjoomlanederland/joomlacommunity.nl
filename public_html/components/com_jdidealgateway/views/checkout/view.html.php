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

use Joomla\CMS\MVC\View\HtmlView;

/**
 * Pay view.
 *
 * @package  JDiDEAL
 * @since    2.14.0
 */
class JdidealgatewayViewCheckout extends HtmlView
{
	/**
	 * An array with form data to initiate a payment
	 *
	 * @var    array
	 * @since  2.14.0
	 */
	public $data = [];

	/**
	 * Execute and display a template script.
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  mixed  A string if successful, otherwise a JError object.
	 *
	 * @since   2.14.0
	 * @throws  Exception
	 */
	public function display($tpl = null)
	{
		/** @var JdidealgatewayModelCheckout $model */
		$model      = $this->getModel();
		$this->data = $model->getVariables();

		return parent::display($tpl);
	}
}
