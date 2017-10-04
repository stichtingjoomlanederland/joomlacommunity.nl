<?php
/**
 * @package     DOCman
 * @copyright   Copyright (C) 2011 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

class ComDocmanDatabaseTableCategories extends KDatabaseTableAbstract
{
    protected function  _initialize(KObjectConfig $config)
    {
        $config->append(array(
            'relation_table' => 'docman_category_relations',
            'behaviors'      => array(
                'permissible',
                'lockable',
                'sluggable',
                'creatable',
                'modifiable',
                'identifiable',
                'com://admin/docman.database.behavior.category.orderable',
                'parameterizable',
                'nestable'     => array('relation_table' => 'docman_category_relations'),
                'invalidatable'
            ),
            'column_map' => array(
                'parameters' => 'params'
            ),
            'filters'        => array(
                'parameters'  => array('json'),
                'title'       => array('trim'),
                'description' => array('trim', 'html')
            )
        ));

        parent::_initialize($config);
    }
}
