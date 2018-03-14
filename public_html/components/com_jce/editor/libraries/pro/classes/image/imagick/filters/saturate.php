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
class WFImageImagickFilterSaturate extends WFImageImagickFilter
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
        $value = array_shift($options);
        // get a value between 0 and 200
        $value = 100 + $value;

        $this->handle->modulateImage(100, $value, 100);
    }
}
