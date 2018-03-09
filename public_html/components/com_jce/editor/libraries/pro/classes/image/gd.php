<?php

/**
 * @copyright 	Copyright (c) 2009-2017 Ryan Demmer. All rights reserved
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved
 * @license   	GNU/GPL 2 or later - http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * JCE is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 *
 * Based on JImage library from Joomla.Platform 11.3
 */
defined('_JEXEC') or die;

// load filter class
require_once __DIR__.'/gd/filter.php';

/**
 * Class to manipulate an image.
 */
class WFImageGD
{
    /**
     * @var resource The image resource handle
     */
    protected $handle;

    /**
     * @var string The source image path
     */
    protected $path = null;

    /**
     * @var array Whether or not different image formats are supported
     */
    protected static $formats = array();

    /**
     * @var string File type, eg: jpg, gif, png
     */
    protected static $type;

    /**
     * Class constructor.
     *
     * @param mixed $source Either a file path for a source image or a GD resource handler for an image
     *
     * @throws RuntimeException
     */
    public function __construct($source = null)
    {
        // Verify that GD support for PHP is available.
        if (!extension_loaded('gd')) {
            throw new RuntimeException('The GD extension for PHP is not available.');
        }

        // Determine which image types are supported by GD, but only once.
        if (!isset(self::$formats[IMAGETYPE_JPEG])) {
            $info = gd_info();
            self::$formats[IMAGETYPE_JPEG] = ($info['JPEG Support']) ? true : false;

            if (self::$formats[IMAGETYPE_JPEG] === false) {
                self::$formats[IMAGETYPE_JPEG] = ($info['JPG Support']) ? true : false;
            }

            self::$formats[IMAGETYPE_PNG] = ($info['PNG Support']) ? true : false;
            self::$formats[IMAGETYPE_GIF] = ($info['GIF Read Support']) ? true : false;
        }

        // If the source input is a resource, set it as the image handle.
        if (is_resource($source) && (get_resource_type($source) == 'gd')) {
            $this->handle = &$source;
        } elseif (!empty($source) && is_string($source)) {
            // If the source input is not empty, assume it is a path and populate the image handle.
            $this->loadFile($source);
        }
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
        $channels = ($image->mime == 'image/png') ? 4 : 3;

        if (function_exists('memory_get_usage')) {
            // try ini_get
            $limit = ini_get('memory_limit');

            // try get_cfg_var
            if (empty($limit)) {
                $limit = get_cfg_var('memory_limit');
            }

            // can't get from ini, assume low value of 32M
            if (empty($limit)) {
                $limit = 32 * 1048576;
            } else {
                $limit = self::convertIniValue($limit);
            }

            $used = memory_get_usage(true);

            return $image->width * $image->height * $channels * 1.7 < $limit - $used;
        }

        return true;
    }

    /**
     * Method to get the height of the image in pixels.
     *
     * @return int
     *
     * @throws LogicException
     */
    public function getHeight()
    {
        // Make sure the resource handle is valid.
        if (!$this->isLoaded()) {
            throw new LogicException('No valid image was loaded');
        }

        return imagesy($this->handle);
    }

    /**
     * Method to get the width of the image in pixels.
     *
     * @return int
     *
     * @throws LogicException
     */
    public function getWidth()
    {
        // Make sure the resource handle is valid.
        if (!$this->isLoaded()) {
            throw new LogicException('No valid image was loaded');
        }

        return imagesx($this->handle);
    }

    /**
     * Method to return the path.
     *
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Method to determine whether or not an image has been loaded into the object.
     *
     * @return bool
     */
    public function isLoaded()
    {
        // Make sure the resource handle is valid.
        return is_resource($this->handle) && get_resource_type($this->handle) === 'gd';
    }

    /**
     * Method to determine whether or not the image has transparency.
     *
     * @return bool
     *
     * @throws LogicException
     */
    public function isTransparent()
    {
        // Make sure the resource handle is valid.
        if (!$this->isLoaded()) {
            throw new LogicException('No valid image was loaded');
        }

        return imagecolortransparent($this->handle) >= 0;
    }

    /**
     * Method to load a file into the JImage object as the resource.
     *
     * @param string $path The filesystem path to load as an image
     *
     * @throws InvalidArgumentException
     * @throws RuntimeException
     */
    public function loadFile($path)
    {
        // Make sure the file exists.
        if (!file_exists($path)) {
            throw new InvalidArgumentException('The image file does not exist.');
        }

        $properties = WFImage::getImageFileProperties($path);

        if (self::checkMem($properties) === false) {
            throw new RuntimeException('Insufficient memory available to process this image.');
        }

        // Attempt to load the image based on the MIME-Type
        switch ($properties->mime) {
            case 'image/gif':
                // Make sure the image type is supported.
                if (empty(self::$formats[IMAGETYPE_GIF])) {
                    throw new RuntimeException('Attempting to load an image of unsupported type GIF.');
                }

                // Attempt to create the image handle.
                $handle = imagecreatefromgif($path);
                if (!is_resource($handle)) {
                    throw new RuntimeException('Unable to process GIF image.');
                }
                $this->handle = $handle;
                break;

            case 'image/jpeg':
                // Make sure the image type is supported.
                if (empty(self::$formats[IMAGETYPE_JPEG])) {
                    throw new RuntimeException('Attempting to load an image of unsupported type JPG.');
                }

                // Attempt to create the image handle.
                $handle = imagecreatefromjpeg($path);
                if (!is_resource($handle)) {
                    throw new RuntimeException('Unable to process JPG image.');
                }
                $this->handle = $handle;
                break;

            case 'image/png':
                // Make sure the image type is supported.
                if (empty(self::$formats[IMAGETYPE_PNG])) {
                    throw new RuntimeException('Attempting to load an image of unsupported type PNG.');
                }

                // Attempt to create the image handle.
                $handle = imagecreatefrompng($path);
                if (!is_resource($handle)) {
                    throw new RuntimeException('Unable to process PNG image.');
                }
                $this->handle = $handle;
                break;

            default:
                throw new InvalidArgumentException('Attempting to load an image of unsupported type: '.$properties->mime);
                break;
        }

        // Set the filesystem path to the source image.
        $this->path = $path;

        // set type
        $this->setType(exif_imagetype($path));
    }

    /**
     * Method to load a file into the JImage object as the resource.
     *
     * @param string $path The filesystem path to load as an image
     *
     * @throws InvalidArgumentException
     * @throws RuntimeException
     */
    public function loadString($string)
    {
        $handle = imagecreatefromstring($string);

        if (is_resource($handle) && get_resource_type($handle) == 'gd') {
            $this->handle = $handle;
        } else {
            imagedestroy($handle);
            throw new RuntimeException('Attempting to load an image of unsupported type.');
        }
    }

    private function getWatermarkPosition($options, $mw, $mh)
    {
        $width = $this->getWidth();
        $height = $this->getHeight();

        switch ($options->position) {
            default:
            case 'center':
                $x = floor(($width - $mw) / 2);
                $y = floor(($height - $mh) / 2);
                break;
            case 'top-left':
                $x = $options->margin;
                $y = floor($mh / 2) + $options->margin;
                break;
            case 'top-right':
                $x = $width - $mw - $options->margin;
                $y = floor($mh / 2) + $options->margin;
                break;
            case 'center-left':
                $x = 0 + $options->margin;
                $y = floor(($height - $mh) / 2);

                break;
            case 'center-right':
                $x = $width - $mw - $options->margin;
                $y = floor(($height - $mh) / 2);

                break;
            case 'top-center':
                $x = floor(($width - $mw) / 2);
                $y = floor($mh / 2) + $options->margin;
                break;
            case 'bottom-center':
                $x = floor(($width - $mw) / 2);
                $y = $height - $options->margin;
                if ($options->type == 'image') {
                    $y = $height - $mh - $options->margin;
                }
                break;
            case 'bottom-left':
                $x = 0 + $options->margin;
                $y = $height - $options->margin;

                if ($options->type == 'image') {
                    $y = $height - $mh - $options->margin;
                }
                break;
            case 'bottom-right':
                $x = $width - $mw - $options->margin;
                $y = $height - $options->margin;

                if ($options->type == 'image') {
                    $y = $height - $mh - $options->margin;
                }
                break;
        }

        return array('x' => $x, 'y' => $y);
    }

    private function watermarkText($options)
    {
        $font = $options->font_style;

        if (is_file($font)) {
            $box = imagettfbbox((int) $options->font_size, $options->angle, $font, $options->text);

            $x0 = min($box[0], $box[2], $box[4], $box[6]) - (int) $options->margin;
            $x1 = max($box[0], $box[2], $box[4], $box[6]) + (int) $options->margin;
            $y0 = min($box[1], $box[3], $box[5], $box[7]) - (int) $options->margin;
            $y1 = max($box[1], $box[3], $box[5], $box[7]) + (int) $options->margin;

            $mw = abs($x1 - $x0);
            $mh = abs($y1 - $y0);

            $position = $this->getWatermarkPosition($options, $mw, $mh);

            $options->font_color = preg_replace('#[^\w]+#', '', $options->font_color);

            $color = imagecolorallocatealpha($this->handle, hexdec(substr($options->font_color, 0, 2)), hexdec(substr($options->font_color, 2, 2)), hexdec(substr($options->font_color, 4, 2)), 127 * (100 - (int) $options->opacity) / 100);

            imagettftext($this->handle, (int) $options->font_size, $options->angle, $position['x'], $position['y'], $color, $font, $options->text);
        }
    }

    /**
     * PNG ALPHA CHANNEL SUPPORT for imagecopymerge();
     * This is a function like imagecopymerge but it handle alpha channel well!!!
     * A fix to get a function like imagecopymerge WITH ALPHA SUPPORT
     * Main script by aiden.mail@freemail.hu
     * Transformed to imagecopymerge_alpha() by rodrigo.polo@gmail.com
     * http://www.php.net/manual/en/function.imagecopymerge.php#88456.
     */
    private static function imagecopymerge_alpha($dst_im, $src_im, $dst_x, $dst_y, $src_x, $src_y, $src_w, $src_h, $pct)
    {
        if (!isset($pct)) {
            return false;
        }
        $pct /= 100;
        // Get image width and height
        $w = imagesx($src_im);
        $h = imagesy($src_im);
        // Turn alpha blending off
        imagealphablending($src_im, false);
        // Find the most opaque pixel in the image (the one with the smallest alpha value)
        $minalpha = 127;
        for ($x = 0; $x < $w; ++$x) {
            for ($y = 0; $y < $h; ++$y) {
                $alpha = (imagecolorat($src_im, $x, $y) >> 24) & 0xFF;
                if ($alpha < $minalpha) {
                    $minalpha = $alpha;
                }
            }
        }
        //loop through image pixels and modify alpha for each
        for ($x = 0; $x < $w; ++$x) {
            for ($y = 0; $y < $h; ++$y) {
                //get current alpha value (represents the TANSPARENCY!)
                $colorxy = imagecolorat($src_im, $x, $y);
                $alpha = ($colorxy >> 24) & 0xFF;
                //calculate new alpha
                if ($minalpha !== 127) {
                    $alpha = 127 + 127 * $pct * ($alpha - 127) / (127 - $minalpha);
                } else {
                    $alpha += 127 * $pct;
                }
                //get the color index with new alpha
                $alphacolorxy = imagecolorallocatealpha($src_im, ($colorxy >> 16) & 0xFF, ($colorxy >> 8) & 0xFF, $colorxy & 0xFF, $alpha);
                //set pixel with the new color + opacity
                if (!imagesetpixel($src_im, $x, $y, $alphacolorxy)) {
                    return false;
                }
            }
        }
        // The image copy
        imagecopy($dst_im, $src_im, $dst_x, $dst_y, $src_x, $src_y, $src_w, $src_h);
    }

    public function watermark($options)
    {
        // Make sure the resource handle is valid.
        if (!$this->isLoaded()) {
            throw new LogicException('No valid image was loaded');
        }

        $mark = null;

        if ($options->type == 'text' && isset($options->text)) {
            $this->watermarkText($options);
        } elseif (isset($options->image)) {
            $mark = new self($options->image);
        }

        if ($mark && is_resource($mark->handle) && get_resource_type($mark->handle) == 'gd') {
            $mw = imagesx($mark->handle);
            $mh = imagesy($mark->handle);

            $position = $this->getWatermarkPosition($options, $mw, $mh);

            if (exif_imagetype($options->image) === IMAGETYPE_PNG) {
                self::imagecopymerge_alpha($this->handle, $mark->handle, $position['x'], $position['y'], 0, 0, $mw, $mh, $options->opacity);
            } else {
                // Allow transparency for the new image handle.
                imagealphablending($mark->handle, false);
                imagesavealpha($mark->handle, true);

                imagecopymerge($this->handle, $mark->handle, $position['x'], $position['y'], 0, 0, $mw, $mh, $options->opacity);
            }
        }

        return $this;
    }

    /**
     * Set image resolution (not available in PHP GD).
     *
     * @param type $resolution
     *
     * @return WFImageGD
     * https://gist.github.com/chemicaloliver/3164297
     */
    public function resample($resolution)
    {
        // Make sure the resource handle is valid.
        if (!$this->isLoaded()) {
            throw new LogicException('No valid image was loaded');
        }

        // only resample jpeg images
        if ($this->getType() !== IMAGETYPE_JPEG) {
            return $this;
        }

        // empty string
        $string = '';

        $width = $this->getWidth();
        $height = $this->getHeight();

        // Use truecolour image to avoid any issues with colours changing
        $handle = imagecreatetruecolor($width, $height);

        // Resample the image
        imagecopyresampled($handle, $this->handle, 0, 0, 0, 0, $width, $height, $this->getWidth(), $this->getHeight());

        // Get GD image resource as JPEG string
        ob_start();
        imagejpeg($handle, '');
        $string = ob_get_contents();
        ob_end_clean();

        if ($string) {
            // change the JPEG header to resolution
            $string = substr_replace($string, pack('Cnn', 0x01, $resolution, $resolution), 13, 5);
            $this->handle = imagecreatefromstring($string);
        }

        return $this;
    }

    /**
     * Method to resize the current image.
     *
     * @param mixed $width       The width of the resized image in pixels or a percentage
     * @param mixed $height      The height of the resized image in pixels or a percentage
     * @param bool  $createNew   If true the current image will be cloned, resized and returned; else
     *                           the current image will be resized and returned
     * @param int   $scaleMethod Which method to use for scaling
     *
     * @return WFImageGD
     *
     * @throws LogicException
     */
    public function resize($width, $height, $createNew = false)
    {
        // Make sure the resource handle is valid.
        if (!$this->isLoaded()) {
            throw new LogicException('No valid image was loaded');
        }

        // Create the new truecolor image handle.
        $handle = imagecreatetruecolor($width, $height);

        // Allow transparency for the new image handle.
        imagealphablending($handle, false);
        imagesavealpha($handle, true);

        if ($this->isTransparent() && $this->getType() === IMAGETYPE_GIF) {
            // Get the transparent color values for the current image.
            $rgba = imagecolorsforindex($this->handle, imagecolortransparent($this->handle));
            $color = imagecolorallocate($this->handle, $rgba['red'], $rgba['green'], $rgba['blue']);

            // Set the transparent color values for the new image.
            imagefill($handle, 0, 0, $color);
            imagecolortransparent($handle, $color);

            imagecopyresized($handle, $this->handle, 0, 0, 0, 0, $width, $height, $this->getWidth(), $this->getHeight());
        } else {
            imagecopyresampled($handle, $this->handle, 0, 0, 0, 0, $width, $height, $this->getWidth(), $this->getHeight());
        }

        // If we are resizing to a new image, create a new JImage object.
        if ($createNew) {
            $new = new self($handle);

            return $new;
        }
        // Swap out the current handle for the new image handle.
        else {
            $this->handle = $handle;

            return $this;
        }
    }

    /**
     * Method to crop the current image.
     *
     * @param mixed $width     The width of the image section to crop in pixels or a percentage
     * @param mixed $height    The height of the image section to crop in pixels or a percentage
     * @param int   $left      The number of pixels from the left to start cropping
     * @param int   $top       The number of pixels from the top to start cropping
     * @param bool  $createNew If true the current image will be cloned, cropped and returned; else
     *                         the current image will be cropped and returned
     *
     * @return WFImageGD
     *
     * @throws LogicException
     */
    public function crop($width, $height, $left, $top, $createNew = false)
    {
        // Make sure the resource handle is valid.
        if (!$this->isLoaded()) {
            throw new LogicException('No valid image was loaded');
        }

        // Create the new truecolor image handle.
        $handle = imagecreatetruecolor($width, $height);

        // Allow transparency for the new image handle.
        imagealphablending($handle, false);
        imagesavealpha($handle, true);

        if ($this->isTransparent() && $this->getType() === IMAGETYPE_GIF) {
            // Get the transparent color values for the current image.
            $rgba = imagecolorsforindex($this->handle, imagecolortransparent($this->handle));
            $color = imagecolorallocate($this->handle, $rgba['red'], $rgba['green'], $rgba['blue']);

            // Set the transparent color values for the new image.
            imagefill($handle, 0, 0, $color);
            imagecolortransparent($handle, $color);

            imagecopyresized($handle, $this->handle, 0, 0, $left, $top, $width, $height, $width, $height);
        } else {
            imagecopyresampled($handle, $this->handle, 0, 0, $left, $top, $width, $height, $width, $height);
        }

        // If we are cropping to a new image, create a new JImage object.
        if ($createNew) {
            $new = new self($handle);

            return $new;
        }
        // Swap out the current handle for the new image handle.
        else {
            $this->handle = $handle;

            return $this;
        }
    }

    /**
     * Method to rotate the current image.
     *
     * @param mixed $angle      The angle of rotation for the image
     * @param int   $background The background color to use when areas are added due to rotation
     * @param bool  $createNew  If true the current image will be cloned, rotated and returned; else
     *                          the current image will be rotated and returned
     *
     * @return WFImageGD
     *
     * @throws LogicException
     */
    public function rotate($angle, $background = -1, $createNew = false)
    {
        // Make sure the resource handle is valid.
        if (!$this->isLoaded()) {
            throw new LogicException('No valid image was loaded');
        }

        // Create the new truecolor image handle.
        $handle = imagecreatetruecolor($this->getWidth(), $this->getHeight());

        // Allow transparency for the new image handle.
        imagealphablending($handle, false);
        imagesavealpha($handle, true);

        // Copy the image
        imagecopy($handle, $this->handle, 0, 0, 0, 0, $this->getWidth(), $this->getHeight());

        // Rotate the image
        $handle = imagerotate($handle, $angle, $background);

        // If we are resizing to a new image, create a new JImage object.
        if ($createNew) {
            $new = new self($handle);

            return $new;
        }
        // Swap out the current handle for the new image handle.
        else {
            $this->handle = $handle;

            return $this;
        }
    }

    public function flip($mode, $createNew = false)
    {
        // Make sure the resource handle is valid.
        if (!$this->isLoaded()) {
            throw new LogicException('No valid image was loaded');
        }

        // Create the new truecolor image handle.
        $handle = imagecreatetruecolor($this->getWidth(), $this->getHeight());

        // Allow transparency for the new image handle.
        imagealphablending($handle, false);
        imagesavealpha($handle, true);

        $width = $this->getWidth();
        $height = $this->getHeight();

        switch ((int) $mode) {

            case IMAGE_FLIP_VERTICAL:
                for ($y = 0; $y < $height; ++$y) {
                    imagecopy($handle, $this->handle, 0, $y, 0, $height - $y - 1, $width, 1);
                }
                break;

            case IMAGE_FLIP_HORIZONTAL:
                for ($x = 0; $x < $width; ++$x) {
                    imagecopy($handle, $this->handle, $x, 0, $width - $x - 1, 0, 1, $height);
                }
                break;
        }

        // If we are flipping to a new image, create a new JImage object.
        if ($createNew) {
            $new = new self($handle);

            return $new;
        }
        // Swap out the current handle for the new image handle.
        else {
            $this->handle = $handle;

            return $this;
        }
    }

    /**
     * Method to apply a filter to the image by type.  Two examples are: grayscale and sketchy.
     *
     * @param string $type    The name of the image filter to apply
     * @param array  $options An array of options for the filter
     *
     * @return WFImage
     *
     * @see     WFImageFilter
     *
     * @throws LogicException
     * @throws RuntimeException
     */
    public function filter($type, array $options = array())
    {
        // Get the image filter instance.
        $filter = $this->getFilterInstance($type);

        // Execute the image filter.
        $filter->execute($options);

        return $this;
    }

    /**
     * Method to write the current image out to a file.
     *
     * @param string $path    The filesystem path to save the image
     * @param int    $type    The image type to save the file as
     * @param array  $options The image type options to use in saving the file
     *
     * @return bool
     *
     * @throws LogicException
     */
    public function toFile($path, $type = IMAGETYPE_JPEG, array $options = array())
    {
        // Make sure the resource handle is valid.
        if (!$this->isLoaded()) {
            throw new LogicException('No valid image was loaded');
        }

        if (is_string($type)) {
            $type = WFImage::getImageType($type);
        }

        $result = false;

        switch ($type) {
            case IMAGETYPE_GIF:
                $result = imagegif($this->handle, $path);
                break;

            case IMAGETYPE_PNG:

                $quality = (array_key_exists('quality', $options)) ? $options['quality'] : 0;

                // get as value from 0-9
                if ($quality) {
                    // png compression is a value from 0 (none) to 9 (max)
                    $quality = 100 - $quality;
                    // convert to value between 0 - 9
                    $quality = min(floor($quality / 10), 9);
                }

                $result = imagepng($this->handle, $path, $quality);
                break;

            case IMAGETYPE_JPEG:
                $result = imagejpeg($this->handle, $path, (array_key_exists('quality', $options)) ? $options['quality'] : 75);
                break;
        }

        $this->destroy();

        return $result;
    }

    /**
     * Method to write the current image out to a file.
     *
     * @param string $path    The filesystem path to save the image
     * @param int    $type    The image type to save the file as
     * @param array  $options The image type options to use in saving the file
     *
     * @throws LogicException
     */
    public function toString($type = IMAGETYPE_JPEG, array $options = array())
    {
        return $this->toFile(null, $type, $options);
    }

    /**
     * Method to get an image filter instance of a specified type.
     *
     * @param string $type The image filter type to get
     *
     * @return WFImageFilter
     *
     * @throws RuntimeException
     */
    protected function getFilterInstance($type)
    {
        // Sanitize the filter type.
        $type = strtolower(preg_replace('#[^A-Z0-9_]#i', '', $type));

        // load the filter
        require_once dirname(__FILE__).'/gd/filters/'.$type.'.php';

        // Verify that the filter type exists.
        $className = 'WFImageGDFilter'.ucfirst($type);

        if (!class_exists($className)) {
            throw new RuntimeException('The '.ucfirst($type).' image filter is not available.');
        }

        // Instantiate the filter object.
        $instance = new $className($this->handle);

        // Verify that the filter type is valid.
        if (!($instance instanceof WFImageGDFilter)) {
            throw new RuntimeException('The '.ucfirst($type).' image filter is not valid.');
        }

        return $instance;
    }

    public function getType()
    {
        return $this->type;
    }

    public function setType($type)
    {
        $this->type = $type;
    }

    public function destroy()
    {
        imagedestroy($this->handle);
    }
}
