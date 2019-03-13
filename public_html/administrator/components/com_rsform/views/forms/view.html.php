<?php
/**
 * @package RSForm! Pro
 * @copyright (C) 2007-2014 www.rsjoomla.com
 * @license GPL, http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die('Restricted access');

class RsformViewForms extends JViewLegacy
{
    protected $layouts = array();

	public function display($tpl = null)
	{
        if (!JFactory::getUser()->authorise('forms.manage', 'com_rsform'))
        {
            throw new Exception(JText::_('COM_RSFORM_NOT_AUTHORISED_TO_USE_THIS_SECTION'));
        }

		JToolbarHelper::title('RSForm! Pro','rsform');

		$layout = $this->getLayout();
		$this->isComponent = JFactory::getApplication()->input->getCmd('tmpl') == 'component';
		$this->tooltipClass = RSFormProHelper::getTooltipClass();

		$displayPlaceholders = array(
			'{global:username}',
			'{global:userid}',
			'{global:useremail}',
			'{global:fullname}',
			'{global:mailfrom}',
			'{global:fromname}',
			'{global:submissionid}',
			'{global:sitename}',
			'{global:siteurl}',
			'{global:userip}',
			'{global:date_added}'
		);

		if ($layout == 'edit')
		{
			$this->user = JFactory::getUser();

			JToolbarHelper::apply('forms.apply');
			JToolbarHelper::save('forms.save');
			JToolbarHelper::spacer();
			JToolbarHelper::custom('forms.preview', 'new tab', 'new tab', JText::_('JGLOBAL_PREVIEW'), false);
			if ($this->user->authorise('submissions.manage', 'com_rsform'))
			{
                JToolbarHelper::custom('submissions.back', 'database', 'database', JText::_('RSFP_SUBMISSIONS'), false);
            }
            if ($this->user->authorise('directory.manage', 'com_rsform'))
            {
			    JToolbarHelper::custom('forms.directory', 'folder', 'folder', JText::_('RSFP_DIRECTORY'), false);
            }
			JToolbarHelper::custom('components.copy', 'copy', 'copy', JText::_('RSFP_COPY_TO_FORM'), true);
			JToolbarHelper::custom('components.duplicate', 'copy', 'copy', JText::_('RSFP_DUPLICATE'), true);
			JToolbarHelper::deleteList(JText::_('RSFP_ARE_YOU_SURE_DELETE'), 'components.remove', JText::_('JTOOLBAR_DELETE'));
			JToolbarHelper::publishList('components.publish', JText::_('JTOOLBAR_PUBLISH'));
			JToolbarHelper::unpublishList('components.unpublish', JText::_('JTOOLBAR_UNPUBLISH'));
			JToolbarHelper::spacer();
			JToolbarHelper::cancel('forms.cancel');

			$this->tabposition = JFactory::getApplication()->input->getInt('tabposition', 0);
			$this->tab 		   = JFactory::getApplication()->input->getInt('tab', 0);
			$this->form 	   = $this->get('form');
			$this->form_post   = $this->get('formPost');

			$this->hasSubmitButton = $this->get('hasSubmitButton');

			JToolbarHelper::title('RSForm! Pro <small>['.JText::sprintf('RSFP_EDITING_FORM', $this->form->FormTitle).']</small>','rsform');

            $lists['Published'] = $this->renderHTML('select.booleanlist', 'Published', '', $this->form->Published);
            $lists['DisableSubmitButton'] = $this->renderHTML('select.booleanlist', 'DisableSubmitButton', '', $this->form->DisableSubmitButton);
            $lists['RemoveCaptchaLogged'] = $this->renderHTML('select.booleanlist', 'RemoveCaptchaLogged', '', $this->form->RemoveCaptchaLogged);
            $lists['ShowFormTitle'] = $this->renderHTML('select.booleanlist', 'ShowFormTitle', '', $this->form->ShowFormTitle);
            $lists['keepdata'] = $this->renderHTML('select.booleanlist', 'Keepdata', '', $this->form->Keepdata);
            $lists['KeepIP'] = $this->renderHTML('select.booleanlist', 'KeepIP', '', $this->form->KeepIP);
            $lists['confirmsubmission'] = $this->renderHTML('select.booleanlist', 'ConfirmSubmission', '', $this->form->ConfirmSubmission);
            $lists['ShowSystemMessage'] = $this->renderHTML('select.booleanlist', 'ShowSystemMessage', '', $this->form->ShowSystemMessage);
            $lists['ShowThankyou'] = $this->renderHTML('select.booleanlist', 'ShowThankyou', 'onclick="enableThankyou(this.value);"', $this->form->ShowThankyou);
            $lists['ScrollToThankYou'] = $this->renderHTML('select.booleanlist', 'ScrollToThankYou', 'onclick="enableThankyouPopup(this.value);"', $this->form->ScrollToThankYou);
            $lists['ThankYouMessagePopUp'] = $this->renderHTML('select.booleanlist', 'ThankYouMessagePopUp', '', $this->form->ThankYouMessagePopUp);
            $lists['ShowContinue'] = $this->renderHTML('select.booleanlist', 'ShowContinue', '', $this->form->ShowContinue);
            $lists['UserEmailMode'] = $this->renderHTML('select.booleanlist', 'UserEmailMode', 'onclick="enableEmailMode(\'User\', this.value)"', $this->form->UserEmailMode, JText::_('HTML'), JText::_('RSFP_COMP_FIELD_TEXT'));
            $lists['UserEmailAttach'] = $this->renderHTML('select.booleanlist', 'UserEmailAttach', 'onclick="enableAttachFile(this.value)"', $this->form->UserEmailAttach);
            $lists['AdminEmailMode'] = $this->renderHTML('select.booleanlist', 'AdminEmailMode', 'onclick="enableEmailMode(\'Admin\', this.value)"', $this->form->AdminEmailMode, JText::_('HTML'), JText::_('RSFP_COMP_FIELD_TEXT'));
            $lists['DeletionEmailMode'] = $this->renderHTML('select.booleanlist', 'DeletionEmailMode', 'onclick="enableEmailMode(\'User\', this.value)"', $this->form->DeletionEmailMode, JText::_('HTML'), JText::_('RSFP_COMP_FIELD_TEXT'));
            $lists['MetaTitle'] = $this->renderHTML('select.booleanlist', 'MetaTitle', '', $this->form->MetaTitle);
            $lists['TextareaNewLines'] = $this->renderHTML('select.booleanlist', 'TextareaNewLines', '', $this->form->TextareaNewLines);
            $lists['AjaxValidation'] = $this->renderHTML('select.booleanlist', 'AjaxValidation', '', $this->form->AjaxValidation);
            $lists['ScrollToError'] = $this->renderHTML('select.booleanlist', 'ScrollToError', '', $this->form->ScrollToError);
            $lists['FormLayoutAutogenerate'] = $this->renderHTML('select.booleanlist', 'FormLayoutAutogenerate', 'onclick="changeFormAutoGenerateLayout(' . $this->form->FormId . ', this.value);"', $this->form->FormLayoutAutogenerate);
            $lists['FormLayoutFlow'] = JHtml::_('select.genericlist', array(
               JHtml::_('select.option', 0, JText::_('RSFP_FORM_FLOW_HORIZONTAL')),
               JHtml::_('select.option', 1, JText::_('RSFP_FORM_FLOW_VERTICAL'))
            ), 'FormLayoutFlow', 'onchange="changeFormLayoutFlow();"', 'value', 'text', $this->form->FormLayoutFlow);

			$lists['post_enabled'] 	= $this->renderHTML('select.booleanlist', 'form_post[enabled]', '', $this->form_post->enabled);
			$lists['post_method'] 	= $this->renderHTML('select.booleanlist', 'form_post[method]', '', $this->form_post->method, JText::_('RSFP_POST_METHOD_POST'), JText::_('RSFP_POST_METHOD_GET'));
			$lists['post_silent'] 	= $this->renderHTML('select.booleanlist', 'form_post[silent]', '', $this->form_post->silent);

			$this->lang = $this->get('lang');

			// workaround for first time visit
			$session 	 = JFactory::getSession();
			$session->set('com_rsform.form.formId'.$this->form->FormId.'.lang', $this->lang);

			$this->fields = $this->get('fields');
			$this->totalFields = $this->get('totalfields');
			$this->quickfields = $this->get('quickfields');
			$this->pagination = $this->get('fieldspagination');
			$this->calculations = RSFormProHelper::getCalculations($this->form->FormId);

			$lists['Languages'] = JHtml::_('select.genericlist', $this->get('languages'), 'Language', 'onchange="Joomla.submitbutton(\'changeLanguage\')"', 'value', 'text', $this->lang);
			$lists['totalFields'] = JHtml::_('select.genericlist', $this->get('languages'), 'Language', 'onchange="Joomla.submitbutton(\'changeLanguage\')"', 'value', 'text', $this->lang);

			$this->mappings = $this->get('mappings');
			$this->mpagination = $this->get('mpagination');
			$this->conditions = $this->get('conditions');
			$this->formId = $this->form->FormId;
			$this->emails = $this->get('emails');

			$this->lists = $lists;

			// layouts
			$this->layouts = RSFormProHelper::getFormLayouts($this->formId);

			$this->hasLegacyLayout = in_array($this->form->FormLayoutName, array_merge($this->layouts['classicLayouts'], $this->layouts['xhtmlLayouts']));

			foreach($this->quickfields as $fields){
				$displayPlaceholders = array_merge($displayPlaceholders, $fields['display']);
			};

			RSFormProAssets::addScriptDeclaration('
				var $displayPlaceholders = "' . implode(',', $displayPlaceholders) . '";
				RSFormPro.Placeholders = $displayPlaceholders.split(\',\');
			');
		}
		elseif ($layout == 'new')
		{
			JToolbarHelper::custom('forms.new.steptwo', 'next', 'next', JText::_('JNEXT'), false);
			JToolbarHelper::cancel('forms.cancel');
		}
		elseif ($layout == 'new2')
		{
			JToolbarHelper::custom('forms.new.stepthree', 'next', 'next', JText::_('JNEXT'), false);
			JToolbarHelper::cancel('forms.cancel');

			$lists['AdminEmail'] 			= $this->renderHTML('select.booleanlist', 'AdminEmail', 'onclick="changeAdminEmail(this.value)"', 1);
			$lists['UserEmail'] 			= $this->renderHTML('select.booleanlist', 'UserEmail', '', 1);
			$lists['ScrollToThankYou']      = $this->renderHTML('select.booleanlist', 'ScrollToThankYou','onclick="showPopupThankyou(this.value)"', 1);
			$lists['ThankYouMessagePopUp']  = $this->renderHTML('select.booleanlist', 'ThankYouMessagePopUp','', 0);
			$actions = array(
				JHtml::_('select.option', 'refresh', JText::_('RSFP_SUBMISSION_REFRESH_PAGE')),
				JHtml::_('select.option', 'thankyou', JText::_('RSFP_SUBMISSION_THANKYOU')),
				JHtml::_('select.option', 'redirect', JText::_('RSFP_SUBMISSION_REDIRECT_TO'))
			);
			$lists['SubmissionAction'] = JHtml::_('select.genericlist', $actions, 'SubmissionAction', 'onclick="changeSubmissionAction(this.value)"');

			$this->adminEmail = $this->get('adminEmail');
			$this->lists = $lists;
			$this->editor = RSFormProHelper::getEditor();

            $this->layouts = RSFormProHelper::getFormLayouts();
		}
		elseif ($layout == 'new3')
		{
			JToolbarHelper::custom('forms.new.stepfinal', 'next', 'next', JText::_('RSFP_FINISH'), false);
			JToolbarHelper::cancel('forms.cancel');

			$lists['predefinedForms'] = JHtml::_('select.genericlist', $this->get('predefinedforms'), 'predefinedForm', '');
			$this->lists = $lists;
		}
		elseif ($layout == 'component_copy')
		{
			JToolbarHelper::custom('components.copy.process', 'copy', 'copy', JText::_('RSFP_DO_COPY'), false);
			JToolbarHelper::cancel('components.copy.cancel');

			$formlist = $this->get('formlist');
			$lists['forms'] = JHtml::_('select.genericlist', $formlist, 'toFormId', '', 'value', 'text');

			$this->formId = JFactory::getApplication()->input->getInt('formId');
			$this->cids = JFactory::getApplication()->input->get('cid', array(), 'array');
			$this->lists = $lists;
		}
		elseif ($layout == 'richtext')
		{
			$this->editor = RSFormProHelper::getEditor();
			$this->noEditor = JFactory::getApplication()->input->getInt('noEditor');
			$this->formId = JFactory::getApplication()->input->getInt('formId');
			$this->editorName = JFactory::getApplication()->input->getCmd('opener');
			$this->editorText = $this->get('editorText');
			$this->lang = $this->get('lang');
		}
		elseif ($layout == 'edit_mappings')
		{
			$formId = JFactory::getApplication()->input->getInt('formId');
			$this->mappings = $this->get('mappings');
			$this->mpagination = $this->get('mpagination');
			$this->formId = $formId;
		}
		elseif ($layout == 'edit_conditions')
		{
			$formId = JFactory::getApplication()->input->getInt('formId');
			$this->conditions = $this->get('conditions');
			$this->formId = $formId;
		}
		elseif ($layout == 'edit_emails')
		{
			$this->emails = $this->get('emails');
			$this->lang = $this->get('emaillang');
		}
		elseif ($layout == 'show')
		{
            JFactory::getLanguage()->load('com_rsform', JPATH_SITE);
            $formId = JFactory::getApplication()->input->getInt('formId');
			$this->formId = $formId;

			$this->setToolbarTitle();
		}
		elseif ($layout == 'emails')
		{
			$this->row = $this->get('email');
			$this->lang = $this->get('emaillang');
			$lists['mode'] = $this->renderHTML('select.booleanlist', 'mode', 'onclick="showMode(this.value);"', $this->row->mode, JText::_('HTML'), JText::_('Text'));
			$lists['Languages'] = JHtml::_('select.genericlist', $this->get('languages'), 'ELanguage', 'onchange="Joomla.submitbutton(\'changeEmailLanguage\')"', 'value', 'text', $this->lang);
			$this->lists = $lists;
			$this->editor = RSFormProHelper::getEditor();
			$this->quickfields = $this->get('quickfields');

			foreach($this->quickfields as $fields){
				$displayPlaceholders = array_merge($displayPlaceholders, $fields['display']);
			};

			RSFormProAssets::addScriptDeclaration('
				var $displayPlaceholders = "' . implode(',', $displayPlaceholders) . '";
				RSFormPro.Placeholders = $displayPlaceholders.split(\',\');
			');

		}
		else
		{
			$this->addToolbar();
			$this->sidebar = $this->get('Sidebar');

            JToolbarHelper::addNew('forms.newstepfinal', JText::_('JTOOLBAR_NEW'));
            JToolbarHelper::custom('forms.add', 'play', 'play', JText::_('COM_RSFORM_NEW_FORM_WIZARD'), false);
			JToolbarHelper::spacer();
			JToolbarHelper::custom('forms.copy', 'copy', 'copy', JText::_('RSFP_DUPLICATE'), true);
			JToolbarHelper::spacer();
			JToolbarHelper::deleteList(JText::_('RSFP_ARE_YOU_SURE_DELETE'), 'forms.delete', JText::_('JTOOLBAR_DELETE'));
			JToolbarHelper::spacer();
			JToolbarHelper::publishList('forms.publish', JText::_('JTOOLBAR_PUBLISH'));
			JToolbarHelper::unpublishList('forms.unpublish', JText::_('JTOOLBAR_UNPUBLISH'));

			$this->user       = JFactory::getUser();
			$this->forms 	  = $this->get('forms');
			$this->pagination = $this->get('Pagination');
			$this->filterbar  = $this->get('FilterBar');

			$this->sortColumn = $this->get('sortColumn');
			$this->sortOrder  = $this->get('sortOrder');
		}

		parent::display($tpl);
	}

	protected function triggerEvent($event, $params = array()) {
        JFactory::getApplication()->triggerEvent($event, $params);
	}

	protected function renderHTML() {
		$args = func_get_args();

		if ($args[0] == 'select.booleanlist') {
			// 0 - type
			// 1 - name
			// 2 - additional
			// 3 - value
			// 4 - yes
			// 5 - no

			// get the radio element
			$radio = JFormHelper::loadFieldType('radio');

			// setup the properties
			$name	 	= $this->escape($args[1]);
			$additional = isset($args[2]) ? (string) $args[2] : '';
			$value		= $args[3];
			$yes 	 	= isset($args[4]) ? $this->escape($args[4]) : 'JYES';
			$no 	 	= isset($args[5]) ? $this->escape($args[5]) : 'JNO';

			// prepare the xml
			$element = new SimpleXMLElement('<field name="'.$name.'" type="radio" class="btn-group"><option '.$additional.' value="0">'.$no.'</option><option '.$additional.' value="1">'.$yes.'</option></field>');

			// run
			$radio->setup($element, $value);

			return $radio->input;
		}
	}

	protected function addToolbar() {
		static $called;

		// this is a workaround so if called multiple times it will not duplicate the buttons
		if (!$called) {
			// set title
			JToolbarHelper::title('RSForm! Pro', 'rsform');

			require_once JPATH_COMPONENT.'/helpers/toolbar.php';
			RSFormProToolbarHelper::addToolbar('forms');

			$called = true;
		}
	}

	protected function setToolbarTitle()
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->select($db->qn('FormTitle'))
            ->from($db->qn('#__rsform_forms'))
            ->where($db->qn('FormId') . ' = ' . $db->q($this->formId));

        $title = $db->setQuery($query)->loadResult();

        $lang = RSFormProHelper::getCurrentLanguage($this->formId);
        if ($translations = RSFormProHelper::getTranslations('forms', $this->formId, $lang))
        {
            if (isset($translations['FormTitle']))
            {
                $title = $translations['FormTitle'];
            }
        }

        JToolbarHelper::title($title,'rsform');
    }
	
	protected function buildGrid()
	{
		$rows 		= array();
		$hidden		= array();
		$row_index 	= 0;
		if (strlen($this->form->GridLayout))
		{
			$used = array();
			$data = json_decode($this->form->GridLayout, true);
			
			// If decoding is successful, we should have $rows and $hidden
			if (is_array($data) && isset($data[0], $data[1]))
			{
				$rows 	= $data[0];
				$hidden = $data[1];
			}
			
			// Actual layout (rows and columns)
			if ($rows)
			{
				foreach ($rows as $row_index => &$row)
				{
					foreach ($row['columns'] as $column_index => $fields)
					{
						foreach ($fields as $position => $id)
						{
							if (isset($this->fields[$id]))
							{
								// Pages have a special property
								if ($this->fields[$id]->type_id == RSFORM_FIELD_PAGEBREAK)
								{
									$row['has_pagebreak'] = true;
								}
								$row['columns'][$column_index][$position] = $this->fields[$id];
								
								$used[] = $id;
							}
							else
							{
								// Field doesn't exist, remove it from grid
								unset($row['columns'][$column_index][$position]);
							}
						}
					}
				}
				unset($row);
			}
			
			// This array just holds hidden fields so we can sort them separately
			if ($hidden)
			{
				foreach ($hidden as $hidden_index => $id)
				{
					if (isset($this->fields[$id]))
					{
						$hidden[$hidden_index] = $this->fields[$id];
						
						$used[] = $id;
					}
					else
					{
						// Field doesn't exist, remove it from grid
						unset($hidden[$hidden_index]);
					}
				}
			}
			
			// Let's see if we've added new fields in the meantime
			$diff = array();
			if ($array_diff = array_diff(array_keys($this->fields), $used))
			{
				foreach ($array_diff as $id)
				{
					$diff[] = $this->fields[$id];
				}

				// Must not be a page container
				$row = end($rows);
				if (!empty($row['has_pagebreak']))
				{
                    $row_index++;
                }
			}
		}
		else
		{
			$diff = $this->fields;
		}

		$hiddenComponents = array(
			RSFORM_FIELD_HIDDEN,
			RSFORM_FIELD_TICKET
		);

		JFactory::getApplication()->triggerEvent('rsfp_onDefineHiddenComponents', array(&$hiddenComponents));

		// Let's add fields to rows, keeping pages on a separate row
		foreach ($diff as $field)
		{
			// These are hidden fields and should be sorted separately in the $hidden array
			if (in_array($field->type_id, $hiddenComponents) || $field->type_name == 'hidden')
			{
				$hidden[] = $field;
				continue;
			}
			
			if (!isset($rows[$row_index]))
			{
				$rows[$row_index] = array(
					'columns' => array(array()),
					'sizes'   => array(12)
				);
			}
			
			// Pages are the only item on a row, they can't be resized
			if ($field->type_id == RSFORM_FIELD_PAGEBREAK)
			{
				if (!count($rows[$row_index]['columns'][0]))
				{
                    $rows[$row_index]['has_pagebreak'] = true;
					$rows[$row_index]['columns'][0][] = $field;
					$row_index++;
				}
				else
				{
					// Add new row with just this page
					$rows[++$row_index] = array(
						'columns'       => array(array($field)),
						'sizes'         => array(12),
                        'has_pagebreak' => true
					);
					
					$row_index++;
				}
			}
			else
			{
				$rows[$row_index]['columns'][0][] = $field;
			}
		}
		
		return array($rows, $hidden);
	}
}