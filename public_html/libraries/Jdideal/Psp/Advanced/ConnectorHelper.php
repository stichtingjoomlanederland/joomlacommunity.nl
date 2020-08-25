<?php


// Import the necessary classes and configuration
use Jdideal\Gateway;

require_once JPATH_LIBRARIES . '/Jdideal/Psp/Advanced/ConfigurationManager.php';
require_once JPATH_LIBRARIES . '/Jdideal/Psp/Advanced/XmlHelper.php';
require_once JPATH_LIBRARIES . '/Jdideal/Psp/Advanced/AcquirerStatusResponse.php';
require_once JPATH_LIBRARIES . '/Jdideal/Psp/Advanced/AcquirerTransactionResponse.php';
require_once JPATH_LIBRARIES . '/Jdideal/Psp/Advanced/DirectoryResponse.php';
require_once JPATH_LIBRARIES . '/Jdideal/Psp/Advanced/ErrorResponse.php';
require_once JPATH_LIBRARIES . '/Jdideal/Psp/Advanced/IssuerEntry.php';
require_once JPATH_LIBRARIES . '/Jdideal/Psp/Advanced/CountryEntry.php';
require_once JPATH_LIBRARIES . '/Jdideal/Psp/Advanced/xmlseclibs.php';

/**
 * Definition of global constants.
 * Can be used but should not be modified by merchant
 */
define('IDEAL_TX_STATUS_INVALID', 0x00);
define('IDEAL_TX_STATUS_SUCCESS', 0x01);
define('IDEAL_TX_STATUS_CANCELLED', 0x02);
define('IDEAL_TX_STATUS_EXPIRED', 0x03);
define('IDEAL_TX_STATUS_FAILURE', 0x04);
define('IDEAL_TX_STATUS_OPEN', 0x05);

define('ERROR_INVALID_SIGNATURE', "1000");
define('ERROR_COULD_NOT_CONNECT', "1001");
define('ERROR_PRIVKEY_INVALID', "1002");
define('ERROR_COULD_NOT_SIGN', "1003");
define('ERROR_CERTIFICATE_INVALID', "1004");
define('ERROR_COULD_NOT_VERIFY', "1005");
define('ERROR_MISSING_CONFIG', "1006");
define('ERROR_PARAMETER', "1007");
define('ERROR_INVALID_SIGNCERT', "1008");

/**
 * Definition of private constants
 */
define('IDEAL_PRV_GENERIEKE_FOUTMELDING', "Betalen met IDEAL is nu niet mogelijk. Probeer het later nogmaals of betaal op een andere manier.");
define('IDEAL_PRV_STATUS_FOUTMELDING', "Het resultaat van uw betaling is nog niet bij ons bekend. U kunt desgewenst uw betaling controleren in uw Internetbankieren.");
define('TRACE_DEBUG', "DEBUG");
define('TRACE_ERROR', "ERROR");


class ConnectorHelper
{
	private $xmlHelper;

	private $config;

	// An object that maintains error information for each request
	private $error;

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
		$this->config = ConfigurationManager::getInstance($jdideal);

		$this->xmlHelper = new XmlHelper($jdideal);
	}

	public function CreateDirectoryMessage()
	{
		$this->clearError();

		$configCheck = $this->config->CheckConfig();

		if ($configCheck != "OK")
		{
			$errorResponse = new ErrorResponse();
			$errorResponse->setErrorCode("001");
			$errorResponse->setErrorMessage("Config error: " . $configCheck);
			$errorResponse->setConsumerMessage("");

			return $errorResponse;
		}

		// Build up the XML header for this request
		$xmlMsg = $this->xmlHelper->getXMLHeader(
			"DirectoryReq",
			null,
			null,
			null,
			null);

		if (!$xmlMsg)
		{
			return false;
		}

		// Close the request information.
		$xmlMsg .= "</DirectoryReq>\n";
		return $xmlMsg;
	}

	public function DirectoryResponseHasErrors($response)
	{
		$errorResponse = $this->responseHasErrors($response);

		if ($errorResponse != false)
		{
			return $errorResponse;
		}

		if ($this->xmlHelper->parseFromXml("acquirerID", $response) == "")
		{
			$errorResponse = new ErrorResponse();

			$errorResponse->setErrorCode("ING1001");
			$errorResponse->setErrorMessage("DirectoryList service probleem");
			$errorResponse->setConsumerMessage("");

			return $errorResponse;
		}
		return false;
	}

	public function TransactionResponseHasErrors($response)
	{
		$errorResponse = $this->responseHasErrors($response);
		if ($errorResponse != false)
		{
			return $errorResponse;
		}

		if ($this->xmlHelper->parseFromXml("acquirerID", $response) == "")
		{
			$errorResponse = new ErrorResponse();

			$errorResponse->setErrorCode("ING1001");
			$errorResponse->setErrorMessage("Transactie mislukt (aquirer side)");
			$errorResponse->setConsumerMessage("");

			return $errorResponse;
		}
	}


	public function CreateTransactionMessage($issuerId,
		$purchaseId,
		$amount,
		$description,
		$entranceCode,
		$optExpirationPeriod = "",
		$optMerchantReturnURL = "",
		$optLanguage = "")
	{
		$this->clearError();

		$configCheck = $this->config->CheckConfig();

		if ($configCheck != "OK")
		{
			$this->setError(ERROR_MISSING_CONFIG, "Config error: " . $configCheck, IDEAL_PRV_GENERIEKE_FOUTMELDING);
			return $this->getError();
		}

		if (!$this->verifyNotNull($issuerId, "issuerId") ||
			!$this->verifyNotNull($purchaseId, "purchaseId") ||
			!$this->verifyNotNull($amount, "amount") ||
			!$this->verifyNotNull($description, "description") ||
			!$this->verifyNotNull($entranceCode, "entranceCode")
		)
		{
			$errorResponse = $this->getError();
			return $errorResponse;
		}

		//check amount length
		$amountOK = $this->LengthCheck("Amount", $amount, "12");
		if ($amountOK != "ok")
		{
			return $this->getError();
		}
		//check for diacritical characters
		$amountOK = $this->CheckDiacritical("Amount", $amount);
		if ($amountOK != "ok")
		{
			return $this->getError();
		}

		//check description length
		$descriptionOK = $this->LengthCheck("Description", $description, "32");
		if ($descriptionOK != "ok")
		{
			return $this->getError();
		}
		//check for diacritical characters
		$descriptionOK = $this->CheckDiacritical("Description", $description);
		if ($descriptionOK != "ok")
		{
			return $this->getError();
		}

		//check entrancecode length
		$entranceCodeOK = $this->LengthCheck("Entrancecode", $entranceCode, "40");
		if ($entranceCodeOK != "ok")
		{
			return $this->getError();
		}
		//check for diacritical characters
		$entranceCodeOK = $this->CheckDiacritical("Entrancecode", $entranceCode);
		if ($entranceCodeOK != "ok")
		{
			return $this->getError();
		}

		//check purchaseid length
		$purchaseIDOK = $this->LengthCheck("PurchaseID", $purchaseId, "16");
		if ($purchaseIDOK != "ok")
		{
			return $this->getError();
		}
		//check for diacritical characters
		$purchaseIDOK = $this->CheckDiacritical("PurchaseID", $purchaseId);
		if ($purchaseIDOK != "ok")
		{
			return $this->getError();
		}

		// According to the specification, these values should be hardcoded.
		$currency = "EUR";
		$language = "nl";

		$result = true;

		// Retrieve these values from the configuration file.
		$cfgExpirationPeriod = $this->config->GetConfiguration("EXPIRATIONPERIOD", true, $result);
		$cfgMerchantReturnURL = $this->config->GetConfiguration("MERCHANTRETURNURL", true, $result);

		if (isset($optExpirationPeriod) && ($optExpirationPeriod != ""))
		{
			// If a (valid?) optional setting was specified for the expiration period, use it.
			$expirationPeriod = $optExpirationPeriod;
		}
		else
		{
			$expirationPeriod = $cfgExpirationPeriod;
		}

		if (isset($optMerchantReturnURL) && ($optMerchantReturnURL != ""))
		{
			// If a (valid?) optional setting was specified for the merchantReturnURL, use it.
			$merchantReturnURL = $optMerchantReturnURL;
		}
		else
		{
			$merchantReturnURL = $cfgMerchantReturnURL;
		}

		if (isset($optLanguage) && ($optLanguage != ""))
		{
			// If a (valid?) optional setting was specified for the merchantReturnURL, use it.
			$language = $optLanguage;
		}

		if (strstr($amount, '.') === false)
		{
			$amount .= '.00';
		}

		if (!$this->verifyNotNull($merchantReturnURL, "merchantReturnURL"))
		{
			return false;
		}

		// Build the XML header for the transaction request
		$xmlMsg = $this->xmlHelper->getXMLHeader(
			"AcquirerTrxReq",
			$issuerId,
			"<Issuer>\n<issuerID>" . $issuerId . "</issuerID>\n</Issuer>\n",
			$merchantReturnURL . $purchaseId . $amount . $currency . $language . $description . $entranceCode,
			"<merchantReturnURL>" . $merchantReturnURL . "</merchantReturnURL>\n");

		if (!$xmlMsg)
		{
			return false;
		}

		// Add transaction information to the request.
		$xmlMsg .= "<Transaction>\n<purchaseID>" . $purchaseId . "</purchaseID>\n";
		$xmlMsg .= "<amount>" . $amount . "</amount>\n";
		$xmlMsg .= "<currency>" . $currency . "</currency>\n";
		if (isset($expirationPeriod))
		{
			$xmlMsg .= "<expirationPeriod>" . $expirationPeriod . "</expirationPeriod>\n";
		}
		$xmlMsg .= "<language>" . $language . "</language>\n";
		$xmlMsg .= "<description>" . $description . "</description>\n";
		$xmlMsg .= "<entranceCode>" . $entranceCode . "</entranceCode>\n";
		$xmlMsg .= "</Transaction>\n";
		$xmlMsg .= "</AcquirerTrxReq>\n";
		return $xmlMsg;
	}

	public function CreateStatusMessage($transactionId)
	{
		$this->clearError();

		$configCheck = $this->config->CheckConfig();

		if ($configCheck != "OK")
		{
			$errorResponse = new ErrorResponse();

			$errorResponse->setErrorCode("001");
			$errorResponse->setErrorMessage("Config error: " . $configCheck);
			$errorResponse->setConsumerMessage("");

			return $errorResponse;
		}

		//check TransactionId length
		$transactionIdOK = $this->LengthCheck("TransactionID", $transactionId, "16");
		if ($transactionIdOK != "ok")
		{
			return $this->getError();
		}


		if (!$this->verifyNotNull($transactionId, "transactionId"))
		{
			$errorResponse = $this->getError();

			return $errorResponse;
		}

		// Build the status request XML.
		$xmlMsg = $this->xmlHelper->getXMLHeader(
			"AcquirerStatusReq",
			null,
			null,
			$transactionId,
			null);

		if (!$xmlMsg)
		{
			return false;
		}

		// Add transaction information.
		$xmlMsg .= "<Transaction>\n<transactionID>" . $transactionId . "</transactionID></Transaction>\n";
		$xmlMsg .= "</AcquirerStatusReq>\n";
		return $xmlMsg;
	}

	/**
	 * Check if the response has any errors.
	 *
	 * @param   string  $response  The response received from the bank.
	 *
	 * @return  mixed  An instance of ErrorResponse if we can identify the error otherwise false.
	 *
	 * @since   1.0
	 */
	public function StatusResponseHasErrors($response)
	{

		$errorResponse = $this->responseHasErrors($response);

		if ($errorResponse != false)
		{
			return $errorResponse;
		}

		if (($this->xmlHelper->parseFromXml("acquirerID", $response) == "") || (!$response))
		{
			$errorResponse = new ErrorResponse;

			$errorResponse->setErrorCode("ING1001");
			$errorResponse->setErrorMessage("Status lookup mislukt (aquirer side)");
			$errorResponse->setConsumerMessage("");

			return $errorResponse;
		}

		return false;
	}


	/**
	 * This public function returns the ErrorResponse object or "" if it does not exist.
	 *
	 * @return ErrorResponse object or an emptry string "".
	 */
	public function getError()
	{
		return $this->error;
	}


	public function DirectoryRequest($xml)
	{
		return $this->PostXMLData($xml, "ACQUIRERDIRECTORYURL");
	}

	public function TransactionRequest($xml)
	{
		return $this->PostXMLData($xml, "ACQUIRERTRANSACTIONURL");
	}

	public function StatusRequest($xml)
	{
		return $this->PostXMLData($xml, "ACQUIRERSTATUSURL");
	}


	/**************************************************************************
	 * ========================================================================
	 *                     Private functions
	 * ========================================================================
	 *************************************************************************/


	/**
	 * Posts XML data to the server or proxy.
	 *
	 * @param string $msg The message to post.
	 * @param string $urlName Optional.
	 * @return string        The response of the server.
	 */
	private function PostXMLData($msg, $urlName = "")
	{
		$result = true;
		if (is_a($msg, "ErrorResponse"))
		{
			return $msg;
		}

		$msg = $this->signMessage($msg);
		if ($msg === false)
		{
			return false;
		}

		if ($this->config->GetConfiguration("PROXY", true, $result) == "")
		{
			$acquirerUrl = $this->config->GetConfiguration($urlName, false, $result);
			if (!$result)
			{
				$result = true;
				$acquirerUrl = $this->config->GetConfiguration("ACQUIRERURL", false, $result);
			}
			if (!$result)
			{
				$this->setError(ERROR_MISSING_CONFIG, "Missing configuration: " . $name, IDEAL_PRV_GENERIEKE_FOUTMELDING);
				return false;
			}

			// If Proxy configuration does not exist
			$response = $this->PostWithCUrl($acquirerUrl, $msg);

			try
			{
				if ($this->verifyMessage($response))
				{
					return $response;
				}
			} catch (Exception $exception)
			{

			}
			$errorResponse = new ErrorResponse();
			$errorResponse->setErrorCode("ING1005");
			$errorResponse->setErrorMessage("Response failed schema validation");
			$errorResponse->setConsumerMessage("");

			return $errorResponse;
		}

		$proxy = $this->config->GetConfiguration("PROXY", false, $result);
		$proxyUrl = $this->config->GetConfiguration("PROXYACQURL", false, $result);

		if (!$result)
		{
			return false;
		}

		// if proxy configuration exists
		$response = $this->PostToHostProxy($proxy, $proxyUrl, $msg);

		if ($this->verifyMessage($response))
		{
			return $response;
		}
		return false;
	}


	/**
	 * Creates a new ErrorResponse object and populates it with the arguments
	 *
	 * @param unknown_type $errCode The error code to return. This is either a code from the platform or an internal code.
	 * @param unknown_type $errMsg The error message. This is not meant for display to the consumer.
	 * @param unknown_type $consumerMsg The consumer message. The error message to be shown to the user.
	 */
	private function setError($errCode, $errMsg, $consumerMsg)
	{
		$this->error = new ErrorResponse();
		$this->error->setErrorCode($errCode);
		$this->error->setErrorMessage($errMsg);
		if ($consumerMsg)
		{
			$this->error->setConsumerMessage($consumerMsg);
		}
		else
		{
			$this->error->setConsumerMessage(IDEAL_PRV_GENERIEKE_FOUTMELDING);
		}
	}

	/**
	 * Clears the error conditions.
	 */
	private function clearError()
	{
		$this->error = "";
	}


	/**
	 * Create a certificate fingerprint
	 *
	 * @param string $filename File containing the certificate
	 * @return string    A hex string of the certificate fingerprint
	 */
	private function createCertFingerprint($filename)
	{

		// Find the certificate with the given path
		$fullPath = SECURE_PATH . "/" . $filename;
		// Open the certificate file for reading
		$fp = fopen($fullPath, "r");

		if (!$fp)
		{
			logMessage(TRACE_ERROR, "Could not read certificate [" . $fullPath . "]. It may be invalid.");
			$this->setError(ERROR_CERTIFICATE_INVALID, "Could not read certificate", IDEAL_PRV_GENERIEKE_FOUTMELDING);
			return false;
		}

		// Read in the certificate, then convert to X.509-style certificate
		// and export it for later use.
		$cert = fread($fp, 8192);
		fclose($fp);

		$data = openssl_x509_read($cert);

		if (!$data)
		{
			logMessage(TRACE_ERROR, "Could not read certificate [" . $fullPath . "]. It may be invalid.");
			$this->setError(ERROR_CERTIFICATE_INVALID, "Could not read certificate", IDEAL_PRV_GENERIEKE_FOUTMELDING);
			return false;
		}

		if (!openssl_x509_export($data, $data))
		{
			logMessage(TRACE_ERROR, "Could not export certificate [" . $fullPath . "]. It may be invalid.");
			$this->setError(ERROR_CERTIFICATE_INVALID, "Could not export certificate", IDEAL_PRV_GENERIEKE_FOUTMELDING);
			return false;
		}

		// Remove any ASCII armor
		$data = str_replace("-----BEGIN CERTIFICATE-----", "", $data);
		$data = str_replace("-----END CERTIFICATE-----", "", $data);

		// Decode the public key.
		$data = base64_decode($data);
		// Digest the binary public key with SHA-1.
		$fingerprint = sha1($data);

		// Ensure all hexadecimal letters are uppercase.
		$fingerprint = strtoupper($fingerprint);

		return $fingerprint;
	}

	/**
	 * Gets a valid certificate file name based on the certificate fingerprint.
	 * Uses configuration items in the config file, which are incremented when new
	 * security certificates are issued:
	 * certificate0=ideal1.crt
	 * certificate1=ideal2.crt
	 * etc...
	 *
	 * @param string $fingerprint A hexadecimal representation of a certificate's fingerprint
	 * @return string    The filename containing the certificate corresponding to the fingerprint
	 */
	private function getCertificateFileName($fingerprint)
	{
		$count = 0;
		$result = true;

		// Don't care whether it exists, that is checked later.
		$certFilename = $this->config->GetConfiguration("CERTIFICATE" . $count, true, $result);

		// Check if the configuration file contains such an item
		while (isset($certFilename))
		{
			// Find the certificate with the given path
			$fullPath = SECURE_PATH . "/" . $certFilename;

			if ($fullPath === SECURE_PATH . '/')
			{
				// No more certificates left to be verified.
				break;
			}

			// Generate a fingerprint from the certificate in the file.
			$buff = $this->createCertFingerprint($certFilename);

			if ($buff == false)
			{
				// Could not create fingerprint from configured certificate.
				return false;
			}

			// Check if the fingerprint is equal to the desired one.
			if ($fingerprint == $buff)
			{
				return $certFilename;
			}

			// Start looking for next certificate
			$count++;
			$certFilename = $this->config->GetConfiguration("CERTIFICATE" . $count, true, $result);
		}

		logMessage(TRACE_ERROR, "Could not find certificate with fingerprint [" . $fingerprint . "]");
		$this->setError(ERROR_COULD_NOT_VERIFY, "Could not verify message", IDEAL_PRV_GENERIEKE_FOUTMELDING);

		// By default, report no success.
		return false;
	}

	/**
	 * Signs the message using xmldsig.
	 *
	 * @param string $message the xml message that needs to be signed
	 * @return string           the signed xml message
	 */
	private function signMessage($message)
	{

		// Collect the errors ourselves
		libxml_use_internal_errors(true);

		$messageAsXML = new DOMDocument();
		$messageAsXML->loadXML($message);

		$objDSig = new XMLSecurityDSig();
		$objDSig->setCanonicalMethod(XMLSecurityDSig::EXC_C14N);
		$objDSig->addReference($messageAsXML, XMLSecurityDSig::SHA256, array('http://www.w3.org/2000/09/xmldsig#enveloped-signature'), array('force_uri' => true));

		$objKey = new XMLSecurityKey(XMLSecurityKey::RSA_SHA256, array('type' => 'private'));

		/* load private key */
		$privateKeyPath = SECURE_PATH . "/" . $this->config->GetConfiguration("PRIVATEKEY", false, $result);
		$objKey->passphrase = $this->config->GetConfiguration("PRIVATEKEYPASS", true, $result);
		$objKey->loadKey($privateKeyPath, TRUE);

		$objDSig->sign($objKey);

		$fingerprint = $this->createCertFingerprint($this->config->GetConfiguration("PRIVATECERT", false, $result));

		$objDSig->addKeyInfoAndName($fingerprint);
		$objDSig->appendSignature($messageAsXML->documentElement);

		if (!$this->schemaValidate($messageAsXML))
		{
			return false;
		}

		return $messageAsXML->saveXML();
	}

	private function verifyMessage($message)
	{
		// Collect the errors ourselves
		libxml_use_internal_errors(true);

		$messageAsXML = new DOMDocument();
		$messageAsXML->loadXML($message);

		if (!$this->schemaValidate($messageAsXML))
		{
			return false;
		}

		$objXMLSecDSig = new XMLSecurityDSig();
		$objDSig = $objXMLSecDSig->locateSignature($messageAsXML);

		if (!$objDSig)
		{
			throw new Exception("Cannot locate Signature Node");
		}
		$objXMLSecDSig->canonicalizeSignedInfo();
		//$objXMLSecDSig->idKeys = array('wsu:Id');
		//$objXMLSecDSig->idNS = array('wsu'=>'http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-utility-1.0.xsd');

		$retVal = $objXMLSecDSig->validateReference();

		if (!$retVal)
		{
			throw new Exception("Reference Validation Failed");
		}

		$objKey = $objXMLSecDSig->locateKey();
		if (!$objKey)
		{
			throw new Exception("We have no idea about the key");
		}

		$objKeyInfo = XMLSecEnc::staticLocateKeyInfo($objKey, $objDSig);

		$certFile = $this->getCertificateFileName($objKeyInfo->name);
		$objKey->loadKey(SECURE_PATH . '/' . $certFile, TRUE);


		if ($objXMLSecDSig->verify($objKey))
		{
			return true;
		}
		return false;
	}

	private function schemaValidate($message, $isString = false)
	{
		if ($isString === true)
		{
			$messageAsString = $message;
			$message = new DOMDocument();
			$message->loadXML($messageAsString);
		}
		if ($message->schemaValidate(SECURE_PATH . "/AcceptantAcquirer_v331.xsd"))
		{
			$xmlmsg = $message->saveXML();
			logMessage(TRACE_DEBUG, "The processed message :" . $xmlmsg);
			return true;
		}
		else
		{
			$errors = libxml_get_errors();
			$xml = explode("\n", $message->saveXML());
			$xmlmsg = $message->saveXML();


			foreach ($errors as $error)
			{
				$errmsg = $this->display_xml_error($error, $xml);
				logMessage(TRACE_DEBUG, $errmsg);
			}
			logMessage(TRACE_DEBUG, "The response  [" . $xmlmsg . "] does not pass schema validation.");
			$this->setError(ERROR_PARAMETER, $errmsg, IDEAL_PRV_GENERIEKE_FOUTMELDING);
			return false;
		}
	}

	private function display_xml_error($error, $xml)
	{
		if (isset($xml[$error->line - 1]))
		{
			$return = $xml[$error->line - 1] . "\n";
		}
		else
		{
			$return = '';
		}
		$return .= str_repeat('-', $error->column) . "^\n";

		switch ($error->level)
		{
			case LIBXML_ERR_WARNING:
				$return .= "Warning $error->code: ";
				break;
			case LIBXML_ERR_ERROR:
				$return .= "Error $error->code: ";
				break;
			case LIBXML_ERR_FATAL:
				$return .= "Fatal Error $error->code: ";
				break;
		}

		$return .= trim($error->message);

		return $return . "\n\n";
	}

	/**
	 * Posts a message to the url.
	 *
	 * @param string $url The URL to send the message to.
	 * @param string $xml_data The data to send
	 * @param string $proxy The proxy if necesery, null otherwise
	 * @return string    The response from the server.
	 */
	private function PostWithCUrl($url, $xml_data, $proxy = null)
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: text/xml'));
		curl_setopt($ch, CURLOPT_POSTFIELDS, "$xml_data");
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		if (isset($proxy))
		{
			$idx = strrpos($proxy, ":");
			$proxyHost = substr($proxy, 0, $idx);
			$idx = strpos($proxy, ":");
			$proxyPort = substr($proxy, $idx + 1);
			curl_setopt($ch, CURLOPT_PROXY, $proxyHost);
			curl_setopt($ch, CURLOPT_PROXYPORT, $proxyPort);
		}
		logMessage(TRACE_DEBUG, "calling: " . $url);

		$output = curl_exec($ch);
		curl_close($ch);
		return $this->checkForErrors($output);
	}

	private function checkForErrors($res)
	{
		// logMessage the response
		if ($this->xmlHelper->parseFromXml("ErrorRes", $res))
		{
			// If the response was an error message, parse it and return an error.
			$this->setError(
				$this->xmlHelper->parseFromXml("errorCode", $res),
				$this->xmlHelper->parseFromXml("errorMessage", $res),
				$this->xmlHelper->parseFromXml("consumerMessage", $res));

			return $this->getError();
		}

		// Return the (textual) response
		return $res;
	}


	/**
	 * Verifies if the parameter is not empty.
	 *
	 * @param string $paramValue
	 * @param string $paramName
	 * @return bool
	 */
	private function verifyNotNull($paramValue, $paramName)
	{
		if ((!isset($paramValue)) || ($paramValue == ""))
		{
			logMessage(TRACE_ERROR, "The parameter [" . $paramName . "] should have a value.");
			$this->setError(ERROR_PARAMETER, "Empty parameter not allowed: " . $paramName, IDEAL_PRV_GENERIEKE_FOUTMELDING);
			return false;
		}
		return true;
	}

	/**
	 * Verifies if the parameter is not too long.
	 *
	 * @param string $checkName
	 * @param string $checkVariable
	 * @param string $checkLength
	 *
	 * @return ErrorResponse object when failed, string when succeeded
	 */
	private function LengthCheck($checkName, $checkVariable, $checkLength)
	{
		if (strlen($checkVariable) > $checkLength)
		{
			$this->setError(ERROR_PARAMETER, $checkName . " too long", IDEAL_PRV_GENERIEKE_FOUTMELDING);

			return "NotOk";
		}
		else
		{
			return "ok";
		}
	}

	/**
	 * Checks if the inserted variable ($checkVariable) contains diacritical characters.
	 * If so, it will return an ErrorResponse object. If not, the string "ok" is returned.
	 *
	 * @param string $checkName
	 * @param string $checkVariable
	 *
	 * @return ErrorResponse object when failed, string when succeeded
	 */
	private function CheckDiacritical($checkName, $checkVariable)
	{
		//$pattern = "/^[A-Za-z0-9\=\ \%\*\+\,\.\/\/\&\@\"\'\:\;\?\(\)\$]/";
		$pattern = "/[ďż˝ďż˝ďż˝ďż˝ďż˝ďż˝ďż˝ďż˝ďż˝ďż˝ďż˝ďż˝ďż˝ďż˝ďż˝ďż˝ďż˝ďż˝ďż˝ďż˝ďż˝ďż˝ďż˝ďż˝ďż˝ďż˝ďż˝ďż˝ďż˝]/";

		if (preg_match($pattern, $checkVariable, $matches))
		{
			$this->setError(ERROR_PARAMETER, $checkName . " contains diacritical or non-permitted character(s)", IDEAL_PRV_GENERIEKE_FOUTMELDING);

			return "NotOk";
		}
		else
		{
			return "ok";
		}
	}

	private function responseHasErrors($response)
	{
		if (is_a($response, 'ErrorResponse'))
		{
			return $response;
		}

		if ($this->xmlHelper->parseFromXml('errorCode', $response) !== '')
		{
			$errorResponse = new ErrorResponse;

			$errorResponse->setErrorCode($this->xmlHelper->parseFromXml('errorCode', $response));
			$errorResponse->setErrorMessage($this->xmlHelper->parseFromXml('errorMessage', $response));
			$errorResponse->setConsumerMessage($this->xmlHelper->parseFromXml('consumerMessage', $response));

			return $errorResponse;
		}
		return false;
	}

}
