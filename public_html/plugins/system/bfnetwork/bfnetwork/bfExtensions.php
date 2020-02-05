<?php

/*
 * @package   bfNetwork
 * @copyright Copyright (C) 2011, 2012, 2013, 2014, 2015, 2016, 2017, 2018, 2019, 2020 Blue Flame Digital Solutions Ltd. All rights reserved.
 * @license   GNU General Public License version 3 or later
 *
 * @see       https://mySites.guru/
 * @see       https://www.phil-taylor.com/
 *
 * @author    Phil Taylor / Blue Flame Digital Solutions Limited.
 *
 * bfNetwork is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * bfNetwork is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this package.  If not, see http://www.gnu.org/licenses/
 *
 * If you have any questions regarding this code, please contact phil@phil-taylor.com
 */

/**
 * If we have not already included bfEncrypt then this must be a direct call, and
 * so we need to decrypt the incoming request.
 */
if (!class_exists('bfEncrypt')) {
    require 'bfEncrypt.php';
}

/*
 * Ok so this is stupid, but we are dealing with XML Parsing on crappy servers on sites with 100+ extensions installed - gulp!
 * 5 Mins! GULP - Most well configured servers will probably not honour this, but in our live tests on crappy servers this seems to work
 */
@set_time_limit(60 * 5);

/**
 * If we have got here then we have already passed through decrypting
 * the encrypted header and so we are sure we are now secure and no one
 * else cannot run the code below.
 */
final class bfExtensions
{
    /**
     * @var JDatabase|JDatabaseDriver|object
     */
    private $db;

    /**
     * Incoming decrypted vars from the request.
     *
     * @var stdClass
     */
    private $_dataObj;

    /**
     * We pass the command to run as a simple integer in our encrypted
     * request this is mainly to speed up the decryption process, plus its a
     * single digit(or 2) rather than a huge string to remember :-).
     */
    private $_methods = array(
        1 => 'getExtensions',
        2 => 'installExtensionFromUrl',
    );

    /**
     * PHP 5 Constructor,
     * I inject the request to the object.
     *
     * @param stdClass $dataObj
     */
    public function __construct($dataObj = null)
    {
        // init Joomla
        require 'bfInitJoomla.php';

        // Get some Joomla version
        $VERSION = new JVersion();

        if ('1.5' != $VERSION->RELEASE) {
            // ffs falang!
            JFactory::$database = null;
        }

        // Set the request vars
        $this->_dataObj = $dataObj;
    }

    /**
     * I'm the controller - I run methods based on the request integer.
     */
    public function run()
    {
        if (property_exists($this->_dataObj, 'c')) {
            $c = (int) $this->_dataObj->c;
            if (array_key_exists($c, $this->_methods)) {
                // call the right method
                $this->{$this->_methods[$c]} ();
            } else {
                // Die if an unknown function
                bfEncrypt::reply('error', 'No Such method #err1 - '.$c);
            }
        } else {
            // Die if an unknown function
            bfEncrypt::reply('error', 'No Such method #err2');
        }
    }

    /**
     * Install an extension from a provided URL.
     *
     * Parts of this code are from Joomla CMS, Modified under license.
     *
     * @license GNU General Public License version 2 or later; see https://github.com/joomla/joomla-cms/blob/staging/LICENSE.txt
     * @copyright Copyright (C) 2005 - 2018 Open Source Matters, Inc. All rights reserved.
     */
    public function installExtensionFromUrl()
    {
        // Get an installer instance.
        $installer = JInstaller::getInstance();

        // Get the URL of the package to install.
        $url = $this->_dataObj->url;

        // Download the package at the URL given.
        $p_file = JInstallerHelper::downloadPackage($url);

        // Was the package downloaded?
        if ($p_file) {
            // Unpack the downloaded package file.
            $package = JInstallerHelper::unpack(JFactory::getConfig()->get('tmp_path').'/'.$p_file, true);

            // Install the package.
            if (!$installer->install($package['dir'])) {
                // There was an error installing the package.
                $msg     = 'There was an error installing the package';
                $result  = false;
                $msgType = 'error';
            } else {
                // Package installed successfully.
                $msg     = 'Package installed successfully';
                $result  = true;
                $msgType = 'message';
            }

            // Cleanup the install files.
            if (!is_file($package['packagefile'])) {
                $config                 = JFactory::getConfig();
                $package['packagefile'] = $config->get('tmp_path').'/'.$package['packagefile'];
            }

            // Cleanup the package file
            JInstallerHelper::cleanupInstall($package['packagefile'], $package['extractdir']);
        } else {
            $result  = false;
            $msgType = 'error';
            $msg     = 'Could not download package from URL: '.$url;
        }

        $res = array(
            'result'            => $result,
            'msgType'           => $msgType,
            'message'           => $msg,
            'extension_message' => $installer->get('extension_message'),
            'redirect_url'      => $installer->get('redirect_url'),
        );

        bfEncrypt::reply($res['result'],
            $res
        );
    }

    /**
     * Get a JSON formatted list of installed extensions
     * Needs to be public so we can call it from the audit.
     *
     * @return string
     */
    public function getExtensions()
    {
        // connect/get the Joomla db object
        $this->db = JFactory::getDbo();

        // crazy way of handling Joomla 1.5.x legacy :-(
        $one5 = false;

        // Get Joomla 2.0+ Extensions
        $this->db->setQuery('SELECT e.extension_id, e.name, e.type, e.element, e.enabled, e.folder,
                                (
                                 SELECT title
                                 FROM #__menu AS m
                                 WHERE m.component_id = e.extension_id
                                 AND parent_id = 1
                                 ORDER BY ID ASC LIMIT 1
                                 )
                                 AS title
                                FROM #__extensions AS e
                                WHERE protected = 0');
        $installedExtensions = $this->db->loadObjectList();

        // ok if we have none maybe we are Joomla < 1.5.26
        if (!$installedExtensions) {
            // Yooo hoo I'm on a crap old, out of date, probably hackable Joomla version!
            $one5 = true;

            // Get the extensions - used to be called components
            $this->db->setQuery('SELECT "component" as "type", name, `option` as "element", enabled FROM #__components WHERE iscore != 1 and parent = 0');
            $components = $this->db->loadObjectList();

            // Get the plugins
            $this->db->setQuery('SELECT "plugin" as "type", name, element, folder, published as enabled FROM #__plugins WHERE iscore != 1');
            $plugins = $this->db->loadObjectList();

            // get the modules
            $this->db->setQuery('SELECT  "module" as "type", module, module as name, client_id, published as enabled FROM #__modules WHERE iscore != 1');
            $modules = $this->db->loadObjectList();

            /**
             * Get the templates - I n Joomla 1.5.x the templates are not in the
             * db unless published so we need to read the folders from the /templates folders
             * Note in Joomla 1.5.x there was no such think as admin templates.
             */
            $folders   = array_merge(scandir(JPATH_BASE.'/templates'), scandir(JPATH_ADMINISTRATOR.'/templates'));
            $templates = array();
            foreach ($folders as $templateFolder) {
                $f = JPATH_BASE.'/templates/'.trim($templateFolder);
                $a = JPATH_ADMINISTRATOR.'/templates/'.trim($templateFolder);

                // We dont want index.html etc...
                if (!is_dir($f) && !is_dir($a) || ('.' == $templateFolder || '..' == $templateFolder)) {
                    continue;
                }

                if (is_dir($a)) {
                    $client_id = 1;
                } else {
                    $client_id = 0;
                }

                // make it look like we want like Joomla 2.5+ would
                $template = array(
                    'type'      => 'template',
                    'template'  => $templateFolder,
                    'client_id' => $client_id,
                    'enabled'   => 1,
                );

                // Convert to an obj
                $templates[] = json_decode(json_encode($template));
            }

            // Merge all the "extensions" we have found all over the place
            $installedExtensions = array_merge($components, $plugins, $modules, $templates);
        }

        $lang = JFactory::getLanguage();

        // Load all the language strings up front incase any strings are shared
        foreach ($installedExtensions as $k => $ext) {
            $lang->load(strtolower($ext->element).'.sys', JPATH_ADMINISTRATOR, 'en-GB', true);
            $lang->load(strtolower($ext->element).'.sys', JPATH_SITE, 'en-GB', true);
            $lang->load(strtolower($ext->name).'.sys', JPATH_ADMINISTRATOR, 'en-GB', true);
            $lang->load(strtolower($ext->name).'.sys', JPATH_SITE, 'en-GB', true);
            $lang->load(strtolower($ext->title).'.sys', JPATH_ADMINISTRATOR, 'en-GB', true);
            $lang->load(strtolower($ext->title).'.sys', JPATH_SITE, 'en-GB', true);

            $lang->load(strtolower($ext->element), JPATH_ADMINISTRATOR, 'en-GB', true);
            $lang->load(strtolower($ext->element), JPATH_SITE, 'en-GB', true);
            $lang->load(strtolower($ext->name), JPATH_ADMINISTRATOR, 'en-GB', true);
            $lang->load(strtolower($ext->name), JPATH_SITE, 'en-GB', true);
            $lang->load(strtolower($ext->title), JPATH_ADMINISTRATOR, 'en-GB', true);
            $lang->load(strtolower($ext->title), JPATH_SITE, 'en-GB', true);

            $element = str_replace('_TITLE', '', strtoupper($ext->element));
            $name    = str_replace('_TITLE', '', strtoupper($ext->name));
            $title   = str_replace('_TITLE', '', strtoupper($ext->title));

            $lang->load(strtolower($element).'.sys', JPATH_ADMINISTRATOR, 'en-GB', true);
            $lang->load(strtolower($element).'.sys', JPATH_SITE, 'en-GB', true);
            $lang->load(strtolower($name).'.sys', JPATH_ADMINISTRATOR, 'en-GB', true);
            $lang->load(strtolower($name).'.sys', JPATH_SITE, 'en-GB', true);
            $lang->load(strtolower($title).'.sys', JPATH_ADMINISTRATOR, 'en-GB', true);
            $lang->load(strtolower($title).'.sys', JPATH_SITE, 'en-GB', true);

            $lang->load(strtolower($element), JPATH_ADMINISTRATOR, 'en-GB', true);
            $lang->load(strtolower($element), JPATH_SITE, 'en-GB', true);
            $lang->load(strtolower($name), JPATH_ADMINISTRATOR, 'en-GB', true);
            $lang->load(strtolower($name), JPATH_SITE, 'en-GB', true);
            $lang->load(strtolower($title), JPATH_ADMINISTRATOR, 'en-GB', true);
            $lang->load(strtolower($title), JPATH_SITE, 'en-GB', true);

            // templates
            $lang->load('tpl_'.strtolower($name), JPATH_ADMINISTRATOR, 'en-GB', true);
            $lang->load('tpl_'.strtolower($name), JPATH_SITE, 'en-GB', true);

            // Joomla 1.5.x modules
            $lang->load('mod_'.strtolower($name), JPATH_ADMINISTRATOR, 'en-GB', true);
            $lang->load('mod_'.strtolower($name), JPATH_SITE, 'en-GB', true);

            // tut tut Akeeba - bad naming!
            $lang->load(strtolower('PLG_SYSTEM_SRP'), JPATH_ADMINISTRATOR, 'en-GB', true); // should be plg_srp
            $lang->load(strtolower('PLG_SYSTEM_ONECLICKACTION'), JPATH_SITE, 'en-GB', true); // should be plg_oneclickaction
            $lang->load(strtolower('PLG_SYSTEM_ONECLICKACTION'), JPATH_ADMINISTRATOR, 'en-GB', true); // should be plg_oneclickaction

            // Joomla 1.5 plugins
            if ('plugin' == $ext->type) {
                $plg = 'plg_'.$ext->folder.'_'.$ext->element;
                $lang->load(strtolower($plg), JPATH_SITE, 'en-GB', true);
                $lang->load(strtolower($plg), JPATH_ADMINISTRATOR, 'en-GB', true);
            }

            if ('template' == $ext->type) {
                $plg = 'tpl_'.$ext->name;
                $lang->load(strtolower($plg), JPATH_SITE, 'en-GB', true);
                $lang->load(strtolower($plg), JPATH_ADMINISTRATOR, 'en-GB', true);
            }
        }

        // ok now we have the extensions - get the xml for further offline crunching
        foreach ($installedExtensions as $k => $ext) {
            // remove not supported types :-(
            if ('file' == $ext->type || 'package' == $ext->type) {
                unset($installedExtensions[$k]);
                continue;
            }
            $ext->xmlFile = $this->findManifest($ext);

            try {
                if (false !== $ext->xmlFile) {
                    $parts = explode('/', $ext->xmlFile);
                    array_pop($parts);
                    $ext->path = implode('/', $parts);
                    bfLog::log('Loading XML file = '.str_replace(JPATH_BASE, '', $ext->xmlFile));
                    $xml   = trim(file_get_contents($ext->xmlFile));
                    $myXML = new SimpleXMLElement($xml);
                    if (property_exists($myXML, 'description')) {
                        $ext->desc = $myXML->description;
                    }
                    $ext->xmlFileContents = base64_encode(gzcompress($xml));
                    $ext->xmlFileCreated  = filemtime($ext->xmlFile);
                } else {
                    $ext->MANIFESTERROR = true;
                }
            } catch (Exception $e) {
                bfLog::log('EXCEPTION = '.$ext->xmlFile.' '.$e->getMessage());
                die('Could not process XML file at: '.str_replace(JPATH_BASE, '', $ext->xmlFile));
            }

            $ext->name  = JText::_($ext->name);
            $ext->title = JText::_($ext->title);
            $ext->desc  = base64_encode(gzcompress(JText::_($ext->desc)));

            // remove base paths - we dont want to leak data :)
            $ext->xmlFile = $this->removeBase($ext->xmlFile);
            $ext->path    = $this->removeBase($ext->path);

            // Sort so its pretty - not that anyone sees, but debugging is easier
            $ext = (array) $ext;
            ksort($ext);

            // push to the result
            $installedExtensions[$k] = $ext;
        }

        return json_encode($installedExtensions);
    }

    /**
     * Find the XML file to parse.
     *
     * @param $ext stdClass
     *
     * @return bool
     */
    private function findManifest($ext)
    {
        $prefixes = array('com_',
            'ext_',
            'plg_content_',
            'plg_system_',
            'plg_user_',
            'plg_authentication_',
            'plg_authentication_',
            'plg_captcha_',
            'plg_content_',
            'plg_editors_',
            'plg_editors-xtd_',
            'plg_extension_',
            'plg_finder_',
            'plg_quickicon_',
            'plg_search_',
            'plg_system_',
            'plg_twofactorauth_',
            'plg_user_',
            'plg_',
        );

        if (property_exists($ext, 'element')) {
            $shortName = str_replace($prefixes, '', strtolower($ext->element));
        } else {
            $shortName = str_replace($prefixes, '', strtolower($ext->option));
        }

        $try = array();

        // Let the UGLY code begin
        switch ($ext->type) {
            case 'component':
                $try[]  = JPATH_ADMINISTRATOR.'/components/'.$ext->element.'/'.$shortName.'.xml';
                $last[] = JPATH_ADMINISTRATOR.'/components/'.$ext->element.'/';
                break;
            case 'module':
                $try[]  = JPATH_ADMINISTRATOR.'/modules/'.$ext->element.'/'.$shortName.'.xml';
                $try[]  = JPATH_BASE.'/modules/'.$ext->element.'/'.$shortName.'.xml';
                $try[]  = JPATH_ADMINISTRATOR.'/modules/'.$ext->module.'/'.$ext->module.'.xml';
                $try[]  = JPATH_BASE.'/modules/'.$ext->module.'/'.$ext->module.'.xml';
                $last[] = JPATH_ADMINISTRATOR.'/modules/'.$ext->module.'/';
                $last[] = JPATH_BASE.'/modules/'.$ext->module.'/';
                break;
            case 'template':

                $try[] = JPATH_ADMINISTRATOR.'/templates/'.$ext->element.'/templateDetails.xml';
                $try[] = JPATH_BASE.'/templates/'.$ext->element.'/templateDetails.xml';
                if (property_exists($ext, 'template')) {
                    $try[] = JPATH_ADMINISTRATOR.'/templates/'.$ext->template.'/templateDetails.xml';
                    $try[] = JPATH_BASE.'/templates/'.$ext->template.'/templateDetails.xml';
                }
                if (property_exists($ext, 'name')) {
                    $try[] = JPATH_ADMINISTRATOR.'/templates/'.$ext->name.'/templateDetails.xml';
                    $try[] = JPATH_BASE.'/templates/'.$ext->name.'/templateDetails.xml';
                }
                break;
            case 'language':
                $try[] = JPATH_ADMINISTRATOR.'/language/'.$ext->element.'/'.$ext->element.'.xml';
                $try[] = JPATH_BASE.'/language/'.$ext->element.'/'.$ext->element.'.xml';
                break;
            case 'plugin':
                $try[] = JPATH_ADMINISTRATOR.'/plugins/'.$ext->element.'/'.$shortName.'.xml';
                $try[] = JPATH_BASE.'/plugins/'.$ext->folder.'/'.$ext->element.'/'.$shortName.'.xml';
                $try[] = JPATH_BASE.'/plugins/'.$ext->element.'/'.$shortName.'.xml';
                $try[] = JPATH_BASE.'/plugins/'.$ext->folder.'/'.$shortName.'.xml';
                $try[] = JPATH_BASE.'/plugins/'.$ext->option.'/'.$shortName.'.xml';

                $last[] = JPATH_ADMINISTRATOR.'/plugins/'.$ext->element.'/';
                $last[] = JPATH_BASE.'/plugins/'.$ext->folder.'/'.$ext->element.'/';
                $last[] = JPATH_BASE.'/plugins/'.$ext->element.'/';
                $last[] = JPATH_BASE.'/plugins/'.$ext->folder.'/';
                $last[] = JPATH_BASE.'/plugins/'.$ext->option.'/';
                break;
        }

        if (count($try)) {
            foreach ($try as $tryThisFile) {
                if (file_exists($tryThisFile)) {
                    return $tryThisFile;
                }
            }
        }

        // argh! still no xml file! - ok lets get tough!
        foreach ($last as $tryThisFolder) {
            $foldersAndFiles = scandir($tryThisFolder);
            foreach ($foldersAndFiles as $f) {
                if (preg_match('/\.xml/', $f)) {
                    $fileContents = file_get_contents($tryThisFolder.'/'.$f);
                    if (preg_match('/(\<install|\<extension )/', $fileContents)) {
                        return realpath($tryThisFolder.'/'.$f);
                    }
                }
            }
            // look for ANY xml files in this folder

            // If you find an xml file - look inside it to see if its a manifest/install
        }

        return false;
    }

    /**
     * Remove the JPATH_BASE from a file with path to prevent leaking absolute paths.
     *
     * @param $path string The full path to a file
     *
     * @return string The absolute path to the file
     */
    private function removeBase($path)
    {
        return str_replace(JPATH_BASE, '', $path);
    }

    /**
     * Updates an extension.
     *
     * @param $extensionId
     *
     * @return bool
     */
    public function doUpdate($extensionId)
    {
        // Manipulate the base Uri in the Joomla Stack to provide compatibility with some 3pd extensions like ACL Manager!
        try {
            $uri = \Joomla\CMS\Uri\Uri::getInstance();

            $reflection   = new \ReflectionClass($uri);
            $baseProperty = $reflection->getProperty('base');
            $baseProperty->setAccessible(true);
            $base           = $baseProperty->getValue();
            $base['prefix'] = $uri->toString(array('scheme', 'host'));
            $base['path']   = '/';
            $baseProperty->setValue($base);
        } catch (ReflectionException $e) {
        }

        require JPATH_BASE.'/administrator/components/com_installer/models/update.php';

        $model = new InstallerModelUpdate();

        if ($res = $model->update(array($extensionId))) {
            $cache = JFactory::getCache('mod_menu');
            $cache->clean();
        }

        return $model->getState('result');
    }
}
