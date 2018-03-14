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

/**
 * Image Filter class adjust the smoothness of an image.
 */
class WFImageGDFilterSaturate extends WFImageGDFilter
{
    /**
     * Method to apply a filter to an image resource.
     *
     * @param array $options An array of options for the filter
     *
     * @throws InvalidArgumentException
     * @throws RuntimeException
     */
    public function execute(array $options = array())
    {
        // Verify that image filter support for PHP is available.
        if (!function_exists('imagefilter')) {
            throw new RuntimeException('The imagefilter function for PHP is not available.');
        }

        if (empty($options)) {
            throw new InvalidArgumentException('No valid amount was given.  Expected float.');
        }

        // percent value
        $value = (int) array_shift($options);

        if ($value == 0) {
            return true;
        }

        $value = $value / 100;

        // set max / min
        $value = max(-1, min(1, $value));

        // get width / height
        $width = imagesx($this->handle);
        $height = imagesy($this->handle);

        for ($x = 0; $x < $width; ++$x) {
            for ($y = 0; $y < $height; ++$y) {
                $index = imagecolorat($this->handle, $x, $y);
                $rgb = imagecolorsforindex($this->handle, $index);

                $r = $rgb['red'];
                $g = $rgb['green'];
                $b = $rgb['blue'];
                $a = $rgb['alpha'];

                $average = ($r + $g + $b) / 3;

                $r += round(($r - $average) * $value);
                $g += round(($g - $average) * $value);
                $b += round(($b - $average) * $value);

                // clamp
                $r = min(255, max(0, $r));
                $g = min(255, max(0, $g));
                $b = min(255, max(0, $b));

                $color = imagecolorallocatealpha($this->handle, $r, $g, $b, $a);

                if ($color === false) {
                    $color = imagecolorclosestalpha($this->handle, $r, $g, $b, $a);
                }

                imagesetpixel($this->handle, $x, $y, $color);
            }
        }
    }
}
