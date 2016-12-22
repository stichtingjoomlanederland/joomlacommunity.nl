<?php
/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright	Copyright (C) 2011 - 2015 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/joomlatools-framework-files for the canonical source repository
 */

/**
 * Attachment Model Entity
 *
 * @author  Arunas Mazeika <https://github.com/amazeika>
 * @package Koowa\Component\Files
 */
class ComFilesModelEntityAttachment extends KModelEntityRow
{
    /**
     * Attachment file getter.
     *
     * @return KModelEntityInterface
     */
    public function getPropertyFile()
    {
        return $this->getObject('com:files.model.files')
                    ->container($this->container_slug)
                    ->name($this->name)
                    ->fetch()
                    ->getIterator()
                    ->current();
    }

    /**
     * Overridden for deleting the attached file.
     */
    public function delete()
    {
        $result = parent::delete();

        if ($result)
        {
            $file = $this->file;

            if (!$file->isNew()) {
                $file->delete();
            }
        }

        return $result;
    }

    public function toArray()
    {
        $data = parent::toArray();

        $file = $this->file;

        if (!$file->isNew())
        {
            $data['file'] = $file->toArray();

            if ($file->isThumbnail())
            {
                $thumbnail = $file->getThumbnail();
                $data['thumbnail'] = !$thumbnail->isNew() ? $thumbnail->thumbnail : false;
            }

            $data['file'] = $file->toArray();
        }

        $data['created_on_timestamp']  = strtotime($this->created_on);
        $data['attached_on_timestamp'] = strtotime($this->attached_on);

        return $data;
    }
}