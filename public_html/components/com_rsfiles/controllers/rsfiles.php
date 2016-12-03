<?php
/**
* @package RSFiles!
* @copyright (C) 2010-2014 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined('_JEXEC') or die('Restricted access');
jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');
jimport('joomla.filesystem.path');
jimport('joomla.mail.helper');

class rsfilesControllerRsfiles extends JControllerLegacy
{
	public function __construct() {
		parent::__construct();
	}
	
	// Method to report a file
	public function report() {
		// Get the model
		$model = $this->getModel('rsfiles');
		
		echo "<script type=\"text/javascript\">";
		echo "window.parent.jQuery('#rsf_alert').css('display','');";
		if (!$model->report()) {
			echo "window.parent.jQuery('#rsf_alert').addClass('alert-error');";
			echo "window.parent.jQuery('#rsf_message').html('".JText::_('COM_RSFILES_REPORT_ERROR',true)."');";
		} else {
			echo "window.parent.jQuery('#rsf_alert').removeClass('alert-error');";
			echo "window.parent.jQuery('#rsf_message').html('".JText::_('COM_RSFILES_REPORT_ADDED',true)."');";
		}
		echo "window.parent.SqueezeBox.close();";
		echo "</script>";
		exit();
	}
	
	// Method to bookmark a file
	public function bookmark() {
		// Get the model
		$model		= $this->getModel('rsfiles');
		$success	= true;
		$data		= new stdClass();

		if (!$model->bookmark()) {
			$success = false;
			$data->message = $model->getError();
		} else {
			$data->message = JText::_('COM_RSFILES_FILE_BOOKMARKED',true);
		}
		
		rsfilesHelper::showResponse($success,$data);
	}
	
	// Method to download bookmarks
	public function downloadbookmarks() {
		// Get the model
		$model		= $this->getModel('rsfiles');
		
		if (!$model->downloadbookmarks()) {
			$this->setMessage(JText::_('COM_RSFILES_PLEASE_SELECT_FILES'),'error');
			$this->setRedirect(JRoute::_('index.php?option=com_rsfiles&layout=bookmarks',false));
		}
	}
	
	// Method to remove a bookmark
	public function removebookmark() {
		// Get the model
		$model		= $this->getModel('rsfiles');
		
		$model->removebookmark();
		$this->setMessage(JText::_('COM_RSFILES_BOOKMARK_REMOVED'));
		$this->setRedirect(JRoute::_('index.php?option=com_rsfiles&layout=bookmarks',false));
	}
	
	// Method to create a new folder
	public function create() {
		// Get the model
		$model		= $this->getModel('rsfiles');
		
		echo "<script type=\"text/javascript\">";
		echo "window.parent.jQuery('#rsf_alert').css('display','');";
		if (!$model->create()) {
			echo "window.parent.jQuery('#rsf_alert').addClass('alert-error');";
			echo "window.parent.jQuery('#rsf_message').html('".$model->getError()."');";
		} else {
			echo "window.parent.jQuery('#rsf_alert').removeClass('alert-error');";
			echo "window.parent.jQuery('#rsf_message').html('".JText::_('COM_RSFILES_NEW_FOLDER_CREATED',true)."');";
			echo "window.parent.document.location.reload();";
		}
		echo "window.parent.SqueezeBox.close();";
		echo "</script>";
		exit();
	}
	
	// Method to upload files
	public function upload() {
		// Get model
		$model		= $this->getModel('rsfiles');
		$success 	= true;
		$data 		= new stdClass();
		
		if (!$model->upload()) {
			$success = false;
			$data->message = $model->getError();
		} else {
			$data->message	= $model->getState('success.message');
		}
		
		rsfilesHelper::showResponse($success, $data);
	}
	
	// Method to check uploaded file
	public function checkupload() {
		// Get model
		$model		= $this->getModel('rsfiles');
		$success 	= true;
		$data 		= new stdClass();
		
		if (!$model->checkupload()) {
			$success = false;
			$data->message = $model->getError();
		} else {
			$data->message	= $model->getState('success.message');
			$data->exists	= (int) $model->getState('perform.insert');
			$data->filename	= $model->getState('file.name');
		}
		
		rsfilesHelper::showResponse($success, $data);
	}
	
	// Method to delete canceled uploads
	public function cancelupload() {
		// Get model
		$model		= $this->getModel('rsfiles');
		$success 	= true;
		
		if (!$model->cancelupload()) {
			$success = false;
		}
		
		rsfilesHelper::showResponse($success);
	}
	
	// Method to upload external files
	public function uploadexternal() {
		$model		= $this->getModel('rsfiles');
		$success 	= true;
		$data 		= new stdClass();
		
		if (!$model->uploadexternal()) {
			$success = false;
			$data->message = $model->getError();
		} else {
			$data->message = $model->getState('success.message');
		}
		
		rsfilesHelper::showResponse($success, $data);
	}
	
	// Method to delete folders / files
	public function delete() {
		// Get the model
		$model	= $this->getModel('rsfiles');
		
		// Delete folder / files
		if (!$model->delete()) {
			$this->setMessage($model->getError(),'error');
		} else {
			$this->setMessage($model->getState('return.message'));
		}
		
		$path = $model->getState('return.path');
		$briefcase = JFactory::getApplication()->input->get('from') == 'briefcase';
		
		$this->setRedirect(JRoute::_('index.php?option=com_rsfiles'.($briefcase ? '&layout=briefcase' : '').($path ? '&folder='.urlencode($path) : ''),false));
	}
	
	// Method to cancel the editing of file
	public function cancel() {
		$return = JFactory::getApplication()->input->get('return', null, 'base64');
		$return = base64_decode($return);
		
		$this->setRedirect($return);
	}
	
	// Method to save the file
	public function save() {
		// Get the model
		$model	= $this->getModel('rsfiles');
		
		$input	= JFactory::getApplication()->input;
		$return = $input->get('return', null, 'base64');
		$return = base64_decode($return);
		$data	= $input->get('jform',array(),'array');
		$form	= $model->getForm();
		$data	= $form->filter($data);
		
		if (!$model->save($data)) {
			$this->setMessage($model->getError(),'error');
		} else {
			$this->setMessage(JText::_('COM_RSFILES_FILE_SAVED'));
		}
		
		$this->setRedirect($return);
	}
	
	// Method to delete the file thumb
	public function deletethumb() {
		// Get the model
		$model	= $this->getModel('rsfiles');
		
		$input	= JFactory::getApplication()->input;
		$return = $input->get('return_from', null, 'base64');
		$return = base64_decode($return);
		$jform	= $input->get('jform',array(),'array');
		
		if (!$model->deletethumb($jform['IdFile'])) {
			$this->setMessage($model->getError(),'error');
		} else {
			$this->setMessage(JText::_('COM_RSFILES_FILE_THUMB_DELETED'));
		}
		
		$this->setRedirect($return);
	}
	
	// Method to delete the file preview
	public function deletepreview() {
		// Get the model
		$model	= $this->getModel('rsfiles');
		
		$input	= JFactory::getApplication()->input;
		$return = $input->get('return_from', null, 'base64');
		$return = base64_decode($return);
		$jform	= $input->get('jform',array(),'array');
		
		if (!$model->deletepreview($jform['IdFile'])) {
			$this->setMessage($model->getError(),'error');
		} else {
			$this->setMessage(JText::_('COM_RSFILES_FILE_PREVIEW_DELETED'));
		}
		
		$this->setRedirect($return);
	}
	
	// Method to download a file
	public function download() {
		// Get the model
		$model	= $this->getModel('rsfiles');
		$model->download();
	}
	
	// Method to validate capthca
	public function validate() {
		// Get the model
		$model	= $this->getModel('rsfiles');
		
		$input	= JFactory::getApplication()->input;
		$return = $input->get('return', null, 'base64');
		$return = base64_decode($return);
		
		if (!$model->validate()) {
			$this->setMessage(JText::_('COM_RSFILES_INVALID_CAPTCHA'),'error');
		} else {
			$model->download();
		}
		
		$this->setRedirect($return);
	}
	
	// Method to send the download link via email
	public function emaildownload() {
		// Get the model
		$model	= $this->getModel('rsfiles');
		
		$input	= JFactory::getApplication()->input;
		$return = $input->get('return', null, 'base64');
		$return = base64_decode($return);
		
		if (!$model->emaildownload()) {
			$this->setMessage($model->getError(),'error');
			return $this->setRedirect($return);
		} else {
			echo rsfilesHelper::modalClose();
		}
	}
	
	// Method to create a new briefcase user folder
	public function newbriefcase() {
		// Get the model
		$model		= $this->getModel('rsfiles');
		$success	= true;
		$data		= new stdClass();

		if (!$model->newbriefcase()) {
			$success = false;
			$data->message = $model->getError();
		} else {
			$data->message = JText::_('COM_RSFILES_BRIEFCASE_CREATED',true);
		}
		
		rsfilesHelper::showResponse($success,$data);
	}
}