<?php
/**
 * @package	AcyMailing for Joomla!
 * @version	5.5.0
 * @author	acyba.com
 * @copyright	(C) 2009-2016 ACYBA S.A.R.L. All rights reserved.
 * @license	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('_JEXEC') or die('Restricted access');
?><?php


class FileViewFile extends acymailingView{
	function display($tpl = null){
		$doc = JFactory::getDocument();
		$doc->addStyleSheet(ACYMAILING_CSS.'frontendedition.css?v='.filemtime(ACYMAILING_MEDIA.'css'.DS.'frontendedition.css'));

		JRequest::setVar('tmpl', 'component');

		$function = $this->getLayout();
		if(method_exists($this, $function)) $this->$function();

		parent::display($tpl);
	}

	function css(){
		$file = JRequest::getCmd('file');
		if(!preg_match('#^([-A-Z0-9]*)_([-_A-Z0-9]*)$#i', $file, $result)){
			acymailing_display('Could not load the file '.$file.' properly');
			exit;
		}
		$type = $result[1];
		$fileName = $result[2];

		$content = JRequest::getString('csscontent');
		if(empty($content)) $content = file_get_contents(ACYMAILING_MEDIA.'css'.DS.$type.'_'.$fileName.'.css');

		if(strpos($fileName, 'default') !== false){
			$fileName = 'custom'.str_replace('default', '', $fileName);
			$i = 1;
			while(file_exists(ACYMAILING_MEDIA.'css'.DS.$type.'_'.$fileName.'.css')){
				$fileName = 'custom'.$i;
				$i++;
			}
		}

		if(JRequest::getString('tmpl') == 'component'){
			$acyToolbar = acymailing::get('helper.toolbar');
			$acyToolbar->custom('savecss', JText::_('ACY_SAVE'), 'save', false);
			$acyToolbar->setTitle($type.'_'.$fileName.'.css');
			$acyToolbar->topfixed = false;
			$acyToolbar->display();
		}

		$this->assignRef('content', $content);
		$this->assignRef('fileName', $fileName);
		$this->assignRef('type', $type);
	}


	function language(){

		$this->setLayout('default');

		$code = JRequest::getCmd('code');
		if(empty($code)){
			acymailing_display('Code not specified', 'error');
			return;
		}

		$file = new stdClass();
		$file->name = $code;
		$path = JLanguage::getLanguagePath(JPATH_ROOT).DS.$code.DS.$code.'.com_acymailing.ini';
		$file->path = $path;

		jimport('joomla.filesystem.file');
		$showLatest = true;
		$loadLatest = false;

		if(JFile::exists($path)){
			$file->content = JFile::read($path);
			if(empty($file->content)){
				acymailing_display('File not found : '.$path, 'error');
			}
		}else{
			$loadLatest = true;
			acymailing_enqueueMessage(JText::_('LOAD_ENGLISH_1').'<br />'.JText::_('LOAD_ENGLISH_2').'<br />'.JText::_('LOAD_ENGLISH_3'), 'info');
			$file->content = JFile::read(JLanguage::getLanguagePath(JPATH_ROOT).DS.'en-GB'.DS.'en-GB.com_acymailing.ini');
		}

		$custompath = JLanguage::getLanguagePath(JPATH_ROOT).DS.$code.DS.$code.'.com_acymailing_custom.ini';
		if(JFile::exists($custompath)){
			$file->customcontent = JFile::read($custompath);
		}

		if($loadLatest OR JRequest::getCmd('task') == 'latest'){
			if(file_exists(JPATH_ROOT.DS.'language'.DS.$code)){
				$doc = JFactory::getDocument();
				$doc->addScript(ACYMAILING_UPDATEURL.'languageload&code='.JRequest::getCmd('code'));
			}else{
				acymailing_enqueueMessage('The specified language "'.htmlspecialchars($code, ENT_COMPAT, 'UTF-8').'" is not installed on your site', 'warning');
			}
			$showLatest = false;
		}elseif(JRequest::getCmd('task') == 'save'){
			$showLatest = false;
		}

		if(JRequest::getString('tmpl') == 'component'){
			$acyToolbar = acymailing::get('helper.toolbar');
			$acyToolbar->save();
			$acyToolbar->custom('share', JText::_('SHARE'), 'share', false);
			$acyToolbar->setTitle(JText::_('ACY_FILE').' : '.$this->escape($file->name));
			$acyToolbar->topfixed = false;
			$acyToolbar->display();
		}

		$this->assignRef('showLatest', $showLatest);
		$this->assignRef('file', $file);
	}

	function share(){
		$file = new stdClass();
		$file->name = JRequest::getCmd('code');

		$acyToolbar = acymailing::get('helper.toolbar');
		$acyToolbar->custom('share', JText::_('SHARE'), 'share', false, "if(confirm('".JText::_('CONFIRM_SHARE_TRANS', true)."')){ javascript:submitbutton('send');} return false;");
		$acyToolbar->setTitle(JText::_('SHARE').' : '.$this->escape($file->name));
		$acyToolbar->topfixed = false;
		$acyToolbar->display();

		$this->assignRef('file', $file);
	}

	function select(){
		jimport('joomla.filesystem.file');
		jimport('joomla.filesystem.folder');

		$config =& acymailing_config();
		$uploadFolders = acymailing_getFilesFolder('upload', true);
		$uploadFolder = JRequest::getString('currentFolder', $uploadFolders[0]);
		$uploadPath = JPath::clean(ACYMAILING_ROOT.trim(str_replace('/', DS, trim($uploadFolder)), DS));
		$map = JRequest::getString('id');

		$doc = JFactory::getDocument();
		$uploadedFile = JRequest::getVar('uploadedFile', array(), 'files', 'array');
		if(!empty($uploadedFile) && !empty($uploadedFile['name'])){
			$uploaded = acymailing_importFile($uploadedFile, $uploadPath, in_array($map, array('thumb', 'readmore')));
			if($uploaded){
				$script = 'parent.document.getElementById("'.$map.'").value = "'.str_replace(DS, '/', $uploadFolder).'/'.$uploaded.'";';
				if(in_array($map, array('thumb', 'readmore'))){
					$script .= 'parent.document.getElementById("'.$map.'preview").src = "'.JURI::root().str_replace(DS, '/', $uploadFolder).'/'.$uploaded.'";';
				}else{
					$script .= 'parent.document.getElementById("'.$map.'selection").innerHTML = "'.$uploaded.'";';
				}
				$script .= 'window.parent.SqueezeBox.close();';
				$doc->addScriptDeclaration($script);
			}
		}

		$fileToDelete = JRequest::getString('filename', '');
		if(!empty($fileToDelete) && file_exists($uploadPath.DS.$fileToDelete) && empty($uploadedFile)){
			$db = JFactory::getDBO();
			$db->setQuery('SELECT mailid FROM #__acymailing_mail WHERE attach LIKE \'%"'.$uploadFolder.'/'.$fileToDelete.'"%\'');
			$checkAttach = acymailing_loadResultArray($db);

			if(!empty($checkAttach)){
				acymailing_display(JText::sprintf('ACY_CANT_DELETEFILE', implode($checkAttach, ', ')), 'error');
			}else{
				if(JFile::delete($uploadPath.DS.$fileToDelete)){
					acymailing_display(JText::_('ACY_DELETED_FILE_SUCCESS'), 'success');
				}else{
					acymailing_display(JText::_('ACY_DELETED_FILE_ERROR'), 'error');
				}
			}
		}

		$displayType = JRequest::getString('displayType', 'icons');
		$this->assignRef('config', $config);
		$this->assignRef('uploadFolder', $uploadFolder);
		$this->assignRef('uploadFolders', $uploadFolders);
		$this->assignRef('uploadPath', $uploadPath);
		$this->assignRef('map', $map);
		$this->assignRef('displayType', $displayType);
	}
}
