<?php
/**
* @package RSFiles!
* @copyright (C) 2010-2014 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/
defined('_JEXEC') or die('Restricted access');

class rsfilesControllerEmail extends JControllerLegacy
{	
	/**
	 * Constructor.
	 *
	 * @param	array	$config	An optional associative array of configuration settings.

	 * @return	rseventsproControllerMessages
	 * @see		JController
	 * @since	1.6
	 */
	public function __construct($config = array()) {
		parent::__construct($config);
	}
	
	/**
	 * Proxy for getModel.
	 *
	 * @param	string	$name	The name of the model.
	 * @param	string	$prefix	The prefix for the PHP class name.
	 *
	 * @return	JModel
	 * @since	1.6
	 */
	public function getModel($name = 'Email', $prefix = 'rsfilesModel', $config = array('ignore_request' => true)) {
		$model = parent::getModel($name, $prefix, $config);
		return $model;
	}
	
	/**
	 * Method to save configuration.
	 *
	 * @return	void
	 * @since	1.6
	 */
	public function save() {
		$data	= JFactory::getApplication()->input->get('jform', array(), 'array');
		$model	= $this->getModel();
		
		$model->save($data);
		echo rsfilesHelper::modalClose();
		JFactory::getApplication()->close();
	}
}