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
 * @category   Zend
 * @package    Zendshl_Http
 * @subpackage UserAgent
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
defined('_JEXEC') or die;

/**
 * Zendshl_Http_UserAgent_Features_Adapter_Interface
 */
require_once SHLIB_PATH_TO_ZEND . 'Zendshl/Http/UserAgent/Features/Adapter.php';

/**
 * Features adapter build with the official WURFL PHP API
 * See installation instruction here : http://wurfl.sourceforge.net/nphp/
 * Download : http://sourceforge.net/projects/wurfl/files/WURFL PHP/1.1/wurfl-php-1.1.tar.gz/download
 *
 * @package    Zendshl_Http
 * @subpackage UserAgent
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zendshl_Http_UserAgent_Features_Adapter_WurflApi
    implements Zendshl_Http_UserAgent_Features_Adapter
{
    const DEFAULT_API_VERSION = '1.1';

    /**
     * Get features from request
     *
     * @param  array $request $_SERVER variable
     * @return array
     */
    public static function getFromRequest($request, array $config)
    {
        if (!isset($config['wurflapi'])) {
            require_once SHLIB_PATH_TO_ZEND . 'Zendshl/Http/UserAgent/Features/Exception.php';
            throw new Zendshl_Http_UserAgent_Features_Exception('"wurflapi" configuration is not defined');
        }

        $config = $config['wurflapi'];

        if (empty($config['wurfl_lib_dir'])) {
            require_once SHLIB_PATH_TO_ZEND . 'Zendshl/Http/UserAgent/Features/Exception.php';
            throw new Zendshl_Http_UserAgent_Features_Exception('The "wurfl_lib_dir" parameter is not defined');
        }
        if (empty($config['wurfl_config_file']) && empty($config['wurfl_config_array'])) {
            require_once SHLIB_PATH_TO_ZEND . 'Zendshl/Http/UserAgent/Features/Exception.php';
            throw new Zendshl_Http_UserAgent_Features_Exception('The "wurfl_config_file" parameter is not defined');
        }

        if (empty($config['wurfl_api_version'])) {
            $config['wurfl_api_version'] = self::DEFAULT_API_VERSION;
        }

        switch ($config['wurfl_api_version']) {
            case '1.0':
                // Zendshl_Http_UserAgent::$config['wurfl_config_file'] must be an XML file
                require_once ($config['wurfl_lib_dir'] . 'WURFLManagerProvider.php');
                $wurflManager = WURFL_WURFLManagerProvider::getWURFLManager(Zendshl_Http_UserAgent::$config['wurfl_config_file']);
                break;
            case '1.1':
                require_once ($config['wurfl_lib_dir'] . 'Application.php');
                if (!empty($config['wurfl_config_file'])) {
                    $wurflConfig = WURFL_Configuration_ConfigFactory::create($config['wurfl_config_file']);
                } elseif (!empty($config['wurfl_config_array'])) {
                    $c            = $config['wurfl_config_array'];
                    $wurflConfig  = new WURFL_Configuration_InMemoryConfig();
                    $wurflConfig->wurflFile($c['wurfl']['main-file'])
                                ->wurflPatch($c['wurfl']['patches'])
                                ->persistence($c['persistence']['provider'], $c['persistence']['dir']);
                }

                $wurflManagerFactory = new WURFL_WURFLManagerFactory($wurflConfig);
                $wurflManager = $wurflManagerFactory->create();
                break;
            default:
                require_once SHLIB_PATH_TO_ZEND . 'Zendshl/Http/UserAgent/Features/Exception.php';
                throw new Zendshl_Http_UserAgent_Features_Exception(sprintf(
                    'Unknown API version "%s"',
                    $config['wurfl_api_version']
                ));
        }

        $device   = $wurflManager->getDeviceForHttpRequest(array_change_key_case($request, CASE_UPPER));
        $features = $device->getAllCapabilities();
        return $features;
    }
}
