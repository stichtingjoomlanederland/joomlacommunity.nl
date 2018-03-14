<?php
/**
 * @package     DOCman
 * @copyright   Copyright (C) 2011 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

class ComDocmanModelEntityConfig extends KModelEntityAbstract implements KObjectMultiton
{
    /**
     * Joomla asset cache
     *
     * @var JTableAsset
     */
    protected static $_asset;

    public function __construct($config = array())
    {
        parent::__construct($config);

        if (!empty($config->auto_load)) {
            $this->load();
        }
    }

    protected function _initialize(KObjectConfig $config)
    {
        $config->append(array(
            'auto_load' => true
        ));

        parent::_initialize($config);
    }

    public function isNew()
    {
        return false;
    }

    public function isLockable()
    {
        return false;
    }

    public function getFilesContainer()
    {
        return $this->getObject('com:files.model.containers')->slug('docman-files')->fetch();
    }

    public function cleanCache()
    {
        $this->getObject('com://admin/docman.database.table.categories')->cleanCache();
        $this->getObject('com://admin/docman.database.table.documents')->cleanCache();
        $this->getObject('com://admin/docman.database.table.levels')->cleanCache();

        JCache::getInstance('output', array('defaultgroup' => 'com_docman.files'))->clean();
    }

    public function load()
    {
        $this->setProperties(JComponentHelper::getParams('com_docman')->toArray());

        $container  = $this->getFilesContainer();
        $parameters = $container->getParameters();

        $this->document_path = $container->path;

        foreach (array('thumbnails', 'allowed_extensions', 'maximum_size', 'allowed_mimetypes') as $key) {
            $this->$key = $parameters->$key;
        }
    }

    protected function _getAsset()
    {
        if (!self::$_asset instanceof JTableAsset)
        {
            self::$_asset = JTable::getInstance('Asset');
            self::$_asset->loadByName('com_docman');
        }

        return self::$_asset;
    }

    /**
     * Copied from JForm
     *
     * @param array $rules
     * @return array
     */
    protected function _filterAccessRules($rules)
    {
        $return = array();
        foreach ((array) $rules as $action => $ids)
        {
            // Build the rules array.
            $return[$action] = array();
            foreach ($ids as $id => $p)
            {
                if ($p !== '') {
                    $return[$action][$id] = ($p == '1' || $p == 'true') ? true : false;
                }
            }
        }

        return $return;
    }

    public function save()
    {
        // System variables shoulnd't be saved
        foreach (array('csrf_token', 'option', 'action', 'format', 'layout', 'task') as $var)
        {
            unset($this->_data[$var]);
            unset($this->_modified[$var]);
        }

        if (!empty($this->rules))
        {
            $rules	= new JAccessRules($this->_filterAccessRules($this->rules));
            $asset	= JTable::getInstance('asset');

            if (!$asset->loadByName('com_docman')) {
                $root	= JTable::getInstance('asset');
                $root->loadByName('root.1');
                $asset->name = 'com_docman';
                $asset->title = 'com_docman';
                $asset->setLocation($root->id, 'last-child');
            }

            $asset->rules = (string) $rules;

            if (!($asset->check() && $asset->store()))
            {
                $translator = $this->getObject('translator');
                $this->getObject('response')->addMessage(
                    $translator->translate('Changes to the ACL rules could not be saved.'), 'warning'
                );
            }

            unset($this->_data['rules']);
        }

        if (!empty($this->_data['allowed_extensions']) && is_string($this->_data['allowed_extensions'])) {
            $this->allowed_extensions = explode(',', $this->_data['allowed_extensions']);
        }

        // Auto-set allowed mimetypes based on the extensions
        if (!empty($this->allowed_extensions))
        {
            $mimetypes = $this->getObject('com:files.model.mimetypes')
                    ->extension($this->allowed_extensions)
                    ->fetch();

            $results = array();
            foreach ($mimetypes as $mimetype) {
                $results[] = $mimetype->mimetype;
            }

            $this->allowed_mimetypes = array_values(array_unique(array_merge($this->allowed_mimetypes, $results)));
        }

        // If the document path changed try to move the files to their new location
        $container = $this->getFilesContainer();
        $this->_saveDocumentPath($container);

        // These are all going to be saved into com_files
        $data = array();
        foreach (array('thumbnails', 'allowed_extensions', 'maximum_size', 'allowed_mimetypes', 'document_path') as $var)
        {
            $value = $this->$var;

            if ($var === 'thumbnails') {
                $value = (bool) $value;
            }

            if (!empty($value) || ($value === false || $value === 0 || $value === '0')) {
                $data[$var] = $value;
            }
            unset($this->_data[$var]);
            unset($this->_modified[$var]);
        }
        unset($data['document_path']);

        $container->getParameters()->merge($data);
        $result = $container->save();

        $extension = $this->getObject('com:koowa.model.extensions')
            ->type('component')->element('com_docman')->fetch();

        $extension->parameters = $this->getProperties();
        $extension->save();

        $this->cleanCache();

        return $result;
    }

    protected function _saveDocumentPath(KModelEntityInterface $entity)
    {
        if (!$this->isModified('document_path')) {
            return;
        }

        $translator = $this->getObject('translator');
        $from       = $entity->path;
        $fullpath    = $entity->fullpath;
        $path       = rtrim($this->document_path, '\\/');

        if ($from === $path) {
            return;
        }

        if ($path === 'joomlatools-files') {
            $this->getObject('response')->addMessage($translator->translate('joomlatools-files is a special folder used for other DOCman features. You can only use a subfolder of it to store your files'), 'error');
            return;
        }

        if (!preg_match('#^[0-9A-Za-z:_\-\\\/\.]+$#', $path)) {
            $this->getObject('response')->addMessage($translator->translate('Document path can only contain letters, numbers, dash or underscore'), 'error');
            return;
        }

        $db = JFactory::getDBO();
        $query = sprintf("SELECT COUNT(*) FROM #__menu WHERE path = %s", $db->quote($path));
        if ($db->setQuery($query)->loadResult())
        {
            $this->getObject('response')->addMessage(
                $translator->translate('A menu item on your site uses this path as its alias. In order to ensure that your site works correctly, the document path was left unchanged.'),
                'error'
            );

            return;
        }

        $entity->path = $path;

        if ($entity->save())
        {
            jimport('joomla.filesystem.folder');
            jimport('joomla.filesystem.file');

            $parent = dirname($entity->fullpath);
            if (!JFolder::exists($parent)) {
                JFolder::create($parent);
            }

            if (JFolder::move($fullpath, $entity->fullpath) !== true)
            {
                $this->getObject('response')->addMessage(
                    $translator->translate('Changes are saved but you should move existing files manually from folder "{from}" to "{to}" at your site root in order to make existing files visible.',
                        array('from' => $from, 'to' => $path)
                    ), 'warning'
                );
            }

            if (!JFile::exists($entity->fullpath.'/.htaccess'))
            {
                $buffer ='DENY FROM ALL';
                JFile::write($entity->fullpath.'/.htaccess', $buffer);
            }

            if (!JFile::exists($entity->fullpath.'/web.config'))
            {
                $buffer ='<?xml version="1.0" encoding="utf-8" ?>
<system.webServer>
    <security>
        <authorization>
            <remove users="*" roles="" verbs="" />
            <add accessType="Allow" roles="Administrators" />
        </authorization>
    </security>
</system.webServer>';
                JFile::write($entity->fullpath.'/web.config', $buffer);
            }
        }
    }

    public function getProperty($column)
    {
        $result = parent::getProperty($column);

        if (in_array($column, array('allowed_extensions', 'allowed_mimetypes')))
        {
            if ($result instanceof KObjectConfigInterface) {
                return $result->toArray();
            }
            elseif (!is_array($result)) {
                return array();
            }
        }

        // Disable thumbnails if these cannot be generated.
        if ($column == 'thumbnails' && $result) {
            $result = $this->thumbnailsAvailable();
        }

        return $result;
    }

    /**
     * Utility function for checking if the server can generate thumbnails.
     *
     * @return bool True if it can, false otherwise.
     */
    public function thumbnailsAvailable()
    {
        return extension_loaded('gd')/* || extension_loaded('imagick')*/;
    }

    /**
     * Utility function for checking if Joomlatools Connect is supported
     *
     * @return bool True if it can, false otherwise.
     */
    public function connectAvailable()
    {
        return class_exists('PlgKoowaConnect') && PlgKoowaConnect::isSupported()
            && defined('PlgKoowaConnect::VERSION') && version_compare(PlgKoowaConnect::VERSION, '2.0.0', '>=');
    }
}
