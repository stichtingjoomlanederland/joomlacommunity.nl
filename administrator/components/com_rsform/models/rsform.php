<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2014 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('_JEXEC') or die('Restricted access');

class RSFormModelRSForm extends JModelLegacy
{
	protected $isJ30;
	protected $config;
	
	public function __construct() {
		parent::__construct();
		
		$jversion 	  = new JVersion();
		
		$this->config = RSFormProConfig::getInstance();
		$this->isJ30  = $jversion->isCompatible('3.0');
	}
	
	public function getCode() {
		return $this->config->get('global.register.code');
	}
	
	public function getSideBar() {
		require_once JPATH_COMPONENT.'/helpers/toolbar.php';
		
		return RSFormProToolbarHelper::render();
	}
	
	public function getButtons() {
		JFactory::getLanguage()->load('com_rsfirewall.sys', JPATH_ADMINISTRATOR);
		
		/* $button = array(
				'access', 'id', 'link', 'target', 'onclick', 'title', 'image', 'alt', 'text'
			); */
		
		$buttons = array(
			array(
				'link' 		=> JRoute::_('index.php?option=com_rsform&view=forms'),
				'image' 	=> 'components/com_rsform/assets/images/forms.png',
				'text' 		=> JText::_('RSFP_MANAGE_FORMS'),
				'access' 	=> true,
				'target' 	=> ''
			),
			array(
				'link' 		=> JRoute::_('index.php?option=com_rsform&view=submissions'),
				'image' 	=> 'components/com_rsform/assets/images/viewdata.png',
				'text' 		=> JText::_('RSFP_MANAGE_SUBMISSIONS'),
				'access' 	=> true,
				'target' 	=> ''
			),
			array(
				'link' 		=> JRoute::_('index.php?option=com_rsform&view=directory'),
				'image' 	=> 'components/com_rsform/assets/images/directory.png',
				'text' 		=> JText::_('RSFP_MANAGE_DIRECTORY_SUBMISSIONS'),
				'access' 	=> true,
				'target' 	=> ''
			),
			array(
				'link' 		=> JRoute::_('index.php?option=com_rsform&view=backuprestore'),
				'image' 	=> 'components/com_rsform/assets/images/backup.png',
				'text' 		=> JText::_('RSFP_BACKUP_RESTORE'),
				'access' 	=> true,
				'target' 	=> ''
			),
			array(
				'link' 		=> JRoute::_('index.php?option=com_rsform&view=configuration'),
				'image' 	=> 'components/com_rsform/assets/images/config.png',
				'text' 		=> JText::_('RSFP_CONFIGURATION'),
				'access' 	=> true,
				'target' 	=> ''
			),
			array(
				'link' 		=> JRoute::_('index.php?option=com_rsform&view=updates'),
				'image' 	=> 'components/com_rsform/assets/images/restore.png',
				'text' 		=> JText::_('RSFP_UPDATES'),
				'access' 	=> true,
				'target' 	=> ''
			),
			array(
				'link' 		=> 'https://www.rsjoomla.com/support/documentation/view-knowledgebase/26-plugins-and-modules.html',
				'image' 	=> 'components/com_rsform/assets/images/samples.png',
				'text' 		=> JText::_('RSFP_PLUGINS'),
				'access' 	=> true,
				'target' 	=> '_blank'
			),
			array(
				'link' 		=> 'https://www.rsjoomla.com/support/documentation/view-knowledgebase/21-rsform-pro-user-guide.html',
				'image' 	=> 'components/com_rsform/assets/images/docs.png',
				'text' 		=> JText::_('RSFP_USER_GUIDE'),
				'access' 	=> true,
				'target' 	=> '_blank'
			),
			array(
				'link' 		=> 'https://www.rsjoomla.com/support.html',
				'image' 	=> 'components/com_rsform/assets/images/support.png',
				'text' 		=> JText::_('RSFP_SUPPORT'),
				'access' 	=> true,
				'target' 	=> '_blank'
			),
		);
		
		return $buttons;
	}
	
	public function getRevision() {
		$version = new RSFormProVersion();
		return $version->revision;
	}
}