<?php
/**
* @package RSFiles!
* @copyright (C) 2010-2014 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined('_JEXEC') or die('Restricted access');

class rsfilesViewEmail extends JViewLegacy
{
	protected $form;
	protected $type;
	protected $types;
	
	public function display($tpl = null) {
		$this->form			= $this->get('Form');
		$this->type			= $this->get('Type');
		$this->types		= $this->get('Types');
		
		if (!in_array($this->type,$this->types)) {
			echo rsfilesHelper::modalClose();
			JFactory::getApplication()->close();
		}
		
		parent::display($tpl);
	}
}