<?php
/**
 * @package    DOCman
 * @copyright   Copyright (C) 2011 Timble CVBA (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

class ComDocmanJobFiles extends ComSchedulerJobAbstract
{
    protected function _initialize(KObjectConfig $config)
    {
        $config->append(array(
            'frequency' => ComSchedulerJobInterface::FREQUENCY_DAILY
        ));

        parent::_initialize($config);
    }

    public function run(ComSchedulerJobContextInterface $context)
    {
        $behavior = $this->getObject('com://admin/docman.controller.behavior.syncable');

        $stale_files = $behavior->getOrphanFiles(KDatabase::FETCH_OBJECT_LIST, function($query) {
            $query->columns(['docman_file_id', 'folder', 'name', 'tbl.modified_on'])
                ->limit(10)
                ->where('tbl.modified_on < :modified')
                ->where('tbl.folder = :tmp')
                ->bind([
                    'modified' => date('Y-m-d H:i:s', time() - 60*60*24),
                    'tmp' => ComDocmanControllerBehaviorMovable::TEMP_FOLDER
                ]);
        });

        $context->log(sprintf('Found %d files to delete', count($stale_files)));

        $controller = $this->_getFileController();

        foreach ($stale_files as $file) {
            $controller->container('docman-files')
                ->folder(ComDocmanControllerBehaviorMovable::TEMP_FOLDER)
                ->name($file->name)
                ->delete();

            $context->log(sprintf('Deleted %s', $file->name));
        }

        if (count($stale_files) === 10) {
            return $this->suspend();
        } else {
            return $this->complete();
        }
    }

    protected function _getFileController()
    {
        $controller = $this->getObject('com:files.controller.file', array(
            'behaviors' => array(
                'com://admin/docman.controller.behavior.movable',
                'com://admin/docman.controller.behavior.syncable',
                'permissible' => array(
                    'permission' => 'com://admin/docman.controller.permission.yesman'
                )
            )
        ));

        return $controller;
    }
}