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

require_once( DISCUSS_ROOT . '/views.php' );

class EasyDiscussViewCompile extends EasyDiscussView
{
	public function testCompile()
	{
		$my					= JFactory::getUser();
		$ajax				= DiscussHelper::getHelper( 'ajax' );
		$config				= DiscussHelper::getConfig();
		$less				= DiscussHelper::getHelper('less');
		$less->compileMode	= 'force';

		$type					= JRequest::getVar( 'type' );

		if( $type == 'site' )
		{
			$name = $config->get('layout_site_theme', 'default');
		}
		else if( $type == 'admin' )
		{
			$name = 'default';
		}
		else if( $type == 'module' )
		{
			// Insert the module name you want to test compile.
			jimport( 'joomla.application.module.helper' );

			$discussModules = array(
				//'mod_ask',
				'mod_easydiscuss_categories',
				'mod_easydiscuss_latest_replies',
				'mod_easydiscuss_leaderboard',
				'mod_easydiscuss_most_likes',
				'mod_easydiscuss_most_replies',
				'mod_easydiscuss_most_voted',
				'mod_easydiscuss_navigation',
				'mod_easydiscuss_notifications',
				'mod_easydiscuss_post_topic',
				'mod_easydiscuss_quickquestion',
				'mod_easydiscuss_recentreplies',
				'mod_easydiscuss_search',
				'mod_easydiscuss_tag_cloud',
				'mod_easydiscuss_top_members',
				'mod_easydiscuss_welcome',
				'mod_recentdiscussions'
			 );

			$name = array();

			foreach( $discussModules as $discussModule )
			{
				// Check if our modules are installed or not
				$modulePath = JFolder::exists( JPATH_ROOT . DIRECTORY_SEPARATOR . 'modules' . DIRECTORY_SEPARATOR . $discussModule );
				if( $modulePath )
				{
					$name[] = $discussModule;
				}
			}
		}

		$result	= new stdClass();

		if (isset($name) && isset($type)) {

			switch ($type) {
				case "admin":
					$result = $less->compileAdminStylesheet($name);
					break;

				case "site":
					$result = $less->compileSiteStylesheet($name);
					break;

				case "module":
					$result = array();
					foreach( $name as $moduleName )
					{
						$result[] = $less->compileModuleStylesheet($moduleName);
					}
					break;

				default:
					$result->failed = 'true';
					$result->message = "Stylesheet type is invalid.";
			}

		} else {
			$result->failed = 'true';
			$result->message = "Insufficient parameters provided.";
		}

		if( $type == 'module' )
		{
			foreach( $result as $module )
			{
				if($module->failed == 'true')
				{
					$ajax->reject( $module, $type );
					return $ajax->send();
				}
			}
		}
		else if( $type != 'module' && $result->failed == 'true' )
		{
			$ajax->reject( $result, $type );
			return $ajax->send();
		}

		$ajax->resolve( $result, $type );
		return $ajax->send();
	}
}
