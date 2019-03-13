<?php
/**
 * @package     Joomlatools
 * @copyright   Copyright (C) 2017 Timble CVBA (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

if (!class_exists('JoomlatoolsInstallerHelper'))
{

class JoomlatoolsInstallerHelper
{
    /**
     * Name of the component
     * 
     * @var string
     */
    public $component;

    /**
     * The version we are upgrading from
     * 
     * @var string
     */
    public $old_version;

    /**
     * Installer instance
     *
     * @var JInstaller
     */
    public $installer;

    public function __construct($installer)
    {
        preg_match('#^com_([a-z0-9_]+)#', get_class($this), $matches);
        $this->component = $matches[1];

        $this->installer = $installer;
    }

    public function abort($error)
    {
        if (is_array($error)) {
            $error = implode('<br />', $error);
        }

        $this->installer->getParent()->abort($error);
    }

    public function preflight($type, $installer)
    {
        @ini_set('memory_limit', '256M');

        if ($type === 'update') {
            $this->old_version = $this->_getComponentVersion($this->component);
        }

        $result = true;
        $errors = array();

        $privileges = $this->getRequiredDatabasePrivileges();
        if ($privileges && ($failed = $this->_checkDatabasePrivileges($privileges))) {
            $errors[] = JText::sprintf('The following MySQL privileges are missing: %s. Please make them available to your MySQL user and try again.',
                htmlspecialchars(implode(', ', $failed), ENT_QUOTES));
            $result   = false;
        }

        if ($result === true && !class_exists('Koowa'))
        {
            if (!$this->_installFramework($type)) {
                $errors[] = sprintf(JText::_('This component requires System - Joomlatools Framework plugin to be installed and enabled. Please go to <a href="%s">Plugin Manager</a>, enable <strong>System - Joomlatools Framework</strong> and try again'), JRoute::_('index.php?option=com_plugins&view=plugins&filter_folder=system'));

                $result = false;
            }
        }

        if ($result === true)
        {
            if ($errors = $this->getSystemErrors($type, $installer)) {
                $result = false;
            }
        }

        if ($result === false && $errors) {
            $this->abort($errors);
        }

        return $result;
    }

    public function update($installer)
    {
        $install_sql = $installer->getParent()->getPath('extension_root') . '/resources/install/install.sql';
        $update_sql  = $installer->getParent()->getPath('extension_root') . '/resources/install/update.sql';

        if (file_exists($install_sql)) {
            $this->_executeSqlFile($install_sql);
        }

        if (file_exists($update_sql)) {
            $this->_executeSqlFile($update_sql);
        }
    }

    public function postflight($type, $installer)
    {
        if ($type === 'discover_install') {
            $this->_createAsset('com_'.$this->component, 1);
        }

        $manifest = simplexml_load_file($installer->getParent()->getPath('manifest'));
        $source   = $installer->getParent()->getPath('source');

        if ($manifest->dependencies)
        {
            $discovered = $type === 'discover_install' ? $this->_discoverExtensions() : array();

            foreach ($manifest->dependencies->dependency as $dependency)
            {
                $dependency_installer = new JInstaller();

                if ($type !== 'discover_install') {
                    $dependency_installer->install($source.'/'.(string)$dependency);
                }
                else {
                    $extension_id = $this->getExtensionId($dependency);

                    if ($extension_id)
                    {
                        $instance = JTable::getInstance('extension');
                        $instance->load($extension_id);
                    }
                    else
                    {
                        if ($instance = $this->_findExtension($discovered, $dependency)) {
                            $instance->store();
                        }
                    }

                    if ($instance && $instance->state == -1) {
                        if ($dependency_installer->discover_install($instance->extension_id)) {
                            $this->_setCoreExtension($instance->extension_id, 1);
                        }
                    }
                }

                if ((string)$dependency['type'] === 'plugin')
                {
                    $query = sprintf(/**@lang text*/'UPDATE #__extensions SET enabled = 1 WHERE extension_id = %d',
                        $this->getExtensionId($dependency));

                    JFactory::getDbo()->setQuery($query)->query();
                }
            }
        }

        if ($manifest->deleted) {
            $this->_deleteOldFiles($manifest->deleted);
        }

        $callback = array($this, 'afterInstall');
        if (is_callable($callback)) {
            call_user_func($callback, $type, $installer);
        }

        $this->_clearCache();

        if ($this->old_version) {
            $this->_migrate();
        }

        $this->_clearCache();
    }

    public function uninstall($installer)
    {
        $manifest = $installer->getParent()->getManifest();

        if (isset($manifest->dependencies) && count($manifest->dependencies->children()))
        {
            foreach ($manifest->dependencies->children() as $dependency)
            {
                if ($dependency->attributes()->uninstall == 'false') {
                    continue;
                }

                $type      = (string)$dependency->attributes()->type;
                $element   = (string)$dependency->attributes()->element;
                $folder    = (string)$dependency->attributes()->folder;
                $client_id = (int)(string)$dependency->attributes()->client_id;

                if (empty($client_id)) {
                    $client_id = $type == 'component' ? 1 : 0;
                }

                $extension_id = $this->getExtensionId(array(
                    'type' => $type,
                    'element' => $element,
                    'folder'  => $folder
                ));

                if ($extension_id) {
                    $this->_setCoreExtension($extension_id, 0);

                    $i = new JInstaller();
                    $i->uninstall($type, $extension_id, $client_id);
                }
            }
        }

        $db = JFactory::getDbo();

        /*
         * Sometimes installer messes up and leaves stuff behind. Remove them too when uninstalling
         */
        $query = /** @lang text */"DELETE FROM #__menu WHERE link = 'index.php?option=com_%s' AND component_id = 0 LIMIT 1";
        $db->setQuery(sprintf($query, $this->component));
        $db->query();

        $this->_clearCache();
    }

    public function getExtensionId($extension)
    {
        $type    = (string)$extension['type'];
        $element = (string)$extension['element'];
        $folder  = isset($extension['folder']) ? (string) $extension['folder'] : '';
        $cid     = isset($extension['client_id']) ? (int) $extension['client_id'] : 0;

        if ($type == 'component') {
            $cid = 1;
        }

        if ($type == 'component' && substr($element, 0, 4) !== 'com_') {
            $element = 'com_'.$element;
        } elseif ($type == 'module' && substr($element, 0, 4) !== 'mod_') {
            $element = 'mod_'.$element;
        }

        $db = JFactory::getDbo();
        $query = /** @lang text */"SELECT extension_id FROM #__extensions
            WHERE type = '$type' AND element = '$element' AND folder = '$folder' AND client_id = '$cid'
            LIMIT 1
        ";

        $db->setQuery($query);

        return $db->loadResult();
    }

    public function getSystemErrors($type, $installer)
    {
        return array();
    }

    public function getRequiredDatabasePrivileges()
    {
        return array();
    }

    protected function _installFramework($type)
    {
        if ($type === 'discover_install')
        {
            $installer = new JInstaller();
            $extension = array(
                'type' => 'plugin',
                'folder' => 'system',
                'element' => 'joomlatools'
            );

            if ($extension_id = $this->getExtensionId($extension))
            {
                $instance = JTable::getInstance('extension');
                $instance->load($extension_id);
            }
            else
            {
                $discovered = $this->_discoverExtensions();
                $instance   = $this->_findExtension($discovered, $extension);

                if ($instance) {
                    $instance->store();
                }
            }

            if ($instance && $instance->state == -1) {
                return $installer->discover_install($instance->extension_id);
            }
        }

        return false;
    }

    protected function _getComponentVersion($component)
    {
        $query = /** @lang text */"SELECT manifest_cache FROM #__extensions WHERE type = 'component' AND element = '%s'";
        $query = sprintf($query, 'com_'.$component);

        if ($result = JFactory::getDbo()->setQuery($query)->loadResult())
        {
            $manifest = new JRegistry($result);

            return $manifest->get('version', null);
        }

        return null;
    }

    protected function _setCoreExtension($extension_id, $value)
    {
        $query = /** @lang text */"UPDATE #__extensions SET protected = %d  WHERE extension_id = %d LIMIT 1";
        $query = sprintf($query, $value, $extension_id);

        return JFactory::getDbo()->setQuery($query)->query();
    }

    protected function _discoverExtensions()
    {
        $installer = new JInstaller();
        $installer->loadAllAdapters();

        return $installer->discover();

    }

    protected function _findExtension($extensions, $search)
    {
        $type      = $search['type'];
        $element   = $search['element'];
        $folder    = isset($search['folder']) ? $search['folder'] : '';
        $client_id = isset($search['client_id']) ? $search['client_id'] : 0;

        if ($type == 'component') {
            $client_id = 1;
        }

        $return = null;

        foreach ($extensions as $extension)
        {
            if ($extension->type == (string) $type &&
                $extension->element == (string) $element &&
                $extension->folder == (string) $folder &&
                $extension->client_id == (int) $client_id
            ) {
                $return = $extension;
                break;
            }
        }

        return $return;
    }

    protected function _executeQuery($query) {
        try {
            return JFactory::getDbo()->setQuery($query)->execute();
        }
        catch (Exception $e)
        {
            JLog::add(JText::sprintf('JLIB_INSTALLER_ERROR_SQL_ERROR', $e->getMessage()), JLog::WARNING, 'jerror');

            return false;
        }
    }

    protected function _executeQueries($queries)
    {
        if (is_string($queries)) {
            $queries = JDatabaseDriver::splitSql($queries);
        }

        foreach ($queries as $query) {
            $this->_executeQuery($query);
        }
    }

    protected function _executeSqlFile($file)
    {
        try
        {
            $buffer = file_get_contents($file);

            if ($buffer === false) {
                JLog::add(JText::sprintf('JLIB_INSTALLER_ERROR_SQL_READBUFFER'), JLog::WARNING, 'jerror');
            }
            else {
                $this->_executeQueries($buffer);
            }
        }
        catch (Exception $e) {}
    }

    protected function _tableExists($table)
    {
        $db = JFactory::getDbo();

        if (substr($table, 0,  3) !== '#__') {
            $table = '#__'.$table;
        }

        return (bool) $db->setQuery('SHOW TABLES LIKE '.$db->quote($db->replacePrefix($table)))->loadResult();
    }

    protected function _columnExists($table, $column)
    {
        $result = false;
        $db     = JFactory::getDbo();

        if (substr($table, 0,  3) !== '#__') {
            $table = '#__'.$table;
        }

        if ($this->_tableExists($table))
        {
            $query  = 'SHOW COLUMNS FROM %s WHERE Field = %s';
            $result = (bool) $db->setQuery(sprintf($query, $db->quoteName($db->escape($table)), $db->quote($column)))->loadResult();
        }

        return $result;
    }

    protected function _indexExists($table, $index_name)
    {
        $result = false;
        $db     = JFactory::getDbo();

        if (substr($table, 0,  3) !== '#__') {
            $table = '#__'.$table;
        }

        if ($this->_tableExists($table))
        {
            $query  = 'SHOW KEYS FROM %s WHERE Key_name = %s';
            $result = (bool) $db->setQuery(sprintf($query, $db->quoteName($db->escape($table)), $db->quote($index_name)))->loadResult();
        }

        return $result;
    }

    protected function _backupTable($table)
    {
        if ($this->_tableExists($table))
        {
            $destination = $table.'_bkp';

            if ($this->_tableExists($destination))
            {
                $i = 2;

                while (true)
                {
                    if (!$this->_tableExists($destination.$i)) {
                        break;
                    }

                    $i++;
                }

                $destination .= $i;
            }

            $return = JFactory::getDbo()->setQuery(sprintf('RENAME TABLE `%1$s` TO `%2$s`;', $table, $destination))->query();
        }
        else $return = true;

        return $return;
    }

    protected function _createAsset($name, $parent_id = 1)
    {
        $asset = JTable::getInstance('Asset');

        if (!$asset->loadByName($name))
        {
            $asset->name  = $name;
            $asset->title = $name;
            $asset->parent_id = $parent_id;
            $asset->rules = '{}';
            $asset->setLocation(1, 'last-child');

            return $asset->check() && $asset->store();
        }

        return true;
    }

    protected function _clearCache()
    {
        // Joomla does not clean up its plugins cache for us
        JCache::getInstance('callback', array(
            'defaultgroup' => 'com_plugins',
            'cachebase'    => JPATH_ADMINISTRATOR . '/cache'
        ))->clean();

        JFactory::getCache('com_koowa.tables', 'output')->clean();
        JFactory::getCache('com_koowa.templates', 'output')->clean();

        // Clear APC opcode cache
        if (extension_loaded('apc'))
        {
            apc_clear_cache();
            apc_clear_cache('user');
        }

        // Clear OPcache
        if (function_exists('opcache_reset')) {
            @opcache_reset();
        }
    }

    protected function _deleteOldFiles($node)
    {
        if (!is_object($node)) {
            return false;
        }

        foreach ($node->file as $file)
        {
            $path = JPATH_ROOT.'/'.(string)$file;

            if (file_exists($path)) {
                JFile::delete($path);
            }
        }

        foreach ($node->folder as $folder)
        {
            $path = JPATH_ROOT.'/'.(string)$folder;

            if (file_exists($path)) {
                JFolder::delete($path);
            }
        }

        return true;
    }


    /**
     * Tests a list of DB privileges against the current application DB connection.
     *
     * @param array $privileges An array containing the privileges to be checked.
     *
     * @return array True An array containing the privileges that didn't pass the test, i.e. not granted.
     */
    protected function _checkDatabasePrivileges($privileges)
    {
        $privileges = (array) $privileges;

        $db = JFactory::getDbo();

        $query = 'SELECT @@SQL_MODE';
        $db->setQuery($query);
        $sql_mode = $db->loadResult();

        $db_name = JFactory::getApplication()->getCfg('db');

        // Quote and escape DB name.
        if (strtolower($sql_mode) == 'ansi_quotes') {
            // Double quotes as delimiters.
            $db_name = '"' . str_replace('"', '""', $db_name) . '"';
        } else {
            $db_name = '`' . str_replace('`', '``', $db_name) . '`';
        }

        // Properly escape DB name.
        $possible_tables = array(
            '*.*',
            $db_name . '.*',
            strtolower($db_name . '*'),
            str_replace('_', '\_', $db_name) . '.*',
            strtolower(str_replace('_', '\_', $db_name) . '.*')
        );

        $db->setQuery('SHOW GRANTS');

        $grants = $db->loadColumn();
        $granted = array();

        foreach ($grants as $grant)
        {
            if (stripos($grant, 'USAGE ON') === false)
            {
                foreach ($privileges as $privilege)
                {
                    $regex = '/(grant\s+|,\s*)' . $privilege . '(\s*,|\s+on)/i';

                    if (stripos($grant, 'ALL PRIVILEGES') || preg_match($regex, $grant))
                    {
                        // Check tables
                        $tables = substr($grant, stripos($grant, ' ON ') + 4);
                        $tables = substr($tables, 0, stripos($tables, ' TO'));
                        $tables = trim($tables);

                        if (in_array($tables, $possible_tables)) {
                            $granted[] = $privilege;
                        }
                    }
                }
            }
            else
            {
                // Proceed with installation if user is granted USAGE
                $granted = $privileges;
                break;
            }
        }

        return array_diff($privileges, $granted);
    }

    protected function _migrate()
    {
    }
}
    
}
