<?php
/**
 * @package     DOCman
 * @copyright   Copyright (C) 2011 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

class ComDocmanDatabaseTableScans extends KDatabaseTableAbstract
{
    protected function _initialize(KObjectConfig $config)
    {
        $config->append(array(
            'behaviors' => array(
                'lib:database.behavior.creatable',
                'lib:database.behavior.modifiable',
                'lib:database.behavior.parameterizable'
            ),
            'filters'   => array(
                'parameters' => array('json'),
            )
        ));

        parent::_initialize($config);
    }
}
