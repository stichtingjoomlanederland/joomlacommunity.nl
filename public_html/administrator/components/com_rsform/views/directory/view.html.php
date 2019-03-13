<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2014 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

class RsformViewDirectory extends JViewLegacy
{
	public function display($tpl = null) {
        if (!JFactory::getUser()->authorise('directory.manage', 'com_rsform'))
        {
            throw new Exception(JText::_('COM_RSFORM_NOT_AUTHORISED_TO_USE_THIS_SECTION'));
        }

		// set title
		JToolbarHelper::title('RSForm! Pro', 'rsform');
		
		$layout = strtolower($this->getLayout());
		
		if ($layout == 'edit') {
			JToolbarHelper::apply('directory.apply');
			JToolbarHelper::save('directory.save');
			JToolbarHelper::cancel('directory.cancel');

            $this->user = JFactory::getUser();

            if ($this->user->authorise('forms.manage', 'com_rsform'))
            {
                JToolbarHelper::spacer();
                JToolbarHelper::custom('directory.cancelform', 'previous', 'previous', JText::_('RSFP_BACK_TO_FORM'), false);
            }
			
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
			JToolbarHelper::title(JText::_('RSFP_SUBM_DIR'),'rsform');
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
		$query = $db->getQuery(true)
            ->select($db->qn('formId'))
            ->from($db->qn('#__rsform_directory'))
            ->where($db->qn('formId') . ' = ' . $db->q($formId));

		return $db->setQuery($query)->loadResult();
	}

	public function getHeaderLabel($field)
    {
        JFactory::getApplication()->triggerEvent('rsfp_bk_onGetHeaderLabel', array(&$field->FieldName, $this->formId));

        $staticHeaders = RSFormProHelper::getDirectoryStaticHeaders();

        if ($field->componentId < 0 && isset($staticHeaders[$field->componentId]))
        {
            return JText::sprintf('RSFP_DIRECTORY_SUBMISSION_HEADER', $field->FieldName);
        }

        return $field->FieldName;
    }
}