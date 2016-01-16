<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' );

class rseventsproViewMessage extends JViewLegacy
{
	protected $form;
	protected $type;
	protected $types;
	
	public function display($tpl = null) {
		require_once JPATH_SITE.'/components/com_rseventspro/helpers/placeholders.php';
		
		$this->form			= $this->get('Form');
		$this->type			= $this->get('Type');
		$this->types		= $this->get('Types');
		$this->placeholders = RSEventsProPlaceholders::get($this->type);
		
		if (!in_array($this->type,$this->types)) {
			JFactory::getApplication()->enqueueMessage(JText::_('COM_RSEVENTSPRO_INVALID_EMAIL_TYPE'),'error');
			JFactory::getApplication()->redirect(JRoute::_('index.php?option=com_rseventspro&view=messages',false));
		}
		
		$this->addToolBar();
		parent::display($tpl);
	}
	
	protected function addToolBar() {
		JToolBarHelper::title(JText::_('COM_RSEVENTSPRO_MESSAGE_'.strtoupper($this->type)),'rseventspro48');
		JToolBarHelper::apply('message.apply');
		JToolBarHelper::save('message.save');
		JToolBarHelper::cancel('message.cancel');
		
		if (rseventsproHelper::isJ3()) {
			JHtml::_('rseventspro.chosen','select');
		}
	}
}