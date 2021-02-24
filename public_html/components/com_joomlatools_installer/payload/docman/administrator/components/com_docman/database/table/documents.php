<?php
/**
 * @package     DOCman
 * @copyright   Copyright (C) 2011 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

class ComDocmanDatabaseTableDocuments extends KDatabaseTableAbstract
{
    protected function _initialize(KObjectConfig $config)
    {
        $config->append(array(
            'behaviors' => array(
                'permissible',
                'lockable',
                'creatable',
                'modifiable',
                'sluggable',
                'identifiable',
                'hittable',
                'parameterizable',
                'invalidatable',
                'orderable'
            ),
            'column_map' => array(
                'parameters' => 'params',
                'touched_on' => 'GREATEST(tbl.created_on, tbl.modified_on)'
            ),
            'filters' => array(
                'parameters'   => array('json'),
                'title'        => array('trim'),
                'storage_type' => array('com://admin/docman.filter.identifier'),
                'description'  => array('trim', 'html')
            )
        ));

        parent::_initialize($config);
    }
}
