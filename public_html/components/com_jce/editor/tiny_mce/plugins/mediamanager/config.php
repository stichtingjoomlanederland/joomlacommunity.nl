<?php

/**
 * @copyright     Copyright (c) 2009-2020 Ryan Demmer. All rights reserved
 * @license       GNU/GPL 2 or later - http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * JCE is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses
 */
class WFMediamanagerPluginConfig
{
    public static function getConfig(&$settings)
    {
        require_once __DIR__ . '/mediamanager.php';

        $plugin = new WFMediaManagerPlugin();

        $config = array();

        if ($plugin->getParam('quickmedia', 1) == 0) {
            $config['quickmedia'] = false;
        }

        if ($plugin->getParam('aggregator.youtube.enable', 1) || $plugin->getParam('aggregator.vimeo.enable', 1)) {
            $settings['invalid_elements'] = array_diff($settings['invalid_elements'], array('iframe'));
        }

        if ($plugin->getParam('inline_upload', 1)) {
            // get the list of filetypes supported
            $filetypes = array_values($plugin->getFileTypes());

            // only allow a limited set that are support by the <video> and <audio> tags
            $filetypes = array_intersect($filetypes, array('mp3', 'oga', 'm4a', 'mp4', 'm4v', 'ogg', 'webm', 'ogv'));

            $config['upload'] = array(
                'max_size' => $plugin->getParam('max_size', 1024),
                'filetypes' => array_values($filetypes),
            );
        }

        $settings['mediamanager'] = $config;
    }
}
