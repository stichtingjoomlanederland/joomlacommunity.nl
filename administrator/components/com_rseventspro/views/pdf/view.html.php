<?php
/**
* @version 1.0.0
* @package RSEvents!Pro 1.0.0
* @copyright (C) 2011 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' ); 
jimport( 'joomla.application.component.view');

class rseventsproViewPdf extends JViewLegacy
{
	protected $buffer;
	
	public function display($tpl = null) {
		if ($this->_load()) {
			JFactory::getDocument()->setMimeEncoding('application/pdf');
			$pdf = RSEventsProPDF::getInstance();
			
			if ($id = JFactory::getApplication()->input->getInt('id',0))
				$this->buffer 		= $pdf->ticket($id);
			
			if ($eid = JFactory::getApplication()->input->getInt('eid',0))
				$this->buffer 		= $pdf->tickets($eid);
			
			if ($this->buffer === false)
				JFactory::getApplication()->redirect('index.php?option=com_rseventspro', JText::_('COM_RSEVENTSPRO_ERROR_WHILE_LOADING_PDF'));
			
		}
		parent::display($tpl);
	}
	
	protected function _load() {
		if (file_exists(JPATH_SITE.'/components/com_rseventspro/helpers/pdf.php')) {
			require_once JPATH_SITE.'/components/com_rseventspro/helpers/pdf.php';
			return true;
		}
		
		return false;
	}
}