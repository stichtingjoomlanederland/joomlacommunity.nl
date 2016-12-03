<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('_JEXEC') or die('Restricted access');

class rseventsproControllerEvent extends JControllerForm
{
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
	
	/**
	 * Method to add a new record.
	 *
	 * @return  mixed  True if the record can be added, a JError object if not.
	 *
	 * @since   11.1
	 */
	public function add() {
		// Get the model
		$model = $this->getModel();
		
		// Get data
		$data = JFactory::getApplication()->input->get('jform',array(),'array');
		
		// Save event
		$model->save($data);
		
		// Redirect
		$this->setRedirect('index.php?option=com_rseventspro&task=event.edit&id='.$model->getState('event.id'));
	}
	
	/**
	 * Gets the URL arguments to append to an item redirect.
	 *
	 * @param   integer  $recordId  The primary key id for the item.
	 * @param   string   $urlVar    The name of the URL variable for the id.
	 *
	 * @return  string  The arguments to append to the redirect URL.
	 *
	 * @since   11.1
	 */
	protected function getRedirectToItemAppend($recordId = null, $urlVar = 'id') {
		$append = parent::getRedirectToItemAppend($recordId, $urlVar);
		$append .= '&tab='.JFactory::getApplication()->input->getInt('tab',0);
		
		return $append;
	}
	
	/**
	 * Method to remove ticket
	 *
	 * @return	string
	 */
	public function removeticket() {
		// Get the model
		$model = $this->getModel();
		
		// Remove the ticket
		$success = $model->removeticket();
		
		echo 'RS_DELIMITER0';
		if ($success) echo 1; else echo 0;
		echo 'RS_DELIMITER1';
		JFactory::getApplication()->close();
	}
	
	/**
	 * Method to remove coupon
	 *
	 * @return	string
	 */
	public function removecoupon() {
		// Get the model
		$model = $this->getModel();
		
		// Remove the coupon
		$success = $model->removecoupon();
		
		echo 'RS_DELIMITER0';
		if ($success) echo 1; else echo 0;
		echo 'RS_DELIMITER1';
		JFactory::getApplication()->close();
	}
	
	/**
	 * Method to upload event icon.
	 *
	 * @return	javascript
	 */
	public function upload() {
		// Get the model
		$model = $this->getModel();
		
		// Upload event icon
		if (!$model->upload()) {
			$icon = '';
			$this->setMessage($model->getError());
		} else {
			$icon = '&icon='.base64_encode($model->getState('com_rseventspro.edit.icon'));
		}
		
		return $this->setRedirect(JRoute::_('index.php?option=com_rseventspro&view=event&layout=upload&tmpl=component'.$icon.'&id='.JFactory::getApplication()->input->getInt('id'),false));
	}
	
	/**
	 * Method to delete event icon.
	 *
	 * @return	int
	 */
	public function deleteicon() {
		// Get the model
		$model = $this->getModel();
		
		// Remove the event icon
		$success = $model->deleteicon();
		
		echo 'RS_DELIMITER0';
		if ($success) echo 1; else echo 0;
		echo 'RS_DELIMITER1';
		JFactory::getApplication()->close();
	}
	
	/**
	 * Method to crop the event icon.
	 *
	 * @return	javascript
	 */
	public function crop() {
		// Get the model
		$model = $this->getModel();
		
		// Crop the event icon
		$model->crop();
		
		$this->setMessage(JText::_('COM_RSEVENTSPRO_CROP_SAVED'));
		return $this->setRedirect(JRoute::_('index.php?option=com_rseventspro&view=event&layout=upload&tmpl=component&icon='.base64_encode($model->getState('com_rseventspro.crop.icon')).'&id='.JFactory::getApplication()->input->getInt('id'),false));
	}
	
	/**
	 * Method to save file details
	 *
	 * @return	javascript
	 */
	public function savefile() {
		// Get the model
		$model = $this->getModel();
		
		// Save the event file info
		$success = $model->savefile();
		
		echo (int) $success;
		JFactory::getApplication()->close();
	}
	
	/**
	 * Method to remove event files
	 *
	 * @return	int
	 */
	public function removefile() {
		// Get the model
		$model = $this->getModel();
		
		// Remove event files
		$success = $model->removefile();
		
		echo 'RS_DELIMITER0';
		if ($success) echo 1; else echo 0;
		echo 'RS_DELIMITER1';
		JFactory::getApplication()->close();
	}
	
	/**
	 * Method to save tickets position
	 *
	 * @return	javascript
	 */
	public function tickets() {
		// Get the model
		$model = $this->getModel();
		
		// Save the tickets configuration
		$model->tickets();
		
		echo '<script type="text/javascript">'."\n";
		echo 'window.parent.SqueezeBox.close()'."\n";
		echo '</script>'."\n";
		JFactory::getApplication()->close();
	}
	
	/**
	 * Method to save tickets ordering
	 *
	 * @return	void
	 */
	public function ticketsorder() {
		// Get the model
		$model = $this->getModel();
		
		// Save the tickets ordering
		$model->ticketsorder();
		
		JFactory::getApplication()->close();
	}
}