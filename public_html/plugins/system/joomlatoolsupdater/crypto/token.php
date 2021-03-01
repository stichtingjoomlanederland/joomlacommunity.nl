<?php
/**
 * @package     JoomlatoolsUpdater
 * @copyright   Copyright (C) 2021 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

if (!class_exists('\Joomlatools\RSA\Crypt_RSA')) {
    require_once __DIR__.'/rsa.php';
}

class PlgSystemJoomlatoolsupdaterCryptoToken extends KHttpToken
{
    const RS256 = 'RS256';

    protected function _initialize(KObjectConfig $config)
    {
        $config->append(array(
            'algorithm' => static::RS256
        ));

        parent::_initialize($config);
    }

    public function setAlgorithm($algorithm)
    {
        if ($algorithm === static::RS256) {
            $this->_header['alg'] = static::RS256;
            $this->_algorithm     = static::RS256;

            return $this;
        } else {
            return parent::setAlgorithm($algorithm);
        }
    }

    public function verify($secret)
    {
        if ($this->_algorithm === static::RS256) {
            $header  = $this->_toBase64url($this->_toJson($this->_header));
            $payload = $this->_toBase64url($this->_toJson($this->_claims));

            $message   = sprintf("%s.%s", $header, $payload);
            $rsa = new \Joomlatools\RSA\Crypt_RSA();
            $rsa->setSignatureMode(CRYPT_RSA_SIGNATURE_PKCS1);
            $rsa->loadKey($secret);

            return $rsa->verify($message, $this->_signature);
        } else {
            return parent::verify($secret);
        }
    }

    public function sign($secret)
    {
        if ($this->_algorithm === static::RS256) {
            $token = $this->toString();

            $rsa = new \Joomlatools\RSA\Crypt_RSA();
            $rsa->loadKey($secret);
            $rsa->setSignatureMode(CRYPT_RSA_SIGNATURE_PKCS1);

            $signature = $rsa->sign($token);

            return sprintf("%s.%s", $token, $this->_toBase64url($signature));
        } else {
            return parent::verify($secret);
        }
    }

    public function getClaims()
    {
        return $this->_claims;
    }
}