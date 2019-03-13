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

    'aliases'    => array(
        'com:files.database.row.container'                    => 'com:files.model.entity.container',
        'com:files.database.rowset.containers'                => 'com:files.model.entity.containers',
        'com://site/docman.controller.doclink'                => 'com://admin/docman.controller.doclink',
        'com://site/docman.controller.file'                   => 'com://admin/docman.controller.file',
        'com://site/docman.controller.upload'                => 'com://admin/docman.controller.upload',
        'com://site/docman.controller.user'                   => 'com://admin/docman.controller.user',
        'com://site/docman.controller.tag'                    => 'com://admin/docman.controller.tag',
        'com://site/docman.controller.behavior.findable'      => 'com://admin/docman.controller.behavior.findable',
        'com://site/docman.controller.behavior.organizable'      => 'com://admin/docman.controller.behavior.organizable',
        'com://site/docman.controller.behavior.sluggable'      => 'com://admin/docman.controller.behavior.sluggable',
        'com://site/docman.controller.behavior.thumbnailable' => 'com://admin/docman.controller.behavior.thumbnailable',
        'com://site/docman.model.categories'              	  => 'com://admin/docman.model.categories',
        'com://site/docman.model.default'                     => 'com://admin/docman.model.default',
        'com://site/docman.model.documents'                   => 'com://admin/docman.model.documents',
        'com://site/docman.model.files'                  	  => 'com://admin/docman.model.files',
        'com://site/docman.model.nodes'                  	  => 'com://admin/docman.model.nodes',
        'com://site/docman.model.pages'                  	  => 'com://admin/docman.model.pages',
        'com://site/docman.database.table.nodes'	      	  => 'com://admin/docman.database.table.nodes',
        'com://site/docman.database.table.categories'    	  => 'com://admin/docman.database.table.categories',
        'com://site/docman.database.table.documents'     	  => 'com://admin/docman.database.table.documents',
        'com://site/docman.database.table.document_contents'  => 'com://admin/docman.database.table.document_contents',
        'com://site/docman.database.table.levels'     	      => 'com://admin/docman.database.table.levels',
        'com://site/docman.model.entity.node'		    	  => 'com://admin/docman.model.entity.node',
        'com://site/docman.model.entity.category'	    	  => 'com://admin/docman.model.entity.category',
        'com://site/docman.model.entity.document'        	  => 'com://admin/docman.model.entity.document',
        'com://site/docman.model.entity.file'            	  => 'com://admin/docman.model.entity.file',
        'com://site/docman.model.entity.nodes'        	      => 'com://admin/docman.model.entity.nodes',
        'com://site/docman.model.entity.files'        	      => 'com://admin/docman.model.entity.files',
        'com://site/docman.model.entity.level'		    	  => 'com://admin/docman.model.entity.level',
        'com://site/docman.model.entity.viewlevel'            => 'com://admin/docman.model.entity.viewlevel',

        'com://site/docman.template.filter.style' 	     	  => 'com://admin/docman.template.filter.style',
        'com://site/docman.template.filter.url'  	     	  => 'com://admin/docman.template.filter.url',
        'com://site/docman.template.helper.access' 	     	  => 'com://admin/docman.template.helper.access',
        'com://site/docman.template.helper.actionbar'      	  => 'com://admin/koowa.template.helper.actionbar',
        'com://site/docman.template.helper.behavior'      	  => 'com://admin/docman.template.helper.behavior',
        'com://site/docman.template.helper.grid' 	     	  => 'com://admin/docman.template.helper.grid',
        'com://site/docman.template.helper.icon' 	     	  => 'com://admin/docman.template.helper.icon',
        'com://site/docman.template.helper.listbox'      	  => 'com://admin/docman.template.helper.listbox',
        'com://site/docman.template.helper.modal'         	  => 'com://admin/docman.template.helper.modal',
        'com://site/docman.template.helper.string'	    	  => 'com://admin/docman.template.helper.string',
        'com://site/docman.template.helper.route'             => 'com://admin/docman.template.helper.route',
    ),

    'identifiers' => array(
        'com:scheduler.controller.dispatcher' => array(
            'jobs' => array(
                'com://admin/docman.job.cache',
                'com://admin/docman.job.documents',
                'com://admin/docman.job.categories',
                'com://admin/docman.job.scans',
                'com://admin/docman.job.files'
            )
        ),
        'com://admin/docman.model.behavior.taggable' => array(
            'strict' => isset($config['tags']['strict']) ? $config['tags']['strict'] : false
        ),

        'com://site/docman.model.categories' => array(
            'state' => 'com://site/docman.model.state'
        ),

        'com://site/docman.model.documents' => array(
            'state' => 'com://site/docman.model.state',
            'behaviors' => array(
                'com://site/docman.model.behavior.publishable'
            )
        )
    )
);
