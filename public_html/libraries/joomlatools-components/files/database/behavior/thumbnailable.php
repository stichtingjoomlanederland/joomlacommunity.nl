<?php
/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright	Copyright (C) 2011 - 2015 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/joomlatools-framework-files for the canonical source repository
 */

/**
 * Thumbnailable Database Behavior
 *
 * @author  Arunas Mazeika <https://github.com/amazeika>
 * @package Koowa\Component\Files
 */
class ComFilesDatabaseBehaviorThumbnailable extends KDatabaseBehaviorAbstract
{
    public function getThumbnail($version =  null)
    {
        $thumbnail = false;

        if (!$this->isNew() && $container = $this->thumbnails_container_slug)
        {
            $model = $this->getObject('com:files.model.thumbnails')->container($container)->source($this->uri);

            if ($version) {
                $model->version($version);
            }

            $thumbnail = $model->fetch();

            if ($thumbnail->isNew()) {
                $thumbnail = false;
            }
        }

        return $thumbnail;
    }
}