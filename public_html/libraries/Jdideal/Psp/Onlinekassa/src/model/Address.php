<?php

namespace nl\rabobank\gict\payments_savings\omnikassa_sdk\model;

use nl\rabobank\gict\payments_savings\omnikassa_sdk\model\signing\SignatureDataProvider;

/**
 * Class Address.
 */
class Address implements \JsonSerializable, SignatureDataProvider
{
    /** @var string */
    private $firstName;
    /** @var string */
    private $middleName;
    /** @var string */
    private $lastName;
    /** @var string */
    private $street;
    /** @var string */
    private $houseNumber;
    /** @var string */
    private $houseNumberAddition;
    /** @var string */
    private $postalCode;
    /** @var string */
    private $city;
    /** @var string */
    private $countryCode;

    /**
     * @param string $firstName
     * @param string $middleName
     * @param string $lastName
     * @param string $street
     * @param string $postalCode
     * @param string $city
     * @param string $countryCode
     * @param string $houseNumber
     * @param string $houseNumberAddition
     *
     * @deprecated This constructor is deprecated but remains available for backwards compatibility. Use the static
     * createFrom method instead.
     * @see Address::createFrom()
     */
    public function __construct($firstName, $middleName, $lastName, $street, $postalCode, $city, $countryCode, $houseNumber = null, $houseNumberAddition = null)
    {
        $this->firstName = $firstName;
        $this->middleName = $middleName;
        $this->lastName = $lastName;
        $this->street = $street;
        $this->postalCode = $postalCode;
        $this->city = $city;
        $this->countryCode = $countryCode;
        $this->houseNumber = $houseNumber;
        $this->houseNumberAddition = $houseNumberAddition;
    }

    public static function createFrom(array $data)
    {
        $address = new Address(null, null, null, null, null, null, null);
        foreach ($data as $key => $value) {
            if (property_exists($address, $key)) {
                $address->$key = $data[(string) $key];
            } else {
                $properties = implode(', ', array_keys(get_object_vars($address)));
                throw new \InvalidArgumentException("Invalid property {$key} supplied. Valid properties for Address are: {$properties}");
            }
        }

        return $address;
    }

    /**
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * @return string
     */
    public function getMiddleName()
    {
        return $this->middleName;
    }

    /**
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * @return string
     */
    public function getStreet()
    {
        return $this->street;
    }

    /**
     * @return string
     */
    public function getHouseNumber()
    {
        return $this->houseNumber;
    }

    /**
     * @return string
     */
    public function getHouseNumberAddition()
    {
        return $this->houseNumberAddition;
    }

    /**
     * @return string
     */
    public function getPostalCode()
    {
        return $this->postalCode;
    }

    /**
     * @return string
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * @return string
     */
    public function getCountryCode()
    {
        return $this->countryCode;
    }

    /**
     * @return array
     */
    public function getSignatureData()
    {
        $data = [];
        $data[] = $this->firstName;
        $data[] = $this->middleName;
        $data[] = $this->lastName;
        $data[] = $this->street;
        if (null !== $this->houseNumber) {
            $data[] = $this->houseNumber;
        }
        if (null !== $this->houseNumberAddition) {
            $data[] = $this->houseNumberAddition;
        }
        $data[] = $this->postalCode;
        $data[] = $this->city;
        $data[] = $this->countryCode;

        return $data;
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        $json = [];
        foreach ($this as $key => $value) {
            if (null !== $value) {
                $json[$key] = $value;
            }
        }

        return $json;
    }
}
