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

// Product info
if (!defined('_RSFORM_REVISION')) {
	$version = new RSFormProVersion();

	define('_RSFORM_REVISION', $version->revision);
}

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
		static $version;
		if (!$version) {
			$version = new JVersion();
		}

		if ($version->isCompatible('3.1.2')) {
			return JHtml::tooltipText($title, $content, 0, 0);
		} else {
			return $title.'::'.$content;
		}
	}

	public static function getTooltipClass() {
		static $class = false;
		if (!$class) {
			$version = new JVersion();
			if ($version->isCompatible('3.1.2')) {
				JHtml::_('bootstrap.tooltip');
				$class = 'hasTooltip';
			} else {
				JHtml::_('behavior.tooltip');
				$class = 'hasTip';
			}
		}

		return $class;
	}

	public static function loadCodeMirror() {
		if (RSFormProHelper::getConfig('global.codemirror')) {
			$document 	= JFactory::getDocument();
			$root		= JUri::root(true).'/administrator/components/com_rsform/assets/codemirror';

			// Load CodeMirror
			$document->addScript($root.'/lib/codemirror.js');
			$document->addScriptDeclaration('RSFormPro.initCodeMirror = true;');

			// Load modes
			$modes = array('xml', 'javascript', 'css', 'htmlmixed', 'clike', 'php');
			foreach ($modes as $mode) {
				$document->addScript("$root/mode/$mode/$mode.js");
			}

			// Load addons
			$document->addScript($root.'/addon/fold/xml-fold.js');
			$document->addScript($root.'/addon/selection/active-line.js');
			$document->addScript($root.'/addon/edit/matchbrackets.js');
			$document->addScript($root.'/addon/edit/matchtags.js');

			// Load CSS
			$document->addStyleSheet($root.'/lib/codemirror.css');
		}
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

	public static function checkValue($setvalue, $array)
	{
		if (!is_array($array))
			$array = RSFormProHelper::explode($array);

		if (strlen($setvalue))
			foreach ($array as $k => $v)
			{
				@list($value, $text) = explode("|", $v, 2);
				if ($value == $setvalue)
					$array[$k] = $v.'[c]';
			}

		return implode("\n", $array);
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
			$mainframe->enqueueMessage(JText::sprintf('RSFP_FORM_DOES_NOT_EXIST', $formId), 'warning');
			return false;
		}

		// Check form access level
		if (!$is_module && $form->Access != '') {
			$canView = false;
			$menu = $mainframe->getMenu();
			$active = $menu->getActive();

			if ($active) {
				if ($query = $active->query) {
					if (isset($query['option']) && isset($query['view']) && isset($query['formId'])) {
						if ($query['option'] == 'com_rsform' && $query['view'] == 'rsform' && $query['formId'] == $formId) {
							$canView = true;
						}
					}
				}
			}

			$rseventspro = $mainframe->input->get('option') == 'com_rseventspro' && $mainframe->input->get('layout') == 'subscribe';
			if ($rseventspro || $mainframe->isClient('administrator'))
				$canView = true;

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
		$post = JRequest::getVar('form', array(), 'post', 'none', JREQUEST_ALLOWRAW);
		if (isset($post['formId']) && $post['formId'] == $formId)
		{
			$invalid = RSFormProHelper::processForm($formId);
			// Did not pass validation - show the form
			if ($invalid)
			{
				if ($form->ScrollToError){
					RSFormProAssets::addScriptDeclaration('RSFormProUtils.addEvent(window, \'load\',function(){ RSFormPro.gotoErrorElement('.$formId.');  });');
				}
				return RSFormProHelper::showForm($formId, $post, $invalid);
			}
		}


		$get = $mainframe->input->get->get('form', array(), 'array');

		// Default - show the form
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

	public static function &getComponentProperties($components) {
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
		$value = str_replace(array("\r\n", "\r"), "\n", $value);
		$value = explode("\n", $value);

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

		$db = JFactory::getDbo();
		$u = JUri::getInstance();
		$config = JFactory::getConfig();
		$SubmissionId = (int) $SubmissionId;
		$mainframe = JFactory::getApplication();
		$Itemid = JFactory::getApplication()->input->getInt('Itemid');
		$Itemid = $Itemid ? '&amp;Itemid='.$Itemid : '';

		$db->setQuery("SELECT * FROM #__rsform_submissions WHERE SubmissionId='".$SubmissionId."'");
		$submission = $db->loadObject();

		$submission->values = array();
		$db->setQuery("SELECT FieldName, FieldValue FROM #__rsform_submission_values WHERE SubmissionId='".$SubmissionId."'");
		$fields = $db->loadObjectList();
		foreach ($fields as $field)
			$submission->values[$field->FieldName] = $field->FieldValue;
		unset($fields);

		$formId = $submission->FormId;
		$db->setQuery("SELECT * FROM #__rsform_forms WHERE FormId='".$formId."'");
		$form = $db->loadObject();
		$form->MultipleSeparator = str_replace(array('\n', '\r', '\t'), array("\n", "\r", "\t"), $form->MultipleSeparator);

		if (empty($submission->Lang))
		{
			if (!empty($form->Lang))
				$submission->Lang = $form->Lang;
			else
			{
				$lang = JFactory::getLanguage();
				$language = $lang->getDefault();
				$submission->Lang = $language;
			}
			$db->setQuery("UPDATE #__rsform_submissions SET Lang='".$db->escape($submission->Lang)."' WHERE SubmissionId='".$submission->SubmissionId."'");
			$db->execute();
		}

		$translations = RSFormProHelper::getTranslations('forms', $form->FormId, $submission->Lang);
		if ($translations)
			foreach ($translations as $field => $value)
			{
				if (isset($form->$field))
					$form->$field = $value;
			}

		$placeholders = array();
		$values = array();

		$db->setQuery("SELECT c.ComponentTypeId, p.ComponentId, p.PropertyName, p.PropertyValue FROM #__rsform_components c LEFT JOIN #__rsform_properties p ON (c.ComponentId=p.ComponentId) WHERE c.FormId='".$formId."' AND c.Published='1' AND p.PropertyName IN ('NAME', 'DESCRIPTION', 'CAPTION', 'EMAILATTACH', 'WYSIWYG', 'ITEMS', 'TEXT')");
		$components = $db->loadObjectList();
		$properties 	   = array();
		$uploadFields 	   = array();
		$hiddenFields	   = array();
		$multipleFields    = array();
		$textareaFields    = array();
		$freetextFields	   = array();
		$userEmailUploads  = array();
		$adminEmailUploads = array();
		$additionalEmailUploads = array();
		$additionalEmailUploadsIds = array();

		foreach ($components as $component)
		{
			// Upload fields - grab by NAME so that we can use it later on when checking $_FILES
			if ($component->ComponentTypeId == RSFORM_FIELD_FILEUPLOAD)
			{
				if ($component->PropertyName == 'EMAILATTACH')
				{
					$emailsvalues = $component->PropertyValue;
					$emailsvalues = trim($emailsvalues) != '' ? explode(',',$emailsvalues) : array();

					if (!empty($emailsvalues))
						foreach ($emailsvalues as $emailvalue)
						{
							if ($emailvalue == 'useremail' || $emailvalue == 'adminemail') continue;
							$additionalEmailUploadsIds[] = $emailvalue;
						}

					$additionalEmailUploadsIds = array_unique($additionalEmailUploadsIds);

					if (!empty($additionalEmailUploadsIds))
						foreach ($additionalEmailUploadsIds as $additionalEmailUploadsId)
						{
							if (in_array($additionalEmailUploadsId,$emailsvalues))
								$additionalEmailUploads[$additionalEmailUploadsId][] = $component->ComponentId;
						}
				}

				if ($component->PropertyName == 'NAME')
					$uploadFields[] = $component->PropertyValue;

				if ($component->PropertyName == 'EMAILATTACH' && !empty($component->PropertyValue))
				{
					$emailvalues = explode(',',$component->PropertyValue);

					if (in_array('useremail',$emailvalues))
					{
						$userEmailUploads[] = $component->ComponentId;
						//continue;
					}

					if (in_array('adminemail',$emailvalues))
					{
						$adminEmailUploads[] = $component->ComponentId;
						//continue;
					}
				}
			}
			// Multiple fields - grab by ComponentId for performance
			elseif (in_array($component->ComponentTypeId, array(RSFORM_FIELD_SELECTLIST, RSFORM_FIELD_CHECKBOXGROUP)))
			{
				if ($component->PropertyName == 'NAME')
					$multipleFields[] = $component->ComponentId;
			}
			// Textarea fields - grab by ComponentId for performance
			elseif ($component->ComponentTypeId == RSFORM_FIELD_TEXTAREA)
			{
				if ($component->PropertyName == 'WYSIWYG' && $component->PropertyValue == 'NO')
					$textareaFields[] = $component->ComponentId;
			} elseif ($component->ComponentTypeId == RSFORM_FIELD_FREETEXT) {
				$freetextFields[] = $component->ComponentId;
			} elseif (in_array($component->ComponentTypeId, array(RSFORM_FIELD_HIDDEN, RSFORM_FIELD_TICKET))) {
				$hiddenFields[] = $component->ComponentId;
			}

			$properties[$component->ComponentId][$component->PropertyName] = $component->PropertyValue;
		}

		// language
		$translations = RSFormProHelper::getTranslations('properties', $formId, $submission->Lang);
		foreach ($properties as $componentId => $componentProperties)
		{
			foreach ($componentProperties as $property => $value)
			{
				$reference_id = $componentId.'.'.$property;
				if (isset($translations[$reference_id]))
					$componentProperties[$property] = $translations[$reference_id];
			}
			$properties[$componentId] = $componentProperties;
		}

		$secret = $config->get('secret');
		foreach ($properties as $ComponentId => $property)
		{
			// {component:caption}
			$placeholders[] = '{'.$property['NAME'].':caption}';
			// Hidden fields don't have a caption
			if (in_array($ComponentId, $hiddenFields))
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

			// {component:price}
			if (isset($property['ITEMS'])) {
				if (strpos($property['ITEMS'], '[p') !== false) {
					$placeholders[] = '{'.$property['NAME'].':price}';
					$values[] = RSFormProHelper::getComponentPrice($property, $submission);
				}
			}

			// {component:value}
			$placeholders[] = '{'.$property['NAME'].':value}';
			$value = '';
			if (isset($submission->values[$property['NAME']]))
			{
				$value = $submission->values[$property['NAME']];

				// Check if this is an upload field
				if (in_array($property['NAME'], $uploadFields))
					$value = '<a href="'.JUri::root().'index.php?option=com_rsform&amp;task=submissions.view.file&amp;hash='.md5($submission->SubmissionId.$secret.$property['NAME']).$Itemid.'">'.basename($submission->values[$property['NAME']]).'</a>';
				// Check if this is a multiple field
				elseif (in_array($ComponentId, $multipleFields))
					$value = str_replace("\n", $form->MultipleSeparator, $value);
				elseif ($form->TextareaNewLines && in_array($ComponentId, $textareaFields))
					$value = nl2br($value);
			} elseif (in_array($ComponentId, $freetextFields)) {
				$value = $property['TEXT'];
			}
			$values[] = $value;

			if (isset($property['ITEMS'])) {
				$placeholders[] = '{'.$property['NAME'].':text}';
				if (isset($submission->values[$property['NAME']])) {
					$value = $submission->values[$property['NAME']];
					$all_values = explode("\n", $value);
					$all_texts  = array();
					$items = RSFormProHelper::explode(RSFormProHelper::isCode($property['ITEMS']));

					$special = array('[c]', '[g]', '[d]');
					$pricePattern = '#\[p([0-9\.\-\+]+)\]#s';
					foreach ($all_values as $v => $value) {
						$all_texts[$v] = $value;
						foreach ($items as $item) {
							$item = str_replace($special, '', $item);
							$item = preg_replace($pricePattern, '', $item);
							@list($item_val, $item_text) = explode("|", $item, 2);

							if ($item_text && $item_val == $value)
							{
								$all_texts[$v] = $item_text;
								break;
							}
						}
					}

					if ($all_texts) {
						$values[] = implode($form->MultipleSeparator, $all_texts);
					} else {
						$values[] = $value;
					}
				} else {
					$values[] = '';
				}
			}

			// {component:path}
			// {component:localpath}
			// {component:filename}
			if (in_array($property['NAME'], $uploadFields))
			{
				$placeholders[] = '{'.$property['NAME'].':path}';
				$placeholders[] = '{'.$property['NAME'].':localpath}';
				$placeholders[] = '{'.$property['NAME'].':filename}';
				if (isset($submission->values[$property['NAME']])) {
					$filepath = $submission->values[$property['NAME']];
					$filepath = substr_replace($filepath, JUri::root(), 0, strlen(JPATH_SITE)+1);
					$filepath = str_replace(array('\\', '\\/', '//\\'), '/', $filepath);
					$values[] = $filepath;
					$values[] = $submission->values[$property['NAME']];
					$values[] = basename($submission->values[$property['NAME']]);
				}
				else {
					$values[] = '';
					$values[] = '';
					$values[] = '';
				}
			}
		}
		$placeholders[] = '{_STATUS:value}';
		$values[] = isset($submission->values['_STATUS']) ? JText::_('RSFP_PAYPAL_STATUS_'.$submission->values['_STATUS']) : '';

		$placeholders[] = '{_ANZ_STATUS:value}';
		$values[] = isset($submission->values['_ANZ_STATUS']) ? JText::_('RSFP_ANZ_STATUS_'.$submission->values['_ANZ_STATUS']) : '';

		$user = JFactory::getUser($submission->UserId);
		if (empty($user->id))
			$user = JFactory::getUser(0);

		$root 				= $mainframe->isClient('administrator') ? JUri::root() : $u->toString(array('scheme','host', 'port'));
		$confirmation_hash 	= md5($submission->SubmissionId.$formId.$submission->DateSubmitted);
		$hash_link 			= 'index.php?option=com_rsform&task=confirm&hash='.$confirmation_hash;
        $delete_link 		= 'index.php?option=com_rsform&task=deletesubmission&hash='.$submission->SubmissionHash;
		$confirmation 		= $root.($mainframe->isClient('administrator') ? $hash_link : JRoute::_($hash_link));
		$deletion 		    = $root.($mainframe->isClient('administrator') ? $delete_link : JRoute::_($delete_link));

		if (!$skip_globals)
		{
			array_push($placeholders, '{global:username}', '{global:userid}', '{global:useremail}', '{global:fullname}', '{global:userip}', '{global:date_added}', '{global:sitename}', '{global:siteurl}', '{global:confirmation}', '{global:deletion}', '{global:submissionid}', '{global:submission_id}', '{global:mailfrom}', '{global:fromname}', '{global:formid}');
			array_push($values, $user->username, $user->id, $user->email, $user->name, $submission->UserIp, RSFormProHelper::getDate($submission->DateSubmitted), $config->get('sitename'), JUri::root(), $confirmation, $deletion, $submission->SubmissionId, $submission->SubmissionId, $config->get('mailfrom'), $config->get('fromname'), $formId);
		}

		$mainframe->triggerEvent('rsfp_onAfterCreatePlaceholders', array(array('form' => &$form, 'placeholders' => &$placeholders, 'values' => &$values, 'submission' => $submission)));

		if ($only_return_replacements)
			return array($placeholders, $values);

		// RSForm! Pro Scripting - User Email Text
		// performance check
		if (strpos($form->UserEmailText, '{/if}') !== false) {
			require_once dirname(__FILE__).'/scripting.php';
			RSFormProScripting::compile($form->UserEmailText, $placeholders, $values);
		}

		$userEmail = array(
			'to' => str_replace($placeholders, $values, $form->UserEmailTo),
			'cc' => str_replace($placeholders, $values, $form->UserEmailCC),
			'bcc' => str_replace($placeholders, $values, $form->UserEmailBCC),
			'from' => str_replace($placeholders, $values, $form->UserEmailFrom),
			'replyto' => str_replace($placeholders, $values, $form->UserEmailReplyTo),
			'fromName' => str_replace($placeholders, $values, $form->UserEmailFromName),
			'text' => str_replace($placeholders, $values, $form->UserEmailText),
			'subject' => str_replace($placeholders, $values, $form->UserEmailSubject),
			'mode' => $form->UserEmailMode,
			'files' => array()
		);

		// user cc
		if (strpos($userEmail['cc'], ',') !== false)
			$userEmail['cc'] = explode(',', $userEmail['cc']);
		// user bcc
		if (strpos($userEmail['bcc'], ',') !== false)
			$userEmail['bcc'] = explode(',', $userEmail['bcc']);

		jimport('joomla.filesystem.file');

		$file = str_replace($placeholders, $values, $form->UserEmailAttachFile);
		if ($form->UserEmailAttach && file_exists($file))
			$userEmail['files'][] = $file;

		// Need to attach files
		// User Email
		foreach ($userEmailUploads as $componentId)
		{
			$name = $properties[$componentId]['NAME'];
			if (!empty($submission->values[$name]))
				$userEmail['files'][] = $submission->values[$name];
		}

		// RSForm! Pro Scripting - Admin Email Text
		// performance check
		if (strpos($form->AdminEmailText, '{/if}') !== false) {
			require_once dirname(__FILE__).'/scripting.php';
			RSFormProScripting::compile($form->AdminEmailText, $placeholders, $values);
		}

		$adminEmail = array(
			'to' => str_replace($placeholders, $values, $form->AdminEmailTo),
			'cc' => str_replace($placeholders, $values, $form->AdminEmailCC),
			'bcc' => str_replace($placeholders, $values, $form->AdminEmailBCC),
			'from' => str_replace($placeholders, $values, $form->AdminEmailFrom),
			'replyto' => str_replace($placeholders, $values, $form->AdminEmailReplyTo),
			'fromName' => str_replace($placeholders, $values, $form->AdminEmailFromName),
			'text' => str_replace($placeholders, $values, $form->AdminEmailText),
			'subject' => str_replace($placeholders, $values, $form->AdminEmailSubject),
			'mode' => $form->AdminEmailMode,
			'files' => array()
		);

		// admin cc
		if (strpos($adminEmail['cc'], ',') !== false)
			$adminEmail['cc'] = explode(',', $adminEmail['cc']);
		// admin bcc
		if (strpos($adminEmail['bcc'], ',') !== false)
			$adminEmail['bcc'] = explode(',', $adminEmail['bcc']);

		// Admin Email
		foreach ($adminEmailUploads as $componentId)
		{
			$name = $properties[$componentId]['NAME'];
			if (!empty($submission->values[$name]))
				$adminEmail['files'][] = $submission->values[$name];
		}

		$mainframe->triggerEvent('rsfp_beforeUserEmail', array(array('form' => &$form, 'placeholders' => &$placeholders, 'values' => &$values, 'submissionId' => $SubmissionId, 'userEmail'=>&$userEmail)));

		// Script called before the User Email is sent.
		eval($form->UserEmailScript);

		// mail users
		if ($userEmail['to']) {
			$recipients = explode(',', $userEmail['to']);
			RSFormProHelper::sendMail($userEmail['from'], $userEmail['fromName'], $recipients, $userEmail['subject'], $userEmail['text'], $userEmail['mode'], !empty($userEmail['cc']) ? $userEmail['cc'] : null, !empty($userEmail['bcc']) ? $userEmail['bcc'] : null, $userEmail['files'], !empty($userEmail['replyto']) ? $userEmail['replyto'] : '');
		}

		$mainframe->triggerEvent('rsfp_beforeAdminEmail', array(array('form' => &$form, 'placeholders' => &$placeholders, 'values' => &$values, 'submissionId' => $SubmissionId, 'adminEmail'=>&$adminEmail)));

		// Script called before the Admin Email is sent.
		eval($form->AdminEmailScript);

		//mail admins
		if ($adminEmail['to']) {
			$recipients = explode(',', $adminEmail['to']);
			RSFormProHelper::sendMail($adminEmail['from'], $adminEmail['fromName'], $recipients, $adminEmail['subject'], $adminEmail['text'], $adminEmail['mode'], !empty($adminEmail['cc']) ? $adminEmail['cc'] : null, !empty($adminEmail['bcc']) ? $adminEmail['bcc'] : null, $adminEmail['files'], !empty($adminEmail['replyto']) ? $adminEmail['replyto'] : '');
		}

		//additional emails
		$db->setQuery("SELECT * FROM #__rsform_emails WHERE `type` = 'additional' AND `formId` = ".$formId." AND `from` != ''");
		if ($emails = $db->loadObjectList()) {
			$etranslations = RSFormProHelper::getTranslations('emails', $formId, $submission->Lang);
			foreach ($emails as $email) {
				if (isset($etranslations[$email->id.'.fromname'])) {
					$email->fromname = $etranslations[$email->id.'.fromname'];
				}
				if (isset($etranslations[$email->id.'.subject'])) {
					$email->subject = $etranslations[$email->id.'.subject'];
				}
				if (isset($etranslations[$email->id.'.message'])) {
					$email->message = $etranslations[$email->id.'.message'];
				}

				if (empty($email->fromname) || empty($email->subject) || empty($email->message)) {
					continue;
				}

				// RSForm! Pro Scripting - Additional Email Text
				// performance check
				if (strpos($email->message, '{/if}') !== false) {
					require_once dirname(__FILE__).'/scripting.php';
					RSFormProScripting::compile($email->message, $placeholders, $values);
				}

				$additionalEmail = array(
					'to' => str_replace($placeholders, $values, $email->to),
					'cc' => str_replace($placeholders, $values, $email->cc),
					'bcc' => str_replace($placeholders, $values, $email->bcc),
					'from' => str_replace($placeholders, $values, $email->from),
					'replyto' => str_replace($placeholders, $values, $email->replyto),
					'fromName' => str_replace($placeholders, $values, $email->fromname),
					'text' => str_replace($placeholders, $values, $email->message),
					'subject' => str_replace($placeholders, $values, $email->subject),
					'mode' => $email->mode,
					'files' => array()
				);

				if (!empty($additionalEmailUploads))
					foreach ($additionalEmailUploads as $additionalEmailId => $additionalEmailUpload)
					{
						if ($additionalEmailId == $email->id)
							foreach ($additionalEmailUpload as $componentId)
							{
								$name = $properties[$componentId]['NAME'];
								if (!empty($submission->values[$name]))
									$additionalEmail['files'][] = $submission->values[$name];
							}
					}

				// additional cc
				if (strpos($additionalEmail['cc'], ',') !== false)
					$additionalEmail['cc'] = explode(',', $additionalEmail['cc']);
				// additional bcc
				if (strpos($additionalEmail['bcc'], ',') !== false)
					$additionalEmail['bcc'] = explode(',', $additionalEmail['bcc']);

				$mainframe->triggerEvent('rsfp_beforeAdditionalEmail', array(array('form' => &$form, 'placeholders' => &$placeholders, 'values' => &$values, 'submissionId' => $SubmissionId, 'additionalEmail'=>&$additionalEmail)));
				eval($form->AdditionalEmailsScript);

				// mail users
				if ($additionalEmail['to']) {
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

	public static function componentExists($formId, $componentTypeId)
	{
		$formId = (int) $formId;
		$db = JFactory::getDbo();

		if (is_array($componentTypeId))
		{
            array_map('intval', $componentTypeId);
            $db->setQuery("SELECT ComponentId FROM #__rsform_components WHERE ComponentTypeId IN (".implode(',', $componentTypeId).") AND FormId='".$formId."' AND Published='1'");
		}
		else
		{
			$componentTypeId = (int) $componentTypeId;
			$db->setQuery("SELECT ComponentId FROM #__rsform_components WHERE ComponentTypeId='".$componentTypeId."' AND FormId='".$formId."' AND Published='1'");
		}

		return $db->loadColumn();
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
		$db   = JFactory::getDbo();

		if (!$lang)
			$lang = RSFormProHelper::getCurrentLanguage();

		// get all conditions
		$db->setQuery("SELECT c.*,p.PropertyValue AS ComponentName FROM `#__rsform_conditions` c LEFT JOIN #__rsform_properties p ON (c.component_id = p.ComponentId) LEFT JOIN #__rsform_components comp ON (comp.ComponentId=p.ComponentId) WHERE c.`form_id` = ".$formId." AND c.lang_code='".$db->escape($lang)."' AND comp.Published = 1 AND p.PropertyName='NAME' ORDER BY c.`id` ASC");
		if ($conditions = $db->loadObjectList())
		{
			// put them all in an array so we can use only one query
			$cids = array();
			foreach ($conditions as $condition)
				$cids[] = $condition->id;

			// get details
			$db->setQuery("SELECT d.*,p.PropertyValue AS ComponentName FROM #__rsform_condition_details d LEFT JOIN #__rsform_properties p ON (d.component_id = p.ComponentId) LEFT JOIN #__rsform_components comp ON (comp.ComponentId=p.ComponentId) WHERE d.condition_id IN (".implode(",", $cids).") AND comp.Published = 1 AND p.PropertyName='NAME'");
			$details = $db->loadObjectList();

			// arrange details within conditions
			foreach ($conditions as $i => $condition)
			{
				$condition->details = array();
				foreach ($details as $detail)
				{
					if ($detail->condition_id != $condition->id) continue;
					$detail->value = preg_replace('#\[p(.*?)\]#is','',$detail->value);
					$condition->details[] = $detail;
				}

				$conditions[$i] = $condition;
			}
			// all done
			return $conditions;
		}
		// nothing found
		return false;
	}

	public static function showForm($formId, $val=array(), $validation=array())
	{
		$mainframe 	= JFactory::getApplication();
		$db 		= JFactory::getDbo();
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
				$page_progress[]	= array('show' => (@$data['DISPLAYPROGRESS'] == 'YES' || @$data['DISPLAYPROGRESS'] == 'AUTO'), 'text' => @$data['DISPLAYPROGRESSMSG'], 'auto' => @$data['DISPLAYPROGRESS'] == 'AUTO');
			}
			elseif ($component->ComponentTypeId == RSFORM_FIELD_SUBMITBUTTON)
			{
				$data['SUBMITS'] = $submits;
				if ($component->ComponentId == end($submits))
					$page_progress[] = array('show' => (@$data['DISPLAYPROGRESS'] == 'YES' || @$data['DISPLAYPROGRESS'] == 'AUTO'), 'text' => @$data['DISPLAYPROGRESSMSG'], 'auto' => @$data['DISPLAYPROGRESS'] == 'AUTO');
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
		$u 			= $form->CSSAction ? RSFormProHelper::htmlEscape($form->CSSAction) : $u;
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

		if ($conditions = RSFormProHelper::getConditions($formId))
		{
			$formLayout .= '<script type="text/javascript">';
			$runAllConditions = "\n".'function rsfp_runAllConditions'.$formId.'(){';

			foreach ($conditions as $condition)
			{
				$formLayout .= "\n".'function rsfp_runCondition'.$condition->id.'(){';
				$runAllConditions .= "\n".'rsfp_runCondition'.$condition->id.'();';
				if ($condition->details)
				{
					$condition_vars = array();
					foreach ($condition->details as $detail)
					{
						$formLayout .= "\n"."isChecked = rsfp_verifyChecked(".$formId.", '".addslashes($detail->ComponentName)."', '".addslashes($detail->value)."');";
						$formLayout .= "\n"."condition".$detail->id." = isChecked == ".($detail->operator == 'is' ? 'true' : 'false').";";

						$condition_vars[] = "condition".$detail->id;
					}

					if ($condition->block)
					{
						$block 		= JFilterOutput::stringURLSafe($condition->ComponentName);
						$formLayout .= "\n"."items = rsfp_getBlock(".$formId.", '".addslashes($block)."');";
					}
					else
					{
						$formLayout .= "\n"."items = rsfp_getFieldsByName(".$formId.", '".addslashes($condition->ComponentName)."');";
					}

					$formLayout .= "\n"."if (items) {";
					$formLayout .= "\n"."if (".implode($condition->condition == 'all' ? '&&' : '||', $condition_vars).") {";
					$formLayout .= "\n"."displayValue = '".($condition->action == 'show' ? '' : 'none')."';";
					$formLayout .= "\n".'} else {';
					$formLayout .= "\n"."displayValue = '".($condition->action == 'show' ? 'none' : '')."';";
					$formLayout .= "\n".'}';
					$formLayout .= "\n".'rsfp_setDisplay(items, displayValue);';
					$formLayout .= "\n"."if (displayValue == 'none') {";
					$formLayout .= "\n"."RSFormPro.resetValues(rsfp_getFieldsByName(".$formId.", '".addslashes($condition->ComponentName)."'));";
					$formLayout .= "\n"."}";
					$formLayout .= "\n"."}";
				}
				$formLayout .= "\n".'}';
				$formLayout .= "\n".'rsfp_runCondition'.$condition->id.'();';
				if ($condition->details) {
					$uniques = array();
					foreach ($condition->details as $detail) {
						if (!in_array($detail->ComponentName, $uniques)) {
							$formLayout .= "\n"."rsfp_addCondition(".$formId.", '".addslashes($detail->ComponentName)."', rsfp_runCondition".$condition->id.");";
							$uniques[] = $detail->ComponentName;
						}
					}
				}
			}

			$runAllConditions .= "\n".'}';

			$formLayout .= "\n"."RSFormPro.Conditions.addReset($formId);";
			$formLayout .= "\n".$runAllConditions."\n".'</script>';
		}

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
		$mainframe = JFactory::getApplication();

		$formId = (int) $formId;

		$db = JFactory::getDbo();
		$form = RSFormProHelper::getForm($formId);

		$lang 		  = RSFormProHelper::getCurrentLanguage();
		$translations = RSFormProHelper::getTranslations('forms', $formId, $lang);
		if ($translations)
			foreach ($translations as $field => $value)
			{
				if (isset($form->$field))
					$form->$field = $value;
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

			$confirmsubmission = $form->ConfirmSubmission ? 0 : 1;

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
                'confirmed'      => $confirmsubmission,
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

				$db->setQuery("INSERT INTO #__rsform_submission_values SET `SubmissionId`='".$SubmissionId."', `FormId`='".$formId."', `FieldName`='".$db->escape($key)."', `FieldValue`='".$db->escape($val)."'");
				$db->execute();
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
			$db->setQuery("SELECT * FROM #__rsform_mappings WHERE formId = ".(int) $formId." ORDER BY ordering ASC");
			$mappings = $db->loadObjectList();

			// get Post to another location
			$db->setQuery("SELECT * FROM #__rsform_posts WHERE form_id='".(int) $formId."' AND enabled='1'");
			$silentPost = $db->loadObject();

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
			} else if ($form->ThankYouMessagePopUp && !$form->ScrollToThankYou)
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
			} else {
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
				$db->setQuery("DELETE FROM #__rsform_submission_values WHERE SubmissionId = ".(int) $SubmissionId." ");
				$db->execute();
				$db->setQuery("DELETE FROM #__rsform_submissions WHERE SubmissionId = ".(int) $SubmissionId." ");
				$db->execute();
			}

			if (!$form->KeepIP) {
				$db->setQuery("UPDATE #__rsform_submissions SET UserIp = '--' WHERE SubmissionId = ".(int) $SubmissionId." ");
				$db->execute();
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
		if (isset($post['form'][$componentName]))
		{
			if (is_array($post['form'][$componentName]) && in_array($value, $post['form'][$componentName]))
				return 1;

			if (!is_array($post['form'][$componentName]) && $post['form'][$componentName] == $value)
				return 1;

			return 0;
		}

		return 0;
	}

	public static function validateForm($formId, $validationType='form', $SubmissionId=0)
	{
		$mainframe  = JFactory::getApplication();
		$db 	 	= JFactory::getDbo();
		$invalid 	= array();
		$formId  	= (int) $formId;
		$post 		= RSFormProHelper::getRawPost();

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
						$dateParts = explode($data['DATESEPARATOR'], $post['form'][$data['NAME']]);

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

						$post['form'][$data['NAME']] = array();

						if ($data['SHOWDAY'] == 'YES') {
							$post['form'][$data['NAME']]['d'] = $dateParts[$day];
						}
						if ($data['SHOWMONTH'] == 'YES') {
							$post['form'][$data['NAME']]['m'] = $dateParts[$month];
						}
						if ($data['SHOWYEAR'] == 'YES') {
							$post['form'][$data['NAME']]['y'] = $dateParts[$year];
						}
					}

					if ($required) {
						// we need all of the fields to be selected
						if ($data['SHOWDAY'] == 'YES' && empty($post['form'][$data['NAME']]['d']) ||
							$data['SHOWMONTH'] == 'YES' && empty($post['form'][$data['NAME']]['m']) ||
							$data['SHOWYEAR'] == 'YES' && empty($post['form'][$data['NAME']]['y'])) {
							$invalid[] = $data['componentId'];
							continue;
						}

						$runValidations = true;
					} else {
						// the field is not required, but if a selection is made it needs to be valid
						$selections = array();
						if ($data['SHOWDAY'] == 'YES') {
							$selections[] = !empty($post['form'][$data['NAME']]['d']) ? $post['form'][$data['NAME']]['d'] : '';
						}
						if ($data['SHOWMONTH'] == 'YES') {
							$selections[] = !empty($post['form'][$data['NAME']]['m']) ? $post['form'][$data['NAME']]['m'] : '';
						}
						if ($data['SHOWYEAR'] == 'YES') {
							$selections[] = !empty($post['form'][$data['NAME']]['y']) ? $post['form'][$data['NAME']]['y'] : '';
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

						$day   = $post['form'][$data['NAME']]['d'];
						$month = $post['form'][$data['NAME']]['m'];
						$year  = $post['form'][$data['NAME']]['y'];

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
						$words = RSFormProHelper::getInvisibleCaptchaWords();
						if (!empty($post[$captchaCode]))
							$invalid[] = $data['componentId'];
						foreach ($words as $word)
							if (!empty($post[$word]))
								$invalid[] = $data['componentId'];
					}
					else
					{
						if (empty($post['form'][$data['NAME']]) || empty($captchaCode) || $post['form'][$data['NAME']] != $captchaCode)
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
							$tmp_name	= $files[$data['NAME']]['tmp_name'];
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
								$acceptedExts 	= !empty($data['ACCEPTEDFILES']) ? self::explode($data['ACCEPTEDFILES']) : false;

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

				if ($required) {
					// field is required, but is missing
					if (!isset($post['form'][$data['NAME']])) {
						$invalid[] = $data['componentId'];
						continue;
					}

					// must have a value if it's required
					if (is_array($post['form'][$data['NAME']])) { // it's an empty array
						$valid = implode('',$post['form'][$data['NAME']]);
						if (empty($valid)) {
							$invalid[] = $data['componentId'];
							continue;
						}
					} else { // it's a string with no length
						if (!strlen(trim($post['form'][$data['NAME']]))) {
							$invalid[] = $data['componentId'];
							continue;
						}

						if ($typeId == RSFORM_FIELD_RANGE_SLIDER && empty($post['form'][$data['NAME']]))
						{
							$invalid[] = $data['componentId'];
						}

						$runValidations = true;
					}
				} else { // not required, perform checks only when something is selected
					// we have a value, make sure it's the correct one
					if (isset($post['form'][$data['NAME']]) && !is_array($post['form'][$data['NAME']]) && strlen(trim($post['form'][$data['NAME']]))) {
						$runValidations = true;
					}
				}
				
				// check the calendar field if is required
				if (($typeId == RSFORM_FIELD_CALENDAR || $typeId == RSFORM_FIELD_JQUERY_CALENDAR) && strlen(trim($post['form'][$data['NAME']]))) {
					$selectedDate = $post['form'][$data['NAME']];

					if (JFactory::getLanguage()->getTag() != 'en-GB')
					{
						require_once JPATH_ADMINISTRATOR.'/components/com_rsform/helpers/calendar.php';
						
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

				if ($runValidations && isset($validations[$validationRule]) && !call_user_func(array($validationClass, $validationRule), $post['form'][$data['NAME']], isset($data['VALIDATIONEXTRA']) ? $data['VALIDATIONEXTRA'] : '', $data)) {
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

		if ($langs[$formId] == $lang)
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
		return array('LABEL', 'RESETLABEL', 'PREVBUTTON', 'NEXTBUTTON', 'CAPTION', 'DESCRIPTION', 'VALIDATIONMESSAGE', 'DEFAULTVALUE', 'ITEMS', 'TEXT', 'REFRESHTEXT', 'DISPLAYPROGRESSMSG', 'WIRE', 'SHOWDAYPLEASE', 'SHOWMONTHPLEASE', 'SHOWYEARPLEASE', 'POPUPLABEL', 'PLACEHOLDER');
	}

	public static function translateIcon()
	{
		return '<span class="rsficon rsficon-flag" title="' . JText::_('RSFP_THIS_ITEM_IS_TRANSLATABLE') . '" style="color:#3071a9"></span>';
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

			$config 	= JFactory::getConfig();
			$mailfrom	= $config->get('mailfrom');

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
			$query = $db->getQuery(true);
			$query->select($db->qn('p.PropertyValue','FieldName'))
				->select($db->qn('p.ComponentId','FieldId'))
				->select($db->qn('c.ComponentTypeId','FieldType'))
				->from($db->qn('#__rsform_components','c'))
				->join('left', $db->qn('#__rsform_properties','p').' ON '.$db->qn('c.ComponentId').' = '.$db->qn('p.ComponentId'))
				->where($db->qn('c.FormId').'='.$db->q($formId))
				->where($db->qn('p.PropertyName').' = '.$db->q('NAME'))
				->where($db->qn('c.ComponentTypeId').' NOT IN (7,8,10,12,13,41)')
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

			$db->setQuery('SELECT * FROM '.$db->qn('#__rsform_directory_fields').' WHERE '.$db->qn('formId').' = '.(int) $formId.' ORDER BY '.$db->qn('ordering').' ASC');
			$currentFields = $db->loadObjectList('componentId');

			$allFields = self::getAllDirectoryFields($formId);
			if ($diffFields = array_diff(array_keys($currentFields), array_keys($allFields))) {
				foreach ($diffFields as $fieldId) {
					unset($currentFields[$fieldId]);
				}
			}

			foreach ($allFields as $field) {
				// Hidden fields don't have a caption
				if (in_array($field->FieldType, array(RSFORM_FIELD_HIDDEN, RSFORM_FIELD_TICKET))) {
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
		$pattern	= '#\[p(.*?)\]#is';

		jimport('joomla.filesystem.file');

		// Load submission
		$query = $db->getQuery(true);
		$query->select('*')
			->from($db->qn('#__rsform_submissions'))
			->where($db->qn('SubmissionId').'='.$db->q($cid));
		$submission = $db->setQuery($query)->loadObject();
		if (empty($submission)) {
			return $return;
		}

		$submission->DateSubmitted = JHtml::_('date', $submission->DateSubmitted, 'Y-m-d H:i:s');

		// Get submission values
		$submission->values = array();
		$query->clear()
			->select($db->qn('FieldName'))
			->select($db->qn('FieldValue'))
			->from($db->qn('#__rsform_submission_values'))
			->where($db->qn('SubmissionId').'='.$db->q($cid));
		if ($submissionValues = $db->setQuery($query)->loadObjectList()) {
			foreach ($submissionValues as $value) {
				$submission->values[$value->FieldName] = $value->FieldValue;
			}
			unset($submissionValues);
		}

		$validation		= !empty($values) ? RSFormProHelper::validateForm($submission->FormId, 'directory') : array();
		$formFields 	= self::getDirectoryFields($submission->FormId);
		$headers 		= self::getDirectoryStaticHeaders();

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
			if ($formField->FieldId < 0) {
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

			if ($data['NAME'] == 'RSEProPayment')
				$field->type = 'rsepropayment';

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
					continue 2;
					break;

				default:
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

					$items = RSFormProHelper::isCode($data['ITEMS']);
					$items = RSFormProHelper::explode($items);

					$options = array();
					foreach($items as $item) {

						if (preg_match($pattern,$item,$match)) {
							$item = preg_replace($pattern,'',$item);
						}

						// <OPTGROUP>
						if(preg_match('/\[g\]/',$item))
						{
							$item = str_replace('[g]', '', $item);
							$optgroup = new stdClass();
							$optgroup->value = '<OPTGROUP>';
							$optgroup->text = $item;
							$options[] = $optgroup;
							continue;
						}

						// </OPTGROUP>
						if(preg_match('/\[\/g\]/',$item))
						{
							$optgroup = new stdClass();
							$optgroup->value = '</OPTGROUP>';
							$optgroup->text = '';
							$options[] = $optgroup;
							continue;
						}

						$buf = explode('|',$item);

						$val = str_replace('[c]', '', $buf[0]);
						$item = str_replace('[c]', '', count($buf) == 1 ? $buf[0] : $buf[1]);
						$options[] = JHtml::_('select.option', $val, $item);
					}

					$attribs = array();
					if ((int) $data['SIZE'] > 0)
						$attribs[] = 'size="'.(int) $data['SIZE'].'"';
					if ($data['MULTIPLE'] == 'YES')
						$attribs[] = 'multiple="multiple"';
					if ($invalid)
						$attribs[] = 'class="rsform-error"';
					$attribs = implode(' ', $attribs);

					$new_field[1] = JHtml::_('select.genericlist', $options, 'form['.$name.'][]', $attribs, 'value', 'text', $value);
					break;

				case 'fileUpload':
					$new_field[1]  = '<span class="'.$invalid.'">'.RSFormProHelper::htmlEscape(basename($value)).'</span>';
					$new_field[1] .= '<br /><input size="45" type="file" name="form['.$name.']" />';
					break;
			}

			$return[] = $new_field;
		}

		return $return;
	}

	public static function getCalculations($formId) {
		$db = JFactory::getDbo();

		$db->setQuery("SELECT * FROM #__rsform_calculations WHERE formId = ".(int) $formId." ORDER BY `ordering` ");
		return $db->loadObjectList();
	}

	public static function hasCalculations($formId) {
		static $cache = array();
		if (!isset($cache[$formId])) {
			$db = JFactory::getDbo();
			$db->setQuery("SELECT COUNT(id) FROM #__rsform_calculations WHERE formId = ".(int) $formId." ");
			$cache[$formId] = (int) $db->loadResult();
		}

		return $cache[$formId];
	}

	public static function getComponentPrice($property, $submission) {
		$price		= 0;
		$pattern	= '#\[p(.*?)\]#is';

		if (isset($property['ITEMS'])) {
			$products = array();
			$special = array('[c]', '[g]', '[d]');
			if ($items = RSFormProHelper::explode(RSFormProHelper::isCode($property['ITEMS']))) {
				foreach ($items as $item) {
					$item = str_replace($special, '', $item);
					@list($item_val, $item_text) = explode("|", $item, 2);

					if (preg_match($pattern,$item,$match)) {
						$item_val = preg_replace($pattern,'',$item_val);
						$products[$item_val] = $match[1];
					}
				}
			}

			if (isset($submission->values[$property['NAME']])) {
				$value = $submission->values[$property['NAME']];
				$all_values = explode("\n", $value);

				foreach ($all_values as $val) {
					$price += isset($products[$val]) ? (float) $products[$val] : 0;
				}
			}
		}

		return number_format($price, (int) RSFormProHelper::getConfig('calculations.nodecimals'),RSFormProHelper::getConfig('calculations.decimal'),RSFormProHelper::getConfig('calculations.thousands'));
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
				->select($db->qn('FormId'))
				->select($db->qn('FormLayoutName'))
				->select($db->qn('FormLayout'))
				->select($db->qn('LoadFormLayoutFramework'))
				->select($db->qn('FormLayoutFlow'))
				->select($db->qn('DisableSubmitButton'))
				->select($db->qn('RemoveCaptchaLogged'))
				->select($db->qn('KeepIP'))
				->select($db->qn('Keepdata'))
				->select($db->qn('ConfirmSubmission'))
				->select($db->qn('ScriptProcess'))
				->select($db->qn('ScriptProcess2'))
				->select($db->qn('UserEmailScript'))
				->select($db->qn('AdminEmailScript'))
				->select($db->qn('ReturnUrl'))
				->select($db->qn('ShowThankyou'))
				->select($db->qn('ShowSystemMessage'))
				->select($db->qn('ScrollToThankYou'))
				->select($db->qn('ThankYouMessagePopUp'))
				->select($db->qn('Thankyou'))
				->select($db->qn('ShowContinue'))
				->select($db->qn('TextareaNewLines'))
				->select($db->qn('Published'))
				->select($db->qn('FormTitle'))
				->select($db->qn('MetaTitle'))
				->select($db->qn('MetaDesc'))
				->select($db->qn('MetaKeywords'))
				->select($db->qn('Access'))
				->select($db->qn('ScriptDisplay'))
				->select($db->qn('ErrorMessage'))
				->select($db->qn('FormTitle'))
				->select($db->qn('CSS'))
				->select($db->qn('JS'))
				->select($db->qn('CSSClass'))
				->select($db->qn('CSSId'))
				->select($db->qn('CSSName'))
				->select($db->qn('CSSAction'))
				->select($db->qn('CSSAdditionalAttributes'))
				->select($db->qn('AjaxValidation'))
				->select($db->qn('ScrollToError'))
				->select($db->qn('MultipleSeparator'))
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
}