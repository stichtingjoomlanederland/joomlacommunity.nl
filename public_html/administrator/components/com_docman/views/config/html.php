<?php
/**
 * @package    DOCman
 * @copyright   Copyright (C) 2011 Timble CVBA (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

class ComDocmanViewConfigHtml extends ComDocmanViewHtml
{
    protected function _initialize(KObjectConfig $config)
    {
        $config->append([
            'decorator' => 'koowa'
        ]);

        parent::_initialize($config);
    }

    protected function _fetchData(KViewContext $context)
    {
        $context->data->upload_max_filesize = ComFilesModelEntityContainer::getServerUploadLimit();

        $context->data->filetypes = array(
            'archive' => array('7z', 'ace', 'bz2', 'dmg', 'gz', 'rar', 'tgz', 'zip'),
            'document' => array('csv', 'doc', 'docx', 'html', 'key', 'keynote', 'odp', 'ods', 'odt', 'pages', 'pdf', 'pps', 'ppt', 'pptx', 'rtf', 'tex', 'txt', 'xls', 'xlsx', 'xml'),
            'image' => array('bmp', 'exif', 'gif', 'ico', 'jpeg', 'jpg', 'png', 'psd', 'tif', 'tiff'),
            'audio' => array('aac', 'aif', 'aiff', 'alac', 'amr', 'au', 'cdda', 'flac', 'm3u', 'm3u', 'm4a', 'm4a', 'm4p', 'mid', 'mp3', 'mp4', 'mpa', 'ogg', 'pac', 'ra', 'wav', 'wma'),
            'video' => array('3gp', 'asf', 'avi', 'flv', 'm4v', 'mkv', 'mov', 'mp4', 'mpeg', 'mpg', 'ogg', 'rm', 'swf', 'vob', 'wmv')
        );

        $context->data->connect_support = $this->getObject('com://admin/docman.model.entity.config')->connectAvailable();

        if (substr($this->getLayout(), 0, 5) === 'debug')
        {
            $context->data->pages = $this->getObject('com://admin/docman.model.pages')
                ->language('all')
                ->access(-1)
                ->sort('title')
                ->fetch();

            $context->data->document_count = $this->getObject('com://admin/docman.model.documents')->count();
            $context->data->category_count = $this->getObject('com://admin/docman.model.categories')->count();
            $context->data->user_count = $this->getObject('com://admin/docman.model.users')->count();
            $context->data->folder_count   = $this->getObject('com://admin/docman.model.folders')->tree(true)->count();
            $context->data->file_count     = $this->getObject('com://admin/docman.model.files')->tree(true)->count();
            $context->data->scan_count     = $this->getObject('com://admin/docman.model.scans')->count();
            $context->data->tag_count     = $this->getObject('com://admin/docman.model.tags')->count();

            $context->data->scheduler_log = null;
            $path = rtrim(JFactory::getConfig()->get('log_path'), '/').'/joomlatools-scheduler.php';
            if (file_exists($path)) {
                $context->data->scheduler_log = file_get_contents($path);
            }

            $context->data->jobs = $this->getObject('com:scheduler.model.jobs')->fetch();

            $adapter = $this->getObject('database.adapter.mysqli');

            $query = $this->getObject('database.query.select')
                ->table('scheduler_metadata')
                ->where('type = :type')->bind(['type' => 'metadata']);

            $context->data->scheduler_metadata = $adapter->select($query, KDatabase::FETCH_OBJECT);

            $db    = $this->getObject('lib:database.adapter.mysqli');
            $q     = $this->getObject('lib:database.query.select')
                ->columns('*')->table('extensions')
                ->where('type = :type')->where('folder = :folder')->where('element = :element ')
                ->bind(['type' => 'plugin', 'folder' => 'koowa', 'element' => 'connect']);

            $plugin = $db->select($q, KDatabase::FETCH_OBJECT);

            if ($plugin && isset($plugin->params) && is_string($plugin->params)) {
                $plugin->params = json_decode($plugin->params);
            }

            $context->data->connect = [
                'plugin' => $plugin,
            ];

        }

        try {
            $context->data->license = $this->getObject('license');

            if ($context->data->license->load()) {
                $context->data->has_connect = $context->data->license->hasFeature('connect');
                $context->data->license_error = null;
                $context->data->license_claims = $context->data->license->getToken() ? json_encode($context->data->license->getToken()->getClaims(), JSON_PRETTY_PRINT) : 'error';
            } else {
                $context->data->has_connect = false;
                $context->data->license_error = $context->data->license->getError();
                $context->data->license_claims = 'error';
            }

        } catch (Exception $e) {
            $context->data->license_error = $e->getMessage();
            $context->data->license_claims = 'error';
        }

        parent::_fetchData($context);
    }
}
