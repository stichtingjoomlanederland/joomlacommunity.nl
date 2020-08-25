<?php

/**
 * @copyright     Copyright (c) 2009-2020 Ryan Demmer. All rights reserved
 * @license       GNU/GPL 2 or later - http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * JCE is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses
 */
defined('JPATH_PLATFORM') or die;

// set as an extension parent
if (!defined('_WF_EXT')) {
    define('_WF_EXT', 1);
}

require_once WF_EDITOR_LIBRARIES . '/classes/manager.php';
require_once WF_EDITOR_LIBRARIES . '/classes/extensions/popups.php';

class WFImgManager_ExtPlugin extends WFMediaManager
{
    public $_filetypes = 'jpg,jpeg,png,apng,gif,webp,avif';

    protected $name = 'imgmanager_ext';

    public function __construct($config = array())
    {
        $config = array(
            'can_edit_images' => 1,
            'show_view_mode' => 1,
            'colorpicker' => true,
        );

        parent::__construct($config);

        $app = JFactory::getApplication();

        $request = WFRequest::getInstance();

        if ($config['can_edit_images'] && $this->getParam('imgmanager_ext.thumbnail_editor', 1)) {
            $request->setRequest(array($this, 'createThumbnail'));
            $request->setRequest(array($this, 'deleteThumbnail'));
        }

        if ($app->input->getCmd('dialog', 'plugin') === 'plugin') {
            $this->addFileBrowserEvent('onFilesDelete', array($this, 'onFilesDelete'));
            $this->addFileBrowserEvent('onGetItems', array($this, 'processListItems'));
            $this->addFileBrowserEvent('onUpload', array($this, 'onUpload'));
        }

        $request->setRequest(array($this, 'getImageProperties'));
    }

    /**
     * Display the plugin.
     */
    public function display()
    {
        $layout = JFactory::getApplication()->input->getCmd('layout', 'plugin');

        if ($layout === 'editor') {
            return parent::display();
        }

        if ($this->getParam('imgmanager_ext.insert_multiple', 1)) {
            $this->addFileBrowserButton('file', 'insert_multiple', array('action' => 'selectMultiple', 'title' => JText::_('WF_BUTTON_INSERT_MULTIPLE'), 'multiple' => true, 'single' => false, 'icon' => 'multiple-images'));
        }

        if ($this->get('can_edit_images') && $this->getParam('imgmanager_ext.thumbnail_editor', 1)) {
            $this->addFileBrowserButton('file', 'thumb_create', array('action' => 'createThumbnail', 'title' => JText::_('WF_BUTTON_CREATE_THUMBNAIL'), 'trigger' => true, 'icon' => 'thumbnail'));
            $this->addFileBrowserButton('file', 'thumb_delete', array('action' => 'deleteThumbnail', 'title' => JText::_('WF_BUTTON_DELETE_THUMBNAIL'), 'trigger' => true, 'icon' => 'thumbnail-remove'));
        }

        parent::display();

        $document = WFDocument::getInstance();

        // create new tabs instance
        $tabs = WFTabs::getInstance(array(
            'base_path' => WF_EDITOR_PLUGINS . '/imgmanager',
        ));

        // Add tabs
        $tabs->addTab('image', 1, array('plugin' => $this));

        if ($this->allowEvents()) {
            $tabs->addTab('rollover', $this->getParam('tabs_rollover', 1));
        }
        $tabs->addTab('advanced', $this->getParam('tabs_advanced', 1));

        // load editing scripts
        $document->addScript(array('transform'), 'pro');
        $document->addStyleSheet(array('transform'), 'pro');

        $document->addScript(array('imgmanager', 'thumbnail'), 'plugins');
        $document->addStyleSheet(array('imgmanager'), 'plugins');

        $document->addScriptDeclaration('ImageManagerDialog.settings=' . json_encode($this->getSettings()) . ';');

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
            $tabs->addTemplatePath(WF_EDITOR_PLUGINS . '/imgmanager_ext/tmpl');

            // Add tabs
            $tabs->addTab('responsive', 1, array('plugin' => $this));
        }
    }

    private function cleanExifString($string)
    {
        $string = (string) filter_var($string, FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES | FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_BACKTICK);
        return htmlspecialchars($string);
    }

    private function getImageDescription($image)
    {
        $description = '';

        // must be a jpeg
        if (!preg_match('#\.(jpg|jpeg)$#', strtolower($image))) {
            return $description;
        }

        $data = $this->getExifData($image, WFUtility::mb_basename($image));

        if (!empty($data) && isset($data['ImageDescription'])) {
            $description = $this->cleanExifString($data['ImageDescription']);
        }

        return $description;
    }

    public function onUpload($file, $relative = '')
    {
        parent::onUpload($file, $relative);

        $app = JFactory::getApplication();

        if ($app->input->getInt('inline', 0) === 1) {
            $result = array(
                'file' => $relative,
                'name' => WFUtility::mb_basename($file),
            );

            if ($this->getParam('imgmanager_ext.always_include_dimensions', 1)) {
                $dim = @getimagesize($file);

                if ($dim) {
                    $result['width'] = $dim[0];
                    $result['height'] = $dim[1];
                }
            }
            
            // exif description
            $description = $this->getImageDescription($file);

            if ($description) {
                $result['alt'] = $description;
            }

            return array_merge($result, array('attributes' => $this->getDefaultAttributes()));
        }

        return array();
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

            if (empty($file['id'])) {
                continue;
            }

            // only some image types
            if (!in_array(strtolower($file['extension']), array('jpg', 'jpeg', 'png'))) {
                continue;
            }

            $thumbnail = $this->getThumbnail($file['id']);

            $classes = array();
            $properties = array();
            $trigger = array();

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

            $image = $filesystem->toAbsolute($file['id']);
            $description = $this->getImageDescription($image);

            if ($description) {
                $properties['description'] = $description;
            }

            $result['files'][$i] = array_merge($file,
                array(
                    'classes' => implode(' ', array_merge(explode(' ', $file['classes']), $classes)),
                    'properties' => array_merge($file['properties'], $properties),
                )
            );
        }
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

        $thumbfolder = $this->getParam('thumbnail_folder', '', 'thumbnails');

        $dir = WFUtility::makePath(str_replace('\\', '/', dirname($relative)), $thumbfolder);
        $thumbnail = WFUtility::makePath($dir, $this->getThumbName($relative));

        // Image is a thumbnail
        if ($relative === $thumbnail) {
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
            $this->deleteThumbnail($file);
        }

        return array();
    }

    public function getThumbnailDimensions($file)
    {
        return $this->getDimensions($this->getThumbPath($file));
    }

    public function deleteThumbnail($file)
    {
        if (!$this->checkAccess('thumbnail_editor', 1)) {
            throw new Exception(JText::_('JERROR_ALERTNOAUTHOR'));
        }

        // check path
        WFUtility::checkPath($file);

        $browser = $this->getFileBrowser();
        $filesystem = $browser->getFileSystem();
        $dir = $this->getThumbDir($file, false);

        if ($browser->deleteItem($this->getThumbPath($file))) {
            if ($filesystem->countFiles($dir) == 0 && $filesystem->countFolders($dir) == 0) {
                if (!$browser->deleteItem($dir)) {
                    $browser->setResult(JText::_('WF_IMGMANAGER_EXT_THUMBNAIL_FOLDER_DELETE_ERROR'), 'error');
                }
            }
        }

        return $browser->getResult();
    }

    private function getThumbnailOptions()
    {
        $options = array();

        $values = array(
            'thumbnail_width' => 120,
            'thumbnail_height' => 90,
            'thumbnail_quality' => 80,
        );

        $states = array(
            'upload_thumbnail' => 1,
            'upload_thumbnail_state' => 0,
            'upload_thumbnail_crop' => 0,
        );

        foreach ($values as $key => $default) {
            $fallback = $this->getParam('editor.upload_' . $key, '', '$');
            $value = $this->getParam('imgmanager_ext.' . $key, '', '$');

            // indicates an unset value, so use the global value or default
            if ($value === '$') {
                $value = $fallback === '$' ? $default : $fallback;
            }

            $options['upload_' . $key] = $value;
        }

        // unset thumbnail width and height if both are empty, use global values
        if ($options['upload_thumbnail_width'] === '' && $options['upload_thumbnail_height'] === '') {
            unset($options['upload_thumbnail_width']);
            unset($options['upload_thumbnail_height']);
        }

        foreach ($states as $key => $default) {
            $value = $this->getParam('editor.' . $key, $default);
            $options[$key] = $this->getParam('imgmanager_ext.' . $key, '');

            // if the value is empty (unset), use the global value or default
            if ($options[$key] === '') {
                $options[$key] = $value;
            }
        }

        return $options;
    }

    public function getDefaultAttributes()
    {
        $attribs = parent::getDefaultAttributes();

        unset($attribs['always_include_dimensions']);

        return $attribs;
    }

    public function getImageProperties()
    {
        return $this->getDefaultAttributes();
    }

    public function getSettings($settings = array())
    {
        $settings = array(
            'attributes' => array(
                'dimensions' => $this->getParam('imgmanager_ext.attributes_dimensions', 1),
                'align' => $this->getParam('imgmanager_ext.attributes_align', 1),
                'margin' => $this->getParam('imgmanager_ext.attributes_margin', 1),
                'border' => $this->getParam('imgmanager_ext.attributes_border', 1),
            ),
            'always_include_dimensions' => $this->getParam('imgmanager_ext.always_include_dimensions', 0),
            'can_edit_images' => 1,
            'thumbnail_width' => $this->getParam('imgmanager_ext.thumbnail_width', ''),
            'thumbnail_height' => $this->getParam('imgmanager_ext.thumbnail_height', ''),
        );

        return parent::getSettings($settings);
    }

    protected function getFileBrowserConfig($config = array())
    {
        $config = $this->getThumbnailOptions();
        return parent::getFileBrowserConfig($config);
    }
}
