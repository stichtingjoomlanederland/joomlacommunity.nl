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

/**
* This bean class represents an issuer as received by a directory response.
*/
class IssuerEntry 
{
    var $issuerID = "";
    var $issuerName = "";

    /**
     * @return Returns the issuerID.
     */
    function getIssuerID() {
        return $this->issuerID;
    }
    /**
     * @param issuerID The issuerID to set.
     */
    function setIssuerID($issuerID) {
        $this->issuerID = $issuerID;
    }

    /**
     * @return Returns the issuerName.
     */
    function getIssuerName() {
        return $this->issuerName;
    }
    /**
     * @param issuerName The issuerName to set.
     */
    function setIssuerName($issuerName) {
        $this->issuerName = $issuerName;
    }
}
