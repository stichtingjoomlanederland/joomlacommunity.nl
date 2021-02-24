<?php
/**
 * @package     DOCman
 * @copyright   Copyright (C) 2011 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

class ComDocmanDatabaseBehaviorPermissible extends KDatabaseBehaviorAbstract
{
    const INHERIT = 0;

    protected static $_groups;

    protected static $_levels;

    public static $task_map = array(
        'delete'   => 'core.delete',
        'add'      => 'core.create',
        'edit'     => 'core.edit',
        'download' => 'component.download'
    );

    protected function _initialize(KObjectConfig $config)
    {
        $config->append(array(
            'priority'   => self::PRIORITY_LOWEST,
        ));

        parent::_initialize($config);
    }

    /**
     * Returns a comma separated group list
     * @return string
     */
    public function getPropertyAccessTitle()
    {
        if ($this->access < self::INHERIT || $this->access_raw < self::INHERIT) {
            $result = implode(', ', $this->getGroups());
        } else {
            $result = $this->viewlevel_title;
        }

        return $result;
    }

    public function getGroups()
    {
        if (!static::$_groups)
        {
            $query = $this->getObject('database.query.select')->columns(array('title', 'id'));
            $table = $this->getObject('com://admin/docman.database.table.usergroups', array(
                'name' => 'usergroups'
            ));
            $groups = $table->select($query, KDatabase::FETCH_OBJECT_LIST);

            static::$_groups = array_map(function($object) { return $object->title; }, $groups);
        }

        $groups = array();

        if ($this->access < self::INHERIT)
        {
            if (!static::$_levels)
            {
                $table = $this->getObject('com://admin/docman.database.table.levels');

                $cache = $table->getCache();
                $key   = 'id_group_map';

                if ($data = $cache->get($key)) {
                    static::$_levels = unserialize($data);
                }
                else
                {
                    $query = $this->getObject('database.query.select')->columns(array('groups', 'docman_level_id'));
                    $levels = $table->select($query, KDatabase::FETCH_OBJECT_LIST);
                    $data   = array();

                    foreach ($levels as $level) {
                        $data[$level->id] = $level->groups;
                    }

                    static::$_levels = $data;

                    $cache->store(serialize($data), $key);
                }
            }

            if (isset(static::$_levels[-1*$this->access])) {
                $groups = static::$_levels[-1*$this->access];

                $groups = array_intersect_key(self::$_groups, array_flip(explode(',', $groups)));
            }
        }

        return $groups;
    }

    protected function _saveGroups(KDatabaseRowInterface $entity)
    {
        $table  = $this->getObject('com://admin/docman.database.table.levels');
        $row    = $table->select(array('entity' => $entity->uuid), KDatabase::FETCH_ROW);
        $groups = KObjectConfig::unbox($entity->groups);
        $access = null;

        if (is_array($groups) && count($groups))
        {
            sort($groups);

            $row->groups = implode(',', array_map('intval', $groups));
            $row->entity = $entity->uuid;

            if ($row->save()) {
                $access = -1*$row->id;
            }
        }
        elseif ($entity->inherit || $groups)
        {
            if (!$row->isNew()) {
                $row->delete();
            }

            $access = $groups ?: self::INHERIT;
        }

        if ($access !== null)
        {
            if ($entity->getIdentifier()->name === 'document')
            {
                // Make sure that an access change always get saved
                if ($entity->access_raw == self::INHERIT) {
                    $entity->access = self::INHERIT;
                }

                $entity->access = $access;
            }
            else $entity->access_raw = $access;
        }
    }

    protected function _beforeInsert(KDatabaseContextInterface $context)
    {
        $this->_beforeUpdate($context);
    }

    protected function _beforeUpdate(KDatabaseContextInterface $context)
    {
        $entity = $context->data;

        $this->_saveGroups($entity);

        if ($this->getMixer()->getIdentifier()->name == 'category')
        {
            $parent_id = $entity->parent_id ?: $entity->getParentId();

            // Re-calculate access if changed or the node got moved.
            if ($entity->isModified('access_raw') || $parent_id != $entity->getParentId())
            {
                // Calculate the access
                if ($entity->access_raw == self::INHERIT)
                {
                    if ($parent_id) {
                        $entity->access = $this->getObject('com://admin/docman.model.categories')
                                               ->id($parent_id)->fetch()->getProperty('access');
                    } else {
                        $entity->access = (int)(JFactory::getConfig()->get('access') || 1);
                    }
                } else {
                    $entity->access = $entity->access_raw;
                }

                // Process children if the calculated access changed, otherwise do not bother.
                if (!$entity->isNew() && $entity->isModified('access')) {
                    $this->_processChildren($entity);
                }
            }
        }
    }

    protected function _processChildren($entity)
    {
        $children = $entity->getDescendants(1);

        foreach ($children as $child)
        {
            if ($child->access_raw == self::INHERIT)
            {
                $child->access = $entity->access;

                $child->save();

                $this->_processChildren($child);
            }
        }
    }

    protected function _afterDelete(KDatabaseContextInterface $context)
    {
        $asset = JTable::getInstance('Asset', 'JTable', array('dbo' => JFactory::getDbo()));
        $asset->loadByName($context->data->getAssetName());

        $asset->delete();
    }

    protected function _afterInsert(KDatabaseContextInterface $context)
    {
        return $this->_afterUpdate($context);
    }

    protected function _afterUpdate(KDatabaseContextInterface $context)
    {
        $rules = null;

        if (!empty($context->data->rules)) {
            $rules = new JAccessRules($this->_filterAccessRules($context->data->rules));
        }

        $parent_id = $this->_getAssetParentId($context->data);
        $name      = $this->getAssetName($context->data);
        $title     = $this->_getAssetTitle($context->data);

        $asset = JTable::getInstance('Asset', 'JTable', array('dbo' => JFactory::getDbo()));
        $asset->loadByName($name);

        // Check for an error.
        if ($asset->getError()) {
            return false;
        }

        // Specify how a new or moved node asset is inserted into the tree.
        if (empty($asset->id) || $asset->parent_id != $parent_id) {
            $asset->setLocation($parent_id, 'last-child');
        }

        // Prepare the asset to be stored.
        $asset->parent_id = $parent_id;
        $asset->name      = $name;
        $asset->title     = $title;

        if ($rules instanceof JAccessRules) {
            $asset->rules = (string)$rules;
        } elseif (empty($asset->rules)) {
            $asset->rules = '{}';
        }

        if (!$asset->check() || !$asset->store()) {
            return false;
        }

        if ($context->data->asset_id != $asset->id) {
            $context->data->asset_id = (int)$asset->id;
        }

        if ($context->data->isModified('asset_id')) {
            $this->getTable()->getCommandChain()->disable();
            $context->data->save();
            $this->getTable()->getCommandChain()->enable();
        }
    }

    public function getPermissions()
    {
        $section     = KStringInflector::singularize($this->getTable()->getIdentifier()->name);
        $component   = 'com_' . $this->getTable()->getIdentifier()->package;
        $actions     = JAccess::getActions($component, $section);
        $permissions = array();

        foreach ($actions as $action) {
            $permissions[$action->name] = $this->canPerform(substr($action->name, strrpos($action->name, '.') + 1));
        }

        $permissions['core.admin']  = $this->canPerform('admin');
        $permissions['core.manage'] = $this->canPerform('manage');

        return $permissions;
    }

    public function canPerform($action)
    {
        static $cache;

        $user      = $this->getObject('user');
        $component = 'com_' . $this->getTable()->getIdentifier()->package;

        if (!$user->isAuthentic() && $action !== 'download') {
            return false;
        }

        // Users can add/edit/delete their own documents no matter what if allowed in the configuration.
        if ($this->created_by == $user->getId() || ($this->category_owner && $this->category_owner == $user->getId()))
        {
            if ($action === 'download') {
                return true;
            }

            if (in_array($action, array('add', 'edit', 'delete')))
            {
                if (!isset($cache)) {
                    $config = $this->getObject('com://admin/docman.model.entity.config');
                    $cache['can_delete_own'] = $config->can_delete_own;
                    $cache['can_edit_own'] = $config->can_edit_own;
                }

                $parameter = $action === 'delete' ? 'can_delete_own' : 'can_edit_own';
                $page_id   = $this->getObject('request')->query->Itemid;

                if (empty($page_id))
                {
                    $value = $cache[$parameter];

                    // Return true if enabled, otherwise check the actual permission
                    if ($value) {
                        return $value;
                    }
                }
                else {
                    $page_parameter = (boolean) $this->getObject('com://admin/docman.model.configs')->page($page_id)->fetch()->$parameter;

                    if ($page_parameter) {
                        return $page_parameter;
                    }
                    //return (boolean) $this->getObject('com://admin/docman.model.configs')->page($page_id)->fetch()->$parameter;
                }
            }
        }

        if (in_array($action, array('admin', 'manage')))
        {
            $joomla_action = 'core.' . $action;
            $asset_name    = $component;
        }
        else
        {
            $joomla_action = isset(self::$task_map[$action]) ? self::$task_map[$action] : $action;
            $joomla_action = str_replace('component.', $component . '.', $joomla_action);
            $asset_name    = $this->getAssetName();
        }

        return (bool)$user->authorise($joomla_action, $asset_name);
    }

    public function getAsset()
    {
        return $this->getObject('com://admin/docman.model.assets')->name($this->getAssetName())->fetch();
    }

    public function getAssetName(KModelEntityInterface $entity = null)
    {
        $id      = $entity ? $entity->id : $this->id;
        $section = KStringInflector::singularize($this->getTable()->getIdentifier()->name);
        $package = $this->getTable()->getIdentifier()->package;

        return sprintf('com_%s.%s.%d', $package, $section, $id);
    }

    protected function _getAssetTitle(KModelEntityInterface $entity)
    {
        return $entity->title;
    }

    protected function _getAssetParentId(KModelEntityInterface $entity)
    {
        $name        = 'com_' . $this->getTable()->getIdentifier()->package;
        $table       = $this->getTable()->getIdentifier()->name;

        if ($table === 'categories')
        {
            $parent    = $entity->getParent();
            $parent_id = $entity->parent_id ? $entity->parent_id : ($parent ? $parent->id : 0);

            if ($parent_id) {
                $name = sprintf('%s.%s.%d', $name, 'category', $parent_id);
            }
        }
        elseif ($table === 'documents')
        {
            if ($this->docman_category_id)
            {
                $item = $this->getObject('com://admin/docman.model.categories')
                    ->id($this->docman_category_id)
                    ->fetch();

                if ($item->isPermissible()) {
                    $name = $item->getAssetName();
                }
            }
        }

        $asset_id = $this->getObject('com://admin/docman.model.assets')->name($name)->fetch()->getProperty('id');

        if (!$asset_id) {
            $asset_id = 1;
        }

        return $asset_id;
    }

    /**
     * This is hardcoded into JForm so need to copy here
     *
     * @param array $rules
     */
    protected function _filterAccessRules($rules)
    {
        $return = array();
        foreach ((array)$rules as $action => $ids)
        {
            // Build the rules array.
            $return[$action] = array();

            foreach ($ids as $id => $p) {
                if ($p !== '') {
                    $return[$action][$id] = ($p == '1' || $p == 'true') ? true : false;
                }
            }
        }

        return $return;
    }
}
