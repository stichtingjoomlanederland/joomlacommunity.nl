<?php
/**
 * @package RSForm! Pro
 * @copyright (C) 2007-2014 www.rsjoomla.com
 * @license GPL, http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die('Restricted access');

class RsformViewForms extends JViewLegacy
{
	public function display($tpl = null)
	{
		$document  = JFactory::getDocument();
		$document->addCustomTag('<!--[if IE 7]><link href="'.JURI::root().'administrator/components/com_rsform/assets/css/styleie.css" rel="stylesheet" type="text/css" /><![endif]-->');

		RSFormProHelper::loadCodeMirror();

		JToolbarHelper::title('RSForm! Pro','rsform');

		// adding the toolbar on 2.5
		if (!RSFormProHelper::isJ('3.0')) {
			$this->addToolbar();
		}
		$layout = $this->getLayout();
		$this->isComponent = JFactory::getApplication()->input->getCmd('tmpl') == 'component';
		$this->tooltipClass = RSFormPRoHelper::getTooltipClass();

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
			JText::script('RSFP_AUTOGENERATE_LAYOUT_WARNING_SURE');
			$submissionsIcon = RSFormProHelper::isJ('3.0') ? 'database' : 'forward';
			$previewIcon	 = RSFormProHelper::isJ('3.0') ? 'new tab' : 'preview';
			$directoryIcon	 = RSFormProHelper::isJ('3.0') ? 'folder' : 'forward';

			JToolbarHelper::apply('forms.apply');
			JToolbarHelper::save('forms.save');
			JToolbarHelper::spacer();
			JToolbarHelper::custom('forms.preview', $previewIcon, $previewIcon, JText::_('JGLOBAL_PREVIEW'), false);
			JToolbarHelper::custom('submissions.back', $submissionsIcon, $submissionsIcon, JText::_('RSFP_SUBMISSIONS'), false);
			JToolbarHelper::custom('forms.directory', $directoryIcon, $directoryIcon, JText::_('RSFP_DIRECTORY'), false);
			JToolbarHelper::custom('components.copy', 'copy', 'copy', JText::_('RSFP_COPY_TO_FORM'), false);
			JToolbarHelper::custom('components.duplicate', 'copy', 'copy', JText::_('RSFP_DUPLICATE'), false);
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

			$lists['Published'] = $this->renderHTML('select.booleanlist','Published','',$this->form->Published);
			$lists['DisableSubmitButton'] = $this->renderHTML('select.booleanlist','DisableSubmitButton','',$this->form->DisableSubmitButton);
			$lists['RemoveCaptchaLogged'] = $this->renderHTML('select.booleanlist','RemoveCaptchaLogged','',$this->form->RemoveCaptchaLogged);
			$lists['ShowFormTitle'] = $this->renderHTML('select.booleanlist','ShowFormTitle','',$this->form->ShowFormTitle);
			$lists['keepdata'] = $this->renderHTML('select.booleanlist','Keepdata','',$this->form->Keepdata);
			$lists['KeepIP'] = $this->renderHTML('select.booleanlist','KeepIP','',$this->form->KeepIP);
			$lists['confirmsubmission'] = $this->renderHTML('select.booleanlist','ConfirmSubmission','',$this->form->ConfirmSubmission);
			$lists['ShowSystemMessage'] = $this->renderHTML('select.booleanlist','ShowSystemMessage','',$this->form->ShowSystemMessage);
			$lists['ShowThankyou'] = $this->renderHTML('select.booleanlist','ShowThankyou','onclick="enableThankyou(this.value);"',$this->form->ShowThankyou);
			$lists['ScrollToThankYou'] = $this->renderHTML('select.booleanlist','ScrollToThankYou','onclick="enableThankyouPopup(this.value);"',$this->form->ScrollToThankYou);
			$lists['ThankYouMessagePopUp'] = $this->renderHTML('select.booleanlist','ThankYouMessagePopUp',((!$this->form->ShowThankyou || ($this->form->ShowThankyou && $this->form->ScrollToThankYou)) ? 'disabled="true"' : ''),$this->form->ThankYouMessagePopUp);
			$lists['ShowContinue'] = $this->renderHTML('select.booleanlist', 'ShowContinue', !$this->form->ShowThankyou ? 'disabled="true"' : '', $this->form->ShowContinue);
			$lists['UserEmailMode'] = $this->renderHTML('select.booleanlist', 'UserEmailMode', 'onclick="enableEmailMode(\'User\', this.value)"', $this->form->UserEmailMode, JText::_('HTML'), JText::_('RSFP_COMP_FIELD_TEXT'));
			$lists['UserEmailAttach'] = $this->renderHTML('select.booleanlist', 'UserEmailAttach', 'onclick="enableAttachFile(this.value)"', $this->form->UserEmailAttach);
			$lists['AdminEmailMode'] = $this->renderHTML('select.booleanlist', 'AdminEmailMode', 'onclick="enableEmailMode(\'Admin\', this.value)"', $this->form->AdminEmailMode, JText::_('HTML'), JText::_('RSFP_COMP_FIELD_TEXT'));
			$lists['MetaTitle'] = $this->renderHTML('select.booleanlist', 'MetaTitle', '', $this->form->MetaTitle);
			$lists['TextareaNewLines'] = $this->renderHTML('select.booleanlist', 'TextareaNewLines', '', $this->form->TextareaNewLines);
			$lists['AjaxValidation'] = $this->renderHTML('select.booleanlist', 'AjaxValidation', '', $this->form->AjaxValidation);
			$lists['ScrollToError'] = $this->renderHTML('select.booleanlist', 'ScrollToError', '', $this->form->ScrollToError);
			$lists['FormLayoutAutogenerate'] = $this->renderHTML('select.booleanlist', 'FormLayoutAutogenerate', 'onclick="changeFormAutoGenerateLayout('.$this->form->FormId.', this.value);"', $this->form->FormLayoutAutogenerate);

			$lists['post_enabled'] 	= $this->renderHTML('select.booleanlist', 'form_post[enabled]', '', $this->form_post->enabled);
			$lists['post_method'] 	= $this->renderHTML('select.booleanlist', 'form_post[method]', '', $this->form_post->method, JText::_('RSFP_POST_METHOD_POST'), JText::_('RSFP_POST_METHOD_GET'));
			$lists['post_silent'] 	= $this->renderHTML('select.booleanlist', 'form_post[silent]', '', $this->form_post->silent);

			$this->themes = $this->get('themes');
			$this->lang = $this->get('lang');

			// workaround for first time visit
			$session 	 = JFactory::getSession();
			$session->set('com_rsform.form.formId'.$this->form->FormId.'.lang', $this->lang);

			$this->fields = $this->get('fields');
			$this->totalFields = $this->get('totalfields');
			$this->quickfields = $this->get('quickfields');
			$this->pagination = $this->get('fieldspagination');
			$this->calculations = RSFormProHelper::getCalculations($this->form->FormId);

			$lists['Languages'] = JHTML::_('select.genericlist', $this->get('languages'), 'Language', 'onchange="submitbutton(\'changeLanguage\')"', 'value', 'text', $this->lang);
			$lists['totalFields'] = JHTML::_('select.genericlist', $this->get('languages'), 'Language', 'onchange="submitbutton(\'changeLanguage\')"', 'value', 'text', $this->lang);

			$this->mappings = $this->get('mappings');
			$this->mpagination = $this->get('mpagination');
			$this->conditions = $this->get('conditions');
			$this->formId = $this->form->FormId;
			$this->emails = $this->get('emails');

			$this->lists = $lists;

			// layouts
			$this->layouts = array(
				'classicLayouts' => array('inline', '2lines', '2colsinline', '2cols2lines'),
				'xhtmlLayouts' 	 => array('inline-xhtml', '2lines-xhtml'),
				'html5Layouts' 	 => array('responsive', 'bootstrap2', 'bootstrap3', 'uikit', 'foundation')
			);

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
			$nextIcon = RSFormProHelper::isJ('3.0') ? 'next' : 'forward';

			JToolbarHelper::custom('forms.new.steptwo', $nextIcon, $nextIcon, JText::_('JNEXT'), false);
			JToolbarHelper::cancel('forms.cancel');
		}
		elseif ($layout == 'new2')
		{
			$nextIcon = RSFormProHelper::isJ('3.0') ? 'next' : 'forward';

			JToolbarHelper::custom('forms.new.stepthree', $nextIcon, $nextIcon, JText::_('JNEXT'), false);
			JToolbarHelper::cancel('forms.cancel');

			$lists['AdminEmail'] 			= $this->renderHTML('select.booleanlist', 'AdminEmail', 'onclick="changeAdminEmail(this.value)"', 1);
			$lists['UserEmail'] 			= $this->renderHTML('select.booleanlist', 'UserEmail', '', 1);
			$lists['ScrollToThankYou']      = $this->renderHTML('select.booleanlist', 'ScrollToThankYou','onclick="showPopupThankyou(this.value)"', 1);
			$lists['ThankYouMessagePopUp']  = $this->renderHTML('select.booleanlist', 'ThankYouMessagePopUp','', 0);
			$actions = array(
				JHTML::_('select.option', 'refresh', JText::_('RSFP_SUBMISSION_REFRESH_PAGE')),
				JHTML::_('select.option', 'thankyou', JText::_('RSFP_SUBMISSION_THANKYOU')),
				JHTML::_('select.option', 'redirect', JText::_('RSFP_SUBMISSION_REDIRECT_TO'))
			);
			$lists['SubmissionAction'] = JHTML::_('select.genericlist', $actions, 'SubmissionAction', 'onclick="changeSubmissionAction(this.value)"');

			$this->adminEmail = $this->get('adminEmail');
			$this->lists = $lists;
			$this->editor = JFactory::getEditor();
		}
		elseif ($layout == 'new3')
		{
			$nextIcon = RSFormProHelper::isJ('3.0') ? 'next' : 'forward';

			JToolbarHelper::custom('forms.new.stepfinal', $nextIcon, $nextIcon, JText::_('RSFP_FINISH'), false);
			JToolbarHelper::cancel('forms.cancel');

			$lists['predefinedForms'] = JHTML::_('select.genericlist', $this->get('predefinedforms'), 'predefinedForm', '');
			$this->lists = $lists;
		}
		elseif ($layout == 'component_copy')
		{
			JToolbarHelper::custom('components.copy.process', 'copy', 'copy', JText::_('RSFP_COPY'), false);
			JToolbarHelper::cancel('components.copy.cancel');

			$formlist = $this->get('formlist');
			$lists['forms'] = JHTML::_('select.genericlist', $formlist, 'toFormId', '', 'value', 'text');

			$this->formId = JFactory::getApplication()->input->getInt('formId');
			$this->cids = JRequest::getVar('cid', array());
			$this->lists = $lists;
		}
		elseif ($layout == 'richtext')
		{
			$this->editor = JFactory::getEditor();
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
			$db = JFactory::getDbo();
			$lang = JFactory::getLanguage();
			$lang->load('com_rsform', JPATH_SITE);
			$formId = JFactory::getApplication()->input->getInt('formId');

			$db->setQuery("SELECT FormTitle FROM #__rsform_forms WHERE FormId = ".$formId." ");
			JToolbarHelper::title($db->loadResult(),'rsform');

			$this->formId = $formId;
		}
		elseif ($layout == 'emails')
		{
			$this->row = $this->get('email');
			$this->lang = $this->get('emaillang');
			$lists['mode'] = $this->renderHTML('select.booleanlist', 'mode', 'onclick="showMode(this.value);"', $this->row->mode, JText::_('HTML'), JText::_('Text'));
			$lists['Languages'] = JHTML::_('select.genericlist', $this->get('languages'), 'ELanguage', 'onchange="submitbutton(\'changeEmailLanguage\')"', 'value', 'text', $this->lang);
			$this->lists = $lists;
			$this->editor = JFactory::getEditor();
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

			JToolbarHelper::addNew('forms.add', JText::_('JTOOLBAR_NEW'));
			JToolbarHelper::spacer();
			JToolbarHelper::custom('forms.copy', 'copy.png', 'copy_f2.png', JText::_('RSFP_DUPLICATE'), false);
			JToolbarHelper::spacer();
			JToolbarHelper::deleteList(JText::_('RSFP_ARE_YOU_SURE_DELETE'), 'forms.delete', JText::_('JTOOLBAR_DELETE'));
			JToolbarHelper::spacer();
			JToolbarHelper::publishList('forms.publish', JText::_('JTOOLBAR_PUBLISH'));
			JToolbarHelper::unpublishList('forms.unpublish', JText::_('JTOOLBAR_UNPUBLISH'));

			$this->forms 	  = $this->get('forms');
			$this->pagination = $this->get('Pagination');
			$this->filterbar  = $this->get('FilterBar');

			$this->sortColumn = $this->get('sortColumn');
			$this->sortOrder  = $this->get('sortOrder');
		}

		parent::display($tpl);
	}

	protected function triggerEvent($event) {
		$app = JFactory::getApplication();
		$app->triggerEvent($event);
	}

	protected function renderHTML() {
		$args = func_get_args();
		if (RSFormProHelper::isJ('3.0')) {
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
		} else {
			if ($args[0] == 'select.booleanlist') {
				$name	 	= $args[1];
				$additional = isset($args[2]) ? (string) $args[2] : '';
				$value		= $args[3];
				$yes 	 	= isset($args[4]) ? $this->escape($args[4]) : 'JYES';
				$no 	 	= isset($args[5]) ? $this->escape($args[5]) : 'JNO';

				return JHtml::_($args[0], $name, $additional, $value, $yes, $no);
			}
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

	protected function escapeJS($string) {
		// Decode HTML entities
		$string = html_entity_decode($string, ENT_QUOTES, 'utf-8');

		// Recode them
		$string = htmlentities($string, ENT_COMPAT, 'utf-8');

		// Add slashes
		$string = addcslashes($string, "\\'");

		// Remove newlines
		$string = str_replace(array("\r\n", "\r", "\n"), ' ', $string);

		return $string;
	}
}