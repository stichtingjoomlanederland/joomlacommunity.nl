<?php
/**
 * @package     DOCman
 * @copyright   Copyright (C) 2011 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

/**
 * Used by the node controller to change document paths after moving files
 */
class ComDocmanControllerBehaviorMovable extends KControllerBehaviorAbstract
{
    const TEMP_FOLDER = 'tmp';

    protected $_path_cache = array();

    protected function _afterBrowse(KControllerContextInterface $context)
    {
        foreach ($context->result as $entity)
        {
            if (substr($entity->path, 0, 3) === 'tmp') {
                $context->result->remove($entity);
            }
        }
    }

    /**
     * Automatically create tmp folder before uploading anything into it
     *
     * @param KControllerContextInterface $context
     * @throws Exception
     */
    protected function _beforeAdd(KControllerContextInterface $context)
    {
        if ($this->getMixer()->getIdentifier()->name === 'file'
            && $context->request->data->container === 'docman-files'
            && $context->request->data->folder === static::TEMP_FOLDER)
        {
            $controller = $this->getObject('com:files.controller.folder')->container('docman-files');
            $folder     = $controller->getModel()->name(static::TEMP_FOLDER)->fetch();

            if ($folder->isNew())
            {
                try {
                    $controller->add(array(
                        'container' => 'docman-files',
                        'overwrite' => 1,
                        'name'      => static::TEMP_FOLDER
                    ));
                }
                catch (Exception $e) {
                    if (JDEBUG) throw $e;
                }

            }
        }
    }

    /**
     * Update document modified_on timestamps when the attached file is overwritten
     *
     * @param KControllerContextInterface $context
     */
    protected function _afterAdd(KControllerContextInterface $context)
    {
        if ($context->request->data->overwrite && $context->result instanceof ComFilesModelEntityFile)
        {
            $file = $context->result;

            $documents = $this->getObject('com://admin/docman.model.documents')
                ->storage_path($file->path)->storage_type('file')
                ->limit(100)
                ->fetch();

            if (count($documents))
            {
                foreach($documents as $document)
                {
                    // Force recalculation and hence a save since no other column has changed
                    $document->modified_by = -1;
                    $document->save();
                }
            }
        }
    }


    protected function _beforeMove(KControllerContextInterface $context)
    {
        $entities = $this->getModel()->fetch();

        foreach ($entities as $entity)
        {
            $entity->setProperties($context->request->data->toArray());

            $from = $entity->path;
            $to   = $entity->destination_path;

            if (is_dir($entity->fullpath))
            {
                $from .= '/';
                $to   .= '/';
            }

            $this->_path_cache[$from] = $to;
        }
    }

    /**
     * Updates attached documents of the moved files
     *
     * Uses a database update query directly since moving folders might mean updating hundreds of rows.
     *
     * @param KControllerContextInterface $context
     */
    protected function _afterMove(KControllerContextInterface $context)
    {
        $table = $this->getObject('com://admin/docman.model.documents')->getTable();

        $documents_query = $this->getObject('lib:database.query.update')
            ->table($table->getName())
            ->where("storage_type = :type")->bind(array('type' => 'file'));

        $folders_query = $this->getObject('lib:database.query.update')
            ->table('docman_category_folders');

        foreach ($this->_path_cache as $from => $to)
        {
            $from = ComFilesFilterPath::normalizePath($from);
            $to   = ComFilesFilterPath::normalizePath($to);

            $query = clone $documents_query;
            $query->bind(array('from' => $from, 'to' => $to));

            if (substr($from, -1) === '/') // Move folder
            {
                $query->values('storage_path = REPLACE(storage_path, :from, :to)')
                      ->where('storage_path LIKE :filter')->bind(array('filter' => $from.'%'));
            }
            else // Move file
            {
                $query->values('storage_path = :to')
                      ->where('storage_path = :from');
            }

            $table->getAdapter()->update($query);
        }

        // Move category folders
        foreach ($this->_path_cache as $from => $to)
        {
            if (substr($from, -1) === '/')
            {
                $from = substr($from, 0, -1);
                $to   = substr($to, 0, -1);

                $from = ComFilesFilterPath::normalizePath($from);
                $to   = ComFilesFilterPath::normalizePath($to);

                $query = clone $folders_query;
                $query->bind(array('from' => $from, 'to' => $to));

                $query->values("folder = CONCAT_WS('/', NULLIF(:to, ''), NULLIF(SUBSTRING(`folder`, LENGTH(:from)+2), ''))")
                    ->where('folder LIKE :filter')->bind(array('filter' => $from.'%'));

                $table->getAdapter()->update($query);
            }
        }
    }
}