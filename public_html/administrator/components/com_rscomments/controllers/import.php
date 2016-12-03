<?php
/**
* @package RSComments!
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('_JEXEC') or die('Restricted access');

class RSCommentsControllerImport extends JControllerForm {
	/**
	 * Class constructor.
	 *
	 * @param   array  $config  A named array of configuration variables.
	 *
	 * @since	1.6
	 */
	public function __construct() {
		parent::__construct();
	}

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