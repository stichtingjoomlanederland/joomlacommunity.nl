<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('_JEXEC') or die('Restricted access');

class rseventsproControllerMessage extends JControllerLegacy
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
		
		$this->registerTask('apply', 'save');
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
	public function getModel($name = 'Message', $prefix = 'rseventsproModel', $config = array('ignore_request' => true)) {
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
		$task	= $this->getTask();
		
		$model->save($data);
		
		$this->setMessage(JText::_('JLIB_APPLICATION_SAVE_SUCCESS'));
		
		if ($task == 'apply') {
			$this->setRedirect(JRoute::_('index.php?option=com_rseventspro&view=message&type='.$data['type'].'&jform[language]='.$data['language'],false));
		} else {
			$this->setRedirect(JRoute::_('index.php?option=com_rseventspro&view=messages',false));
		}
	}
	
	public function cancel() {
		$this->setRedirect(JRoute::_('index.php?option=com_rseventspro&view=messages',false));
	}
}