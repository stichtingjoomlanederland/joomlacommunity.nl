<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2014 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');
jimport('joomla.html.pane');

class RsformViewSubmissions extends JViewLegacy
{
	function display($tpl = null)
	{
		$mainframe = JFactory::getApplication();
		
		JToolbarHelper::title('RSForm! Pro','rsform');
		
		// adding the toolbar on 2.5
		if (!RSFormProHelper::isJ('3.0')) {
			$this->addToolbar();
		}
		
		$this->tooltipClass = RSFormProHelper::getTooltipClass();
		
		$layout = strtolower($this->getLayout());
		if ($layout == 'export')
		{
			JToolbarHelper::custom('submissions.export.task', 'archive', 'archive', JText::_('RSFP_EXPORT'), false);
			JToolbarHelper::spacer();
			JToolbarHelper::cancel('submissions.manage');
			
			$this->formId = $this->get('formId');
			$this->headers = $this->get('headers');
			$this->staticHeaders = $this->get('staticHeaders');
			
			$previewArray = array();
			$i = 0;
			foreach ($this->staticHeaders as $header)
			{
				$i++;
				$previewArray[] = 'Value '.$i;
			}
			foreach ($this->headers as $header)
			{
				$i++;
				$previewArray[] = 'Value '.$i;
			}
			$this->previewArray = $previewArray;
			
			$this->formTitle = $this->get('formTitle');
			$this->exportSelected = $this->get('exportSelected');
			$this->exportSelectedCount = count($this->exportSelected);
			$this->exportAll = $this->exportSelectedCount == 0;
			$this->exportType = $this->get('exportType');
			$this->exportFile = $this->get('exportFile');
			
			$formTitle = $this->get('formTitle');
			JToolbarHelper::title('RSForm! Pro <small>['.JText::sprintf('RSFP_EXPORTING', $this->exportType, $formTitle).']</small>','rsform');
			
			// tabs
			$this->tabs = $this->get('RSTabs');
		}
		elseif ($layout == 'exportprocess')
		{
			$this->limit = 500;
			$this->total = $this->get('exportTotal');
			$this->file = JFactory::getApplication()->input->getCmd('ExportFile');
			$this->exportType = JFactory::getApplication()->input->getCmd('exportType');
			$this->formId	= $this->get('FormId');
			
			$formTitle = $this->get('formTitle');
			JToolbarHelper::title('RSForm! Pro <small>['.JText::sprintf('RSFP_EXPORTING', $this->exportType, $formTitle).']</small>','rsform');
		}
		elseif ($layout == 'edit')
		{
			JToolbarHelper::custom('submission.export.pdf', 'archive', 'archive', JText::_('RSFP_EXPORT_PDF'), false);
			JToolbarHelper::spacer();
			JToolbarHelper::apply('submissions.apply');
			JToolbarHelper::save('submissions.save');
			JToolbarHelper::spacer();
			JToolbarHelper::cancel('submissions.manage');
			
			$this->formId = $this->get('submissionFormId');
			$this->submissionId = $this->get('submissionId');
			$this->submission = $this->get('submission');
			$this->staticHeaders = $this->get('staticHeaders');
			$this->staticFields = $this->get('staticFields');
			$this->fields = $this->get('editFields');
		}
		else
		{
			JToolbarHelper::custom('submissions.export.csv', 'archive', 'archive', JText::_('RSFP_EXPORT_CSV'), false);
			JToolbarHelper::custom('submissions.export.ods', 'archive', 'archive', JText::_('RSFP_EXPORT_ODS'), false);
			JToolbarHelper::custom('submissions.export.excelxml', 'archive', 'archive', JText::_('RSFP_EXPORT_EXCEL_XML'), false);
			JToolbarHelper::custom('submissions.export.excel', 'archive', 'archive', JText::_('RSFP_EXPORT_EXCEL'), false);
			JToolbarHelper::custom('submissions.export.xml', 'archive', 'archive', JText::_('RSFP_EXPORT_XML'), false);
			JToolbarHelper::spacer();
			
			$backIcon = RSFormProHelper::isJ('3.0') ? 'previous' : 'back';
			$sendIcon = RSFormProHelper::isJ('3.0') ? 'mail' : 'send';
			
			JToolbarHelper::custom('submissions.cancelform', $backIcon, $backIcon, JText::_('RSFP_BACK_TO_FORM'), false);
			JToolbarHelper::spacer();
			JToolbarHelper::custom('submissions.resend', $sendIcon, $sendIcon, JText::_('RSFP_RESEND_EMAILS'), false);
			JToolbarHelper::editList('submissions.edit', JText::_('JTOOLBAR_EDIT'));
			JToolbarHelper::deleteList(JText::_('RSFP_ARE_YOU_SURE_DELETE'), 'submissions.delete', JText::_('JTOOLBAR_DELETE'));
			JToolbarHelper::spacer();
			JToolbarHelper::cancel('submissions.cancel', JText::_('JTOOLBAR_CLOSE'));
			
			$forms = $this->get('forms');
			$formId = $this->get('formId');
		
			$formTitle = $this->get('formTitle');
			JToolbarHelper::title('RSForm! Pro <small>['.$formTitle.']</small>','rsform');
			
			$this->form = RSFormProHelper::getForm($formId);
		
			$this->headers = $this->get('headers');
			$this->unescapedFields = $this->get('unescapedFields');
			$this->staticHeaders = $this->get('staticHeaders');
			$this->submissions = $this->get('submissions');
			$this->pagination = $this->get('pagination');
			$this->sortColumn = $this->get('sortColumn');
			$this->sortOrder = $this->get('sortOrder');
			$this->specialFields = $this->get('specialFields');
		
			$this->filter = $this->get('filter');
			$this->formId = $formId;
		
			$calendars['from'] = JHTML::calendar($this->get('dateFrom'), 'dateFrom', 'dateFrom');
			$calendars['to']   = JHTML::calendar($this->get('dateTo'), 'dateTo', 'dateTo');
			$this->calendars = $calendars;
		
			$lists['Languages'] = JHTML::_('select.genericlist', $this->get('languages'), 'Language', '', 'value', 'text', $this->get('lang'));
		
			$lists['forms'] = JHTML::_('select.genericlist', $forms, 'formId', 'onchange="submissionChangeForm(this.value)"', 'value', 'text', $formId);
			$this->lists = $lists;
		}
		
		parent::display($tpl);
	}
	
	function isHeaderEnabled($header, $static=0)
	{
		if (!isset($this->headersEnabled))
			$this->headersEnabled = $this->get('headersEnabled');
		
		$array = 'headers';
		if ($static)
			$array = 'staticHeaders';
		
		if (empty($this->headersEnabled->headers) && empty($this->headersEnabled->staticHeaders))
			return true;
		
		return in_array($header, $this->headersEnabled->{$array});
	}
	
	protected function addToolbar() {
		static $called;
		
		// this is a workaround so if called multiple times it will not duplicate the buttons
		if (!$called) {
			// set title
			JToolbarHelper::title('RSForm! Pro', 'rsform');
			
			require_once JPATH_COMPONENT.'/helpers/toolbar.php';
			RSFormProToolbarHelper::addToolbar('submissions');
			
			$called = true;
		}
	}
}