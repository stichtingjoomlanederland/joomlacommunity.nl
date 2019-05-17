<?php

/**
 * @copyright     Copyright (c) 2009-2019 Ryan Demmer. All rights reserved
 * @license       GNU/GPL 2 or later - http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * JCE is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses
 */
class WFImgmanager_extPluginConfig
{
    public static function getConfig(&$settings)
    {
        require_once __DIR__ . '/imgmanager_ext.php';

        // set plugin
        JFactory::getApplication()->input->set('plugin', 'imgmanager_ext');

        $plugin = new WFImgManager_ExtPlugin();

        if ($plugin->getParam('inline_upload', 1)) {
            $settings['imgmanager_ext_upload'] = array(
                'max_size' => $plugin->getParam('max_size', 1024),
                'filetypes' => $plugin->getFileTypes(),
            );
        }
    }
}
