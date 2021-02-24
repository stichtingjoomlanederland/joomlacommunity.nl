<?php
/**
 * @package    JDiDEAL
 *
 * @author     Roland Dalmulder <contact@rolandd.com>
 * @copyright  Copyright (C) 2009 - 2021 RolandD Cyber Produksi. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link       https://rolandd.com
 */

namespace Jdideal\Addons;

use stdClass;

/**
 * RO Payments Addons Interface.
 *
 * @since  1.0.0
 */
interface AddonInterface
{
	/**
	 * Get the name of the addon.
	 *
	 * @return  string  The name of the component.
	 *
	 * @since   5.3.0
	 */
	public function getName(): string;

	/**
	 * Get the order information.
	 *
	 * @param   string  $orderId  The order ID the request is for.
	 * @param   array   $data     The transaction data.
	 *
	 * @return  array    Order details.
	 *
	 * @since   2.0.0
	 */
	public function getOrderInformation(string $orderId, array $data): array;

	/**
	 * Set the callback to go back to the component.
	 *
	 * @param   array  $data  The data from the processor
	 *
	 * @return  array  The order data.
	 *
	 * @since   2.0.0
	 */
	public function callBack(array $data): array;

	/**
	 * Translate the order status from the component status to an RO Payments status.
	 *
	 * @param   string  $orderStatus  The code of the order status
	 *
	 * @return  string  the RO Payments order status code.
	 *
	 * @since   2.0.0
	 */
	public function translateOrderStatus(string $orderStatus): string;

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
	 */
	public function getCustomerInformation(string $orderId): array;

	/**
	 * Get the order status name.
	 *
	 * @param   array  $data  Array with order information
	 *
	 * @return  string  The name of the new order status.
	 *
	 * @since   2.0.0
	 */
	public function getOrderStatusName(array $data): string;

	/**
	 * Get the component link.
	 *
	 * @return  string  The URL to the component.
	 *
	 * @since   2.0.0
	 */
	public function getComponentLink(): string;

	/**
	 * Get the administrator order link.
	 *
	 * @param   string  $orderId  The order ID for the link.
	 *
	 * @return  string  The URL to the order details.
	 *
	 * @since   2.0.0
	 */
	public function getAdminOrderLink(string $orderId): string;

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
	public function getOrderLink(string $orderId, string $orderNumber): string;

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
	public function replacePlaceholders(stdClass $details, string $message): string;
}
