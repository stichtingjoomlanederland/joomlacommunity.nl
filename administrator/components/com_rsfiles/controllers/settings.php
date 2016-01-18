<?php
/**
* @package RSFiles!
* @copyright (C) 2010-2014 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/
defined('_JEXEC') or die('Restricted access');

class rsfilesControllerSettings extends JControllerLegacy
{	
	/**
	 * Constructor.
	 *
	 * @param	array	$config	An optional associative array of configuration settings.

	 * @return	rseventsproControllerSettings
	 * @see		JController
	 * @since	1.6
	 */
	public function __construct($config = array()) {
		parent::__construct($config);
		
		$this->registerTask('apply', 'save');
	}
	
	/**
	 * Method to cancel.
	 *
	 * @return	void
	 * @since	1.6
	 */
	public function cancel() {
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
		
		$this->setRedirect(JRoute::_('index.php?option=com_rsfiles', false));
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
	public function getModel($name = 'Settings', $prefix = 'rsfilesModel', $config = array('ignore_request' => true)) {
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
		$jinput	= JFactory::getApplication()->input;
		$data	= $jinput->get('jform', array(), 'array');
		$model	= $this->getModel();
		
		if (!$model->save($data)) {
			$this->setMessage($model->getError(), 'error');
		} else {
			$this->setMessage(JText::_('COM_RSFILES_SETTINGS_SAVED'), 'message');
		}
		
		$task = $this->getTask();
		if ($task == 'save') {
			$this->setRedirect(JRoute::_('index.php?option=com_rsfiles', false));
		} elseif ($task == 'apply') {
			$this->setRedirect(JRoute::_('index.php?option=com_rsfiles&view=settings', false));
		}
	}
	
	/**
	 * Method to save download folders
	 *
	 * @return	void
	 * @since	1.6
	 */
	public function savepath() {
		$model	= $this->getModel();
		
		// Save path
		if (!$model->savepath()) {
			$this->setMessage($model->getError(),'error');
			return $this->setRedirect('index.php?option=com_rsfiles&view=settings&layout=select&tmpl=component&type='.JFactory::getApplication()->input->getString('type'));
		}
		
		echo '<script type="text/javascript">window.parent.SqueezeBox.close();</script>';
		exit;
	}
	
	/**
	 * Method to get RSMail! list fields
	 *
	 * @return	json
	 * @since	2.5
	 */
	public function fields() {
		$model	= $this->getModel();
		
		echo $model->fields();
		die();
		
		rsfilesHelper::showResponse(true, $data);
	}
}