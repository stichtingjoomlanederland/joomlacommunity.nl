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

class KomentoViewSystem extends KomentoAdminView
{
	public function display($tpl = null)
	{
		// This is necessary for tabbing.
		jimport('joomla.html.pane');

		$layout = JRequest::getVar('layout');

		$user		= JFactory::getUser();
		$mainframe	= JFactory::getApplication();

		if( Komento::joomlaVersion() >= '1.6' )
		{
			if(!$user->authorise('komento.manage.system' , 'com_komento') )
			{
				$mainframe->redirect( 'index.php' , JText::_( 'JERROR_ALERTNOAUTHOR' ) , 'error' );
				$mainframe->close();
			}
		}

		$konfig		= Komento::getKonfig();

		$version	= Komento::joomlaVersion();

		$this->assignRef( 'konfig'			, $konfig );
		$this->assignRef( 'joomlaVersion'	, $version );
		$this->assignRef( 'layout'	, $layout );

		parent::display($tpl);
	}

	public function registerToolbar()
	{
		$mainframe = JFactory::getApplication();
		$component = $mainframe->getUserStateFromRequest( 'com_komento.acl.component', 'component', 'com_content' );

		JToolBarHelper::title( JText::_( 'COM_KOMENTO_CONFIGURATION' ) , 'system' );
		JToolBarHelper::back( 'Home' , 'index.php?option=com_komento');
		JToolBarHelper::divider();
		JToolBarHelper::apply( 'apply' );
		JToolBarHelper::save();
		JToolBarHelper::divider();
		JToolBarHelper::cancel();
	}

	public function renderSetting( $text, $configName, $type = 'checkbox', $options = '' )
	{
		$type = 'render'.$type;

		$config = Komento::getKonfig();
		$state = $config->get( $configName, '' );

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

	public function getComponentSelection()
	{
		$query = "SELECT DISTINCT `component` FROM `#__komento_comments`";
		$sql = Komento::getSql();
		$sql->raw( $query );

		$result = $sql->loadColumn();

		$options = array( $this->renderOption( 'all', '*' ) );

		foreach( $result as $row )
		{
			$options[] = $this->renderOption( $row, Komento::loadApplication( $row )->getComponentName() );
		}

		$html = JHtml::_('select.genericlist', $options, 'componentSelection', '.fa- class="inputbox componentSelection"', 'value', 'text' );

		return $html;
	}

	public function getArticleSelection()
	{
		$options = array( $this->renderOption( 'all', '*' ) );

		$html = JHtml::_('select.genericlist', $options, 'articleSelection', '.fa- class="inputbox articleSelection"', 'value', 'text' );

		return $html;
	}
}
