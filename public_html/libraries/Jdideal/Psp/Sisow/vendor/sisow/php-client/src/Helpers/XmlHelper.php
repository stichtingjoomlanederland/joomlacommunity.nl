<?php


namespace Sisow\Helpers;

class XmlHelper
{
    /**
     * The XML string
     *
     * @string
     */
    private $xml;

    /**
     * XmlHelper constructor.
     * @param $strXml
     */
    function __construct($strXml){
        $this->xml = $strXml;
    }

    function getValue($paramName, $xml = ''){
        if (empty($this->xml) && empty($xml)) {
            return '';
        } else if(empty($xml)){
            $xml = $this->xml;
        }

        if (($start = strpos($xml, "<" . $paramName . ">")) === false) {
            return '';
        }
        $start += strlen($paramName) + 2;
        if (($end = strpos($xml, "</" . $paramName . ">", $start)) === false) {
            return '';
        }
        return substr($xml, $start, $end - $start);
    }

    function getArray($paramName, $delimiter){
        $strValues = $this->getValue($paramName);
        return array_filter(explode('<' . $delimiter . '>', str_replace('</' . $delimiter . '>', '', $strValues)));
    }

    function getKeyValueArray($paramName, $delimiter, $keyName, $valueName){
        $result = [];
        $issuerValues = $this->getArray($paramName, $delimiter);

        foreach($issuerValues as $issuer){
            $result[$this->getValue($keyName, $issuer)] = $this->getValue($valueName, $issuer);
        }

        return $result;
    }
}