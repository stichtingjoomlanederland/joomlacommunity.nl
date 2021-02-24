<?php
/**
 * @package     DOCman
 * @copyright   Copyright (C) 2011 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

class ComDocmanModelEntityLevel extends KModelEntityRow
{
    protected static $_groups;

    public function toArray()
    {
        $data               = parent::toArray();
        $data['group_list'] = $this->getGroups();

        return $data;
    }

    public function getGroups()
    {
        if (!self::$_groups)
        {
            $query = $this->getObject('database.query.select')->columns(array('title', 'id'));
            $table = $this->getObject('com://admin/docman.database.table.usergroups', array(
                'name' => 'usergroups'
            ));
            $groups = $table->select($query, KDatabase::FETCH_OBJECT_LIST);

            self::$_groups = array_map(function($object) { return $object->title; }, $groups);
        }

        $result = array();
        $groups = explode(',', $this->groups);

        if ($groups) {
            $result = array_intersect_key(self::$_groups, array_flip($groups));
        }

        return $result;
    }
}