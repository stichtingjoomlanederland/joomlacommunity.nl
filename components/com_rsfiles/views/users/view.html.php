<?php
/**
* @package RSFiles!
* @copyright (C) 2010-2014 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined('_JEXEC') or die('Restricted access');

class rsfilesViewUsers extends JViewLegacy
{
	protected $items;
	protected $pagination;
	protected $state;
	
	public function display($tpl = null) {
		if (!rsfilesHelper::briefcase('CanMaintainBriefcase')) {
			throw new Exception(JText::_('COM_RSFILES_CANNOT_CREATE_BRIEFCASE_USERS'), 500);
		}
		
		$this->items		= $this->get('Items');
		$this->pagination	= $this->get('Pagination');
		$this->state		= $this->get('State');

		parent::display($tpl);
	}
}