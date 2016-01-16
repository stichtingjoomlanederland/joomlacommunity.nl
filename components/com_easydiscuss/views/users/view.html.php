<?php
/**
 * @package		EasyDiscuss
 * @copyright	Copyright (C) 2010 Stack Ideas Private Limited. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 *
 * EasyDiscuss is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */
defined('_JEXEC') or die('Restricted access');

require_once( DISCUSS_ROOT . '/views.php' );
require_once DISCUSS_HELPERS . '/router.php';

class EasyDiscussViewUsers extends EasyDiscussView
{
	function display( $tmpl = null )
	{
		$document	= JFactory::getDocument();
		$config = DiscussHelper::getConfig();

		$doc		= JFactory::getDocument();
		DiscussHelper::setPageTitle( JText::_( 'COM_EASYDISCUSS_MEMBERS_TITLE' ) );

		$this->setPathway( JText::_( 'COM_EASYDISCUSS_BREADCRUMBS_MEMBERS' ) );

		$model		= $this->getModel( 'Users' );

		$userQuery		= JRequest::getString( 'userQuery' , '' );
			
		$result		= $model->getData( $userQuery );
			
		$pagination	= $model->getPagination();

		$sort			= JRequest::getString('sort', 'latest');
		$filteractive	= JRequest::getString('filter', 'allposts');

		$users			= DiscussHelper::formatUsers( $result );
		$sort			= JRequest::getCmd('sort', 'name');

		$uids = $config->get( 'main_exclude_members' );

		if( !empty($uids) )
		{
			// Remove white space
			$uids = str_replace(' ', '', $uids);
			$excludeId = explode(',', $uids);

			$temp = array();
			foreach( $users as $user )
			{
				if( !in_array($user->id, $excludeId) )
				{
					$temp[] = $user;
				}
			}

			$users = $temp;
		}



		$theme			= new DiscussThemes();
		$theme->set( 'users'		, $users );
		$theme->set( 'pagination'	, $pagination );
		$theme->set( 'sort'			, $sort );
		$theme->set( 'userQuery'		, $userQuery );

		echo $theme->fetch( 'users.php' );
	}
}
