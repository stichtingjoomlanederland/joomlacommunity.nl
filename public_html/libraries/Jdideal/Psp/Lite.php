<?php
/**
 * @package     JDiDEAL
 * @subpackage  Lite
 *
 * @author      Roland Dalmulder <contact@rolandd.com>
 * @copyright   Copyright (C) 2009 - 2020 RolandD Cyber Produksi. All rights reserved.
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link        https://rolandd.com
 */

namespace Jdideal\Psp;

use Jdideal\Gateway;

defined('_JEXEC') or die;

/**
 * Lite processor.
 *
 * @package     JDiDEAL
 * @subpackage  Lite
 * @since       2.12
 */
class Lite
{
	/**
	 * Database driver
	 *
	 * @var    \JDatabaseDriver
	 * @since  4.0
	 */
	private $db;

	/**
	 * Input processor
	 *
	 * @var    \JInput
	 * @since  4.0
	 */
	private $jinput;

	/**
	 * Live URL
	 *
	 * @var    string
	 * @since  2.13
	 */
	private $liveUrl = 'https://ideal.secure-ing.com/ideal/mpiPayInitIng.do';

	/**
	 * Test URL
	 *
	 * @var    string
	 * @since  2.13
	 */
	private $testUrl = 'https://idealtest.secure-ing.com/ideal/mpiPayInitIng.do';

	/**
	 * Construct the payment reference.
	 *
	 * @param   \Jinput  $jinput  The input object.
	 *
	 * @since   4.0
	 */
	public function __construct(\JInput $jinput)
	{
		// Set the input
		$this->jinput = $jinput;

		// Set the database
		$this->db = \JFactory::getDbo();
	}

	/**
	 * Returns a list of available payment methods.
	 *
	 * @return  array  List of available payment methods.
	 *
	 * @since   3.0
	 */
	public function getAvailablePaymentMethods()
	{
		return array(
			'ideal' => 'iDEAL',
		);
	}

	/**
	 * Return the live URL.
	 *
	 * @return  string  The live URL.
	 *
	 * @since   4.0
	 */
	public function getLiveUrl()
	{
		return $this->liveUrl;
	}

	/**
	 * Return the test URL.
	 *
	 * @return  string  The test URL.
	 *
	 * @since   4.0
	 */
	public function getTestUrl()
	{
		return $this->testUrl;
	}

	/**
	 * Prepare data for the form.
	 *
	 * @param   Gateway  $jdideal  An instance of JdidealGateway.
	 * @param   object   $data     An object with transaction information.
	 *
	 * @return  object  The data for the form.
	 *
	 * @since   2.13
	 *
	 * @throws   \RuntimeException
	 * @throws   \InvalidArgumentException
	 */
	public function getForm(Gateway $jdideal, $data)
	{
		$hashkey           = $jdideal->get('hashkey');
		$data->merchantID  = $jdideal->get('merchantId');
		$data->subID       = $jdideal->get('subId');
		$orderNumber       = $jdideal->get('orderNumber', 'order_number');
		$data->purchaseID  = $data->$orderNumber;
		$data->paymentType = 'ideal';
		$data->validUntil  = date("Y-m-d\\TG:i:s\\Z", strtotime('+1 week'));

		// Go through all products
		$data->amount   = sprintf('%.2f', $data->amount) * 100;
		$items          = 'ororder1' . $data->amount;
		$data->products = '<input type="hidden" name="itemNumber1" value="or">' . "\n";
		$data->products .= '<input type="hidden" name="itemDescription1" value="order">' . "\n";
		$data->products .= '<input type="hidden" name="itemQuantity1" value="1">' . "\n";
		$data->products .= '<input type="hidden" name="itemPrice1" value="' . $data->amount . '">' . "\n";

		// Construct the hashed string
		$shaString = $hashkey . $data->merchantID . $data->subID . $data->amount . $data->purchaseID . $data->paymentType . $data->validUntil . $items;

		// Decode HTML entities
		$clean_shaString = html_entity_decode($shaString);

		// Remove forbidden characters
		$not_allowed = array("\\t", "\n", "\\r", ' ');
		$shaString   = str_replace($not_allowed, '', $clean_shaString);

		// SHA1 calculation
		$data->shasign = sha1($shaString);

		// Other variables not in hash
		$data->language = $jdideal->get('language');
		$data->currency = $jdideal->get('currency');

		// Store the currency
		$jdideal->setCurrency($data->currency, $data->logid);
		$jdideal->log('Currency: ' . $data->currency, $data->logid);

		// Replace some predefined values
		$description       = $jdideal->replacePlaceholders($data->logid, $jdideal->get('description'));
		$data->description = substr($description, 0, 32);

		// Set the root URL
		$root = $jdideal->getUrl();

		// Get the URLs
		$data->urlSuccess = $root . 'cli/notify.php?status=success&logid=' . $data->logid;
		$data->urlCancel  = $root . 'cli/notify.php?status=cancel&logid=' . $data->logid;
		$data->urlError   = $root . 'cli/notify.php?status=failure&logid=' . $data->logid;

		// Store the transaction reference
		$trans = time() . 'R' . $data->logid;
		$jdideal->setTrans($trans, $data->logid);

		return $data;
	}

	/**
	 * Get the log ID.
	 *
	 * @return  int  The ID of the log.
	 *
	 * @since   4.0
	 *
	 * @throws  \RuntimeException
	 */
	public function getLogId()
	{
		// Convert the strings to a real array
		$logId = $this->jinput->getInt('logid', false);

		if (!$logId)
		{
			throw new \RuntimeException(\JText::_('COM_ROPAYMENTS_NO_LOGID_FOUND'));
		}

		return $logId;
	}

	/**
	 * Get the transaction ID.
	 *
	 * @return  int  The ID of the transaction.
	 *
	 * @since   4.0
	 *
	 * @throws  \RuntimeException
	 */
	public function getTransactionId()
	{
		$logId = $this->getLogId();

		$query = $this->db->getQuery(true)
			->select($this->db->quoteName('trans'))
			->from($this->db->quoteName('#__jdidealgateway_logs'))
			->where($this->db->quoteName('id') . ' = ' . (int) $logId);
		$this->db->setQuery($query);

		$transactionReference = $this->db->loadResult();

		if (!$transactionReference)
		{
			throw new \RuntimeException(\JText::_('COM_ROPAYMENTS_NO_TRANSACTIONID_FOUND'));
		}

		// Get the transaction ID
		return $transactionReference;
	}

	/**
	 * Check the transaction status.
	 *
	 * isOK            = Set if the validation is OK
	 * card            = The payment method used by the customer
	 * suggestedAction = The result of the transaction
	 * error_message   = An error message in case there is an error with the transaction
	 * consumer        = Array with info about the customer
	 *
	 * @param   Gateway  $jdideal  An instance of JdidealGateway.
	 * @param   int      $logId    The ID of the transaction log.
	 *
	 * @return  array  Array of transaction details.
	 *
	 * @since   2.13
	 *
	 * @throws  \Exception
	 * @throws  \RuntimeException
	 */
	public function transactionStatus(Gateway $jdideal, $logId)
	{
		// Get some details
		$result = $this->jinput->get('status');

		switch ($result)
		{
			case 'success':
				$status['isOK'] = true;
				$status['suggestedAction'] = 'SUCCESS';
				break;
			case 'cancel':
				$status['isOK'] = true;
				$status['suggestedAction'] = 'CANCELLED';

				// No idea what is wrong since there is no feedback
				$status['error_message'] = '';
				break;
			case 'failure':
				$status['isOK'] = true;
				$status['suggestedAction'] = 'FAILURE';

				// No idea what is wrong since there is no feedback
				$status['error_message'] = '';
				break;
			default:
				$status['isOK'] = false;
				$status['suggestedAction'] = 'OPEN';
				$status['error_message'] = \JText::_('COM_ROPAYMENTS_ING_LITE_NO_STATUS');
				break;
		}

		// No transaction IDs
		$status['transactionID'] = $this->getTransactionId();
		$status['card'] = 'iDEAL';

		$jdideal->setTransactionDetails('iDEAL', 1, $logId);

		// Get the customer info, not available
		$status['consumer'] = array();

		return $status;
	}

	/**
	 * Check who is knocking at the door.
	 *
	 * @return  bool  True if it is the customer | False if it is the PSP.
	 *
	 * @since   4.0
	 */
	public function isCustomer()
	{
		return true;
	}

	/**
	 * Tell RO Payments if status can be checked based on customer.
	 *
	 * @return  boolean  True if user status can be used | False otherwise.
	 *
	 * @since   4.13.0
	 */
	public function canUseCustomerStatus()
	{
		return true;
	}

	/**
	 * Tell RO Payments the bank must be called instead of the bank calling us
	 *
	 * @return  boolean  True if the bank must be called | False if the bank calls us.
	 *
	 * @since   4.14.2
	 */
	public function callHome()
	{
		return false;
	}
}
