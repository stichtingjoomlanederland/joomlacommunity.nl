<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2014 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('_JEXEC') or die('Restricted access');

class RsformModelRsform extends JModelLegacy
{
	protected $config;
	
	public function __construct() {
		parent::__construct();
		$this->config = RSFormProConfig::getInstance();
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

		$user = JFactory::getUser();

		$buttons = array(
			array(
				'link' 		=> JRoute::_('index.php?option=com_rsform&view=forms'),
				'image' 	=> JHtml::image('com_rsform/admin/forms.png', '', null, true, 1),
				'text' 		=> JText::_('RSFP_MANAGE_FORMS'),
				'access' 	=> $user->authorise('forms.manage', 'com_rsform'),
				'target' 	=> ''
			),
			array(
				'link' 		=> JRoute::_('index.php?option=com_rsform&view=submissions'),
				'image' 	=> JHtml::image('com_rsform/admin/viewdata.png', '', null, true, 1),
				'text' 		=> JText::_('RSFP_MANAGE_SUBMISSIONS'),
                'access' 	=> $user->authorise('submissions.manage', 'com_rsform'),
				'target' 	=> ''
			),
			array(
				'link' 		=> JRoute::_('index.php?option=com_rsform&view=directory'),
				'image' 	=> JHtml::image('com_rsform/admin/directory.png', '', null, true, 1),
				'text' 		=> JText::_('RSFP_MANAGE_DIRECTORY_SUBMISSIONS'),
                'access' 	=> $user->authorise('directory.manage', 'com_rsform'),
				'target' 	=> ''
			),
			array(
				'link' 		=> JRoute::_('index.php?option=com_rsform&view=backuprestore'),
				'image' 	=> JHtml::image('com_rsform/admin/backup.png', '', null, true, 1),
				'text' 		=> JText::_('RSFP_BACKUP_RESTORE'),
                'access' 	=> $user->authorise('backuprestore.manage', 'com_rsform'),
				'target' 	=> ''
			),
			array(
				'link' 		=> JRoute::_('index.php?option=com_rsform&view=configuration'),
				'image' 	=> JHtml::image('com_rsform/admin/config.png', '', null, true, 1),
				'text' 		=> JText::_('RSFP_CONFIGURATION'),
                'access' 	=> $user->authorise('core.admin', 'com_rsform'),
				'target' 	=> ''
			),
			array(
				'link' 		=> 'https://www.rsjoomla.com/support/documentation/rsform-pro/plugins-and-modules.html',
				'image' 	=> JHtml::image('com_rsform/admin/samples.png', '', null, true, 1),
				'text' 		=> JText::_('RSFP_PLUGINS'),
				'access' 	=> true,
				'target' 	=> '_blank'
			),
			array(
				'link' 		=> 'https://www.rsjoomla.com/support/documentation/rsform-pro.html',
				'image' 	=> JHtml::image('com_rsform/admin/docs.png', '', null, true, 1),
				'text' 		=> JText::_('RSFP_USER_GUIDE'),
				'access' 	=> true,
				'target' 	=> '_blank'
			),
			array(
				'link' 		=> 'https://www.rsjoomla.com/support.html',
				'image' 	=> JHtml::image('com_rsform/admin/support.png', '', null, true, 1),
				'text' 		=> JText::_('RSFP_SUPPORT'),
				'access' 	=> true,
				'target' 	=> '_blank'
			),
		);
		
		return $buttons;
	}
}