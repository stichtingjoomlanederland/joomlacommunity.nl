<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined('_JEXEC') or die('Restricted access');

class RseventsproControllerUsers extends JControllerAdmin
{
	public function getModel($name = 'User', $prefix = 'RseventsproModel', $config = array('ignore_request' => true)) {
		return parent::getModel($name, $prefix, $config);
	}
	
	public function deleteimage() {
		// Get the model
		$model = $this->getModel();
		
		// Delete image
		echo (int) $model->deleteimage();
		JFactory::getApplication()->close();
	}
}