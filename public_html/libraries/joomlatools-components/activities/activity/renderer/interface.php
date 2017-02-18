<?php
/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright   Copyright (C) 2011 - 2015 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://github.com/joomlatools/joomlatools-framework-activities for the canonical source repository
 */

/**
 * Activity Renderer Interface.
 *
 * @author  Arunas Mazeika <https://github.com/amazeika>
 * @package Koowa\Component\Activities
 */
interface ComActivitiesActivityRendererInterface
{
    /**
     * Renders an activity.
     *
     * @param ComActivitiesActivityInterface $activity The activity object.
     * @param array                          $config   An optional configuration array.
     *
     * @return string The rendered activity.
     */
    public function render(ComActivitiesActivityInterface $activity, $config = array());
}