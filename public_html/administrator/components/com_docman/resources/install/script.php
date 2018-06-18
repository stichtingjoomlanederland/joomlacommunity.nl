<?php
/**
 * @package    DOCman
 * @copyright   Copyright (C) 2011 Timble CVBA (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

require_once __DIR__.'/helper.php';

class com_docmanInstallerScript extends JoomlatoolsInstallerHelper
{
    /**
     * Overridden to use a different privilege check
     *
     * @param array $privileges An array containing the privileges to be checked.
     *
     * @return array True An array containing the privileges that didn't pass the test, i.e. not granted.
     */
    protected function _checkDatabasePrivileges($privileges)
    {
        $db     = JFactory::getDbo();
        $failed = array();
        $rand   = rand(1, 1000);
        $view   = sprintf('#__docman_dummy_view_%d', $rand);
        $table  = sprintf('#__docman_dummy_table_%d', $rand);

        // Check CREATE VIEW privilege
        try {
            $db->setQuery("CREATE TABLE $table (dummy TINYINT(1))")->execute();
            $db->setQuery("ALTER TABLE $table CHANGE dummy dummy TINYINT(2)")->execute();
        } catch (JDatabaseExceptionExecuting $e) {
            $failed[] = 'ALTER';
        }

        // Check ALTER privilege
        try {
            $db->setQuery("CREATE VIEW $view AS SELECT 1")->execute();

            $result = $db->setQuery("SELECT * FROM $view")->loadResult();

            if ($result != '1') {
                throw new JDatabaseExceptionExecuting('Result not correct');
            }
        } catch (JDatabaseExceptionExecuting $e) {
            $failed[] = 'CREATE VIEW';
        }

        // Cleanup
        try {
            $db->setQuery("DROP VIEW IF EXISTS $view")->execute();
            $db->setQuery("DROP TABLE IF EXISTS $table")->execute();
        } catch (Exception $e) {}

        return $failed;
    }

    public function getRequiredDatabasePrivileges()
    {
        return array('ALTER', 'CREATE VIEW');
    }

    public function getSystemErrors($type, $installer)
    {
        $errors = $this->_handleDocman16($type);

        if ($type === 'update' && version_compare($this->old_version, '2.1.0', '<')) {
            $errors[] = JText::_('Please first upgrade to DOCman 2.1 and then install DOCman 3. This will ensure your data is properly migrated.');
        }

        if (!$errors && $type !== 'update')
        {
            jimport('joomla.filesytem.file');
            jimport('joomla.filesytem.folder');

            $path = JPATH_ROOT.'/joomlatools-files';
            if (JFolder::exists($path))
            {
                // Try to write a file
                $test  = $path.'/removethisfile';
                $blank = '';
                if (!JFile::write($test, $blank)) {
                    $errors[] = JText::_('Document path is not writable. Please make sure that joomlatools-files folder in your site root is writable.');
                }
                elseif (JFile::exists($test)) {
                    JFile::delete($test);
                }

            }
            elseif (!JFolder::create($path))
            {
                $errors[] = JText::_('Document path cannot be automatically created. Please create a folder named joomlatools-files in your site root and make sure it is writable.');
            }
        }

        return $errors;
    }

    protected function _handleDocman16($type)
    {
        $errors = array();

        // If user has Docman 1.x installed, stop the installation
        if ($type === 'update' && file_exists(JPATH_ADMINISTRATOR.'/components/com_docman/docman.class.php'))
        {
            $errors[] = JText::_('It seems that you have DOCman 1.6 installed. In order to install DOCman 3, you need to migrate your documents using our <a href=http://www.joomlatools.com/support/forums/topic/3363-how-to-migrate-from-docman-1x-to-docman-20 target=_blank>migrator</a>.');
        }
        else
        {
            // If user used to have Docman 1.x installed, Docman leaves some tables around so back them up
            $tables = array(
                '#__docman',
                '#__docman_groups',
                '#__docman_history',
                '#__docman_licenses',
                '#__docman_log'
            );

            // Special case for docman_categories since it also exists for 2.0
            $db = JFactory::getDbo();
            $db->setQuery('SHOW TABLES LIKE '.$db->quote($db->replacePrefix('#__docman_categories')));
            if ($db->loadResult())
            {
                $fields = $db->getTableColumns('#__docman_categories');
                if (isset($fields['parent_id']) || isset($fields['section'])) {
                    $tables[] = '#__docman_categories';
                }
            }

            $result = true;
            foreach ($tables as $table)
            {
                if (!$this->_backupTable($table))
                {
                    $result = false;
                    break;
                }
            }

            if (!$result)
            {
                $errors[] = JText::_('Unable to backup and remove old Docman database tables.');
            }
        }

        return $errors;
    }

    public function afterInstall($type, $installer)
    {
        if ($type === 'update')
        {
            $this->_removeOldScheduler();
            $this->_updateRedirectPlugin($installer);

            if ($this->old_version && version_compare($this->old_version, '3.0.0', '<')) {
                $this->_updateNotifyPlugin($installer);
            }
        }

        $this->_createFilesContainer();
        $this->_createIconsContainer();
        $this->_createImagesContainer();

        if ($type === 'install')
        {
            // Set default config options
            $config = KObjectManager::getInstance()->getObject('com://admin/docman.model.entity.config');
            $config->can_edit_own = 1;
            $config->can_delete_own = 1;
            $config->can_create_tag = 1;

            $config->automatic_document_creation = 0;
            $config->automatic_category_creation = 0;
            $config->automatic_humanized_titles = 1;

            $config->default_owner = JFactory::getUser()->id;

            $config->save();

            // Add a rule to authorize Public group to download
            $asset = JTable::getInstance('Asset');
            $asset->loadByName('com_docman');

            $rules = new JAccessRules($asset->rules);
            $rules->mergeAction('com_docman.download', new JAccessRule(array(1 => true)));
            $asset->rules = (string) $rules;

            if ($asset->check()) {
                $asset->store();
            }

            // Disable finder plugin by default
            $finder_id = $this->getExtensionId(array(
                'type'    => 'plugin',
                'element' => 'docman',
                'folder'  => 'finder',
            ));

            if ($finder_id)
            {
                $query = sprintf(/** @lang text */'UPDATE #__extensions SET enabled = 0 WHERE extension_id = %d', $finder_id);
                JFactory::getDbo()->setQuery($query)->query();
            }
        }

        $products = array(
            'FILEman' => $this->_getComponentVersion('fileman'),
            'LOGman' => $this->_getComponentVersion('logman')
        );
        $incompatible = array();

        foreach ($products as $product => $version) {
            if ($version && version_compare($version, '3.0.0-rc.1', '<')) {
                $incompatible[] = $product.' '.$version;
            }
        }

        if (count($incompatible)) {
            $warning = 'This is important! You need to upgrade %s to 3.0 too or your site will break. Please go to <a target="_blank" href="https://joomlatools.com">https://joomlatools.com</a> and download the latest versions.';
            JFactory::getApplication()->enqueueMessage(sprintf($warning, implode(' and ', $incompatible)), 'warning');
        }
    }

    public function update($installer)
    {
        parent::update($installer);

        // 3.0.0-beta.2
        if (!$this->_columnExists('docman_documents', 'ordering'))
        {
            $queries = [
                "ALTER TABLE `#__docman_documents` ADD `ordering` int(11) NOT NULL default 0;",
                "SET @order := 0;",
                "UPDATE `#__docman_documents` SET `ordering` = (@order := @order + 1) ORDER BY `docman_category_id`, `docman_document_id`;"
            ];
            foreach ($queries as $query) {
                $this->_executeQuery($query);
            }
        }

        // 3.0.5-beta.1
        if ($this->_indexExists('docman_files', 'path')) {
            $this->_executeQuery("ALTER TABLE `#__docman_files` DROP INDEX `path`");
        }

        if ($this->_indexExists('docman_folders', 'path')) {
            $this->_executeQuery("ALTER TABLE `#__docman_folders` DROP INDEX `path`");
        }

        // 3.1.0-beta.1
        if (!$this->_columnExists('docman_scans', 'response')) {
            $this->_executeQuery("ALTER TABLE `#__docman_scans` ADD `response` varchar(2048) NOT NULL DEFAULT ''");
        }
    }

    public function uninstall($installer)
    {
        parent::uninstall($installer);

        $db = JFactory::getDbo();
        $db->setQuery('SHOW TABLES LIKE '.$db->quote($db->replacePrefix('#__files_containers')));
        if ($db->loadResult()) {
            $db->setQuery("DELETE FROM `#__files_containers` WHERE `slug` = 'docman-files'");
            $db->query();
            $db->setQuery("DELETE FROM `#__files_containers` WHERE `slug` = 'docman-icons'");
            $db->query();
            $db->setQuery("DELETE FROM `#__files_containers` WHERE `slug` = 'docman-images'");
            $db->query();
        }
    }

    protected function _removeOldScheduler()
    {
        $extension_id = $this->getExtensionId(array(
            'type'    => 'plugin',
            'element' => 'scheduler',
            'folder'  => 'system'
        ));

        if ($extension_id)
        {
            $this->_setCoreExtension($extension_id, 0);

            $i = new JInstaller();
            $i->uninstall('plugin', $extension_id);
        }
    }

    protected function _updateRedirectPlugin($installer)
    {
        $plugin_exists = $this->getExtensionId(array(
            'type'    => 'plugin',
            'element' => 'docman_redirect',
            'folder'  => 'system'
        ));

        if ($plugin_exists)
        {
            $path = $installer->getParent()->getPath('source').'/extensions/plg_system_docman_redirect';
            $instance = new JInstaller();
            $instance->install($path);
        }
    }

    protected function _updateNotifyPlugin($installer)
    {
        $plugin_exists = $this->getExtensionId(array(
            'type'    => 'plugin',
            'element' => 'notify',
            'folder'  => 'docman'
        ));

        if ($plugin_exists)
        {
            $path = $installer->getParent()->getPath('source').'/extensions/plg_docman_notify';
            $instance = new JInstaller();
            $instance->install($path);
        }
    }

    protected function _clearCache()
    {
        parent::_clearCache();

        if ($this->old_version && class_exists('Koowa') && class_exists('KObjectManager'))
        {
            try {
                $config = KObjectManager::getInstance()->getObject('com://admin/docman.model.entity.config');
                $config->cleancache();
            }
            catch (Exception $e) {}
        }
    }

    protected function _createFilesContainer()
    {
        $entity = KObjectManager::getInstance()->getObject('com:files.model.containers')->slug('docman-files')->fetch();

        if ($entity->isNew())
        {
            $thumbnails = true;

            if (!extension_loaded('gd'))
            {
                $thumbnails = false;
                $translator = KObjectManager::getInstance()->getObject('translator');
                JFactory::getApplication()->enqueueMessage($translator->translate('Your server does not have the necessary GD image library for thumbnails.'));
            }

            $extensions = explode(',', 'csv,doc,docx,html,key,keynote,odp,ods,odt,pages,pdf,pps,ppt,pptx,rtf,tex,txt,xls,xlsx,xml,7z,ace,bz2,dmg,gz,rar,tgz,zip,bmp,exif,gif,ico,jpeg,jpg,png,psd,tif,tiff,aac,aif,aiff,alac,amr,au,cdda,flac,m3u,m3u,m4a,m4a,m4p,mid,mp3,mp4,mpa,ogg,pac,ra,wav,wma,3gp,asf,avi,flv,m4v,mkv,mov,mp4,mpeg,mpg,ogg,rm,swf,vob,wmv');

            $entity->create(array(
                'slug' => 'docman-files',
                'path' => 'joomlatools-files/docman-files',
                'title' => 'DOCman',
                'parameters' => array(
                    'allowed_extensions' => $extensions,
                    'allowed_mimetypes' => array("image/jpeg", "image/gif", "image/png", "image/bmp", "application/x-shockwave-flash", "application/msword", "application/excel", "application/pdf", "application/powerpoint", "text/plain", "application/x-zip"),
                    'maximum_size' => 0,
                    'thumbnails' => $thumbnails
                )
            ));
            $entity->save();
        }

        jimport('joomla.filesystem.folder');
        jimport('joomla.filesystem.file');

        if (!$entity->isNew() && $entity->path)
        {
            $path = JPATH_ROOT.'/'.$entity->path;
            if (!JFolder::exists($path))
            {
                if (!JFolder::create($path)) {
                    JFactory::getApplication()->enqueueMessage(JText::_('Document path cannot be automatically created. Please create the folder structure joomlatools-files/docman-files in your site root.'), 'error');
                }
            }

            if (!JFile::exists($path.'/.htaccess')) {
                $buffer ='DENY FROM ALL';
                JFile::write($path.'/.htaccess', $buffer);
            }

            if (!JFile::exists($path.'/web.config')) {
                $buffer ='<?xml version="1.0" encoding="utf-8" ?>
<system.webServer>
    <security>
        <authorization>
            <remove users="*" roles="" verbs="" />
            <add accessType="Allow" roles="Administrators" />
        </authorization>
    </security>
</system.webServer>';
                JFile::write($path.'/web.config', $buffer);
            }
        }
    }

    protected function _createIconsContainer()
    {
        $entity = KObjectManager::getInstance()->getObject('com:files.model.containers')->slug('docman-icons')->fetch();
        $path = 'joomlatools-files/docman-icons';

        if ($entity->isNew())
        {
            $entity->create(array(
                'slug' => 'docman-icons',
                'path' => $path,
                'title' => 'DOCman Icons',
                'parameters' => array(
                    'allowed_extensions' => explode(',', 'bmp,gif,jpeg,jpg,png'),
                    'allowed_mimetypes' => array("image/jpeg", "image/gif", "image/png", "image/bmp"),
                    'maximum_size' => 0,
                    'thumbnails' => true
                )
            ));
            $entity->save();
        }

        jimport('joomla.filesystem.folder');
        jimport('joomla.filesystem.file');

        if (!JFolder::exists(JPATH_ROOT.'/'.$path)) {
            JFolder::create(JPATH_ROOT.'/'.$path);
        }
    }

    protected function _createImagesContainer()
    {
        $entity = KObjectManager::getInstance()->getObject('com:files.model.containers')->slug('docman-images')->fetch();
        $path = 'joomlatools-files/docman-images';

        if ($entity->isNew())
        {
            $entity->create(array(
                'slug' => 'docman-images',
                'path' => $path,
                'title' => 'DOCman Images',
                'parameters' => array(
                    'allowed_extensions' => explode(',', 'bmp,gif,jpeg,jpg,png'),
                    'allowed_mimetypes'  => array("image/jpeg", "image/gif", "image/png", "image/bmp"),
                    'maximum_size'       => 0,
                    'thumbnails'         => false
                )
            ));

            $entity->save();
        }

        jimport('joomla.filesystem.folder');
        jimport('joomla.filesystem.file');

        if (!JFolder::exists(JPATH_ROOT.'/'.$path)) {
            JFolder::create(JPATH_ROOT.'/'.$path);
        }
    }

    protected function _migrate()
    {
        if (JComponentHelper::getComponent('com_docman')->id) {
            $this->_migrateMenuItems();
        }

        // can_create_tag was added in 3.0.0
        if (version_compare($this->old_version, '3.0.0-beta.2', '<'))
        {
            $config = KObjectManager::getInstance()->getObject('com://admin/docman.model.entity.config');
            $config->can_create_tag = 1;
            $config->save();
        }

        // automatic creation was added in 3.0.0 but it should stay disabled by default not to break existing sites
        if (version_compare($this->old_version, '3.0.0', '<'))
        {
            $config = KObjectManager::getInstance()->getObject('com://admin/docman.model.entity.config');
            //$config->automatic_document_creation = 1;
            //$config->automatic_category_creation = 1;
            $config->automatic_humanized_titles = 1;

            $config->default_owner = JFactory::getUser()->id;

            $config->save();
        }


        // sync files and folders to the database
        if (version_compare($this->old_version, '3.0.0', '<'))
        {
            try {
                $behavior = KObjectManager::getInstance()->getObject('com://admin/docman.controller.behavior.syncable');
                $behavior->syncFolders();
                $behavior->syncFiles();
            }
            catch (Exception $e) {}

        }
    }

    protected function _migrateModules()
    {
        $table   = KObjectManager::getInstance()->getObject('com://admin/docman.database.table.modules', array('name' => 'modules'));
        $modules = $table->select(array('module' => 'mod_docman_documents'));

        foreach ($modules as $module)
        {
            $parameters = json_decode($module->params);

            if (!$parameters || empty($parameters->page)) {
                continue;
            }

            $page = $parameters->page;

            if (is_array($page))
            {
                if (count($page) === 1) {
                    $page = $page[0];
                }
                elseif (is_array($page)) {
                    $page = '';
                }
            }

            $parameters->page = $page;

            $module->params = json_encode($parameters);
            $module->save();
        }
    }

    protected function _migrateMenuItems()
    {
        $id     = JComponentHelper::getComponent('com_docman')->id;
        $table  = KObjectManager::getInstance()->getObject('com://admin/docman.database.table.menus', array('name' => 'menu'));
        $items  = $table->select(array('component_id' => $id));

        foreach ($items as $item)
        {
            if ($item->menutype === 'main') {
                continue;
            }

            parse_str(str_replace('index.php?', '', $item->link), $query);

            $query['view'] = isset($query['view']) ? $query['view'] : null;
            $query['layout'] = isset($query['layout']) ? $query['layout'] : null;

            // filteredlist view got renamed to flat in 3.0.0
            if ($query['view'] === 'filteredlist')
            {
                $item->link = str_replace('view=filteredlist', 'view=flat', $item->link);

                $params = json_decode($item->params);
                $q      = array();

                if (isset($params->category)) {
                    $q['category'] = $params->category;
                }

                if (isset($params->created_by)) {
                    $q['created_by'] = $params->created_by;
                }

                if (isset($params->category_children)) {
                    $q['category_children'] = $params->category_children;
                }

                if (count($q)) {
                    $item->link .= str_replace(array('%5B', '%5D'), array('[', ']'), '&'.http_build_query($q, null, '&'));
                }

                $query['view'] = 'flat';
            }

            // userlist view is implemented as a parameter in 3.0.0
            if ($query['view'] === 'userlist') {
                $item->link = str_replace('view=userlist', 'view=list', $item->link);

                $item->link .= '&own=1';

                $query['view'] = 'list';
            }

            if ($query['view'] === 'list' && in_array($query['layout'], array('tree', 'treetable'))) {
                $item->link = str_replace('view=list', 'view=tree', $item->link);
                $item->link = str_replace('layout=treetable', 'layout=table', $item->link);
                $item->link = str_replace('layout=tree', 'layout=default', $item->link);

                $query['view'] = 'tree';
            }

            if (in_array($query['view'], array('tree', 'flat', 'list')))
            {
                // add defaults for the new parameters
                if (strpos($item->params, 'show_category_filter') === false) {
                    $params = json_decode($item->params);

                    $params->show_category_filter = 1;
                    $params->show_tag_filter      = 1;
                    $params->show_owner_filter    = 0;
                    $params->show_date_filter     = 0;

                    $item->params = json_encode($params);
                }

                if (strpos($item->params, 'allow_category_add') === false && in_array($query['view'], ['tree', 'list'])) {
                    $params = json_decode($item->params);

                    $params->allow_category_add = 1;

                    $item->params = json_encode($params);
                }

                if (strpos($item->params, 'show_document_tags') === false) {
                    $params = json_decode($item->params);

                    $params->show_document_tags = 1;

                    $item->params = json_encode($params);
                }

                // Gallery layout goes into details view now if document_title_link==details
                if ($this->old_version && version_compare($this->old_version, '3.0.0', '<')
                    && $query['layout'] === 'gallery'
                ) {
                    $params = json_decode($item->params);

                    if ($params->document_title_link === 'details') {
                        $params->document_title_link = 'download';

                        $item->params = json_encode($params);
                    }
                }

                // add default layout to the query string
                if (strpos($item->link, 'layout=') === false) {
                    $item->link .= '&layout=default';
                }

                // Player parameters for 3.2.1
                if (strpos($item->params, 'show_player') === false) {
                    $params = json_decode($item->params);

                    $params->show_player = 1;

                    $item->params = json_encode($params);
                }

                // Search parameters for 3.2.1
                if (strpos($item->params, 'search_by') === false) {
                    $params = json_decode($item->params);

                    $params->search_by = 'exact';

                    $item->params = json_encode($params);
                }
            }

            $item->save();
        }
    }

}
