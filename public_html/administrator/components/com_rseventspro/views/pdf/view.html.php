<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' );

class rseventsproViewPdf extends JViewLegacy
{
	protected $buffer;
	
	public function display($tpl = null) {
		if ($this->_load()) {
			JFactory::getDocument()->setMimeEncoding('application/pdf');
			
			try {
				$pdf = RSEventsProPDF::getInstance();
			
				if ($id = JFactory::getApplication()->input->getInt('id',0)) {
					$this->buffer = $pdf->ticket($id);
				}
			
				if ($eid = JFactory::getApplication()->input->getInt('eid',0)) {
					$this->buffer = $pdf->tickets($eid);
				}
			
				if ($this->buffer === false) {
					JFactory::getApplication()->redirect('index.php?option=com_rseventspro', JText::_('COM_RSEVENTSPRO_ERROR_WHILE_LOADING_PDF'));
				}
			} catch (Exception $e) {
				JFactory::getApplication()->enqueueMessage($e->getMessage());
				JFactory::getApplication()->redirect('index.php?option=com_rseventspro');
			}
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