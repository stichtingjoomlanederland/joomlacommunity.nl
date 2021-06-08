<?php

/* ******************************************************************************
 * History:
 * $Log$
 *
 * ******************************************************************************
 * Date : $Date$
 * Revision : $Revision$
 * ******************************************************************************
 */
use Jdideal\Gateway;

/**
 * Contains all response information for a transaction request
 *
 */
class AcquirerTransactionResponse
{
    private $acquirerID;
    private $issuerAuthenticationURL;
    private $transactionID;
	private $purchaseID;
	private $errorMessage = false;
    private $transactionCreateDateTimestamp;

	/**
	 * Construct the class.
	 *
	 * @param   string   $xml      The received XML message.
	 * @param   Gateway  $jdideal  An instance of JdidealGateway.
	 *
	 * @since   1.0
	 *
	 * @throws  Exception
	 * @throws  RuntimeException
	 * @throws  InvalidArgumentException
	 *
	 */
	public function __construct($xml, Gateway $jdideal)
	{
		$xmlHelper = new XmlHelper($jdideal);
		$this->setAcquirerID($xmlHelper->parseFromXml("acquirerID", $xml));
		$this->setIssuerAuthenticationURL(html_entity_decode($xmlHelper->parseFromXml("issuerAuthenticationURL", $xml)));
		$this->setTransactionID($xmlHelper->parseFromXml("transactionID", $xml));
		$this->setPurchaseID($xmlHelper->parseFromXml("purchaseID", $xml));
		$this->setTransactionCreateDateTimestamp($xmlHelper->parseFromXml("transactionCreateDateTimestamp", $xml));

    }
    /**
     * @return Returns the acquirerID.
     */
    function getAcquirerID()
    {
        return $this->acquirerID;
    }
    /**
     * @param acquirerID The acquirerID to set. (mandatory)
     */
    function setAcquirerID( $acquirerID )
    {
        $this->acquirerID = $acquirerID;
    }
    /**
     * @return Returns the issuerAuthenticationURL.
     */
    function getIssuerAuthenticationURL()
    {
        return $this->issuerAuthenticationURL;
    }
    /**
     * @param issuerAuthenticationURL The issuerAuthenticationURL to set.
     */
    function setIssuerAuthenticationURL( $issuerAuthenticationURL )
    {
        $this->issuerAuthenticationURL = $issuerAuthenticationURL;
    }

    /**
     * @return Returns the transactionID.
     */
    function getTransactionID()
    {
        return $this->transactionID;
    }
    /**
     * @param transactionID The transactionID to set.
     */
    function setTransactionID( $transactionID )
    {
        $this->transactionID = $transactionID;
    }
    /**
     * @return Returns the purchaseID.
     */
    function getPurchaseID()
    {
        return $this->purchaseID;
    }
    /**
     * @param purchaseID The purchaseID to set. (mandatory)
     */
    function setPurchaseID( $purchaseID )
    {
        $this->purchaseID = $purchaseID;
    }

    /**
     * @return Returns the transactionCreateDateTimestamp.
     */
    function getTransactionCreateDateTimestamp()
    {
        return $this->transactionCreateDateTimestamp;
    }

    /**
     * @param acquirerID The transactionCreateDateTimestamp to set.
     */
    function setTransactionCreateDateTimestamp( $timestamp )
    {
        $this->transactionCreateDateTimestamp = $timestamp;
    }

	function IsResponseError()
	{
		return false;
	}

}
