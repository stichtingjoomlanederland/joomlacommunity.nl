<?php

namespace nl\rabobank\gict\payments_savings\omnikassa_sdk\connector;

use nl\rabobank\gict\payments_savings\omnikassa_sdk\model\AccessToken;

/**
 * This class is responsible for retrieving and storing token related information.
 */
abstract class TokenProvider
{
    const REFRESH_TOKEN = 'REFRESH_TOKEN';
    const ACCESS_TOKEN = 'ACCESS_TOKEN';
    const ACCESS_TOKEN_VALID_UNTIL = 'ACCESS_TOKEN_VALID_UNTIL';
    const ACCESS_TOKEN_DURATION = 'ACCESS_TOKEN_DURATION';

    /**
     * @return string
     */
    final public function getRefreshToken()
    {
        return $this->getValue(static::REFRESH_TOKEN);
    }

    /**
     * @return AccessToken
     *
     * @throws \InvalidArgumentException
     */
    final public function getAccessToken()
    {
        $token = $this->getValue(static::ACCESS_TOKEN);
        $validUntil = $this->getValue(static::ACCESS_TOKEN_VALID_UNTIL);
        $durationInMillis = $this->getValue(static::ACCESS_TOKEN_DURATION);

        return new AccessToken($token, new \DateTime($validUntil), $durationInMillis);
    }

    /**
     * @param AccessToken $accessToken
     */
    final public function setAccessToken(AccessToken $accessToken)
    {
        $this->setValue(static::ACCESS_TOKEN, $accessToken->getToken());
        $this->setValue(static::ACCESS_TOKEN_VALID_UNTIL, $accessToken->getValidUntil()->format(\DateTime::ATOM));
        $this->setValue(static::ACCESS_TOKEN_DURATION, $accessToken->getDurationInMillis());
        $this->flush();
    }

    /**
     * Retrieve the value for the given key.
     *
     * @param string $key
     *
     * @return string Value of the given key
     */
    abstract protected function getValue($key);

    /**
     * Store the value by the given key.
     *
     * @param string $key
     * @param string $value
     */
    abstract protected function setValue($key, $value);

    /**
     * Optional functionality to flush your systems.
     * It is called after storing all the values of the access token and can be used for example to clean caches or reload changes from the database.
     */
    abstract protected function flush();
}
