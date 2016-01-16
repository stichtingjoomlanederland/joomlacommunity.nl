<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 Stack Ideas Private Limited. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.controller');

class EasyDiscussControllerDiscuss extends EasyDiscussController
{
	public function __construct()
	{
		parent::__construct();
	}

	public function clearCache()
	{
		$paths	= array( DISCUSS_ADMIN_THEMES , DISCUSS_SITE_THEMES , DISCUSS_JOOMLA_MODULES );
		$count 	= 0;

		foreach( $paths as $path )
		{
			$cachedFiles 	= JFolder::files( $path , 'style.less.cache' , true , true );

			foreach( $cachedFiles as $file )
			{
				$count++;
				JFile::delete( $file );
			}
		}

		// Also purge the /resources and /config files
		require_once( DISCUSS_CLASSES . '/compiler.php' );

		$compiler 	= new DiscussCompiler();
		$compiler->purgeResources();
		
		$message	= JText::sprintf('COM_EASYDISCUSS_CACHE_DELETED', $count );
		DiscussHelper::setMessageQueue( $message , DISCUSS_QUEUE_SUCCESS );
		$this->setRedirect( 'index.php?option=com_easydiscuss' );
	}
}
