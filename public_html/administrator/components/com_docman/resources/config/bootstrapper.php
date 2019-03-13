<?php
/**
 * @package    DOCman
 * @copyright   Copyright (C) 2011 Timble CVBA (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

$config = array();

if(file_exists(Koowa::getInstance()->getRootPath().'/joomlatools-config/docman.php')) {
    $config = (array) include Koowa::getInstance()->getRootPath().'/joomlatools-config/docman.php';
}


return array(
    'identifiers' => array(
        'com://admin/docman.model.behavior.taggable' => array(
            'strict' => isset($config['tags']['strict']) ? $config['tags']['strict'] : false
        )
    )
);
