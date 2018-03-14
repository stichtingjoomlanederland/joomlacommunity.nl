<?php

/**
 * @copyright     Copyright (c) 2009-2017 Ryan Demmer. All rights reserved
 * @license       GNU/GPL 2 or later - http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * JCE is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses
 */
defined('_JEXEC') or die('RESTRICTED');

// set as an extension parent
if (!defined('_WF_EXT')) {
    define('_WF_EXT', 1);
}

require_once WF_EDITOR_LIBRARIES.'/classes/manager.php';
require_once WF_EDITOR_LIBRARIES.'/classes/extensions/popups.php';

class WFImageManagerExtPlugin extends WFMediaManager
{
    public $_filetypes = 'jpg,jpeg,png,gif';

    public function __construct($config = array())
    {
        $config = array(
            'can_edit_images' => 1,
            'show_view_mode' => 1,
            'colorpicker' => true,
        );

        parent::__construct($config);

        $request = WFRequest::getInstance();

        if ($config['can_edit_images'] && $this->getParam('imgmanager_ext.thumbnail_editor', 1)) {
            $request->setRequest(array($this, 'createThumbnail'));
            $request->setRequest(array($this, 'deleteThumbnail'));
        }

        if (JRequest::getCmd('dialog', 'plugin') === 'plugin') {
            $this->addFileBrowserEvent('onFilesDelete', array($this, 'onFilesDelete'));
            $this->addFileBrowserEvent('onGetItems', array($this, 'processListItems'));
            $this->addFileBrowserEvent('onUpload', array($this, 'onUpload'));
        }
    }

    /**
     * Display the plugin.
     */
    public function display()
    {
        $layout = JRequest::getCmd('layout', 'plugin');

        if ($layout === 'editor') {
            return parent::display();
        }

        if ($this->getParam('imgmanager_ext.insert_multiple', 1)) {
            $this->addFileBrowserButton('file', 'insert_multiple', array('action' => 'selectMultiple', 'title' => WFText::_('WF_BUTTON_INSERT_MULTIPLE'), 'multiple' => true, 'single' => false, 'icon' => 'th-large'));
        }

        if ($this->get('can_edit_images') && $this->getParam('imgmanager_ext.thumbnail_editor', 1)) {
            $this->addFileBrowserButton('file', 'thumb_create', array('action' => 'createThumbnail', 'title' => WFText::_('WF_BUTTON_CREATE_THUMBNAIL'), 'trigger' => true, 'icon' => 'clone'));
            $this->addFileBrowserButton('file', 'thumb_delete', array('action' => 'deleteThumbnail', 'title' => WFText::_('WF_BUTTON_DELETE_THUMBNAIL'), 'trigger' => true, 'icon' => 'clone trash'));
        }

        parent::display();

        $document = WFDocument::getInstance();

        // create new tabs instance
        $tabs = WFTabs::getInstance(array(
            'base_path' => WF_EDITOR_PLUGINS.'/imgmanager',
        ));

        // Add tabs
        $tabs->addTab('image', 1, array('plugin' => $this));

        $tabs->addTab('rollover', $this->getParam('tabs_rollover', 1));
        $tabs->addTab('advanced', $this->getParam('tabs_advanced', 1));

        // load editing scripts
        $document->addScript(array('transform'), 'pro');
        $document->addStyleSheet(array('transform'), 'pro');

        $document->addScript(array('imgmanager', 'thumbnail'), 'plugins');
        $document->addStyleSheet(array('imgmanager'), 'plugins');

        $document->addScriptDeclaration('ImageManagerDialog.settings='.json_encode($this->getSettings()).';');

        // Load Popups instance
        $popups = WFPopupsExtension::getInstance(array(
            // map src value to popup link href
            'map' => array('href' => 'popup_src'),
            // set text to false
            'text' => false,
            // default popup option
            'default' => $this->getParam('imgmanager_ext.popups.default', ''),
        )
        );

        $popups->addTemplate('popup');
        $popups->display();

        if ($this->getParam('tabs_responsive', 1)) {
            $tabs->addTemplatePath(WF_EDITOR_PLUGINS.'/imgmanager_ext/tmpl');

            // Add tabs
            $tabs->addTab('responsive', 1, array('plugin' => $this));
        }
    }

    public function onUpload($file, $relative = '')
    {
        parent::onUpload($file, $relative);

        $browser = $this->getFileBrowser();
        $editor = $this->getImageEditor();

        $params = $this->getParams(array('key' => 'imgmanager_ext'));

        if (JRequest::getInt('inline', 0) === 1) {
            $result = array(
                'file' => $relative,
                'name' => basename($file),
            );

            if ($params->get('always_include_dimensions', 1)) {
                $dim = @getimagesize($file);

                if ($dim) {
                    $result['width'] = $dim[0];
                    $result['height'] = $dim[1];
                }
            }

            $defaults = $this->getDefaults();

            unset($defaults['always_include_dimensions']);

            if (!empty($defaults)) {
                $styles = array();
            }

            foreach ($defaults as $k => $v) {
                switch ($k) {
                    case 'align':
                        // convert to float
                        if ($v == 'left' || $v == 'right') {
                            $k = 'float';
                        } else {
                            $k = 'vertical-align';
                        }

                        $styles[$k] = $v;

                        break;
                    case 'border_width':
                    case 'border_style':
                    case 'border_color':
                        // only if border state set
                        $v = $defaults['border'] ? $v : '';

                        // add px unit to border-width
                        if ($v && $k == 'border_width' && is_numeric($v)) {
                            $v .= 'px';
                        }

                        // check for value and exclude border state parameter
                        if ($v != '') {
                            $styles[str_replace('_', '-', $k)] = $v;
                        }

                        break;
                    case 'margin_left':
                    case 'margin_right':
                    case 'margin_top':
                    case 'margin_bottom':
                        // add px unit to border-width
                        if ($v && is_numeric($v)) {
                            $v .= 'px';
                        }

                        // check for value and exclude border state parameter
                        if ($v != '') {
                            $styles[str_replace('_', '-', $k)] = $v;
                        }
                        break;
                    case 'classes':
                    case 'title':
                    case 'id':
                    case 'direction':
                    case 'usemap':
                    case 'longdesc':
                    case 'style':
                    case 'alt':
                        if ($k == 'direction') {
                            $k = 'dir';
                        }

                        if ($k == 'classes') {
                            $k = 'class';
                        }

                        if ($v != '') {
                            $result[$k] = $v;
                        }

                        break;
                }
            }

            if (!empty($styles)) {
                $result['styles'] = $styles;
            }

            return $result;
        }

        return $browser->getResult();
    }

    /**
     * Manipulate file and folder list.
     *
     * @param  array file/folder array reference
     *
     * @since  1.5
     */
    public function processListItems(&$result)
    {
        $browser = $this->getFileBrowser();

        if (empty($result['files'])) {
            return;
        }

        // clean cache
        $filesystem = $browser->getFileSystem();

        for ($i = 0; $i < count($result['files']); ++$i) {
            $file = $result['files'][$i];

            $thumbnail = $this->getThumbnail($file['id']);

            $classes = array();
            $properties = array();
            $trigger = array();

            // add transform trigger
            $trigger[] = 'transform';

            // add thumbnail properties
            if ($thumbnail && $thumbnail != $file['id']) {
                $classes[] = 'thumbnail';
                $properties['thumbnail-src'] = WFUtility::makePath($filesystem->getRootDir(), $thumbnail, '/');

                $dim = @getimagesize(WFUtility::makePath($browser->getBaseDir(), $thumbnail));

                if ($dim) {
                    $properties['thumbnail-width'] = $dim[0];
                    $properties['thumbnail-height'] = $dim[1];
                }
                $trigger[] = 'thumb_delete';
            } else {
                $trigger[] = 'thumb_create';
            }

            // add trigger properties
            $properties['trigger'] = implode(',', $trigger);

            /*$exif = $this->getExifData($file['id']);

            if (!empty($exif)) {                                
                if (!empty($exif['ImageDescription'])) {
                    $properties['caption'] = $exif['ImageDescription'];
                }
            }*/

            $result['files'][$i] = array_merge($file, array('classes' => implode(' ', array_merge(explode(' ', $file['classes']), $classes)), 'properties' => array_merge($file['properties'], $properties)));
        }
    }

    private function getExifData($relative) {
        $data = array();

        $browser    = $this->getFileBrowser();
        $image      = WFUtility::makePath($browser->getBaseDir(), $relative);
        
        if (function_exists('exif_read_data')) {
            $data = exif_read_data($image);
        }

        return $data;
    }

    /**
     * Check for the thumbnail for a given file.
     *
     * @param string $relative The relative path of the file
     *
     * @return The thumbnail URL or false if none
     */
    private function getThumbnail($relative)
    {
        // get browser
        $browser = $this->getFileBrowser();
        $filesystem = $browser->getFileSystem();

        $path = WFUtility::makePath($browser->getBaseDir(), $relative);
        $dim = @getimagesize($path);

        $dir = WFUtility::makePath(str_replace('\\', '/', dirname($relative)), $this->getParam('thumbnail_folder', 'thumbnails'));
        $thumbnail = WFUtility::makePath($dir, $this->getThumbName($relative));

        // Image has a thumbnail prefix
        if (strpos($relative, $this->getParam('thumbnail_prefix', 'thumb_', false)) === 0) {
            return $relative;
        }

        // The original image is smaller than a thumbnail so just return the url to the original image.
        if ($dim[0] <= $this->getParam('thumbnail_size', 120) && $dim[1] <= $this->getParam('thumbnail_size', 90)) {
            return $relative;
        }
        //check for thumbnails, if exists return the thumbnail url
        if (file_exists(WFUtility::makePath($browser->getBaseDir(), $thumbnail))) {
            return $thumbnail;
        }

        return false;
    }

    private function getThumbPath($file)
    {
        return WFUtility::makePath($this->getThumbDir($file, false), $this->getThumbName($file));
    }

    public function onFilesDelete($file)
    {
        $browser = $this->getFileBrowser();

        if (file_exists(WFUtility::makePath($browser->getBaseDir(), $this->getThumbPath($file)))) {
            return $this->deleteThumbnail($file);
        }

        return $browser->getResult();
    }

    public function getThumbnailDimensions($file)
    {
        return $this->getDimensions($this->getThumbPath($file));
    }

    public function deleteThumbnail($file)
    {
        if (!$this->checkAccess('thumbnail_editor', 1)) {
            JError::raiseError(403, 'Access to this resource is restricted');
        }

        // check path
        WFUtility::checkPath($file);

        $browser = $this->getFileBrowser();
        $filesystem = $browser->getFileSystem();
        $dir = $this->getThumbDir($file, false);

        if ($browser->deleteItem($this->getThumbPath($file))) {
            if ($filesystem->countFiles($dir) == 0 && $filesystem->countFolders($dir) == 0) {
                if (!$browser->deleteItem($dir)) {
                    $browser->setResult(WFText::_('WF_IMGMANAGER_EXT_THUMBNAIL_FOLDER_DELETE_ERROR'), 'error');
                }
            }
        }

        return $browser->getResult();
    }

    public function getSettings($settings = array())
    {
        $params = $this->getParams(array('key' => 'imgmanager_ext'));

        $settings = array(
            'attributes' => array(
                'dimensions' => $params->get('attributes_dimensions', 1),
                'align' => $params->get('attributes_align', 1),
                'margin' => $params->get('attributes_margin', 1),
                'border' => $params->get('attributes_border', 1),
            ),
            'always_include_dimensions' => $params->get('always_include_dimensions', 0),
            // Thumbnails
            'upload_thumbnail' => $params->get('upload_thumbnail', 1),
            'upload_thumbnail_state' => $params->get('upload_thumbnail_state', 0),
            'upload_thumbnail_crop' => $params->get('upload_thumbnail_crop', 0),
            'thumbnail_width' => $params->get('thumbnail_width', 120),
            'thumbnail_height' => $params->get('thumbnail_height', 90),
            'thumbnail_quality' => $params->get('thumbnail_quality', 80),
            'can_edit_images' => 1,
        );

        return parent::getSettings($settings);
    }

    protected function getFileBrowserConfig($config = array())
    {
        $config = array(
            'upload_thumbnail_width' => $this->getParam('thumbnail_width', 120),
            'upload_thumbnail_height' => $this->getParam('thumbnail_height', 90),
            'upload_thumbnail_quality' => $this->getParam('thumbnail_quality', 80)
        );

        return parent::getFileBrowserConfig($config);
    }
}
