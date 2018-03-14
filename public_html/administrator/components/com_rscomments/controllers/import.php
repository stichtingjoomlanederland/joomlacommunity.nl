<?php
/**
* @package RSComments!
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('_JEXEC') or die('Restricted access');

class RscommentsControllerImport extends JControllerForm 
{
	public function getcolumns() {
		$model = $this->getModel('import');
		$columns = $model->getFields();
	
		echo json_encode($columns);
		exit();
	}

	public function save($key = null, $urlVar = null){
		$model = $this->getModel('import');
		$model->save();
	}
}