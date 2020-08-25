<?php


namespace Tests\Sisow\Helpers;


class SisowHelper
{
    public static function generateRandomString($length = 16) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    public static function transactionParams(){
        return [
            'payment' => 'afterpay',
            'purchaseid' => self::generateRandomString(),
            'amount' => '10000',
            'description' => 'unit test',
            'testmode' => 'true',
            'returnurl' => 'https://www.sisow.nl',
            'currency' => 'EUR',
            'ipaddress' => '1.1.1.1',
            'gender' => 'm',
            'birthdate' => '01011970',
            'billing_firstname' => 'Test',
            'billing_lastname' => 'Approved',
            'billing_mail' => 'info@sisow.nl',
            'billing_address1' => 'Binnen Parallelweg 14',
            'billing_address2' => '',
            'billing_zip' => '5701PH',
            'billing_city' => 'Helmond',
            'billing_countrycode' => 'NL',
            'billing_phone' => '0612345678',
            'shipping_firstname' => 'Test',
            'shipping_lastname' => 'Approved',
            'shipping_mail' => 'info@sisow.nl',
            'shipping_address1' => 'Binnen Parallelweg 14',
            'shipping_address2' => '',
            'shipping_zip' => '5701PH',
            'shipping_city' => 'Helmond',
            'shipping_countrycode' => 'NL',
            'shipping_phone' => '0612345678',
            'product_id_1' => 'test',
            'product_description_1' => 'Test product',
            'product_quantity_1' => '1',
            'product_netprice_1' => '8264',
            'product_total_1' => '10000',
            'product_nettotal_1' => '8264',
            'product_tax_1' => '1736',
            'product_taxrate_1' => '2100'
        ];
    }
}