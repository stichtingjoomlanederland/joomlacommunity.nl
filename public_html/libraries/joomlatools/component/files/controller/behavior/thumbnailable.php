<?php
/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright	Copyright (C) 2011 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/joomlatools-framework-files for the canonical source repository
 */

/**
 * Thumbnailable Controller Behavior
 *
 * @author  Ercan Ozkaya <https://github.com/ercanozkaya>
 * @package Koowa\Component\Files
 */
class ComFilesControllerBehaviorThumbnailable extends KControllerBehaviorAbstract
{
	protected function _afterBrowse(KControllerContextInterface $context)
	{
        $container = $this->getModel()->getContainer();

        if (!$context->request->query->get('thumbnails', 'cmd') || $container->getParameters()->thumbnails !== true) {
            return;
        }

        $files = array();
        foreach ($context->result as $entity)
        {
            if ($entity->getIdentifier()->name === 'file' && $entity->isImage()) {
                $files[] = $entity->name;
            }
        }

        if (!count($files)) {
            return;
        }

        $thumbnails = $this->getObject('com:files.controller.thumbnail')
            ->container($this->getModel()->getState()->container)
            ->folder($this->getRequest()->query->folder)
            ->filename($files)
            ->limit(0)
            ->offset(0)
            ->browse();

        foreach ($thumbnails as $thumbnail)
        {
            if ($entity = $context->result->find($thumbnail->filename)) {
                $entity->thumbnail = $thumbnail->thumbnail;
            }
        }

        foreach ($context->result as $entity)
        {
            if (!is_string($entity->thumbnail)) {
                $entity->thumbnail = false;
            }
        }
	}
}
