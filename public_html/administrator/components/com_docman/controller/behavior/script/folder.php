<?php
/**
 * @package     DOCman
 * @copyright   Copyright (C) 2011 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

class ComDocmanControllerBehaviorScriptFolder extends KControllerBehaviorAbstract
{
    protected $_temporary_folder;

    public function __construct(KObjectConfig $config)
    {
        $this->_temporary_folder = JPATH_ROOT.'/tmp';

        parent::__construct($config);
    }

    protected function _initialize(KObjectConfig $config)
    {
        $config->append(array(
                'title'     => 'Category folder relations',
                'jobs'  => [
                    'prepare'        => [
                        'label'      => 'Prepare'
                    ],
                    'create_folders' => [
                        'label'      => 'Create folders',
                        'chunkable'  => true
                    ],
                    'find_mismatches' => [
                        'label'       => 'Find files to move'
                    ],
                    'move_files'     => [
                        'label'      => 'Move files',
                        'chunkable'  => true
                    ],
                    'find_empty_folders' => [
                        'label'      => 'Find empty folders'
                    ],
                    'delete_folders' => [
                        'label'      => 'Delete empty folders',
                        'chunkable'  => true,
                    ],
                ]
            )
        );

        parent::_initialize($config);
    }

    protected function _actionPrepare(KControllerContextInterface $context)
    {
        $behavior = $this->getObject('com://admin/docman.controller.behavior.syncable');
        $behavior->syncFolders();
        $behavior->syncFiles();

        $context->response->setContent(json_encode(['result' => true]));
    }

    protected function _actionFind_empty_folders(KControllerContextInterface $context)
    {
        $behavior = $this->getObject('com://admin/docman.controller.behavior.syncable');
        $behavior->syncFolders();
        $behavior->syncFiles();

        $folders = $behavior->getOrphanFolders(KDatabase::FETCH_FIELD_LIST, function($query) {
            $query->join(array('d' => 'docman_documents'), 'd.storage_path LIKE CONCAT(TRIM(LEADING "/" FROM CONCAT_WS("/", `tbl`.`folder`, `tbl`.`name`)), \'/%\')');
            $query->where('d.docman_document_id IS NULL');
        });

        $result = file_put_contents($this->_temporary_folder.'/delete_list', serialize($folders));

        $context->response->setContent(json_encode(['result' => $result]));
    }

    protected function _actionDelete_folders(KControllerContextInterface $context)
    {
        $offset = (int) $this->getRequest()->getData()->offset;
        $limit  = 10;

        $processed = $errors = [];
        $folders = unserialize(file_get_contents($this->_temporary_folder.'/delete_list'));

        $i = 0;

        foreach ($folders as $path)
        {
            $i++;

            if ($i <= $offset) {
                continue;
            }

            if ($i > $offset + $limit) {
                break;
            }

            list($folder, $name) = $this->_splitPath($path);

            $processed[] = [$folder, $name];

            try {
                $this->_getFolderController()
                    ->container('docman-files')->folder($folder)->name($name)
                    ->delete();
            } catch (UnexpectedValueException $e) {
                // invalid folder, probably because it's already deleted
            } catch (Exception $e) {
                $errors[] = $folder."/".$name." ".$e->getMessage();
            }
        }

        $remaining = max(count($folders) - $offset, 0);
        $offset += $limit;

        if ($remaining == 0) {
            unlink($this->_temporary_folder.'/delete_list');
        }

        $output = array(
            'remaining' => $remaining,
            'completed' => $limit,
            'offset'    => $offset,
            'folders' => $processed,
            'errors' => $errors
        );

        $context->response->setContent(json_encode(['result' => $output]));
    }

    protected function _actionCreate_folders(KControllerContextInterface $context)
    {
        $offset = (int) $this->getRequest()->getData()->offset;
        $limit  = 10;

        $controller = $this->getObject('com://admin/docman.controller.category')->limit($limit)->offset($offset)->sort('title');
        $total      = $controller->getModel()->count();

        $categories = $controller->browse();
        $titles     = [];

        foreach ($categories as $category) {
            if ($category->automatic_folder) {
                continue;
            }

            if ($category->isLocked()) {
                $category->locked_by = $category->locked_on = null;
                $category->save();
            }

            $result   = $controller->id($category->id)->edit(['automatic_folder' => 1]);

            $titles[] = $result->title;
        }

        $offset += $limit;

        $output = array(
            'remaining' => max($total - $offset, 0),
            'completed' => $limit,
            'offset'    => $offset,
            'categories' => $titles
        );

        $context->response->setContent(json_encode(['result' => $output]));
    }

    protected function _actionFind_mismatches(KControllerContextInterface $context)
    {
        $behavior = $this->getObject('com://admin/docman.controller.behavior.syncable');
        $behavior->syncFolders();
        $behavior->syncFiles();

        $behavior = $this->getObject('com://admin/docman.controller.behavior.organizable');

        $list = $behavior->getFolderMismatches(function($query) {
            $query->columns(['SUBSTRING_INDEX(tbl.storage_path, ".", -1) AS extension']);
        });

        $result = file_put_contents($this->_temporary_folder.'/move_list', serialize($list));

        $context->response->setContent(json_encode(['result' => $result]));
    }

    protected function _actionMove_files(KControllerContextInterface $context)
    {
        $offset = (int) $this->getRequest()->getData()->offset;
        $limit  = 20;

        $processed = $errors = [];
        $list = unserialize(file_get_contents($this->_temporary_folder.'/move_list'));
        $controller = $this->_getFileController();

        $i = 0;

        foreach ($list as $file)
        {
            $i++;

            if ($i <= $offset) {
                continue;
            }

            if ($i > $offset + $limit) {
                break;
            }

            $processed[] = [$file->folder, $file->name];

            $name        = $this->_getUniqueFileName($file);
            $destination = array(
                'destination_folder' => $file->destination
            );

            if ($name !== $file->name) {
                $destination['destination_name'] = $name;
            }

            try {
                $controller
                    ->container('docman-files')->folder($file->folder)->name($file->name)
                    ->move($destination);

                $files[] = $file->name.': '.$file->folder.' -> '.$file->destination;
            }
            catch (Exception $e) {
                $errors[] = $file->name.': '.$file->folder.' -> '.$file->destination." ".$e->getMessage();
            }
        }

        $remaining = max(count($list) - $offset, 0);
        $offset += $limit;

        if ($remaining == 0) {
            unlink($this->_temporary_folder.'/move_list');
        }

        $output = array(
            'remaining' => $remaining,
            'completed' => $limit,
            'offset'    => $offset,
            'files' => $processed,
            'errors' => $errors
        );

        $context->response->setContent(json_encode(['result' => $output]));
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

    protected function _getUniqueFileName($file)
    {
        $controller = $this->_getFileController();
        $name       = $file->name;
        $i          = 1;

        while (true)
        {
            $entity = $controller->getModel()->container('docman-files')->folder($file->destination)->name($name)->fetch();

            if (count($entity)) {
                $name = substr_replace($file->name, ' ('.$i.').'.$file->extension, -1*(strlen($file->extension)+1));
                $i++;

                continue;
            }

            break;
        }

        return $name;
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

    /**
     * @return KObjectInterface
     */
    protected function _getFolderController()
    {
        $controller = $this->getObject('com:files.controller.folder', array(
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