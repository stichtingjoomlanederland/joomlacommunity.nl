<?php
/**
 * @package     DOCman
 * @copyright   Copyright (C) 2012 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

class ComDocmanControllerBehaviorSyncable extends KControllerBehaviorAbstract
{
    protected $_path_cache = array();

    protected function _initialize(KObjectConfig $config)
    {
        $config->append(array(
            'priority'   => self::PRIORITY_LOW,
        ));

        parent::_initialize($config);
    }

    protected function _afterAdd(KControllerContextInterface $context)
    {
        $name   = $this->getMixer()->getIdentifier()->getName();

        try
        {
            if ($name === 'file') {
                $this->addFile($context->result->path, $context->result);
            } else if ($name === 'folder') {
                $this->addFolder($context->result->path);
            }
        }
        catch (Exception $e) {}
    }

    protected function _afterDelete(KControllerContextInterface $context)
    {
        $name = $this->getMixer()->getIdentifier()->getName();

        if ($name === 'file')
        {
            foreach ($context->result as $entity)
            {
                $this->getObject('com://admin/docman.model.files')
                    ->folder($entity->folder)->name($entity->name)
                    ->fetch()->delete();
            }
        }
        else if ($name === 'folder')
        {
            foreach ($context->result as $entity)
            {
                // Using model to fetch rows might return thousands of files in a rowset leading to memory errors
                if ($entity->path)
                {
                    $query = $this->getObject('database.query.delete')
                        ->table('docman_files')
                        ->where('(folder = :folder OR folder LIKE :folder_like)')
                        ->bind(array(
                            'folder' => $entity->path,
                            'folder_like' => $entity->path.'/%'
                        ));

                    $this->getObject('com://admin/docman.database.table.files')->getAdapter()->delete($query);

                    $query = $this->getObject('database.query.delete')
                        ->table('docman_folders')
                        ->where('(folder = :folder OR folder LIKE :folder_like)')
                        ->bind(array(
                            'folder' => $entity->path,
                            'folder_like' => $entity->path.'/%'
                        ));

                    $this->getObject('com://admin/docman.database.table.folders')->getAdapter()->delete($query);

                }

                $this->getObject('com://admin/docman.model.folders')
                    ->folder($entity->folder)->name($entity->name)
                    ->fetch()->delete();
            }
        }
    }

    protected function _beforeMove(KControllerContextInterface $context)
    {
        $entities = $this->getModel()->fetch();

        foreach ($entities as $entity) {
            $entity->setProperties($context->request->data->toArray());

            $this->_path_cache[] = array(
                $entity->getIdentifier()->getName(), array($entity->folder, $entity->name), array($entity->destination_folder, $entity->destination_name)
            );
        }
    }


    protected function _afterMove(KControllerContextInterface $context)
    {
        foreach ($this->_path_cache as $row) {
            list($name, $from, $to) = $row;

            $result = KObjectManager::getInstance()->getObject(sprintf('com://admin/docman.database.table.%ss', $name))->select(array(
                'folder' => (string)$from[0],
                'name'   => (string)$from[1]
            ));

            if (isset($to[0])) {
                $result->folder = $to[0];
            }

            if (isset($to[1])) {
                $result->name = $to[1];
            }

            $result->save();

            // Update children folder and file paths
            if ($name === 'folder')
            {
                $from_path = ($from[0] ? $from[0].'/' : '') . $from[1];
                $to_path   = ($result->folder ? $result->folder.'/' : '') . $result->name;

                $query = $this->getObject('database.query.update')
                    ->values('folder = CONCAT_WS(\'/\', NULLIF(:to, \'\'), NULLIF(SUBSTRING(folder, LENGTH(:from)+2), \'\'))')
                    ->where('folder LIKE CONCAT(:from, \'%\')')
                    ->bind(array('to' => $to_path, 'from' => $from_path));

                $adapter = $this->getObject('com://admin/docman.database.table.folders')->getAdapter();

                $query->table('docman_folders');
                $adapter->update($query);

                $query->table('docman_files');
                $adapter->update($query);
            }
        }
    }

    protected function _beforeRender(KControllerContextInterface $context)
    {
        if ($context->getRequest()->getQuery()->has('revalidate_cache')) {
            $this->syncFolders();
            $this->syncFiles();
        }
    }

    public function syncFiles()
    {
        $list     = $this->getFileList();
        $path     = $this->getObject('com:files.model.containers')->slug('docman-files')->fetch()->fullpath;
        $exclude  = array('.', '..', '.svn', '.htaccess', 'web.config', '.git', 'CVS', 'index.html', '.DS_Store', 'Thumbs.db', 'Desktop.ini');
        $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path), RecursiveIteratorIterator::CHILD_FIRST);
        $insert   = array();

        foreach ($iterator as $file)
        {
            if ($file->isDir() || in_array($file->getFilename(), $exclude) || substr($file->getFilename(), 0, 1) === '.') {
                continue;
            }

            $name = str_replace('\\', '/', $file->getPathname());
            $name = str_replace($path.'/', '', $name);

            if (preg_match('#(\/|^)\.[^\/\.]#i', $name)) {
                continue;
            }

            if (!isset($list[$name])) {
                $insert[] = $name;
            }
            else {
                // file is in the list, unset it, so the rest of list is deleted files
                unset($list[$name]);
            }
        }

        // Delete stale entries
        if (count($list))
        {
            $query = $this->getObject('database.query.delete')
                ->table('docman_files')
                ->where('docman_file_id IN :id')->bind(array('id' => $list));

            $this->getObject('com://admin/docman.database.table.files')->getAdapter()->delete($query);
        }

        // Add new files
        if (count($insert))
        {
            $query = $this->getObject('database.query.insert')
                ->table('docman_files')
                ->columns(array('folder', 'name', 'modified_on'));

            $query_count = 0;

            for ($i = 0, $count = count($insert); $i < $count; $i++)
            {
                $file = $insert[$i];

                $query->values(array_merge($this->_splitPath($file), [$this->_getModifiedTime($path.'/'.$file)]));

                $query_count++;

                if ($query_count == 100 || $i == $count-1)
                {
                    $once = 1;
                    $string = str_replace('INSERT', 'INSERT IGNORE', $query->toString(), $once);

                    $this->getObject('lib:database.adapter.mysqli')->execute($string);

                    $query->values = array();
                    $query_count = 0;
                }
            }
        }
    }

    public function syncFolders()
    {
        $list     = $this->getFolderList();
        $path     = $this->getObject('com:files.model.containers')->slug('docman-files')->fetch()->fullpath;
        $exclude  = array('.', '..', '.svn', '.htaccess', 'web.config', '.git', 'CVS', 'index.html', '.DS_Store', 'Thumbs.db', 'Desktop.ini');
        $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path), RecursiveIteratorIterator::CHILD_FIRST);
        $insert   = array();

        foreach ($iterator as $file)
        {
            if (!$file->isDir() || in_array($file->getFilename(), $exclude) || substr($file->getFilename(), 0, 1) === '.') {
                continue;
            }

            $name = str_replace('\\', '/', $file->getPathname());
            $name = str_replace($path.'/', '', $name);

            if (preg_match('#(\/|^)\.[^\/\.]#i', $name)) {
                continue;
            }

            if (!isset($list[$name])) {
                $insert[] = $name;
            }
            else {
                // file is in the list, unset it, so the remaining files in the list are deleted files
                unset($list[$name]);
            }
        }

        // Delete stale entries
        if (count($list))
        {
            $query = $this->getObject('database.query.delete')
                ->table('docman_folders')
                ->where('docman_folder_id IN :id')->bind(array('id' => $list));

            $this->getObject('com://admin/docman.database.table.folders')->getAdapter()->delete($query);
        }

        // Add new files
        if (count($insert))
        {
            $query = $this->getObject('database.query.insert')
                ->table('docman_folders')
                ->columns(array('folder', 'name', 'modified_on'));

            $query_count = 0;

            for ($i = 0, $count = count($insert); $i < $count; $i++)
            {
                $file = $insert[$i];

                $query->values(array_merge($this->_splitPath($file), [$this->_getModifiedTime($path.'/'.$file)]));

                $query_count++;

                if ($query_count == 100 || $i == $count-1)
                {
                    $once = 1;
                    $string = str_replace('INSERT', 'INSERT IGNORE', $query->toString(), $once);

                    $this->getObject('lib:database.adapter.mysqli')->execute($string);

                    $query->values = array();
                    $query_count = 0;
                }
            }
        }
    }

    protected function _getModifiedTime($path)
    {
        $modified = @filemtime($path);

        if ($modified) {
            $modified = date('Y-m-d H:i:s', $modified);
        }

        return $modified;
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

    public function getFolderList()
    {
        $query = KObjectManager::getInstance()->getObject('database.query.select')
            ->columns(array('docman_folder_id', 'path' => 'CONCAT_WS("/", NULLIF(folder, ""), name)'))
            ->table('docman_folders');

        $result = $this->getObject('database.adapter.mysqli')->execute($query, KDatabase::RESULT_USE);

        $array = array();

        while ($row = $result->fetch_object()) {
            $array[$row->path] = $row->docman_folder_id;
        }

        $result->free();

        return $array;
    }

    public function getFileList()
    {
        $query = $this->getObject('database.query.select')
            ->columns(array('docman_file_id', 'path' => 'CONCAT_WS("/", NULLIF(folder, ""), name)'))
            ->table('docman_files');

        $result = $this->getObject('database.adapter.mysqli')->execute($query, KDatabase::RESULT_USE);

        $array = array();

        while ($row = $result->fetch_object()) {
            $array[$row->path] = $row->docman_file_id;
        }

        $result->free();

        return $array;
    }

    public function getOrphanFiles($mode = KDatabase::FETCH_FIELD_LIST, $callback = null)
    {
        /** @var KDatabaseQuerySelect $query */
        $query = $this->getObject('database.query.select');

        $query->columns(array('path' => 'TRIM(LEADING "/" FROM CONCAT_WS("/", tbl.folder, tbl.name))'))
            ->table(array('tbl' => 'docman_files'))
            ->join(array('d' => 'docman_documents'), 'd.storage_path = TRIM(LEADING "/" FROM CONCAT_WS("/", tbl.folder, tbl.name))')
            ->where('d.docman_document_id IS  NULL')
            ->order('path');

        if (is_callable($callback)) {
            call_user_func($callback, $query);
        }

        $results = $this->getObject('com://admin/docman.database.table.files')->select($query, $mode);

        return $results;
    }

    public function getOrphanFolders($mode = KDatabase::FETCH_FIELD_LIST, $callback = null)
    {
        /** @var KDatabaseQuerySelect $query */
        $query = $this->getObject('database.query.select');

        $query->columns(array('path' => 'TRIM(LEADING "/" FROM CONCAT_WS("/", tbl.folder, tbl.name))'))
            ->table(array('tbl' => 'docman_folders'))
            ->join(array('cf' => 'docman_category_folders'), 'cf.folder = TRIM(LEADING "/" FROM CONCAT_WS("/", tbl.folder, tbl.name))')
            ->where('cf.docman_category_id IS  NULL')
            ->order('path');

        if (is_callable($callback)) {
            call_user_func($callback, $query);
        }

        $results = $this->getObject('com://admin/docman.database.table.folders')->select($query, $mode);

        return $results;
    }

    public function addFile($path, $entity = null)
    {
        list($folder, $name) = $this->_splitPath($path);

        $row = $this->getObject('com://admin/docman.database.table.files')->createRow();
        $row->folder = $folder;
        $row->name   = $name;
        $row->modified_on = $entity && $entity->modified_date ? date('Y-m-d H:i:s', $entity->modified_date) : null;

        return $row->save();
    }

    public function addFolder($path)
    {
        list($folder, $name) = $this->_splitPath($path);

        $table = $this->getObject('com://admin/docman.database.table.folders');

        if (!$table->count(['folder' => $folder, 'name' => $name]))
        {
            $row = $table->createRow();
            $row->folder = $folder;
            $row->name   = $name;

            return $row->save();
        }

        return true;
    }
}
