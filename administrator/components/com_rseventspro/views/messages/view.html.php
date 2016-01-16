<?php
/**
* @version 1.0.0
* @package RSEvents!Pro 1.0.0
* @copyright (C) 2011 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' ); 
jimport('joomla.application.component.view');

class rseventsproViewMessages extends JViewLegacy
{
	protected $form;
	protected $type;
	protected $types;
	
	public function display($tpl = null) {
		$this->form			= $this->get('Form');
		$this->type			= $this->get('Type');
		$this->types		= $this->get('Types');
		
		if (!in_array($this->type,$this->types)) {
			echo rseventsproHelper::modalClose();
			JFactory::getApplication()->close();
		}
		
		parent::display($tpl);
	}
}