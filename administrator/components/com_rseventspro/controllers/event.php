<?php
/**
* @version 1.0.0
* @package RSEvents!Pro 1.0.0
* @copyright (C) 2009-2012 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.controller');

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
		$success = $model->upload();
		
		echo '<script type="text/javascript">'."\n";
		echo 'window.parent.hm(\'box\')'."\n";
		
		if ($success !== true) {
			echo 'window.parent.$(\'rs_errors\').innerHTML = \'<p class="rs_error">'.$success.'</p>\''."\n";
		} else {
			$eventicon = $model->getState('com_rseventspro.edit.icon');
			
			$image = @getimagesize(JPATH_SITE.'/components/com_rseventspro/assets/images/events/'.$eventicon);
			$eimage = @getimagesize(JPATH_SITE.'/components/com_rseventspro/assets/images/events/thumbs/e_'.$eventicon);
			$ewidth = isset($eimage[0]) ? $eimage[0] : 800;
			$eheight = isset($eimage[1]) ? $eimage[1] : 380;
			$width = isset($image[0]) ? $image[0] : 800;
			$height = isset($image[1]) ? $image[1] : 380;
			$customheight = round(($height * ($width < 380 ? $width : 380)) / $width) + 50;
			$modal_height = ($height > $width ? $customheight : 500) + 50;
			
			echo 'window.parent.$(\'rs_errors\').innerHTML = \'\''."\n";
			echo 'window.parent.$(\'rs_icon_img\').set(\'src\',\''.JURI::root().'components/com_rseventspro/assets/images/events/thumbs/e_'.$eventicon.'?nocache='.uniqid('').'\')'."\n";
			echo $eheight > $ewidth ? 'window.parent.$(\'rs_icon_img\').set(\'height\',\'180\')'."\n" : 'window.parent.$(\'rs_icon_img\').erase(\'height\')'."\n";
			echo 'window.parent.rs_modal(\''.JRoute::_('index.php?option=com_rseventspro&view=event&layout=crop&tmpl=component&id='.JFactory::getApplication()->input->getInt('id'),false).'\',640,'.$modal_height.')'."\n";
		}
		echo '</script>'."\n";
		JFactory::getApplication()->close();
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
		$success = $model->crop();
		
		echo '<script type="text/javascript">'."\n";
		echo 'window.parent.hm(\'box\')'."\n";
		$eventicon = $model->getState('com_rseventspro.crop.icon');
		$image = @getimagesize(JPATH_SITE.'/components/com_rseventspro/assets/images/events/thumbs/e_'.$eventicon);
		$width = isset($image[0]) ? $image[0] : 800;
		$height = isset($image[1]) ? $image[1] : 380;
		
		echo 'window.parent.$(\'rs_icon_img\').set(\'src\',\''.JURI::root().'components/com_rseventspro/assets/images/events/thumbs/e_'.$eventicon.'?nocache='.uniqid('').'\')'."\n";
		echo $height > $width ? 'window.parent.$(\'rs_icon_img\').set(\'height\',\'180\')'."\n" : 'window.parent.$(\'rs_icon_img\').erase(\'height\')'."\n";
		echo '</script>'."\n";
		JFactory::getApplication()->close();
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
		
		echo '<script type="text/javascript">'."\n";
		echo 'window.parent.hm(\'box\')'."\n";
		echo 'window.parent.$(\'rs_file_'.$model->getState('com_rseventspro.file.id').'\').set(\'text\',\''.$model->getState('com_rseventspro.file.name').'\')'."\n";
		echo '</script>'."\n";
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
		echo 'window.parent.hm(\'box\')'."\n";
		echo '</script>'."\n";
		JFactory::getApplication()->close();
	}
}