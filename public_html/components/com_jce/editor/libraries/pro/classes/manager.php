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

require_once WF_EDITOR_LIBRARIES . '/classes/manager/base.php';

JLoader::register('WFImage', WF_EDITOR_LIBRARIES . '/pro/classes/image/image.php');

class WFMediaManager extends WFMediaManagerBase
{
    public $can_edit_images = 0;

    public $show_view_mode = 0;
    
    protected $exifCache = array();

    public function __construct($config = array())
    {
        parent::__construct($config);

        $app = JFactory::getApplication();

        $request = WFRequest::getInstance();
        $layout = $app->input->getCmd('layout', 'plugin');

        if ($layout === 'plugin') {
            $this->addFileBrowserEvent('onBeforeUpload', array($this, 'onBeforeUpload'));
            $this->addFileBrowserEvent('onUpload', array($this, 'onUpload'));

            if ($app->input->getCmd('action') === 'thumbnail') {
                JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

                $file = $app->input->get('img', '', 'STRING');

                // check file path
                WFUtility::checkPath($file);

                // clean path
                $file = WFUtility::makeSafe($file);

                if ($file && preg_match('/\.(jpg|jpeg|png|gif|tiff|bmp)$/i', $file)) {
                    return $this->createCacheThumb(rawurldecode($file));
                }
            }
        } else {
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

        $layout = JFactory::getApplication()->input->getCmd('layout', 'plugin');

        // Plugin
        if ($layout === 'plugin') {
            if ($this->get('can_edit_images')) {
                $request = WFRequest::getInstance();

                if ($this->getParam('editor.image_editor', 1)) {
                    $this->addFileBrowserButton('file', 'image_editor', array('action' => 'editImage', 'title' => JText::_('WF_BUTTON_EDIT_IMAGE'), 'restrict' => 'jpg,jpeg,png,gif'));
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
                throw new Exception(JText::_('JERROR_ALERTNOAUTHOR'));
            }

            // cleanup tmp files
            $this->cleanTempDirectory();

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

            $document->setTitle(JText::_('WF_MANAGER_IMAGE_EDITOR'));
        }
    }

    public function getPresetsList($type)
    {
        $list = array();

        switch ($type) {
            case 'resize':
                $list = $this->getParam('editor.resize_presets', '320x240,640x480,800x600,1024x768');

                if (is_string($list)) {
                    $list = explode(',', $list);
                }

                break;
            case 'crop':
                $list = $this->getParam('editor.crop_presets', '4:3,16:9,20:30,320x240,240x320,640x480,480x640,800x600,1024x768');

                if (is_string($list)) {
                    $list = explode(',', $list);
                }

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
            $value = (int) $matches[1];
        }

        // Convert to bytes
        switch (strtolower($suffix)) {
            case 'g':
            case 'gb':
                $value *= 1073741824;
                break;
            case 'm':
            case 'mb':
                $value *= 1048576;
                break;
            case 'k':
            case 'kb':
                $value *= 1024;
                break;
        }

        return (int) $value;
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

    /**
     * Get and temporarily store the exif data of an image
     *
     * @param [String] $file The aboslute path to the image
     * @param [String] $key The key to store the data under
     * @return void
     */
    protected function getExifData($file, $key = null)
    {
        // use file name as key
        if (empty($key)) {
            $key = $file;
        }

        if (array_key_exists($key, $this->exifCache)) {
            return $this->exifCache[$key];
        }
        
        $exif = null;
        
        if (!function_exists('exif_read_data') || !is_file($file)) {
            return $exif;
        }

        $exif = @exif_read_data($file);

        if ($exif && is_array($exif) && array_key_exists('EXIF', $exif)) {
            $this->exifCache[$key] = $exif;
        }

        return $exif;
    }

    public function onBeforeUpload(&$file, &$dir, &$name)
    {
        // check for and reset image orientation
        if (preg_match('#\.(jpg|jpeg)$#i', $file['name'])) {

            // store exif data
            $exif = $this->getExifData($file['tmp_name'], $file['name']);

            $remove_exif = (bool) $this->getParam('editor.upload_remove_exif', false);

            // data exists and we are allowed to remove it
            if ($exif && $remove_exif) {
                if (false == $this->removeExifData($file['tmp_name'])) {
                    throw new InvalidArgumentException(JText::_('WF_MANAGER_UPLOAD_EXIF_REMOVE_ERROR'));
                }
            }
        }
    }

    protected function getImageLab($file)
    {
        static $instance = array();

        if (!isset($instance[$file])) {
            $browser = $this->getFileBrowser();
            $filesystem = $browser->getFileSystem();

            if (!$filesystem->is_file($file)) {
                return false;
            }

            // get the image as data
            $data = $filesystem->read($file);

            if (!$data) {
                return null;
            }

            try {
                $image = new WFImage(null, array(
                    'preferImagick' => (bool) $this->getParam('editor.prefer_imagick', true),
                    'removeExif' => (bool) $this->getParam('editor.upload_remove_exif', false),
                    'resampleImage' => (bool) $this->getParam('editor.resample_image', false),
                ));

                $image->loadString($data);

                // get extension
                $extension = WFUtility::getExtension($file);

                // set image type
                $image->setType($extension);

                // correct orientation
                $image->orientate();

                // create backup of original image resource
                $image->backup();

                // store instance
                $instance[$file] = $image;

            } catch (Exception $e) {
                $instance[$file] = null;

                $browser->setResult($e->getMessage(), 'error');
            }
        }

        return $instance[$file];
    }

    protected function resizeUploadImage($file, &$cache)
    {
        $app = JFactory::getApplication();

        $browser = $this->getFileBrowser();
        $filesystem = $browser->getFileSystem();

        // resize state
        $resize = (int) $browser->get('upload_resize_state');

        // resize crop
        $upload_resize_crop = (int) $browser->get('upload_resize_crop');

        // get parameter values, allow empty but fallback to system default
        $resize_width = $browser->get('upload_resize_width');
        $resize_height = $browser->get('upload_resize_height');

        // both values cannot be empty
        if (empty($resize_width) && empty($resize_height)) {
            $resize_width = 640;
            $resize_height = 480;
        }

        if (!is_array($resize_width)) {
            $resize_width = explode(',', (string) $resize_width);
        }

        if (!is_array($resize_height)) {
            $resize_height = explode(',', (string) $resize_height);
        }

        // create array of integer value
        $resize_crop = array($upload_resize_crop);

        // dialog/form upload
        if ($app->input->getInt('inline', 0) === 0) {
            $file_resize = false;

            // Resize options visible
            if ((bool) $browser->get('upload_resize')) {
                $resize = $app->input->getInt('upload_resize_state', 0);

                // set empty default values
                $file_resize_width = array();
                $file_resize_height = array();
                $file_resize_crop = array();

                foreach (array('resize_width', 'resize_height', 'resize_crop', 'file_resize_width', 'file_resize_height', 'file_resize_crop') as $var) {
                    $$var = $app->input->get('upload_' . $var, array(), 'array');
                    // pass each value through intval
                    $$var = array_map('intval', $$var);
                }

                $resize_suffix = $app->input->get('upload_resize_suffix', array(), 'array');

                // clean suffix
                $resize_suffix = WFUtility::makeSafe($resize_suffix);

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
                    $resize_crop = $file_resize_crop;

                    // get file resize suffix
                    $file_resize_suffix = $app->input->get('upload_file_resize_suffix', array(), 'array');

                    // clean suffix
                    $file_resize_suffix = WFUtility::makeSafe($file_resize_suffix);

                    // transfer values
                    $resize_suffix = $file_resize_suffix;

                    // set global resize option
                    $resize = true;
                }
            }
        }

        // no resizing, return empty array
        if (!$resize) {
            return false;
        }

        // get imagelab instance
        $instance = $this->getImageLab($file);

        // no instance was created, perhaps due to memory error?
        if (!$instance) {
            $browser->setResult(JText::_('WF_MANAGER_RESIZE_ERROR'), 'error');
            return false;
        }

        // get width
        $width = $instance->getWidth();

        // get height
        $height = $instance->getHeight();

        $image_quality = (int) $browser->get('upload_resize_quality', 100);

        $count = max(count($resize_width), count($resize_height));

        // get file extension
        $extension = WFUtility::getExtension($file);

        for ($i = 0; $i < $count; $i++) {
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

                    // get file path
                    $path = WFUtility::mb_dirname($file);

                    // get file name
                    $name = WFUtility::mb_basename($file);

                    // remove file extension
                    $name = WFUtility::stripExtension($name);

                    if (!isset($resize_crop[$i])) {
                        $resize_crop[$i] = $upload_resize_crop;
                    }

                    $suffix = '';

                    if (empty($resize_suffix[$i])) {
                        $resize_suffix[$i] = '';
                    }

                    // create suffix based on width/height values for images after first
                    if (empty($resize_suffix[$i]) && $i > 0) {
                        $suffix = '_' . $resize_width[$i] . '_' . $resize_height[$i];
                    } else {
                        // replace width and height variables
                        $suffix = str_replace(array('$width', '$height'), array($resize_width[$i], $resize_height[$i]), $resize_suffix[$i]);
                    }

                    $name .= $suffix . '.' . $extension;

                    // validate name
                    WFUtility::checkPath($name);

                    // create new destination
                    $destination = WFUtility::makePath($path, $name);

                    if ($resize_crop[$i]) {
                        $instance->fit($resize_width[$i], $resize_height[$i]);
                    } else {
                        $instance->resize($resize_width[$i], $resize_height[$i]);
                    }

                    $data = $instance->toString($extension, array('quality' => $image_quality));

                    // write to file
                    if ($data && $filesystem->write($destination, $data)) {
                        $cache[$destination] = $data;
                    } else {
                        $browser->setResult(JText::_('WF_MANAGER_RESIZE_ERROR'), 'error');
                    }

                    // restore backup resource for the next resize process
                    $instance->restore();
                }
            }
        }

        return true;
    }

    protected function watermarkUploadImage($file, &$cache)
    {
        $app = JFactory::getApplication();

        $browser = $this->getFileBrowser();
        $filesystem = $browser->getFileSystem();

        // get extension
        $extension = WFUtility::getExtension($file);

        // watermark state
        $watermark = (int) $browser->get('upload_watermark_state');

        // option visible so allow user set value
        if ((bool) $browser->get('upload_watermark')) {
            $watermark = $app->input->getInt('upload_watermark_state', 0);
        }

        // no watermark, return false
        if (!$watermark) {
            return false;
        }

        // get imagelab instance
        $instance = $this->getImageLab($file);

        // no instance was created, perhaps due to memory error?
        if (!$instance) {
            $browser->setResult(JText::_('WF_MANAGER_WATERMARK_ERROR'), 'error');
            return false;
        }

        // if the files array is empty, no resizing was done, create a new one for further processing
        if (empty($cache)) {
            $cache = array(
                $file => '',
            );
        }

        $font_style = $this->getParam('watermark_font_style', 'LiberationSans-Regular.ttf');

        // default LiberationSans fonts
        if (preg_match('#^LiberationSans-(Regular|Bold|BoldItalic|Italic)\.ttf$#', $font_style)) {
            $font_style = WFUtility::makePath(WF_EDITOR_LIBRARIES, '/pro/fonts/' . $font_style);
            // custom font
        } else {
            $font_style = WFUtility::makePath(JPATH_SITE, $font_style);
        }

        $watermark_image = $this->getParam('editor.watermark_image', '');

        if ($watermark_image) {
            $watermark_image = WFUtility::makePath(JPATH_SITE, $watermark_image);
        }

        $options = array(
            'type' => $this->getParam('editor.watermark_type', 'text'),
            'text' => $this->getParam('editor.watermark_text', ''),
            'image' => $watermark_image,
            'font_style' => $font_style,
            'font_size' => $this->getParam('editor.watermark_font_size', '32'),
            'font_color' => $this->getParam('editor.watermark_font_color', '#FFFFFF'),
            'opacity' => $this->getParam('editor.watermark_opacity', 50),
            'position' => $this->getParam('editor.watermark_position', 'center'),
            'margin' => $this->getParam('editor.watermark_margin', 10),
            'angle' => $this->getParam('editor.watermark_angle', 0),
        );

        // should image quality be set?
        $upload_quality = (int) $this->getParam('editor.upload_quality', 100);

        // watermark
        foreach ($cache as $destination => $data) {
            // load processed data if available
            if ($data) {
                $instance->loadString($data);
            }

            $instance->watermark($options);

            $data = $instance->toString($extension, array('quality' => $upload_quality));

            // valid data string
            if ($data) {
                // write to file and update cache
                if ($filesystem->write($destination, $data)) {
                    $cache[$destination] = $data;
                } else {
                    $browser->setResult(JText::_('WF_MANAGER_WATERMARK_ERROR'), 'error');
                }
            }

            // restore backup resource
            $instance->restore();
        }

        return true;
    }

    protected function thumbnailUploadImage($file, &$cache)
    {
        $app = JFactory::getApplication();

        $browser = $this->getFileBrowser();
        $filesystem = $browser->getFileSystem();

        // get extension
        $extension = WFUtility::getExtension($file);

        $thumbnail = (int) $browser->get('upload_thumbnail_state');

        // get parameter values, allow empty but fallback to system default
        $tw = $browser->get('upload_thumbnail_width');
        $th = $browser->get('upload_thumbnail_height');

        // both values cannot be empty
        if (empty($tw) && empty($th)) {
            $tw = 120;
            $th = 90;
        }

        $crop = $browser->get('upload_thumbnail_crop');

        // Thumbnail options visible
        if ((bool) $browser->get('upload_thumbnail')) {
            $thumbnail = $app->input->getInt('upload_thumbnail_state', 0);

            $tw = $app->input->getInt('upload_thumbnail_width');
            $th = $app->input->getInt('upload_thumbnail_height');

            // Crop Thumbnail
            $crop = $app->input->getInt('upload_thumbnail_crop', 0);
        }

        // not activated
        if (!$thumbnail) {
            return false;
        }

        $tq = $browser->get('upload_thumbnail_quality');

        // cast values to integer
        $tw = (int) $tw;
        $th = (int) $th;

        // need at least one value
        if ($tw || $th) {

            // get imagelab instance
            $instance = $this->getImageLab($file);

            // no instance was created, perhaps due to memory error?
            if (!$instance) {
                $browser->setResult(JText::_('WF_IMGMANAGER_EXT_THUMBNAIL_ERROR'), 'error');
                return false;
            }

            // if the files array is empty, no other processing was done, create a new one for further processing
            if (empty($cache)) {
                $cache = array(
                    $file => '',
                );
            }

            foreach ($cache as $destination => $data) {
                // if image data is available, load it
                if ($data) {
                    $instance->loadString($data);
                }

                $thumb = WFUtility::makePath($this->getThumbDir($destination, true), $this->getThumbName($destination));

                $w = $instance->getWidth();
                $h = $instance->getHeight();

                // calculate width if not set
                if (!$tw) {
                    $tw = round($th / $h * $w, 0);
                }

                // calculate height if not set
                if (!$th) {
                    $th = round($tw / $w * $h, 0);
                }

                if ($crop) {
                    $instance->fit($tw, $th);
                } else {
                    $instance->resize($tw, $th);
                }

                $data = $instance->toString($extension, array('quality' => $tq));

                if ($data) {
                    // write to file
                    if (!$filesystem->write($thumb, $data)) {
                        $browser->setResult(JText::_('WF_IMGMANAGER_EXT_THUMBNAIL_ERROR'), 'error');
                    }
                }

                // restore backup resource
                $instance->restore();
            }
        }

        return true;
    }

    /**
     * Special function to determine whether an image can be resampled, as this required Imagick support
     *
     * @return boolean
     */
    protected function canResampleImage()
    {
        $resample = (bool) $this->getParam('editor.resample_image', false);
        $imagick = (bool) $this->getParam('editor.prefer_imagick', true);

        return $resample && $imagick && extension_loaded('imagick');
    }

    public function onUpload($file, $relative = '')
    {
        // get file extension
        $ext = WFUtility::getExtension($file);

        // must be an image
        if (!in_array(strtolower($ext), ['jpg', 'jpeg', 'png', 'apng'])) {
            return array();
        }

        // get filesystem reference
        $filesystem = $this->getFileBrowser()->getFileSystem();

        // make file path relative
        $file = $filesystem->toRelative($file);

        // a cache of processed files. This includes the original file, and any others created by resizing
        $cache = array();

        // process image resize
        $this->resizeUploadImage($file, $cache);

        // process thumbnails
        $this->thumbnailUploadImage($file, $cache);

        // process image watermark
        $this->watermarkUploadImage($file, $cache);

        // should image quality be set?
        $upload_quality = (int) $this->getParam('editor.upload_quality', 100);

        // should the image be resampled?
        $upload_resample = $this->canResampleImage();

        if (empty($cache)) {
            // are we resampling or setting upload quality?
            if ($upload_resample || $upload_quality < 100) {
                // get filesystem reference
                $filesystem = $this->getFileBrowser()->getFileSystem();

                // get imagelab instance
                $instance = $this->getImageLab($file);

                if ($instance) {
                    $cache = array(
                        $file => '',
                    );

                    foreach ($cache as $destination => $data) {
                        if ($data) {
                            $instance->loadString($data);
                        }

                        $options = array();

                        if ($upload_quality < 100) {
                            $options['quality'] = $upload_quality;
                        }

                        $data = $instance->toString($ext, $options);

                        if ($data) {
                            $filesystem->write($destination, $data);
                        }
                    }
                }

            }
        }

        if (!empty($cache)) {
            $instance = $this->getImageLab($file);

            if ($instance) {
                $instance->destroy();
            }
        }

        return array();
    }

    private function toRelative($file)
    {
        return WFUtility::makePath(str_replace(JPATH_ROOT . '/', '', WFUtility::mb_dirname(JPath::clean($file))), WFUtility::mb_basename($file));
    }

    private function cleanTempDirectory()
    {
        $files = JFolder::files($this->getCacheDirectory(), '^(wf_ie_)([a-z0-9]+)\.(jpg|jpeg|gif|png)$');

        if (!empty($files)) {
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

    public function cleanEditorTmp($file = null, $exit = true)
    {
        // Check for request forgeries
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

        // check for image editor access
        if ($this->checkAccess('image_editor', 1) === false) {
            throw new Exception(JText::_('JERROR_ALERTNOAUTHOR'));
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
                return (bool) $result;
            }
        } else {
            $this->cleanTempDirectory();
        }

        return true;
    }

    /**
     * Apply an image edit to a file and return a url to a temp version of that file
     *
     * @param [string] $file The name of the file being edited
     * @param [string] $task The edit type to apply, eg: resize
     * @param [object] $value The edit value to apply
     * @return WFFileSystemResult
     */
    public function applyEdit($file, $task, $value)
    {
        // Check for request forgeries
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

        // check for image editor access
        if ($this->checkAccess('image_editor', 1) === false) {
            throw new Exception(JText::_('JERROR_ALERTNOAUTHOR'));
        }

        $app = JFactory::getApplication();

        $browser = $this->getFileBrowser();

        // check file
        self::validateImagePath($file);

        $upload = $app->input->files->get('file', array(), 'array');

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

            $image = new WFImage(null, array(
                'preferImagick' => (bool) $this->getParam('editor.prefer_imagick', true),
            ));

            $image->loadFile($upload['tmp_name']);
            $image->setType($ext);

            switch ($task) {
                case 'resize':
                    $image->resize($value->width, $value->height);
                    break;
                case 'crop':
                    $image->crop($value->width, $value->height, $value->x, $value->y, false, 1);
                    break;
            }

            // get image data
            $data = $image->toString($ext);

            // write to file
            if ($data) {
                $result->state = (bool) @JFile::write($tmp, $data);
            }

            if ($result->state === true) {
                $tmp = str_replace(WFUtility::cleanPath(JPATH_SITE), '', $tmp);
                $browser->setResult(WFUtility::cleanPath($tmp, '/'), 'files');
            } else {
                $browser->setResult(JText::_('WF_IMAGE_EDIT_APPLY_ERROR'), 'error');
            }

            @unlink($upload['tmp_name']);

            return $browser->getResult();
        }
    }

    public function saveEdit($file, $name, $options = array(), $quality = 100)
    {
        // Check for request forgeries
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

        // check for image editor access
        if ($this->checkAccess('image_editor', 1) === false) {
            throw new Exception(JText::_('JERROR_ALERTNOAUTHOR'));
        }

        $app = JFactory::getApplication();

        $browser = $this->getFileBrowser();
        $filesystem = $browser->getFileSystem();

        // check file
        self::validateImagePath($file);

        // clean temp
        $this->cleanEditorTmp($file, false);

        // check new name
        self::validateImagePath($name);

        $upload = $app->input->files->get('file', '', 'files', 'array');

        // create a filesystem result object
        $result = new WFFileSystemResult();

        if (isset($upload) && isset($upload['tmp_name']) && is_uploaded_file($upload['tmp_name'])) {
            $tmp = $upload['tmp_name'];

            self::validateImageFile($upload);
            $result = $filesystem->upload('multipart', trim($tmp), WFUtility::mb_dirname($file), $name);

            @unlink($tmp);
        } else {
            // set upload as false - JSON request
            $upload = false;

            $file = WFUtility::makePath($filesystem->getBaseDir(), $file);
            $dest = WFUtility::mb_dirname($file) . '/' . WFUtility::mb_basename($name);

            // get extension
            $ext = WFUtility::getExtension($dest);

            // create image
            $image = $this->getImageLab($file);

            foreach ($options as $filter) {
                if (isset($filter->task)) {
                    $args = isset($filter->args) ? (array) $filter->args : array();

                    switch ($filter->task) {
                        case 'resize':
                            $w = $args[0];
                            $h = $args[1];

                            $image->resize($w, $h);
                            break;
                        case 'crop':
                            $w = $args[0];
                            $h = $args[1];

                            $x = $args[2];
                            $y = $args[3];

                            $image->crop($w, $h, $x, $y);
                            break;
                        case 'rotate':
                            $image->rotate(array_shift($args));
                            break;
                        case 'flip':
                            $image->flip(array_shift($args));
                            break;
                    }
                }
            }

            // get image data
            $data = $image->toString($ext);

            // make path relative
            $dest = $filesystem->toRelative($dest);

            // write to file
            if ($data) {
                $result->state = (bool) $filesystem->write($dest, $data);
            }

            // set path
            $result->path = $dest;
        }

        if ($result->state === true) {
            // check if its a valid image
            if (@getimagesize($result->path) === false) {
                JFile::delete($result->path);
                throw new InvalidArgumentException('Invalid image file');
            } else {
                $result->path = str_replace(WFUtility::cleanPath(JPATH_SITE), '', $result->path);
                $browser->setResult(WFUtility::cleanPath($result->path, '/'), 'files');
            }
        } else {
            $browser->setResult($result->message || JText::_('WF_MANAGER_EDIT_SAVE_ERROR'), 'error');
        }

        // return to WFRequest
        return $browser->getResult();
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

        if (!is_dir($dir)) {
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

        $cache_max_size = intval($this->getParam('editor.cache_size', 10, 0)) * 1024 * 1024;
        $cache_max_age = intval($this->getParam('editor.cache_age', 30, 0)) * 86400;
        $cache_max_files = intval($this->getParam('editor.cache_files', 0, 0));

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
                            if (($size + $fsize) > $cache_max_size) {
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
        $thumb = 'wf_thumb_cache_' . md5(WFUtility::mb_basename(WFUtility::stripExtension($file)) . $mtime . $width . $height) . '.' . WFUtility::getExtension($file);

        return WFUtility::makePath($this->getCacheDirectory(), $thumb);
    }

    private function createCacheThumb($file)
    {
        jimport('joomla.filesystem.file');

        $browser = $this->getFileBrowser();

        // check path
        WFUtility::checkPath($file);

        $extension = WFUtility::getExtension($file);

        // lowercase extension
        $extension = strtolower($extension);

        // not an image
        if (!in_array($extension, array('jpeg', 'jpeg', 'png', 'tiff', 'gif'))) {
            exit();
        }

        $file = WFUtility::makePath($browser->getBaseDir(), $file);

        // default for list thumbnails
        $width = 100;
        $height = 100;
        $quality = 75;

        $info = @getimagesize($file);

        // not a valid image?
        if (!$info) {
            exit();
        }

        list($w, $h, $type, $text, $mime) = $info;

        // smaller than thumbnail so output file instead
        if (($w < $width && $h < $height)) {
            return $this->outputImage($file, $mime);
        }

        $exif_types = array('jpg', 'jpeg', 'tiff');

        // try exif thumbnail
        if (in_array($extension, $exif_types)) {
            $exif = exif_thumbnail($file, $width, $height, $mime);

            if ($exif !== false) {
                header('Content-type: ' . $mime);
                die($exif);
            }
        }

        $thumb = $this->getCacheThumbPath($file, $width, $height);

        if (is_file($thumb)) {
            return $this->outputImage($thumb, $mime);
        }

        // create thumbnail file
        $image = new WFImage($file, array(
            'preferImagick' => (bool) $this->getParam('editor.prefer_imagick', true),
        ));

        $image->fit($width, $height);

        if ($image->toFile($thumb, $extension, array('quality' => $quality))) {
            if (is_file($thumb)) {
                return $this->outputImage($thumb, $mime);
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
            $thumbnails[$file['name']] = $this->getCacheThumb(WFUtility::makePath($browser->getBaseDir(), $file['url']), true, 50, 50, WFUtility::getExtension($file['name']), 50);
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
     * Get an image's thumbnail file name.
     *
     * @param string $file the full path to the image file
     *
     * @return string of the thumbnail file
     */
    protected function getThumbName($file)
    {
        $prefix = $this->getParam('thumbnail_prefix', 'thumb_$');

        $ext = WFUtility::getExtension($file);

        if (strpos($prefix, '$') !== false) {
            return str_replace('$', WFUtility::mb_basename($file, '.' . $ext), $prefix) . '.' . $ext;
        }

        return (string) $prefix . WFUtility::mb_basename($file);
    }

    protected function getThumbDir($file, $create)
    {
        $browser = $this->getFileBrowser();
        $filesystem = $browser->getFileSystem();

        // get base directory from editor parameter
        $baseDir = $this->getParam('editor.thumbnail_folder', '', 'thumbnails');

        // get directory from plugin parameter, if any (Image Manager Extended)
        $folder = $this->getParam($this->getName() . '.thumbnail_folder', '', '$$');

        // ugly workaround for parameter issues - a $ or $$ value denotes un unset value, so fallback to global
        // a user can "unset" the value, if it has been stored as an empty string, by setting the value to $
        if ($folder === "$" || $folder === "$$") {
            $folder = $baseDir;
        }

        // make path relative to source file
        $dir = WFUtility::makePath(WFUtility::mb_dirname($file), $folder);

        // create the folder if it does not exist
        if ($create && !$filesystem->exists($dir)) {
            $filesystem->createFolder(WFUtility::mb_dirname($dir), WFUtility::mb_basename($dir));
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
    public function createThumbnail($file, $width = null, $height = null, $quality = 100, $box = null)
    {
        // check path
        self::validateImagePath($file);

        $browser = $this->getFileBrowser();
        $filesystem = $browser->getFileSystem();

        $thumb = WFUtility::makePath($this->getThumbDir($file, true), $this->getThumbName($file));

        $extension = WFUtility::getExtension($file);

        $instance = $this->getImageLab($file);

        if ($instance) {
            if ($box) {
                $box = (array) $box;
                $instance->crop($box['sw'], $box['sh'], $box['sx'], $box['sy']);
            }

            $instance->resize($width, $height);

            $data = $instance->toString($extension, array('quality' => $quality));

            if ($data) {
                // write to file
                if (!$filesystem->write($thumb, $data)) {
                    $browser->setResult(JText::_('WF_IMGMANAGER_EXT_THUMBNAIL_ERROR'), 'error');
                }
            }
        }

        return $browser->getResult();
    }

    /**
     * Remove exif data from an image by rewriting it. This will also rotate images to correct orientation.
     *
     * @param $file Absolute path to the image file
     *
     * @return bool
     */
    private function removeExifData($file)
    {
        $exif = null;

        // check if exif_read_data disabled...
        if (function_exists('exif_read_data')) {

            // get exif data
            $exif = @exif_read_data($file, 'EXIF');
            $rotate = 0;

            if ($exif && !empty($exif['Orientation'])) {
                $orientation = (int) $exif['Orientation'];

                // Fix Orientation
                switch ($orientation) {
                    case 3:
                        $rotate = 180;
                        break;
                    case 6:
                        $rotate = 90;
                        break;
                    case 8:
                        $rotate = 270;
                        break;
                }
            }
        }

        if (extension_loaded('imagick')) {
            try {
                $img = new Imagick($file);

                if ($rotate) {
                    $img->rotateImage(new ImagickPixel(), $rotate);
                }

                $img->stripImage();

                $img->writeImage($file);
                $img->clear();
                $img->destroy();

                return true;
            } catch (Exception $e) {
            }
        } elseif (extension_loaded('gd')) {
            try {

                $handle = imagecreatefromjpeg($file);

                if (is_resource($handle)) {
                    if ($rotate) {
                        $rotation = imagerotate($handle, -$rotate, 0);

                        if ($rotation) {
                            $handle = $rotation;
                        }
                    }

                    imagejpeg($handle, $file);
                    @imagedestroy($handle);

                    return true;
                }
            } catch (Exception $e) {
            }
        }

        return false;
    }

    protected function getFileBrowserConfig($config = array())
    {
        $resize_width = $this->getParam('editor.resize_width', '', 640);

        if (!is_array($resize_width)) {
            $resize_width = explode(',', (string) $resize_width);
        }

        $resize_height = $this->getParam('editor.resize_height', '', 480);

        if (!is_array($resize_height)) {
            $resize_height = explode(',', (string) $resize_height);
        }

        $data = array(
            'view_mode' => $this->getParam('editor.mode', 'list'),
            'can_edit_images' => $this->get('can_edit_images'),
            'cache_enable' => $this->getParam('editor.cache_enable', 0),
            // Upload
            'upload_resize' => $this->getParam('editor.upload_resize', 1),
            'upload_resize_state' => $this->getParam('editor.upload_resize_state', 0),
            // value must be cast as string for javascript processing
            'upload_resize_width' => $resize_width,
            // value must be cast as string for javascript processing
            'upload_resize_height' => $resize_height,
            'upload_resize_quality' => $this->getParam('editor.resize_quality', 100),
            'upload_resize_crop' => $this->getParam('editor.upload_resize_crop', 0),
            'upload_watermark' => $this->getParam('editor.upload_watermark', 0),
            'upload_watermark_state' => $this->getParam('editor.upload_watermark_state', 0),
            // thumbnail
            'upload_thumbnail' => $this->getParam('editor.upload_thumbnail', 1),
            'upload_thumbnail_state' => $this->getParam('editor.upload_thumbnail_state', 0),
            'upload_thumbnail_crop' => $this->getParam('editor.upload_thumbnail_crop', 0),
            // value must be cast as string for javascript processing
            'upload_thumbnail_width' => (string) $this->getParam('editor.upload_thumbnail_width', '', 120),
            // value must be cast as string for javascript processing
            'upload_thumbnail_height' => (string) $this->getParam('editor.upload_thumbnail_height', '', 90),
            'upload_thumbnail_quality' => $this->getParam('editor.upload_thumbnail_quality', 80),
        );

        $config = WFUtility::array_merge_recursive_distinct($data, $config);

        return parent::getFileBrowserConfig($config);
    }
}
