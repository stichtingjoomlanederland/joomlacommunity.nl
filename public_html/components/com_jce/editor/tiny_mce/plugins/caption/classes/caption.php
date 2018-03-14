<?php

/**
 * @copyright 	Copyright (c) 2009-2017 Ryan Demmer. All rights reserved
 * @license   	GNU/GPL 2 or later - http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * JCE is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses
 */
defined('_JEXEC') or die('RESTRICTED');
// Load class dependencies
wfimport('editor.libraries.classes.plugin');

class WFCaptionPlugin extends WFEditorPlugin
{
    /**
     * Constructor activating the default information of the class.
     */
    public function __construct()
    {
        parent::__construct();
    }

    public function display()
    {
        parent::display();

        $document = WFDocument::getInstance();
        $settings = $this->getSettings();

        $document->addScriptDeclaration('CaptionDialog.settings='.json_encode($settings).';');

        $tabs = WFTabs::getInstance(array(
                    'base_path' => WF_EDITOR_PLUGIN,
                ));

        // Add tabs
        $tabs->addTab('text', 1);
        $tabs->addTab('container', 1);

        // add link stylesheet
        $document->addStyleSheet(array('caption'), 'plugins');
        // add link scripts last
        $document->addScript(array('caption'), 'plugins');
    }

    public function getSettings($settings = array())
    {
        $settings = array(
            'defaults' => $this->getDefaults(),
        );

        return parent::getSettings($settings);
    }
}
