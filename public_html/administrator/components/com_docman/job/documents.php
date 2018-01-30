<?php
/**
 * @package    DOCman
 * @copyright   Copyright (C) 2011 Timble CVBA (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

class ComDocmanJobDocuments extends ComSchedulerJobAbstract
{
    const NO_CATEGORY = -1;

    const NO_FILE = -2;

    const HAS_VERSION = -3;

    protected function _initialize(KObjectConfig $config)
    {
        $config->append(array(
            'frequency' => ComSchedulerJobInterface::FREQUENCY_EVERY_FIVE_MINUTES
        ));

        parent::_initialize($config);
    }

    public function run(ComSchedulerJobContextInterface $context)
    {
        if (!$this->getObject('com://admin/docman.model.entity.config')->automatic_document_creation) {
            $context->log('Automatic document creation is turned off in global configuration');

            return $this->skip();
        }

        $state = $context->getState();
        $queue = KObjectConfig::unbox($state->queue);

        $context->log(count($queue).' files in the queue');

        if (is_array($queue))
        {
            $limit = 5; // only create 5 documents per run to limit memory errors
            while ($context->hasTimeLeft() && count($queue) && $limit)
            {
                $path = array_shift($queue);

                $context->log('Creating document for the path '.$path);

                $result = $this->_createDocument($path);

                if ($result === false) {
                    $queue[] = $path; // add to the end of queue to retry later

                    $context->log('Failed to create document for '.$path);
                }
                else if ($result === static::HAS_VERSION) {
                    $context->log('File has an older version in the filesystem: '.$path);
                }
                else if ($result === static::NO_FILE) {
                    $context->log('File is missing in the filesystem: '.$path);
                }
                else if ($result === static::NO_CATEGORY) {
                    $context->log('No category selected for '.$path);
                } else {
                    $context->log('Created document for the path '.$path);
                }

                $limit--;
            }
        }

        if (empty($queue) && $context->hasTimeLeft()) {
            $behavior = $this->getObject('com://admin/docman.controller.behavior.syncable');
            $behavior->syncFolders();
            $behavior->syncFiles();
            $queue = $behavior->getOrphanFiles(KDatabase::FETCH_FIELD_LIST, function($query) {
                $query->limit(100);
                $query->join(array('cf' => 'docman_category_folders'), 'cf.folder = tbl.folder')
                      ->where('cf.folder IS NOT NULL');
                $query->where('tbl.folder <> \'\'');
                $query->where('tbl.folder <> :tmp')->bind(['tmp' => ComDocmanControllerBehaviorMovable::TEMP_FOLDER]);
            });

            $context->log(sprintf('Added %s orphans to the queue', count($queue)));
        }

        $state->queue = (array) $queue;

        return empty($queue) ? $this->complete() : $this->suspend();
    }

    /**
     * Checks if the physical file is still there
     *
     * @param string $path
     * @return bool
     */
    protected function _fileExists($path)
    {
        $basepath = $this->getObject('com:files.model.containers')->slug('docman-files')->fetch()->fullpath;

        return !empty($path) && file_exists($basepath.'/'.$path);
    }

    protected function _createDocument($path)
    {
        list($folder, $name) = $this->_splitPath($path);

        if (!$this->_fileExists($path)) {
            return static::NO_FILE;
        }

        // A document has been created for the file already
        if ($this->getObject('com://admin/docman.model.documents')->storage_path($path)->count()) {
            return true;
        }

        $extension = pathinfo($name, PATHINFO_EXTENSION);
        // if the name is a revision like foo (1).txt and foo.txt exists and already has a document
        if (preg_match("#\(\d+\)\.$extension$#i", $name)) {
            $canonical_path = preg_replace("#( \(\d+\)\.$extension)$#i", '.'.$extension, $path);

            if ($this->_fileExists($canonical_path)) {
                return static::HAS_VERSION;
            }
        }

        $category = $this->getObject('com://admin/docman.model.categories')->folder($folder)->fetch();

        if (count($category) !== 1 || $category->isNew()) {
            return static::NO_CATEGORY;
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
            'created_by' => $category->created_by,
            'docman_category_id' => $category->id,
            'storage_type' => 'file',
            'storage_path' => $path,
            'automatic_thumbnail' => 1
        );

        $result = $this->getObject('com://admin/docman.controller.document', array(
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