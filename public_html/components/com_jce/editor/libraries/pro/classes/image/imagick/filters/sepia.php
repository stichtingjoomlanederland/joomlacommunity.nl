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
class WFImageImagickFilterSepia extends WFImageImagickFilter
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

        $amount = (int) array_shift($options);

        // Microsoft sepia
        $ms_sepia = array(
            0.393, 0.769, 0.189,
            0.349, 0.686, 0.168,
            0.272, 0.534, 0.131,
        );

        // colour offset (normalized)
        $r = round(39 / 255, 3);
        $g = round(14 / 255, 3);
        $b = round(-36 / 255, 3);

        // matrix with greyscale and rgb offsets
        $sepia = array(
            0.299, 0.587, 0.114, 0, 0, $r,
            0.299, 0.587, 0.114, 0, 0, $g,
            0.299, 0.587, 0.114, 0, 0, $b,
            0, 0, 0, 1, 0, 0,
            0, 0, 0, 0, 1, 0,
            0, 0, 0, 0, 0, 1,
        );

        $this->handle->recolorImage($ms_sepia);
    }
}
