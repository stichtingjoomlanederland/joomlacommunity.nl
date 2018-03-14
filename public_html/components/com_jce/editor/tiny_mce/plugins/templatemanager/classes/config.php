<?php

/**
 * @copyright 	Copyright (c) 2009-2017 Ryan Demmer. All rights reserved
 * @license   	GNU/GPL 2 or later - http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * JCE is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses
 */
class WFTemplateManagerPluginConfig
{
    public static function getConfig(&$settings)
    {
        $wf = WFEditor::getInstance();

        $settings['templatemanager_selected_content_classes'] = $wf->getParam('templatemanager.selected_content_classes', '');
        $settings['templatemanager_cdate_classes'] = $wf->getParam('templatemanager.cdate_classes', 'cdate creationdate', 'cdate creationdate');
        $settings['templatemanager_mdate_classes'] = $wf->getParam('templatemanager.mdate_classes', 'mdate modifieddate', 'mdate modifieddate');
        $settings['templatemanager_cdate_format'] = $wf->getParam('templatemanager.cdate_format', '%m/%d/%Y : %H:%M:%S', '%m/%d/%Y : %H:%M:%S');
        $settings['templatemanager_mdate_format'] = $wf->getParam('templatemanager.mdate_format', '%m/%d/%Y : %H:%M:%S', '%m/%d/%Y : %H:%M:%S');

        $settings['templatemanager_content_url'] = $wf->getParam('templatemanager.content_url', '');
    }
}
