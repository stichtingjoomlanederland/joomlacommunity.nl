<?php
/**
 * @package     DOCman
 * @copyright   Copyright (C) 2011 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

class ComDocmanControllerTag extends ComTagsControllerTag
{
    protected function _initialize(KObjectConfig $config)
    {
        $config->append(array(
            'behaviors' => array(
                'editable',
                'persistable'
            ),
            'formats' => array('json'),
            'toolbars' => array(
                'menubar',
                'com:docman.controller.toolbar.tag'
            )
        ));

        parent::_initialize($config);
    }
}
