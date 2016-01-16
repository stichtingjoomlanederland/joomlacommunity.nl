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

require( KOMENTO_ADMIN_ROOT . DIRECTORY_SEPARATOR . 'views.php');

class KomentoViewAcl extends KomentoAdminView
{
	public function display($tpl = null)
	{
		$user = JFactory::getUser();
		$mainframe = JFactory::getApplication();

		if( Komento::joomlaVersion() >= '1.6' )
		{
			if(!$user->authorise('komento.manage.acl' , 'com_komento') )
			{
				$mainframe->redirect( 'index.php' , JText::_( 'JERROR_ALERTNOAUTHOR' ) , 'error' );
				$mainframe->close();
			}
		}

		$component = JRequest::getString('component', '');

		if ($component == '') {
			$components = Komento::getHelper( 'components' )->getAvailableComponents();
			$this->assignRef( 'components', $components );
			parent::display( 'component' );
		} else {
			$components = $this->getComponentState( $component );
			$usergroups = Komento::getUsergroups();

			$this->assignRef( 'usergroups', $usergroups );
			$this->assignRef( 'component', $component );
			$this->assignRef( 'components', $components );

			parent::display( $tpl );
		}
	}

	public function form($tpl = null)
	{
		$user		= JFactory::getUser();
		$mainframe	= JFactory::getApplication();

		if( Komento::joomlaVersion() >= '1.6' )
		{
			if(!$user->authorise('komento.manage.acl' , 'com_komento') )
			{
				$mainframe->redirect( 'index.php' , JText::_( 'JERROR_ALERTNOAUTHOR' ) , 'error' );
				$mainframe->close();
			}
		}

		$component = JRequest::getString( 'component', '' );

		if( $component == '' )
		{
			$mainframe->redirect( 'index.php?option=com_komento&view=acl' );
		}

		$components = $this->getComponentState( $component );

		$id			= $mainframe->getUserStateFromRequest( 'com_komento.acl.id', 'id', '0' );
		$type		= JRequest::getCmd( 'type', 'usergroup' );

		$usergroups	= '';
		if( $type == 'usergroup' )
		{
			$usergroups = $this->getUsergroupState( $id );
		}

		$type = JRequest::getCmd( 'type' );
		$id = JRequest::getInt( 'id' );

		$model = Komento::getModel( 'acl', true );

		$model->updateUserGroups( $component );

		$rulesets = $model->getData( $component, $type, $id );

		$this->assignRef( 'rulesets', $rulesets );
		$this->assignRef( 'component', $component );
		$this->assignRef( 'components', $components );
		$this->assignRef( 'type', $type );
		$this->assignRef( 'id', $id );
		$this->assignRef( 'usergroups', $usergroups );

		parent::display( $tpl );
	}

	public function registerToolbar()
	{
		$mainframe = JFactory::getApplication();
		$component = JRequest::getString('component', '');
		$id = JRequest::getInt('id', '');

		if ($component == '') {
			JToolBarHelper::title(JText::_('COM_KOMENTO_ACL'), 'acl');
		} else {
			$application = Komento::loadApplication($component);
			$name = $application->getComponentName();

			JToolBarHelper::title(JText::_('COM_KOMENTO_ACL') . ': ' . $name, 'acl');
		}

		if ($component == '') {
			JToolBarHelper::back(JText::_('COM_KOMENTO_BACK'), 'index.php?option=com_komento');
		} else {
			
			if ($id == '') {
				JToolBarHelper::back(JText::_('COM_KOMENTO_BACK'), 'index.php?option=com_komento&view=acl');
			} else {
				JToolBarHelper::back(JText::_('COM_KOMENTO_BACK'), 'index.php?option=com_komento&view=acl&component=' . $component);
			}
		}

		if (JRequest::getCmd('layout') == 'form') {
			JToolBarHelper::divider();

			JToolBarHelper::custom('enableall', 'plus', '', JText::_('COM_KOMENTO_ACL_ENABLE_ALL'), false);
			JToolBarHelper::custom('disableall', 'minus', '', JText::_('COM_KOMENTO_ACL_DISABLE_ALL'), false);

			JToolBarHelper::divider();
			JToolBarHelper::apply('apply');
			JToolBarHelper::save();
			JToolBarHelper::divider();
			JToolBarHelper::cancel();
		}
	}

	function getComponentState($filter_component = '')
	{
		$result		= Komento::getHelper( 'components' )->getAvailableComponents();
		$components	= array();

		foreach( $result as $item )
		{
			$components[$item] = JHTML::_( 'select.option', $item, Komento::loadApplication( $item )->getComponentName() );
		}

		return JHTML::_( 'select.genericlist', $components, 'component', 'class="inputbox" .fa- onchange="submitform();"', 'value', 'text', $filter_component );
	}

	function getUsergroupState($filter_usergroup = '')
	{
		$result = Komento::getUsergroups();
		$usergroups = array();

		foreach( $result as $item )
		{
			$usergroups[] = JHTML::_( 'select.option', $item->id, str_repeat( '|—', $item->depth ) . ' ' . $item->title );
		}

		return JHTML::_( 'select.genericlist', $usergroups, 'id', 'class="inputbox" .fa- onchange="submitform();"', 'value', 'text', $filter_usergroup );
	}

	public function addComponent( $component )
	{
		// $package = Komento::getPackage();
		// if( $package !== 'paid' && in_array( $component, Komento::getPaidComponents() ) )
		// {
		// 	return;
		// }

		$application = Komento::loadApplication( $component );

		$link = JURI::root() . 'administrator/index.php?option=com_komento&view=acl&component=' . $component;
		$image = $application->getComponentIcon();
		$name = $application->getComponentName();
		?>
		<li>
			<a href="<?php echo $link;?>">
				<img src="<?php echo $image; ?>" width="32" />
				<span class="item-title"><?php echo $name; ?></span>
			</a>
		</li>
		<?php
	}
}
