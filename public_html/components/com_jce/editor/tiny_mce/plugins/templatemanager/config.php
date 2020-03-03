<?php

/**
 * @copyright     Copyright (c) 2009-2020 Ryan Demmer. All rights reserved
 * @license       GNU/GPL 2 or later - http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * JCE is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses
 */
class WFTemplateManagerPluginConfig
{
    public static function getConfig(&$settings)
    {
        $wf = WFApplication::getInstance();

        $config = array();

        $config['selected_content_classes'] = $wf->getParam('templatemanager.selected_content_classes', '');
        $config['cdate_classes'] = $wf->getParam('templatemanager.cdate_classes', 'cdate creationdate', 'cdate creationdate');
        $config['mdate_classes'] = $wf->getParam('templatemanager.mdate_classes', 'mdate modifieddate', 'mdate modifieddate');
        $config['cdate_format'] = $wf->getParam('templatemanager.cdate_format', '%m/%d/%Y : %H:%M:%S', '%m/%d/%Y : %H:%M:%S');
        $config['mdate_format'] = $wf->getParam('templatemanager.mdate_format', '%m/%d/%Y : %H:%M:%S', '%m/%d/%Y : %H:%M:%S');

        $config['content_url'] = $wf->getParam('templatemanager.content_url', '');

        $templates = $wf->getParam('templatemanager.templates', array());

        if (is_string($templates)) {
            $templates = json_decode(htmlspecialchars_decode($templates), true);
        }

        require_once __DIR__ . '/templatemanager.php';

        $plugin = new WFTemplateManagerPlugin();

        // associative array of template items
        $list = array();

        if (!empty($templates)) {
            foreach ($templates as $template) {                
                extract($template);

                $value = "";

                if (!empty($url)) {
                    if (preg_match("#\.(htm|html|txt)$#", $url) && strpos('://', $url) === false) {
                        if (is_file(JPATH_SITE . '/' . trim($url, '/'))) {
                            $value = JURI::root() . '/' . trim($url, '/');
                        }
                    }
                } else if (!empty($html)) {
                    $value = htmlspecialchars_decode($html);
                }

                $list[$name] = $value;
            }
        } else {
            $list = $plugin->getTemplateList();
        }

        if ($plugin->getParam('inline_upload', 1)) {
            $config['upload'] = array(
                'max_size'  => $plugin->getParam('max_size', 1024),
                'filetypes' => $plugin->getFileTypes()
            );
        }

        if (!empty($list)) {
            $config['templates'] = $list;
        }

        $settings['templatemanager'] = $config;
    }
}
