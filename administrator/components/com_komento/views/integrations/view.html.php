<?php
/**
* @package      Komento
* @copyright    Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* Komento is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Restricted access');

require_once( KOMENTO_ADMIN_ROOT . DIRECTORY_SEPARATOR . 'views.php');

class KomentoViewIntegrations extends KomentoAdminView
{
	static $extension;

	public function display($tpl = null)
	{
		$user		= JFactory::getUser();
		$mainframe	= JFactory::getApplication();

		if( Komento::joomlaVersion() >= '1.6' )
		{
			if(!$user->authorise('komento.manage.integrations' , 'com_komento') )
			{
				$mainframe->redirect( 'index.php' , JText::_( 'JERROR_ALERTNOAUTHOR' ) , 'error' );
				$mainframe->close();
			}
		}

		// This is necessary for tabbing.
		jimport('joomla.html.pane');

		$app		= JFactory::getApplication();
		// $component	= $app->getUserStateFromRequest( 'com_komento.integrations.component' , 'component' , 'com_content' );
		$component	= JRequest::getString( 'component', '' );

		if( $component == '' )
		{
			$components = Komento::getHelper( 'components' )->getAvailableComponents();
			$this->assignRef( 'components', $components );
			parent::display( 'component' );
		}
		else
		{
			self::$extension	= $component;
			$config				= Komento::getConfig($component);
			$componentObj		= Komento::loadApplication( $component );

			$version			= Komento::joomlaVersion();

			// Get a list of components
			$components	= array();
			$result		= Komento::getHelper('components')->getAvailableComponents();

			// @task: Translate each component with human readable name.
			foreach( $result as $item )
			{
				$components[ $item ]	= Komento::loadApplication( $item )->getComponentName();
			}

			$this->assignRef( 'config'			, $config );
			$this->assignRef( 'joomlaVersion'	, $version );
			$this->assignRef( 'component'		, $component );
			$this->assignRef( 'components'		, $components );
			$this->assignRef( 'componentObj'	, $componentObj );

			parent::display($tpl);
		}
	}

	public function registerToolbar()
	{
		$mainframe = JFactory::getApplication();
		$component	= JRequest::getString( 'component', '' );

		JToolBarHelper::back( 'Home' , 'index.php?option=com_komento');
		JToolBarHelper::divider();

		if( $component == '' ) {
			JToolBarHelper::title( JText::_( 'COM_KOMENTO_INTEGRATIONS' ), 'integrations' );
		} else {
			$application = Komento::loadApplication( $component );
			$name = $application->getComponentName();
			JToolBarHelper::title( JText::_( 'COM_KOMENTO_INTEGRATIONS' ) . ': ' . $name , 'integrations' );

			JToolBarHelper::apply( 'apply' );
			JToolBarHelper::save();
			JToolBarHelper::divider();
			JToolBarHelper::cancel();
		}
	}

	public function getEditorList( $selected )
	{
		$db		= Komento::getDBO();

		// compile list of the editors
		if( Komento::joomlaVersion() >= '1.6' )
		{
			$query = 'SELECT `element` AS value, `name` AS text'
					.' FROM `#__extensions`'
					.' WHERE `folder` = "editors"'
					.' AND `type` = "plugin"'
					.' AND `enabled` = 1'
					.' ORDER BY ordering, name';
		}
		else
		{
			$query = 'SELECT element AS value, name AS text'
					.' FROM #__plugins'
					.' WHERE folder = "editors"'
					.' AND published = 1'
					.' ORDER BY ordering, name';
		}

		//echo $query;

		$db->setQuery( $query );
		$editors = $db->loadObjectList();

		if(count($editors) > 0)
		{
			if(Komento::joomlaVersion() >= '1.6')
			{
				$lang = JFactory::getLanguage();
				for($i = 0; $i < count($editors); $i++)
				{
					$editor = $editors[$i];
					$lang->load($editor->text . '.sys', JPATH_ADMINISTRATOR, null, false, false);
					$editor->text   = JText::_($editor->text);
				}
			}
		}

		// temporary. remove when wysiwyg editors are ready
		$editors = array();

		$bbcode = new stdClass();
		$bbcode->value = 'bbcode';
		$bbcode->text = JText::_( 'COM_KOMENTO_EDITOR_BBCODE' );

		$none = new stdClass();
		$none->value = 'none';
		$none->text = JText::_( 'COM_KOMENTO_EDITOR_NONE' );

		$editors[] = $bbcode;
		$editors[] = $none;

		return JHTML::_('select.genericlist',  $editors , 'form_editor', 'class="inputbox" .fa-', 'value', 'text', $selected );
	}

	public function renderSetting( $text, $configName, $type = 'checkbox', $options = '' )
	{
		$type = 'render'.$type;

		$state = Komento::getConfig( self::$extension )->get( $configName );

		if( ( $configName === 'email_regex' || $configName === 'website_regex' ) && is_array( $state ) )
		{
			$state = urldecode( $state[0] );
		}

		ob_start();
	?>
		<div class="form-group">
			<label class="col-md-5 control-label">
				<span class="<?php echo $configName; ?>"><?php echo JText::_( $text ); ?></span>
			</label>
			<div class="col-md-7">
				<div class="has-tip">
					<div class="tip"><i></i><?php echo JText::_( $text . '_DESC' ); ?></div>
					<?php echo $this->$type( $configName, $state, $options );?>
				</div>
			</div>
		</div>

	<?php
		$html = ob_get_contents();
		ob_end_clean();

		return $html;
	}

	public function addComponent( $component )
	{
		// $package = Komento::getPackage();
		// if( $package !== 'paid' && in_array( $component, Komento::getPaidComponents() ) )
		// {
		// 	return;
		// }

		$application = Komento::loadApplication( $component );

		$link = JURI::root() . 'administrator/index.php?option=com_komento&view=integrations&component=' . $component;
		$name = $application->getComponentName();
		$image = $application->getComponentIcon();
		?>
		<li>
			<a href="<?php echo $link;?>">
				<img src="<?php echo $image; ?>" width="32" />
				<span class="item-title"><?php echo $name; ?></span>
			</a>
		</li>
		<?php
	}

	public function getUsergroupsMultilist()
	{
		$usergroups = Komento::getUsergroups();
		foreach( $usergroups as $usergroup )
		{
			$usergroup->treename = str_repeat( '.&#160;&#160;&#160;', $usergroup->depth ) . ( $usergroup->depth > 0 ? '|_&#160;' : '' ) . $usergroup->title;
		}

		return $usergroups;
	}
}
