<?php
/**
 * @package     DOCman
 * @copyright   Copyright (C) 2011 - 2014 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

class PlgSystemJoomlatoolsupdater extends JPlugin
{
    protected static $_extensions = array(
        'docman' => array('type' => 'component', 'element' => 'com_docman'),
        'fileman' => array('type' => 'component', 'element' => 'com_fileman'),
        'leadman' => array('type' => 'component', 'element' => 'com_leadman'),
        'logman' => array('type' => 'component', 'element' => 'com_logman'),
        'textman' => array('type' => 'component', 'element' => 'com_textman'),
        'connect' => array('type' => 'plugin', 'element' => 'connect', 'folder' => 'koowa')
    );

    const BASE_URL = 'https://api.joomlatools.com/';

    public function onInstallerBeforePackageDownload(&$url, &$headers)
    {
        if (strpos($url, static::BASE_URL) === false) {
            return;
        }

        if (!preg_match('#extension/([a-z_]+)\.zip#i', $url, $matches)) {
            return;
        }

        $headers['Referer'] = JURI::root();
        $extension = $matches[1];
        $error = false;

        if (array_key_exists($extension, static::$_extensions)) {
            $api_key = $this->_getApiKey(static::$_extensions[$extension]);

            if ($api_key) {
                $headers['Authorization'] = 'Bearer '.$api_key;
            }
            else {
                $error = sprintf('API key for %s not found.', ucfirst($extension));

            }
        }

        if (!$error) {
            try {
                $res = \JHttpFactory::getHttp()->head($url, $headers);
                if ($res->code === 403) {
                    $error = sprintf('Cannot validate your API key for %s.', ucfirst($extension));
                }
            }
            catch (\RuntimeException $exception) {
                $error = sprintf('Unable to download %s.', ucfirst($extension));
            }
        }

        if ($error) {
            $error .= ' Please go to <a target="_blank" href="https://dashboard.joomlatools.com">Joomlatools Dashboard</a> and download the latest version manually.';

            \JLog::add($error, \JLog::ERROR, 'jerror');

            $app = JFactory::getApplication();

            if ($app->isClient('administrator') && JFactory::getDocument()->getType() === 'html') {
                $redirect_url = $app->getUserState('com_installer.redirect_url');

                // Don't redirect to an external URL.
                if (!JUri::isInternal($redirect_url)) {
                    $redirect_url = '';
                }

                if (empty($redirect_url)) {
                    $redirect_url = JRoute::_('index.php?option=com_installer&view=update', false);
                }
                else {
                    // Wipe out the user state when we're going to redirect.
                    $app->setUserState('com_installer.redirect_url', '');
                    $app->setUserState('com_installer.message', '');
                    $app->setUserState('com_installer.extension_message', '');
                }

                $app->redirect($redirect_url);
            }
        }
    }

    public function onBeforeCompileHead()
    {
        $app = JFactory::getApplication();

        if ($app->isClient('administrator') && JFactory::getDocument()->getType() === 'html') {
            $option = str_replace('com_', '', $app->input->get('option'));

            if (array_key_exists($option, static::$_extensions) && static::$_extensions[$option]['type'] === 'component') {
                $this->_addUpdateNotifier($option);
            }
        }
    }

    public function onGetIcons($context)
    {
        $this->_addUpdateNotifier();
    }

    protected function _addUpdateNotifier($extension = null)
    {
        if (!JFactory::getUser()->authorise('core.manage', 'com_installer')) {
            return;
        }

        $extension_list = static::$_extensions;
        if ($extension !== null) {
            $extension_list = array($extension => $extension_list[$extension]);
        }

        $extensions = array();
        $api_key    = '';

        foreach ($extension_list as $key => $extension) {
            if (file_exists($this->_getExtensionPath($extension, 'manifest'))) {
                $version = 'unknown';

                if ($v = $this->_getExtensionVersion($extension)) {
                    $version = $v;
                }

                if ($new_key = $this->_getApiKey($extension)) {
                    $api_key = $new_key;
                }

                $extensions[] = $key.'@'.$version;
            }
        }

        if (!count($extensions)) {
            return;
        }

        $query = array(
            'joomla' => JVERSION,
            'php'    => PHP_VERSION,
            'extensions' => implode(',', $extensions)
        );

        $url = static::BASE_URL.'extensions.json?'.http_build_query($query, null, '&');

        $token    = JSession::getFormToken() . '=' . 1;
        $updates_url      = JUri::base() . 'index.php?option=com_installer&view=update&task=update.find&' . $token;
        $updates_ajax_url = JUri::base() . 'index.php?option=com_installer&view=update&task=update.ajax&eid=0&skip=700&' . $token;

        JHtml::_('jquery.framework');

        $script = /** @lang javascript */
            <<<JS
                
jQuery(function($) {
    if (typeof sessionStorage === 'undefined' || typeof sessionStorage.joomlatools_updater_notified === 'undefined') {
        /**
         * Show notifications if there are available updates 
         */
        var showMessages = function(messages) {
            var error_container = $('#system-message-container');
        
            $.each(messages, function(i, message) {
                if (typeof message !== 'object' || typeof message.type === 'undefined') {
                    return;
                }   
    
                var type = message.type,
                    msg  = message.message.replace(/{upgrade_url}/g, '$updates_url'), 
                    c = error_container.find('.alert-joomlatoolsupdate.alert-'+type);
    
                if (c.length == 0) {
                    c = $('<div style="text-align: center" class="alert alert-joomlatoolsupdate alert-'+type+'"></div>');
                    error_container.append(c);
                }
    
                c.append($('<p>'+msg+'</p>'));
            });
        };
        
        $.ajax({
            url: '$url',
            dataType: 'json',
            cache: false,
            headers: {
                'Authorization': 'Bearer $api_key'
            }	
        }).done(function(response) {
            if (typeof response.data !== 'undefined') {
            
                if (response.data.length) {
                    showMessages(response.data);
            
                    $.ajax({url: '$updates_ajax_url'});
                }
            
                if (typeof sessionStorage !== 'undefined') {
                    sessionStorage.joomlatools_updater_notified = true;
                }
            }
        });
    }
});

JS;

        JFactory::getDocument()->addScriptDeclaration($script);
    }

    protected function _getApiKey($extension)
    {
        $key  = null;
        $file = $this->_getExtensionPath($extension, 'apikey');

        if (file_exists($file)) {
            $file = trim(@file_get_contents($file));
            $file = str_replace(array("\n", "\r"), '', $file);

            if (strlen($file) > 0 && strlen($file) < 2048) {
                $key = $file;
            }
        }

        return $key;
    }

    /**
     * @param array  $extension
     * @param string $path_type manifest or apikey
     *
     * @return string
     */
    protected function _getExtensionPath($extension, $path_type)
    {
        $path = null;
        $type = $extension['type'];

        if ($path_type === 'manifest')
        {
            if ($type === 'component') {
                $path = sprintf('administrator/components/%1$s/%2$s.xml', $extension['element'], str_replace('com_', '', $extension['element']));
            } elseif ($type === 'plugin') {
                $path = sprintf('plugins/%1$s/%2$s/%2$s.xml', $extension['folder'], $extension['element']);
            }
        }
        elseif ($path_type === 'apikey') {
            if ($type === 'component') {
                $path = sprintf('administrator/components/%1$s/resources/install/.api.key', $extension['element']);
            } elseif ($type === 'plugin') {
                $path = sprintf('plugins/%1$s/%2$s/.api.key', $extension['folder'], $extension['element']);
            }
        }

        return JPATH_ROOT.'/'.$path;

    }

    /**
     * Returns null if the extension does not exist, version number as string if it exists
     * or 'unknown' if extension exists but the version number cannot be determined
     * @param $extension
     * @return null|string
     */
    protected function _getExtensionVersion($extension)
    {
        $version = null;

        try {
            $version = 'unknown';
            $folder  = isset($extension['folder']) ? $extension['folder'] : '';

            $query = /** @lang text */"SELECT manifest_cache FROM #__extensions
                    WHERE type = '{$extension['type']}' AND element = '{$extension['element']}' AND folder = '$folder'
                    LIMIT 1
                    ";

            if ($result = JFactory::getDbo()->setQuery($query)->loadResult()) {
                $manifest = @json_decode($result);

                if (is_object($manifest) && !empty($manifest->version)) {
                    $version = $manifest->version;
                }
            }

        }
        catch (Exception $e) {}

        return $version;
    }

}
