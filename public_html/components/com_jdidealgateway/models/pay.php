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

use Jdideal\Gateway;
use Joomla\CMS\Date\Date;
use Joomla\CMS\Factory;
use Joomla\CMS\Form\Form;
use Joomla\CMS\MVC\Model\FormModel;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Table\Table;
use Joomla\CMS\Uri\Uri;

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
	public function getForm($data = [], $loadData = true)
	{
		// Get the form.
		$form = $this->loadForm('com_jdidealgateway.pay', 'pay', ['control' => 'jform', 'load_data' => $loadData]);

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
	 * @param   string  $trans  The transaction ID to check
	 * @param   string  $column The column to use for the check
	 *
	 * @return  string  The customer message.
	 *
	 * @since   2.0.0
	 * @throws  Exception
	 */
	public function getResult(string $trans, string $column = 'trans'): string
	{
		$jdideal = new Gateway;
		$details = $jdideal->getDetails($trans, $column, false, 'jdidealgateway');
		$status  = $jdideal->getStatusCode($details->result);

		if (is_object($details))
		{
			$db    = $this->getDbo();
			$query = $db->getQuery(true)
				->update($db->quoteName('#__jdidealgateway_pays'))
				->set($db->quoteName('status') . ' = ' . $db->quote($status))
				->where($db->quoteName('id') . ' = ' . (int) $details->order_id);
			$db->setQuery($query)->execute();
		}

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

		if (0 === count($data))
		{
			$input              = Factory::getApplication()->input;
			$data['user_email'] = $input->getString('email', '');
			$data['amount']     = $input->getString('amount', '');
			$data['remark']     = $input->getString('remark', '');
		}

		return $data;
	}
}
