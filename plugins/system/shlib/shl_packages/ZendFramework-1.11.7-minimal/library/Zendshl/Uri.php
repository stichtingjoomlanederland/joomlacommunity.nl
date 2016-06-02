<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category  Zend
 * @package   Zendshl_Uri
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd     New BSD License
 * @version   $Id: Uri.php 23775 2011-03-01 17:25:24Z ralph $
 */
defined('_JEXEC') or die;

/**
 * Abstract class for all Zendshl_Uri handlers
 *
 * @category  Zend
 * @package   Zendshl_Uri
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd     New BSD License
 */
abstract class Zendshl_Uri
{
    /**
     * Scheme of this URI (http, ftp, etc.)
     *
     * @var string
     */
    protected $_scheme = '';

    /**
     * Global configuration array
     *
     * @var array
     */
    static protected $_config = array(
        'allow_unwise' => false
    );

    /**
     * Return a string representation of this URI.
     *
     * @see    getUri()
     * @return string
     */
    public function __toString()
    {
        try {
            return $this->getUri();
        } catch (Exception $e) {
            trigger_error($e->getMessage(), E_USER_WARNING);
            return '';
        }
    }

    /**
     * Convenience function, checks that a $uri string is well-formed
     * by validating it but not returning an object.  Returns TRUE if
     * $uri is a well-formed URI, or FALSE otherwise.
     *
     * @param  string $uri The URI to check
     * @return boolean
     */
    public static function check($uri)
    {
        try {
            $uri = self::factory($uri);
        } catch (Exception $e) {
            return false;
        }

        return $uri->valid();
    }

    /**
     * Create a new Zendshl_Uri object for a URI.  If building a new URI, then $uri should contain
     * only the scheme (http, ftp, etc).  Otherwise, supply $uri with the complete URI.
     *
     * @param  string $uri       The URI form which a Zendshl_Uri instance is created
     * @param  string $className The name of the class to use in order to manipulate URI
     * @throws Zendshl_Uri_Exception When an empty string was supplied for the scheme
     * @throws Zendshl_Uri_Exception When an illegal scheme is supplied
     * @throws Zendshl_Uri_Exception When the scheme is not supported
     * @throws Zendshl_Uri_Exception When $className doesn't exist or doesn't implements Zendshl_Uri
     * @return Zendshl_Uri
     * @link   http://www.faqs.org/rfcs/rfc2396.html
     */
    public static function factory($uri = 'http', $className = null)
    {
        // Separate the scheme from the scheme-specific parts
        $uri            = explode(':', $uri, 2);
        $scheme         = strtolower($uri[0]);
        $schemeSpecific = isset($uri[1]) === true ? $uri[1] : '';

        if (strlen($scheme) === 0) {
            require_once SHLIB_PATH_TO_ZEND . 'Zendshl/Uri/Exception.php';
            throw new Zendshl_Uri_Exception('An empty string was supplied for the scheme');
        }

        // Security check: $scheme is used to load a class file, so only alphanumerics are allowed.
        if (ctype_alnum($scheme) === false) {
            require_once SHLIB_PATH_TO_ZEND . 'Zendshl/Uri/Exception.php';
            throw new Zendshl_Uri_Exception('Illegal scheme supplied, only alphanumeric characters are permitted');
        }

        if ($className === null) {
            /**
             * Create a new Zendshl_Uri object for the $uri. If a subclass of Zendshl_Uri exists for the
             * scheme, return an instance of that class. Otherwise, a Zendshl_Uri_Exception is thrown.
             */
            switch ($scheme) {
                case 'http':
                    // Break intentionally omitted
                case 'https':
                    $className = 'Zendshl_Uri_Http';
                    break;

                case 'mailto':
                    // TODO
                default:
                    require_once SHLIB_PATH_TO_ZEND . 'Zendshl/Uri/Exception.php';
                    throw new Zendshl_Uri_Exception("Scheme \"$scheme\" is not supported");
                    break;
            }
        }

        require_once SHLIB_PATH_TO_ZEND . 'Zendshl/Loader.php';
        try {
            Zendshl_Loader::loadClass($className);
        } catch (Exception $e) {
            require_once SHLIB_PATH_TO_ZEND . 'Zendshl/Uri/Exception.php';
            throw new Zendshl_Uri_Exception("\"$className\" not found");
        }

        $schemeHandler = new $className($scheme, $schemeSpecific);

        if (! $schemeHandler instanceof Zendshl_Uri) {
            require_once SHLIB_PATH_TO_ZEND . 'Zendshl/Uri/Exception.php';
            throw new Zendshl_Uri_Exception("\"$className\" is not an instance of Zendshl_Uri");
        }

        return $schemeHandler;
    }

    /**
     * Get the URI's scheme
     *
     * @return string|false Scheme or false if no scheme is set.
     */
    public function getScheme()
    {
        if (empty($this->_scheme) === false) {
            return $this->_scheme;
        } else {
            return false;
        }
    }

    /**
     * Set global configuration options
     *
     * @param Zendshl_Config|array $config
     */
    static public function setConfig($config)
    {
        if ($config instanceof Zendshl_Config) {
            $config = $config->toArray();
        } elseif (!is_array($config)) {
            throw new Zendshl_Uri_Exception("Config must be an array or an instance of Zendshl_Config.");
        }

        foreach ($config as $k => $v) {
            self::$_config[$k] = $v;
        }
    }

    /**
     * Zendshl_Uri and its subclasses cannot be instantiated directly.
     * Use Zendshl_Uri::factory() to return a new Zendshl_Uri object.
     *
     * @param string $scheme         The scheme of the URI
     * @param string $schemeSpecific The scheme-specific part of the URI
     */
    abstract protected function __construct($scheme, $schemeSpecific = '');

    /**
     * Return a string representation of this URI.
     *
     * @return string
     */
    abstract public function getUri();

    /**
     * Returns TRUE if this URI is valid, or FALSE otherwise.
     *
     * @return boolean
     */
    abstract public function valid();
}
