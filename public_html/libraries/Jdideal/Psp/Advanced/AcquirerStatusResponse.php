<?php

/* * *****************************************************************************
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
 * This class contains all necessary data that can be returned from a iDEAL AcquirerTrxRequest.
 */
class AcquirerStatusResponse {

    private $acquirerID;
    private $consumerName = "";
    private $consumerIBAN = "";
    private $consumerBIC = "";
    private $amount = "";
    private $currency = "";
    private $transactionID = "";
    private $status = "";
    private $statusDateTime = "";
    private $errorMessage = false;

	/**
	 * Construct the class.
	 *
	 * @param   string   $xml      The response received.
	 * @param   Gateway  $jdideal  An instance of JdidealGateway.
	 *
	 * @since   1.0
	 *
	 * @throws  Exception
	 * @throws  RuntimeException
	 * @throws  InvalidArgumentException
	 */
	public function __construct($xml, Gateway $jdideal)
	{
		$xmlHelper = new XmlHelper($jdideal);
		$this->setAcquirerID($xmlHelper->parseFromXml("acquirerID", $xml));
		$this->setConsumerName($xmlHelper->parseFromXml("consumerName", $xml));
		$this->setConsumerIBAN($xmlHelper->parseFromXml("consumerIBAN", $xml));
		$this->setConsumerBIC($xmlHelper->parseFromXml("consumerBIC", $xml));
		$this->setAmount($xmlHelper->parseFromXml("amount", $xml));
		$this->setCurrency($xmlHelper->parseFromXml("currency", $xml));
		$this->setStatusDateTime($xmlHelper->parseFromXml("statusDateTimestamp", $xml));
		$this->setTransactionID($xmlHelper->parseFromXml("transactionID", $xml));

        // The initial status is INVALID, so that future modifications to
        // this or remote code will yield alarming conditions.
        $this->setStatus( IDEAL_TX_STATUS_INVALID );
        $status = $xmlHelper->parseFromXml( "status", $xml );

        // Determine status identifier (case-insensitive).
        if ( strcasecmp( $status, "success" ) == 0 ) {
            $this->setStatus( IDEAL_TX_STATUS_SUCCESS );
        } else if ( strcasecmp( $status, "Cancelled" ) == 0 ) {
            $this->setStatus( IDEAL_TX_STATUS_CANCELLED );
        } else if ( strcasecmp( $status, "Expired" ) == 0 ) {
            $this->setStatus( IDEAL_TX_STATUS_EXPIRED );
        } else if ( strcasecmp( $status, "Failure" ) == 0 ) {
            $this->setStatus( IDEAL_TX_STATUS_FAILURE );
        } else if ( strcasecmp( $status, "Open" ) == 0 ) {
            $this->setStatus( IDEAL_TX_STATUS_OPEN );
        }
    }

    /**
     * @return Returns the acquirerID.
     */
    function getAcquirerID() {
        return $this->acquirerID;
    }

    /**
     * @param acquirerID The acquirerID to set. (mandatory)
     */
    function setAcquirerID($acquirerID) {
        $this->acquirerID = $acquirerID;
    }

    /**
     * @return Returns the consumerIBAN.
     */
    function getConsumerIBAN() {
        return $this->consumerIBAN;
    }

    /**
     * @param consumerIBAN The consumerIBAN to set.
     */
    function setConsumerIBAN($consumerIBAN) {
        $this->consumerIBAN = $consumerIBAN;
    }

    /**
     * @return Returns the consumerBIC.
     */
    function getConsumerBIC() {
        return $this->consumerBIC;
    }

    /**
     * @param consumerBIC The consumerBIC to set.
     */
    function setConsumerBIC($consumerBIC) {
        $this->consumerBIC = $consumerBIC;
    }

    /**
     * @return Returns the consumerName.
     */
    function getConsumerName() {
        return $this->consumerName;
    }

    /**
     * @param consumerName The consumerName to set.
     */
    function setConsumerName($consumerName) {
        $this->consumerName = $consumerName;
    }

    /**
     * @return Returns the transactionID.
     */
    function getTransactionID() {
        return $this->transactionID;
    }

    /**
     * @param transactionID The transactionID to set.
     */
    function setTransactionID($transactionID) {
        $this->transactionID = $transactionID;
    }

    /**
     * @return Returns the status. See the definitions
     */
    function getStatus() {
        return $this->status;
    }

    /**
     * @param status The status to set. See the definitions
     */
    function setStatus($status) {
        $this->status = $status;
    }

    /**
     * @return Returns the amount. See the definitions
     */
    function getAmount() {
        return $this->amount;
    }

    /**
     * @param status The amount to set. See the definitions
     */
    function setAmount($amount) {
        $this->amount = $amount;
    }

    /**
     * @return Returns the currency. See the definitions
     */
    function getCurrency() {
        return $this->currency;
    }

    /**
     * @param status The currency to set. See the definitions
     */
    function setCurrency($currency) {
        $this->currency = $currency;
    }

    /**
     * @return Returns the statusDateTime. See the definitions
     */
    function getStatusDateTime() {
        return $this->statusDateTime;
    }

    /**
     * @param status The statusDateTime to set. See the definitions
     */
    function setStatusDateTime($statusDateTime) {
        $this->statusDateTime = $statusDateTime;
    }

    function IsResponseError() {
        return false;
    }

}
