<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

class EDLegacy
{
	/**
	 * Legacy code to retrieve the config
	 *
	 * @since	3.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function getConfig()
	{
		return ED::config();
	}

	public static function getDBO()
	{
		return ED::db();
	}

	public static function getJConfig()
	{
		return ED::jconfig();
	}


	/**
	 * Retrieves a user's rank score
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function getUserRankScore( $userId, $percentage = true)
	{
		return ED::ranks()->getScore($userId, $percentage);
	}

	/**
	 * Retrieves a user's rank
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function getUserRanks($userId)
	{
		$rank = ED::ranks()->getRank($userId);

		return $rank;
	}


	/**
	 * Retrieve model from easydiscuss.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string	The model's name.
	 * @return	mixed
	 */
	public static function getModel( $name , $backend = false )
	{
		return ED::model($name, $backend);
	}

	public static function getTable( $tableName , $prefix = 'Discuss' , $config = array() )
	{
		return ED::table($tableName, $prefix, $config);
	}

	/**
	 * Renders the subscription html codes
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function getSubscriptionHTML($userid, $cid = 0, $type = DISCUSS_ENTITY_TYPE_POST, $class = '', $simpleText = true )
	{
		return ED::subscription()->html($userid, $cid, $type, $class, $simpleText);
	}

	/**
	 * Initializes the entire easydiscuss dependencies
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function compileJS()
	{
		$compile 	= JRequest::getVar( 'compile' );
		$minify 	= JRequest::getVar( 'minify' );

		if( $compile )
		{
			require_once( DISCUSS_CLASSES . '/compiler.php' );

			$minify 	= $minify ? true : false;
			$compiler 	= new DiscussCompiler();
			$result = $compiler->compile( $minify );

			var_dump($result);
			exit;
		}

	}

	public static function setMessageQueue($message, $type)
	{
		ED::setMessage($message, $type);
	}

	public static function loadHeaders()
	{
// 			$url = self::getAjaxURL();
// 			$config = DiscussHelper::getConfig();
// 			$document	= JFactory::getDocument();
// 			$ajaxData	=
// "/*<![CDATA[*/
// 	var discuss_site 	= '" . $url . "';
// 	var spinnerPath		= '" . DISCUSS_SPINNER . "';
// 	var lang_direction	= '" . $document->direction . "';
// 	var discuss_featured_style	= '" . $config->get('layout_featuredpost_style', 0) . "';
// /*]]>*/";

// 			$document->addScriptDeclaration($ajaxData);

// 			// Only legacy and oranje should be using this.
// 			if( $config->get( 'layout_site_theme' ) == 'legacy' || $config->get( 'layout_site_theme') == 'oranje' )
// 			{
// 				$document->addStyleSheet( DISCUSS_MEDIA_URI . '/styles/legacy-common.css' );
// 			}

// 			// Load MCE editor css if editor is not bbcode
// 			if( $config->get( 'layout_editor' ) != 'bbcode' )
// 			{
// 				$document->addStyleSheet( DISCUSS_MEDIA_URI . '/styles/editor-mce.css' );
// 			}

		return ED::init();
	}

	/**
	 * Load the theme's css file.
	 *
	 * @since	3.0
	 * @access	public
	 * @param	string	The theme's name
	 * @author	Mark Lee <mark@stackideas.com>
	 */
	public static function loadThemeCss()
	{
		$app	= JFactory::getApplication();

		$assets	= DiscussHelper::getHelper('assets');
		$config	= DiscussHelper::getConfig();

		// Determine site location
		$location = ($app->isAdmin()) ? 'admin' : 'site';

		// Get theme name
		$theme = strtolower($config->get('layout_' . $location . '_theme'));

		return ED::loadStylesheet($location, $theme);
	}


	public static function loadStylesheet($location, $name)
	{
		$config	= DiscussHelper::getConfig();
		$doc	= JFactory::getDocument();

		$less = DiscussHelper::getHelper('less');

		$less->compileMode = $config->get('layout_compile_mode');

		$less->allowTemplateOverride = $config->get('layout_compile_allow_template_override');

		switch ($location)
		{
			case "admin":
				$result = $less->compileAdminStylesheet($name);
				break;

			case "site":
				$result = $less->compileSiteStylesheet($name);
				break;

			case "module":
				$result = $less->compileModuleStylesheet($name);
				break;
		}

		if (!isset($result)) {
			DiscussHelper::setMessageQueue('Could not load stylesheet for ' . $name . '.', 'error');
		};

		if (JFile::exists($result->out)) {

			if ($result->failed) {
				DiscussHelper::setMessageQueue( 'Could not compile stylesheet for ' . $name . '. Using last compiled stylesheet.', 'error' );
			}

			$doc->addStyleSheet($result->out_uri);

		} elseif (JFile::exists($result->failsafe)) {

			if ($result->failed) {
				DiscussHelper::setMessageQueue( 'Could not compile stylesheet for ' . $name . '. Using failsafe stylesheet.', 'error' );
			} else {
				DiscussHelper::setMessageQueue( 'Could not locate compiled stylesheet for ' . $name . '. Using failsafe stylesheet.', 'error' );
			}

			$doc->addStyleSheet($result->failsafe_uri);

		} else {

			DiscussHelper::setMessageQueue( 'Unable to load stylesheet for ' . $name . '.', 'error' );
		}

		return $result;
	}

	public static function loadString( $view )
	{
		$doc 	= JFactory::getDocument();

		switch( $view )
		{
			case 'post':
				$string = '
					var langEmptyTitle			= "' . JText::_( 'COM_EASYDISCUSS_POST_TITLE_CANNOT_EMPTY' , true ) .'";
					var langEmptyContent		= "' . JText::_( 'COM_EASYDISCUSS_POST_CONTENT_IS_EMPTY' , true ) . '";
					var langConfirmDeleteReply	= "' . JText::_( 'COM_EASYDISCUSS_CONFIRM_DELETE_REPLY' , true ).'";
					var langConfirmDeleteReplyTitle	= "'. JText::_('COM_EASYDISCUSS_CONFIRM_DELETE_REPLY_TITLE' , true ).'";

					var langConfirmDeleteComment		= "'.JText::_('COM_EASYDISCUSS_CONFIRM_DELETE_COMMENT' , true ).'";
					var langConfirmDeleteCommentTitle	= "'.JText::_('COM_EASYDISCUSS_CONFIRM_DELETE_COMMENT_TITLE', true ).'";

					var langPostTitle	= "'.JText::_('COM_EASYDISCUSS_POST_TITLE_EXAMPLE', true ).'";
					var langEmptyTag	= "'.JText::_('COM_EASYDISCUSS_POST_EMPTY_TAG_NOT_ALLOWED' , true ).'";
					var langTagSepartor	= "'.JText::_('COM_EASYDISCUSS_POST_TAGS_SEPERATE', true ).'";
					var langTagAlreadyAdded	= "'.JText::_('COM_EASYDISCUSS_TAG_ALREADY_ADDED', true ).'";

					var langEmptyCategory	= "'.JText::_('COM_EASYDISCUSS_POST_CATEGORY_IS_EMPTY', true ).'";
				';
		}

		$doc->addScriptDeclaration($string);
	}

}
