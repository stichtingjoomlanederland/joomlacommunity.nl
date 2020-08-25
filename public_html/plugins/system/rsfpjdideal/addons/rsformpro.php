<?php
/**
 * @package     JDIdeal
 * @subpackage  Addon
 *
 * @author      Roland Dalmulder <contact@rolandd.com>
 * @copyright   Copyright (C) 2006 - 2020 RolandD Cyber Produksi. All rights reserved.
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
		list($formId, $submissionId) = explode('.', $orderId);

		$query = $this->db->getQuery(true);
		$query->select($this->db->quoteName('v.FieldValue', 'order_state_id'))
			->select($this->db->quoteName('u.email', 'user_email'))
			->from($this->db->quoteName('#__rsform_submission_values', 'v'))
			->leftJoin(
				$this->db->quoteName('#__rsform_submissions', 's')
				. ' ON ' . $this->db->quoteName('v.SubmissionId') . ' = ' . $this->db->quoteName('s.SubmissionId')
			)
			->leftJoin(
				$this->db->quoteName('#__users', 'u')
				. ' ON ' . $this->db->quoteName('s.UserId') . ' = ' . $this->db->quoteName('u.id')
			)
			->where($this->db->quoteName('FieldName') . ' = ' . $this->db->quote('_STATUS'))
			->where($this->db->quoteName('v.SubmissionId') . ' = ' . (int) $submissionId);
		$this->db->setQuery($query);
		$order = $this->db->loadObject();

		if (!$order)
		{
			$data['order_status'] = false;

			return $this->callBack($data);
		}

		// Get the order total
		$query = $this->db->getQuery(true)
			->select($this->db->quoteName('amount'))
			->from($this->db->quoteName('#__jdidealgateway_logs'))
			->where($this->db->quoteName('order_id') . ' = ' . $this->db->quote($orderId))
			->where($this->db->quoteName('origin') . ' = ' . $this->db->quote('rsformpro'));
		$this->db->setQuery($query);
		$order->order_total = $this->db->loadResult();

		$data['order_total']  = $order->order_total;
		$data['order_status'] = $this->translateOrderStatus($order->order_state_id);
		$data['user_email']   = $order->user_email;

		// Return the data
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
			case '0':
				return 'P';
				break;
			case '1':
				return 'C';
				break;
			case '3':
				return 'X';
				break;
			default:
				return 'P';
				break;
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
		// Collect the data
		$data = [];

		// Get the email address field name
		$emailField = RSFormProHelper::getConfig('jdideal.email');

		// Get the email value
		[$formId, $submissionId] = explode('.', $orderId);

		$query = $this->db->getQuery(true);
		$query->select($this->db->quoteName('FieldValue'))
			->from($this->db->quoteName('#__rsform_submission_values', 'v'))
			->where($this->db->quoteName('FieldName') . ' = ' . $this->db->quote($emailField))
			->where($this->db->quoteName('v.SubmissionId') . ' = ' . (int) $submissionId);
		$this->db->setQuery($query);
		$email = $this->db->loadResult();

		if ($email)
		{
			$data['billing']        = new stdClass;
			$data['billing']->email = $email;
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
