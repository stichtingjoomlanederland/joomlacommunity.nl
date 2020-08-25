<?php
/**
 * @package    JDiDEAL
 *
 * @author     Roland Dalmulder <contact@rolandd.com>
 * @copyright  Copyright (C) 2009 - 2020 RolandD Cyber Produksi. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link       https://rolandd.com
 */

use Jdideal\Gateway;
use Joomla\CMS\Date\Date;
use Joomla\CMS\Factory;
use Joomla\CMS\Form\Form;
use Joomla\CMS\MVC\Model\FormModel;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Table\Table;
use Joomla\CMS\Uri\Uri;

defined('_JEXEC') or die;

/**
 * Model for handling the payment.
 *
 * @package  JDiDEAL
 * @since    2.0.0
 */
class JdidealgatewayModelPay extends FormModel
{
	/**
	 * Method to get the registration form.
	 *
	 * The base form is loaded from XML and then an event is fired
	 * for users plugins to extend the form with extra fields.
	 *
	 * @param   array    $data      An optional array of data for the form to interrogate.
	 * @param   boolean  $loadData  True if the form is to load its own data (default case), false if not.
	 *
	 * @return   Form|boolean  A Form object on success, false on failure
	 *
	 * @since    2.0.0
	 * @throws   Exception
	 */
	public function getForm($data = array(), $loadData = true)
	{
		// Get the form.
		$form = $this->loadForm('com_jdidealgateway.pay', 'pay', array('control' => 'jform', 'load_data' => $loadData));

		if (empty($form))
		{
			return false;
		}

		return $form;
	}

	/**
	 * Set up the payment request.
	 *
	 * @return  array  Data used in the ExtraPayment form.
	 *
	 * @since   2.0.0
	 * @throws  Exception
	 */
	public function getIdeal(): array
	{
		$app          = Factory::getApplication();
		$input        = $app->input;
		$table        = $this->getTable();
		$id           = $input->get('order_id', false);
		$profileAlias = '';
		$menu         = $app->getMenu();

		if ($menu)
		{
			$activeMenu = $menu->getActive();

			if ($activeMenu)
			{
				$profileId    = $activeMenu->query['profile_id'];
				$profileTable = Table::getInstance('Profile', 'Table');
				$profileTable->load($profileId);
				$profileAlias = $profileTable->get('alias');
			}
		}

		if ($id)
		{
			$table->load($id);
			$post           = [];
			$post['amount'] = $table->amount;
		}
		else
		{
			$post = $input->get('jform', [], 'array');

			// Add the current date
			$now           = new Date;
			$post['cdate'] = $now->toSql();

			// Make sure the amount has a period
			if (isset($post['amount']))
			{
				$post['amount'] = str_replace(',', '.', $post['amount']);
			}

			// Store the data in the database
			$table->bind($post);
			$table->store();
		}

		// Set some needed data
		$profileSetting = '';

		if (isset($profileId) && $profileId)
		{
			$profileSetting = '&profile_id=' . $profileId;
		}

		return [
			'amount'         => array_key_exists('amount', $post) ? $post['amount'] : 0,
			'order_number'   => array_key_exists('order_number', $post) ? $post['order_number'] : $table->get('id'),
			'order_id'       => $table->get('id'),
			'origin'         => 'jdidealgateway',
			'return_url'     => substr(Uri::root(), 0, -1) . Route::_('index.php?option=com_jdidealgateway&task=pay.result' . $profileSetting),
			'notify_url'     => '',
			'cancel_url'     => substr(Uri::root(), 0, -1) . Route::_('index.php?option=com_jdidealgateway&task=pay.result' . $profileSetting),
			'email'          => $table->get('user_email'),
			'payment_method' => '',
			'profileAlias'   => $profileAlias,
			'custom_html'    => '',
			'silent'         => false,
		];
	}

	/**
	 * Check the payment result.
	 *
	 * @return  string  The customer message.
	 *
	 * @since   2.0
	 *
	 * @throws  RuntimeException
	 * @throws  InvalidArgumentException
	 * @throws  Exception
	 */
	public function getResult(): string
	{
		$input = Factory::getApplication()->input;

		// Load the helper
		$jdideal = new Gateway;
		$trans   = $input->get('transactionId');
		$column  = 'trans';

		if (empty($trans))
		{
			$trans  = $input->get('pid');
			$column = 'pid';
		}

		$details = $jdideal->getDetails($trans, $column, false, 'jdidealgateway');

		$status = $jdideal->getStatusCode($details->result);

		// Update the order status
		if (is_object($details))
		{
			$db    = $this->getDbo();
			$query = $db->getQuery(true)
				->update($db->quoteName('#__jdidealgateway_pays'))
				->set($db->quoteName('status') . ' = ' . $db->quote($status))
				->where($db->quoteName('id') . ' = ' . (int) $details->order_id);
			$db->setQuery($query)->execute();
		}

		// Load the message
		return $jdideal->getMessage($details->id);
	}

	/**
	 * Load the checkout form data the user has already entered.
	 *
	 * @return  array  Form data.
	 *
	 * @since   2.0.0
	 * @throws  Exception
	 */
	protected function loadFormData(): array
	{
		$data = (array) Factory::getApplication()->getUserState('com_jdidealgateway.pay.data', []);

		// Check if we have any data, otherwise try to get it from the URL
		if (0 === count($data))
		{
			$input              = Factory::getApplication()->input;
			$data['user_email'] = $input->getEmail('email', '');
			$data['amount']     = $input->getEmail('amount', '');
			$data['remark']     = $input->getEmail('remark', '');
		}

		return $data;
	}
}
