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

use Exception;
use InvalidArgumentException;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;

/**
 * Class that holds all addons.
 *
 * @package  JDiDEAL
 * @since    4.12.0
 */
class Addon
{
	/**
	 * Holds a list of registered addons
	 *
	 * @var    array
	 * @since  4.12.0
	 */
	private $addons = [];

	/**
	 * Construct the class.
	 *
	 * @since     4.12.0
	 */
	public function __construct()
	{
		$this->init();
	}

	/**
	 * Initialiase the known paths.
	 *
	 * @return  void
	 *
	 * @since   4.12.0
	 */
	private function init(): void
	{
		$this->addons = [
			'booking'           => JPATH_ROOT . '/plugins/bookingpayment/jdideal/addons/booking.php',
			'eshop'             => JPATH_ROOT . '/plugins/system/jdidealeshop/addons/eshop.php',
			'eventbooking'      => JPATH_ROOT . '/plugins/system/jdidealeventbooking/addons/eventbooking.php',
			'hikashop'          => JPATH_ROOT . '/plugins/hikashoppayment/jdideal/addons/hikashop.php',
			'j2store'           => JPATH_ROOT . '/plugins/j2store/payment_jdideal/addons/j2store.php',
			'jdidealgateway'    => JPATH_ROOT . '/administrator/components/com_jdidealgateway/models/addons/jdidealgateway.php',
			'jgive'             => JPATH_ROOT . '/plugins/payment/jdideal/addons/jgive.php',
			'joomdonation'      => JPATH_ROOT . '/plugins/system/jdidealjoomdonation/addons/joomdonation.php',
			'joomshopping'      => JPATH_ROOT . '/plugins/system/jdidealjoomshopping/addons/joomshopping.php',
			'jticketing'        => JPATH_ROOT . '/plugins/payment/jdideal/addons/jticketing.php',
			'mijoshop'          => JPATH_ROOT . '/plugins/system/jdidealmijoshop/addons/mijoshop.php',
			'osmembership'      => JPATH_ROOT . '/plugins/system/jdidealosmembership/addons/osmembership.php',
			'quick2cart'        => JPATH_ROOT . '/plugins/payment/jdideal/addons/quick2cart.php',
			'rdsubs'            => JPATH_ROOT . '/plugins/rdmedia_payment/jdideal/addons/rdsubs.php',
			'rsdirectory'       => JPATH_ROOT . '/plugins/system/rsdirectoryjdideal/addons/rsdirectory.php',
			'rseventspro'       => JPATH_ROOT . '/plugins/system/rseprojdideal/addons/rseventspro.php',
			'rsformpro'         => JPATH_ROOT . '/plugins/system/rsfpjdideal/addons/rsformpro.php',
			'rsmembership'      => JPATH_ROOT . '/plugins/system/rsmembershipjdideal/addons/rsmembership.php',
			'servicesbooking'   => JPATH_ROOT . '/plugins/system/jdidealservicesbooking/addons/servicesbooking.php',
			'socialads'         => JPATH_ROOT . '/plugins/payment/jdideal/addons/socialads.php',
			'virtuemart'        => JPATH_ROOT . '/plugins/vmpayment/jdideal/addons/virtuemart.php',
		];
	}

	/**
	 * Return the addon path if it exists.
	 *
	 * @param   string  $origin  The addon to get the path for
	 *
	 * @return  AddonInterface The path to the addon.
	 *
	 * @since   4.12.0
	 *
	 * @throws  Exception
	 */
	public function get($origin): AddonInterface
	{
		if (!isset($this->addons[$origin]))
		{
			// Check if the file exists in the old location
			$oldPath = JPATH_ADMINISTRATOR . '/components/com_jdidealgateway/models/addons/' . $origin . '.php';

			if (file_exists($oldPath))
			{
				$this->addons[$origin] = $oldPath;
			}

			if (!isset($this->addons[$origin]))
			{
				// Check in the template override folder
				$template     = Factory::getApplication()->getTemplate();
				$templatePath = JPATH_SITE . '/templates/' . $template . '/html/com_jdidealgateway/addons/' . $origin . '.php';

				if (!file_exists($templatePath))
				{
					throw new InvalidArgumentException(Text::sprintf('COM_ROPAYMENTS_CANNOT_LOAD_ADDON', $origin));
				}

				$this->addons[$origin] = $templatePath;
			}
		}

		// Load the file
		if (!file_exists($this->addons[$origin]))
		{
			throw new InvalidArgumentException(Text::sprintf('COM_ROPAYMENTS_CANNOT_LOAD_ADDON', $origin));
		}

		require_once $this->addons[$origin];

		// Instantiate the class
		/** @var AddonInterface $classname */
		$classname = 'Jdideal\Addons\Addon' . $origin;

		return new $classname;
	}

	/**
	 * Check if an addon exists.
	 *
	 * @param   string  $origin  The addon to get the path for
	 *
	 * @return  boolean  True if it exists | False otherwise.
	 *
	 * @since   4.12.0
	 */
	public function exists($origin): bool
	{
		return array_key_exists($origin, $this->addons);
	}
}
