<?php
/**
 * @package     DOCman
 * @copyright   Copyright (C) 2011 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

class ComDocmanModelEntityViewlevel extends KModelEntityRow
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
            self::$_groups = $table->select($query, KDatabase::FETCH_OBJECT_LIST);
        }

        $result = array();
        $groups = json_decode($this->rules);

        if ($groups) {
            $result = array_intersect_key(self::$_groups, array_flip($groups));
            $result = array_map(function($group) { return $group->title; }, $result);
        }

        return $result;
    }
}