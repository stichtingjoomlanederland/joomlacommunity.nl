<?php

/**
 * @copyright	KAINOTOMO PH LTD - All rights reserved.
 * @author		KAINOTOMO PH LTD
 * @link		http://www.kainotomo.com
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
// No direct access
defined('_JEXEC') or die;

JLoader::import('joomla.application.component.model');

class CYENDModelRemoteUpdate extends JModelLegacy {

    /**
     *
     * @var string Download ID of user
     */
    protected $downloadid;

    /**
     *
     * @var string The name of the current extension
     */
    protected $extension;

    /**
     *
     * @var string current site secret
     */
    protected $secret;

    /**
     * Construction function
     * 
     * @param array $config
     */
    public function __construct($config = array()) {

        if (array_key_exists('extension', $config)) {
            $this->extension = $config['extension'];
        } else {
            $app = JFactory::getApplication();
            $this->extension = $app->input->get('option', null);
        }

        if (array_key_exists('downloadid', $config)) {
            $this->downloadid = $config['downloadid'];
        } else {
            $params = JComponentHelper::getParams($this->extension, true);
            if ($params) {
                $this->downloadid = $params->get('downloadid');
            } else {
                $this->downloadid = null;
            }
        }

        if (array_key_exists('secret', $config)) {
            $this->secret = $config['secret'];
        } else {
            $this->secret = JFactory::getConfig()->get('secret');
        }

        parent::__construct($config);
    }

    /**
     * Method to update with download id the update_sites table
     * @param string $extension The name of the extension to update
     */
    public function updateDownloadId($extension = null) {

        if ($extension == null) {
            $extension = $this->extension;
        }

        $downloadid = $this->downloadid;
        if (empty($downloadid)) {
            return false;
        }

        $db = JFactory::getDbo();
        $query = $db->getQuery(true);

        //get data
        $query->clear();
        $query->select('a.extension_id')
                ->from('#__extensions AS a')
                ->where('a.element LIKE ' . $db->quote($extension));
        $query->select('b.update_site_id AS update_site_id');
        $query->join('LEFT', '#__update_sites_extensions AS b ON b.extension_id = a.extension_id');
        $query->select('c.location AS location');
        $query->join('LEFT', '#__update_sites AS c ON c.update_site_id = b.update_site_id');
        $query->order('c.update_site_id DESC');
        $db->setQuery($query);
        $db->execute();
        $results = $db->loadObjectList();
        
        foreach ($results as $counter => $result) {
            
            //clear old entries
            if ($counter > 0) {
                $query->clear();
                $query->delete('#__update_sites_extensions')
                        ->where('update_site_id = ' . $db->q($result->update_site_id))
                        ->where('extension_id = ' . $db->q($result->extension_id));
                $db->setQuery($query);
                $db->execute();
                
                $query->clear();
                $query->delete('#__update_sites')
                        ->where('update_site_id = ' . $db->q($result->update_site_id));
                $db->setQuery($query);
                $db->execute();
                
                continue;
            }

            //compare location
            $url_old = $result->location;
            if (strpos($url_old, 'dlid=' . $downloadid)) {
                continue;
            }

            //fix location
            $url_arr = explode('&', $url_old);
            foreach ($url_arr as $key => $value) {
                if (strpos($value, 'dlid=') === 0) {
                    $url_arr[$key] = 'dlid=' . $downloadid;
                    break;
                }
            }
            $location = implode('&', $url_arr);

            //update location id db
            // Modify the database record
            $update_site = new stdClass();
            $update_site->last_check_timestamp = 0;
            $update_site->enabled = 1;
            $update_site->location = $location;
            $update_site->update_site_id = $result->update_site_id;
            $db->updateObject('#__update_sites', $update_site, 'update_site_id');
        }

        return true;
    }

    /**
     * Function to set the download id from extension's parameter
     * 
     * @param string $extension Return the download id
     * 
     * @return boolean true on success, false on failre
     */
    public function setDownloadId($extension = null) {

        if ($extension == null) {
            $extension = $this->extension;
        }

        $params = JComponentHelper::getParams($extension, true);
        if ($params) {
            $this->downloadid = $params->get('downloadid');
            return true;
        } else {
            return false;
        }
    }

    /**
     * Contact kainotomo.com to get available subscriptions
     * 
     * @param string $extension Extension name
     * @param string $downloadid User download id
     * @param string $secret current site secret
     * @return boolean or array of available subscriptions
     */
    public function getSubscriptions($extension = null, $downloadid = null, $secret = null) {

        if (is_null($extension)) {
            $extension = $this->extension;
        }

        if (!$this->check_timestamp($extension)) {
            return false;
        }

        if (is_null($downloadid)) {
            $downloadid = $this->downloadid;
        }

        if (is_null($secret)) {
            $secret = $this->secret;
        }

        $post = Array();
        $post['downloadid'] = $downloadid;
        $post['extension'] = $extension;
        $post['secret'] = $secret;

        $url = 'http://cyend.com/extensions/index.php?option=com_cyendsubs';
        $jhttp = JHttpFactory::getHttp();
        try {
            $jhttp_response = $jhttp->post($url, $post);
        } catch (Exception $exc) {
            //echo $exc->getTraceAsString();
            return false;
        }

        if ($jhttp_response->code != 200) {
            return false;
        }

        $doc = new DOMDocument();
        @$doc->loadHTML($jhttp_response->body);
        $nodeValue = $doc->getElementById('cyendsubs_trxs')->nodeValue;
        $result = json_decode($nodeValue);
        if (empty($result)) {
            $this->writeSubscriptions($result);
            return false;
        }

        return $result;
    }

    /**
     * 
     * @param string $data The string to write in file.
     * @param string $extension extension name
     */
    public function writeSubscriptions($data, $extension = null) {

        if (is_null($extension)) {
            $extension = $this->extension;
        }

        $file = JPATH_ROOT .
                DIRECTORY_SEPARATOR . 'administrator' .
                DIRECTORY_SEPARATOR . 'components' .
                DIRECTORY_SEPARATOR . $extension .
                DIRECTORY_SEPARATOR . 'helpers' .
                DIRECTORY_SEPARATOR . 'subs' .
                DIRECTORY_SEPARATOR . 'subs.txt';
        jimport('joomla.filesystem.file');
        if (JFile::exists($file)) {
            JFile::delete($file);
        }
        if (empty($data))
            $data = '[{"id":0,"sub":"00000000000000"}]';
        JFile::write($file, $data);
    }

    /**
     * Check a file timestamp if older than a day
     * 
     * @param type $extension extension name
     * @param string $file filename full path
     * 
     * @return boolean
     */
    public function check_timestamp($extension = null, $file = null) {

        if (is_null($extension)) {
            $extension = $this->extension;
        }

        if (is_null($file)) {
            $file = JPATH_ROOT .
                    DIRECTORY_SEPARATOR . 'administrator' .
                    DIRECTORY_SEPARATOR . 'components' .
                    DIRECTORY_SEPARATOR . $extension .
                    DIRECTORY_SEPARATOR . 'helpers' .
                    DIRECTORY_SEPARATOR . 'subs' .
                    DIRECTORY_SEPARATOR . 'subs.txt';
        }

        if (!is_file($file)) {
            return true;
        }

        $time = filemtime($file);
        $curtime = time();
        $diff = $curtime - $time;
        if ($diff > 86400) {
            return true;
        }
        return false;
    }

}
