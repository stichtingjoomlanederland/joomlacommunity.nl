<?php
/**
* @version 1.0.0
* @package RSEvents!Pro 1.0.0
* @copyright (C) 2009-2012 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.controller');

class rseventsproControllerRseventspro extends JControllerLegacy
{
	/**
	 *	Main constructor
	 */
	public function __construct() {
		parent::__construct();
		$this->_db			= JFactory::getDbo();
		$this->_app			= JFactory::getApplication();
		$this->permissions	= rseventsproHelper::permissions();
		
		$this->registerTask('approve',	'status');
		$this->registerTask('pending',	'status');
		$this->registerTask('denied',	'status');
	}
	
	// Subscribers - export subscribers
	public function exportguests() {
		// Get the model
		$model = $this->getModel('rseventspro');
		
		// Get event details
		$event = $model->getEvent();
		
		$admin = rseventsproHelper::admin();
		$user  = $model->getUser();
		
		if ($admin || $event->owner == $user || $event->sid == $user) {
			$model->exportguests();
		} else {
			return $this->setRedirect(rseventsproHelper::route('index.php?option=com_rseventspro&layout=show&id='.rseventsproHelper::sef($event->id,$event->name),false), JText::_('COM_RSEVENTSPRO_GLOBAL_PERMISSION_DENIED'));
		}
	}
	
	// Subscribers - change state
	public function status() {
		// Get the model
		$model = $this->getModel('rseventspro');
		
		// Get event details
		$event = $model->getEvent();
		
		$admin = rseventsproHelper::admin();
		$user  = $model->getUser();
		$msg   = '';
		
		if ($admin || $event->owner == $user || $event->sid == $user) {
			$id		= JFactory::getApplication()->input->getInt('id');
			$values	= array('approve' => 1, 'pending' => 0, 'denied' => 2);
			$task	= $this->getTask();
			$value	= JArrayHelper::getValue($values, $task, 0, 'int');
			
			if (!$model->status($id, $value))
				$this->setMessage($model->getError(), 'error');
			else
				$msg = JText::_('COM_RSEVENTSPRO_SUBSCRIBER_STATUS_CHANGED');
		} else $msg = JText::_('COM_RSEVENTSPRO_GLOBAL_PERMISSION_DENIED');
		
		// Clean the cache, if any
		$cache =& JFactory::getCache('com_rseventspro');
		$cache->clean();
		
		$this->setRedirect(rseventsproHelper::route('index.php?option=com_rseventspro&layout=subscribers&id='.rseventsproHelper::sef($event->id,$event->name),false),$msg);
	}
	
	// Subscribers - save subscriber
	public function savesubscriber() {
		// Check for request forgeries
		JSession::checkToken() or jexit('Invalid Token');
		
		// Get the model
		$model = $this->getModel('rseventspro');
		
		// Get event details
		$event = $model->getEvent();
		
		$admin = rseventsproHelper::admin();
		$user  = $model->getUser();
		
		if ($admin || $event->owner == $user || $event->sid == $user) {
			if (!$model->savesubscriber())
				$this->setMessage($model->getError(), 'error');
			else
				$msg = JText::_('COM_RSEVENTSPRO_SUBSCRIBER_SAVED');
		} else $msg = JText::_('COM_RSEVENTSPRO_GLOBAL_PERMISSION_DENIED');
		
		// Clean the cache, if any
		$cache =& JFactory::getCache('com_rseventspro');
		$cache->clean();
		
		$this->setRedirect(rseventsproHelper::route('index.php?option=com_rseventspro&layout=subscribers&id='.rseventsproHelper::sef($event->id,$event->name),false),$msg);
	}
	
	// Subscribers - remove
	public function removesubscriber() {
		// Get the model
		$model = $this->getModel('rseventspro');
		
		// Get event details
		$event = $model->getEvent();
		
		$admin = rseventsproHelper::admin();
		$user  = $model->getUser();
		
		if ($admin || $event->owner == $user || $event->sid == $user) {
			if (!$model->removesubscriber())
				$this->setMessage($model->getError(), 'error');
			else
				$msg = JText::_('COM_RSEVENTSPRO_SUBSCRIBER_DELETED');
		} else $msg = JText::_('COM_RSEVENTSPRO_GLOBAL_PERMISSION_DENIED');
		
		// Clean the cache, if any
		$cache =& JFactory::getCache('com_rseventspro');
		$cache->clean();
		
		$this->setRedirect(rseventsproHelper::route('index.php?option=com_rseventspro&layout=subscribers&id='.rseventsproHelper::sef($event->id,$event->name),false),$msg);
	}
	
	// Event - send message to guests
	public function message() {
		// Get the model
		$model = $this->getModel('rseventspro');
		
		// Get event details
		$event = $model->getEvent();
		
		$admin = rseventsproHelper::admin();
		$user  = $model->getUser();
		
		if ($admin || $event->owner == $user || $event->sid == $user) {
			if ($model->message()) {
				$url = rseventsproHelper::route('index.php?option=com_rseventspro&layout=show&id='.rseventsproHelper::sef($event->id,$event->name),false);
				echo rseventsproHelper::redirect(true,JText::_('COM_RSEVENTSPRO_MESSAGES_SENT'),$url,true);
			}
		}
		else 
			return $this->setRedirect(rseventsproHelper::route('index.php?option=com_rseventspro&layout=show&id='.rseventsproHelper::sef($event->id,$event->name),false), JText::_('COM_RSEVENTSPRO_GLOBAL_PERMISSION_DENIED'));
	}
	
	// Invite people to event
	public function invite() {
		// Get the model
		$model = $this->getModel('rseventspro');
		
		// Get event details
		$event = $model->getEvent();
		
		$nowunix = JFactory::getDate()->toUnix();
		$endunix = JFactory::getDate($event->end)->toUnix();
		
		if ($nowunix > $endunix)
			return $this->setRedirect(rseventsproHelper::route('index.php?option=com_rseventspro&layout=show&id='.rseventsproHelper::sef($event->id,$event->name),false), JText::_('COM_RSEVENTSPRO_GLOBAL_PERMISSION_DENIED'));
		
		$model->invite();
		$url = rseventsproHelper::route('index.php?option=com_rseventspro&layout=show&id='.rseventsproHelper::sef($event->id,$event->name),false);
		echo rseventsproHelper::redirect(true,JText::_('COM_RSEVENTSPRO_INVITATIONS_SENT'),$url,true);
	}
	
	// Event - export event
	public function export() {
		// Get the model
		$model = $this->getModel('rseventspro');
		
		// Export event
		if (!$model->export())
			return $this->setRedirect(rseventsproHelper::route('index.php?option=com_rseventspro&layout=default',false));
	}
	
	// Event - rate event
	public function rate() {
		// Get the model
		$model = $this->getModel('rseventspro');
		
		// Export event
		echo 'RS_DELIMITER0';
		echo $model->rate();
		echo 'RS_DELIMITER1';
		
		JFactory::getApplication()->close();
	}
	
	// Events - save edited location
	public function savelocations() {
		// Check for request forgeries
		JSession::checkToken() or jexit('Invalid Token');
		
		// Get the model
		$model = $this->getModel('rseventspro');
		
		$admin = rseventsproHelper::admin();
		if (empty($this->permissions['can_edit_locations']) && !$admin)
			JError::raiseError(500, JText::_('COM_RSEVENTSPRO_GLOBAL_PERMISSION_DENIED'));
		
		// Save location
		if (!$model->savelocation()) {
			$this->setMessage($model->getError(), 'error');
		} else {
			$this->setMessage(JText::_('COM_RSEVENTSPRO_LOCATION_SAVED'));
		}
		
		// Redirect
		$this->setRedirect(rseventsproHelper::route('index.php?option=com_rseventspro&layout=locations',false));
	}
	
	// Event - subscribe
	public function subscribe() {
		// Check for request forgeries
		JSession::checkToken() or jexit('Invalid Token');
		
		// Get the model
		$model = $this->getModel('rseventspro');
		
		// Subscribe user
		$result = $model->subscribe();
		
		echo rseventsproHelper::redirect(true,$result['message'],$result['url'],true);
	}
	
	// Event unsubscribe
	public function unsubscribeuser() {
		// Get the model
		$model = $this->getModel('rseventspro');
		
		// Unsubscribe user
		$result = $model->unsubscribeuser();
		
		if (!$result['status']) {
			$this->setRedirect($result['url'],$result['message']);
		} else {
			JFactory::getApplication()->input->set('tmpl','component');
			echo '<div class="rs_message_info">'.$result['message'].'</div>';
			if (rseventsproHelper::getConfig('modal','int') == 0 || rseventsproHelper::getConfig('modal','int') == 2)
				echo '<script type="text/javascript">window.top.setTimeout(\'window.parent.location.reload();window.parent.SqueezeBox.close();\',1200);</script>';
			else
				echo '<script type="text/javascript">window.top.setTimeout(\'window.parent.location.reload();window.parent.jQuery.colorbox.close();\',1200);</script>';
		}
	}
	
	// Event unsubscribe
	public function unsubscribe() {
		// Get the model
		$model = $this->getModel('rseventspro');
		
		// Unsubscribe user
		$result = $model->unsubscribe();
		
		$this->setRedirect(rseventsproHelper::route('index.php?option=com_rseventspro&layout=show&id='.rseventsproHelper::sef($result['id'],$result['name']),false),$result['message']);
	}
	
	// Events - add location from event
	public function savelocation() {
		// Get the model
		$model = $this->getModel('rseventspro');
		$admin = rseventsproHelper::admin();
		
		if (!$admin && empty($this->permissions['can_add_locations']))
			JError::raiseError(500, JText::_('COM_RSEVENTSPRO_GLOBAL_PERMISSION_DENIED'));
		
		if ($model->savelocation())
			echo $model->getState('rseventspro.lid');
		
		JFactory::getApplication()->close();
	}
	
	// Events - save category
	public function savecategory() {
		// Get the model
		$model = $this->getModel('rseventspro');
		$admin = rseventsproHelper::admin();
		
		if (!$admin && empty($this->permissions['can_create_categories']))
			JError::raiseError(500, JText::_('COM_RSEVENTSPRO_GLOBAL_PERMISSION_DENIED'));
		
		if ($model->savecategory())
			echo $model->getState('rseventspro.cid');
			
		JFactory::getApplication()->close();
	}
	
	// Event - save ticket
	public function saveticket() {
		// Get the model
		$model = $this->getModel('rseventspro');
		
		// Get event details
		$event = $model->getEvent();
		
		$admin = rseventsproHelper::admin();
		$user  = $model->getUser();
		
		if ($admin || $event->owner == $user || $event->sid == $user) {
			echo 'RS_DELIMITER0';
			echo $model->saveticket();
			echo 'RS_DELIMITER1';
		}
		
		JFactory::getApplication()->close();
	}
	
	// Events - remove ticket
	public function removeticket() {		
		// Get the model
		$model = $this->getModel('rseventspro');
		
		$permission_denied = false;
		$admin = rseventsproHelper::admin();
		
		if (!$admin) {
			JFactory::getApplication()->input->set('from','ticket');
			$user = $model->getUser();
			$owner = $model->getOwner();
			if (($user != $owner && empty($this->permissions['can_edit_events'])) || empty($user))
				$permission_denied = true;
		}
		if ($permission_denied)
			JError::raiseError(500, JText::_('COM_RSEVENTSPRO_GLOBAL_PERMISSION_DENIED'));
		
		echo 'RS_DELIMITER0';
		echo (int) $model->removeticket();
		echo 'RS_DELIMITER1';
		
		JFactory::getApplication()->close();
	}
	
	// Event - save coupon
	public function savecoupon() {
		// Get the model
		$model = $this->getModel('rseventspro');
		
		// Get event details
		$event = $model->getEvent();
		
		$admin = rseventsproHelper::admin();
		$user  = $model->getUser();
		
		if ($admin || $event->owner == $user || $event->sid == $user) {
			echo 'RS_DELIMITER0';
			echo $model->savecoupon();
			echo 'RS_DELIMITER1';
		}
		
		JFactory::getApplication()->close();
	}
	
	// Events - remove coupon
	public function removecoupon() {	
		// Get the model
		$model = $this->getModel('rseventspro');
		
		$permission_denied = false;
		$admin = rseventsproHelper::admin();
		
		if (!$admin) {
			JFactory::getApplication()->input->set('from','coupon');
			$user = $model->getUser();
			$owner = $model->getOwner();
			if (($user != $owner && empty($this->permissions['can_edit_events'])) || empty($user))
				$permission_denied = true;
		}
		
		if ($permission_denied)
			JError::raiseError(500, JText::_('COM_RSEVENTSPRO_GLOBAL_PERMISSION_DENIED'));
		
		echo 'RS_DELIMITER0';
		echo (int) $model->removecoupon();
		echo 'RS_DELIMITER1';
		
		JFactory::getApplication()->close();
	}
	
	// Events - save file
	public function savefile() {
		// Check for request forgeries
		JSession::checkToken() or jexit('Invalid Token');
		
		// Get the model
		$model = $this->getModel('rseventspro');
		
		$permission_denied = false;
		$admin = rseventsproHelper::admin();
		
		if (!$admin) {
			JFactory::getApplication()->input->set('from','file');
			$user = $model->getUser();
			$owner = $model->getOwner();		
			if (($user != $owner && empty($this->permissions['can_edit_events'])) || empty($user))
				$permission_denied = true;
		}
		
		if ($permission_denied)
			JError::raiseError(500, JText::_('COM_RSEVENTSPRO_GLOBAL_PERMISSION_DENIED'));
		
		// Save
		$model->savefile();
		
		echo '<script type="text/javascript">'."\n";
		echo 'window.parent.hm(\'box\')'."\n";
		echo 'window.parent.$(\'rs_file_'.$model->getState('com_rseventspro.file.id').'\').set(\'text\',\''.$model->getState('com_rseventspro.file.name').'\')'."\n";
		echo '</script>'."\n";
		JFactory::getApplication()->close();
	}
	
	// Events - remove file
	public function removefile() {
		// Get the model
		$model = $this->getModel('rseventspro');
		
		$permission_denied = false;
		$admin = rseventsproHelper::admin();
		
		if (!$admin) {
			JFactory::getApplication()->input->set('from','file');
			$user = $model->getUser();
			$owner = $model->getOwner();		
			if (($user != $owner && empty($this->permissions['can_edit_events'])) || empty($user))
				$permission_denied = true;
		}
		
		if ($permission_denied)
			JError::raiseError(500, JText::_('COM_RSEVENTSPRO_GLOBAL_PERMISSION_DENIED'));
		
		echo 'RS_DELIMITER0';
		echo (int) $model->removefile();
		echo 'RS_DELIMITER1';
		
		JFactory::getApplication()->close();
	}
	
	// Delete event icon
	public function deleteicon() {
		// Get the model
		$model = $this->getModel('rseventspro');
		
		// Get event
		$event = $model->getEvent();
		
		$admin = rseventsproHelper::admin();
		$user = $model->getUser();
		
		if ($admin || $event->owner == $user || $event->sid == $user) {
			$model->deleteicon();
			return $this->setRedirect(rseventsproHelper::route('index.php?option=com_rseventspro&layout=edit&id='.rseventsproHelper::sef($event->id,$event->name),false), JText::_('COM_RSEVENTSPRO_EVENT_ICON_DELETED'));
		}
		else 
			return $this->setRedirect(rseventsproHelper::route('index.php?option=com_rseventspro&layout=show&id='.rseventsproHelper::sef($event->id,$event->name),false), JText::_('COM_RSEVENTSPRO_GLOBAL_PERMISSION_DENIED'));
	}
	
	// Events - upload
	public function upload() {
		// Check for request forgeries
		JSession::checkToken() or jexit('Invalid Token');
		
		// Get the model
		$model = $this->getModel('rseventspro');
		
		$permission_denied = false;
		$admin = rseventsproHelper::admin();
		
		if (!$admin) {
			$user = $model->getUser();
			$owner = $model->getOwner();
			if (($user != $owner && empty($this->permissions['can_edit_events'])) || empty($user))
				$permission_denied = true;
		}
		
		if ($permission_denied)
			JError::raiseError(500, JText::_('COM_RSEVENTSPRO_GLOBAL_PERMISSION_DENIED'));
		
		// Save
		$result = $model->upload();
		
		echo '<script type="text/javascript">'."\n";
		echo 'window.parent.hm(\'box\')'."\n";
		
		if ($result !== true) {
			echo 'window.parent.$(\'rs_errors\').innerHTML = \'<p class="rs_error">'.$result.'</p>\''."\n";
		} else {
			$eventicon = $model->getState('rseventspro.icon');
			
			$image = @getimagesize(JPATH_SITE.'/components/com_rseventspro/assets/images/events/'.$eventicon);
			$eimage = @getimagesize(JPATH_SITE.'/components/com_rseventspro/assets/images/events/thumbs/e_'.$eventicon);
			$ewidth = isset($eimage[0]) ? $eimage[0] : 800;
			$eheight = isset($eimage[1]) ? $eimage[1] : 380;
			$width = isset($image[0]) ? $image[0] : 800;
			$height = isset($image[1]) ? $image[1] : 380;
			$customheight = round(($height * ($width < 380 ? $width : 380)) / $width) + 50;
			$modal_height = ($height > $width ? $customheight : 500) + 50;
			
			echo 'window.parent.$(\'rs_errors\').innerHTML = \'\''."\n";
			echo 'window.parent.$(\'rs_icon_img\').src = \''.JURI::root().'components/com_rseventspro/assets/images/events/thumbs/e_'.$eventicon.'?nocache='.uniqid('').'\''."\n";
			echo $eheight > $ewidth ? 'window.parent.$(\'rs_icon_img\').set(\'height\',\'180\')'."\n" : 'window.parent.$(\'rs_icon_img\').erase(\'height\')'."\n";
			echo 'window.parent.rs_modal(\''.rseventsproHelper::route('index.php?option=com_rseventspro&layout=crop&id='.$model->getState('rseventspro.eid').'&tmpl=component',false).'\',640,'.$modal_height.')'."\n";
		}
		echo '</script>'."\n";
		
		JFactory::getApplication()->close();
	}
	
	// Events - crop
	public function crop() {
		// Check for request forgeries
		JSession::checkToken() or jexit('Invalid Token');
		
		// Get the model
		$model = $this->getModel('rseventspro');
		
		$permission_denied = false;
		$admin = rseventsproHelper::admin();
		
		if (!$admin) {
			$user = $model->getUser();
			$owner = $model->getOwner();
			if (($user != $owner && empty($this->permissions['can_edit_events'])) || empty($user))
				$permission_denied = true;
		}
		
		if ($permission_denied)
			JError::raiseError(500, JText::_('COM_RSEVENTSPRO_GLOBAL_PERMISSION_DENIED'));
		
		// Save
		$result = $model->crop();
		
		echo '<script type="text/javascript">'."\n";
		echo 'window.parent.hm(\'box\')'."\n";
		$eventicon = $model->getState('rseventspro.crop.icon');
		
		$image = @getimagesize(JPATH_SITE.'/components/com_rseventspro/assets/images/events/thumbs/e_'.$eventicon);
		$width = isset($image[0]) ? $image[0] : 800;
		$height = isset($image[1]) ? $image[1] : 380;
		
		echo 'window.parent.$(\'rs_icon_img\').src = \''.JURI::root().'components/com_rseventspro/assets/images/events/thumbs/e_'.$eventicon.'?nocache='.uniqid('').'\''."\n";
		echo $height > $width ? 'window.parent.$(\'rs_icon_img\').set(\'height\',\'180\')'."\n" : 'window.parent.$(\'rs_icon_img\').erase(\'height\')'."\n";
		echo '</script>'."\n";
		
		JFactory::getApplication()->close();
	}
	
	// Events - remove
	public function remove() {
		// Get the model
		$model = $this->getModel('rseventspro');
		
		$permission_denied = false;
		$admin = rseventsproHelper::admin();
		
		if (!$admin) {
			$user  = $model->getUser();
			$owner = $model->getOwner();
			if (($user != $owner && empty($this->permissions['can_delete_events'])) || empty($user))
				$permission_denied = true;
		}
		
		if ($permission_denied)
			JError::raiseError(500, JText::_('COM_RSEVENTSPRO_GLOBAL_PERMISSION_DENIED'));
		
		$model->remove();
		
		// Redirect
		$this->_app->redirect(rseventsproHelper::route('index.php?option=com_rseventspro&layout=default',false), JText::_('COM_RSEVENTSPRO_EVENT_DELETED'));
	}
	
	// Events - save event
	public function save() {
		// Check for request forgeries
		JSession::checkToken() or jexit('Invalid Token');
		
		// Get the model
		$model = $this->getModel('rseventspro');
		
		// Get the id of the event
		$jform = JFactory::getApplication()->input->get('jform', array(), 'array');
		$id		= (int) $jform['id'];
		$tab	= JFactory::getApplication()->input->getInt('tab');
		
		$permission_denied = false;
		$admin = rseventsproHelper::admin();
		
		if (!$admin) {
			if (!$id) {
				if (empty($this->permissions['can_post_events']))
					$permission_denied = true;
			} else {
				$user = $model->getUser();
				$owner = $model->getOwner();
				if (($user != $owner && empty($this->permissions['can_edit_events'])) || empty($user))
					$permission_denied = true;
			}
		}
		
		if ($permission_denied)
			JError::raiseError(500, JText::_('COM_RSEVENTSPRO_GLOBAL_PERMISSION_DENIED'));
		
		// Save
		if ($model->save()) {
			$eventID	= $model->getState('eventid');
			$eventName	= $model->getState('eventname');
			$link		= 'index.php?option=com_rseventspro&layout=edit&id='.rseventsproHelper::sef($eventID,$eventName).($tab ? '&tab='.$tab : '');
			
			return $this->setRedirect(rseventsproHelper::route($link,false), JText::_('COM_RSEVENTSPRO_EVENT_SAVED_OK'));
		} else {
			$link		= 'index.php?option=com_rseventspro&layout=default';
			return $this->setRedirect(rseventsproHelper::route($link,false), JText::_('COM_RSEVENTSPRO_EVENT_SAVED_ERROR'));
		}
	}
	
	/**
	 * Method to save tickets position
	 *
	 * @return	javascript
	 */
	public function tickets() {
		// Get the model
		$model = $this->getModel();
		
		// Get event details
		$event = $model->getEvent();
		
		$admin = rseventsproHelper::admin();
		$user  = $model->getUser();
		
		if ($admin || $event->owner == $user || $event->sid == $user) {
			// Save the tickets configuration
			$model->tickets();
		}
		
		echo '<script type="text/javascript">'."\n";
		echo 'window.parent.hm(\'box\')'."\n";
		echo '</script>'."\n";
		JFactory::getApplication()->close();
	}
	
	/**
	 * Method to save the report message
	 *
	 * @return	javascript
	 */
	public function report() {
		// Check for request forgeries
		JSession::checkToken() or jexit('Invalid Token');
		
		// Get the model
		$model = $this->getModel();
		
		// Report 
		if ($model->getCanreport()) {
			$model->report();
			echo '<div class="rs_message_info">'.JText::_('COM_RSEVENTSPRO_REPORT_ADDED').'</div>';
			echo '<script type="text/javascript">window.top.setTimeout(\'window.parent.SqueezeBox.close();\',1200);</script>';
		} else {
			echo '<script type="text/javascript">'."\n";
			echo 'window.parent.SqueezeBox.close();'."\n";
			echo '</script>'."\n";
		}
	}
	
	public function deletereports() {
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
		
		// Get the selected items
		$pks = JFactory::getApplication()->input->get('cid', array(0), 'array');
		
		$model = $this->getModel();
		
		// Get event details
		$event = $model->getEvent();
		
		// Force array elements to be integers
		JArrayHelper::toInteger($pks);
		
		$admin = rseventsproHelper::admin();
		$user  = $model->getUser();
		
		// Delete reports
		if ($model->getCanreport()) {
			if ($admin || $event->owner == $user || $event->sid == $user) {
				$model->deletereports($pks);
			}
		}
		
		$this->setRedirect(base64_decode(JFactory::getApplication()->input->getString('return')), JText::_('COM_RSEVENTSPRO_REPORTS_DELETED'));
	}
}