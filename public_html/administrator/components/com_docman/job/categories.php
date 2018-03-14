<?php
/**
 * @package    DOCman
 * @copyright   Copyright (C) 2011 Timble CVBA (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

class ComDocmanJobCategories extends ComSchedulerJobAbstract
{
    const NO_CATEGORY = -1;

    const NO_OWNER = -2;

    const NO_FOLDER = -3;

    protected function _initialize(KObjectConfig $config)
    {
        $config->append(array(
            'frequency' => ComSchedulerJobInterface::FREQUENCY_EVERY_FIVE_MINUTES
        ));

        parent::_initialize($config);
    }

    public function run(ComSchedulerJobContextInterface $context)
    {
        if (!$this->getObject('com://admin/docman.model.entity.config')->automatic_category_creation) {
            $context->log('Automatic category creation is turned off in global configuration');

            return $this->skip();
        }

        if (!$this->getObject('com://admin/docman.model.entity.config')->default_owner) {
            $context->log('No default owner selected in global configuration');

            return $this->skip();
        }

        $state = $context->getState();
        $queue = KObjectConfig::unbox($state->queue);

        $context->log(count($queue).' folders in the queue');

        if (is_array($queue))
        {
            $limit = 5; // only create 5 documents per run to limit memory errors
            while ($context->hasTimeLeft() && count($queue) && $limit)
            {
                $path = array_shift($queue);

                $context->log('Creating category for the path '.$path);

                $result = $this->_createCategory($path);

                if ($result === false) {
                    $queue[] = $path; // add to the end of queue to retry later

                    $context->log('Failed to create category for '.$path);
                }
                else if ($result === static::NO_FOLDER) {
                    $context->log('Folder is missing in the filesystem: '.$path);
                }
                else if ($result === static::NO_CATEGORY) {
                    $context->log('No category selected for the parent of '.$path);
                }
                else if ($result === static::NO_OWNER) {
                    $context->log('No default owner selected in global configuration');
                } else {
                    $context->log('Created category for the path '.$path);
                }

                $limit--;
            }
        }

        if (empty($queue) && $context->hasTimeLeft())
        {
            $behavior = $this->getObject('com://admin/docman.controller.behavior.syncable');
            $behavior->syncFolders();

            /*
             * Add folders to the queue if and only if:
             * 1- It is not attached to a category
             * 2- There is not a document linking to the folder. (this makes sure we don't break existing category structures)
             * 3- It is not the tmp folder
             * 4- The parent folder has a category linked to it
             */
            $queue = $behavior->getOrphanFolders(KDatabase::FETCH_FIELD_LIST, function($query) {
                $query->limit(100);
                $query->join(array('d' => 'docman_documents'), 'd.storage_path LIKE CONCAT(TRIM(LEADING "/" FROM CONCAT_WS("/", `tbl`.`folder`, `tbl`.`name`)), \'/%\')');
                $query->where('d.docman_document_id IS NULL');

                // Skip tmp folder on root
                $query->where('(tbl.name <> :tmp OR tbl.folder <> :empty)')->bind([
                    'tmp' => ComDocmanControllerBehaviorMovable::TEMP_FOLDER,
                    'empty' => ''
                ]);

                // Skip folders with no parents
                $query->join(['parent_folder' => 'docman_category_folders'], 'parent_folder.folder = tbl.folder');
                $query->where('(parent_folder.folder IS NOT NULL OR tbl.folder = :empty)');
            });

            $context->log(sprintf('Added %s orphans to the queue', count($queue)));
        }

        $state->queue = (array) $queue;

        return empty($queue) ? $this->complete() : $this->suspend();
    }

    /**
     * Checks if the physical folder is still there
     *
     * @param string $path
     * @return bool
     */
    protected function _folderExists($path)
    {
        $basepath = $this->getObject('com:files.model.containers')->slug('docman-files')->fetch()->fullpath;

        return !empty($path) && is_dir($basepath.'/'.$path);
    }

    protected function _createCategory($path)
    {
        list($folder, $name) = $this->_splitPath($path);

        if (!$this->_folderExists($path)) {
            return static::NO_FOLDER;
        }

        if ($this->getObject('com://admin/docman.model.categories')->folder($path)->count()) {
            return true;
        }

        if ($folder)
        {
            $category = $this->getObject('com://admin/docman.model.categories')->folder($folder)->fetch();

            if (count($category) !== 1 || $category->isNew()) {
                return static::NO_CATEGORY;
            }

            $parent_id  = $category->id;
            $created_by = $category->created_by;
        }
        else {
            $parent_id  = null;
            $created_by = $this->getObject('com://admin/docman.model.entity.config')->default_owner;

            if (!$created_by) {
                return static::NO_OWNER;
            }
        }

        if ($this->getObject('com://admin/docman.model.entity.config')->automatic_humanized_titles) {
            $title = $this->getObject('com://admin/docman.template.helper.string')->humanize(array(
                'string' => $name,
                'strip_extension' => true
            ));
        } else {
            $title = $name;
        }

        $details = array(
            'title' => $title,
            'created_by' => $created_by,
            'parent_id' => $parent_id,
            'folder' => $path,
            'automatic_folder' => 1
        );

        $result = $this->getObject('com://admin/docman.controller.category', array(
            'request'   => $this->getObject('request'),
            'behaviors' => array(
                'permissible' => array(
                    'permission' => 'com://admin/docman.controller.permission.yesman'
                )
            )
        ))->add($details);

        return $result instanceof KDatabaseRowInterface ? !$result->isNew() : false;
    }
    
    protected function _splitPath($path)
    {
        $folder = pathinfo($path, PATHINFO_DIRNAME);
        $name   = ltrim(basename(' '.strtr($path, array('/' => '/ '))));

        if ($folder === '.') {
            $folder = '';
        }

        return array($folder, $name);
    }
}