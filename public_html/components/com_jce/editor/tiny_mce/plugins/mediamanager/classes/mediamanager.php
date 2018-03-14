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

if (!defined('_WF_EXT')) {
    define('_WF_EXT', 1);
}

wfimport('editor.libraries.classes.manager');
wfimport('editor.libraries.classes.extensions.aggregator');
wfimport('editor.libraries.classes.extensions.popups');

/**
 * MediaManager Class.
 *
 * @author $Author: Ryan Demmer
 */
class WFMediaManagerPlugin extends WFMediaManager
{
    /*
     * @var string
     */

    public $_filetypes = 'windowsmedia=avi,wmv,wm,asf,asx,wmx,wvx;quicktime=mov,qt,mpg,mpeg,m4a;flash=swf;shockwave=dcr;real=rm,ra,ram;divx=divx;video=mp4,ogv,ogg,webm,flv,f4v;audio=mp3,ogg,wav;silverlight=xap';

    /**
     * Display the plugin.
     */
    public function display()
    {
        parent::display();

        $document = WFDocument::getInstance();

        $document->addScript(array('mediamanager'), 'plugins');
        $document->addStyleSheet(array('mediamanager'), 'plugins');

        $document->addScriptDeclaration('MediaManagerDialog.settings='.json_encode($this->getSettings()).';');

        $tabs = WFTabs::getInstance(array('base_path' => WF_EDITOR_PLUGIN));

        // Add tabs
        $tabs->addTab('file', 1, array('plugin' => $this));
        $tabs->addTab('media', $this->getParam('tabs_media', 1), array('plugin' => $this));
        $tabs->addTab('advanced', $this->getParam('tabs_advanced', 1));

        // Load Popups instance
        $popups = WFPopupsExtension::getInstance(array(
                    // map src value to popup link href
                    'map' => array('href' => 'src'),
                    'default' => $this->getParam('mediamanager.popups.default', ''),
        ));

        $popups->display();

        // Load video aggregators (Youtube, Vimeo etc)
        $this->loadAggregators();
    }

    /**
     * Get a list of media extensions.
     *
     * @param bool	Map the extensions to media type
     *
     * @return string Extension list or type map
     */
    protected function getMediaTypes($map = false)
    {
        $extensions = $this->getParam('extensions', $this->get('_filetypes'));

        if ($map) {
            return $extensions;
        } else {
            $this->listFileTypes($extensions);
        }
    }

    protected function setMediaOption($name, $value)
    {
        $options = $this->get('_media_options');

        $options[$name] = $value;

        $this->set('_media_options', $options);
    }

    public function getMediaOptions()
    {
        $list = $this->getParam('extensions', $this->get('_filetypes'));

        $options = '';

        if ($list) {
            foreach (explode(';', $list) as $type) {
                $kv = explode('=', $type);

                if (substr($kv[0], 0, 1) === '-') {
                    continue;
                }

                $options .= '<option value="'.$kv[0].'">'.WFText::_('WF_MEDIAMANAGER_'.strtoupper($kv[0]).'_TITLE').'</option>'."\n";
            }

            foreach ($this->get('_media_options') as $k => $v) {
                $options .= '<option value="'.$k.'">'.WFText::_($v, ucfirst($k)).'</option>'."\n";
            }
        }

        return $options;
    }

    protected function getViewable()
    {
        return $this->get('filetypes');
    }

    protected function loadAggregators()
    {
        $extension = WFAggregatorExtension::getInstance(array('format' => 'video'));
        $extension->display();

        foreach ($extension->getAggregators() as $aggregator) {
            // set the Media Type option
            $this->setMediaOption($aggregator->getName(), $aggregator->getTitle());
        }
    }

    public function getAggregatorTemplate()
    {
        $tpl = '';

        $extension = WFAggregatorExtension::getInstance();

        foreach ($extension->getAggregators() as $aggregator) {
            $tpl .= '<div class="media_option '.$aggregator->getName().'" id="'.$aggregator->getName().'_options" style="display:none;"><h4>'.WFText::_($aggregator->getTitle()).'</h4>';
            $tpl .= $extension->loadTemplate($aggregator->getName());
            $tpl .= '</div>';
        }

        return $tpl;
    }

    public function getSettings($settings = array())
    {
        $settings = array(
            // Plugin parameters
            'media_types' => $this->get('filetypes', $this->get('_filetypes')),
            'defaults' => $this->getDefaults(),
        );

        return parent::getSettings($settings);
    }
}
