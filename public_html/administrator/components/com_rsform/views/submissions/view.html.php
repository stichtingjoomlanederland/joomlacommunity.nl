<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2014 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

class RsformViewSubmissions extends JViewLegacy
{
    protected $previewArray = array();
    protected $staticHeaders = array();
    protected $headers = array();

	public function display($tpl = null)
	{
        if (!JFactory::getUser()->authorise('submissions.manage', 'com_rsform'))
        {
            throw new Exception(JText::_('COM_RSFORM_NOT_AUTHORISED_TO_USE_THIS_SECTION'));
        }
		
		JToolbarHelper::title('RSForm! Pro','rsform');
		
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

			$i = 0;
			foreach ($this->staticHeaders as $header)
			{
				$i++;
				$this->previewArray[] = 'Value '.$i;
			}
			foreach ($this->headers as $header)
			{
				$i++;
				$this->previewArray[] = 'Value '.$i;
			}
			
			$this->formTitle = $this->get('formTitle');
			$this->exportSelected = $this->get('exportSelected');
			$this->exportSelectedCount = count($this->exportSelected);
			$this->exportAll = $this->exportSelectedCount == 0;
			$this->exportType = $this->get('exportType');
			$this->exportFile = $this->get('exportFile');

			JToolbarHelper::title('RSForm! Pro <small>['.JText::sprintf('RSFP_EXPORTING', $this->exportType, $this->formTitle).']</small>','rsform');
			
			// tabs
			$this->tabs = $this->get('RSTabs');
		}
        elseif ($layout == 'import')
        {
            JToolbarHelper::custom('submissions.importtask', 'archive', 'archive', JText::_('COM_RSFORM_IMPORT_SUBMISSIONS'), false);
            JToolbarHelper::spacer();
            JToolbarHelper::cancel('submissions.manage');

            $this->formId = $this->get('formId');
            $this->headers = $this->get('headers');
            $this->staticHeaders = $this->get('staticHeaders');
            $this->formTitle = $this->get('formTitle');
            $this->previewData = $this->get('previewImportData');
            $this->countHeaders = $this->previewData ? count(reset($this->previewData)) : 0;

            $options = array(
                JHtml::_('select.option', '', JText::_('COM_RSFORM_IMPORT_IGNORE'))
            );
            foreach ($this->staticHeaders as $header)
            {
                $options[] = JHtml::_('select.option', $header);
            }
            foreach ($this->headers as $header)
            {
                $options[] = JHtml::_('select.option', $header);
            }
            $this->options = JHtml::_('select.options', $options);

            JToolbarHelper::title('RSForm! Pro <small>['.JText::sprintf('COM_RSFORM_IMPORTING', $this->formTitle).']</small>','rsform');
        }
		elseif ($layout == 'exportprocess')
		{
			$this->limit        = 500;
			$this->total        = $this->get('exportTotal');
			$this->file         = JFactory::getApplication()->input->getCmd('ExportFile');
			$this->exportType   = JFactory::getApplication()->input->getCmd('exportType');
			$this->formId	    = $this->get('FormId');

			JToolbarHelper::title('RSForm! Pro <small>['.JText::sprintf('RSFP_EXPORTING', $this->exportType, $this->get('formTitle')).']</small>','rsform');

			JToolbarHelper::custom('submissions.cancelform', 'previous', 'previous', JText::_('RSFP_BACK_TO_FORM'), false);
			JToolbarHelper::custom('submissions.back', 'database', 'database', JText::_('RSFP_SUBMISSIONS'), false);
        }
        elseif ($layout == 'importprocess')
        {
            $this->limit    = 500;
            $this->total    = $this->get('importTotal');
            $this->formId	= $this->get('FormId');

            JToolbarHelper::title('RSForm! Pro <small>['.JText::sprintf('COM_RSFORM_IMPORTING', $this->get('formTitle')).']</small>','rsform');

            JToolbarHelper::custom('submissions.cancelform', 'previous', 'previous', JText::_('RSFP_BACK_TO_FORM'), false);
            JToolbarHelper::custom('submissions.back', 'database', 'database', JText::_('RSFP_SUBMISSIONS'), false);
        }
        elseif ($layout == 'import')
        {
            $this->limit    = 500;
            $this->formId   = $this->get('FormId');

            JToolbarHelper::custom('submissions.cancelform', 'previous', 'previous', JText::_('RSFP_BACK_TO_FORM'), false);
            JToolbarHelper::custom('submissions.back', 'database', 'database', JText::_('RSFP_SUBMISSIONS'), false);
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
		    $this->user = JFactory::getUser();

            if ($this->user->authorise('forms.manage', 'com_rsform'))
            {
                JToolbarHelper::custom('submissions.cancelform', 'previous', 'previous', JText::_('RSFP_BACK_TO_FORM'), false);
                JToolbarHelper::spacer();
            }

			JToolbarHelper::custom('submissions.resend', 'mail', 'mail', JText::_('RSFP_RESEND_EMAILS'), true);
            JToolbarHelper::modal('exportModal', 'icon-archive icon white', 'RSFP_EXPORT');
            JToolbarHelper::modal('importModal', 'icon-upload icon white', 'COM_RSFORM_IMPORT_SUBMISSIONS');
            JToolbarHelper::spacer();
			JToolbarHelper::editList('submissions.edit', JText::_('JTOOLBAR_EDIT'));
			JToolbarHelper::deleteList(JText::_('RSFP_ARE_YOU_SURE_DELETE'), 'submissions.delete', JText::_('JTOOLBAR_DELETE'));
			JToolbarHelper::spacer();
			JToolbarHelper::cancel('submissions.cancel', JText::_('JTOOLBAR_CLOSE'));
			
			$forms = $this->get('forms');
			$formId = $this->get('formId');

			JToolbarHelper::title('RSForm! Pro <small>['.$this->get('formTitle').']</small>','rsform');
			
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
		
			$calendars['from'] = JHtml::_('calendar', $this->get('dateFrom'), 'dateFrom', 'dateFrom', '%Y-%m-%d %H:%M:%S', array('placeholder' => JText::_('RSFP_FROM'), 'showTime' => 'true', 'todayBtn' => 'true'));
			$calendars['to']   = JHtml::_('calendar', $this->get('dateTo'), 'dateTo', 'dateTo', '%Y-%m-%d %H:%M:%S', array('placeholder' => JText::_('RSFP_TO'), 'showTime' => 'true', 'todayBtn' => 'true'));
			$this->calendars = $calendars;
		
			$lists['Languages'] = JHtml::_('select.genericlist', $this->get('languages'), 'Language', '', 'value', 'text', $this->get('lang'));
		
			$lists['forms'] = JHtml::_('select.genericlist', $forms, 'formId', 'onchange="submissionChangeForm(this.value)"', 'value', 'text', $formId);
			$this->lists = $lists;
			
			$this->sidebar = $this->get('Sidebar');
		}
		
		parent::display($tpl);
	}
	
	public function isHeaderEnabled($header, $static=0)
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

	public function getHeaderLabel($header)
    {
        JFactory::getApplication()->triggerEvent('rsfp_bk_onGetHeaderLabel', array(&$header, $this->formId));

        return $header;
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