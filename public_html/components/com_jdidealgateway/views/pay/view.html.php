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

use Joomla\CMS\Factory;
use Joomla\CMS\Form\Form;
use Joomla\CMS\MVC\View\HtmlView;
use Joomla\Registry\Registry;

/**
 * Payment page.
 *
 * @package  JDiDEAL
 * @since    2.0.0
 */
class JdidealgatewayViewPay extends HtmlView
{
	/**
	 * Current user state
	 *
	 * @var    Registry
	 * @since  2.0.0
	 */
	protected $state;

	/**
	 * Form class
	 *
	 * @var    Form
	 * @since  2.0.0
	 */
	protected $form;

	/**
	 * Return the payment result.
	 *
	 * @var    string
	 * @since  2.0.0
	 */
	protected $result = '';

	/**
	 * Array with payment details
	 *
	 * @var    array
	 * @since  2.0.0
	 */
	protected $data = [];

	/**
	 * Display the payment form.
	 *
	 * @param   string  $tpl  A template file to use
	 *
	 * @return  mixed  The rendered view
	 *
	 * @since   2.0.0
	 * @throws  Exception
	 */
	public function display($tpl = null)
	{
		/** @var JdidealgatewayModelPay $model */
		$model = $this->getModel();
		$task  = Factory::getApplication()->input->get('task');

		switch ($task)
		{
			case 'sendmoney':
				$this->data = $model->getIdeal();
				break;
			case 'result':
				break;
			default:
				$this->form  = $model->getForm();
				$this->state = $model->getState();
				break;
		}

		return parent::display($tpl);
	}
}
