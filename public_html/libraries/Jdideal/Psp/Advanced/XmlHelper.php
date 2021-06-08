<?php

use Jdideal\Gateway;

class XmlHelper
{
	private $config;

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
	}

	/**
	 * Function to parse XML
	 *
	 * @param string $key The XML tag to look for.
	 * @param string $xml The XML string to look into.
	 *
	 * @return string    The value (PCDATA) inbetween the tag.
	 */
	function parseFromXml($key, $xml)
	{
		$begin = 0;
		$end   = 0;

		// Find the first occurrence of the tag
		$begin = strpos($xml, "<" . $key . ">");
		if ($begin === false)
		{
			return false;
		}

		// Find the end position of the tag.
		$begin += strlen($key) + 2;
		$end = strpos($xml, "</" . $key . ">");

		if ($end === false)
		{
			return false;
		}

		// Get the value inbetween the tags and replace the &amp; character.
		$result = substr($xml, $begin, $end - $begin);
		$result = str_replace("&amp;", "&", $result);

		// Decode it with UTF-8
		return utf8_decode($result);
	}

	/**
	 * Builds up the XML message header.
	 *
	 * @param string $msgType              The type of message to construct.
	 * @param string $firstCustomIdInsert  The identifier value(s) to prepend to the hash ID.
	 * @param string $firstCustomFragment  The fragment to insert in the header before the general part.
	 * @param string $secondCustomIdInsert The identifier value(s) to append to the hash ID.
	 * @param string $secondCustomFragment THe XML fragment to append to the header after the general part.
	 *
	 * @return string
	 */
	function getXMLHeader(
		$msgType,
		$firstCustomIdInsert,
		$firstCustomFragment,
		$secondCustomIdInsert,
		$secondCustomFragment)
	{
		// Determine the (string) timestamp for the header and hash id.
		$timestamp = getCurrentDateTime();
		$result    = true;

		// Merchant ID and sub ID come from the configuration file.
		$merchantId = utf8_encode($this->config->GetConfiguration("MERCHANTID", false, $result));
		$subId      = utf8_encode($this->config->GetConfiguration("SUBID", false, $result));

		if (!$result)
		{
			return false;
		}

		// Start building the header.
		$xmlHeader = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n"
			. "<" . $msgType . " xmlns=\"http://www.idealdesk.com/ideal/messages/mer-acq/3.3.1\" version=\"3.3.1\">\n"
			. "<createDateTimestamp>" . $timestamp . "</createDateTimestamp>\n";

		if ($firstCustomFragment)
		{
			if ($firstCustomFragment != "")
			{
				// If there is a custom fragment to prepend, insert it here.
				$xmlHeader .= $firstCustomFragment . "\n";
			}
		}

		// The general parts of the header
		$xmlHeader .= "<Merchant>\n"
			. "<merchantID>" . $this->encode_html($merchantId) . "</merchantID>\n"
			. "<subID>" . $subId . "</subID>\n";

		if ($secondCustomFragment)
		{
			if ($secondCustomFragment != "")
			{
				// If there is a fragment to append, append it here.
				$xmlHeader .= $secondCustomFragment;
			}
		}
		// Close the header and return it.
		$xmlHeader .= "</Merchant>\n";

		return $xmlHeader;
	}

	/**
	 * Encodes HTML entity codes to characters
	 *
	 * @param string $text The text to encode
	 *
	 * @return string         The encoded text
	 */
	private function encode_html($text)
	{
		$trans = array("&amp;" => "&", "&quot;" => "\"", "&#039;" => "'", "&lt;" => "<", "&gt;" => ">");

		return htmlspecialchars(strtr($text, $trans), ENT_QUOTES);
	}
}
