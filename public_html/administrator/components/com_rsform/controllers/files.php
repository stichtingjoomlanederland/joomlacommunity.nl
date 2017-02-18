<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2014 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controller');

class RsformControllerFiles extends RsformController
{
	public function display($cachable = false, $urlparams = false) {
		JFactory::getApplication()->input->set('view', 'files');
		JFactory::getApplication()->input->set('layout', 'default');
		
		parent::display($cachable, $urlparams);
	}
	
	public function upload() {
		// Check for request forgeries
		JSession::checkToken() or jexit('Invalid Token');

		// Get the model
		$model  = $this->getModel('files');
		
		$folder = $model->getCurrent();
		$result = $model->upload();
		$file 	= $model->getUploadFile();
		
		if ($result) {
			$msg = sprintf('Successfully uploaded %s!', $file);
		} else {
			$msg = sprintf('Failed to upload %s in %s', $file, $folder);
		}
		
		$this->setRedirect('index.php?option=com_rsform&controller=files&task=display&folder='.urlencode($folder).'&tmpl=component', $msg);
	}
}