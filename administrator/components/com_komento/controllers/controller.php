<?php
/**
* @package		Komento
* @copyright	Copyright (C) 2012 Stack Ideas Private Limited. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* Komento is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Restricted access');

require_once( dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'parent.php' );

class KomentoController extends KomentoParentController
{
	protected static $instances;

	public $messages = array();

	public function __construct($config = array())
	{
		$document	= JFactory::getDocument();
		//load dialog stylesheet in backend
		KomentoDocumentHelper::loadHeaders();

		$config		= Komento::getConfig();
		$konfig		= Komento::getKonfig();

		$toolbar	= JToolbar::getInstance( 'toolbar' );
		$toolbar->addButtonPath( KOMENTO_ADMIN_ROOT . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'images');

		if( $document->getType() == 'html' )
		{
			require_once(KOMENTO_CLASSES . DIRECTORY_SEPARATOR . 'configuration.php');

			$configuration = KomentoConfiguration::getInstance();

			$configuration->attach();
		}

		$version	= str_ireplace( '.' , '' , Komento::komentoVersion() );
		$document->addScript( rtrim( JURI::root() , '/' ) . '/administrator/components/com_komento/assets/js/admin.js?' . $version );
		$document->addStyleSheet( rtrim( JURI::root() , '/' ) . '/administrator/components/com_komento/assets/css/reset.css?' . $version );
		$document->addStyleSheet( rtrim( JURI::root() , '/' ) . '/components/com_komento/assets/css/common.css?' . $version );
		$document->addStyleSheet( rtrim( JURI::root() , '/' ) . '/administrator/components/com_komento/assets/css/style.css?' . $version );

		// For the sake of loading the core.js in Joomla 1.6 (1.6.2 onwards)
		if( Komento::joomlaVersion() >= '1.6' )
		{
			JHTML::_('behavior.framework');
		}

		parent::__construct($config);
	}

	public function display($cacheable = false, $urlparams = false)
	{
		/* [KOMENTO_FREE_VERSION_MESSAGE] */

		// free version text (for reference only)
		// JFactory::getApplication()->enqueueMessage( 'You are using Komento free version. <a href="http://stackideas.com/komento/plans.html" target="_blank">Upgrade to Komento Pro</a> to enjoy our priority support and more. <a href="http://stackideas.com/komento/plans.html" target="_blank" style="padding: 5px 10px; background-color: #C02828; color: #FFFFFF;">Upgrade now!</a>', 'notice' );

		$document	= JFactory::getDocument();

		// Set the layout
		$viewType	= $document->getType();
		$viewName	= JRequest::getCmd( 'view', $this->getName() );
		$viewLayout	= JRequest::getCmd( 'layout', 'default' );
		$view		= $this->getView( $viewName, $viewType, '' );
		$view->setLayout($viewLayout);

		$format		= JRequest::getCmd( 'format' , 'html' );

		// Test if the call is for Ajax
		if( !empty( $format ) && $format == 'ajax' )
		{
			// Ajax calls.
			if( !JRequest::checkToken( 'GET' ) )
			{
				$ejax	= new Ejax();
				$ejax->script( 'alert("' . JText::_('Not allowed here') . '");' );
				$ejax->send();
			}

			// Process Ajax call
			$data		= JRequest::get( 'POST' );
			$arguments	= array();

			foreach( $data as $key => $value )
			{
				if( JString::substr( $key , 0 , 5 ) == 'value' )
				{
					if(is_array($value))
					{
						$arrVal    = array();
						foreach($value as $val)
						{
							$item   =& $val;
							$item   = stripslashes($item);
							$item   = rawurldecode($item);
							$arrVal[]   = $item;
						}

						$arguments[]	= $arrVal;
					}
					else
					{
						$val			= stripslashes( $value );
						$val			= rawurldecode( $val );
						$arguments[]	= $val;
					}
				}
			}

			// if(!method_exists( $view , $viewLayout ) )
			// {
			// 	$ejax	= new Ejax();
			// 	$ejax->script( 'alert("' . JText::sprintf( 'Method %1$s does not exists in this context' , $viewLayout ) . '");');
			// 	$ejax->send();

			// 	return;
			// }

			// Execute method
			call_user_func_array( array( $view , $viewLayout ) , $arguments );
		}
		else
		{
			// Non ajax calls.

			if( $viewLayout != 'default' )
			{
				if( $cacheable )
				{
					$cache	= JFactory::getCache( 'com_komento' , 'view' );
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
						// @todo: Display error about unknown layout.
						$view->$viewLayout();
					}
				}
			}
			else
			{
				$view->display();
			}



			// Add necessary buttons to the site.
			if( method_exists( $view , 'registerToolbar' ) )
			{
				$view->registerToolbar();
			}

			// // Override submenu if needed
			// if( method_exists( $view , 'registerSubmenu' ) && $view->registerSubmenu() != ''  )
			// {
			// 	$this->loadSubmenu( $view->getName() , $view->registerSubmenu() );
			// }

			// @task: Append hidden token into the page.
			echo '<span id="komento-token" style="display:none;"><input type="hidden" name="' . Komento::_( 'getToken' ) . '" value="1" /></span>';
		}
	}

	public static function getInstance( $controllerName, $config = array() )
	{
		if( !self::$instances )
		{
			self::$instances = array();
		}

		$controllerName = preg_replace('/[^A-Z0-9_]/i', '', trim($controllerName));

		// Set the controller name
		$className	= 'KomentoController' . ucfirst( $controllerName );

		if( !isset( self::$instances[ $className ] ) )
		{
			if( !class_exists( $className ) )
			{
				jimport( 'joomla.filesystem.file' );
				$controllerFile	= KOMENTO_ADMIN_ROOT . DIRECTORY_SEPARATOR . 'controllers' . DIRECTORY_SEPARATOR . JString::strtolower( $controllerName ) . '.php';

				if( JFile::exists( $controllerFile ) )
				{
					require_once( $controllerFile );

					if( !class_exists( $className ) )
					{
						// Controller does not exists, throw some error.
						JError::raiseError( '500' , JText::sprintf('Controller %1$s not found' , $className ) );
					}
				}
				else
				{
					// File does not exists, throw some error.
					JError::raiseError( '500' , JText::sprintf('Controller %1$s.php not found' , $controllerName ) );
				}
			}

			self::$instances[ $className ]	= new $className();
		}

		return self::$instances[ $className ];
	}

	private function loadSubmenu( $viewName , $path = 'submenu.php' )
	{
		JHTML::_('behavior.switcher');

		//Build submenu
		$contents = '';
		ob_start();
			require_once( KOMENTO_ADMIN_ROOT . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR . $viewName . DIRECTORY_SEPARATOR . 'tmpl' . DIRECTORY_SEPARATOR . $path );
			$contents = ob_get_contents();
		ob_end_clean();

		$document = JFactory::getDocument();
		$document->setBuffer($contents, 'modules', 'submenu');
	}

	private function getCurrentUrl()
	{
		$url		= rtrim( JURI::root() , '/' );
		$currentURL	= isset( $_SERVER[ 'HTTP_HOST' ] ) ? $_SERVER[ 'HTTP_HOST' ] : '';

		if( !empty( $currentURL ) )
		{
			// When the url contains www and the current accessed url does not contain www, fix it.
			if( stristr($currentURL , 'www' ) === false && stristr( $url , 'www') !== false )
			{
				$url	= str_ireplace( 'www.' , '' , $url );
			}

			// When the url does not contain www and the current accessed url contains www.
			if( stristr( $currentURL , 'www' ) !== false && stristr( $url , 'www') === false )
			{
				$url	= str_ireplace( '://' , '://www.' , $url );
			}
		}

		return $url;
	}

	public function ajaxGetSystemString()
	{
		$data = JRequest::getVar('data');
		echo JText::_(strtoupper($data));
	}

	public function updateall()
	{
		require_once( JPATH_ROOT . DIRECTORY_SEPARATOR . 'administrator' . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_komento' . DIRECTORY_SEPARATOR . 'install.default.php' );
		$class = new KomentoDatabaseUpdate( $this );

		$class->update();
		$class->updateConfigParam();
		$class->updateACLParam();

		echo 'done';

		exit;
	}

	public function updatedb()
	{
		require_once( JPATH_ROOT . DIRECTORY_SEPARATOR . 'administrator' . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_komento' . DIRECTORY_SEPARATOR . 'install.default.php' );
		$class = new KomentoDatabaseUpdate( $this );

		if( $class->update() )
		{
			$type = 'message';
			$message = 'DB Updated';

			$this->setRedirect( 'index.php?option=com_komento' , $message , $type );
		}
		else
		{
			foreach( $this->messages as $msg )
			{
				echo $msg['message'] . "\n\n";
			}
			exit;
		}

		return;
	}

	public function updateconfig()
	{
		require_once( JPATH_ROOT . DIRECTORY_SEPARATOR . 'administrator' . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_komento' . DIRECTORY_SEPARATOR . 'install.default.php' );
		$class = new KomentoDatabaseUpdate( $this );

		if( $class->updateConfigParam() )
		{
			$type = 'message';
			$message = 'Config Updated';

			$this->setRedirect( 'index.php?option=com_komento' , $message , $type );
		}
		else
		{
			foreach( $this->messages as $msg )
			{
				echo $msg['message'] . "\n\n";
			}
			exit;
		}

		return;
	}

	public function updateacl()
	{
		require_once( JPATH_ROOT . DIRECTORY_SEPARATOR . 'administrator' . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_komento' . DIRECTORY_SEPARATOR . 'install.default.php' );
		$class = new KomentoDatabaseUpdate( $this );

		if( $class->updateACLParam() )
		{
			$type = 'message';
			$message = 'ACL Updated';

			$this->setRedirect( 'index.php?option=com_komento' , $message , $type );
		}
		else
		{
			foreach( $this->messages as $msg )
			{
				echo $msg['message'] . "\n\n";
			}
			exit;
		}

		return;
	}

	public function setMessage( $msg, $type = 'message' )
	{
		$this->messages[] = array( 'type' => strtolower($type), 'message' => $msg );
	}

	public function setErrorLog( $file, $line )
	{
		$msg = 'Error at function: ' . $file . ' line ' . $line;
		$this->setMessage( $msg, 'warning' );
	}

	public function cleardb()
	{
		$db = Komento::getDBO();

		$query = array();
		$query[] = 'DELETE FROM `#__komento_activities`';
		$query[] = 'DELETE FROM `#__komento_actions`';
		$query[] = 'DELETE FROM `#__komento_comments`';
		$query[] = 'DELETE FROM `#__komento_captcha`';
		$query[] = 'DELETE FROM `#__komento_mailq`';
		$query[] = 'DELETE FROM `#__komento_subscription`';

		foreach( $query as $q )
		{
			$db->setQuery( $q );
			$db->query();
		}

		$this->setRedirect( 'index.php?option=com_komento' , 'DB Reset' , 'message' );

		return;
	}

	public function approveComment()
	{
		$id = JRequest::getInt( 'commentId', '' );
		$comment = Komento::getComment( $id );
		Komento::setCurrentComponent( $comment->component );

		$acl = Komento::getHelper( 'acl' );

		$type = 'message';
		$message = '';

		if( !$acl->allow( 'publish', $comment ) )
		{
			$type = 'error';
			$message = JText::_( 'COM_KOMENTO_NOT_ALLOWED' );
			$this->setRedirect( 'index.php?option=com_komento', $message, $type );
			return false;
		}

		$model		= Komento::getModel( 'comments' );
		if( $model->publish( $id ) )
		{
			$message	= JText::_('COM_KOMENTO_COMMENTS_COMMENT_PUBLISHED');
		}
		else
		{
			$message	= JText::_( 'COM_KOMENTO_COMMENTS_COMMENT_PUBLISH_ERROR' );
			$type		= 'error';
		}

		$this->setRedirect( 'index.php?option=com_komento', $message, $type );
		return true;
	}

	public function pulldb()
	{
		$db = Komento::getDBO();

		$db->setQuery( 'select id, component, cid from #__komento_comments order by id' );
		$result = $db->loadObjectList();

		echo '<table><tr><td>id</td><td>component</td><td>cid</td></tr>';
		foreach( $result as $row )
		{
			echo '<tr>';
			echo '<td>' . $row->id . '</td>';
			echo '<td>' . $row->component . '</td>';
			echo '<td>' . $row->cid . '</td>';
			echo '</tr>';
		}
		echo '</table>';

		exit;
	}

	public function populatedb()
	{
		$db = Komento::getDBO();

		$articles = array('8', '50', '35');
		$authors = array( '0' => 'Alien', '42' => 'Super User', '62' => 'Jason' );
		$comments = array( 'hahaha :) love you', 'me too!!!', 'this is a link http://test.com', '[b]bbcode in use[/b] :( fuck you');

		$parentmap = array();

		$counter = 0;

		$date = Komento::getDate();

		for( $i = 0; $i < 20000; $i++ )
		{
			$date->sub( new DateInterval( 'PT' . ( 20001 - $i ) . 'S' ) );

			$modulo = $i % 3;
			$set = ( (int) ( $i / 3 ) );
			$setmodulo = (int) ( $set / 3 );
			$factor = $setmodulo * 6;

			$level = $set % 3 + 1;

			$lft = $level + $factor;
			$rgt = 7 - $level + $factor;

			$author = array_rand( $authors );

			$article = $articles[$modulo];

			$comment = array_rand( $comments );

			$parentid = 0;

			if( $level > 1 )
			{
				$parentid = $parentmap[$article][$setmodulo][$level - 1];
			}

			$table = Komento::getTable( 'comments' );
			$table->component = 'com_content';
			$table->comment = $comments[$comment];
			$table->cid = $article;
			$table->lft = $lft;
			$table->rgt = $rgt;
			$table->published = 1;
			$table->name = $authors[$author];
			$table->created_by = $author;
			$table->created = $date->toMySQL();
			$table->parent_id = $parentid;

			$result = $table->store();

			if( !$result )
			{
				var_dump( $table ); exit;
			}

			$parentmap[$article][$setmodulo][$level] = $table->id;
		}

		echo 'done'; exit;
	}

	public function debug()
	{
		$table = JRequest::getString( 'table' );

		$tablename = '#__' . $table;

		$db = Komento::getDBO();

		$db->setQuery( 'show columns from ' . $db->nameQuote( $tablename ) );
		$result = $db->loadObjectList();

		$html = '<table><tr>';

		foreach( $result as $row )
		{
			$html .= '<td>' . $row->Field . '</td>';
		}

		$html .= '</tr>';

		$db->setQuery( 'select * from ' . $db->nameQuote( $tablename ) . ' order by id' );
		$result = $db->loadObjectList();

		foreach( $result as $row )
		{
			$html .= '<tr>';

			foreach( $row as $key => $value )
			{
				$html .= '<td>' . $value . '</td>';
			}

			$html .= '</tr>';
		}

		$html .= '</table>';

		echo $html;

		exit;
	}
}
