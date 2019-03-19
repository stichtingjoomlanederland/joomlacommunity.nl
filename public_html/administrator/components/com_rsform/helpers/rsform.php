<?php
/**
 * @package RSForm! Pro
 * @copyright (C) 2007-2014 www.rsjoomla.com
 * @license GPL, http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die('Restricted access');

require_once dirname(__FILE__).'/constants.php';
require_once dirname(__FILE__).'/config.php';
require_once dirname(__FILE__).'/version.php';
require_once dirname(__FILE__).'/assets.php';

JTable::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_rsform/tables');

// Let's run some workarounds

// Disable caching for the current view (eg. com_content with Content Plugin)
$cache = JFactory::getCache(JFactory::getApplication()->input->getCmd('option'), 'view');
$cache->setCaching(false);

// Disable caching for the current component
$cache = JFactory::getCache(JFactory::getApplication()->input->getCmd('option'));
$cache->setCaching(false);

// Disable caching for com_rsform
$cache = JFactory::getCache('com_rsform');
$cache->setCaching(false);

// Disable System - Page Cache caching
$cache = JFactory::getCache('page');
$cache->setCaching(false);

// Disable module caching
$cache = JFactory::getCache('mod_rsform');
$cache->setCaching(false);

$lang = JFactory::getLanguage();
$lang->load('com_rsform', JPATH_ADMINISTRATOR, 'en-GB', true);
$lang->load('com_rsform', JPATH_ADMINISTRATOR, $lang->getDefault(), true);
$lang->load('com_rsform', JPATH_ADMINISTRATOR, null, true);

class RSFormProHelper
{
	public static $captchaFields = array(
		RSFORM_FIELD_CAPTCHA,
		24, 	// ReCAPTCHA v1.0
		2424, 	// ReCAPTCHA v2.0
		2525 	// Joomla! Captcha plugin
	);
	
	// just for legacy reasons
	public static function isJ16() { return true; }

	public static function isJ($version) {
		static $cache = array();
		if (!isset($cache[$version])) {
			$jversion = new JVersion();
			$cache[$version] = $jversion->isCompatible($version);
		}

		return $cache[$version];
	}

	public static function getDate($date)
	{
		static $mask;
		if (!$mask) {
			$mask = RSFormProHelper::getConfig('global.date_mask');
			if (!$mask) {
				$mask = 'Y-m-d H:i:s';
			}
		}
		return JHtml::_('date', $date, $mask);
	}

	public static function getTooltipText($title, $content='') {
		return JHtml::tooltipText($title, $content, 0, 0);
	}

	public static function getTooltipClass() {
		static $class = false;
		if (!$class) {
			JHtml::_('bootstrap.tooltip');
			$class = 'hasTooltip';
		}

		return $class;
	}

	public static function showEditor($name, $html, $options = array())
	{
		if (!isset($options['syntax']))
		{
			$options['syntax'] = 'html';
		}
		if (!isset($options['readonly']))
		{
			$options['readonly'] = false;
		}
		if (!isset($options['id']))
		{
			$options['id'] = null;
		}
		
		$use_editor = RSFormProHelper::getConfig('global.codemirror');
		$editor = 'codemirror';

		if ($use_editor)
		{
			$plugin = JPluginHelper::getPlugin('editors', $editor);
			if (empty($plugin))
			{
				$use_editor = false;
			}
			else
			{
				if (!is_string($plugin->params) && is_callable(array($plugin->params, 'toString')))
				{
					$plugin->params = $plugin->params->toString();
				}
			}
		}
		
		if ($use_editor)
		{
            $jversion = new JVersion;
            if ($jversion->isCompatible('3.8.0'))
			{
				$instance = new \Joomla\CMS\Editor\Editor($editor);
			}
			else
			{
				$instance = new JEditor($editor);
			}
			
			$html = $instance->display($name, static::htmlEscape($html), '100%', 300, 75, 20, $buttons = false, $options['id'], $asset = null, $author = null, array('syntax' => $options['syntax'], 'readonly' => $options['readonly']));
			
			if ($options['syntax'] == 'php')
			{
				$name = preg_replace('/jform\[(.*?)\]/', '$1', $name);
				JFactory::getDocument()->addScriptDeclaration("jQuery(function(){Joomla.editors.instances['$name'].setOption('mode', 'text/x-php');});");
			}
		}
		else
		{
			if (empty($options['id']))
			{
				$options['id'] = $name;
			}
			$readonly = '';
			if (!empty($options['readonly']))
			{
				$readonly = 'readonly';
			}
			
			$html = '<textarea class="' . $options['classes'] . '" ' . $readonly . ' rows="20" cols="75" name="' . static::htmlEscape($name) . '" id="' . static::htmlEscape($options['id']) . '">' . static::htmlEscape($html) . '</textarea>';
		}
		
		return $html;
	}

	public static function getComponentId($name, $formId=0)
	{
		static $cache = array();

		if (empty($formId))
		{
			$formId = JFactory::getApplication()->input->getInt('formId');
			if (empty($formId))
			{
				$post   = JFactory::getApplication()->input->get('form', array(),'array');
				$formId = isset($post['formId']) ? $post['formId'] : 0;
			}
		}

		if (!isset($cache[$formId][$name]))
			$cache[$formId][$name] = RSFormProHelper::componentNameExists($name, $formId, 0, 'ComponentId');

		return $cache[$formId][$name];
	}

	public static function getComponentTypeId($name, $formId=0)
	{
		static $cache = array();

		if (empty($formId))
		{
			$formId = JFactory::getApplication()->input->getInt('formId');
			if (empty($formId))
			{
				$post   = JFactory::getApplication()->input->get('form', array(),'array');
				$formId = isset($post['formId']) ? $post['formId'] : 0;
			}
		}

		if (!isset($cache[$formId][$name])) {
			$cache[$formId][$name] = RSFormProHelper::componentNameExists($name, $formId, 0, 'ComponentTypeId');
		}

		return $cache[$formId][$name];
	}

	public static function createList($results, $value='value', $text='text')
	{
		$list = array();
		if (is_array($results))
			foreach ($results as $result)
				if (is_object($result))
					$list[] = $result->{$value}.'|'.$result->{$text};
				elseif (is_array($result))
					$list[] = $result[$value].'|'.$result[$text];

		return implode("\n", $list);
	}

	public static function displayForm($formId, $is_module=false)
	{
		$mainframe = JFactory::getApplication();
		$form = RSFormProHelper::getForm($formId);

		if (empty($form) || !$form->Published)
		{
			if ($is_module)
			{
				$mainframe->enqueueMessage(JText::sprintf('RSFP_FORM_DOES_NOT_EXIST', $formId), 'warning');
				return false;
			}
			else
			{
				return JError::raiseError(404, JText::sprintf('RSFP_FORM_DOES_NOT_EXIST', $formId));
			}
		}

		// Check form access level
		if (!$is_module && $form->Access != '')
		{
			$canView = false;

			// Forms shown in the backend are accessible
			if ($mainframe->isClient('administrator'))
            {
                $canView = true;
            }
            else
            {
                // If we have a menu item, inherit access from that.
                if ($active = $mainframe->getMenu()->getActive())
                {
                    if ($query = $active->query)
                    {
                        if (isset($query['option']) && isset($query['view']) && isset($query['formId']))
                        {
                            if ($query['option'] == 'com_rsform' && $query['view'] == 'rsform' && $query['formId'] == $formId)
                            {
                                $canView = true;
                            }
                        }
                    }
                }
            }

			if (!$canView) {
				$user = JFactory::getUser();
				if (!in_array($form->Access,$user->getAuthorisedViewLevels())) {
					// Error, the form cannot be accessed
					$mainframe->enqueueMessage(JText::sprintf('RSFP_FORM_CANNOT_BE_ACCESSED', $formId), 'warning');
					$mainframe->redirect(JUri::root());
					return false;
				}
			}
		}

		$lang 		  = RSFormProHelper::getCurrentLanguage($formId);
		$translations = RSFormProHelper::getTranslations('forms', $formId, $lang);
		if ($translations)
			foreach ($translations as $field => $value)
			{
				if (isset($form->$field))
					$form->$field = $value;
			}

		$doc = JFactory::getDocument();
		if (!$is_module)
		{
			if ($form->MetaDesc)
				$doc->setMetaData('description', $form->MetaDesc);
			if ($form->MetaKeywords)
				$doc->setMetaData('keywords', $form->MetaKeywords);
			if ($form->MetaTitle)
				$doc->setTitle($form->FormTitle);
		}

		$session = JFactory::getSession();
		$formparams = $session->get('com_rsform.formparams.formId'.$formId);

		// Form has been processed ?
		if ($formparams && !empty($formparams->formProcessed))
		{
			// Must show Thank You Message
			if ($form->ShowThankyou)
			{
				return RSFormProHelper::showThankYouMessage($formId);
			}

			// Clear
			$session->clear('com_rsform.formparams.formId'.$formId);

			// Must show small message
			if ($formparams->showSystemMessage) {
				$mainframe->enqueueMessage(JText::_('RSFP_THANKYOU_SMALL'));
			}

			if ($form->ScrollToThankYou)
			{
				// scroll the window to the Thank You Message
				$scrolltoScript = 'RSFormProUtils.addEvent(window, \'load\',function(){ RSFormPro.scrollToElement(document.getElementById(\'system-message-container\')); });';
				RSFormProAssets::addScriptDeclaration($scrolltoScript);
			}
		}

		if ($form->DisableSubmitButton) {
			RSFormProAssets::addScriptDeclaration('RSFormProUtils.addEvent(window, \'load\',function(){ RSFormPro.setDisabledSubmit(\''.$formId.'\', '.($form->AjaxValidation ? 'true' : 'false').');  });');
		}
		
		// Must process form
		$post = $mainframe->input->post->get('form', array(), 'array');
		if (isset($post['formId']) && $post['formId'] == $formId)
		{
			$invalid = RSFormProHelper::processForm($formId);
			// Did not pass validation - show the form
			if ($invalid)
			{
				if ($form->ScrollToError){
					RSFormProAssets::addScriptDeclaration('RSFormProUtils.addEvent(window, \'load\',function(){ RSFormPro.gotoErrorElement('.$formId.');  });');
				}
				// We call this again because the array might have been modified by processForm()
                $post = $mainframe->input->post->get('form', array(), 'array');
				return RSFormProHelper::showForm($formId, $post, $invalid);
			}
		}

		// Default - show the form
		$get = $mainframe->input->get->get('form', array(), 'array');
		return RSFormProHelper::showForm($formId, $get);
	}

	public static function getEditor()
    {
        static $editor;

        // Get the editor configuration setting
        if (is_null($editor))
        {
            $editor = JFactory::getConfig()->get('editor');
        }

        $jversion = new JVersion;

        if ($jversion->isCompatible('3.8.0'))
        {
            return \Joomla\CMS\Editor\Editor::getInstance($editor);
        }
        else
        {
            return JEditor::getInstance($editor);
        }
    }

	public static function WYSIWYG($name, $content, $hiddenField, $width, $height, $col, $row)
	{
		$editor = RSFormProHelper::getEditor();
		$params = array('relative_urls' => '0', 'cleanup_save' => '0', 'cleanup_startup' => '0', 'cleanup_entities' => '0');

		$id = trim(substr($name, 4), '][');
		$content = $editor->display($name, $content , $width, $height, $col, $row, true, $id, null, null, $params);

		return $content;
	}

	public static function getOtherCalendars($type = RSFORM_FIELD_CALENDAR) {
		$list 	= array();

		$formId 	 = JFactory::getApplication()->input->getInt('formId');
		$componentId = JFactory::getApplication()->input->getInt('componentId');

		$list[] = array(
			'value' => '',
			'text' => 'NO_DATE_MODIFIER'
		);

		if ($calendars = self::componentExists($formId, $type)) {
			// remove our current calendar from the list
			if ($componentId) {
				$pos = array_search($componentId, $calendars);
				if ($pos !== false) {
					unset($calendars[$pos]);
				}
			}
			// any calendars left?
			if ($calendars) {
				$all_data = self::getComponentProperties($calendars);
				foreach ($calendars as $calendar) {
					$data =& $all_data[$calendar];
					$list[] = array(
						'value' => 'min '.$calendar,
						'text' => JText::sprintf('RSFP_CALENDAR_SETS_MINDATE', $data['NAME'])
					);
					$list[] = array(
						'value' => 'max '.$calendar,
						'text' => JText::sprintf('RSFP_CALENDAR_SETS_MAXDATE', $data['NAME'])
					);
				}
			}
		}

		return self::createList($list);
	}

	public static function getValidationClass() {
		if (file_exists(JPATH_SITE.'/components/com_rsform/helpers/customvalidation.php')) {
			require_once JPATH_SITE.'/components/com_rsform/helpers/customvalidation.php';
			return 'RSFormProCustomValidations';
		} else {
			require_once JPATH_SITE.'/components/com_rsform/helpers/validation.php';
			return 'RSFormProValidations';
		}
	}

	public static function getValidationRules($asArray = false, $remove_multiple = false) {
		$results = get_class_methods(static::getValidationClass());
		
		// Add 'none' as first validation rule
		unset($results[array_search('none', $results)]);
		array_unshift($results, 'none');
		
		// remove the multiple validation because the multiple rules has already been selected, also the none validation is not necessary
		if ($remove_multiple) {
			unset($results[array_search('multiplerules', $results)]);
			unset($results[array_search('none', $results)]);
		}
		
		if ($asArray) {
			return $results;
		} else {
			return implode("\n", $results);
		}
	}

	public static function getDateValidationClass() {
		if (file_exists(JPATH_SITE.'/components/com_rsform/helpers/customdatevalidation.php')) {
			require_once JPATH_SITE.'/components/com_rsform/helpers/customdatevalidation.php';
			return 'RSFormProCustomDateValidations';
		} else {
			require_once JPATH_SITE.'/components/com_rsform/helpers/datevalidation.php';
			return 'RSFormProDateValidations';
		}
	}

	public static function getDateValidationRules($asArray = false) {
		$results = get_class_methods(static::getDateValidationClass());

		// Add 'none' as first validation rule
		unset($results[array_search('none', $results)]);
		array_unshift($results, 'none');

		if ($asArray) {
			return $results;
		} else {
			return implode("\n", $results);
		}
	}

	public static function getEmailAttachOptions()
    {
        $formId = JFactory::getApplication()->input->getInt('formId');
        $db     = JFactory::getDbo();

        $options = array(
            'useremail',
            'adminemail'
        );

        $query = $db->getQuery(true)
            ->select($db->qn('id'))
            ->select($db->qn('subject'))
            ->from($db->qn('#__rsform_emails'))
            ->where($db->qn('type') . ' = ' . $db->q('additional'))
            ->where($db->qn('formId') . ' = ' . $db->q($formId));

        if ($emails = $db->setQuery($query)->loadObjectList())
        {
            foreach ($emails as $email)
            {
                $options[] = $email->id . '|' . $email->subject;
            }
        }

        return implode("\n", $options);
    }

	public static function readConfig($force=false)
	{
		$config = RSFormProConfig::getInstance();

		if ($force) {
			$config->reload();
		}

		return $config->getData();
	}

	public static function getConfig($name = null)
	{
		$config = RSFormProConfig::getInstance();
		if (is_null($name)) {
			return $config->getData();
		} else {
			return $config->get($name);
		}
	}

	public static function componentNameExists($componentName, $formId, $currentComponentId=0, $column = 'ComponentId')
	{
		$db = JFactory::getDbo();

		if ($componentName == 'formId')
			return true;

		$componentName = $db->escape($componentName);
		$formId = (int) $formId;
		$currentComponentId = (int) $currentComponentId;

		$query  = "SELECT c.".$column." FROM #__rsform_properties p LEFT JOIN #__rsform_components c ON (p.ComponentId = c.ComponentId)";
		$query .= "WHERE c.FormId='".$formId."' AND p.PropertyName='NAME' AND p.PropertyValue='".$componentName."'";
		if ($currentComponentId)
			$query .= " AND c.ComponentId != '".$currentComponentId."'";

		$db->setQuery($query);
		$exists = $db->loadResult();

		return $exists;
	}

	public static function getCurrentLanguage($formId=null)
	{
		$mainframe = JFactory::getApplication();
		$lang 	   = JFactory::getLanguage();
		$session   = JFactory::getSession();
		$formId    = !$formId ? $mainframe->input->getInt('formId') || $mainframe->input->getInt('FormId') : $formId;

		// editing in backend ?
		if ($mainframe->isClient('administrator'))
		{
			if ($mainframe->input->getCmd('task') == 'submissions.edit' || ($mainframe->input->getCmd('view') == 'submissions' && $mainframe->input->getCmd('layout') == 'edit'))
			{
				$cid = $mainframe->input->get('cid', array(), 'array');

				$db = JFactory::getDbo();
				$query = $db->getQuery(true)
					->select($db->qn('Lang'))
					->from($db->qn('#__rsform_submissions'))
					->where($db->qn('SubmissionId').'='.$db->q(reset($cid)));
				return $db->setQuery($query)
					->loadResult();
			}

			return $session->get('com_rsform.form.formId'.$formId.'.lang', $lang->getTag());
		}
		// frontend
		else
		{
			return $lang->getTag();
		}
	}

	public static function &getComponentProperties($components, $translate = true) {
		static $cache = array();

		if (is_numeric($components)) {
			$componentIds = array($components);
			$single		  = $components;
		} else {
			$componentIds = array();
			$single		  = false;
			foreach ($components as $componentId) {
				if (is_object($componentId) && !empty($componentId->ComponentId)) {
					$componentIds[] = (int) $componentId->ComponentId;
				} elseif (is_array($componentId) && !empty($componentId['ComponentId'])) {
					$componentIds[] = (int) $componentId['ComponentId'];
				} else {
					$componentIds[] = (int) $componentId;
				}
			}
		}

		if ($componentIds) {
			if ($newComponentIds = array_diff($componentIds, array_keys($cache))) {
				$all_data		= &$cache;
				$db 			= JFactory::getDbo();
				$query 			= $db->getQuery(true);

				$query->select($db->qn('PropertyName'))
					->select($db->qn('PropertyValue'))
					->select($db->qn('ComponentId'))
					->from($db->qn('#__rsform_properties'))
					->where($db->qn('ComponentId').' IN ('.implode(',', $newComponentIds).')');

				if ($results = $db->setQuery($query)->loadObjectList()) {
					foreach ($results as $result) {
						if (!isset($all_data[$result->ComponentId])) {
							$all_data[$result->ComponentId] = array('componentId' => $result->ComponentId);
						}

						$all_data[$result->ComponentId][$result->PropertyName] = $result->PropertyValue;
					}
				}

				// Guess the form ID
				$query = $db->getQuery(true);
				$query->select($db->qn('FormId'))
					->from($db->qn('#__rsform_components'))
					->where($db->qn('ComponentId').'='.$db->q(reset($newComponentIds)));
				$formId = $db->setQuery($query)->loadResult();

				// language
                if ($translate)
                {
                    $lang 		  = RSFormProHelper::getCurrentLanguage($formId);
                    $translations = RSFormProHelper::getTranslations('properties', $formId, $lang);
                    foreach ($all_data as $componentId => $properties) {
                        foreach ($properties as $property => $value) {
                            $reference_id = $componentId.'.'.$property;
                            if (isset($translations[$reference_id])) {
                                $properties[$property] = $translations[$reference_id];
                            }
                        }
                        $all_data[$componentId] = $properties;
                    }
                }
			}
		}

		if ($single) {
			if (!empty($cache[$single])) {
				return $cache[$single];
			}
		} else {
			$results = array();
			foreach ($componentIds as $componentId) {
				$results[$componentId] = &$cache[$componentId];
			}

			return $results;
		}

		$blank = array();
		
		return $blank;
	}

	public static function isCode($value) {
		if (self::hasCode($value)) {
			return eval($value);
		}

		return $value;
	}

	public static function hasCode($value) {
		return (strpos($value, '<code>') !== false);
	}

	public static function getIcon($type) {
		$icon = '';

		switch ($type) {
			case 'calendar': 		$icon = 'calendar-o'; break;
			case 'gmaps': 			$icon = 'map-marker'; break;
			case 'hidden': 			$icon = 'texture'; break;
			case 'jQueryCalendar':  $icon = 'calendar'; break;
			case 'rangeSlider':  	$icon = 'th-list'; break;
			case 'php': 			$icon = 'code'; break;
			case 'support': 		$icon = 'ticket'; break;
		}
		return '<span class="rsficon rsficon-'.$icon.'" style="font-size:24px;margin-right:5px"></span>';
	}

	public static function htmlEscape($val)
	{
		return htmlentities($val, ENT_COMPAT, 'UTF-8');
	}

	public static function explode($value)
	{
		if (!is_array($value))
		{
			$value = str_replace(array("\r\n", "\r"), "\n", $value);
			$value = explode("\n", $value);
		}

		return $value;
	}

	public static function readFile($file, $download_name=null)
	{
		jimport('joomla.filesystem.file');
		$ext = strtolower(JFile::getExt($file));

		if ($ext == 'tgz' || $ext == 'gz') {
			// Needed when some servers with GZIP compression perform double encoding
			if (is_callable('ini_set')) {
				if (is_callable('ini_get') && ini_get('zlib.output_compression')) {
					ini_set('zlib.output_compression', 'Off');
				}

				ini_set('output_buffering', 'Off');
				ini_set('output_handler', '');
			}
			header('Content-Encoding: none');
		}

		if (empty($download_name))
			$download_name = basename($file);

		$fsize = filesize($file);

		header("Cache-Control: public, must-revalidate");
		header('Cache-Control: pre-check=0, post-check=0, max-age=0');
		if (!preg_match('#MSIE#', $_SERVER['HTTP_USER_AGENT']))
			header("Pragma: no-cache");
		header("Expires: 0");
		header("Content-Description: File Transfer");
		header("Expires: Sat, 01 Jan 2000 01:00:00 GMT");
		if (preg_match('#Opera#', $_SERVER['HTTP_USER_AGENT']))
			header("Content-Type: application/octetstream");
		else
			header("Content-Type: application/octet-stream");
		header("Content-Length: ".(string) ($fsize));
		header('Content-Disposition: attachment; filename="'.$download_name.'"');
		header("Content-Transfer-Encoding: binary\n");
		ob_end_flush();
		RSFormProHelper::readFileChunked($file);
		exit();
	}

	public static function readFileChunked($filename, $retbytes=true)
	{
		$chunksize = 1*(1024*1024); // how many bytes per chunk
		$cnt = 0;
		$handle = fopen($filename, 'rb');
		if ($handle === false) {
			return false;
		}
		while (!feof($handle)) {
			$buffer = fread($handle, $chunksize);
			echo $buffer;
			if ($retbytes) {
				$cnt += strlen($buffer);
			}
		}
		$status = fclose($handle);
		if ($retbytes && $status) {
			return $cnt; // return num. bytes delivered like readfile() does.
		}
		return $status;
	}

	public static function getReplacements($SubmissionId, $skip_globals=false)
	{
		// Small hack
		return RSFormProHelper::sendSubmissionEmails($SubmissionId, true, $skip_globals);
	}

	public static function sendSubmissionEmails($SubmissionId, $only_return_replacements=false, $skip_globals=false)
	{
		jimport('joomla.filesystem.file');

		$db             = JFactory::getDbo();
        $mainframe      = JFactory::getApplication();
        $config         = JFactory::getConfig();
        $secret         = $config->get('secret');
		$u              = JUri::getInstance();
		$SubmissionId   = (int) $SubmissionId;
        $placeholders   = array();
        $values         = array();
        $Itemid         = $mainframe->input->getInt('Itemid');
        $Itemid         = $Itemid ? '&amp;Itemid='.$Itemid : '';

        // Get the submission
        require_once JPATH_ADMINISTRATOR . '/components/com_rsform/helpers/submissions.php';
        $submission = RSFormProSubmissionsHelper::getSubmission($SubmissionId);

        if (!$submission)
        {
            return false;
        }

		$formId = $submission->FormId;

        // Load our form
        $query = $db->getQuery(true)
            ->select('*')
            ->from($db->qn('#__rsform_forms'))
            ->where($db->qn('FormId') . ' = ' . $db->q($formId));

		$form = $db->setQuery($query)->loadObject();

		// Multiple items separators
		$form->MultipleSeparator = str_replace(array('\n', '\r', '\t'), array("\n", "\r", "\t"), $form->MultipleSeparator);

		// If our submission has no language, simply inherit it from the form
		if (empty($submission->Lang))
		{
			if (!empty($form->Lang))
            {
                $submission->Lang = $form->Lang;
            }
			else
			{
				$submission->Lang = JFactory::getLanguage()->getDefault();
			}

            $object = (object) array(
                'SubmissionId'  => $submission->SubmissionId,
                'Lang'          => $submission->Lang
            );

			$db->updateObject('#__rsform_submissions', $object, array('SubmissionId'));
		}

		// Get translations
		$translations = RSFormProHelper::getTranslations('forms', $form->FormId, $submission->Lang);
		if ($translations)
        {
            foreach ($translations as $field => $value)
            {
                if (isset($form->{$field}))
                {
                    $form->{$field} = $value;
                }
            }
        }

        $userEmailUploads       = array();
        $adminEmailUploads      = array();
        $additionalEmailUploads = array();

        $nodecimals = (int) RSFormProHelper::getConfig('calculations.nodecimals');
        $decimal    = RSFormProHelper::getConfig('calculations.decimal');
        $thousands  = RSFormProHelper::getConfig('calculations.thousands');

		if ($components = RSFormProHelper::getComponents($formId))
        {
            // Get all properties (default to English)
            $all_data = RSFormProHelper::getComponentProperties($components, false);

            // Translate properties in requested language
            if ($translations = RSFormProHelper::getTranslations('properties', $form->FormId, $submission->Lang))
            {
                foreach ($all_data as $componentId => $properties)
                {
                    foreach ($properties as $property => $value)
                    {
                        $reference_id = $componentId.'.'.$property;
                        if (isset($translations[$reference_id]))
                        {
                            $properties[$property] = $translations[$reference_id];
                        }
                    }
                    $all_data[$componentId] = $properties;
                }
            }

            foreach ($components as $component)
            {
                if (!isset($all_data[$component->ComponentId]))
                {
                    continue;
                }

                $property = $all_data[$component->ComponentId];

                // {component:caption}
                $placeholders[] = '{'.$property['NAME'].':caption}';
                // Hidden fields don't have a caption
                if (in_array($component->ComponentTypeId, array(RSFORM_FIELD_HIDDEN, RSFORM_FIELD_TICKET)))
                {
                    $values[] = $property['NAME'];
                }
                else
                {
                    $values[] = isset($property['CAPTION']) ? $property['CAPTION'] : '';
                }

                // {component:description}
                $placeholders[] = '{'.$property['NAME'].':description}';
                $values[] = isset($property['DESCRIPTION']) ? $property['DESCRIPTION'] : '';

                // {component:name}
                $placeholders[] = '{'.$property['NAME'].':name}';
                $values[] = $property['NAME'];

                // {component:value}
                $placeholders[] = '{'.$property['NAME'].':value}';
                $value = '';
                if (isset($submission->values[$property['NAME']]))
                {
                    $value = $submission->values[$property['NAME']];

                    // Check if this is an upload field
                    if ($component->ComponentTypeId == RSFORM_FIELD_FILEUPLOAD)
                    {
                        $value = '<a href="'.JUri::root().'index.php?option=com_rsform&amp;task=submissions.view.file&amp;hash='.md5($submission->SubmissionId.$secret.$property['NAME']).$Itemid.'">'.basename($submission->values[$property['NAME']]).'</a>';
                    }

                    // Check if this is a multiple field
                    if (in_array($component->ComponentTypeId, array(RSFORM_FIELD_SELECTLIST, RSFORM_FIELD_CHECKBOXGROUP)) || isset($property['ITEMS']))
                    {
                        $value = str_replace("\n", $form->MultipleSeparator, $value);
                    }

                    if ($component->ComponentTypeId == RSFORM_FIELD_TEXTAREA && $form->TextareaNewLines && $property['WYSIWYG'] == 'NO')
                    {
                        $value = nl2br($value);
                    }
                }
                if ($component->ComponentTypeId == RSFORM_FIELD_FREETEXT)
                {
                    $value = $property['TEXT'];
                }
                $values[] = $value;

                // {component:text}
                // {component:price}
                if (isset($property['ITEMS']))
                {
                    $placeholders[] = '{'.$property['NAME'].':text}';
                    $placeholders[] = '{'.$property['NAME'].':price}';
                    if (isset($submission->values[$property['NAME']]))
                    {
                        $all_texts  = array();
                        $all_prices = array();

                        require_once JPATH_ADMINISTRATOR . '/components/com_rsform/helpers/fields/fielditem.php';
                        require_once JPATH_ADMINISTRATOR . '/components/com_rsform/helpers/fieldmultiple.php';
                        $field = new RSFormProFieldMultiple(array(
                            'formId' 			=> $formId,
                            'componentId' 		=> $component->ComponentId,
                            'data' 				=> $property,
                            'value' 			=> array('formId' => $formId, $property['NAME'] => explode("\n", $submission->values[$property['NAME']])),
                            'invalid' 			=> false
                        ));

                        if ($items = $field->getItems())
                        {
                            foreach ($items as $item)
                            {
                                $item = new RSFormProFieldItem($item);

                                if ($item->value === $field->getItemValue($item))
                                {
                                    $all_texts[] = $item->label;
                                    $all_prices[] = $item->flags['price'] !== false ? (float) $item->flags['price'] : 0;
                                }
                            }
                        }

                        if ($all_texts)
                        {
                            $values[] = implode($form->MultipleSeparator, $all_texts);
                            $values[] = number_format(array_sum($all_prices), $nodecimals, $decimal, $thousands);
                        }
                        else
                        {
                            $values[] = $submission->values[$property['NAME']];
                            $values[] = '';
                        }
                    }
                    else
                    {
                        $values[] = '';
                        $values[] = '';
                    }
                }

                // {component:path}
                // {component:localpath}
                // {component:filename}
                if ($component->ComponentTypeId == RSFORM_FIELD_FILEUPLOAD)
                {
                    $placeholders[] = '{'.$property['NAME'].':path}';
                    $placeholders[] = '{'.$property['NAME'].':localpath}';
                    $placeholders[] = '{'.$property['NAME'].':filename}';
                    if (isset($submission->values[$property['NAME']]))
                    {
                        $filepath = $submission->values[$property['NAME']];
                        $filepath = substr_replace($filepath, JUri::root(), 0, strlen(JPATH_SITE)+1);
                        $filepath = str_replace(array('\\', '\\/', '//\\'), '/', $filepath);
                        $values[] = $filepath;
                        $values[] = $submission->values[$property['NAME']];
                        $values[] = basename($submission->values[$property['NAME']]);
                    }
                    else
                    {
                        $values[] = '';
                        $values[] = '';
                        $values[] = '';
                    }

                    // Handle attach to email settings.
                    if (!empty($property['EMAILATTACH']))
                    {
                        if ($parts = explode(',', trim($property['EMAILATTACH'])))
                        {
                            if (in_array('useremail', $parts))
                            {
                                $userEmailUploads[] = $property['NAME'];
                            }

                            if (in_array('adminemail', $parts))
                            {
                                $adminEmailUploads[] = $property['NAME'];
                            }

                            if ($filtered = array_filter($parts, 'intval'))
                            {
                                foreach ($filtered as $emailId)
                                {
                                    if (!isset($additionalEmailUploads[$emailId]))
                                    {
                                        $additionalEmailUploads[$emailId] = array();
                                    }

                                    $additionalEmailUploads[$emailId][] = $property['NAME'];
                                }
                            }
                        }
                    }
                }
            }
        }

		$user = JFactory::getUser($submission->UserId);
		if (empty($user->id))
        {
            $user = JFactory::getUser(0);
        }

		$root 				= $mainframe->isClient('administrator') ? JUri::root() : $u->toString(array('scheme','host', 'port'));
		$confirmation_hash 	= md5($submission->SubmissionId.$formId.$submission->DateSubmitted);
		$hash_link 			= 'index.php?option=com_rsform&task=confirm&hash='.$confirmation_hash;
        $delete_link 		= 'index.php?option=com_rsform&task=deletesubmission&hash='.$submission->SubmissionHash;
		$confirmation 		= $root.($mainframe->isClient('administrator') ? $hash_link : JRoute::_($hash_link));
		$deletion 		    = $root.($mainframe->isClient('administrator') ? $delete_link : JRoute::_($delete_link));

		if (!$skip_globals)
		{
		    $global_placeholders = array(
                '{global:username}'     => $user->username,
                '{global:userid}'       => $user->id,
                '{global:useremail}'    => $user->email,
                '{global:fullname}'     => $user->name,
                '{global:userip}'       => $submission->UserIp,
                '{global:date_added}'   => RSFormProHelper::getDate($submission->DateSubmitted),
                '{global:sitename}'     => $config->get('sitename'),
                '{global:siteurl}'      => JUri::root(),
                '{global:confirmation}' => $confirmation,
                '{global:deletion}'     => $deletion,
                '{global:submissionid}' => $submission->SubmissionId,
                '{global:submission_id}' => $submission->SubmissionId,
                '{global:mailfrom}'     => $config->get('mailfrom'),
                '{global:fromname}'     => $config->get('fromname'),
                '{global:formid}'       => $formId,
                '{global:language}'     => $submission->Lang,
            );

		    $placeholders = array_merge($placeholders, array_keys($global_placeholders));
		    $values = array_merge($values, array_values($global_placeholders));
		}

		$mainframe->triggerEvent('rsfp_onAfterCreatePlaceholders', array(array('form' => &$form, 'placeholders' => &$placeholders, 'values' => &$values, 'submission' => $submission)));

		if ($only_return_replacements)
        {
            return array($placeholders, $values);
        }

		// RSForm! Pro Scripting - User Email Text
		// performance check
		if (strpos($form->UserEmailText, '{/if}') !== false)
		{
			require_once dirname(__FILE__).'/scripting.php';
			RSFormProScripting::compile($form->UserEmailText, $placeholders, $values);
		}

		$userEmail = array(
			'to'        => str_replace($placeholders, $values, $form->UserEmailTo),
			'cc'        => str_replace($placeholders, $values, $form->UserEmailCC),
			'bcc'       => str_replace($placeholders, $values, $form->UserEmailBCC),
			'from'      => str_replace($placeholders, $values, $form->UserEmailFrom),
			'replyto'   => str_replace($placeholders, $values, $form->UserEmailReplyTo),
			'fromName'  => str_replace($placeholders, $values, $form->UserEmailFromName),
			'text'      => str_replace($placeholders, $values, $form->UserEmailText),
			'subject'   => str_replace($placeholders, $values, $form->UserEmailSubject),
			'mode'      => $form->UserEmailMode,
			'files'     => array()
		);

		// user cc
		if (strpos($userEmail['cc'], ',') !== false)
        {
            $userEmail['cc'] = explode(',', $userEmail['cc']);
        }

		// user bcc
		if (strpos($userEmail['bcc'], ',') !== false)
        {
            $userEmail['bcc'] = explode(',', $userEmail['bcc']);
        }

		$file = str_replace($placeholders, $values, $form->UserEmailAttachFile);
		if ($form->UserEmailAttach && file_exists($file))
        {
            $userEmail['files'][] = $file;
        }

		// Need to attach files
		// User Email
        if ($userEmailUploads)
        {
            foreach ($userEmailUploads as $name)
            {
                if (!empty($submission->values[$name]))
                {
                    $userEmail['files'][] = $submission->values[$name];
                }
            }
        }

		// RSForm! Pro Scripting - Admin Email Text
		// performance check
		if (strpos($form->AdminEmailText, '{/if}') !== false)
		{
			require_once dirname(__FILE__).'/scripting.php';
			RSFormProScripting::compile($form->AdminEmailText, $placeholders, $values);
		}

		$adminEmail = array(
			'to'        => str_replace($placeholders, $values, $form->AdminEmailTo),
			'cc'        => str_replace($placeholders, $values, $form->AdminEmailCC),
			'bcc'       => str_replace($placeholders, $values, $form->AdminEmailBCC),
			'from'      => str_replace($placeholders, $values, $form->AdminEmailFrom),
			'replyto'   => str_replace($placeholders, $values, $form->AdminEmailReplyTo),
			'fromName'  => str_replace($placeholders, $values, $form->AdminEmailFromName),
			'text'      => str_replace($placeholders, $values, $form->AdminEmailText),
			'subject'   => str_replace($placeholders, $values, $form->AdminEmailSubject),
			'mode'      => $form->AdminEmailMode,
			'files'     => array()
		);

		// admin cc
		if (strpos($adminEmail['cc'], ',') !== false)
        {
            $adminEmail['cc'] = explode(',', $adminEmail['cc']);
        }

		// admin bcc
		if (strpos($adminEmail['bcc'], ',') !== false)
        {
            $adminEmail['bcc'] = explode(',', $adminEmail['bcc']);
        }

		// Admin Email
        if ($adminEmailUploads)
        {
            foreach ($adminEmailUploads as $name)
            {
                if (!empty($submission->values[$name]))
                {
                    $adminEmail['files'][] = $submission->values[$name];
                }
            }
        }

		$mainframe->triggerEvent('rsfp_beforeUserEmail', array(array('form' => &$form, 'placeholders' => &$placeholders, 'values' => &$values, 'submissionId' => $SubmissionId, 'userEmail'=>&$userEmail)));

		// Script called before the User Email is sent.
		eval($form->UserEmailScript);

		// mail users
		if ($userEmail['to'])
		{
			$recipients = explode(',', $userEmail['to']);

			RSFormProHelper::sendMail($userEmail['from'], $userEmail['fromName'], $recipients, $userEmail['subject'], $userEmail['text'], $userEmail['mode'], !empty($userEmail['cc']) ? $userEmail['cc'] : null, !empty($userEmail['bcc']) ? $userEmail['bcc'] : null, $userEmail['files'], !empty($userEmail['replyto']) ? $userEmail['replyto'] : '');
		}

		$mainframe->triggerEvent('rsfp_beforeAdminEmail', array(array('form' => &$form, 'placeholders' => &$placeholders, 'values' => &$values, 'submissionId' => $SubmissionId, 'adminEmail'=>&$adminEmail)));

		// Script called before the Admin Email is sent.
		eval($form->AdminEmailScript);

		//mail admins
		if ($adminEmail['to'])
		{
			$recipients = explode(',', $adminEmail['to']);

			RSFormProHelper::sendMail($adminEmail['from'], $adminEmail['fromName'], $recipients, $adminEmail['subject'], $adminEmail['text'], $adminEmail['mode'], !empty($adminEmail['cc']) ? $adminEmail['cc'] : null, !empty($adminEmail['bcc']) ? $adminEmail['bcc'] : null, $adminEmail['files'], !empty($adminEmail['replyto']) ? $adminEmail['replyto'] : '');
		}

		// Additional emails
        $query = $db->getQuery(true)
            ->select('*')
            ->from($db->qn('#__rsform_emails'))
            ->where($db->qn('type') . ' = ' . $db->q('additional'))
            ->where($db->qn('formId') . ' = ' . $db->q($formId))
            ->where($db->qn('from') . ' != ' . $db->q(''));
		if ($emails = $db->setQuery($query)->loadObjectList())
		{
			$translations = RSFormProHelper::getTranslations('emails', $formId, $submission->Lang);

			foreach ($emails as $email)
			{
			    foreach (array('fromname', 'subject', 'message') as $value)
                {
                    if (isset($translations[$email->id . '.' . $value]))
                    {
                        $email->{$value} = $translations[$email->id . '.' . $value];
                    }
                }

				if (empty($email->fromname) || empty($email->subject) || empty($email->message))
				{
					continue;
				}

				// RSForm! Pro Scripting - Additional Email Text
				// performance check
				if (strpos($email->message, '{/if}') !== false)
				{
					require_once dirname(__FILE__).'/scripting.php';
					RSFormProScripting::compile($email->message, $placeholders, $values);
				}

				$additionalEmail = array(
					'to'        => str_replace($placeholders, $values, $email->to),
					'cc'        => str_replace($placeholders, $values, $email->cc),
					'bcc'       => str_replace($placeholders, $values, $email->bcc),
					'from'      => str_replace($placeholders, $values, $email->from),
					'replyto'   => str_replace($placeholders, $values, $email->replyto),
					'fromName'  => str_replace($placeholders, $values, $email->fromname),
					'text'      => str_replace($placeholders, $values, $email->message),
					'subject'   => str_replace($placeholders, $values, $email->subject),
					'mode'      => $email->mode,
					'files'     => array()
				);

				if (isset($additionalEmailUploads, $additionalEmailUploads[$email->id]))
                {
                    foreach ($additionalEmailUploads[$email->id] as $name)
                    {
                        if (!empty($submission->values[$name]))
                        {
                            $additionalEmail['files'][] = $submission->values[$name];
                        }
                    }
                }

				// additional cc
				if (strpos($additionalEmail['cc'], ',') !== false)
                {
                    $additionalEmail['cc'] = explode(',', $additionalEmail['cc']);
                }

				// additional bcc
				if (strpos($additionalEmail['bcc'], ',') !== false)
                {
                    $additionalEmail['bcc'] = explode(',', $additionalEmail['bcc']);
                }

				$mainframe->triggerEvent('rsfp_beforeAdditionalEmail', array(array('form' => &$form, 'placeholders' => &$placeholders, 'values' => &$values, 'submissionId' => $SubmissionId, 'additionalEmail'=>&$additionalEmail)));
				eval($form->AdditionalEmailsScript);

				// mail users
				if ($additionalEmail['to'])
				{
					$recipients = explode(',', $additionalEmail['to']);

					RSFormProHelper::sendMail($additionalEmail['from'], $additionalEmail['fromName'], $recipients, $additionalEmail['subject'], $additionalEmail['text'], $additionalEmail['mode'], !empty($additionalEmail['cc']) ? $additionalEmail['cc'] : null, !empty($additionalEmail['bcc']) ? $additionalEmail['bcc'] : null, $additionalEmail['files'], !empty($additionalEmail['replyto']) ? $additionalEmail['replyto'] : '');
				}
			}
		}

		return array($placeholders, $values);
	}

	public static function escapeArray(&$val, &$key)
	{
		$db = JFactory::getDbo();
		$val = $db->escape($val);
		$key = $db->escape($key);
	}

	public static function quoteArray(&$val, $key) {
		static $db;
		if (!$db) {
			$db = JFactory::getDbo();
		}

		$val = $db->q($val);
	}

	public static function componentExists($formId, $componentTypeId, $published = 1)
	{
		$formId = (int) $formId;
		$db     = JFactory::getDbo();

        if (is_array($componentTypeId))
        {
            $componentTypeId = array_map('intval', $componentTypeId);
        }
        else
        {
            $componentTypeId = array((int) $componentTypeId);
        }

        $query = $db->getQuery(true)
            ->select($db->qn('ComponentId'))
            ->from($db->qn('#__rsform_components'))
            ->where($db->qn('ComponentTypeId') . ' IN (' . implode(',', $componentTypeId) . ')')
            ->where($db->qn('FormId') . ' = ' . $db->q($formId));
        if ($published)
        {
            $query->where($db->qn('Published') . ' = ' . $db->q(1));
        }

        return $db->setQuery($query)->loadColumn();
	}

	public static function cleanCache()
	{
		$cache 	= JCache::getInstance('page');
		$id 	= $cache->makeId();

		if ($handler = $cache->_getStorage()) {
			$handler->remove($id, 'page');
		}

		// Test this
		// $cache->clean();
	}

	// conditions
	public static function getConditions($formId, $lang=null)
	{
	    require_once __DIR__ . '/conditions.php';

		return RSFormProConditions::getConditions($formId, $lang);
	}

	public static function showForm($formId, $val=array(), $validation=array())
	{
		$mainframe 	= JFactory::getApplication();
		$doc 		= JFactory::getDocument();
		$user 		= JFactory::getUser();
		$jconfig 	= JFactory::getConfig();
		$u 			= RSFormProHelper::getURL();
		$formId 	= (int) $formId;

		$logged     = $user->id;

		$mainframe->triggerEvent('rsfp_f_onBeforeShowForm');

		$form = RSFormProHelper::getForm($formId);

		$lang 		  = RSFormProHelper::getCurrentLanguage();
		$translations = RSFormProHelper::getTranslations('forms', $form->FormId, $lang);
		if ($translations)
			foreach ($translations as $field => $value)
			{
				if (isset($form->$field))
					$form->$field = $value;
			}

		if ($form->JS)
			RSFormProAssets::addCustomTag($form->JS);
		if ($form->CSS)
			RSFormProAssets::addCustomTag($form->CSS);

		if ($form->ScrollToError) {
			RSFormProAssets::addScriptDeclaration('RSFormPro.scrollToError = true;');
		}

		RSFormProAssets::addStyleSheet(JHtml::stylesheet('com_rsform/front.css', array('pathOnly' => true, 'relative' => true)));
		RSFormProAssets::addScript(JHtml::script('com_rsform/script.js', array('pathOnly' => true, 'relative' => true)));

		// calendars
		$YUICalendars = RSFormProHelper::componentExists($formId, RSFORM_FIELD_CALENDAR);
		$jQueryCalendars = RSFormProHelper::componentExists($formId, RSFORM_FIELD_JQUERY_CALENDAR);
		$rangeSliders = RSFormProHelper::componentExists($formId, RSFORM_FIELD_RANGE_SLIDER);

		$formLayout = $form->FormLayout;

		unset($form->FormLayout);
		$errorMessage = $form->ErrorMessage;
		unset($form->ErrorMessage);

		$components = RSFormProHelper::getComponents($formId);

		$pages			= array();
		$page_progress  = array();
		$submits		= array();
		foreach ($components as $component)
		{
			if ($component->ComponentTypeId == RSFORM_FIELD_PAGEBREAK)
				$pages[] = $component->ComponentId;
			elseif ($component->ComponentTypeId == RSFORM_FIELD_SUBMITBUTTON)
				$submits[] = $component->ComponentId;
		}

		$find 	  = array();
		$replace  = array();

        eval($form->ScriptBeforeDisplay);

		$start_page = 0;
		if (!empty($validation)) {
			foreach ($components as $component)
			{
				if (in_array($component->ComponentId, $validation)) {
					break;
				}
				if ($component->ComponentTypeId == RSFORM_FIELD_PAGEBREAK)
					$start_page++;
			}
		}

		// stores the error class names found in the form layout
		$layoutErrorClass = array();
		$fieldErrorClass  = array();

		$layoutName = (string) preg_replace('/[^A-Z0-9]/i', '', $form->FormLayoutName);
		
		// keep the loaded framework class for further purpose
		$layoutClassLoaded = false;
		if (file_exists(dirname(__FILE__).'/formlayouts/'.$layoutName.'.php')) {
			require_once dirname(__FILE__).'/formlayouts/'.$layoutName.'.php';

			$class = 'RSFormProFormLayout'.$layoutName;
			if (class_exists($class)) {
				$layout = new $class();
				
				$layoutClassLoaded = $layout;
				if ($form->LoadFormLayoutFramework) {
					$layout->loadFramework();
				}

				// Return the specific layout error class
				$layoutErrorClass[$layoutName] = $layout->errorClass;
				$fieldErrorClass[$layoutName] = $layout->fieldErrorClass;
			}
		} else {
			$layoutErrorClass[$layoutName] = '';
			$fieldErrorClass[$layoutName] = 'rsform-error';
		}
		
		if ($doc->getDirection() == 'rtl')
			RSFormProAssets::addStyleSheet(JHtml::stylesheet('com_rsform/front-rtl.css', array('pathOnly' => true, 'relative' => true)));

		$hasAjax = (bool) $form->AjaxValidation;

		$all_data = RSFormProHelper::getComponentProperties($components);
		foreach ($components as $component) {
			if (in_array($component->ComponentTypeId, RSFormProHelper::$captchaFields))
			{
				if ($logged && $form->RemoveCaptchaLogged)
				{
					continue;
				}
			}

			$data 						= $all_data[$component->ComponentId];
			$data['componentTypeId'] 	= $component->ComponentTypeId;
			$data['ComponentTypeName'] 	= $component->ComponentTypeName;
			$data['Order'] 				= $component->Order;

			// Pagination
			if ($component->ComponentTypeId == RSFORM_FIELD_PAGEBREAK)
			{
				// Set flag to load Ajax scripts
				if (!empty($data['VALIDATENEXTPAGE']) && $data['VALIDATENEXTPAGE'] == 'YES') {
					$hasAjax = true;
				}
				$data['PAGES'] 	 	= $pages;
				$page_progress[]	= array(
				    'show' => !empty($data['DISPLAYPROGRESS']) && in_array($data['DISPLAYPROGRESS'], array('YES', 'AUTO')),
                    'text' => !empty($data['DISPLAYPROGRESSMSG']) ? $data['DISPLAYPROGRESSMSG'] : '',
                    'auto' => !empty($data['DISPLAYPROGRESS']) && $data['DISPLAYPROGRESS'] == 'AUTO'
                );
			}
			elseif ($component->ComponentTypeId == RSFORM_FIELD_SUBMITBUTTON)
			{
				$data['SUBMITS'] = $submits;
				if ($component->ComponentId == end($submits))
                {
                    $page_progress[] = array(
                        'show' => !empty($data['DISPLAYPROGRESS']) && in_array($data['DISPLAYPROGRESS'], array('YES', 'AUTO')),
                        'text' => !empty($data['DISPLAYPROGRESSMSG']) ? $data['DISPLAYPROGRESSMSG'] : '',
                        'auto' => !empty($data['DISPLAYPROGRESS']) && $data['DISPLAYPROGRESS'] == 'AUTO'
                    );
                }
			}

			// Error classes
			$errorClass = '';
			if (!empty($validation) && in_array($component->ComponentId, $validation)) {
				$errorClass = $layoutErrorClass[$layoutName];
			}
			$find[] = '{'.$component->name.':errorClass}';
			$replace[] 	= $errorClass;

			// Caption
			$caption = '';
			if (isset($data['SHOW']) && $data['SHOW'] == 'NO') {
				$caption = '';
			} elseif (isset($data['CAPTION'])) {
				$caption = $data['CAPTION'];
			}
			$find[] 	= '{'.$component->name.':caption}';
			$replace[] 	= $caption;

			// Body
			$out	   = '';
			$invalid   = in_array($component->ComponentId, $validation);

			// Some filtering in the field type
			$type 	= (string) preg_replace('/[^A-Z0-9_\.-]/i', '', $data['ComponentTypeName']);
			$type 	= ltrim($type, '.');

			$layouts = array(
				// Path to the layout (overridden) class
				'RSFormProField'.$layoutName.$type => dirname(__FILE__).'/fields/'.strtolower($layoutName).'/'.strtolower($type).'.php',

				// Path to the fallback (basic) class
				'RSFormProField'.$type => dirname(__FILE__).'/fields/'.strtolower($type).'.php'
			);

			// For legacy reasons...
			$r = array(
				'ComponentTypeId' => $data['componentTypeId'],
				'Order'			  => isset($data['Order']) ? $data['Order'] : 0
			);

			$mainframe->triggerEvent('rsfp_bk_onBeforeCreateFrontComponentBody', array(array(
				'out' 			=> &$out,
				'formId' 		=> $formId,
				'componentId' 	=> $component->ComponentId,
				'data' 			=> &$data,
				'value' 		=> &$val
			)));

			$config = array(
				'formId' 			=> $formId,
				'componentId' 		=> $component->ComponentId,
				'data' 				=> $data,
				'value' 			=> $val,
				'invalid' 			=> $invalid,
				'errorClass' 		=> $layoutErrorClass[$layoutName],
				'fieldErrorClass' 	=> $fieldErrorClass[$layoutName]
			);

			foreach ($layouts as $class => $file) {
				if (file_exists($file)) {
					// If class doesn't exist, load the file
					if (!class_exists($class)) {
						require_once $file;
					}

					// Create the field
					$field = new $class($config);

					// Return the output
					$out .= $field->output;

					// do not load the other class again if one is already initiated
					break;
				}
			}

			$mainframe->triggerEvent('rsfp_bk_onAfterCreateFrontComponentBody', array(array(
				'out' 			=> &$out,
				'formId' 		=> $formId,
				'componentId' 	=> $component->ComponentId,
				'data' 			=> $data,
				'value' 		=> $val,
				'r'				=> $r,
				'invalid' 		=> $invalid
			)));

			$find[] 	= '{'.$component->name.':body}';
			$replace[] 	= $out;

			// Description
			$description = '';
			if (isset($data['SHOW']) && $data['SHOW'] == 'NO') {
				$description = '';
			} elseif (isset($data['DESCRIPTION'])) {
				$description = $data['DESCRIPTION'];
			}
			$find[] 	= '{'.$component->name.':description}';
			$replace[] 	= $description;

			// Validation message
			$validationMessage 	= '';
			if (isset($data['SHOW']) && $data['SHOW'] == 'NO') {
				$validationMessage = '';
			} elseif (isset($data['VALIDATIONMESSAGE'])) {
				if (!empty($validation) && in_array($component->ComponentId, $validation)) {
					$validationMessage = '<span id="component'.$component->ComponentId.'" class="formError">'.$data['VALIDATIONMESSAGE'].'</span>';
				} else {
					$validationMessage = '<span id="component'.$component->ComponentId.'" class="formNoError">'.$data['VALIDATIONMESSAGE'].'</span>';
				}
			}
			$find[] 	= '{'.$component->name.':validation}';
			$replace[] 	= $validationMessage;
		}
		unset($all_data);


		$mainframe->triggerEvent('rsfp_f_onInitFormDisplay', array(array(
			'find'		 => &$find,
			'replace'	 => &$replace,
			'formLayout' => &$formLayout
		)));

		// Global placeholders
		$global = array(
			'{global:formid}'		=> $form->FormId,
			'{global:formtitle}'	=> $form->FormTitle,
			'{global:username}'		=> $user->get('username'),
			'{global:userip}'		=> $mainframe->input->server->getString('REMOTE_ADDR'),
			'{global:userid}'		=> $user->get('id'),
			'{global:useremail}'	=> $user->get('email'),
			'{global:fullname}'		=> $user->get('name'),
			'{global:sitename}'		=> $jconfig->get('sitename'),
			'{global:siteurl}'		=> JUri::root(),
			'{global:mailfrom}'		=> $jconfig->get('mailfrom'),
			'{global:fromname}'		=> $jconfig->get('fromname')
		);

		$find 	 = array_merge($find, array_keys($global));
		$replace = array_merge($replace, array_values($global));

		// Error placeholder
		$error = '';
		if (!empty($validation)) {
			$error = $errorMessage;
		} elseif ($hasAjax) {
			$error = '<div id="rsform_error_'.$formId.'" style="display: none;">'.$errorMessage.'</div>';
		}
		$find[] 	= '{error}';
		$replace[] 	= $error;

        // Check for {if} scripting inside Form Layout
        if (strpos($formLayout, '{/if}') !== false)
        {
            require_once dirname(__FILE__) . '/scripting.php';
            RSFormProScripting::compile($formLayout, $find, $replace);
        }

		// Replace all placeholders
		$formLayout = str_replace($find, $replace, $formLayout);

		$formLayout .= '<input type="hidden" name="form[formId]" value="'.$formId.'"/>';

		if ($form->FormLayoutName == 'responsive') {
			$form->CSSClass .= ' formResponsive';
		}

		$CSSClass 	= $form->CSSClass ? ' class="'.RSFormProHelper::htmlEscape(trim($form->CSSClass)).'"' : '';
		$CSSId 		= $form->CSSId ? ' id="'.RSFormProHelper::htmlEscape(trim($form->CSSId)).'"' : '';
		$CSSName 	= $form->CSSName ? ' name="'.RSFormProHelper::htmlEscape(trim($form->CSSName)).'"' : '';
		$u 			= $form->CSSAction ? $form->CSSAction : $u;
		$CSSAdditionalAttributes = $form->CSSAdditionalAttributes ? ' '.trim($form->CSSAdditionalAttributes) : '';

		if (!empty($pages))
		{
			$total_pages 	  = count($pages)+1;
			$step			  = round(100/$total_pages, 2);
			$replace_progress = array('{page}', '{total}', '{percent}');
			$with_progress 	  = array(1, $total_pages, $step*1);

			$progress 		 = reset($page_progress);
			$progress_script = '';
			if ($layoutClassLoaded && $progress['auto']) {
				$progress['text'] = $layoutClassLoaded->progressContent;
			} 
			$formLayout = '<div id="rsform_progress_'.$formId.'" class="rsformProgress">'.($progress['show'] ? str_replace($replace_progress, $with_progress, $progress['text']) : '').'</div>'."\n".$formLayout;
			foreach ($page_progress as $p => $progress)
			{
				$progress['text'] = str_replace(array("\r", "\n"), array('', '\n'), addcslashes($progress['text'], "'"));
				if ($layoutClassLoaded && $progress['auto']) {
					$progress['text'] = $layoutClassLoaded->progressContent;
				}
				$replace_progress = array('{page}', '{total}', '{percent}');
				$with_progress 	  = array($p+1, $total_pages, $p+1 == $total_pages ? 100 : $step*($p+1));
				$progress_script .= "if (page == ".$p.") document.getElementById('rsform_progress_".$formId."').innerHTML = '".($progress['show'] ? str_replace($replace_progress, $with_progress, $progress['text']) : '')."';";
			}
			$formLayout .= "\n".'<script type="text/javascript">'."\n".'function rsfp_showProgress_'.$formId.'(page) {'."\n".$progress_script."\n".'}'."\n".'</script>';
		}

		$encType = '';
		if (RSFormProHelper::componentExists($formId, RSFORM_FIELD_FILEUPLOAD)) {
			$encType = ' enctype="multipart/form-data"';
		}

		$formLayout = '<form method="post" '.$CSSId.$CSSClass.$CSSName.$CSSAdditionalAttributes.$encType.' action="'.RSFormProHelper::htmlEscape($u).'">'.$formLayout.'</form>';

		require_once JPATH_ADMINISTRATOR.'/components/com_rsform/helpers/prices.php';
		if ($prices = RSFormProPrices::getInstance($formId)->getPrices()) {
			$script = '';
			foreach ($prices as $componentName => $values) {
				$script .= "RSFormProPrices['".addslashes($formId.'_'.$componentName)."'] = ".json_encode($values).";\n";
			}
			$formLayout .= "\n".'<script type="text/javascript">'."\n".$script."\n".'</script>'."\n";
		}

		if (!empty($YUICalendars) || !empty($jQueryCalendars))
		{
			require_once JPATH_ADMINISTRATOR.'/components/com_rsform/helpers/calendar.php';
			// render the YUI Calendars
			if (!empty($YUICalendars)) {
				$calendar = RSFormProCalendar::getInstance('YUICalendar');
				RSFormProAssets::addScriptDeclaration($calendar->printInlineScript($formId));
			}
			// render the jQuery Calendars
			if ($jQueryCalendars) {
				$calendar = RSFormProCalendar::getInstance('jQueryCalendar');
				RSFormProAssets::addScriptDeclaration($calendar->printInlineScript($formId));
			}
		}
		
		if (!empty($rangeSliders)) {
			require_once JPATH_ADMINISTRATOR.'/components/com_rsform/helpers/rangeslider.php';
			$rangeSlider = RSFormProRangeSlider::getInstance();
			RSFormProAssets::addScriptDeclaration($rangeSlider->printInlineScript($formId));
		}

		if (!empty($pages)) {
			$formLayout .= '<script type="text/javascript">rsfp_changePage('.$formId.', '.$start_page.', '.count($pages).');</script>'."\n";
		}

		if ($hasAjax) {
			$formLayout .= '<script type="text/javascript">RSFormPro.Ajax.URL = '.json_encode(JRoute::_('index.php?option=com_rsform&task=ajaxValidate', false)).';</script>';
		}

		$validationParams = array(
			'parent' => $layoutErrorClass[$layoutName],
			'field'  => $fieldErrorClass[$layoutName]
		);

		if ($form->AjaxValidation) {
		    RSFormProAssets::addScriptDeclaration(
"RSFormProUtils.addEvent(window, 'load', function(){
    RSFormPro.Ajax.overrideSubmit(" . $formId . ", " . json_encode($validationParams) . ");
});");
		} else {
			RSFormProAssets::addScriptDeclaration(
"RSFormProUtils.addEvent(window, 'load', function(){
	RSFormPro.setHTML5Validation('" . $formId . "', " . ($form->DisableSubmitButton ? 'true' : 'false') . ", " . json_encode($validationParams) . ", " . count($pages) . ");  
});");
		}

		// Allow plugins to inject code with their own Ajax script
		$ajaxScript = '';
		$mainframe->triggerEvent('rsfp_f_onAJAXScriptCreate', array(array('script' => &$ajaxScript, 'formId' => $formId)));

		if ($hasAjax || $ajaxScript) {
			$formLayout .= "\n".'<script type="text/javascript">';
			$formLayout .= "\n".'ajaxExtraValidationScript['.$formId.'] = function(task, formId, data){ '."\n";
			$formLayout .= 'var formComponents = {};'."\n";
			foreach ($components as $component) {
				if (in_array($component->ComponentTypeId, array(RSFORM_FIELD_BUTTON, RSFORM_FIELD_FILEUPLOAD, RSFORM_FIELD_FREETEXT, RSFORM_FIELD_HIDDEN, RSFORM_FIELD_SUBMITBUTTON, RSFORM_FIELD_TICKET, RSFORM_FIELD_PAGEBREAK))) {
					continue;
				}

				$formLayout .= "formComponents[".$component->ComponentId."]='".$component->name."';";
			}
			$formLayout .= "\n".'RSFormPro.Ajax.displayValidationErrors(formComponents, task, formId, data);'."\n";
			// has this been modified?
			if ($ajaxScript) {
				$formLayout .= $ajaxScript;
			}
			$formLayout .= '};'."\n";
			$formLayout .= '</script>';
		}

        require_once __DIR__ . '/conditions.php';
		if ($conditions = RSFormProConditions::getConditions($formId, $lang))
		{
			$formLayout .= RSFormProConditions::buildJS($formId, $conditions);
		}
		unset($conditions);

		if ($calculations = RSFormProHelper::getCalculations($formId)) {
			require_once dirname(__FILE__).'/calculations.php';

			$formLayout .= "\n".'<script type="text/javascript">';
			$formLayout .= "\n".'function rsfp_Calculations'.$formId.'(){';

			foreach ($calculations as $calculation) {
				$expression = RSFormProCalculations::expression($calculation, $formId);
				$formLayout .= "\n".$expression."\n";
			}

			$formLayout .= "\n".'}';
			$formLayout .= "\n".'rsfp_Calculations'.$formId.'();';
			$formLayout .= RSFormProCalculations::getFields($calculations,$formId);
			$formLayout .= "\n".'rsfp_setCalculationsEvents('.$formId.',rsfpCalculationFields'.$formId.');';
			$formLayout .= "\n".'</script>';
		}

		eval($form->ScriptDisplay);

		//Trigger Event - onBeforeFormDisplay
		$mainframe->triggerEvent('rsfp_f_onBeforeFormDisplay', array(array('formLayout'=>&$formLayout,'formId'=>$formId,'formLayoutName' => $layoutName)));
		return $formLayout;
	}

	public static function showThankYouMessage($formId)
	{
		$mainframe = JFactory::getApplication();
		$formId = (int) $formId;

		$db = JFactory::getDbo();
		$db->setQuery("SELECT ScrollToThankYou, ThankYouMessagePopUp, FormLayoutName FROM #__rsform_forms WHERE FormId='".$formId."'");
		$form = $db->loadObject();

		$doc = JFactory::getDocument();
		RSFormProAssets::addStyleSheet(JHtml::stylesheet('com_rsform/front.css', array('pathOnly' => true, 'relative' => true)));
		if ($doc->getDirection() == 'rtl')
			RSFormProAssets::addStyleSheet(JHtml::stylesheet('com_rsform/front-rtl.css', array('pathOnly' => true, 'relative' => true)));

		$session = JFactory::getSession();
		$formparams = $session->get('com_rsform.formparams.formId'.$formId);
		$output = base64_decode($formparams->thankYouMessage);

		if ($formparams->loadLayoutFramework) {
            $layoutName = $form->FormLayoutName;
            if (file_exists(dirname(__FILE__) . '/formlayouts/' . $layoutName . '.php')) {
                require_once dirname(__FILE__) . '/formlayouts/' . $layoutName . '.php';

                $class = 'RSFormProFormLayout' . $layoutName;
                if (class_exists($class)) {
                    $layout = new $class();

                    if (is_callable(array($layout, 'loadFramework'))) {
                        $layout->loadFramework();
                    }
                }
            }
        }

		// Clear
		$session->clear('com_rsform.formparams.formId'.$formId);

		//Trigger Event - onAfterShowThankyouMessage
		$mainframe->triggerEvent('rsfp_f_onAfterShowThankyouMessage', array(array('output'=>&$output,'formId'=>&$formId)));

		if ($form->ScrollToThankYou)
		{
			// scroll the window to the Thank You Message
			$scrolltoScript = 'RSFormProUtils.addEvent(window, \'load\',function(){ RSFormPro.scrollToElement(document.getElementById(\'rsfp-thankyou-scroll' . $formId . '\')); });';
			RSFormProAssets::addScript(JHtml::script('com_rsform/script.js', array('pathOnly' => true, 'relative' => true)));
			RSFormProAssets::addScriptDeclaration($scrolltoScript);
		}

		if ($form->ThankYouMessagePopUp && !$form->ScrollToThankYou) {
			//rsfp-thankyou-popup-container
			$popupScript = 'RSFormProUtils.addEvent(window, \'load\',function(){ RSFormPro.showThankYouPopup(document.getElementById(\'rsfp-thankyou-popup-container' . $formId . '\')); });';
			RSFormProAssets::addScript(JHtml::script('com_rsform/script.js', array('pathOnly' => true, 'relative' => true)));
			RSFormProAssets::addStyleSheet(JHtml::stylesheet('com_rsform/popup.css', array('pathOnly' => true, 'relative' => true)));
			RSFormProAssets::addScriptDeclaration($popupScript);
		}

		// Cache enabled ?
		jimport('joomla.plugin.helper');
		$cache_enabled = JPluginHelper::isEnabled('system', 'cache');
		if ($cache_enabled)
			RSFormProHelper::cleanCache();

		return $output;
	}

	public static function processForm($formId)
	{
		$mainframe  = JFactory::getApplication();
        $db         = JFactory::getDbo();
		$formId     = (int) $formId;
		$form       = RSFormProHelper::getForm($formId);

		$lang 		  = RSFormProHelper::getCurrentLanguage();
		$translations = RSFormProHelper::getTranslations('forms', $formId, $lang);
		if ($translations)
        {
            foreach ($translations as $field => $value)
            {
                if (isset($form->$field))
                {
                    $form->$field = $value;
                }
            }
        }

		$invalid = RSFormProHelper::validateForm($formId);

		$post = JFactory::getApplication()->input->post->get('form', array(), 'array');

		//Trigger Event - onBeforeFormValidation
		$mainframe->triggerEvent('rsfp_f_onBeforeFormValidation', array(array('invalid'=>&$invalid, 'formId' => $formId, 'post' => &$post)));

		$_POST['form'] = $post;

		eval($form->ScriptProcess);

		if (!empty($invalid))
			return $invalid;

		$post = $_POST['form'];

		//Trigger Event - onBeforeFormProcess
		$mainframe->triggerEvent('rsfp_f_onBeforeFormProcess', array(array('post' => &$post)));

		if (empty($invalid))
		{
			// Cache enabled ?
			jimport('joomla.plugin.helper');
			$cache_enabled = JPluginHelper::isEnabled('system', 'cache');
			if ($cache_enabled)
				RSFormProHelper::cleanCache();

			$user = JFactory::getUser();

            $SubmissionHash = JApplicationHelper::getHash(JUserHelper::genRandomPassword());

			// Add to db (submission)
			$date = JFactory::getDate();

            $submission = (object) array(
                'FormId'         => $formId,
                'DateSubmitted'  => $date->toSql(),
                'UserIp'         => isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '',
                'Username'       => $user->username,
                'UserId'         => $user->id,
                'Lang'           => RSFormProHelper::getCurrentLanguage(),
                'confirmed'      => $form->ConfirmSubmission ? 0 : 1,
                'SubmissionHash' => $SubmissionHash
            );

			$db->insertObject('#__rsform_submissions', $submission, 'SubmissionId');

			$SubmissionId = $submission->SubmissionId;

			// get the form components
			$formComponents = RSFormProHelper::getComponents($formId);
			// check if files have been submitted
			$files = JFactory::getApplication()->input->files->get('form', null, 'raw');

			foreach ($formComponents as $component) {
				$type 	= (string) preg_replace('/[^A-Z0-9_\.-]/i', '', $component->ComponentTypeName);
				$type 	= ltrim($type, '.');

				$fieldTypeClass = 'RSFormProField' . $type;
				$fieldTypeFile  = dirname(__FILE__) . '/fields/' . strtolower($type) . '.php';

				if (file_exists($fieldTypeFile))
				{
					// If class doesn't exist, load the file
					if (!class_exists($fieldTypeClass))
					{
						require_once $fieldTypeFile;
					}
					
					$config = array(
						'formId'        => $formId,
						'componentId'   => $component->ComponentId,
						'data'          => RSFormProHelper::getComponentProperties($component->ComponentId)
					);

					// access the field class
					$field = new $fieldTypeClass($config);

					$field->processBeforeStore($SubmissionId, $post, $files);
				}
			}

			//Trigger Event - onBeforeStoreSubmissions
			$mainframe->triggerEvent('rsfp_f_onBeforeStoreSubmissions', array(array('formId'=>$formId,'post'=>&$post,'SubmissionId'=>$SubmissionId)));

			// Add to db (values)
			foreach ($post as $key => $val)
			{
				$val = is_array($val) ? implode("\n", $val) : $val;
				$val = RSFormProHelper::stripJava($val);

                $object = (object) array(
                    'SubmissionId'  => $SubmissionId,
                    'FormId'        => $formId,
                    'FieldName'     => $key,
                    'FieldValue'    => $val
                );
                $db->insertObject('#__rsform_submission_values', $object);
			}

			//Trigger Event - onAfterStoreSubmissions
			$mainframe->triggerEvent('rsfp_f_onAfterStoreSubmissions', array(array('SubmissionId'=>$SubmissionId, 'formId'=>$formId)));

			// Send emails
			list($replace, $with) = RSFormProHelper::sendSubmissionEmails($SubmissionId);

			// RSForm! Pro Scripting - Thank You Message
			// performance check
			if (strpos($form->Thankyou, '{/if}') !== false) {
				require_once dirname(__FILE__).'/scripting.php';
				RSFormProScripting::compile($form->Thankyou, $replace, $with);
			}

			// Thank You Message
			$thankYouMessage = str_replace($replace, $with, $form->Thankyou);
			$form->ReturnUrl = str_replace($replace, $with, $form->ReturnUrl);

			// Set redirect link
			$u = RSFormProHelper::getURL();

			// Create the Continue button
			$continueButton = '';
			if ($form->ShowContinue)
			{
				// Create goto link
				$goto = 'document.location.reload();';

				// Cache workaround #1
				if ($cache_enabled)
					$goto = "document.location='".addslashes($u)."';";

				if (!empty($form->ReturnUrl))
					$goto = "document.location='".addslashes($form->ReturnUrl)."';";

				// Continue button
				$continueButtonLabel = JText::_('RSFP_THANKYOU_BUTTON');
                $continueButton = '<input type="button" class="rsform-submit-button btn btn-primary" name="continue" value="'.JText::_('RSFP_THANKYOU_BUTTON').'" onclick="'.$goto.'"/>';
				if (strpos($continueButtonLabel, 'input')) {
					$continueButton = JText::sprintf('RSFP_THANKYOU_BUTTON',$goto);
				} else {
                    $layoutName = $form->FormLayoutName;
                    if (file_exists(dirname(__FILE__).'/formlayouts/'.$layoutName.'.php')) {
                        require_once dirname(__FILE__).'/formlayouts/'.$layoutName.'.php';

                        $class = 'RSFormProFormLayout'.$layoutName;
                        if (class_exists($class)) {
                            $layout = new $class();

                            $loadLayoutFramework = $form->LoadFormLayoutFramework;

                            if (is_callable(array($layout, 'generateButton')))
                            {
                                $continueButton = $layout->generateButton($goto);
                            }
                        }
                    }
				}
			}

			// get mappings data
			$query = $db->getQuery(true)
                ->select('*')
                ->from($db->qn('#__rsform_mappings'))
                ->where($db->qn('formId') . ' = ' . $db->q($formId))
                ->order($db->qn('ordering') . ' ' . $db->escape('asc'));
			$mappings = $db->setQuery($query)->loadObjectList();

			// get Post to another location
            $query = $db->getQuery(true)
                ->select('*')
                ->from($db->qn('#__rsform_posts'))
                ->where($db->qn('form_id') . ' = ' . $db->q($formId))
                ->where($db->qn('enabled') . ' = ' . $db->q(1));
			$silentPost = $db->setQuery($query)->loadObject();

			if ($silentPost && !empty($silentPost->fields)) {
				$silentPost->fields = json_decode($silentPost->fields);
				if (!is_array($silentPost->fields)) {
					$silentPost->fields = array();
				}
			}

			eval($form->ScriptProcess2);

			if ($form->ScrollToThankYou)
			{
				$scrollToElement = '<div id="rsfp-thankyou-scroll' . $formId . '"></div>';
				$thankYouMessage = $scrollToElement . $thankYouMessage . $continueButton;
			}
			else if ($form->ThankYouMessagePopUp && !$form->ScrollToThankYou)
			{
				// Create goto link
				$gotoLink = '';

				if ($form->ShowContinue)
				{
					// Cache workaround #1
					if ($cache_enabled)
						$gotoLink =  addslashes($u);

					if (!empty($form->ReturnUrl))
						$gotoLink = addslashes($form->ReturnUrl);
				}
				$gotoLink = '<input type="hidden" id="rsfp-thankyou-popup-return-link" value="'.$gotoLink.'"/>';

				$thankYouMessage = '<div id="rsfp-thankyou-popup-container'.$formId.'">'.$thankYouMessage.$continueButton.$gotoLink.'</div>';
			}
			else
			{
				$thankYouMessage .= $continueButton;
			}

			//Mappings
			if (!empty($mappings))
			{
				$lastinsertid = '';
				$replacewith = $with;
				array_walk($replacewith, array('RSFormProHelper', 'escapeSql'));

				foreach ($mappings as $mapping)
				{
					try {
						//get the query
						$query = RSFormProHelper::getMappingQuery($mapping);

						//replace the placeholders
						$query = str_replace($replace, $replacewith, $query);

						//replace the last insertid placeholder
						$query = str_replace('{last_insert_id}', $lastinsertid, $query);

						if ($mapping->connection) {
							$options = array(
								'driver' => isset($mapping->driver) ? $mapping->driver : 'mysql',
								'host' => $mapping->host,
								'port' => $mapping->port,
								'user' => $mapping->username,
								'password' => $mapping->password,
								'database' => $mapping->database
							);

							$database = JDatabaseDriver::getInstance($options);

							//is a valid database connection
							if (is_a($database, 'JException')) continue;

							$database->setQuery($query);
							$database->execute();
							$lastinsertid = $database->insertid();

						} else {
							$db->setQuery($query);
							$db->execute();
							$lastinsertid = $db->insertid();
						}

					} catch (Exception $e) {
						$mainframe->enqueueMessage($e->getMessage(), 'warning');
					}
				}
			}

			if (!$form->Keepdata)
			{
			    require_once JPATH_ADMINISTRATOR . '/components/com_rsform/helpers/submissions.php';

			    RSFormProSubmissionsHelper::deleteSubmissions($SubmissionId);
			}

			if (!$form->KeepIP)
			{
			    $query = $db->getQuery(true)
                    ->update($db->qn('#__rsform_submissions'))
                    ->set($db->qn('UserIp') . ' = ' . $db->q('0.0.0.0'))
                    ->where($db->qn('SubmissionId') . ' = ' . $db->q($SubmissionId));
			    $db->setQuery($query)->execute();
			}

			if ($silentPost && !empty($silentPost->url) && $silentPost->url != 'http://')
			{
				// Set URL to send data to
				$url = $silentPost->url;

				// Prepare data
				if (!empty($silentPost->fields)) {
					$data = '';
					foreach ($silentPost->fields as $field) {
						$field->name  = str_replace($replace, $with, $field->name);
						$field->value = str_replace($replace, $with, $field->value);

						if (strlen($field->name)) {
							$data .= urlencode($field->name).'='.urlencode($field->value).'&';
						}
					}

					$data = rtrim($data, '&');
				} else {
					$data = http_build_query($post);
				}

				try {
					// Do we need to send data silently?
					if ($silentPost->silent) {
						// Get HTTP connector
						$http = JHttpFactory::getHttp();

						if ($silentPost->method) {
							// POST
							$http->post($url, $data);
						} else {
							// GET
							$http->get($url.(strpos($url, '?') === false ? '?' : '&').$data);
						}
					} else {
						// Try to follow the URL
						if ($silentPost->method) {
							@ob_end_clean();

							$dataArray = explode('&', $data);
							// Create a hidden form that we submit through Javascript
							?>
							<form id="formSubmit" method="post" action="<?php echo RSFormProHelper::htmlEscape($url); ?>">
								<?php
								if (!empty($dataArray) && is_array($dataArray)) {
									foreach ($dataArray as $value) {
										list($key, $value) = explode('=', $value, 2);
										?>
										<input type="hidden" name="<?php echo RSFormProHelper::htmlEscape(urldecode($key)); ?>" value="<?php echo RSFormProHelper::htmlEscape(urldecode($value)); ?>" />
										<?php
									}
								}
								?>
							</form>
							<script type="text/javascript">
								function formSubmit() {
									if (typeof document.getElementById("formSubmit").submit == "function") {
										document.getElementById("formSubmit").submit()
									} else {
										document.createElement("form").submit.call(document.getElementById("formSubmit"));
									}
								}

								try {
									window.addEventListener ? window.addEventListener("load",formSubmit,false) : window.attachEvent("onload",formSubmit);
								} catch (err) {
									formSubmit();
								}
							</script>
							<?php
							$mainframe->close();
						} else {
							$mainframe->redirect($url.(strpos($url, '?') === false ? '?' : '&').$data);
						}
					}
				} catch (Exception $e) {
					$mainframe->enqueueMessage($e->getMessage(), 'warning');
				}
			}

			// Cache workaround #2
			if ($cache_enabled)
			{
				$uniqid = uniqid('rsform');
				$u .= (strpos($u, '?') === false) ? '?skipcache='.$uniqid : '&skipcache='.$uniqid;
			}

			// Get session object
			$session = JFactory::getSession();

			// Populate data
			$formparams = (object) array(
                'loadLayoutFramework'   => !empty($loadLayoutFramework) ? 1 : 0,
				'submissionId' 		    => $SubmissionId,
				'redirectUrl'		    => !$form->ShowThankyou && $form->ReturnUrl ? $form->ReturnUrl : $u,
				'showSystemMessage'     => $form->ShowSystemMessage
			);

			// Store the Thank You Message if option is set
			if ($form->ShowThankyou) {
				$formparams->thankYouMessage = base64_encode($thankYouMessage);
			}

			// Store session data
			$session->set('com_rsform.formparams.formId'.$formId, $formparams);

			// Trigger - After form process
			$mainframe->triggerEvent('rsfp_f_onAfterFormProcess', array(array('SubmissionId' => $SubmissionId, 'formId' => $formId)));

			// If we didn't get redirected through a plugin, mark form as processed to display Thank You Message on next page load
			$formparams->formProcessed = true;

			// Store new session data
			$session->set('com_rsform.formparams.formId'.$formId, $formparams);

			if (!$form->ShowThankyou && $form->ReturnUrl)
			{
				$mainframe->redirect($form->ReturnUrl);
				return;
			}

			$mainframe->redirect($u);
		}

		return false;
	}

	public static function getComponents($formId) {
		static $components = array();

		if (!isset($components[$formId])) {
			$db = JFactory::getDbo();
			$query = $db->getQuery(true);

			// need to get the component type name so that we can load the specific class
			$query->clear()
				->select($db->qn('p.PropertyValue', 'name'))
				->select($db->qn('c.ComponentId'))
				->select($db->qn('c.ComponentTypeId'))
				->select($db->qn('ct.ComponentTypeName'))
				->select($db->qn('c.Order'))
				->from($db->qn('#__rsform_properties', 'p'))
				->join('LEFT', $db->qn('#__rsform_components', 'c').' ON ('.$db->qn('c.ComponentId').' = '.$db->qn('p.ComponentId').')')
				->join('LEFT', $db->qn('#__rsform_component_types', 'ct').' ON ('.$db->qn('ct.ComponentTypeId').' = '.$db->qn('c.ComponentTypeId').')')
				->where($db->qn('c.FormId') . ' = ' . $db->q($formId))
				->where($db->qn('p.PropertyName') . ' = ' . $db->q('NAME'))
				->where($db->qn('c.Published') . ' = ' . $db->q('1'))
				->order($db->qn('c.Order') . ' ASC');
			$db->setQuery($query);
			$components[$formId] =  $db->loadObjectList();
		}

		return $components[$formId];
	}

	public static function getURL()
	{
		$uri = JUri::getInstance();
		return $uri->toString(array('scheme', 'user', 'pass', 'host', 'port', 'path', 'query', 'fragment'));
	}

	public static function verifyChecked($componentName, $value, $post)
	{
		if (isset($post[$componentName]))
		{
			if (is_array($post[$componentName]) && in_array($value, $post[$componentName]))
				return 1;

			if (!is_array($post[$componentName]) && $post[$componentName] == $value)
				return 1;

			return 0;
		}

		return 0;
	}

	public static function validateForm($formId, $validationType='form', $SubmissionId=0)
	{
		$db 	 	= JFactory::getDbo();
		$invalid 	= array();
		$formId  	= (int) $formId;
        $post       = JFactory::getApplication()->input->post->get('form', array(), 'array');

		if ($validationType == 'form')
        {
            $form = RSFormProHelper::getForm($formId);

            $_POST['form'] = $post;

            eval($form->ScriptBeforeValidation);

            $post = $_POST['form'];

            JFactory::getApplication()->input->post->set('form', $post);
        }

		$query = $db->getQuery(true);
		$query->select($db->qn('c.ComponentId'))
			->select($db->qn('c.ComponentTypeId'))
			->from($db->qn('#__rsform_components', 'c'))
			->where($db->qn('FormId').'='.$db->q($formId))
			->where($db->qn('Published').'='.$db->q(1))
			->order($db->qn('Order').' '.$db->escape('asc'));

		// if $type is directory, we need to validate the fields that are editable in the directory
		if ($validationType == 'directory') {
			$subquery = $db->getQuery(true);
			$subquery->select($db->qn('componentId'))
				->from($db->qn('#__rsform_directory_fields'))
				->where($db->qn('formId').'='.$db->q($formId))
				->where($db->qn('editable').'='.$db->q(1));
			$query->where($db->qn('ComponentId').' IN ('.(string) $subquery.')');
		}

		$db->setQuery($query);

		if ($components = $db->loadObjectList('ComponentId')) {
			$componentIds = array_keys($components);
			// load properties
			$all_data = RSFormProHelper::getComponentProperties($componentIds);
			if (empty($all_data)) {
				return $invalid;
			}

			// load conditions
			if ($conditions = RSFormProHelper::getConditions($formId)) {
				foreach ($conditions as $condition) {
					if ($condition->details) {
						$condition_vars = array();
						foreach ($condition->details as $detail) {
							$isChecked 		  = RSFormProHelper::verifyChecked($detail->ComponentName, $detail->value, $post);
							$condition_vars[] = $detail->operator == 'is' ? $isChecked : !$isChecked;
						}
						// this check is performed like this
						// 'all' must be true (ie. no 0s in the array); 'any' can be true (ie. one value of 1 in the array will do)
						$result = $condition->condition == 'all' ? !in_array(0, $condition_vars) : in_array(1, $condition_vars);

						// if the item is hidden, no need to validate it
						if (($condition->action == 'show' && !$result) || ($condition->action == 'hide' && $result)) {
							foreach ($components as $i => $component) {
								if ($component->ComponentId == $condition->component_id) {
									// ... just remove it from the components array
									unset($components[$i]);
									break;
								}
							}
						}
					}
				}
			}

			// load validation rules
			$validations 	 = array_flip(RSFormProHelper::getValidationRules(true));
			$dateValidations = array_flip(RSFormProHelper::getDateValidationRules(true));

			$validationClass 		= RSFormProHelper::getValidationClass();
			$dateValidationClass 	= RSFormProHelper::getDateValidationClass();

			// validate through components
			foreach ($components as $component) {
				$data 			= $all_data[$component->ComponentId];
				$required 		= !empty($data['REQUIRED']) && $data['REQUIRED'] == 'YES';
				$validationRule = !empty($data['VALIDATIONRULE']) ? $data['VALIDATIONRULE'] : '';
				$typeId 		= $component->ComponentTypeId;

				// birthDay field
				if ($typeId == RSFORM_FIELD_BIRTHDAY) {
					// flag to check if we need to run the validation functions
					$runValidations = false;

					if ($validationType == 'directory') {
						// Split the field...
						$dateParts = explode($data['DATESEPARATOR'], $post[$data['NAME']]);

						if ($data['SHOWDAY'] != 'YES') {
							$data['DATEORDERING'] = str_replace('D', '', $data['DATEORDERING']);
						}
						if ($data['SHOWMONTH'] != 'YES') {
							$data['DATEORDERING'] = str_replace('M', '', $data['DATEORDERING']);
						}
						if ($data['SHOWYEAR'] != 'YES') {
							$data['DATEORDERING'] = str_replace('Y', '', $data['DATEORDERING']);
						}

						$day   = strpos($data['DATEORDERING'], 'D');
						$month = strpos($data['DATEORDERING'], 'M');
						$year  = strpos($data['DATEORDERING'], 'Y');

						$post[$data['NAME']] = array();

						if ($data['SHOWDAY'] == 'YES' && isset($dateParts[$day])) {
							$post[$data['NAME']]['d'] = $dateParts[$day];
						}

						if ($data['SHOWMONTH'] == 'YES' && isset($dateParts[$month])) {
							$post[$data['NAME']]['m'] = $dateParts[$month];
						}
						if ($data['SHOWYEAR'] == 'YES' && isset($dateParts[$year])) {
							$post[$data['NAME']]['y'] = $dateParts[$year];
						}
					}

					if ($required) {
						// we need all of the fields to be selected
						if ($data['SHOWDAY'] == 'YES' && empty($post[$data['NAME']]['d']) ||
							$data['SHOWMONTH'] == 'YES' && empty($post[$data['NAME']]['m']) ||
							$data['SHOWYEAR'] == 'YES' && empty($post[$data['NAME']]['y'])) {
							$invalid[] = $data['componentId'];
							continue;
						}

						$runValidations = true;
					} else {
						// the field is not required, but if a selection is made it needs to be valid
						$selections = array();
						if ($data['SHOWDAY'] == 'YES') {
							$selections[] = !empty($post[$data['NAME']]['d']) ? $post[$data['NAME']]['d'] : '';
						}
						if ($data['SHOWMONTH'] == 'YES') {
							$selections[] = !empty($post[$data['NAME']]['m']) ? $post[$data['NAME']]['m'] : '';
						}
						if ($data['SHOWYEAR'] == 'YES') {
							$selections[] = !empty($post[$data['NAME']]['y']) ? $post[$data['NAME']]['y'] : '';
						}
						$foundEmpty = false;
						$foundValue = false;
						foreach ($selections as $selection) {
							if ($selection == '') {
								$foundEmpty = true;
							} else {
								$foundValue = true;
							}
						}
						// at least 1 value has been selected but we've found empty values as well, make sure the selection is valid first!
						if ($foundEmpty && $foundValue) {
							$invalid[] = $data['componentId'];
							continue;
						} elseif ($foundValue && !$foundEmpty) {
							$runValidations = true;
						}
					}

					// we have all the info we need, validations only work when all fields are selected
					if ($runValidations && $data['SHOWDAY'] == 'YES' && $data['SHOWMONTH'] == 'YES' && $data['SHOWYEAR'] == 'YES') {
						$validationRule = !empty($data['VALIDATIONRULE_DATE']) ? $data['VALIDATIONRULE_DATE'] : '';

						$day   = $post[$data['NAME']]['d'];
						$month = $post[$data['NAME']]['m'];
						$year  = $post[$data['NAME']]['y'];

						// start checking validation rules
						if (isset($dateValidations[$validationRule]) && !call_user_func(array($dateValidationClass, $validationRule), $day, $month, $year, $data)) {
							$invalid[] = $data['componentId'];
							continue;
						}
					}

					// no need to process further
					continue;
				}

				// CAPTCHA
				if ($typeId == RSFORM_FIELD_CAPTCHA) {
					if (JFactory::getUser()->id) {
						$form = RSFormProHelper::getForm($formId);
						if ($form->RemoveCaptchaLogged) {
							continue;
						}
					}
					$session = JFactory::getSession();
					$captchaCode = $session->get('com_rsform.captcha.captchaId'.$component->ComponentId);
					if ($data['IMAGETYPE'] == 'INVISIBLE')
					{
					    if (empty($captchaCode))
                        {
                            $invalid[] = $data['componentId'];
                        }

						if (JFactory::getApplication()->input->post->get($captchaCode, '', 'raw'))
                        {
                            $invalid[] = $data['componentId'];
                        }

                        $words = RSFormProHelper::getInvisibleCaptchaWords();
						foreach ($words as $word)
                        {
                            if (JFactory::getApplication()->input->post->get($word, '', 'raw'))
                            {
                                $invalid[] = $data['componentId'];
                            }
                        }
					}
					else
					{
						if (empty($post[$data['NAME']]) || empty($captchaCode) || $post[$data['NAME']] != $captchaCode)
							$invalid[] = $data['componentId'];
					}

					// no sense continuing
					continue;
				}

				// Upload field
				if ($typeId == RSFORM_FIELD_FILEUPLOAD) {
					$originalUpload = false;
					if ($validationType == 'directory' && $SubmissionId) {
						$db->setQuery("SELECT FieldValue FROM #__rsform_submission_values WHERE FieldName='".$db->escape($data['NAME'])."' AND SubmissionId='".(int) $SubmissionId."' LIMIT 1");
						$originalUpload = $db->loadResult();
					}

					if ($files = JFactory::getApplication()->input->files->get('form', null, 'raw')) {
						if (!empty($files[$data['NAME']])) {
							$name 		= $files[$data['NAME']]['name'];
							$error 		= $files[$data['NAME']]['error'];
							$size		= $files[$data['NAME']]['size'];

							// File has not been sent but it's required
							if ($error == UPLOAD_ERR_NO_FILE && $required && !$originalUpload) {
								$invalid[] = $data['componentId'];
								continue;
							}

							// File has been uploaded correctly to the server
							if ($error == UPLOAD_ERR_OK) {
								// Let's check if the extension is allowed
								$extParts 		= explode('.', $name);
								$ext 			= strtolower(end($extParts));
								$acceptedExts   = false;
                                if (!empty($data['ACCEPTEDFILESIMAGES']) && $data['ACCEPTEDFILESIMAGES'] == 'YES')
                                {
                                    $acceptedExts = array('jpg', 'jpeg', 'png', 'gif');
                                }
                                elseif (!empty($data['ACCEPTEDFILES']))
                                {
                                    $acceptedExts = self::explode($data['ACCEPTEDFILES']);
                                }

								// Let's check only if accepted extensions are set
								if ($acceptedExts) {
									$accepted = false;
									foreach ($acceptedExts as $acceptedExt) {
										$acceptedExt = trim(strtolower($acceptedExt));
										if (strlen($acceptedExt) && $acceptedExt == $ext) {
											$accepted = true;
											break;
										}
									}
									if (!$accepted) {
										$invalid[] = $data['componentId'];
										continue;
									}
								}

								// Let's check if it's the correct size
								if ($size > 0 && $data['FILESIZE'] > 0 && $size > $data['FILESIZE']*1024) {
									$invalid[] = $data['componentId'];
									continue;
								}
							} elseif ($error != UPLOAD_ERR_NO_FILE) {
								// Parse the error message
								switch ($error) {
									default:
										// File has not been uploaded correctly
										$msg = JText::_('RSFP_FILE_HAS_NOT_BEEN_UPLOADED_DUE_TO_AN_UNKNOWN_ERROR');
										break;

									case UPLOAD_ERR_INI_SIZE:
										$msg = JText::_('RSFP_UPLOAD_ERR_INI_SIZE');
										break;

									case UPLOAD_ERR_FORM_SIZE:
										$msg = JText::_('RSFP_UPLOAD_ERR_FORM_SIZE');
										break;

									case UPLOAD_ERR_PARTIAL:
										$msg = JText::_('RSFP_UPLOAD_ERR_PARTIAL');
										break;

									case UPLOAD_ERR_NO_TMP_DIR:
										$msg = JText::_('RSFP_UPLOAD_ERR_NO_TMP_DIR');
										break;

									case UPLOAD_ERR_CANT_WRITE:
										$msg = JText::_('RSFP_UPLOAD_ERR_CANT_WRITE');
										break;

									case UPLOAD_ERR_EXTENSION:
										$msg = JText::_('RSFP_UPLOAD_ERR_EXTENSION');
										break;
								}

								// Show the warning
								JFactory::getApplication()->enqueueMessage($msg, 'warning');

								$invalid[] = $data['componentId'];
								continue;
							}
						}
					}

					// Files have been handled, no need to continue
					continue;
				}

				// flag to check if we need to run the validation functions
				$runValidations = false;

                // Must have a value if it's required
				if ($required)
				{
					// Field is missing from request
					if (!isset($post[$data['NAME']]))
					{
						$invalid[] = $data['componentId'];
						continue;
					}

					// For convenience
                    $value = $post[$data['NAME']];

					// If it's an array, implode it
                    if (is_array($value))
                    {
                        $value = implode('', $value);
                    }

                    // Remove unwanted spaces
                    $value = trim($value);

                    // Field has no length
                    if (!strlen($value))
                    {
                        $invalid[] = $data['componentId'];
                        continue;
                    }

                    // Range Slider must have a value > 0
                    if ($typeId == RSFORM_FIELD_RANGE_SLIDER && empty($value))
                    {
                        $invalid[] = $data['componentId'];
                        continue;
                    }

                    $runValidations = true;
				} else { // not required, perform checks only when something is selected
					// we have a value, make sure it's the correct one
					if (isset($post[$data['NAME']]) && !is_array($post[$data['NAME']]) && strlen(trim($post[$data['NAME']]))) {
						$runValidations = true;
					}
				}
				
				// check the calendar field if is required
				if (($typeId == RSFORM_FIELD_CALENDAR || $typeId == RSFORM_FIELD_JQUERY_CALENDAR) && strlen(trim($post[$data['NAME']]))) {
				    if (!isset($data['VALIDATIONDATE']) || $data['VALIDATIONDATE'] == 'YES') {
                        $selectedDate = $post[$data['NAME']];

                        if (JFactory::getLanguage()->getTag() != 'en-GB') {
                            require_once JPATH_ADMINISTRATOR . '/components/com_rsform/helpers/calendar.php';

                            $selectedDate = RSFormProCalendar::fixValue($selectedDate, $data['DATEFORMAT']);
                        }

                        $validDate = JFactory::getDate()->createFromFormat($data['DATEFORMAT'], $selectedDate);

                        if ($validDate) {
                            $validDate = $validDate->format($data['DATEFORMAT']);
                        }

                        if ($validDate != $selectedDate) {
                            $invalid[] = $data['componentId'];
                            continue;
                        }
                    }
				}

				if ($runValidations && isset($validations[$validationRule]) && !call_user_func(array($validationClass, $validationRule), $post[$data['NAME']], isset($data['VALIDATIONEXTRA']) ? $data['VALIDATIONEXTRA'] : '', $data)) {
					$invalid[] = $data['componentId'];
					continue;
				}
			}
		}
		return $invalid;
	}

	public static function addClass(&$attributes, $className)
	{
		if (preg_match('#class="(.*?)"#is', $attributes, $matches))
			$attributes = str_replace($matches[0], str_replace($matches[1], $matches[1].' '.$className, $matches[0]), $attributes);
		else
			$attributes .= ' class="'.$className.'"';

		return $attributes;
	}

	public static function addOnClick(&$attributes, $onClick)
	{
		if (preg_match('#onclick="(.*?)"#is', $attributes, $matches))
			$attributes = str_replace($matches[0], str_replace($matches[1], $matches[1].'; '.$onClick, $matches[0]), $attributes);
		else
			$attributes .= ' onclick="'.$onClick.'"';

		return $attributes;
	}

	public static function getInvisibleCaptchaWords()
	{
		return array('Website', 'Email', 'Name', 'Address', 'User', 'Username', 'Comment', 'Message');
	}

	public static function stripJava($val) {
		$filtering = RSFormProHelper::getConfig('global.filtering');

		switch ($filtering)
		{
			default:
			case 'joomla':
				static $filter;
				if (is_null($filter)) {
					jimport('joomla.filter.filterinput');
					$filter = JFilterInput::getInstance(array('form', 'input', 'select', 'textarea'), array('style'), 1, 1);
				}

				$val = preg_replace('/([\x00-\x08][\x0b-\x0c][\x0e-\x20])/', "", $val);
				$val = str_replace("\0", "", $val);

				return $filter->clean($val);
				break;

			case 'rsform':
				// remove all non-printable characters. CR(0a) and LF(0b) and TAB(9) are allowed
				// this prevents some character re-spacing such as <java\0script>
				// note that you have to handle splits with \n, \r, and \t later since they *are* allowed in some inputs
				$val = preg_replace('/([\x00-\x08][\x0b-\x0c][\x0e-\x20])/', '', $val);

				// straight replacements, the user should never need these since they're normal characters
				// this prevents like <IMG SRC=&#X40&#X61&#X76&#X61&#X73&#X63&#X72&#X69&#X70&#X74&#X3A&#X61&#X6C&#X65&#X72&#X74&#X28&#X27&#X58&#X53&#X53&#X27&#X29>
				$search = 'abcdefghijklmnopqrstuvwxyz';
				$search .= 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
				$search .= '1234567890!@#$%^&*()';
				$search .= '~`";:?+/={}[]-_|\'\\';
				for ($i = 0; $i < strlen($search); $i++) {
					// ;? matches the ;, which is optional
					// 0{0,7} matches any padded zeros, which are optional and go up to 8 chars

					// &#x0040 @ search for the hex values
					$val = preg_replace('/(&#[x|X]0{0,8}'.dechex(ord($search[$i])).';?)/i', $search[$i], $val); // with a ;
					// &#00064 @ 0{0,7} matches '0' zero to seven times
					$val = preg_replace('/(&#0{0,8}'.ord($search[$i]).';?)/', $search[$i], $val); // with a ;
				}

				// now the only remaining whitespace attacks are \t, \n, and \r
				// ([ \t\r\n]+)?
				$ra1 = Array('\/([ \t\r\n]+)?javascript', '\/([ \t\r\n]+)?vbscript', ':([ \t\r\n]+)?expression', '<([ \t\r\n]+)?applet', '<([ \t\r\n]+)?meta', '<([ \t\r\n]+)?xml', '<([ \t\r\n]+)?blink', '<([ \t\r\n]+)?link', '<([ \t\r\n]+)?style', '<([ \t\r\n]+)?script', '<([ \t\r\n]+)?embed', '<([ \t\r\n]+)?object', '<([ \t\r\n]+)?iframe', '<([ \t\r\n]+)?frame', '<([ \t\r\n]+)?frameset', '<([ \t\r\n]+)?ilayer', '<([ \t\r\n]+)?layer', '<([ \t\r\n]+)?bgsound', '<([ \t\r\n]+)?title', '<([ \t\r\n]+)?base');
				$ra2 = Array('onabort([ \t\r\n]+)?=', 'onactivate([ \t\r\n]+)?=', 'onafterprint([ \t\r\n]+)?=', 'onafterupdate([ \t\r\n]+)?=', 'onbeforeactivate([ \t\r\n]+)?=', 'onbeforecopy([ \t\r\n]+)?=', 'onbeforecut([ \t\r\n]+)?=', 'onbeforedeactivate([ \t\r\n]+)?=', 'onbeforeeditfocus([ \t\r\n]+)?=', 'onbeforepaste([ \t\r\n]+)?=', 'onbeforeprint([ \t\r\n]+)?=', 'onbeforeunload([ \t\r\n]+)?=', 'onbeforeupdate([ \t\r\n]+)?=', 'onblur([ \t\r\n]+)?=', 'onbounce([ \t\r\n]+)?=', 'oncellchange([ \t\r\n]+)?=', 'onchange([ \t\r\n]+)?=', 'onclick([ \t\r\n]+)?=', 'oncontextmenu([ \t\r\n]+)?=', 'oncontrolselect([ \t\r\n]+)?=', 'oncopy([ \t\r\n]+)?=', 'oncut([ \t\r\n]+)?=', 'ondataavailable([ \t\r\n]+)?=', 'ondatasetchanged([ \t\r\n]+)?=', 'ondatasetcomplete([ \t\r\n]+)?=', 'ondblclick([ \t\r\n]+)?=', 'ondeactivate([ \t\r\n]+)?=', 'ondrag([ \t\r\n]+)?=', 'ondragend([ \t\r\n]+)?=', 'ondragenter([ \t\r\n]+)?=', 'ondragleave([ \t\r\n]+)?=', 'ondragover([ \t\r\n]+)?=', 'ondragstart([ \t\r\n]+)?=', 'ondrop([ \t\r\n]+)?=', 'onerror([ \t\r\n]+)?=', 'onerrorupdate([ \t\r\n]+)?=', 'onfilterchange([ \t\r\n]+)?=', 'onfinish([ \t\r\n]+)?=', 'onfocus([ \t\r\n]+)?=', 'onfocusin([ \t\r\n]+)?=', 'onfocusout([ \t\r\n]+)?=', 'onhelp([ \t\r\n]+)?=', 'onkeydown([ \t\r\n]+)?=', 'onkeypress([ \t\r\n]+)?=', 'onkeyup([ \t\r\n]+)?=', 'onlayoutcomplete([ \t\r\n]+)?=', 'onload([ \t\r\n]+)?=', 'onlosecapture([ \t\r\n]+)?=', 'onmousedown([ \t\r\n]+)?=', 'onmouseenter([ \t\r\n]+)?=', 'onmouseleave([ \t\r\n]+)?=', 'onmousemove([ \t\r\n]+)?=', 'onmouseout([ \t\r\n]+)?=', 'onmouseover([ \t\r\n]+)?=', 'onmouseup([ \t\r\n]+)?=', 'onmousewheel([ \t\r\n]+)?=', 'onmove([ \t\r\n]+)?=', 'onmoveend([ \t\r\n]+)?=', 'onmovestart([ \t\r\n]+)?=', 'onpaste([ \t\r\n]+)?=', 'onpropertychange([ \t\r\n]+)?=', 'onreadystatechange([ \t\r\n]+)?=', 'onreset([ \t\r\n]+)?=', 'onresize([ \t\r\n]+)?=', 'onresizeend([ \t\r\n]+)?=', 'onresizestart([ \t\r\n]+)?=', 'onrowenter([ \t\r\n]+)?=', 'onrowexit([ \t\r\n]+)?=', 'onrowsdelete([ \t\r\n]+)?=', 'onrowsinserted([ \t\r\n]+)?=', 'onscroll([ \t\r\n]+)?=', 'onselect([ \t\r\n]+)?=', 'onselectionchange([ \t\r\n]+)?=', 'onselectstart([ \t\r\n]+)?=', 'onstart([ \t\r\n]+)?=', 'onstop([ \t\r\n]+)?=', 'onsubmit([ \t\r\n]+)?=', 'onunload([ \t\r\n]+)?=', 'style([ \t\r\n]+)?=');
				$ra = array_merge($ra1, $ra2);

				foreach ($ra as $tag)
				{
					$pattern = '#'.$tag.'#i';
					preg_match_all($pattern, $val, $matches);

					foreach ($matches[0] as $match)
						$val = str_replace($match, substr($match, 0, 2).'<x>'.substr($match, 2), $val);
				}

				return $val;
				break;

			case 'none':
				return $val;
				break;
		}
	}

	public static function getTranslations($reference, $formId, $lang, $select = 'value')
	{
		static $selections = array();
		static $langs = array();

		$formId = (int) $formId;
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);

		// Do not grab translations if the form is in the same language as the translation.
		if (!isset($langs[$formId])) {

			$query->clear()
				->select($db->qn('Lang'))
				->from('#__rsform_forms')
				->where($db->qn('FormId').' = '.$db->q($formId));
			$db->setQuery($query);
			$langs[$formId] = $db->loadResult();
		}

		if ($langs[$formId] == $lang || RSFormProHelper::getConfig('global.disable_multilanguage'))
			return false;
		// build the reference hash
		$hash = md5($reference.$formId.$lang.$select);

		if (!isset($selections[$hash])) {
			$acceptedReferences = array('forms', 'emails', 'properties');

			if (in_array($reference, $acceptedReferences)) {
				$selections[$hash] = array();
				$lang_code = $db->escape($lang);
				// build the proper SQL Query
				$query->clear()
					->select('*')
					->from('#__rsform_translations')
					->where($db->qn('form_id').' = '.$db->q($formId))
					->where($db->qn('lang_code').' = '.$db->q($lang_code))
					->where($db->qn('reference').' = '.$db->q($reference));
				$db->setQuery($query);

				$results = $db->loadObjectList();
				foreach ($results as $result) {
					$selections[$hash][$result->reference_id] = ($select == '*') ? $result : (isset($result->$select) ? $result->$select : false);
				}
			} else {
				$selections[$hash] = false;
			}
		}

		return $selections[$hash];
	}

	public static function getTranslatableProperties()
	{
	    static $translatable;

	    if (!$translatable)
        {
            $translatable = array('LABEL', 'RESETLABEL', 'PREVBUTTON', 'NEXTBUTTON', 'CAPTION', 'DESCRIPTION', 'VALIDATIONMESSAGE', 'DEFAULTVALUE', 'ITEMS', 'TEXT', 'REFRESHTEXT', 'DISPLAYPROGRESSMSG', 'WIRE', 'SHOWDAYPLEASE', 'SHOWMONTHPLEASE', 'SHOWYEARPLEASE', 'POPUPLABEL', 'PLACEHOLDER');

            JFactory::getApplication()->triggerEvent('rsfp_bk_onGetTranslatableProperties', array(&$translatable));
        }

        return $translatable;
	}

	public static function translateIcon()
	{
        return RSFormProHelper::getConfig('global.disable_multilanguage') ? '' : '<span class="rsficon rsficon-flag" title="' . JText::_('RSFP_THIS_ITEM_IS_TRANSLATABLE') . '" style="color:#3071a9"></span>';
	}

	public static function mappingsColumns($config, $method, $row = null)
	{
		require_once dirname(__FILE__).'/mappings.php';

		return RSFormProMappings::mappingsColumns($config, $method, $row);
	}

	public static function getMappingQuery($row)
	{
		require_once dirname(__FILE__).'/mappings.php';

		return RSFormProMappings::getMappingQuery($row);
	}

	public static function escapeSql(&$value)
	{
		static $db;
		if (!$db) {
			$db = JFactory::getDbo();
		}

		$value = $db->escape($value);
	}

	public static function sendMail($from, $fromname, $recipient, $subject, $body, $mode=0, $cc=null, $bcc=null, $attachment=null, $replyto=null, $replytoname=null)
	{
		try {
			// Get a JMail instance
			$mail 		= JFactory::getMailer();

			// Allow this to be overridden
			JFactory::getApplication()->triggerEvent('rsfp_onCreateMailer', array(array(
				'mailer' 		=> &$mail,
				'from' 			=> &$from,
				'fromname' 		=> &$fromname,
				'recipient' 	=> &$recipient,
				'subject' 		=> &$subject,
				'body' 			=> &$body,
				'mode' 			=> &$mode,
				'cc' 			=> &$cc,
				'bcc' 			=> &$bcc,
				'attachment' 	=> &$attachment,
				'replyto' 		=> &$replyto,
				'replytoname'	=> &$replytoname
			)));

			$mail->ClearReplyTos();
			$mail->setSender(array($from, $fromname));

			$mail->setSubject($subject);
			$mail->setBody($body);

			// Are we sending the email as HTML?
			if ($mode)
			{
				$mail->IsHTML(true);
				
				$textBody = str_ireplace(array('<p>', '<br>', '<br/>', '<br />'), "\n", $body);
				$mail->AltBody = strip_tags($textBody);
			}

			// Some cleanup
			if (is_array($recipient)) {
				foreach ($recipient as $i => $r) {
					if (empty($r)) {
						unset($recipient[$i]);
					}
				}
			}

			$mail->addRecipient($recipient);
			$mail->addCC($cc);
			$mail->addBCC($bcc);
			$mail->addAttachment($attachment);

			// Take care of reply email addresses
			if (!is_array($replyto)) {
				$replyto = explode(',', $replyto);
			}
			if (!is_array($replytoname)) {
				$replytoname = explode(',', $replytoname);
			}
			
			$replyto = array_filter($replyto);
			$replytoname = array_filter($replytoname);
			
			$mail->ClearReplyTos();
			$numReplyTo = count($replyto);
			for ($i = 0; $i < $numReplyTo; $i++) {
				$mail->addReplyTo(trim($replyto[$i]), isset($replytoname[$i]) ? trim($replytoname[$i]) : '');
			}

			return $mail->Send();
		} catch (Exception $e) {
			JFactory::getApplication()->enqueueMessage($e->getMessage(), 'warning');
			return false;
		}
	}

	public static function renderHTML() {
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
			$name	 	= htmlspecialchars($args[1], ENT_COMPAT, 'utf-8');
			$additional = isset($args[2]) ? (string) $args[2] : '';
			$value		= $args[3];
			$yes 	 	= isset($args[4]) ? htmlspecialchars($args[4], ENT_COMPAT, 'utf-8') : 'JYES';
			$no 	 	= isset($args[5]) ? htmlspecialchars($args[5], ENT_COMPAT, 'utf-8') : 'JNO';

			// prepare the xml
			$element = new SimpleXMLElement('<field name="'.$name.'" type="radio" class="btn-group"><option '.$additional.' value="0">'.$no.'</option><option '.$additional.' value="1">'.$yes.'</option></field>');

			// run
			$radio->setup($element, $value);

			return $radio->input;
		}
	}

	public static function getAllDirectoryFields($formId) {
		$db		= JFactory::getDbo();
		static $cache = array();

		if (!isset($cache[$formId])) {
		    $excludedFields = array(
                RSFORM_FIELD_BUTTON,
                RSFORM_FIELD_CAPTCHA,
                RSFORM_FIELD_SUBMITBUTTON,
                RSFORM_FIELD_PAGEBREAK
            );

            JFactory::getApplication()->triggerEvent('rsfp_bk_onGetDirectoryExcludedFields', array(&$excludedFields, $formId));

			$query = $db->getQuery(true);
			$query->select($db->qn('p.PropertyValue','FieldName'))
				->select($db->qn('p.ComponentId','FieldId'))
				->select($db->qn('c.ComponentTypeId','FieldType'))
				->from($db->qn('#__rsform_components','c'))
				->join('left', $db->qn('#__rsform_properties','p').' ON '.$db->qn('c.ComponentId').' = '.$db->qn('p.ComponentId'))
				->where($db->qn('c.FormId').'='.$db->q($formId))
				->where($db->qn('p.PropertyName').' = '.$db->q('NAME'))
				->where($db->qn('c.ComponentTypeId').' NOT IN (' . implode(',', $excludedFields) . ')')
				->where($db->qn('c.Published').'='.$db->q(1))
				->order($db->qn('c.Order').' '.$db->escape('asc'));
			$db->setQuery($query);
			$cache[$formId] = $db->loadObjectList('FieldId');

			$data = RSFormProHelper::getComponentProperties(array_keys($cache[$formId]));
			foreach ($cache[$formId] as $FieldId => $field) {
				$properties =& $data[$FieldId];
				$caption = isset($properties['CAPTION']) ? $properties['CAPTION'] : '';

				$cache[$formId][$FieldId]->FieldCaption = $caption;
			}

			// Add #__rsform_submissions headers.
			$headers = self::getDirectoryStaticHeaders();
			foreach ($headers as $index => $header) {
				$cache[$formId][$index] = (object) array(
					'FieldName' 	=> $header,
					'FieldId'		=> $index,
					'FieldType' 	=> 0,
					'FieldCaption' 	=> JText::_('RSFP_'.$header)
				);
			}

			JFactory::getApplication()->triggerEvent('rsfp_bk_onGetAllDirectoryFields', array(&$cache[$formId], $formId));
		}

		return $cache[$formId];
	}

	public static function getDirectoryStaticHeaders() {
		return array(
			-1 => 'DateSubmitted',
			-2 => 'UserIp',
			-3 => 'Username',
			-4 => 'UserId',
			-5 => 'Lang',
			-6 => 'confirmed'
		);
	}

	public static function getDirectoryFields($formId) {
		static $cache = array();

		if (!isset($cache[$formId])) {
			$db = JFactory::getDbo();
            $query = $db->getQuery(true)
                ->select('*')
                ->from($db->qn('#__rsform_directory_fields'))
                ->where($db->qn('formId') . ' = ' . $db->q($formId))
                ->order($db->qn('ordering') . ' ' . $db->escape('ASC'));
			$db->setQuery($query);
			$currentFields = $db->loadObjectList('componentId');

			$allFields = self::getAllDirectoryFields($formId);
			if ($diffFields = array_diff(array_keys($currentFields), array_keys($allFields))) {
				foreach ($diffFields as $fieldId) {
					unset($currentFields[$fieldId]);
				}
			}

			foreach ($allFields as $field) {
				// Hidden fields don't have a caption
				if (in_array($field->FieldType, array(RSFORM_FIELD_HIDDEN, RSFORM_FIELD_TICKET, RSFORM_FIELD_FREETEXT))) {
					$field->FieldCaption = $field->FieldName;
				}

				if (!isset($currentFields[$field->FieldId])) { // field has been added after, add it to the end of the list
					$currentFields[] = (object) array(
						'FieldId' 		=> $field->FieldId,
						'FieldName' 	=> $field->FieldName,
						'FieldCaption' 	=> $field->FieldCaption,
						'formId' 		=> $formId,
						'componentId' 	=> $field->FieldId,
						'viewable' 		=> 0,
						'searchable' 	=> 0,
						'editable' 		=> 0,
						'indetails' 	=> 0,
						'incsv' 		=> 0,
						'ordering' 		=> count($currentFields)+1,
						'allowEdit'     => true,
					);
				} else { // just set the name & id for reference
					$currentFields[$field->FieldId]->FieldId 		= $field->FieldId;
					$currentFields[$field->FieldId]->FieldName 		= $field->FieldName;
					$currentFields[$field->FieldId]->FieldCaption 	= $field->FieldCaption;
					$currentFields[$field->FieldId]->allowEdit      = true;
				}
			}

			// this is to reset the indexes (0, 1, 2, 3)
			$cache[$formId] = array_merge($currentFields, array());
		}

		return $cache[$formId];
	}

	public static function getDirectoryFormProperties($formId) {
		$db = JFactory::getDbo();

		// form multiple separator
		$db->setQuery("SELECT MultipleSeparator FROM #__rsform_forms WHERE FormId = '".(int) $formId."'");
		$multipleSeparator = str_replace(array('\n', '\r', '\t'), array("\n", "\r", "\t"), $db->loadResult());

		$uploadFields 	= array();
		$multipleFields = array();

		$db->setQuery("SELECT c.ComponentTypeId, p.ComponentId, p.PropertyValue AS FieldName FROM #__rsform_components c LEFT JOIN #__rsform_properties p ON (c.ComponentId=p.ComponentId) WHERE c.FormId='".(int) $formId."' AND c.Published='1' AND p.PropertyName='NAME'");
		$allFields = $db->loadObjectList();
		foreach ($allFields as $field) {
			if ($field->ComponentTypeId == RSFORM_FIELD_FILEUPLOAD) {
				$uploadFields[] = $field->FieldName;
			} elseif (in_array($field->ComponentTypeId, array(RSFORM_FIELD_SELECTLIST, RSFORM_FIELD_CHECKBOXGROUP))) {
				$multipleFields[] = $field->FieldName;
			}
		}

		$config = JFactory::getConfig();
		$secret = $config->get('secret');

		return array(
			$multipleSeparator,
			$uploadFields,
			$multipleFields,
			$secret
		);
	}

	public static function canEdit($formId, $SubmissionId)
    {
        $db = JFactory::getDbo();
        $user = JFactory::getUser();
        $canedit = false;
        $user_groups = JAccess::getGroupsByUser($user->get('id'));

        if ($groups = static::getDirectoryGroups($formId, 'edit')) {
            $registry = new JRegistry;
            $registry->loadString($groups);
            if ($groups = $registry->toArray()) {
                // Check if the user can edit its own submissions
                if (in_array('own', $groups)) {
                    $db->setQuery('SELECT ' . $db->qn('UserId') . ' FROM ' . $db->qn('#__rsform_submissions') . ' WHERE ' . $db->qn('SubmissionId') . ' = ' . $SubmissionId . ' ');
                    $UserId = $db->loadResult();
                    if ($UserId == $user->get('id') && !$user->get('guest')) {
                        $canedit = true;
                    }
                }

                // Check if the current user can edit submissions
                if ($user_groups) {
                    foreach ($user_groups as $user_group) {
                        if (in_array($user_group, $groups))
                            $canedit = true;
                    }
                }
            }
        }

        return $canedit;
    }

    public static function canDelete($formId, $SubmissionId)
    {
        $db         = JFactory::getDbo();
        $user       = JFactory::getUser();
        $canDelete  = false;
        $user_groups = JAccess::getGroupsByUser($user->get('id'));

        if ($groups = static::getDirectoryGroups($formId, 'delete')) {
            $registry = new JRegistry;
            $registry->loadString($groups);
            if ($groups = $registry->toArray()) {
                // Check if the user can edit its own submissions
                if (in_array('own', $groups)) {
                    $db->setQuery('SELECT ' . $db->qn('UserId') . ' FROM ' . $db->qn('#__rsform_submissions') . ' WHERE ' . $db->qn('SubmissionId') . ' = ' . $SubmissionId . ' ');
                    $UserId = $db->loadResult();
                    if ($UserId == $user->get('id') && !$user->get('guest')) {
                        $canDelete = true;
                    }
                }

                // Check if the current user can edit submissions
                if ($user_groups) {
                    foreach ($user_groups as $user_group) {
                        if (in_array($user_group, $groups))
                            $canDelete = true;
                    }
                }
            }
        }

        return $canDelete;
    }

    public static function getDirectoryGroups($formId, $type = 'edit')
    {
        static $cache = array();

        if (!isset($cache[$formId]))
        {
            $cache[$formId] = (object) array(
                'edit'   => null,
                'delete' => null
            );

            $db = JFactory::getDbo();
            $query = $db->getQuery(true)
                ->select($db->qn('groups'))
                ->select($db->qn('DeletionGroups'))
                ->from($db->qn('#__rsform_directory'))
                ->where($db->qn('formId') . ' = ' . $db->q($formId));

            if ($result = $db->setQuery($query)->loadObject())
            {
                $cache[$formId]->edit = $result->groups;
                $cache[$formId]->delete = $result->DeletionGroups;
            }
        }

        return $cache[$formId]->{$type};
    }

	public static function getEditFields($cid) {
		$db			= JFactory::getDbo();
		$return		= array();
		$values		= JFactory::getApplication()->input->get('form',array(),'array');

		jimport('joomla.filesystem.file');

		// Load submission
        require_once JPATH_ADMINISTRATOR . '/components/com_rsform/helpers/submissions.php';
        $submission = RSFormProSubmissionsHelper::getSubmission($cid);

        if (!$submission)
        {
            return $return;
        }

        $query = $db->getQuery(true)
            ->select($db->qn('MultipleSeparator'))
            ->select($db->qn('TextareaNewLines'))
            ->from($db->qn('#__rsform_forms'))
            ->where($db->qn('FormId') . ' = ' . $db->q($submission->FormId));
        $form = $db->setQuery($query)->loadObject();

        $form->MultipleSeparator = str_replace(array('\n', '\r', '\t'), array("\n", "\r", "\t"), $form->MultipleSeparator);

		$submission->DateSubmitted = JHtml::_('date', $submission->DateSubmitted, 'Y-m-d H:i:s');

		$validation		= !empty($values) ? RSFormProHelper::validateForm($submission->FormId, 'directory', $cid) : array();
		$headers        = self::getDirectoryStaticHeaders();
		$formFields 	= self::getDirectoryFields($submission->FormId);

		$query = $db->getQuery(true);
		$query->select($db->qn('ct.ComponentTypeName', 'type'))
			->select($db->qn('c.ComponentId'))
			->from($db->qn('#__rsform_components', 'c'))
			->join('left', $db->qn('#__rsform_component_types', 'ct').' ON ('.$db->qn('c.ComponentTypeId').'='.$db->qn('ct.ComponentTypeId').')')
			->where($db->qn('c.FormId').'='.$db->q($submission->FormId))
			->where($db->qn('c.Published').'='.$db->q(1));
		$componentTypes = $db->setQuery($query)->loadObjectList('ComponentId');

		$componentIds = array();
		foreach ($formFields as $formField) {
			if ($formField->FieldId > 0) {
				$componentIds[] = $formField->FieldId;
			}

			// Assign the type
			$formField->type = '';
			if ($formField->FieldId < 0 && isset($headers[$formField->FieldId])) {
				$formField->type = 'static';
			} elseif (isset($componentTypes[$formField->FieldId])) {
				$formField->type = $componentTypes[$formField->FieldId]->type;
			}

			// For convenience...
			$formField->id 		= $formField->FieldId;
			$formField->name 	= $formField->FieldName;
		}

		$properties	= RSFormProHelper::getComponentProperties($componentIds);

		foreach ($formFields as $field)
		{
			if (!$field->editable) {
				continue;
			}

			$invalid		= !empty($validation) && in_array($field->id,$validation) ? ' rsform-error' : '';
			$data			= $field->id > 0 ? $properties[$field->id] : array('NAME' => $field->name);
			$new_field		= array();
			$new_field[0]	= !empty($data['CAPTION']) ? $data['CAPTION'] : $field->name;
			$new_field[2]	= isset($data['REQUIRED']) && $data['REQUIRED'] == 'YES' ? '<strong class="formRequired">(*)</strong>' : '';
			$new_field[3]	= $field->name;
			$name			= $field->name;

			if ($field->type != 'static') {
				if (isset($values[$field->name]))
					$value	= $values[$field->name];
				else {
					$value	= isset($submission->values[$field->name]) ? $submission->values[$field->name] : '';
				}
			} else {
				$value = isset($submission->{$field->name}) ? $submission->{$field->name} : '';
			}

			switch ($field->type)
			{
				case 'static':
					$new_field[0] = JText::_('RSFP_'.$field->name);

					// Show a dropdown for yes/no
					if ($field->name == 'confirmed') {
						$options = array(
							JHtml::_('select.option', 0, JText::_('RSFP_NO')),
							JHtml::_('select.option', 1, JText::_('RSFP_YES'))
						);

						$new_field[1] = JHtml::_('select.genericlist', $options, 'formStatic[confirmed]', null, 'value', 'text', $value);
					} else {
						$new_field[1] = '<input class="rs_inp rs_80" type="text" name="formStatic['.$name.']" value="'.RSFormProHelper::htmlEscape($value).'" />';
					}
					break;

				// skip this field for now, no need to edit it
				case 'freeText':
				    $new_field[0] = '';
				    $new_field[1] = RSFormProHelper::isCode($data['TEXT']);
					break;

				default:
				    if (is_array($value))
                    {
                        $value = implode($form->MultipleSeparator, $value);
                    }

					if (strpos($value, "\n") !== false || strpos($value, "\r") !== false) {
						$new_field[1] = '<textarea style="width: 95%" class="rs_textarea'.$invalid.'" rows="10" cols="60" name="form['.$name.']">'.RSFormProHelper::htmlEscape($value).'</textarea>';
					} else {
						$new_field[1] = '<input class="rs_inp rs_80'.$invalid.'" type="text" name="form['.$name.']" value="'.RSFormProHelper::htmlEscape($value).'" />';
					}
					break;

				case 'textArea':
					if (isset($data['WYSIWYG']) && $data['WYSIWYG'] == 'YES')
						$new_field[1] = RSFormProHelper::WYSIWYG('form['.$name.']', RSFormProHelper::htmlEscape($value), '', 600, 100, 60, 10);
					else
						$new_field[1] = '<textarea style="width: 95%" class="rs_textarea'.$invalid.'" rows="10" cols="60" name="form['.$name.']">'.RSFormProHelper::htmlEscape($value).'</textarea>';
					break;

				case 'radioGroup':
				case 'checkboxGroup':
				case 'selectList':
					if ($field->type == 'radioGroup') {
						$data['SIZE'] = 0;
						$data['MULTIPLE'] = 'NO';
					} elseif ($field->type == 'checkboxGroup') {
						$data['SIZE'] = 5;
						$data['MULTIPLE'] = 'YES';
					}

					$value = !empty($values) ? $value : RSFormProHelper::explode($value);

                    require_once JPATH_ADMINISTRATOR . '/components/com_rsform/helpers/fields/fielditem.php';
                    require_once JPATH_ADMINISTRATOR . '/components/com_rsform/helpers/fieldmultiple.php';
                    $f = new RSFormProFieldMultiple(array(
                        'formId' 			=> $submission->FormId,
                        'componentId' 		=> $field->id,
                        'data' 				=> $data,
                        'value' 			=> array('formId' => $submission->FormId, $data['NAME'] => $value),
                        'invalid' 			=> in_array($field->id, $validation)
                    ));

                    $options = array();
                    if ($items = $f->getItems())
                    {
                        foreach ($items as $item)
                        {
                            $item = new RSFormProFieldItem($item);

                            if ($item->flags['optgroup']) {
                                $options[] = JHtml::_('select.option', '<OPTGROUP>', $item->label, 'value', 'text');
                            } elseif ($item->flags['/optgroup']) {
                                $options[] = JHtml::_('select.option', '</OPTGROUP>', $item->label, 'value', 'text');
                            } else {
                                $options[] = JHtml::_('select.option', $item->value, $item->label, 'value', 'text', $item->flags['disabled']);
                            }
                        }
                    }

                    $attribs = array();

                    if ((int) $data['SIZE'] > 0)
                    {
                        $attribs[] = 'size="'.(int) $data['SIZE'].'"';
                    }

                    if ($data['MULTIPLE'] == 'YES')
                    {
                        $attribs[] = 'multiple="multiple"';
                    }

					if ($invalid)
					{
						$attribs[] = 'class="' . $invalid . '"';
					}

                    $attribs = implode(' ', $attribs);

					$new_field[1] = JHtml::_('select.genericlist', $options, 'form['.$name.'][]', $attribs, 'value', 'text', $value);
					break;

				case 'fileUpload':
					$new_field[1] = '<div>';
					$new_field[1] .= '<span' . ($invalid ? ' class="' . $invalid . '"' : '') . '>' . RSFormProHelper::htmlEscape(basename($value)) . '</span>';

					if (strlen($value))
                    {
                        $new_field[1] .= ' <button type="button" class="btn btn-small" onclick="RSFormProDirectory.clearUpload(\'' . $name . '\', this);">' . JText::_('COM_RSFORM_CLEAR') . '</button>';
                    }

					$new_field[1] .= '</div>';
					$new_field[1] .= '<input size="45" type="file" name="form['.$name.']" />';
					break;
			}

			$return[$field->id] = $new_field;
		}

        JFactory::getApplication()->triggerEvent('rsfp_f_onGetEditFields', array(&$return, $submission));

		return $return;
	}

	public static function getCalculations($formId) {
		$db = JFactory::getDbo();
		$query = $db->getQuery(true)
            ->select('*')
            ->from($db->qn('#__rsform_calculations'))
            ->where($db->qn('formId') . ' = '. $db->q($formId))
            ->order($db->qn('ordering'));

		return $db->setQuery($query)->loadObjectList();
	}

	public static function getRelativeUploadPath($destination) {
		// Relative path
		// First check - Unix server and the path doesn't start with /
		// Second check - Windows server, path doesn't start with DRIVE:
		if (($destination[0] != '/' && DIRECTORY_SEPARATOR == '/') || (DIRECTORY_SEPARATOR == '\\' && $destination[1] != ':')) {
			$destination = JPATH_SITE.'/'.$destination;
		}

		return $destination;
	}

	public static function getForm($formId){
		static $form = array();

		$formId = (int) $formId;

		if (!isset($form[$formId])) {
			$db = JFactory::getDbo();
			$query = $db->getQuery(true);

			$query->clear()
				->select('*')
				->from($db->qn('#__rsform_forms'))
				->where($db->qn('FormId').'='.$db->q($formId));

			$db->setQuery($query);
			$form[$formId] = $db->loadObject();
		}

		return is_object($form[$formId]) ? clone $form[$formId] : false;
	}

	public static function getRawPost() {
		return JFactory::getApplication()->input->post->getArray(array(), null, 'raw');
	}

	public static function generateQuickAdd($field, $key){
		$html = '<strong>'.$field['name'].'</strong><br/>';

		foreach($field[$key] as $placeholder) {
			$html .= '<pre>'. $placeholder .'</pre>';
		}

		$html .= '<br/>';

		return $html;
	}

	public static function getFormLayouts($formId = 0)
    {
        $layouts = array(
            'classicLayouts' => array('inline', '2lines', '2colsinline', '2cols2lines'),
            'xhtmlLayouts' 	 => array('inline-xhtml', '2lines-xhtml'),
            'html5Layouts' 	 => array('responsive', 'bootstrap2', 'bootstrap3', 'bootstrap4', 'uikit', 'uikit3', 'foundation')
        );

        JFactory::getApplication()->triggerEvent('rsfp_bk_onLayoutsDefine', array(&$layouts, $formId));

        return $layouts;
    }
}