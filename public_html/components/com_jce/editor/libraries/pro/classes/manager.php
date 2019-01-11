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

require_once WF_EDITOR_LIBRARIES . '/classes/manager/base.php';

// load image processor class
require_once dirname(__FILE__) . '/editor.php';

class WFMediaManager extends WFMediaManagerBase
{
    public $can_edit_images = 0;

    public $show_view_mode = 0;

    public function __construct($config = array())
    {
        parent::__construct($config);

        $request = WFRequest::getInstance();
        $layout = JRequest::getCmd('layout', 'plugin');

        if ($layout === 'plugin') {
            $this->addFileBrowserEvent('onBeforeUpload', array($this, 'onBeforeUpload'));
            $this->addFileBrowserEvent('onUpload', array($this, 'onUpload'));

            if (JRequest::getCmd('action') === 'thumbnail') {
                WFToken::checkToken() or die('RESTRICTED');

                $file = JRequest::getVar('img', '', 'GET', 'PATH');

                // check file path
                WFUtility::checkPath($file);

                if ($file && preg_match('/\.(jpg|jpeg|png|gif|tiff|bmp)$/i', $file)) {
                    return $this->createCacheThumb(rawurldecode($file));
                }
            }
        }
        else {
            $request->setRequest(array($this, 'applyEdit'));
        }

        $request->setRequest(array($this, 'saveEdit'));
        $request->setRequest(array($this, 'cleanEditorTmp'));
    }

    /**
     * Display the plugin.
     */
    public function display()
    {
        $document = WFDocument::getInstance();

        $layout = JRequest::getCmd('layout', 'plugin');

        // Plugin
        if ($layout === 'plugin') {
            if ($this->get('can_edit_images')) {
                $request = WFRequest::getInstance();

                if ($this->getParam('editor.image_editor', 1)) {
                    $this->addFileBrowserButton('file', 'image_editor', array('action' => 'editImage', 'title' => WFText::_('WF_BUTTON_EDIT_IMAGE'), 'restrict' => 'jpg,jpeg,png,gif'));
                }
            }

            // get parent display data
            parent::display();

            // add pro scripts
            $document->addScript(array('widget'), 'pro');
            $document->addStyleSheet(array('manager'), 'pro');
        }

        // Image Editor
        if ($layout === 'editor') {
            if ($this->getParam('editor.image_editor', 1) == 0) {
                JError::raiseError(403, WFText::_('RESTRICTED'));
            }

            // cleanup tmp files
            $this->cleanEditorTmp();

            $view = $this->getView();

            $view->setLayout('editor');
            $view->addTemplatePath(WF_EDITOR_LIBRARIES . '/pro/views/editor/tmpl');

            $lists = array();

            $lists['resize'] = $this->getPresetsList('resize');
            $lists['crop'] = $this->getPresetsList('crop');

            $view->assign('lists', $lists);

            // get parent display data
            parent::display();

            // get UI Theme
            $theme = $this->getParam('editor.dialog_theme', 'jce');

            $document->addScript(array('webgl', 'filter', 'canvas', 'transform', 'editor'), 'pro');
            $document->addStyleSheet(array('editor.css'), 'pro');
            $document->addScriptDeclaration('jQuery(document).ready(function($){EditorDialog.init({"site" : "' . JURI::root() . '", "root" : "' . JURI::root(true) . '"})});');

            $document->setTitle(WFText::_('WF_MANAGER_IMAGE_EDITOR'));
        }
    }

    public function getPresetsList($type)
    {
        $list = array();

        switch ($type) {
            case 'resize' :
                $list = explode(',', $this->getParam('editor.resize_presets', '320x240,640x480,800x600,1024x768', '', 'string', false));
                break;
            case 'crop' :
                $list = explode(',', $this->getParam('editor.crop_presets', '4:3,16:9,20:30,320x240,240x320,640x480,480x640,800x600,1024x768', '', 'string', false));
                break;
        }

        return $list;
    }

    private function isFtp()
    {
        // Initialize variables
        jimport('joomla.client.helper');
        $FTPOptions = JClientHelper::getCredentials('ftp');

        return $FTPOptions['enabled'] == 1;
    }

    protected function getImageEditor()
    {
        static $editor;

        if (!is_object($editor)) {
            $editor = new WFImageEditor(array(
                'ftp' => $this->isFtp(),
                'edit' => $this->get('can_edit_images'),
                'prefer_imagick' => (bool)$this->getParam('editor.prefer_imagick', true),
                'remove_exif' => (bool)$this->getParam('editor.upload_remove_exif', false),
                'resample_image' => (bool)$this->getParam('editor.resample_image', true),
            ));
        }

        return $editor;
    }

    private static function convertIniValue($value)
    {
        $suffix = '';

        preg_match('#([0-9]+)\s?([a-z]+)#i', $value, $matches);

        // get unit
        if (isset($matches[2])) {
            $suffix = $matches[2];
        }
        // get value
        if (isset($matches[1])) {
            $value = (int)$matches[1];
        }

        // Convert to bytes
        switch (strtolower($suffix)) {
            case 'g' :
            case 'gb' :
                $value *= 1073741824;
                break;
            case 'm' :
            case 'mb' :
                $value *= 1048576;
                break;
            case 'k' :
            case 'kb' :
                $value *= 1024;
                break;
        }

        return (int)$value;
    }

    private static function checkMem($image)
    {
        $channels = ($image['mime'] == 'image/png') ? 4 : 3;

        if (function_exists('memory_get_usage')) {
            // try ini_get
            $limit = ini_get('memory_limit');

            // try get_cfg_var
            if (empty($limit)) {
                $limit = get_cfg_var('memory_limit');
            }

            // no limit set...
            if ($limit === '-1') {
                return true;
            }

            // can't get from ini, assume low value of 32M
            if (empty($limit)) {
                $limit = 32 * 1048576;
            } else {
                $limit = self::convertIniValue($limit);
            }

            // get memory used so far
            $used = memory_get_usage(true);

            return $image[0] * $image[1] * $channels * 1.7 < $limit - $used;
        }

        return true;
    }

    public function onBeforeUpload(&$file, &$dir, &$name)
    {
        $remove_exif = $this->getParam('editor.remove_exif', 0);

        // remove exif data
        if ($remove_exif && preg_match('#\.(jpg|jpeg|png)$#i', $file['name'])) {
            if ($this->removeExifData($file['tmp_name']) === false) {
                @unlink($file['tmp_name']);
                throw new InvalidArgumentException(WFText::_('WF_MANAGER_UPLOAD_EXIF_REMOVE_ERROR'));

                return false;
            }
        }
    }

    public function onUpload($file, $relative = '')
    {
        $browser = $this->getFileBrowser();
        $editor = $this->getImageEditor();

        // default values from parameters
        $resize = (int)$this->getParam('editor.upload_resize_state', 0);

        $resize_width = $this->getParam('editor.resize_width', '640');
        $resize_height = $this->getParam('editor.resize_height', '480');

        if (!is_array($resize_width)) {
            $resize_width = explode(',', (string)$resize_width);
        }

        if (!is_array($resize_height)) {
            $resize_height = explode(',', (string)$resize_height);
        }

        jimport('joomla.input.filter');

        // dialog/form upload
        if (JRequest::getInt('inline', 0) === 0) {
            $file_resize = false;

            // Resize options visible
            if ((bool)$this->getParam('editor.upload_resize', 1)) {
                $resize = JRequest::getInt('upload_resize_state', 0);

                foreach (array('resize_width', 'resize_height', 'file_resize_width', 'file_resize_height') as $var) {
                    $ $var = JRequest::getVar('upload_' . $var, '');
                    // pass each value through intval
                    $ $var = array_map('intval', $ $var);
                }

                $resize_suffix = JRequest::getVar('upload_resize_suffix', '');

                // check for individual resize values
                foreach (array_merge($file_resize_width, $file_resize_height) as $item) {
                    // at least one value set, so resize
                    if (!empty($item)) {
                        $file_resize = true;

                        break;
                    }
                }

                // transfer values
                if ($file_resize) {
                    $resize_width = $file_resize_width;
                    $resize_height = $file_resize_height;

                    $file_resize_suffix = JRequest::getVar('upload_file_resize_suffix', '');

                    for ($i = 1; $i < count($file_resize_suffix); $i++) {
                        $resize_suffix[i] = $file_resize_suffix[$i];
                    }

                    // set global resize option
                    $resize = true;
                }
            }
        }

        // should exif data be removed?
        $removeExif = (int)$this->getParam('editor.upload_remove_exif', 0) && preg_match('#\.(jpg|jpeg|png)$#i', $file);

        $dim = getimagesize($file);

        if ($dim) {
            $width = $dim[0];
            $height = $dim[1];

            $files = array($file);

            if ($resize) {
                $resize_quality = (int)$this->getParam('editor.resize_quality', 100);

                $count = max(count($resize_width), count($resize_height));

                for ($i = 0; $i < $count; ++$i) {
                    // need at least one value
                    if (!empty($resize_width[$i]) || !empty($resize_height[$i])) {
                        // calculate width if not set
                        if (empty($resize_width[$i])) {
                            $resize_width[$i] = round($resize_height[$i] / $height * $width, 0);
                        }

                        // calculate height if not set
                        if (empty($resize_height[$i])) {
                            $resize_height[$i] = round($resize_width[$i] / $width * $height, 0);
                        }

                        // get scale based on aspect ratio
                        $scale = ($width > $height) ? $resize_width[$i] / $width : $resize_height[$i] / $height;

                        if ($scale < 1) {
                            $destination = '';

                            $path = dirname($file);
                            $name   = WFUtility::getFilename($file);
                            $ext    = WFUtility::getExtension($file);

                            $suffix = '';

                            // create suffix based on width/height values for images after first
                            if (empty($resize_suffix[$i]) && $i > 0) {
                                $suffix = '_' . $resize_width[$i] . '_' . $resize_height[$i];
                            }
                            else {
                                // replace width and height variables
                                $suffix = str_replace(array('$width', '$height'), array($resize_width[$i], $resize_height[$i]), $resize_suffix[$i]);
                            }

                            $name .= $suffix . '.' . $ext;

                            // validate name
                            WFUtility::checkPath($name);

                            // create new destination
                            $destination = WFUtility::makePath($path, $name);

                            // no need to remove exif data on successful resize
                            if ($editor->resize($file, $destination, $resize_width[$i], $resize_height[$i], $resize_quality)) {
                                $removeExif = false;

                                if ($file !== $destination) {
                                    $files[] = $destination;
                                }

                            }
                            else {
                                $browser->setResult(WFText::_('WF_MANAGER_RESIZE_ERROR'), 'error');
                            }
                        }
                    }
                }
            }

            // default parameter option
            $watermark = $this->getParam('editor.upload_watermark_state', 0);

            // option visible so allow user set value
            if ((bool)$this->getParam('editor.upload_watermark', 0)) {
                $watermark = JRequest::getInt('upload_watermark_state', 0);
            }

            if ($watermark) {
                $font_style = $this->getParam('watermark_font_style', 'LiberationSans-Regular.ttf');
                // default LiberationSans fonts
                if (preg_match('#^LiberationSans-(Regular|Bold|BoldItalic|Italic)\.ttf$#', $font_style)) {
                    $font_style = WF_EDITOR_LIBRARIES . '/pro/fonts/' . $font_style;
                    // custom font


                }
                else {
                    $font_style = JPATH_SITE . '/' . trim(preg_replace('#[\/\\\\]+#', '/', $font_style), '/');
                }

                $options = array(
                    'type' => $this->getParam('editor.watermark_type', 'text'),
                    'text' => $this->getParam('editor.watermark_text', ''),
                    'image' => $this->getParam('editor.watermark_image', ''),
                    'font_style' => str_replace(JPATH_SITE, '', $font_style),
                    'font_size' => $this->getParam('editor.watermark_font_size', '32'),
                    'font_color' => $this->getParam('editor.watermark_font_color', '#FFFFFF'),
                    'opacity' => $this->getParam('editor.watermark_opacity', 50),
                    'position' => $this->getParam('editor.watermark_position', 'center'),
                    'margin' => $this->getParam('editor.watermark_margin', 10),
                    'angle' => $this->getParam('editor.watermark_angle', 0),
                );

                foreach ($files as $file) {
                    // no need to remove exif on successful watermark
                    if ($editor->watermark($file, $options)) {
                        $removeExif = false;
                    }
                    else {
                        $browser->setResult(WFText::_('WF_MANAGER_WATERMARK_ERROR'), 'error');
                    }
                }
            }

            $thumbnail = $this->getParam('upload_thumbnail_state', 0);

            $tw = (int)$this->getParam('thumbnail_width', 120);
            $th = (int)$this->getParam('thumbnail_height', 90);

            $crop = $this->getParam('upload_thumbnail_crop', 0);
            
            // Thumbnail options visible
            if ((bool)$this->getParam('upload_thumbnail', 1)) {
                $thumbnail = JRequest::getInt('upload_thumbnail_state', 0);

                $tw = (int)JRequest::getInt('upload_thumbnail_width');
                $th = (int)JRequest::getInt('upload_thumbnail_height');
            
                // Crop Thumbnail
                $crop = JRequest::getInt('upload_thumbnail_crop', 0);
            }

            if ($thumbnail) {
                $dim = @getimagesize($file);
                $tq = $this->getParam('upload_thumbnail_quality', 80, false);
            
                // need at least one value
                if ($tw || $th) {
                    // calculate width if not set
                    if (!$tw) {
                        $tw = round($th / $dim[1] * $dim[0], 0);
                    }
                    // calculate height if not set
                    if (!$th) {
                        $th = round($tw / $dim[0] * $dim[1], 0);
                    }
                
                    // Make relative
                    $source = str_replace($browser->getBaseDir(), '', $file);

                    $coords = array(
                        'sx' => null,
                        'sy' => null,
                        'sw' => null,
                        'sh' => null,
                    );

                    if ($crop) {
                        $coords = $this->cropThumbnail($dim[0], $dim[1], $tw, $th);
                    }

                    if (!$this->createThumbnail($source, $tw, $th, $tq, $coords['sx'], $coords['sy'], $coords['sw'], $coords['sh'], true)) {
                        $browser->setResult(WFText::_('WF_IMGMANAGER_EXT_THUMBNAIL_ERROR'), 'error');
                    }
                }
            }

            if ($removeExif) {
                // remove exif data
                if ($this->removeExifData($file) === false) {
                    @unlink($file);
                    $browser->setResult(WFText::_('WF_MANAGER_UPLOAD_EXIF_REMOVE_ERROR'), 'error');
                }
            }
        }

        return $browser->getResult();
    }

    private function toRelative($file)
    {
        return WFUtility::makePath(str_replace(JPATH_ROOT . '/', '', dirname(JPath::clean($file))), basename($file));
    }

    public function cleanEditorTmp($file = null, $exit = true)
    {
        // Check for request forgeries
        WFToken::checkToken() or die('Access to this resource is restricted');

        // check for image editor access
        if ($this->checkAccess('image_editor', 1) === false) {
            JError::raiseError(403, 'Access to this resource is restricted');
        }

        if ($file) {
            $ext = WFUtility::getExtension($file);

            // create temp file
            $tmp = 'wf_ie_' . md5($file) . '.' . $ext;
            $path = WFUtility::makePath($this->getCacheDirectory(), $tmp);

            self::validateImagePath($file);

            $result = false;

            if (is_file($path)) {
                $result = @JFile::delete($path);
            }

            if ($exit) {
                return (bool)$result;
            }
        }
        else {
            $files = JFolder::files($this->getCacheDirectory(), '^(wf_ie_)([a-z0-9]+)\.(jpg|jpeg|gif|png)$');

            if (count($files)) {
                $time = strtotime('24 hours ago');
                clearstatcache();
                foreach ($files as $file) {
                    // delete files older than 24 hours
                    if (@filemtime($file) >= $time) {
                        @JFile::delete($file);
                    }
                }
            }
        }

        return true;
    }

    public function applyEdit($file, $task, $value)
    {
        // Check for request forgeries
        WFToken::checkToken() or die('Access to this resource is restricted');

        // check for image editor access
        if ($this->checkAccess('image_editor', 1) === false) {
            JError::raiseError(403, 'Access to this resource is restricted');
        }

        $browser = $this->getFileBrowser();

        // check file
        self::validateImagePath($file);

        $upload = JRequest::getVar('file', '', 'files', 'array');

        // create a filesystem result object
        $result = new WFFileSystemResult();

        if (isset($upload) && isset($upload['tmp_name']) && is_uploaded_file($upload['tmp_name'])) {
            self::validateImageFile($upload);

            $ext = WFUtility::getExtension($file);

            // create temp file
            $tmp = 'wf_ie_' . md5($file) . '.' . $ext;
            $tmp = WFUtility::makePath($this->getCacheDirectory(), $tmp);

            // delete existing tmp file
            if (is_file($tmp)) {
                @JFile::delete($tmp);
            }

            // load image class
            require_once __DIR__ . '/image/image.php';
            // create image
            $image = new WFImage($upload['tmp_name'], array('preferImagick' => $this->getParam('editor.prefer_imagick', true)));

            switch ($task) {
                case 'resize' :
                    $image->resize($value->width, $value->height);
                    break;
                case 'crop' :
                    $image->crop($value->width, $value->height, $value->x, $value->y, false, 1);
                    break;
            }
            // get image data
            $data = $image->toString($ext);

            // write to file
            if ($data) {
                $result->state = (bool)@JFile::write($tmp, $data);
            }

            if ($result->state === true) {
                $tmp = str_replace(WFUtility::cleanPath(JPATH_SITE), '', $tmp);
                $browser->setResult(WFUtility::cleanPath($tmp, '/'), 'files');
            }
            else {
                $browser->setResult(WFText::_('WF_IMAGE_EDIT_APPLY_ERROR'), 'error');
            }

            @unlink($upload['tmp_name']);

            return $browser->getResult();
        }
    }

    public function saveEdit($file, $name, $options = array(), $quality = 100)
    {
        // Check for request forgeries
        WFToken::checkToken() or die('Access to this resource is restricted');

        // check for image editor access
        if ($this->checkAccess('image_editor', 1) === false) {
            JError::raiseError(403, 'Access to this resource is restricted');
        }

        $browser = $this->getFileBrowser();
        $filesystem = $browser->getFileSystem();

        // check file
        self::validateImagePath($file);

        // clean temp
        $this->cleanEditorTmp($file, false);

        // check new name
        self::validateImagePath($name);

        $upload = JRequest::getVar('file', '', 'files', 'array');

        // create a filesystem result object
        $result = new WFFileSystemResult();

        if (isset($upload) && isset($upload['tmp_name']) && is_uploaded_file($upload['tmp_name'])) {
            $tmp = $upload['tmp_name'];

            self::validateImageFile($upload);
            $result = $filesystem->upload('multipart', trim($tmp), dirname($file), $name);

            @unlink($tmp);
        }
        else {
            // set upload as false - JSON request
            $upload = false;

            $file = WFUtility::makePath($filesystem->getBaseDir(), $file);
            $dest = dirname($file) . '/' . basename($name);

            // get extension
            $ext = WFUtility::getExtension($dest);

            // load image class
            require_once __DIR__ . '/image/image.php';
            // create image
            $image = new WFImage($file, array(
                'preferImagick' => $this->getParam('editor.prefer_imagick', true),
                'resampleImage' => $this->getParam('editor.resample_image', true),
            ));

            foreach ($options as $filter) {
                if (isset($filter->task)) {
                    $args = isset($filter->args) ? (array)$filter->args : array();

                    switch ($filter->task) {
                        case 'resize' :
                            $w = $args[0];
                            $h = $args[1];

                            $image->resize($w, $h);
                            break;
                        case 'crop' :
                            $w = $args[0];
                            $h = $args[1];

                            $x = $args[2];
                            $y = $args[3];

                            $image->crop($w, $h, $x, $y);
                            break;
                        case 'rotate' :
                            $image->rotate(array_shift($args));
                            break;
                        case 'flip' :
                            $image->flip(array_shift($args));
                            break;
                        default :
                            $image->filter($filter->task, $args);
                            break;
                    }
                }
            }

            // get image data
            $data = $image->toString($ext);
            // write to file
            if ($data) {
                $result->state = (bool)@JFile::write($dest, $data);
            }
            // set path
            $result->path = $dest;
        }

        if ($result->state === true) {
            // check if its a valid image
            if (@getimagesize($result->path) === false) {
                JFile::delete($result->path);
                throw new InvalidArgumentException('Invalid image file');
            }
            else {
                $result->path = str_replace(WFUtility::cleanPath(JPATH_SITE), '', $result->path);
                $browser->setResult(WFUtility::cleanPath($result->path, '/'), 'files');
            }
        }
        else {
            $browser->setResult($result->message || WFText::_('WF_MANAGER_EDIT_SAVE_ERROR'), 'error');
        }
        // return to WFRequest
        return $browser->getResult();
    }

    protected function cropThumbnail($sw, $sh, $dw, $dh)
    {
        $sx = 0;
        $sy = 0;
        $w = $dw;
        $h = $dh;

        if ($w / $h > $sw / $w) {
            $h = $h * ($sw / $w);
            $w = $sw;
            if ($h > $sh) {
                $w = $w * ($sh / $h);
                $h = $sh;
            }
        }
        else {
            $w = $w * ($sh / $h);
            $h = $sh;
            if ($w > $sw) {
                $h = $h * ($sw / $w);
                $w = $sw;
            }
        }

        if ($w < $sw) {
            $sx = floor( ($sw - $w) / 2);
        }
        else {
            $sx = 0;
        }

        if ($h < $sh) {
            $sy = floor( ($sh - $h) / 2);
        }
        else {
            $sy = 0;
        }

        return array('sx' => $sx, 'sy' => $sy, 'sw' => $w, 'sh' => $h);
    }

    private function getCacheDirectory()
    {
        $app = JFactory::getApplication();

        jimport('joomla.filesystem.folder');

        $cache = $app->getCfg('tmp_path');
        $dir = $this->getParam('editor.cache', $cache);

        // make sure a value is set
        if (empty($dir)) {
            $dir = 'tmp';
        }
        // check for and create absolute path
        if (strpos($dir, JPATH_SITE) === false) {
            $dir = WFUtility::makePath(JPATH_SITE, JPath::clean($dir));
        }

        if (!JFolder::exists($dir)) {
            if (@JFolder::create($dir)) {
                return $dir;
            }
        }

        return $dir;
    }

    private function cleanCacheDir()
    {
        jimport('joomla.filesystem.folder');
        jimport('joomla.filesystem.file');

        $cache_max_size = intval($this->getParam('editor.cache_size', 10, false)) * 1024 * 1024;
        $cache_max_age = intval($this->getParam('editor.cache_age', 30, false)) * 86400;
        $cache_max_files = intval($this->getParam('editor.cache_files', 0, false));

        if ($cache_max_age > 0 || $cache_max_size > 0 || $cache_max_files > 0) {
            $path = $this->getCacheDirectory();
            $files = JFolder::files($path, '^(wf_thumb_cache_)([a-z0-9]+)\.(jpg|jpeg|gif|png)$');
            $num = count($files);
            $size = 0;
            $cutofftime = time() - 3600;

            if ($num) {
                foreach ($files as $file) {
                    $file = WFUtility::makePath($path, $file);
                    if (is_file($file)) {
                        $ftime = @fileatime($file);
                        $fsize = @filesize($file);
                        if ($fsize == 0 && $ftime < $cutofftime) {
                            @JFile::delete($file);
                        }
                        if ($cache_max_files > 0) {
                            if ($num > $cache_max_files) {
                                @JFile::delete($file);
                                --$num;
                            }
                        }
                        if ($cache_max_age > 0) {
                            if ($ftime < (time() - $cache_max_age)) {
                                @JFile::delete($file);
                            }
                        }
                        if ($cache_max_files > 0) {
                            if ( ($size + $fsize) > $cache_max_size) {
                                @JFile::delete($file);
                            }
                        }
                    }
                }
            }
        }

        return true;
    }

    private function redirectThumb($file, $mime)
    {
        if (is_file($file)) {
            header('Content-length: ' . filesize($file));
            header('Content-type: ' . $mime);
            header('Location: ' . $this->toRelative($file));
        }
    }

    private function outputImage($file, $mime)
    {
        if (is_file($file)) {
            header('Content-length: ' . filesize($file));
            header('Content-type: ' . $mime);
            ob_clean();
            flush();

            @readfile($file);
        }

        exit();
    }

    private function getCacheThumbPath($file, $width, $height)
    {
        jimport('joomla.filesystem.file');

        $mtime = @filemtime($file);
        $thumb = 'wf_thumb_cache_' . md5(basename(JFile::stripExt($file)) . $mtime . $width . $height) . '.' . JFile::getExt($file);

        return WFUtility::makePath($this->getCacheDirectory(), $thumb);
    }

    private function createCacheThumb($file)
    {
        jimport('joomla.filesystem.file');

        $browser = $this->getFileBrowser();
        $editor = $this->getImageEditor();

        // check path
        WFUtility::checkPath($file);

        $file = WFUtility::makePath($browser->getBaseDir(), $file);

        // default for list thumbnails
        $width = 100;
        $height = 100;
        $quality = 75;

        $data = @getimagesize($file);
        $mime = $data['mime'];

        if ( ($data[0] < $width && $data[1] < $height)) {
            return $this->outputImage($file, $mime);
        }

        // try exif thumbnail
        if ($mime == 'image/jpeg' || $mime == 'image/tiff') {
            $exif = exif_thumbnail($file, $width, $height, $type);
            if ($exif !== false) {
                header('Content-type: ' . image_type_to_mime_type($type));
                die($exif);
            }
        }

        $thumb = $this->getCacheThumbPath($file, $width, $height);

        if (JFile::exists($thumb)) {
            return $this->outputImage($thumb, $mime);
        }

        $coords = $this->cropThumbnail($data[0], $data[1], $width, $height);

        if (self::checkMem($data)) {
            if ($editor->resize($file, $thumb, $width, $height, $quality, $coords['sx'], $coords['sy'], $coords['sw'], $coords['sh'])) {
                if (JFile::exists($thumb)) {
                    return $this->outputImage($thumb, $mime);
                }
            }
        }
        // exit with no data
        exit();
    }

    public function getThumbnails($files)
    {
        $browser = $this->getFileBrowser();

        jimport('joomla.filesystem.file');

        $thumbnails = array();
        foreach ($files as $file) {
            $thumbnails[$file['name']] = $this->getCacheThumb(WFUtility::makePath($browser->getBaseDir(), $file['url']), true, 50, 50, JFile::getExt($file['name']), 50);
        }

        return $thumbnails;
    }

    protected static function validateImageFile($file)
    {
        return WFUtility::isSafeFile($file);
    }

    /**
     * Validate an image path and extension.
     *
     * @param type $path Image path
     *
     * @throws InvalidArgumentException
     */
    protected static function validateImagePath($path)
    {
        // nothing to validate
        if (empty($path)) {
            return false;
        }

        // clean path
        $path = WFUtility::cleanPath($path);
        // check file path
        WFUtility::checkPath($path);
        // check file name and contents
        WFUtility::validateFileName($path);
    }

    /**
     * Remove exif data from an image by rewriting it. This will also rotate images to correct orientation.
     *
     * @param $image
     *
     * @return bool
     */
    private function removeExifData($image)
    {
        $exif = null;

        // check if exif_read_data disabled...
        if (function_exists('exif_read_data')) {
            // get exif data
            $exif = exif_read_data($image);
            $rotate = 0;

            if (!empty($exif['Orientation'])) {
                $orientation = (int)$exif['Orientation'];

                // Fix Orientation
                switch ($orientation) {
                    case 3 :
                        $rotate = 180;
                        break;
                    case 6 :
                        $rotate = 90;
                        break;
                    case 8 :
                        $rotate = 270;
                        break;
                }
            }
        }

        if (extension_loaded('imagick')) {
            try {
                $img = new Imagick($image);

                if ($rotate) {
                    $img->rotateImage(new ImagickPixel(), $rotate);
                }

                // get iptcc
                //$iptcc = $img->getImageProfile('iptcc');

                $img->stripImage();

                // add back iptcc
                /*if (!empty($iptcc)) {
                $img->profileImage($iptcc);
                }*/

                $img->writeImage($image);
                $img->clear();
                $img->destroy();

                return true;
            } catch (Exception $e) {
            }
        }
        elseif (extension_loaded('gd')) {
            try {
                $info = getimagesize($image);

                if (!empty($info)) {
                    if ($info[2] === IMAGETYPE_JPEG) {
                        $handle = imagecreatefromjpeg($image);

                        if (is_resource($handle)) {
                            if ($rotate) {
                                $handle = imagerotate($handle, $rotate, -1);
                            }

                            imagejpeg($handle, $image);
                            @imagedestroy($handle);

                            return true;
                        }
                    }

                    if ($info[2] === IMAGETYPE_PNG) {
                        $handle = imagecreatefrompng($image);

                        if (is_resource($handle)) {
                            if ($rotate) {
                                $handle = imagerotate($handle, $rotate, -1);
                            }

                            // Allow transparency for the new image handle.
                            imagealphablending($handle, false);
                            imagesavealpha($handle, true);

                            imagepng($handle, $image);
                            @imagedestroy($handle);

                            return true;
                        }
                    }
                }
            } catch (Exception $e) {
            }
        }

        return false;
    }

    /**
     * Get an image's thumbnail file name.
     *
     * @param string $file the full path to the image file
     *
     * @return string of the thumbnail file
     */
    protected function getThumbName($file)
    {
        $ext = WFUtility::getExtension($file);
        $string = $this->getParam($this->getName() . '.thumbnail_prefix', $this->getParam('editor.thumbnail_prefix', 'thumb_$'));

        if (strpos($string, '$') !== false) {
            return str_replace('$', basename($file, '.' . $ext), $string) . '.' . $ext;
        }

        return $string . basename($file);
    }

    protected function getThumbDir($file, $create)
    {
        $browser = $this->getFileBrowser();
        $filesystem = $browser->getFileSystem();

        $folder = $this->getParam($this->getName() . '.thumbnail_folder', $this->getParam('editor.thumbnail_folder', 'thumbnails'));

        $dir = WFUtility::makePath(dirname($file), $folder);

        if ($create && !$filesystem->exists($dir)) {
            $filesystem->createFolder(dirname($dir), basename($dir));
        }

        return $dir;
    }

    /**
     * Create a thumbnail.
     *
     * @param string $file    relative path of the image
     * @param string $width   thumbnail width
     * @param string $height  thumbnail height
     * @param string $quality thumbnail quality (%)
     * @param string $mode    thumbnail mode
     */
    public function createThumbnail($file, $width = null, $height = null, $quality = 100, $sx = null, $sy = null, $sw = null, $sh = null)
    {
        // check path
        self::validateImagePath($file);

        $browser = $this->getFileBrowser();
        $editor = $this->getImageEditor();

        $thumb = WFUtility::makePath($this->getThumbDir($file, true), $this->getThumbName($file));

        $path = WFUtility::makePath($browser->getBaseDir(), $file);
        $thumb = WFUtility::makePath($browser->getBaseDir(), $thumb);

        if (!$editor->resize($path, $thumb, $width, $height, $quality, $sx, $sy, $sw, $sh)) {
            $browser->setResult(WFText::_('WF_IMGMANAGER_EXT_THUMBNAIL_ERROR'), 'error');
        }

        return $browser->getResult();
    }

    protected function getFileBrowserConfig($config = array())
    {
        $data = array(
            'view_mode' => $this->getParam('editor.mode', 'list'),
            'can_edit_images' => $this->get('can_edit_images'),
            'cache_enable' => $this->getParam('editor.cache_enable', 0),
            // Upload
            'upload_resize' => $this->getParam('editor.upload_resize', 1),
            'upload_resize_state' => $this->getParam('editor.upload_resize_state', 0),
            'upload_resize_width' => (string)$this->getParam('editor.resize_width', '640'),
            'upload_resize_height' => (string)$this->getParam('editor.resize_height', '480'),
            'upload_resize_quality' => $this->getParam('editor.resize_quality', 100),
            'upload_watermark' => $this->getParam('editor.upload_watermark', 0),
            'upload_watermark_state' => $this->getParam('editor.upload_watermark_state', 0),
            'upload_thumbnail' => $this->getParam('upload_thumbnail', 1),
            'upload_thumbnail_state' => $this->getParam('upload_thumbnail_state', 0),
            'upload_thumbnail_crop' => $this->getParam('upload_thumbnail_crop', 0),
            'upload_thumbnail_width' => $this->getParam('upload_thumbnail_width', 120),
            'upload_thumbnail_height' => $this->getParam('upload_thumbnail_height', 90),
            'upload_thumbnail_quality' => $this->getParam('upload_thumbnail_quality', 80)
        );

        $config = WFUtility::array_merge_recursive_distinct($data, $config);

        return parent::getFileBrowserConfig($config);
    }
}
