<?php
/**
 * @package     JDIdeal
 * @subpackage  Addon
 *
 * @author      Roland Dalmulder <contact@rolandd.com>
 * @copyright   Copyright (C) 2006 - 2021 RolandD Cyber Produksi. All rights reserved.
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link        https://rolandd.com
 */

namespace Jdideal\Addons;

defined('_JEXEC') or die;

use JDatabaseDriver;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use RSFormProHelper;
use stdClass;

\JLoader::register(
	'RSFormProHelper',
	JPATH_ADMINISTRATOR . '/components/com_rsform/helpers/rsform.php'
);
\JLoader::register(
	'RsfpjdidealHelper',
	dirname(__DIR__) . '/RsfpjdidealHelper.php'
);

/**
 * RSForm! Pro addon.
 *
 * @package     JDIdeal
 * @subpackage  Addon
 * @since       2.13.0
 */
class AddonRsformpro implements AddonInterface
{
	/**
	 * Database interface
	 *
	 * @var    JDatabaseDriver
	 * @since  5.3.0
	 */
	private $db;

	/**
	 * Constructor.
	 *
	 * @since  5.3.0
	 */
	public function __construct()
	{
		$this->db = Factory::getDbo();
	}

	/**
	 * Get the name of the addon.
	 *
	 * @return  string  The name of the component.
	 *
	 * @since   5.3.0
	 */
	public function getName(): string
	{
		return 'RSForm! Pro';
	}

	/**
	 * Collect the order information.
	 *
	 * @param   string  $orderId  The ID to get the order data for
	 * @param   array   $data     Array with payment info
	 *
	 * @return  array  Array with order information.
	 *
	 * @since   2.13.0
	 *
	 * @throws  \RuntimeException
	 */
	public function getOrderInformation(string $orderId, array $data): array
	{
		$helper = new \RsfpjdidealHelper;

		[$formId, $submissionId] = explode('.', $orderId);
		$settings   = $helper->loadFormSettings($formId);
		$emailField = $settings->get('fieldEmail');

		$query = $this->db->getQuery(true);
		$query->select(
			$this->db->quoteName(
				[
					'values.FieldName',
					'values.FieldValue',
					'submissions.UserId',
					'users.email'
				],
				[
					'fieldName',
					'fieldValue',
					'userId',
					'userEmail'
				]
			)
		)
			->from($this->db->quoteName('#__rsform_submission_values', 'values'))
			->leftJoin(
				$this->db->quoteName('#__rsform_submissions', 'submissions')
				. ' ON ' . $this->db->quoteName('values.SubmissionId') . ' = ' . $this->db->quoteName('submissions.SubmissionId')
			)
			->leftJoin(
				$this->db->quoteName('#__users', 'users')
				. ' ON ' . $this->db->quoteName('submissions.UserId') . ' = ' . $this->db->quoteName('users.id')
			)
			->where($this->db->quoteName('values.FieldName') . ' IN (' . $this->db->quote('_STATUS') . ',' . $this->db->quote($emailField) . ')')
			->where($this->db->quoteName('values.SubmissionId') . ' = ' . (int) $submissionId);
		$this->db->setQuery($query);
		$order = $this->db->loadObjectList('fieldName');

		if (!$order)
		{
			$data['order_status'] = false;

			return $this->callBack($data);
		}

		$query->clear()
			->select($this->db->quoteName('amount'))
			->from($this->db->quoteName('#__jdidealgateway_logs'))
			->where($this->db->quoteName('order_id') . ' = ' . $this->db->quote($orderId))
			->where($this->db->quoteName('origin') . ' = ' . $this->db->quote('rsformpro'));
		$this->db->setQuery($query);

		$data['order_total']  = $this->db->loadResult();
		$data['order_status'] = $this->translateOrderStatus($order['_STATUS']->fieldValue);
		$data['user_email']   = $order[$emailField]->fieldValue ?? $order[$emailField]->userEmail;

		return $data;
	}

	/**
	 * Set the callback to go back to the component.
	 *
	 * @param   array  $data  The data from the processor
	 *
	 * @return  array  The order data.
	 *
	 * @since   2.13.0
	 */
	public function callBack(array $data): array
	{
		return [];
	}

	/**
	 * Translate the order status from the component status to an RO Payments status.
	 *
	 * @param   string  $orderStatus  The code of the order status
	 *
	 * @return  string  the RO Payments order status code.
	 *
	 * @since   2.13.0
	 */
	public function translateOrderStatus(string $orderStatus): string
	{
		switch ($orderStatus)
		{
			case '1':
				return 'C';
			case '3':
				return 'X';
			case '0':
			default:
				return 'P';
		}
	}

	/**
	 * Get the customer information
	 *
	 * @param   string  $orderId  The order ID the request is for
	 *
	 * @return  array  Customer details.
	 *
	 * @since   2.13.0
	 */
	public function getCustomerInformation(string $orderId): array
	{
		$data   = [];
		$helper = new \RsfpjdidealHelper;

		[$formId, $submissionId] = explode('.', $orderId);
		$settings   = $helper->loadFormSettings($formId);
		$emailField = $settings->get('fieldEmail');
		$nameField  = $settings->get('fieldName');
		$query      = $this->db->getQuery(true);
		$query->select($this->db->quoteName(['FieldValue','FieldName']))
			->from($this->db->quoteName('#__rsform_submission_values', 'v'))
			->where($this->db->quoteName('FormId') . ' = ' . $formId)
			->where($this->db->quoteName('FieldName') . ' IN (' . $this->db->quote($emailField) . ',' . $this->db->quote($nameField) . ')')
			->where($this->db->quoteName('v.SubmissionId') . ' = ' . (int) $submissionId);
		$this->db->setQuery($query);
		$fieldValues = $this->db->loadAssocList('FieldName');

		if (isset($fieldValues[$emailField]))
		{
			$data['billing']        = new stdClass;
			$data['billing']->name  = $fieldValues[$nameField]['FieldValue'] ?? $fieldValues[$emailField]['FieldValue'];
			$data['billing']->email = $fieldValues[$emailField]['FieldValue'];
		}

		return $data;
	}

	/**
	 * Get the order status name.
	 *
	 * @param   array  $data  Array with order information
	 *
	 * @return  string  The name of the new order status.
	 *
	 * @since   2.13.0
	 */
	public function getOrderStatusName(array $data): string
	{
		switch ($data['order_status'])
		{
			case 'C':
				return Text::_('COM_ROPAYMENTS_STATUS_CONFIRMED');
				break;
			case 'P':
				return Text::_('COM_ROPAYMENTS_STATUS_PENDING');
				break;
			case 'X':
				return Text::_('COM_ROPAYMENTS_STATUS_CANCELLED');
				break;
			default:
				return '';
				break;
		}
	}

	/**
	 * Get the component link.
	 *
	 * @return  string  The URL to the component.
	 *
	 * @since   2.13.0
	 */
	public function getComponentLink(): string
	{
		return 'index.php?option=com_rsform';
	}

	/**
	 * Get the administrator order link.
	 *
	 * @param   string  $orderId  The order ID for the link.
	 *
	 * @return  string  The URL to the order details.
	 *
	 * @since   2.13.0
	 */
	public function getAdminOrderLink(string $orderId): string
	{
		// Clean up the order ID since it is a composite
		$orderId = substr($orderId, strpos($orderId, '.') + 1);

		return 'index.php?option=com_rsform&view=submissions&layout=edit&cid=' . $orderId;
	}

	/**
	 * Get the order link.
	 *
	 * @param   string  $orderId      The order ID for the link
	 * @param   string  $orderNumber  The order number for the link
	 *
	 * @return  string  The URL to the order details.
	 *
	 * @since   2.13.0
	 */
	public function getOrderLink(string $orderId, string $orderNumber): string
	{
		return '';
	}

	/**
	 * Replace addon specific placeholders.
	 *
	 * @param   stdClass  $details  The transaction details
	 * @param   string    $message  The message to replace the placeholders for
	 *
	 * @return  string  The message with replaced placeholders.
	 *
	 * @since   5.3.0
	 */
	public function replacePlaceholders(stdClass $details, string $message): string
	{
		[$find, $replace] = RSFormProHelper::getReplacements($details->order_number);

		return str_ireplace($find, $replace, $message);
	}
}
