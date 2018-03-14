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

jimport('joomla.application.component.controller');

if (!class_exists('EasyDiscussControllerParent')) {

	if (ED::getJoomlaVersion() >= '3.0') {
		class EasyDiscussControllerParent extends JControllerLegacy { }
	} else {
		class EasyDiscussControllerParent extends JController { }
	}
}

class EasyDiscussController extends EasyDiscussControllerParent
{
	public function __construct()
	{
		parent::__construct();

		// Determines if the current request is an ajax request
        $this->ajax = ED::ajax();
		$this->config = ED::config();
        $this->jconfig = ED::jconfig();
		$this->doc = JFactory::getDocument();
		$this->app = JFactory::getApplication();
		$this->input = ED::request();
		$this->theme = ED::themes();
		$this->my = JFactory::getUser();
        $this->profile = ED::profile();
        $this->acl = ED::acl();
        $this->isAdmin = ED::isSiteAdmin();
        $this->isAjax = $this->doc->getType() == 'ajax';

        if (method_exists($this, 'isFeatureAvailable')) {
        	$available = $this->isFeatureAvailable();

        	if (!$available) {
        		return JError::raiseError(500, JText::_('COM_EASYDISCUSS_FEATURE_IS_NOT_ENABLED'));
        	}
        }
	}

	/**
	 * Override parent controller's display behavior.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function display($params = array() , $urlparams = false)
	{
		$type = $this->doc->getType();
		$name = $this->input->get('view', 'index', 'cmd');
		$view = $this->getView($name, $type, '');

		// Once we have the view, set the appropriate layout.
		$layout = $this->input->get('layout', 'default', 'cmd');

		$view->setLayout($layout);

		// For ajax methods, we just load the view methods.
		if ($type == 'ajax') {

			if (!method_exists($view, $layout)) {
				$view->display();
			} else {
				$params = $this->input->get('params', '', 'default');

				if ($params) {
					$params = json_decode($params);
				} else {
					// empty. just assign empty array so that call user func will not trigger
					// errors.
					$params = array();
				}

				call_user_func_array(array($view, $layout), $params);
			}

		} else {

			if ($layout != 'default') {
				if (!method_exists($view, $layout)) {
					$view->display();
				} else {
					call_user_func_array(array($view, $layout), $params);
				}
			} else {
				$view->display();
			}
		}
	}

	public function xdisplay( $cachable = false , $urlparams = false )
	{
		$document	= JFactory::getDocument();

		$viewName	= JRequest::getCmd( 'view'		, 'index' );
		$viewLayout	= JRequest::getCmd( 'layout'	, 'default' );
		$view		= $this->getView( $viewName		, $document->getType() , '' );
		$format		= JRequest::getCmd( 'format'	, 'html' );
		$tmpl		= JRequest::getCmd( 'tmpl'		, 'html' );

		// @rule: Skip processing for feed views
		if( in_array( $format , array( 'feed' , 'weever' ) ) )
		{
			if( $viewLayout != 'default' )
			{
				if( $cachable )
				{
					$cache	= JFactory::getCache( 'com_easydiscuss' , 'view' );
					$cache->get( $view , $viewLayout );
				}
				else
				{
					if( !method_exists( $view , $viewLayout ) )
					{
						$view->display();
					}
					else
					{
						$view->$viewLayout();
					}
				}
			}
			else
			{
				$view->display();
			}

			return;
		}



		if( !empty( $format ) && $format == 'ajax' )
		{
			if( !JRequest::checkToken() && !JRequest::checkToken( 'get' ) )
			{
				echo 'Invalid token';
				exit;
			}

			$data		= JRequest::get( 'POST' );
			$arguments	= array();

			foreach( $data as $key => $value )
			{
				if( JString::substr( $key , 0 , 5 ) == 'value' )
				{
					if(is_array($value))
					{
						$arrVal			= array();
						foreach($value as $val)
						{
							$item		=& $val;
							$item		= stripslashes($item);
							$item		= rawurldecode($item);
							$arrVal[]	= $item;
						}

						$arguments[]	= $arrVal;
					}
					else
					{
						$value			= stripslashes( $value );
						$value			= rawurldecode( $value );
						$arguments[]	= $value;
					}
				}
			}

			if(!method_exists( $view , $viewLayout ) )
			{
				$ajax	= new Disjax();
				$ajax->script( 'alert("' . JText::sprintf( 'Method %1$s does not exists in this context' , $viewLayout ) . '");');
				$ajax->send();

				return;
			}

			// Execute method
			call_user_func_array( array( $view , $viewLayout ) , $arguments );
		}
		else
		{
			$config	= DiscussHelper::getConfig();

			// Load necessary css and javascript files.
			DiscussHelper::loadHeaders();

			// Load theme css
			DiscussHelper::loadThemeCss();

			echo $this->getContents( $view , $viewLayout , $format , $tmpl , $cachable );
		}
	}

	public function getJomSocialToolbar( $format , $tmpl )
	{
		$config 	= DiscussHelper::getConfig();

		if( !$config->get( 'integration_jomsocial_toolbar' ) || $format == 'pdf' || $format == 'phocapdf' || $tmpl == 'component' )
		{
			return;
		}

		$jsFile 	=  JPATH_ROOT . '/components/com_community/libraries/core.php';

		$exists 	= JFile::exists( $jsFile );

		if( !$exists )
		{
			return;
		}


		require_once( $jsFile );
		require_once( JPATH_ROOT . '/components/com_community/libraries/toolbar.php' );

		$appsLib	= CAppPlugins::getInstance();
		$appsLib->loadApplications();

		$appsLib->triggerEvent( 'onSystemStart' , array() );

		// Since Jomsocial 4.0 render their icon on the page
        $svgFile = CFactory::getPath('template://assets/icon/joms-icon.svg');
        include_once $svgFile;

		ob_start();
		if( class_exists( 'CToolbarLibrary' ) )
		{
			echo '<div id="community-wrap">';
			if( method_exists( 'CToolbarLibrary' , 'getInstance' ) )
			{
				$jsToolbar  = CToolbarLibrary::getInstance();
				echo $jsToolbar->getHTML();
			}
			else
			{
				echo CToolbarLibrary::getHTML();
			}
			echo '</div>';
		}
		$contents 	= ob_get_contents();
		ob_end_clean();

		return $contents;
	}

	public function getContents( $view , $viewLayout , $format , $tmpl , $cachable = false )
	{
		// Prepare class names for wrapper
		$config 		= DiscussHelper::getConfig();
		$cat_id			= JRequest::getInt( 'category_id', '', 'GET' );
		$categoryClass	= $cat_id ? ' category-' . $cat_id : '';
		$suffix			= htmlspecialchars( $config->get( 'layout_wrapper_sfx', '' ) );

		if( !is_object( $view ) )
		{
			return;
		}

		$discussView	= ' discuss-view-' . $view->getName();

		// Try to get JomSocial toolbar
		$jsToolbar 		= $this->getJomSocialToolbar( $format , $tmpl );

		// We allow 3rd party to show jomsocial's toolbar even if integrations are disabled.
		$showJomsocial	= JRequest::getBool( 'showJomsocialToolbar' , false );
		$jomsocialClass	= $jsToolbar ? ' jomsocial-discuss' : '';

		$print 			= JRequest::getVar( 'print' );

		// Allow 3rd party to hide our headers
		$hideToolbar	= JRequest::getBool( 'hideToolbar' , false );

		$toolbar 		= '';

		ob_start();
		if(!$print && $format != 'pdf' && $format != 'feed' && !$hideToolbar )
		{
			$toolbar 	= $this->getToolbar( $view->getName() , $view->getLayout() );
		}

		if( $viewLayout != 'default' )
		{
			if( $cachable )
			{
				$cache		= JFactory::getCache( 'com_easydiscuss' , 'view' );
				$cache->get( $view , $viewLayout );
			}
			else
			{
				if( !method_exists( $view , $viewLayout ) )
				{
					$view->display();
				}
				else
				{
					$view->$viewLayout();
				}
			}
		}
		else
		{
			$view->display();
		}

		$contents 	= ob_get_contents();
		ob_end_clean();

		$theme 			= new DiscussThemes();

		$theme->set( 'discussView'	, $discussView );
		$theme->set( 'jomsocialClass', $jomsocialClass );
		$theme->set( 'suffix'		, $suffix );
		$theme->set( 'categoryClass', $categoryClass );

		$theme->set( 'jsToolbar'	, $jsToolbar );
		$theme->set( 'toolbar'		, $toolbar );
		$theme->set( 'contents'		, $contents );

		$output 		= $theme->fetch( 'structure.php' );

		return $output;
	}

	public function getToolbar( $currentView )
	{

	}
}
