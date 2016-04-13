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

if (ED::getJoomlaVersion() >= '3.0') {
	class EasyDiscussViewParent extends JViewLegacy
	{
		public function __construct($config = array())
		{
			return parent::__construct($config);
		}
	}
} else {

	jimport('joomla.application.component.view');

	class EasyDiscussViewParent extends JView
	{
		public function __construct($config = array())
		{
			return parent::__construct($config);
		}
	}
}

class EasyDiscussAdminView extends EasyDiscussViewParent
{
	public $panelTitle = '';
	public $panelDescription = '';

	public function __construct()
	{
		parent::__construct();

        $this->doc = JFactory::getDocument();
		$this->app = JFactory::getApplication();
		$this->my = JFactory::getUser();
		$this->config = ED::config();
        $this->jconfig = ED::jconfig();
        $this->input = ED::request();
        $this->theme = ED::themes();

        if ($this->doc->getType() != 'html') {
        	$this->ajax = ED::ajax();
    	}
	}

	/**
	 * Alias to $app->getUserStateFromRequest
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getUserState($key, $name, $default = '', $type = 'string')
	{
		return $this->app->getUserStateFromRequest('com_easydiscuss.' . $key, $name, $default, $type);
	}

	/**
	 * Allows child to set variables
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function set($key, $value = '')
	{
        if ($this->doc->getType() == 'json') {
            $this->props[$key] = $value;

            return;
        }

		$this->theme->set($key, $value);
	}

	/**
	 * Checks if the current viewer can really access this section
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function checkAccess($rule)
	{
		if (!$this->my->authorise($rule , 'com_easydiscuss')) {
            ED::setMessage(JText::_('JERROR_ALERTNOAUTHOR'), 'error');
            return $this->app->redirect('index.php?option=com_easydiscuss');
		}
	}

	public function display($tpl = null)
	{
		$format = $this->input->get('format', 'html', 'word');
		$view = $this->getName();
		$layout = $this->getLayout();

		$tpl = 'admin/' . $tpl;

		if ($this->doc->getType() == 'html') {

			// Initialize whatever that is necessary
			JHTML::_('behavior.framework', true);

			ED::init('admin');

			// get the bbcode settings
			$bbcodeSettings = $this->theme->output('admin/structure/settings');

			// Get the contents of the view.
			$contents = $this->theme->output($tpl);

            // attached bbcode settings
			$contents = $bbcodeSettings . $contents;

			// We need to output the structure
			$theme = ED::themes();

            // Set the ajax url
            $ajaxUrl = JURI::root() . 'administrator/index.php';

            $browse = $this->input->get('browse', '', 'default');

            // Get the sidebar
            $sidebar = $this->getSidebar();

            $message = ED::getMessageQueue();

            $theme->set('title', $this->panelTitle);
            $theme->set('desc', $this->panelDescription);
            $theme->set('message', $message);
            $theme->set('sidebar', $sidebar);
            $theme->set('browse', $browse);
			$theme->set('contents', $contents);
			$theme->set('layout', $layout);
			$theme->set('view', $view);
            $theme->set('ajaxUrl', $ajaxUrl);

			$output = $theme->output('admin/structure/default');

            // Get the scripts
            $scripts = ED::scripts()->getScripts();

			echo $output;
            echo $scripts;

			// If the toolbar registration exists, load it up
			if (method_exists($this, 'registerToolbar')) {
				$this->registerToolbar();
			}

			return;
		}
	}

	/**
	 * Prepares the sidebar
	 *
	 * @since	1.2
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getSidebar()
	{
		$file = JPATH_COMPONENT . '/defaults/menus.json';
		$contents = JFile::read($file);

		$view = $this->input->get('view', '', 'cmd');
		$layout = $this->input->get('layout', '', 'cmd');
		$result = json_decode($contents);
		$menus = array();

		foreach ($result as &$row) {

			// Check if the user is allowed to view this sidebar
			if (isset($row->access) && $row->access) {
		        if (!$this->my->authorise($row->access, 'com_easydiscuss')) {
		        	continue;
		        }
		    }

			if (!isset($row->view)) {
				$row->link = 'index.php?option=com_easydiscuss';
				$row->view = '';
			}

			if (isset($row->counter)) {
				$row->counter = $this->getCounter($row->counter);
			}

			if (!isset($row->link)) {
				$row->link = 'index.php?option=com_easydiscuss&view=' . $row->view;
			}

			// Translate the sidebar title
			$row->title = JText::_($row->title);

			// Default properties of each menu
			$row->class = $view == $row->view ? ' active ' : '';

			if (isset($row->childs) && $row->childs) {

				foreach ($row->childs as &$child) {

					// Update the child's link
					$child->link = 'index.php?option=com_easydiscuss&view=' . $row->view;

					if ($child->url) {

						foreach ($child->url as $key => $value) {
							if (!empty($value)) {
								$child->link .= '&' . $key . '=' . $value;
							}

							// Determines if the child is active
							$child->class = '';

							if ($key == 'layout' && $layout == $value) {
								$child->class = 'active';
							}
						}

					}

					$child->title = JText::_($child->title);
				}
			} else {
				$row->childs = array();
			}

			$menus[] = $row;
		}

		$theme = ED::themes();
		$theme->set('layout', $layout);
		$theme->set('view', $view);
		$theme->set('menus', $menus);

		$output = $theme->output('admin/structure/sidebar');

		return $output;
	}

	/**
	 * Allows caller to set the title
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function title($title)
	{
		$this->panelTitle = JText::_($title);

		// Set the title in Joomla as well
		JToolBarHelper::title($this->panelTitle, $this->getName());

		// Always set the descripion unless caller explicitly want's to override this
		$this->panelDescription = JText::_($title . '_DESC');
	}

	/**
	 * Allows caller to set the title
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function desc($desc)
	{
		$this->panelDescription = JText::_($desc);
	}

	// public function getFilterState( $filter_state='*' )
	// {
	// 	$state[] = JHTML::_('select.option',  '', '- '. JText::_( 'COM_EASYDISCUSS_SELECT_STATE' ) .' -' );
	// 	$state[] = JHTML::_('select.option',  'P', JText::_( 'COM_EASYDISCUSS_PUBLISHED' ) );
	// 	$state[] = JHTML::_('select.option',  'U', JText::_( 'COM_EASYDISCUSS_UNPUBLISHED' ) );
	// 	$state[] = JHTML::_('select.option',  'A', JText::_( 'COM_EASYDISCUSS_PENDING' ) );

	// 	return JHTML::_('select.genericlist',   $state, 'filter_state', '  onchange="submitform( );"', 'value', 'text', $filter_state );
	// }
}

class OldMigration
{
	protected $breadcrumbs  = array();
	protected $panelTitle 	= '';

	function xdisplay( $cachable = false, $urlparams = false )
	{
		$document	= JFactory::getDocument();

		$viewType	= $document->getType();
		$viewName	= JRequest::getCmd( 'view'	, 'discuss' );
		$viewLayout	= JRequest::getCmd( 'layout', 'default' );
		$view		= $this->getView( $viewName, $viewType, '' );

		// Set the layout
		$view->setLayout($viewLayout);

		$format		= JRequest::getCmd( 'format' , 'html' );

		// Test if the call is for Ajax
		if( !empty( $format ) && $format == 'ajax' )
		{
			$data		= JRequest::get( 'POST' );
			$arguments	= array();

			foreach( $data as $key => $value )
			{
				if( JString::substr( $key , 0 , 5 ) == 'value' )
				{
					if(is_array($value))
					{
						$arrVal = array();
						foreach($value as $val)
						{
							$item	= $val;
							$item	= stripslashes($item);
							$item	= rawurldecode($item);
							$arrVal[] = $item;
						}

						$arguments[] = $arrVal;
					}
					else
					{
						$val	= stripslashes( $value );
						$val	= rawurldecode( $val );
						$arguments[] = $val;
					}
				}
			}

			if(!method_exists( $view , $viewLayout ) )
			{
				$disjax	= new Disjax();
				$disjax->script( 'alert("' . JText::sprintf( 'Method %1$s does not exists in this context' , $viewLayout ) . '");');
				$disjax->send();

				return;
			}

			// Execute method
			call_user_func_array( array( $view , $viewLayout ) , $arguments );
		}
		else
		{

			ED::loadHeaders();
			ED::loadThemeCss();

			// For the sake of loading the core.js in Joomla 1.6 (1.6.2 onwards)
			if( ED::getJoomlaVersion() >= '1.6' )
			{
				JHTML::_('behavior.framework');
			}

			// Non ajax calls.
			// Get/Create the model
			if ($model = $this->getModel($viewName))
			{
				// Push the model into the view (as default)
				$view->setModel($model, true);
			}

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
		}
	}

	/**
	 * Overrides parent method
	 **/
	public static function getInstance( $controllerName, $config = array() )
	{
		static $instances;

		if( !$instances )
		{
			$instances	= array();
		}

		// Set the controller name
		$className	= 'EasyDiscussController' . ucfirst( $controllerName );

		if( !isset( $instances[ $className ] ) )
		{
			if( !class_exists( $className ) )
			{
				jimport( 'joomla.filesystem.file' );
				$controllerFile	= DISCUSS_CONTROLLERS . '/' . JString::strtolower( $controllerName ) . '.php';

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

			$instances[ $className ]	= new $className();
		}
		return $instances[ $className ];
	}

	function ajaxGetSystemString()
	{
		$data = JRequest::getVar('data');
		echo JText::_(strtoupper($data));
	}


	public function displayx($tpl = null)
	{
		$active		= JRequest::getCmd( 'view' , 'discuss' );
		$frontCss	= ( $active == 'discuss' ) ? ' front' : '';
		$menus		= $this->getXMLData( JPATH_COMPONENT . '/views/menu.xml' );
		$browseMode	= JRequest::getCmd('browse');

		$message	= DiscussHelper::getMessageQueue();

		echo '<div id="ed">';

				if( !$browseMode )
				{
					include( dirname( __FILE__ ) . '/themes/default/sidebar.php' );
				}

				echo $browseMode ? '<div>' : '<div class="content' . $frontCss . '">';
					if( !$browseMode )
					{
						include( dirname( __FILE__ ) . '/themes/default/breadcrumbs.php' );
					}

					if( isset( $this->panelTitle ) && !empty( $this->panelTitle ) )
					{
						echo '<div class="content-top">';
							echo '<h2 class="panel-title panel-title-alt">'. $this->panelTitle . '</h2>';
						echo '</div>';
					}

					echo '<div class="wrapper clearfix clear accordion">';

					include( dirname( __FILE__ ) . '/themes/default/notice.php' );

					echo $this->_formStart($active);
						parent::display( $tpl );
					echo $this->_formEnd($active);
					echo '</div>';

				echo '</div>';

		echo '</div>';
	}

	private function _formStart( $view )
	{
		if( $view !== 'settings' )
		{
			return;
		}

		return '<form action="index.php" method="post" name="adminForm" id="adminForm">';
	}

	private function _formEnd( $view )
	{
		if( $view !== 'settings' )
		{
			return;
		}

		ob_start(); ?>
		<input type="hidden" name="child" value="<?php echo JRequest::getCmd('child'); ?>" />
		<input type="hidden" name="layout" value="<?php echo JRequest::getCmd('layout'); ?>" />
		<input type="hidden" name="active" id="active" value="" />
		<input type="hidden" name="task" value="save" />
		<input type="hidden" name="option" value="com_easydiscuss" />
		<input type="hidden" name="controller" value="settings" />
		<?php echo JHTML::_( 'form.token' ); ?>
		</form>
		<?php

		$content = ob_get_clean();

		return $content;
	}

	public function getModel( $name = null )
	{
		static $model = array();

		if( !isset( $model[ $name ] ) )
		{
			$path = DISCUSS_ADMIN_ROOT . '/models/' . JString::strtolower( $name ) . '.php';

			jimport('joomla.filesystem.path');
			if ( !JFile::exists( $path ))
			{
				JError::raiseWarning( 0, 'Model file not found.' );
			}

			$modelClass = 'EasyDiscussModel' . ucfirst( $name );

			if( !class_exists( $modelClass ) )
				require_once( $path );


			$model[$name] = new $modelClass();
		}

		return $model[$name];
	}

	public function renderCheckbox( $configName , $state )
	{
		ob_start();
	?>

		<div class="btn-group-yesno"
			data-foundry-toggle="buttons-radio"
			>
			<button type="button" class="btn btn-yes<?php echo $state ? ' active' : '';?>" data-fd-toggle-value="1"><?php echo JText::_( 'COM_EASYDISCUSS_YES_OPTION' );?></button>
			<button type="button" class="btn btn-no<?php echo !$state ? ' active' : '';?>" data-fd-toggle-value="0"><?php echo JText::_( 'COM_EASYDISCUSS_NO_OPTION' );?></button>
			<input type="hidden" id="<?php echo empty( $id ) ? $configName : $id; ?>" name="<?php echo $configName ;?>" value="<?php echo $state ? '1' : '0'; ?>" />
		</div>
	<?php
		$html	= ob_get_contents();
		ob_end_clean();

		return $html;
	}

}
