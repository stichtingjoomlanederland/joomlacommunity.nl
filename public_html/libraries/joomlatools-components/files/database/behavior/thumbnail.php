<?php
/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright	Copyright (C) 2011 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/joomlatools-framework-files for the canonical source repository
 */

/**
 * File Thumbnail Behavior
 *
 * @author  Ercan Ozkaya <https://github.com/ercanozkaya>
 * @package Koowa\Component\Files
 */
class ComFilesDatabaseBehaviorThumbnail extends KDatabaseBehaviorAbstract
{
    /**
     * Constructor.
     *
     * @param KObjectConfig $config	An optional KObjectConfig object with configuration options.
     */
    public function __construct(KObjectConfig $config)
    {
        parent::__construct($config);

        $this->addCommandCallback('after.save'  , 'resizeIfNeeded');
        $this->addCommandCallback('after.save'  , 'saveThumbnail');
        $this->addCommandCallback('after.delete', 'deleteThumbnail');
    }

    public function resizeIfNeeded()
    {
        $available_extensions = array('jpg', 'jpeg', 'gif', 'png');

        if ($this->isImage()
            && $this->getContainer()->getParameters()->maximum_image_size
            && in_array(strtolower($this->extension), $available_extensions))
        {
            $parameters  = $this->getContainer()->getParameters();
            $size        = $parameters['maximum_image_size'];

            if (!empty($size))
            {
                $current_size = @getimagesize($this->fullpath);

                if ($current_size && $current_size[0] > $size || $current_size[1] > $size)
                {
                    $thumb = $this->getObject('com:files.model.entity.thumbnail', [
                        'size' => ['x' => $size, 'y' => 0]
                    ]);
                    $thumb->source = $this;
                    $thumb->generateThumbnail(true);
                }
            }
        }
    }

    public function saveThumbnail()
    {
        $result = null;
        $available_extensions = array('jpg', 'jpeg', 'gif', 'png');

        if ($this->isImage() && $this->getContainer()->getParameters()->thumbnails && in_array(strtolower($this->extension), $available_extensions))
        {
            $parameters  = $this->getContainer()->getParameters();
            $size        = isset($parameters['thumbnail_size']) ? $parameters['thumbnail_size'] : array();

            $thumb = $this->getObject('com:files.model.thumbnails')
                ->container($this->container)
                ->folder($this->folder)
                ->filename($this->name)
                ->fetch();

            if ($thumb->isNew()) {
                $thumb = $this->getObject('com:files.model.entity.thumbnail', array('size' => $size));
            } elseif ($size) {
                $thumb->setSize($size);
            }

            $thumb->source = $this;

            $result = $thumb->save();
        }

        return $result;
    }

    public function getThumbnail()
    {
        $thumbnail = $this->getObject('com:files.model.thumbnails')
                          ->container($this->container)
                          ->folder($this->folder)
                          ->filename($this->name)
                          ->fetch();

        if ($thumbnail->isNew())
        {
            if ($this->saveThumbnail()) {
                $thumbnail = $this->getThumbnail();
            }
        }

        return $thumbnail;
    }

    public function deleteThumbnail(KDatabaseContext $context)
    {
        $entity = $context->getSubject();

        $thumb = $this->getObject('com:files.model.thumbnails')
            ->container($entity->container)
            ->folder($entity->folder)
            ->filename($entity->name)
            ->fetch();

        $result = $thumb->delete();

        return $result;
    }
}