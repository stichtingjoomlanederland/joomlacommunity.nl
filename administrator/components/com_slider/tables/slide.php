<?php
/**
 * @package     Slider
 * @subpackage  com_slider
 *
 * @copyright   Copyright (C) 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * Slider table class
 */
class SliderTableSlide extends JTable
{
    /**
     * Constructor
     *
     * @param   JDatabaseDriver  &$db  A database connector object
     */
    function __construct(&$db)
    {
    	parent::__construct('#__slider', 'id', $db);
    }
}
