<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('_JEXEC') or die('Restricted access');

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
			return $this->setRedirect(rseventsproHelper::route('index.php?option=com_rseventspro&layout=show&id='.rseventsproHelper::sef($event->id,$event->name),false,rseventsproHelper::itemid($event->id)), JText::_('COM_RSEVENTSPRO_ERROR_EXPORT_SUBSCRIBERS'));
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
		} else $msg = JText::_('COM_RSEVENTSPRO_ERROR_SUBSCRIBER_STATUS');
		
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
		} else $msg = JText::_('COM_RSEVENTSPRO_ERROR_SUBSCRIBER_SAVE');
		
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
		} else $msg = JText::_('COM_RSEVENTSPRO_ERROR_SUBSCRIBER_DELETE');
		
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
				$url = rseventsproHelper::route('index.php?option=com_rseventspro&layout=show&id='.rseventsproHelper::sef($event->id,$event->name),false,rseventsproHelper::itemid($event->id));
				echo rseventsproHelper::redirect(true,JText::_('COM_RSEVENTSPRO_MESSAGES_SENT'),$url,true);
			}
		}
		else 
			return $this->setRedirect(rseventsproHelper::route('index.php?option=com_rseventspro&layout=show&id='.rseventsproHelper::sef($event->id,$event->name),false,rseventsproHelper::itemid($event->id)), JText::_('COM_RSEVENTSPRO_ERROR_SEND_MESSAGE_GUESTS'));
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
			return $this->setRedirect(rseventsproHelper::route('index.php?option=com_rseventspro&layout=show&id='.rseventsproHelper::sef($event->id,$event->name),false,rseventsproHelper::itemid($event->id)), JText::_('COM_RSEVENTSPRO_ERROR_INVITE_1'));
		
		$model->invite();
		$url = rseventsproHelper::route('index.php?option=com_rseventspro&layout=show&id='.rseventsproHelper::sef($event->id,$event->name),false,rseventsproHelper::itemid($event->id));
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
		
		echo $model->rate();
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
			JError::raiseError(500, JText::_('COM_RSEVENTSPRO_ERROR_EDIT_LOCATION'));
		
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
		
		$this->setRedirect(rseventsproHelper::route('index.php?option=com_rseventspro&layout=show&id='.rseventsproHelper::sef($result['id'],$result['name']),false,rseventsproHelper::itemid($result['id'])),$result['message']);
	}
	
	// Events - add location from event
	public function savelocation() {
		// Get the model
		$model = $this->getModel('rseventspro');
		$admin = rseventsproHelper::admin();
		
		if (!$admin && empty($this->permissions['can_add_locations']))
			JError::raiseError(500, JText::_('COM_RSEVENTSPRO_ERROR_ADD_LOCATION'));
		
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
			JError::raiseError(500, JText::_('COM_RSEVENTSPRO_ERROR_ADD_CATEGORY'));
		
		if ($model->savecategory()) {
			echo json_encode(JHtml::_('category.options','com_rseventspro', array('filter.published' => array(1))));
		}
			
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
			$tid = $model->saveticket();
			if (JFactory::getApplication()->input->get('format') == 'raw') {
				return $tid;
			} else {
				echo 'RS_DELIMITER0';
				echo $tid;
				echo 'RS_DELIMITER1';
			}
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
			JError::raiseError(500, JText::_('COM_RSEVENTSPRO_ERROR_REMOVE_TICKET'));
		
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
			$cid = $model->savecoupon();
			if (JFactory::getApplication()->input->get('format') == 'raw') {
				return $cid;
			} else {
				echo 'RS_DELIMITER0';
				echo $cid;
				echo 'RS_DELIMITER1';
			}
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
			JError::raiseError(500, JText::_('COM_RSEVENTSPRO_ERROR_REMOVE_COUPON'));
		
		echo 'RS_DELIMITER0';
		echo (int) $model->removecoupon();
		echo 'RS_DELIMITER1';
		
		JFactory::getApplication()->close();
	}
	
	// Events - save file
	public function savefile() {
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
			JError::raiseError(500, JText::_('COM_RSEVENTSPRO_ERROR_EDIT_FILE'));
		
		// Save
		$success = $model->savefile();
		
		echo (int) $success;
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
			JError::raiseError(500, JText::_('COM_RSEVENTSPRO_ERROR_REMOVE_FILE'));
		
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
		
		echo 'RS_DELIMITER0';
		if ($admin || $event->owner == $user || $event->sid == $user) {
			$model->deleteicon();
			echo '1';
		}
		else {
			echo '0';
		}
		echo 'RS_DELIMITER1';
		JFactory::getApplication()->close();
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
		
		if ($permission_denied) {
			JError::raiseError(500, JText::_('COM_RSEVENTSPRO_ERROR_UPLOAD_ICON'));
			return false;
		}
		
		
		if (!$model->upload()) {
			$icon = '';
			$this->setMessage($model->getError());
		} else {
			$icon = '&icon='.base64_encode($model->getState('com_rseventspro.edit.icon'));
		}
		
		return $this->setRedirect(JRoute::_('index.php?option=com_rseventspro&layout=upload&tmpl=component'.$icon.'&id='.JFactory::getApplication()->input->getInt('id'),false));
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
		
		if ($permission_denied) {
			JError::raiseError(500, JText::_('COM_RSEVENTSPRO_ERROR_CROP_ICON'));
			return false;
		}
		
		// Crop image
		$model->crop();
		
		$this->setMessage(JText::_('COM_RSEVENTSPRO_CROP_SAVED'));
		return $this->setRedirect(JRoute::_('index.php?option=com_rseventspro&layout=upload&tmpl=component&icon='.base64_encode($model->getState('rseventspro.crop.icon')).'&id='.JFactory::getApplication()->input->getInt('id'),false));
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
			JError::raiseError(500, JText::_('COM_RSEVENTSPRO_ERROR_REMOVE_EVENT'));
		
		$model->remove();
		
		// Hack for Joomla! SEF 
		$menus		= $this->_app->getMenu();
		$menu		= $menus->getActive(); 
		
		if (!empty($menu)) {
			$query = $menu->query;
			
			if (isset($query['option']) && $query['option'] == 'com_rseventspro' && isset($query['layout']) && $query['layout'] == 'edit') {
				if ($eventsItemid = RseventsproHelperRoute::getEventsItemid()) {
					return $this->_app->redirect(rseventsproHelper::route('index.php?option=com_rseventspro&layout=default',false,$eventsItemid), JText::_('COM_RSEVENTSPRO_EVENT_DELETED'));
				} else {
					return $this->_app->redirect(rseventsproHelper::route('index.php?option=com_rseventspro&layout=default',false,'99999'), JText::_('COM_RSEVENTSPRO_EVENT_DELETED'));
				}
			}
		}
		
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
		
		if ($permission_denied) {
			JError::raiseError(500, JText::_('COM_RSEVENTSPRO_GLOBAL_PERMISSION_DENIED'));
		}
		
		// Save
		if ($model->save()) {
			$moderated	= $model->getState('moderated');
			$eventID	= $model->getState('eventid');
			$eventName	= $model->getState('eventname');
			$link		= 'index.php?option=com_rseventspro&layout=edit&id='.rseventsproHelper::sef($eventID,$eventName).($tab ? '&tab='.$tab : '');
			$msg		= $moderated ? JText::_('COM_RSEVENTSPRO_EVENT_SAVED_WITH_MODERATION') : JText::_('COM_RSEVENTSPRO_EVENT_SAVED_OK');
			
			return $this->setRedirect(rseventsproHelper::route($link,false), $msg);
		} else {
			$msg	= $model->getError() ? $model->getError() : JText::_('COM_RSEVENTSPRO_EVENT_SAVED_ERROR');
			$link	= JUri::getInstance()->toString();
			
			return $this->setRedirect(JRoute::_($link,false), $msg, 'error');
		}
	}
	
	public function cancel() {
		// Check for request forgeries
		JSession::checkToken() or jexit('Invalid Token');
		
		$model = $this->getModel('rseventspro');
		$event = $model->getEvent();
		
		return $this->setRedirect(rseventsproHelper::route('index.php?option=com_rseventspro&layout=show&id='.rseventsproHelper::sef($event->id,$event->name),false,rseventsproHelper::itemid($event->id)));
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
	
	// Delete reports
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
	
	// Confirm a subscriber
	public function confirmsubscriber() {
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
		
		// Get the model
		$model = $this->getModel();
		
		echo 'RS_DELIMITER0';
		echo $model->confirmsubscriber();
		echo 'RS_DELIMITER1';
		JFactory::getApplication()->close();
	}
	
	// Get events for the map
	public function getajaxmap() {
		// Get the model
		$model = $this->getModel();
		
		header('Content-Type: application/json');
		
		echo json_encode($model->getMapItems());
		
		JFactory::getApplication()->close();
	}
	
	// Order tickets
	public function ticketsorder() {
		// Get the model
		$model = $this->getModel();
		
		// Get event details
		$event = $model->getEvent();
		
		$admin = rseventsproHelper::admin();
		$user  = $model->getUser();
		
		if ($admin || $event->owner == $user || $event->sid == $user) {
			// Save the tickets ordering
			$model->ticketsorder();
		}
		
		JFactory::getApplication()->close();
	}
}