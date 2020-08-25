<?php
/**
 * @package    JDiDEAL
 *
 * @author     Roland Dalmulder <contact@rolandd.com>
 * @copyright  Copyright (C) 2009 - 2020 RolandD Cyber Produksi. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link       https://rolandd.com
 */

namespace Jdideal\Addons;

defined('_JEXEC') or die;

use JDatabaseDriver;
use JEventDispatcher;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Plugin\PluginHelper;
use RuntimeException;
use stdClass;

/**
 * Addon for the RO Payments payment form.
 *
 * @package  JDiDEAL
 * @since    2.0.0
 */
class AddonJdidealgateway implements AddonInterface
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
		return 'RO Payments';
	}

	/**
	 * Returns the order information in an array
	 *
	 * order_total  = The amount of the order
	 * order_status = The translated order status
	 * user_email   = The email address of the customer
	 *
	 * @param   string  $orderId  The order ID the request is for.
	 * @param   array   $data     The transaction data.
	 *
	 * @return  array    Order details.
	 *
	 * @since   2.0.0
	 *
	 * @throws  RuntimeException
	 */
	public function getOrderInformation(string $orderId, array $data): array
	{
		$query = $this->db->getQuery(true);
		$query->select(
			[
				$this->db->quoteName('user_email'),
				$this->db->quoteName('amount'),
				$this->db->quoteName('status')
			]
		)
			->from($this->db->quoteName('#__jdidealgateway_pays'))
			->where($this->db->quoteName('id') . ' = ' . (int) $orderId);
		$this->db->setQuery($query);
		$order = $this->db->loadObject();

		if (!$order)
		{
			$data['order_status'] = false;

			return $this->callBack($data);
		}

		$data['order_total']  = $order->amount;
		$data['order_status'] = $this->translateOrderStatus($order->status);
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
	 * @since   2.0.0
	 */
	public function callBack(array $data): array
	{
		$dispatcher = JEventDispatcher::getInstance();
		PluginHelper::importPlugin('jdideal');
		$dispatcher->trigger('onPaymentComplete', [$data]);

		return $data;
	}

	/**
	 * Translate the order status from the component status to an RO Payments status.
	 *
	 * @param   string  $orderStatus  The code of the order status
	 *
	 * @return  string  the RO Payments order status code.
	 *
	 * @since   2.0.0
	 */
	public function translateOrderStatus(string $orderStatus): string
	{
		return $orderStatus ?: 'P';
	}

	/**
	 * Get the customer information
	 *
	 * Array of required information:
	 *
	 * Shipping:
	 * firstname
	 * lastname
	 * company
	 * address1
	 * address2
	 * city
	 * zip
	 * countrycode (2 letters)
	 * country (name)
	 * phone
	 * email
	 *
	 * Billing:
	 * firstname
	 * lastname
	 * company
	 * address1
	 * address2
	 * city
	 * zip
	 * countrycode (2 letters)
	 * country (name)
	 * phone
	 * email
	 *
	 * Pricing:
	 * amount
	 * tax (tax amount)
	 * currency (3 letters)
	 *
	 * @param   string  $orderId  The order ID the request is for
	 *
	 * @return  array  Customer details.
	 *
	 * @since   2.0.0
	 *
	 * @throws  RuntimeException
	 */
	public function getCustomerInformation(string $orderId): array
	{
		// Collect the data
		$query = $this->db->getQuery(true)
			->select($this->db->quoteName('user_email', 'email'))
			->select($this->db->quoteName('amount'))
			->from($this->db->quoteName('#__jdidealgateway_pays'))
			->where($this->db->quoteName('id') . ' = ' . (int) $orderId);
		$this->db->setQuery($query);

		$data['billing'] = $this->db->loadObject();

		return $data;
	}

	/**
	 * Get the order status name.
	 *
	 * @param   array  $data  Array with order information
	 *
	 * @return  string  The name of the new order status.
	 *
	 * @since   2.0.0
	 */
	public function getOrderStatusName(array $data): string
	{
		switch ($data['order_status'])
		{
			case 'C':
				$orderStatusName = Text::_('COM_ROPAYMENTS_STATUS_SUCCESS');
				break;
			case 'X':
				$orderStatusName = Text::_('COM_ROPAYMENTS_STATUS_CANCELLED');
				break;
			case 'P':
				$orderStatusName = Text::_('COM_ROPAYMENTS_STATUS_PENDING');
				break;
			default:
				$orderStatusName = Text::_('COM_ROPAYMENTS_STATUS_UNKNOWN');
				break;
		}

		return $orderStatusName;
	}

	/**
	 * Get the component link.
	 *
	 * @return  string  The URL to the component.
	 *
	 * @since   2.0.0
	 */
	public function getComponentLink(): string
	{
		return 'index.php?option=com_jdidealgateway';
	}

	/**
	 * Get the administrator order link.
	 *
	 * @param   string  $orderId  The order ID for the link.
	 *
	 * @return  string  The URL to the order details.
	 *
	 * @since   2.0.0
	 */
	public function getAdminOrderLink(string $orderId): string
	{
		return 'index.php?option=com_jdidealgateway&view=pays&filter[search]=' . $orderId;
	}

	/**
	 * Get the order link.
	 *
	 * @param   string  $orderId      The order ID for the link
	 * @param   string  $orderNumber  The order number for the link
	 *
	 * @return  string  The URL to the order details.
	 *
	 * @since   2.0.0
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
		return $message;
	}
}
