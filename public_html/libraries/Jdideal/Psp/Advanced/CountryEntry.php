<?php

 
/**
* This bean class represents a country as received by a directory response.
*/
class CountryEntry
{
    var $countryNames = "";
    var $issuers = array();
    
    function addIssuer($entry){
        if ( is_a( $entry, "IssuerEntry" ) ) 
        {
            	$this->issuers[ $entry->getIssuerName() ] = $entry;
                ksort( $this->issuers );
        }
    }
    
    function getIssuers(){
        return $this->issuers;
    }
    
    function getCountryNames() {
        return $this->countryNames;
    }
    
    function setCountryNames($names) {
        $this->countryNames = $names;
    }
}
