<?php

/* ******************************************************************************
 * History:
 * $Log$
 * ******************************************************************************
 * Date : $Date$
 * Revision : $Revision$
 * ******************************************************************************
 */

/**
 * Contains all information for a Directory List request.
 */

use Jdideal\Gateway;

require_once("IssuerEntry.php");

class DirectoryResponse
{
	private $acquirerID        = '';

	private $directoryDateTime = '';

	private $countries         = array();

	private $errorMessage      = false;

	public function __construct($xml, Gateway $jdideal)
	{
		$this->xmlHelper = new XmlHelper($jdideal);

		$this->setAcquirerID($this->xmlHelper->parseFromXml("acquirerID", $xml));
		$this->setDirectoryDateTimeStamp($this->xmlHelper->parseFromXml("directoryDateTimestamp", $xml));

		while (strpos($xml, "<Country>") != false)
		{
			$country      = new CountryEntry();
			$countryNames = $this->xmlHelper->parseFromXml("countryNames", $xml);
			$country->setCountryNames($countryNames);
			$x = 0;
			// While there are issuers to be read from the stream
			while (strpos($xml, "<issuerID>") && strpos($xml, "<issuerID>") < strpos($xml, "</Country>"))
			{
				// Read the information for the next issuer.
				$issuerID   = $this->xmlHelper->parseFromXml("issuerID", $xml);
				$issuerName = $this->xmlHelper->parseFromXml("issuerName", $xml);

				// Create a new entry and add it to the list
				$issuerEntry = new IssuerEntry();
				$issuerEntry->setIssuerID($issuerID);
				$issuerEntry->setIssuerName($issuerName);
				$country->addIssuer($issuerEntry);

				// Find the next issuer.
				$xml = substr($xml, strpos($xml, "</Issuer>") + 9);
			}
			$this->addCountry($country);
			$xml = substr($xml, strpos($xml, "</Country>") + 10);
		}
	}

	/**
	 * @return Returns a list if IssuerEntry objects for the short listing only.
	 * The List contains all Issuers that were sent by the acquirer System during the Directory Request.
	 * The Issuers are stored as IssuerEntry objects.
	 */
	public function getCountries()
	{
		return $this->countries;
	}

	/**
	 * @return Returns the acquirerID from the answer XML message.
	 */
	public function getAcquirerID()
	{
		return $this->acquirerID;
	}

	/**
	 * @param sets the acquirerID
	 */
	public function setAcquirerID($acquirerID)
	{
		$this->acquirerID = $acquirerID;
	}

	/**
	 * @return Returns the directory date/time stamp from the response XML message.
	 */
	public function getDirectoryDateTimestamp()
	{
		return $this->directoryDateTime;
	}

	/**
	 * @param sets the directory date time stamp
	 */
	public function setDirectoryDateTimestamp($directoryDateTime)
	{
		$this->directoryDateTime = $directoryDateTime;
	}

	/**
	 * adds an Issuer to the IssuerList
	 */
	public function addCountry($entry)
	{
		if (is_a($entry, "CountryEntry"))
		{
			$this->countries[$entry->getCountryNames()] = $entry;
			ksort($this->countries);
		}
	}

	public function IsResponseError()
	{
		return false;
	}
}
