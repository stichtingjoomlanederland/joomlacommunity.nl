<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2014 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

class RSFormViewDirectory extends JViewLegacy
{
	public function display($tpl = null) {
		// set title
		JToolBarHelper::title('RSForm! Pro', 'rsform');
		
		// adding the toolbar on 2.5
		if (!RSFormProHelper::isJ('3.0')) {
			$this->addToolbar();
		}
		
		$layout = strtolower($this->getLayout());
		
		if ($layout == 'edit') {
			JToolBarHelper::apply('directory.apply');
			JToolBarHelper::save('directory.save');
			JToolBarHelper::cancel('directory.cancel');
			
			$backIcon = RSFormProHelper::isJ('3.0') ? 'previous' : 'back';
			JToolBarHelper::custom('directory.cancelform', $backIcon, $backIcon, JText::_('RSFP_BACK_TO_FORM'), false);
			
			RSFormProHelper::loadCodeMirror();
			
			$this->directory	= $this->get('Directory');
			$this->formId		= JFactory::getApplication()->input->getInt('formId',0);
			$this->tab			= JFactory::getApplication()->input->getInt('tab', 0);
			$this->emails		= $this->get('emails');
			$this->fields		= RSFormProHelper::getDirectoryFields($this->formId);
			$this->quickfields	= $this->get('QuickFields');
			
			$lists['ViewLayoutAutogenerate'] = RSFormProHelper::renderHTML('select.booleanlist', 'jform[ViewLayoutAutogenerate]', 'onclick="changeDirectoryAutoGenerateLayout('.$this->formId.', this.value);"', $this->directory->ViewLayoutAutogenerate);
			$lists['enablepdf'] = RSFormProHelper::renderHTML('select.booleanlist', 'jform[enablepdf]', '', $this->directory->enablepdf);
			$lists['enablecsv'] = RSFormProHelper::renderHTML('select.booleanlist', 'jform[enablecsv]', '', $this->directory->enablecsv);
			
			$this->lists		= $lists;
		} elseif ($layout == 'edit_emails') {
			$this->emails = $this->get('emails');
		} else {
			$this->addToolbar();
			JToolBarHelper::title(JText::_('RSFP_SUBM_DIR'),'rsform');
			JToolbarHelper::deleteList('','directory.remove');
			
			$this->sidebar		= $this->get('Sidebar');
			$this->forms		= $this->get('forms');
			$this->pagination	= $this->get('pagination');
			$this->sortColumn 	= $this->get('sortColumn');
			$this->sortOrder 	= $this->get('sortOrder');
		}
		
		parent::display($tpl);
	}
	
	protected function addToolbar() {
		static $called;
		
		// this is a workaround so if called multiple times it will not duplicate the buttons
		if (!$called) {			
			require_once JPATH_COMPONENT.'/helpers/toolbar.php';
			RSFormProToolbarHelper::addToolbar('directory');
			
			$called = true;
		}
	}
	
	public function getStatus($formId) {
		$db = JFactory::getDbo();
		
		$db->setQuery("SELECT COUNT(formId) FROM #__rsform_directory WHERE formId = ".(int) $formId." ");
		return $db->loadResult();
	}
}