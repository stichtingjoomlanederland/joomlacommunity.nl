<?php

/******************************************************************************
 * History:
 * $Log$
 *
 ******************************************************************************
 * Last CheckIn :   $Author$
 * Date :           $Date$
 * Revision :       $Revision$
 ******************************************************************************
 */

use Jdideal\Gateway;

require_once __DIR__ . '/ConnectorHelper.php';

/**
 *  This class is responsible for handling all iDEAL operations and shields
 *  external developers from the complexities of the platform.
 *
 */
class iDEALConnector
{
	private $connectorHelper;
	/**
	 * Construct the class.
	 *
	 * @param   Gateway  $jdideal  An instance of JdidealGateway.
	 *
	 * @since   1.0
	 *
	 * @throws  Exception
	 * @throws  RuntimeException
	 * @throws  InvalidArgumentException
	 *
	 */
	public function __construct(Gateway $jdideal)
	{
		$this->connectorHelper = new ConnectorHelper($jdideal);
	}

	/**
	 * Public function to get the list of issuers that the consumer can choose from.
	 *
	 * @param   Gateway  $jdideal  An instance of JdidealGateway.
	 *
	 * @return  mixed  An instance of DirectoryResponse or "FALSE" on failure.
	 */
	public function GetIssuerList(Gateway $jdideal)
	{
		$xmlMsg = $this->connectorHelper->CreateDirectoryMessage();

		// Post the XML to the server.
		$response = $this->connectorHelper->DirectoryRequest($xmlMsg);

		// If the response did not work out, return an ErrorResponse object.
		$error = $this->connectorHelper->DirectoryResponseHasErrors($response);

		if ($error === false)
		{
			return $error;
		}

		return new DirectoryResponse($response, $jdideal);
	}

	/**
	 * This function submits a transaction request to the server.
	 *
	 * @param string $issuerId The issuer Id to send the request to
	 * @param string $purchaseId The purchase Id that the merchant generates
	 * @param integer $amount The amount in cents for the purchase
	 * @param string $description The description of the transaction
	 * @param string $entranceCode The entrance code for the visitor of the merchant site. Determined by merchant
	 * @param   Gateway  $jdideal  An instance of JdidealGateway.
	 * @param string $optExpirationPeriod Expiration period in specific format. See reference guide. Can be configured in config.
	 * @param string $optMerchantReturnURL The return URL (optional) for the visitor. Optional. Can be configured in config.
	 * @param string $optLanguage The language. Optional.
	 * @return An instance of AcquirerTransactionResponse or "false" on failure.
	 */
	public function RequestTransaction(
		$issuerId,
		$purchaseId,
		$amount,
		$description,
		$entranceCode,
		Gateway $jdideal,
		$optExpirationPeriod = '',
		$optMerchantReturnURL = '',
		$optLanguage = '')
	{
		$xmlMsg = $this->connectorHelper->CreateTransactionMessage(
			$issuerId,
			$purchaseId,
			$amount,
			$description,
			$entranceCode,
			$optExpirationPeriod,
			$optMerchantReturnURL,
			$optLanguage
		);

		// Post the request to the server.
		$response = $this->connectorHelper->TransactionRequest($xmlMsg);

		$error = $this->connectorHelper->TransactionResponseHasErrors($response);

		if ($error === false)
		{
			return $error;
		}

		return new AcquirerTransactionResponse($response, $jdideal);
	}

	/**
	 * This public function makes a transaction status request
	 *
	 * @param   string   $transactionId  The transaction ID to query. (as returned from the TX request)
	 * @param   Gateway  $jdideal        An instance of JdidealGateway.
	 *
	 * @return  mixed  An instance of AcquirerStatusResponse or FALSE on failure.
	 *
	 * @throws Exception
	 * @throws RuntimeException
	 * @throws InvalidArgumentException
	 */
	public function RequestTransactionStatus($transactionId, Gateway $jdideal)
	{
		$xmlMsg = $this->connectorHelper->CreateStatusMessage($transactionId);

		// Post the request to the server.
		$response = $this->connectorHelper->StatusRequest($xmlMsg);

		$error = $this->connectorHelper->StatusResponseHasErrors($response);

		if ($error === false)
		{
			return $error;
		}

		// Build the status response object and pass the data into it.
		return new AcquirerStatusResponse($response, $jdideal);
	}
}
