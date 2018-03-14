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

define('IMAGE_FLIP_HORIZONTAL', 1);
define('IMAGE_FLIP_VERTICAL', 2);
define('IMAGE_FLIP_BOTH', 3);

// http://www.php.net/manual/en/function.exif-imagetype.php#80383
if (!function_exists('exif_imagetype')) {
    function exif_imagetype($filename)
    {
        if ((list($width, $height, $type, $attr) = getimagesize($filename)) !== false) {
            return $type;
        }

        return false;
    }
}

/**
 * Class to manipulate an image.
 */
class WFImage
{
    /**
     * @const  integer
     *
     * @since  2.1
     */
    const SCALE_FILL = 1;

    /**
     * @const  integer
     *
     * @since  2.1
     */
    const SCALE_INSIDE = 2;

    /**
     * @const  integer
     *
     * @since  2.1
     */
    const SCALE_OUTSIDE = 3;

    /**
     * @var string The source image path
     *
     * @since  2.1
     */
    protected $path = null;

    /**
     * @var string The image library object
     *
     * @since  2.1
     */
    protected static $lib = null;

    /**
     * @var bool Use IMagick if available
     *
     * @since  2.1
     */
    protected static $preferImagick = true;

    /**
     * @var bool REmove EXIF data from JPEG and PNG images
     *
     * @since  2.1
     */
    protected static $removeExif = false;

    /**
     * @var integer Resample the image to 72dpi
     *
     * @since  2.1
     */
    protected static $resampleImage = true;

    /**
     * Class constructor.
     *
     * @param mixed $source A file path for a source image
     *
     * @since   2.1
     */
    public function __construct($source = null, $options = array())
    {
        if (isset($options['preferImagick'])) {
            self::$preferImagick = (bool) $options['preferImagick'];
        }

        if (isset($options['removeExif'])) {
            self::$removeExif = (bool) $options['removeExif'];
        }

        if (isset($options['resampleImage'])) {
            self::$resampleImage = (bool) $options['resampleImage'];
        }

        // create image library instance
        self::getLib();

        if ($source && is_file($source)) {
            $this->loadFile($source);
        }
    }

    /**
     * Get the available Graphics Library.
     *
     * @return object Graphics Library Instance
     *
     * @throws RuntimeException
     */
    public static function getLib($lib = null)
    {
        if (!isset(self::$lib)) {
            if (extension_loaded('imagick')) {
                $lib = 'Imagick';
            } elseif (extension_loaded('gd')) {
                $lib = 'GD';
            } else {
                throw new RuntimeException('No supported Image library available.');
            }

            if ($lib == 'Imagick' && self::$preferImagick === false) {
                $lib = 'GD';
            }

            require_once dirname(__FILE__).'/'.strtolower($lib).'.php';
            $class = 'WFImage'.$lib;

            if (class_exists($class)) {
                self::$lib = new $class();
            } else {
                throw new RuntimeException('Class '.$class.' not found');
            }
        }

        return self::$lib;
    }

    /**
     * Method to return a properties object for an image given a filesystem path.  The
     * result object has values for image width, height, type, attributes, mime type, bits,
     * and channels.
     *
     * @param string $path The filesystem path to the image for which to get properties
     *
     * @return object
     *
     * @since   2.1
     *
     * @throws InvalidArgumentException
     * @throws RuntimeException
     */
    public static function getImageFileProperties($path)
    {
        // Make sure the file exists.
        if (!file_exists($path)) {
            throw new InvalidArgumentException('The image file does not exist.');
        }

        // Get the image file information.
        $info = getimagesize($path);
        if (!$info) {
            throw new RuntimeException('Unable to get properties for the image.');
        }

        // Build the response object.
        $properties = (object) array(
            'width' => $info[0],
            'height' => $info[1],
            'type' => $info[2],
            'attributes' => $info[3],
            'bits' => isset($info['bits']) ? $info['bits'] : null,
            'channels' => isset($info['channels']) ? $info['channels'] : null,
            'mime' => $info['mime'],
        );

        return $properties;
    }

    /**
     * Method to apply a filter to the image by type.
     *
     * @param string $type    The name of the image filter to apply
     * @param array  $options An array of options for the filter
     *
     * @since   2.1
     */
    public function filter($type, array $options = array())
    {
        return self::getLib()->filter($type, $options);
    }

    /**
     * Method to get the height of the image in pixels.
     *
     * @return int
     *
     * @since   2.1
     */
    public function getHeight()
    {
        return self::getLib()->getHeight();
    }

    /**
     * Method to get the width of the image in pixels.
     *
     * @return int
     *
     * @since   2.1
     */
    public function getWidth()
    {
        return self::getLib()->getWidth();
    }

    /**
     * Method to load a file into the Graphics Libary object as the resource.
     *
     * @param string $path The filesystem path to load as an image
     *
     * @since   2.1
     */
    public function loadFile($path)
    {
        self::getLib()->loadFile($path);
    }

    /**
     * Method to load a string into the Graphics Library object as the resource.
     *
     * @param string $string The image string
     *
     * @since   2.1
     *
     * @throws InvalidArgumentException
     */
    public function loadString($string)
    {
        if (strlen($string) < 128) {
            throw new InvalidArgumentException('String does not contain image data.');
        }

        self::getLib()->loadString($string);
    }

    public function resample($resolution = 72)
    {
        return self::getLib()->resample($resolution);
    }

    public function watermark($options = array())
    {
        if (empty($options)) {
            throw new InvalidArgumentException('No watermark options set');
        }

        if (array_key_exists($options, 'text') === false || array_key_exists($options, 'image') === false) {
            //throw new InvalidArgumentException('No watermark text or image');
        }

        $options = (object) array(
                    'type' => $options['type'],
                    'text' => $options['text'],
                    'image' => $options['image'],
                    'font_style' => isset($options['font_style']) ? $options['font_style'] : 'sans-serif',
                    'font_size' => isset($options['font_size']) ? $options['font_size'] : '36',
                    'font_color' => isset($options['font_color']) ? $options['font_color'] : '#FFFFFF',
                    'opacity' => isset($options['opacity']) ? $options['opacity'] : 50,
                    'position' => isset($options['position']) ? $options['position'] : 'center',
                    'margin' => isset($options['margin']) ? (int) $options['margin'] : 10,
                    'angle' => isset($options['angle']) ? (int) $options['angle'] : 0,
                );

        return self::getLib()->watermark($options);
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
     * @return bool
     *
     * @since   2.1
     */
    public function resize($width, $height, $createNew = false, $scaleMethod = self::SCALE_INSIDE)
    {
        // Sanitize width.
        $width = $this->sanitizeWidth($width, $height);

        // Sanitize height.
        $height = $this->sanitizeHeight($height, $width);

        // Prepare the dimensions for the resize operation.
        $dimensions = $this->prepareDimensions($width, $height, $scaleMethod);

        return self::getLib()->resize($dimensions->width, $dimensions->height, $createNew);
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
     * @return bool
     *
     * @since   2.1
     */
    public function crop($width, $height, $left, $top, $createNew = false)
    {
        // Sanitize width.
        $width = $this->sanitizeWidth($width, $height);

        // Sanitize height.
        $height = $this->sanitizeHeight($height, $width);

        // Sanitize left.
        $left = $this->sanitizeOffset($left);

        // Sanitize top.
        $top = $this->sanitizeOffset($top);

        return self::getLib()->crop($width, $height, $left, $top, $createNew);
    }

    /**
     * Method to rotate the current image.
     *
     * @param mixed $angle      The angle of rotation for the image
     * @param int   $background The background color to use when areas are added due to rotation
     * @param bool  $createNew  If true the current image will be cloned, rotated and returned; else
     *                          the current image will be rotated and returned
     *
     * @return bool
     *
     * @since   2.1
     */
    public function rotate($angle, $background = -1, $createNew = false)
    {
        // Sanitize input
        $angle = floatval($angle);

        return self::getLib()->rotate($angle, $background, $createNew);
    }

    /**
     * Method to rotate the current image.
     *
     * @param mixed $angle      The angle of rotation for the image
     * @param int   $background The background color to use when areas are added due to rotation
     * @param bool  $createNew  If true the current image will be cloned, rotated and returned; else
     *                          the current image will be rotated and returned
     *
     * @return bool
     *
     * @since   2.1
     */
    public function flip($direction, $createNew = false)
    {
        if (empty($direction)) {
            throw new InvalidArgumentException('No flip direction set');
        }

        switch ($direction) {
            case 'horizontal':
                $direction = IMAGE_FLIP_HORIZONTAL;
                break;
            case 'vertical':
                $direction = IMAGE_FLIP_VERTICAL;
                break;
            case 'both':
                $direction = IMAGE_FLIP_BOTH;
                break;
        }

        return self::getLib()->flip($direction, $createNew);
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
     * @since   2.1
     */
    public function toFile($path, $type = 'jpeg', array $options = array())
    {

        // remove exif data before saving?
        $options['removeExif'] = self::$removeExif;

        // resample on saving?
        $options['resampleImage'] = self::$resampleImage;

        return self::getLib()->toFile($path, $type, $options);
    }

    /**
     * Method to write the current image out to a string.
     *
     * @param int   $type    The image type to save the file as
     * @param array $options The image type options to use in saving the file
     *
     * @return bool
     *
     * @since   2.1
     */
    public function toString($type = 'jpeg', array $options = array())
    {
        ob_start();

        // remove exif data before saving?
        $options['removeExif'] = self::$removeExif;

        // resample on saving?
        $options['resampleImage'] = self::$resampleImage;

        self::getLib()->toString($type, $options);

        return ob_get_clean();
    }

    /**
     * Method to get the new dimensions for a resized image.
     *
     * @param int $width       The width of the resized image in pixels
     * @param int $height      The height of the resized image in pixels
     * @param int $scaleMethod The method to use for scaling
     *
     * @return object
     *
     * @since   11.3
     *
     * @throws InvalidArgumentException
     */
    protected function prepareDimensions($width, $height, $scaleMethod)
    {
        // Instantiate variables.
        $dimensions = new stdClass();

        switch ($scaleMethod) {
            case self::SCALE_FILL:
                $dimensions->width = intval(round($width));
                $dimensions->height = intval(round($height));
                break;

            case self::SCALE_INSIDE:
            case self::SCALE_OUTSIDE:
                $rx = $this->getWidth() / $width;
                $ry = $this->getHeight() / $height;

                if ($scaleMethod == self::SCALE_INSIDE) {
                    $ratio = ($rx > $ry) ? $rx : $ry;
                } else {
                    $ratio = ($rx < $ry) ? $rx : $ry;
                }

                $dimensions->width = intval(round($this->getWidth() / $ratio));
                $dimensions->height = intval(round($this->getHeight() / $ratio));
                break;

            default:
                throw new InvalidArgumentException('Invalid scale method.');
                break;
        }

        return $dimensions;
    }

    /**
     * Method to sanitize a height value.
     *
     * @param mixed $height The input height value to sanitize
     * @param mixed $width  The input width value for reference
     *
     * @return int
     *
     * @since   11.3
     */
    protected function sanitizeHeight($height, $width)
    {
        // If no height was given we will assume it is a square and use the width.
        $height = ($height === null) ? $width : $height;

        // If we were given a percentage, calculate the integer value.
        if (preg_match('/^[0-9]+(\.[0-9]+)?\%$/', $height)) {
            $height = intval(round($this->getHeight() * floatval(str_replace('%', '', $height)) / 100));
        }
        // Else do some rounding so we come out with a sane integer value.
        else {
            $height = intval(round(floatval($height)));
        }

        return $height;
    }

    /**
     * Method to sanitize an offset value like left or top.
     *
     * @param mixed $offset An offset value
     *
     * @return int
     *
     * @since   11.3
     */
    protected function sanitizeOffset($offset)
    {
        return intval(round(floatval($offset)));
    }

    /**
     * Method to sanitize a width value.
     *
     * @param mixed $width  The input width value to sanitize
     * @param mixed $height The input height value for reference
     *
     * @return int
     *
     * @since   11.3
     */
    protected function sanitizeWidth($width, $height)
    {
        // If no width was given we will assume it is a square and use the height.
        $width = ($width === null) ? $height : $width;

        // If we were given a percentage, calculate the integer value.
        if (preg_match('/^[0-9]+(\.[0-9]+)?\%$/', $width)) {
            $width = intval(round($this->getWidth() * floatval(str_replace('%', '', $width)) / 100));
        }
        // Else do some rounding so we come out with a sane integer value.
        else {
            $width = intval(round(floatval($width)));
        }

        return $width;
    }

    public static function getImageType($string)
    {
        switch ($string) {
            case 'jpeg':
            case 'jpg':
            default:
                return IMAGETYPE_JPEG;
                break;
            case 'png':
                return IMAGETYPE_PNG;
                break;
            case 'gif':
                return IMAGETYPE_GIF;
                break;
        }
    }

    public function destroy()
    {
        self::getLib()->destroy();
    }

    public function getType()
    {
        self::getLib()->getType();
    }

    public function setType($type)
    {
        // convert extension to image type constant
        $type = self::getImageType($type);

        // set type
        self::getLib()->setType($type);
    }
}
