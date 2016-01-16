<?php

/**
 * @package		SP Libraries
 * @subpackage	Utilites
 * @copyright	KAINOTOMO PH LTD - All rights reserved.
 * @author		KAINOTOMO PH LTD
 * @link		http://www.kainotomo.com
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('JPATH_PLATFORM') or die;

/**
 * SPGeneral is a class with various frequent used functions
 *
 * @package     spcyend.utilities.factory
 * @subpackage  Utilities
 * @since       1.1.0
 */
class CYENDPaypal {

    /**
     * Constructor.
     *
     * @since   1.1.0
     *
     */
    public function __construct() {
        JFactory::getLanguage()->load('lib_spcyend', JPATH_SITE); //Load library language
    }

    /**
     * Holds functions for EC for index.php and return.php for Digital Goods EC Calls
     * Makes an API call using an NVP String and an Endpoint
     *
     * @param   string  $my_endpoint    Paypal url
     * @param   string  $my_api_str  Api string
     *
     * @return  array  The response
     *
     * @since   1.1.0
     */
    public function PPHttpPost($my_endpoint, $my_api_str) {
        // setting the curl parameters.
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $my_endpoint);
        curl_setopt($ch, CURLOPT_VERBOSE, 1);
        // turning off the server and peer verification(TrustManager Concept).
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        // setting the NVP $my_api_str as POST FIELD to curl
        curl_setopt($ch, CURLOPT_POSTFIELDS, $my_api_str);
        // getting response from server
        $httpResponse = curl_exec($ch);
        if (!$httpResponse) {
            $response = "API_method failed: " . curl_error($ch) . '(' . curl_errno($ch) . ')';
            return $response;
        }
        $httpResponseAr = explode("&", $httpResponse);
        $httpParsedResponseAr = array();
        foreach ($httpResponseAr as $value) {
            $tmpAr = explode("=", $value);
            if (sizeof($tmpAr) > 1) {
                $httpParsedResponseAr[$tmpAr[0]] = $tmpAr[1];
            }
        }

        if ((0 == sizeof($httpParsedResponseAr)) || !array_key_exists('ACK', $httpParsedResponseAr)) {
            $response = "Invalid HTTP Response for POST request(".$my_api_str.") to API_Endpoint.";
            return $response;
        }

        return $httpParsedResponseAr;
    }
    
    /**
     * Simulate PPHttpPost response
     *
     * @return  array  The response
     *
     * @since   1.1.0
     */
    public function PPHttpPost_Sim() {
        //$responseJSON = '{"code": "demo1","token": "EC-08K295908N632783G","invnum": "","phonenum": "","note": "","redirectrequired": "","checkoutstatus": "PaymentActionCompleted","email": "info@cyend.com","payerid": "DLPKHL2T6K2NJ","payerstatus": "verified","countrycode": "CY","business": "","salutation": "","firstname": "John","middlename": "","lastname": "Smith","suffix": "","paymentrequest_0_amt": 0.01,"paymentrequest_0_currencycode": "EUR","paymentrequest_0_itemamt": 0.01,"paymentrequest_0_taxamt": 0.00,"paymentrequest_0_desc": "Demo Album","paymentrequest_0_invnum": "","paymentrequest_0_notifyurl": "","paymentrequest_0_transactionid": "'.rand().'","paymentrequest_0_paymentrequestid": "","l_paymentrequest_0_name0": "Demo Album","l_paymentrequest_0_desc0": "Download","l_paymentrequest_0_amt0": 0.01,"l_paymentrequest_0_number0": "demo1","l_paymentrequest_0_qty0": 1,"l_paymentrequest_0_taxamt0": 0.00,"l_paymentrequest_0_itemcategory0": "Digital"}';
        $responseJSON = '{"code": "demo1","token": "EC-08K295908N632783G","invnum": "","phonenum": "","note": "","redirectrequired": "","checkoutstatus": "PaymentActionCompleted","email": "phalouvas@gmail.com","payerid": "DLPKHL2T6K2NJ","payerstatus": "verified","countrycode": "CY","business": "","salutation": "","firstname": "John","middlename": "","lastname": "Smith","suffix": "","paymentrequest_0_amt": 0.01,"paymentrequest_0_currencycode": "EUR","paymentrequest_0_itemamt": 0.01,"paymentrequest_0_taxamt": 0.00,"paymentrequest_0_desc": "Demo Album","paymentrequest_0_invnum": "","paymentrequest_0_notifyurl": "","paymentrequest_0_transactionid": "'.rand().'","paymentrequest_0_paymentrequestid": "","l_paymentrequest_0_name0": "Demo Album","l_paymentrequest_0_desc0": "Download","l_paymentrequest_0_amt0": 0.01,"l_paymentrequest_0_number0": "demo1","l_paymentrequest_0_qty0": 1,"l_paymentrequest_0_taxamt0": 0.00,"l_paymentrequest_0_itemcategory0": "Digital"}';
        $responseObj = json_decode($responseJSON);        
        $response = Array();
        foreach ($responseObj as $key => $value) {
            $response[strtoupper($key)] = $value;
        }
        
        return $response;
    }

}
