<?php
/**
 * @package     DOCman
 * @copyright   Copyright (C) 2011 - 2014 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

class ComDocmanControllerBehaviorScriptScan extends KControllerBehaviorAbstract
{
    protected $_temporary_folder;

    protected $_temporary_file;

    protected $_thumbnail_path;

    public function __construct(KObjectConfig $config)
    {
        parent::__construct($config);

        $this->_temporary_folder = JPATH_ROOT.'/tmp';
        $this->_temporary_file   = $this->_temporary_folder.'/scan_list';
        $this->_thumbnail_path   = $this->getObject('com:files.model.containers')->slug('docman-images')->fetch()->fullpath;
    }

    protected function _initialize(KObjectConfig $config)
    {
        /** @var callable $translator */
        $translator = $this->getObject('translator');

        $config->append([
            'title'     => $translator('Scan documents'),
            'jobs'      => [
                'clear_thumbnails' => [
                    'label'       => $translator('Clear broken thumbnails')
                ],
                'clear_scans' => [
                    'label'       => $translator('Clear pending scans'),
                    'chunkable'   => true
                ],
                'find_documents' => [
                    'label'       => $translator('Find documents to scan')
                ],
                'scan_documents' => [
                    'label'       => $translator('Queue documents for scanning'),
                    'chunkable'   => true
                ]
            ]
        ]);

        parent::_initialize($config);
    }

    protected function _actionClear_thumbnails(KControllerContextInterface $context)
    {
        $query = $this->getObject('database.query.select')
            ->columns(['tbl.image'])
            ->table(['tbl' => 'docman_documents'])
            ->where('tbl.image <> :image')
            ->bind([
                'image' => ''
            ]);

        $list    = $this->getObject('database.adapter.mysqli')->select($query, KDatabase::FETCH_FIELD_LIST);
        $missing = [];

        foreach ($list as $image) {
            if (!is_file($this->_thumbnail_path.'/'.$image)) {
                $missing[] = $image;
            }
        }

        $missing = array_unique($missing);
        $count   = count($missing);
        $offset  = 0;
        $limit   = 10;
        $query   = $this->getObject('database.query.update')
            ->table('docman_documents')
            ->values('image = :empty')
            ->where('image IN :image');

        while ($offset < $count)
        {
            $current  = array_slice($missing, $offset, $limit);

            $query->bind(['empty' => '', 'image' => $current]);

            $this->getObject('database.adapter.mysqli')->update($query);

            $offset += $limit;
        }

        $context->response->setContent(json_encode(['result' => true]));
    }

    protected function _actionClear_scans(KControllerContextInterface $context)
    {
        $offset = (int) $this->getRequest()->getData()->offset;
        $limit  = 50;

        $controller = $this->getObject('com://admin/docman.controller.scan');
        $controller->limit($limit)->offset($offset);

        if ($controller->getModel()->count()) {
            $controller->delete();
        }

        $remaining = $controller->getModel()->count();
        $offset   += $limit;

        $output = array(
            'remaining' => $remaining,
            'completed' => $limit,
            'offset'    => $offset
        );

        $context->response->setContent(json_encode(['result' => $output]));
    }

    protected function _actionFind_documents(KControllerContextInterface $context)
    {
        if (!$this->getObject('com://admin/docman.controller.behavior.scannable')->isSupported()) {
            $error = $this->getObject('translator')->translate('Scanning documents is only available with Joomlatools Connect');
            $context->response->setStatus(500);
            $context->response->setContent(json_encode(['result' => !$error, 'error' => $error]));

            return;
        }

        $query = $this->getObject('database.query.select')
            ->columns(['tbl.docman_document_id'])
            ->table(['tbl' => 'docman_documents'])
            ->join(['scan' => 'docman_scans'], 'scan.identifier = tbl.uuid')
            ->where('scan.identifier IS NULL')
            ->join(['contents' => 'docman_document_contents'], 'contents.docman_document_id = tbl.docman_document_id')
            ->where('tbl.storage_type = :storage_type')
            ->where('((contents.docman_document_id IS NULL AND SUBSTRING_INDEX(tbl.storage_path, ".", -1) IN :ocr_extensions)')
            ->where('("1" = :automatic_thumbnails AND tbl.image = :image AND SUBSTRING_INDEX(tbl.storage_path, ".", -1) IN :thumbnail_extensions))', 'OR')
            ->bind([
                'image' => '',
                'automatic_thumbnails' => $this->getObject('com://admin/docman.model.configs')->fetch()->thumbnails ? '1' : '2',
                'storage_type' => 'file',
                'thumbnail_extensions' => ComDocmanControllerBehaviorScannable::$thumbnail_extensions,
                'ocr_extensions' => ComDocmanControllerBehaviorScannable::$ocr_extensions
            ]);

        $list = $this->getObject('database.adapter.mysqli')->select($query, KDatabase::FETCH_FIELD_LIST);

        file_put_contents($this->_temporary_file, serialize(array_unique($list)));

        $context->response->setContent(json_encode(['result' => true]));
    }

    protected function _actionScan_documents(KControllerContextInterface $context)
    {
        $offset = (int) $this->getRequest()->getData()->offset;
        $limit  = 1;

        $processed = $errors = [];
        $documents = unserialize(file_get_contents($this->_temporary_file));
        $controller = $this->getObject('com://admin/docman.controller.document');

        $i = 0;

        foreach ($documents as $id)
        {
            $i++;

            if ($i <= $offset) {
                continue;
            }

            if ($i > $offset + $limit) {
                break;
            }

            $processed[] = $id;

            $entity = $controller->id($id)->read();

            if ($entity->isNew()) {
                continue;
            }

            try {
                $controller->id($id)->edit(['force_scan' => 1]);
                sleep(5);
            } catch (Exception $e) {
                $errors[] = $id." ".$e->getMessage();
            }
        }

        $remaining = max(count($documents) - $offset, 0);
        $offset += $limit;

        if ($remaining == 0) {
            unlink($this->_temporary_file);
        }

        $output = array(
            'remaining' => $remaining,
            'completed' => $limit,
            'offset'    => $offset,
            'documents' => $processed,
            'errors'    => $errors
        );

        $context->response->setContent(json_encode(['result' => $output]));
    }

}