<?php
/**
* @version 1.0.0
* @package RSEvents!Pro 1.0.0
* @copyright (C) 2011 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined( '_JEXEC' ) or die( 'Restricted access' ); 
jimport( 'joomla.application.component.view');

class rseventsproViewRseventspro extends JViewLegacy
{
	public function display($tpl = null) {
		
		JHTML::_('behavior.modal');
		
		$lists = array();
		$db			= JFactory::getDbo();
		$query		= $db->getQuery(true);
		$doc		= JFactory::getDocument();
		$app		= JFactory::getApplication();
		$user		= JFactory::getUser();
		$layout		= $this->getLayout();
		$pathway	= $app->getPathWay();
		$menus		= $app->getMenu();
		$menu		= $menus->getActive();
		
		// Get menu parameters , user permission etc.
		$this->now			= rseventsproHelper::date('now','Y-m-d H:i:s');
		$this->user			= $this->get('User');
		$this->admin		= rseventsproHelper::admin();
		$this->params		= rseventsproHelper::getParams();
		$this->permissions	= rseventsproHelper::permissions();
		$this->pdf			= rseventsproHelper::pdf();
		$this->config		= rseventsproHelper::getConfig();
		
		// Load RSEvents!Pro plugins
		rseventsproHelper::loadPlugins();
		
		// Add Joomla! 2.5 menu metadata
		if ($this->params->get('menu-meta_description'))
			$doc->setDescription($this->params->get('menu-meta_description'));

		if ($this->params->get('menu-meta_keywords'))
			$doc->setMetadata('keywords', $this->params->get('menu-meta_keywords'));

		if ($this->params->get('robots'))
			$doc->setMetadata('robots', $this->params->get('robots'));
		
		// Add search bar
		if ($this->params->get('search',1)) {
			$doc->addScript(JURI::root(true).'/components/com_rseventspro/assets/js/select.js');
			$doc->addCustomTag('<!--[if IE 7]>
			<style type="text/css">
				.elSelect { width: 200px; }
				.optionsContainer { margin-top: -30px; }
				.selectedOption { width: 165px !important; }
			</style>
			<![endif]-->');
		
			$lists['filter_from']		= JHtml::_('select.genericlist', $this->get('FilterOptions'), 'filter_from[]', 'size="1"','value','text');
			$lists['filter_condition']	= JHtml::_('select.genericlist', $this->get('FilterConditions'), 'filter_condition[]', 'size="1"','value','text');
		}
		
		if ($layout == 'default') {
			// Get events
			$this->events	= $this->get('events');
			$this->total	= $this->get('total');
			
			// Add custom scripts
			$doc->addScript(JURI::root(true).'/components/com_rseventspro/assets/js/dom.js');
			$doc->addScript(JURI::root(true).'/components/com_rseventspro/assets/js/scripts.js');
			
			// Add feed
			if ($this->params->get('rss',1)) {
				$link	= '&format=feed';
				$attribs = array('type' => 'application/rss+xml', 'title' => 'RSS 2.0');
				$doc->addHeadLink(JRoute::_($link.'&type=rss'), 'alternate', 'rel', $attribs);
				$attribs = array('type' => 'application/atom+xml', 'title' => 'Atom 1.0');
				$doc->addHeadLink(JRoute::_($link.'&type=atom'), 'alternate', 'rel', $attribs);
			}
			
			$filters			= $this->get('filters');
			$this->columns		= $filters[0];
			$this->operators	= $filters[1];
			$this->values		= $filters[2];
			$this->category		= $this->get('EventCategory');
			
			//set the pathway
			if (!$menu) 
				$pathway->addItem(JText::_('COM_RSEVENTSPRO_BC_EVENTS'));
			
		} elseif ($layout == 'edit') {
		
			JHTML::_('behavior.tooltip');
			
			// Check for permission
			$id				= $app->input->getInt('id');
			$this->owner	= $this->get('owner');
			$this->states	= array('published' => true, 'unpublished' => true, 'archived' => false, 'trash' => false, 'all' => false);
			
			$this->tab	= $app->input->getInt('tab');
			$this->row	= $this->get('Event');
			
			$permission_denied = false;
			if (!$this->admin) {
				if (!$id) {
					if (empty($this->permissions['can_post_events']))
						$permission_denied = true;
				} else {
					if (($this->user != $this->owner && empty($this->permissions['can_edit_events'])) || empty($this->user))
						$permission_denied = true;
				}
			}
			
			if ($permission_denied) {
				rseventsproHelper::error(JText::_('COM_RSEVENTSPRO_GLOBAL_PERMISSION_DENIED'), rseventsproHelper::route('index.php?option=com_rseventspro&layout=show&id='.rseventsproHelper::sef($this->row->id,$this->row->name),false));
			}
			
			if ($id == 0) {
				// Set new state
				$app->input->set('new',1);
				// Get the model
				$model = $this->getModel();
				// Save event
				$model->save();
				// Redirect
				$app->redirect(rseventsproHelper::route('index.php?option=com_rseventspro&layout=edit&id='.rseventsproHelper::sef($model->getState('eventid'),$model->getState('eventname')),false));
			}
			
			// Load scripts
			rseventsproHelper::loadEditEvent();
			
			rseventsproHelper::chosen();
			
			// Load custom scripts
			$app->triggerEvent('rsepro_addCustomScripts');
			
			if (rseventsproHelper::getConfig('enable_google_maps'))
				$doc->addScript('https://maps.google.com/maps/api/js?sensor=false');
			
			require_once JPATH_SITE.'/components/com_rseventspro/helpers/events.php';
			$this->eventClass = RSEvent::getInstance($this->row->id);
			
			//set the pathway
			$theview = isset($menu->query['layout']) ? $menu->query['layout'] : 'rseventspro';
			if (($menu && $theview != 'show') || !$menu)
				$pathway->addItem($this->row->name,rseventsproHelper::route('index.php?option=com_rseventspro&layout=show&id='.rseventsproHelper::sef($this->row->id,$this->row->name),false));
			
			$pathway->addItem(JText::_('COM_RSEVENTSPRO_BC_EDIT_EVENT'));
			
		} elseif ($layout == 'file') {
		
			$app->input->set('from','file');
			$this->owner = $this->get('owner');
			$permission_denied = false;
			
			if (!$this->admin) {
				if (($this->user != $this->owner && empty($this->permissions['can_edit_events'])) || empty($this->user))
					$permission_denied = true;
			}
			
			if ($permission_denied) {
				rseventsproHelper::error(JText::_('COM_RSEVENTSPRO_GLOBAL_PERMISSION_DENIED'), rseventsproHelper::route('index.php?option=com_rseventspro&layout=default',false));
			}
			
			$this->row = $this->get('file');
			
		} elseif ($layout == 'upload') {
			
			$this->owner = $this->get('owner');
			$permission_denied = false;
			
			if (!$this->admin) {
				if (($this->user != $this->owner && empty($this->permissions['can_edit_events'])) || empty($this->user))
					$permission_denied = true;
			}
			
			if ($permission_denied) {
				rseventsproHelper::error(JText::_('COM_RSEVENTSPRO_GLOBAL_PERMISSION_DENIED'), rseventsproHelper::route('index.php?option=com_rseventspro&layout=default',false));
			}
			
			$this->id			= $app->input->getInt('id');
			$this->icon			= $this->get('icon');
			
		} elseif ($layout == 'crop') {
			
			$this->owner = $this->get('owner');
			$permission_denied = false;
			
			if (!$this->admin) {
				if (($this->user != $this->owner && empty($this->permissions['can_edit_events'])) || empty($this->user))
					$permission_denied = true;
			}
			
			if ($permission_denied) {
				rseventsproHelper::error(JText::_('COM_RSEVENTSPRO_GLOBAL_PERMISSION_DENIED'), rseventsproHelper::route('index.php?option=com_rseventspro&layout=default',false));
			}
			
			// Load scripts
			rseventsproHelper::loadEditEvent(false,true);
			
			$this->id			= $app->input->getInt('id');
			$this->icon			= $this->get('icon');
			$this->properties	= $this->get('properties');
			
		} elseif ($layout == 'forms') {
			
			$this->owner = $this->get('owner');
			$permission_denied = false;
			
			if (!$this->admin) {
				if (($this->user != $this->owner && empty($this->permissions['can_edit_events'])) || empty($this->user))
					$permission_denied = true;
			}
			
			if ($permission_denied) {
				rseventsproHelper::error(JText::_('COM_RSEVENTSPRO_GLOBAL_PERMISSION_DENIED'), rseventsproHelper::route('index.php?option=com_rseventspro&layout=default',false));
			}
			
			$this->forms		= $this->get('forms');
			$this->pagination	= $this->get('formspagination');
			
		} elseif ($layout == 'location') {
			
			if (rseventsproHelper::getConfig('enable_google_maps'))
				$doc->addScript('https://maps.google.com/maps/api/js?sensor=false');
			
			$this->row = $this->get('location');
			
			//set the pathway
			$pathway->addItem(JText::_('COM_RSEVENTSPRO_BC_LOCATION'));
			
		} elseif ($layout == 'locations') {
			
			$doc->addScript(JURI::root(true).'/components/com_rseventspro/assets/js/dom.js');
			$this->locations	= $this->get('locations');
			$this->total		= $this->get('totallocations');
			
		} elseif ($layout == 'editlocation') {
			
			if (empty($this->permissions['can_edit_locations']) && !$this->admin) {
				rseventsproHelper::error(JText::_('COM_RSEVENTSPRO_GLOBAL_PERMISSION_DENIED'), rseventsproHelper::route('index.php?option=com_rseventspro&layout=locations',false));
			}
			
			if (rseventsproHelper::getConfig('enable_google_maps','int'))
				$doc->addScript('https://maps.google.com/maps/api/js?sensor=false');
			
			rseventsproHelper::chosen();
			
			// Load custom scripts
			$app->triggerEvent('rsepro_addCustomScripts');
			
			if (rseventsproHelper::isGallery()) {
				$doc->addStyleSheet(JURI::root(true).'/components/com_rseventspro/assets/css/edit.css');
			}
			
			$this->row			= $this->get('location');
			
			//set the pathway
			$pathway->addItem(JText::_('COM_RSEVENTSPRO_BC_EDIT_LOCATION'));
			
		} elseif ($layout == 'categories') {
		
			$doc->addScript(JURI::root(true).'/components/com_rseventspro/assets/js/dom.js');
			$this->categories	= $this->get('categories');
			$this->total		= $this->get('totalcategories');
		
		} elseif ($layout == 'map') {
		
			if (rseventsproHelper::getConfig('enable_google_maps'))
				$doc->addScript('https://maps.google.com/maps/api/js?sensor=false');
			
			$filters			= $this->get('filters');
			$this->columns		= $filters[0];
			$this->operators	= $filters[1];
			$this->values		= $filters[2];
			
			$this->events = $this->get('eventsmap');
		
		} elseif ($layout == 'show') {
			if (!rseventsproHelper::check(JFactory::getApplication()->input->getInt('id')))	{			
				rseventsproHelper::error(JText::_('COM_RSEVENTSPRO_GLOBAL_PERMISSION_DENIED'), rseventsproHelper::route('index.php?option=com_rseventspro&layout=default',false));
			}
			
			// Get the event
			$this->event			= $this->get('event');
			$this->cansubscribe		= $this->get('cansubscribe');
			$this->issubscribed		= $this->get('issubscribed');
			$this->canunsubscribe	= $this->get('canunsubscribe');
			$this->report			= $this->get('canreport');
			
			if (!rseventsproHelper::canview($this->event->id) && $this->event->owner != $this->user && !$this->admin) {
				rseventsproHelper::error(JText::_('COM_RSEVENTSPRO_GLOBAL_PERMISSION_DENIED'), rseventsproHelper::route('index.php?option=com_rseventspro&layout=default',false));
			}
			
			// Load event metadata
			rseventsproHelper::metas($this->event);
			
			$this->options	= rseventsproHelper::options($this->event->id);
			$this->guests	= $this->get('guests');
			
			// Load jQuery
			if (rseventsproHelper::getConfig('modal','int') == 1) {
				if (!rseventsproHelper::isJ3())
					$doc->addCustomTag('<script type="text/javascript" src="'.JURI::root().'components/com_rseventspro/assets/js/colorbox/jquery-1.5.1.min.js"></script>');
				$doc->addCustomTag('<script type="text/javascript" src="'.JURI::root().'components/com_rseventspro/assets/js/colorbox/jquery.colorbox-min.js"></script>');
				$doc->addStyleSheet(JURI::root().'components/com_rseventspro/assets/js/colorbox/colorbox.css');
				$doc->addCustomTag('<script type="text/javascript">jQuery.noConflict();</script>');
				$doc->addCustomTag('<script type="text/javascript">jQuery(document).ready(function(){
					jQuery("a[rel=\'rs_subscribe\']").colorbox({iframe:true, innerWidth:650, innerHeight:450 , title:\''.JText::_('COM_RSEVENTSPRO_EVENT_JOIN',true).'\'});
					jQuery("a[rel=\'rs_invite\']").colorbox({iframe:true, innerWidth:530, innerHeight:500, title:\''.JText::_('COM_RSEVENTSPRO_EVENT_INVITE',true).'\'});
					jQuery("a[rel=\'rs_message\']").colorbox({iframe:true, innerWidth:550, innerHeight:400, title:\''.JText::_('COM_RSEVENTSPRO_EVENT_MESSAGE_TO_GUESTS',true).'\'});
					jQuery("a[rel=\'rs_unsubscribe\']").colorbox({iframe:true, innerWidth:550, innerHeight:400, title:\''.JText::_('COM_RSEVENTSPRO_UNSUBSCRIBE_UNSUBSCRIBE',true).'\'});
					});</script>
				');
			}
			
			//set the pathway
			$theview = isset($menu->query['layout']) ? $menu->query['layout'] : 'rseventspro';
			if (($menu && $theview != 'show') || !$menu)
				$pathway->addItem($this->event->name);
			
			// date helping functions
			$nowunix = JFactory::getDate()->toUnix();
			
			if ($this->event->allday) {
				$date = rseventsproHelper::date($this->event->start,null,false,true);
				$date->addSeconds(86400);
				$date->setTZByID($date->getTZID());
				$date->convertTZ(new RSDate_Timezone('GMT'));
				$endunix = $date->getDate(RSDATE_FORMAT_UNIXTIME);
			} else {
				$endunix = JFactory::getDate($this->event->end)->toUnix();
			}
			$this->eventended = $endunix < $nowunix;
			
		} elseif ($layout == 'subscribe') {
			$app->input->set('cid',$app->input->getInt('id'));
			
			$this->event		= $this->get('event');
			$this->cansubscribe	= $this->get('cansubscribe');
			
			$this->updatefunction = $this->event->ticketsconfig ? 'rsepro_update_total();' : 'rse_calculatetotal();';
			
			// If the Force login option is enabled and the current user is not logged in redirect the user
			if (rseventsproHelper::getConfig('must_login','int') && $user->get('id') == 0) {
				$link = rseventsproHelper::getConfig('modal','int');
				if ($link == 0) {
					$app->redirect(rseventsproHelper::route('index.php?option=com_rseventspro&layout=show&id='.rseventsproHelper::sef($this->event->id,$this->event->name),false),JText::_('COM_RSEVENTSPRO_PLEASE_LOGIN'));
				} else {
					echo rseventsproHelper::redirect(true,JText::_('COM_RSEVENTSPRO_PLEASE_LOGIN'),rseventsproHelper::route('index.php?option=com_rseventspro&layout=show&id='.rseventsproHelper::sef($this->event->id,$this->event->name),false),false,true);
					return;
				}
			}
			
			// If the user cannot view this event or there is no registration to the event -> redirect
			if (!rseventsproHelper::canview($this->event->id) && $this->event->owner != $this->user && !$this->admin) {
				rseventsproHelper::error(JText::_('COM_RSEVENTSPRO_GLOBAL_PERMISSION_DENIED'), rseventsproHelper::route('index.php?option=com_rseventspro&layout=show&id='.rseventsproHelper::sef($this->event->id,$this->event->name),false));
			}
			
			// Can the current user view the subscription form
			if (!$this->cansubscribe['status']) {
				rseventsproHelper::error($this->cansubscribe['err'], rseventsproHelper::route('index.php?option=com_rseventspro&layout=show&id='.rseventsproHelper::sef($this->event->id,$this->event->name),false));
			}
			
			if (rseventsproHelper::getConfig('modal','int') == 1 || rseventsproHelper::getConfig('modal','int') == 2)
				$app->input->set('tmpl','component');
			
			$this->form = rseventsproHelper::rsform();
			
			$thankyou = false;
			if ($this->event->form) {
				$formparams = JFactory::getSession()->get('com_rsform.formparams.'.$this->event->form);
				if (isset($formparams->formProcessed)) 
					$thankyou = true;
			}
			
			$this->thankyou = $thankyou;
			
			// There are no tickets left
			$tickets = $this->get('eventtickets');
			
			$query->clear()
				->select('COUNT('.$db->qn('id').')')
				->from($db->qn('#__rseventspro_tickets'))
				->where($db->qn('ide').' = '.(int) $this->event->id);
			
			$db->setQuery($query);
			$eventtickets = $db->loadResult();
			
			if (!empty($eventtickets) && empty($tickets) && $this->event->form == 0) {
				rseventsproHelper::error(JText::_('COM_RSEVENTSPRO_GLOBAL_PERMISSION_DENIED'), rseventsproHelper::route('index.php?option=com_rseventspro&layout=show&id='.rseventsproHelper::sef($this->event->id,$this->event->name),false));
			}
			
			$lists['tickets'] = JHTML::_('select.genericlist', $tickets, 'ticket', 'size="1" onchange="rs_get_ticket(this.value);" class="rs_edit_sel_small"','value','text');
			
			$payments = rseventsproHelper::getPayments(false,$this->event->payments);
			if (!empty($payments)) {
				if (rseventsproHelper::getConfig('payment_type','int'))
					$lists['payments']      = JHTML::_('select.genericlist', $payments, 'payment', 'class="rs_edit_sel_small" onchange="'.$this->updatefunction.'"', 'value' ,'text',rseventsproHelper::getConfig('default_payment'));
				else
					$lists['payments']      = JHTML::_('select.radiolist', $payments, 'payment', 'class="inputbox" onchange="'.$this->updatefunction.'"', 'value' ,'text',rseventsproHelper::getConfig('default_payment'));
			}
			
			$this->user			= $user;
			$this->tickets		= $tickets;
			$this->payment		= $this->get('ticketpayment');
			$this->payments		= !empty($payments);
			
			//set the pathway
			$theview = isset($menu->query['layout']) ? $menu->query['layout'] : 'rseventspro';
			if (($menu && $theview != 'show') || !$menu)
				$pathway->addItem($this->event->name,rseventsproHelper::route('index.php?option=com_rseventspro&layout=show&id='.rseventsproHelper::sef($this->event->id,$this->event->name),false));
			
			$pathway->addItem(JText::_('COM_RSEVENTSPRO_BC_SUBSCRIBE'));
			
		} elseif ($layout == 'wire') {
			
			$this->data		= $this->get('subscriber');
			$this->payment  = $this->get('payment');
			
			//set the pathway
			$theview = isset($menu->query['layout']) ? $menu->query['layout'] : 'rseventspro';
			if (($menu && $theview != 'show') || !$menu)
				$pathway->addItem($this->data['event']->name,rseventsproHelper::route('index.php?option=com_rseventspro&layout=show&id='.rseventsproHelper::sef($this->data['event']->id,$this->data['event']->name),false));
			
			$pathway->addItem(JText::_('COM_RSEVENTSPRO_BC_WIRE'));
			
		} elseif ($layout == 'subscriptions') {
			
			$this->subscriptions = $this->get('subscriptions');
			
		} elseif ($layout == 'subscribers') {
			
			$this->row = $this->get('event');
			
			if ($this->admin || $this->row->owner == $user->get('id')) {
				$doc->addScript(JURI::root(true).'/components/com_rseventspro/assets/js/dom.js');
				
				$states = array_merge(array(JHTML::_('select.option', '-', JText::_('COM_RSEVENTSPRO_GLOBAL_SELECT_STATE'))), $this->get('statuses'));
				$lists['state'] = JHTML::_('select.genericlist', $states, 'state', 'size="1" onchange="document.adminForm.submit();" class="rs_edit_sel_small"','value','text',$app->getUserState('com_rseventspro.subscriptions.state.frontend'));
				
				$lists['tickets'] = JHTML::_('select.genericlist', $this->get('ticketsfromevent'), 'ticket', 'size="1" onchange="document.adminForm.submit();" class="rs_edit_sel_small"','value','text',$app->getUserState('com_rseventspro.subscriptions.ticket.frontend'));
				
				$this->data			= $this->get('subscribers');
				$this->total		= $this->get('totalsubscribers');
				$this->filter_word	= $app->getUserState('com_rseventspro.subscriptions.search_frontend');
				
				//set the pathway
				$theview = isset($menu->query['layout']) ? $menu->query['layout'] : 'rseventspro';
				if (($menu && $theview != 'show') || !$menu)
					$pathway->addItem($this->row->name,rseventsproHelper::route('index.php?option=com_rseventspro&layout=show&id='.rseventsproHelper::sef($this->row->id,$this->row->name),false));
				
				$pathway->addItem(JText::_('COM_RSEVENTSPRO_BC_SUBSCRIBERS'));
				
			} else {
				rseventsproHelper::error(JText::_('COM_RSEVENTSPRO_GLOBAL_PERMISSION_DENIED'), rseventsproHelper::route('index.php?option=com_rseventspro&layout=show&id='.rseventsproHelper::sef($this->row->id,$this->row->name),false));
			}
		
		} elseif ($layout == 'editsubscriber') {
			
			$this->data = $this->get('subscriber');
			if ($this->admin || $this->data['event']->owner == $user->get('id')) {
				$lists['status'] = JHTML::_('select.genericlist', $this->get('statuses'), 'jform[state]', 'size="1" class="rs_edit_sel_small"','value','text', $this->data['data']->state);
				
				$this->fields	= $this->get('fields');
				$tparams = $this->data['data']->gateway == 'offline' ? $this->get('card') : $this->data['data']->params;
				$this->tparams = $tparams;
				
				//set the pathway
				$theview = isset($menu->query['layout']) ? $menu->query['layout'] : 'rseventspro';
				if (($menu && $theview != 'show') || !$menu)
					$pathway->addItem($this->data['event']->name,rseventsproHelper::route('index.php?option=com_rseventspro&layout=show&id='.rseventsproHelper::sef($this->data['event']->id,$this->data['event']->name),false));
				
				$pathway->addItem(JText::_('COM_RSEVENTSPRO_BC_EDIT_SUBSCRIBER'));
				
			} else {
				rseventsproHelper::error(JText::_('COM_RSEVENTSPRO_GLOBAL_PERMISSION_DENIED'), rseventsproHelper::route('index.php?option=com_rseventspro&layout=show&id='.rseventsproHelper::sef($this->data['event']->id,$this->data['event']->name),false));
			}
		
		} elseif ($layout == 'message') {
			
			$this->event = $this->get('event');
			
			if ($this->admin || $this->event->owner == $user->get('id')) {
				$this->subscribers = $this->get('people');
				
				//set the pathway
				$theview = isset($menu->query['layout']) ? $menu->query['layout'] : 'rseventspro';
				if (($menu && $theview != 'show') || !$menu)
					$pathway->addItem($this->event->name,rseventsproHelper::route('index.php?option=com_rseventspro&layout=show&id='.rseventsproHelper::sef($this->event->id,$this->event->name),false));
					
				$pathway->addItem(JText::_('COM_RSEVENTSPRO_BC_SEND_MESSAGE'));
			} else {
				rseventsproHelper::error(JText::_('COM_RSEVENTSPRO_GLOBAL_PERMISSION_DENIED'), rseventsproHelper::route('index.php?option=com_rseventspro&layout=show&id='.rseventsproHelper::sef($this->event->id,$this->event->name),false));
			}
			
		} elseif ($layout == 'invite') {
		
			$this->event	= $this->get('event');
			$options = rseventsproHelper::options($this->event->id);
			
			if (!rseventsproHelper::check($this->event->id)) {
				rseventsproHelper::error(JText::_('COM_RSEVENTSPRO_GLOBAL_PERMISSION_DENIED'), rseventsproHelper::route('index.php?option=com_rseventspro&layout=show&id='.rseventsproHelper::sef($this->event->id,$this->event->name),false));
			}
			
			// date helping functions
			$nowunix 	= JFactory::getDate()->toUnix();
			
			if ($this->event->allday) {
				$date = rseventsproHelper::date($this->event->start,null,false,true);
				$date->addSeconds(86400);
				$date->setTZByID($date->getTZID());
				$date->convertTZ(new RSDate_Timezone('GMT'));
				$endunix = $date->getDate(RSDATE_FORMAT_UNIXTIME);
			} else {
				$endunix 	= JFactory::getDate($this->event->end)->toUnix();
			}
			$eventended = $endunix < $nowunix;
			
			if ($eventended) {
				rseventsproHelper::error(JText::_('COM_RSEVENTSPRO_GLOBAL_PERMISSION_DENIED'), rseventsproHelper::route('index.php?option=com_rseventspro&layout=show&id='.rseventsproHelper::sef($this->event->id,$this->event->name),false));
			}
			
			if (empty($options['show_invite'])) {
				rseventsproHelper::error(JText::_('COM_RSEVENTSPRO_GLOBAL_PERMISSION_DENIED'), rseventsproHelper::route('index.php?option=com_rseventspro&layout=show&id='.rseventsproHelper::sef($this->event->id,$this->event->name),false));
			}
			
			//set the pathway
			$theview = isset($menu->query['layout']) ? $menu->query['layout'] : 'rseventspro';
			if (($menu && $theview != 'show') || !$menu)
				$pathway->addItem($this->event->name,rseventsproHelper::route('index.php?option=com_rseventspro&layout=show&id='.rseventsproHelper::sef($this->event->id,$this->event->name),false));
				
			$pathway->addItem(JText::_('COM_RSEVENTSPRO_BC_INVITE'));
		
		} elseif ($layout == 'unsubscribe') {
			
			$this->event		= $this->get('event');
			$this->issubscribed	= $this->get('issubscribed');
			
			if ($this->issubscribed)
				$this->subscriptions = $this->get('usersubscriptions');
			else {
				rseventsproHelper::error(JText::_('COM_RSEVENTSPRO_GLOBAL_PERMISSION_DENIED'), rseventsproHelper::route('index.php?option=com_rseventspro&layout=show&id='.rseventsproHelper::sef($this->event->id,$this->event->name),false));
			}
				
		} elseif ($layout == 'search') {
			
			$doc->addScript(JURI::root(true).'/components/com_rseventspro/assets/js/dom.js');
			
			$this->events	= $this->get('results');
			$this->total	= $this->get('totalresults');
			$this->search	= $app->getUserStateFromRequest('rsepro.search.search', 'rskeyword');
		} elseif ($layout == 'tickets') {
			
			$this->event		= $this->get('event');
			$this->cansubscribe	= $this->get('cansubscribe');
			
			// If the user cannot view this event or there is no registration to the event -> redirect
			if (!rseventsproHelper::canview($this->event->id) && $this->event->owner != $this->user && !$this->admin) {
				rseventsproHelper::error(JText::_('COM_RSEVENTSPRO_GLOBAL_PERMISSION_DENIED'), rseventsproHelper::route('index.php?option=com_rseventspro&layout=show&id='.rseventsproHelper::sef($this->event->id,$this->event->name),false));
			}
			
			// Can the current user view the subscription form
			if (!$this->cansubscribe['status']) {
				rseventsproHelper::error($this->cansubscribe['err'], rseventsproHelper::route('index.php?option=com_rseventspro&layout=show&id='.rseventsproHelper::sef($this->event->id,$this->event->name),false));
			}
			
			// There are no tickets left
			$tickets = $this->get('eventtickets');
			
			$query->clear()
				->select('COUNT('.$db->qn('id').')')
				->from($db->qn('#__rseventspro_tickets'))
				->where($db->qn('ide').' = '.(int) $this->event->id);
			
			$db->setQuery($query);
			$eventtickets = $db->loadResult();
			
			if (!empty($eventtickets) && empty($tickets) && $this->event->form == 0) {
				rseventsproHelper::error(JText::_('COM_RSEVENTSPRO_GLOBAL_PERMISSION_DENIED'), rseventsproHelper::route('index.php?option=com_rseventspro&layout=show&id='.rseventsproHelper::sef($this->event->id,$this->event->name),false));
			}
			
			JFactory::getDocument()->addStyleSheet(JURI::root(true).'/components/com_rseventspro/assets/js/jquery/jquery-ui-1.10.3.custom.css');
			JFactory::getDocument()->addStyleSheet(JURI::root(true).'/components/com_rseventspro/assets/css/tickets.css');
			
			
			$this->tickets = rseventsproHelper::getTickets(JFactory::getApplication()->input->getInt('id',0));
		} elseif ($layout == 'seats') {
			
			$permission_denied = false;
			if (!$this->admin) {
				if (!$id) {
					if (empty($this->permissions['can_post_events']))
						$permission_denied = true;
				} else {
					if (($this->user != $this->owner && empty($this->permissions['can_edit_events'])) || empty($this->user))
						$permission_denied = true;
				}
			}
			
			if ($permission_denied) {
				rseventsproHelper::error(JText::_('COM_RSEVENTSPRO_GLOBAL_PERMISSION_DENIED'), rseventsproHelper::route('index.php?option=com_rseventspro&layout=show&id='.rseventsproHelper::sef($this->row->id,$this->row->name),false));
			}
			
			if (!rseventsproHelper::isJ3()) {
				JFactory::getDocument()->addScript(JURI::root(true).'/components/com_rseventspro/assets/js/jquery/jquery-1.9.1.js');
				JFactory::getDocument()->addScript(JURI::root(true).'/components/com_rseventspro/assets/js/jquery/jquery.noconflict.js');
			} else {
				JHtml::_('jquery.framework');
			}
			
			JFactory::getDocument()->addScript(JURI::root(true).'/components/com_rseventspro/assets/js/jquery/jquery-ui-1.10.3.custom.js');
			JFactory::getDocument()->addStyleSheet(JURI::root(true).'/components/com_rseventspro/assets/js/jquery/jquery-ui-1.10.3.custom.css');
			JFactory::getDocument()->addStyleSheet(JURI::root(true).'/components/com_rseventspro/assets/css/tickets.css');
			
			$this->tickets = rseventsproHelper::getTickets(JFactory::getApplication()->input->getInt('id',0));
		} elseif ($layout == 'report') {
			$this->report			= $this->get('canreport');
			
			if (!$this->report) {
				rseventsproHelper::error(JText::_('COM_RSEVENTSPRO_GLOBAL_PERMISSION_DENIED'), rseventsproHelper::route('index.php?option=com_rseventspro&layout=default',false));
			}
		} elseif ($layout == 'reports') {
			$this->report		= $this->get('canreport');
			$this->event		= $this->get('event');
			
			if ($this->admin || $this->event->owner == $user->get('id')) {
				$this->reports	= rseventsproHelper::getReports($this->event->id);
			} else {
				rseventsproHelper::error(JText::_('COM_RSEVENTSPRO_GLOBAL_PERMISSION_DENIED'), rseventsproHelper::route('index.php?option=com_rseventspro&layout=show&id='.rseventsproHelper::sef($this->event->id,$this->event->name),false));
			}
			
			if (!rseventsproHelper::check($this->event->id) || !$this->report) {
				rseventsproHelper::error(JText::_('COM_RSEVENTSPRO_GLOBAL_PERMISSION_DENIED'), rseventsproHelper::route('index.php?option=com_rseventspro&layout=show&id='.rseventsproHelper::sef($this->event->id,$this->event->name),false));
			}
		} elseif ($layout == 'scan') {
			$this->event		= $this->get('event');
			
			if ($this->admin || $this->event->owner == $user->get('id')) {
				$this->scan			= rseventsproHelper::getScan();
				
				//set the pathway
				$theview = isset($menu->query['layout']) ? $menu->query['layout'] : 'rseventspro';
				if (($menu && $theview != 'show') || !$menu)
					$pathway->addItem($this->event->name,rseventsproHelper::route('index.php?option=com_rseventspro&layout=show&id='.rseventsproHelper::sef($this->event->id,$this->event->name),false));
				
				$pathway->addItem(JText::_('COM_RSEVENTSPRO_BC_SCAN'));
				
			} else {
				rseventsproHelper::error(JText::_('COM_RSEVENTSPRO_GLOBAL_PERMISSION_DENIED'), rseventsproHelper::route('index.php?option=com_rseventspro&layout=show&id='.rseventsproHelper::sef($this->event->id,$this->event->name),false));
			}
		} elseif ($layout == 'userseats') {
			$this->data = $this->get('subscriber');
			if ($this->admin || $this->data['event']->owner == $user->get('id')) {
				JFactory::getDocument()->addStyleSheet(JURI::root(true).'/components/com_rseventspro/assets/js/jquery/jquery-ui-1.10.3.custom.css');
				JFactory::getDocument()->addStyleSheet(JURI::root(true).'/components/com_rseventspro/assets/css/tickets.css');
				
				$eventId		= $this->data['event']->id;
				$this->tickets	= rseventsproHelper::getTickets($eventId);
				$this->id		= JFactory::getApplication()->input->getInt('id',0);
			} else {
				rseventsproHelper::error(JText::_('COM_RSEVENTSPRO_GLOBAL_PERMISSION_DENIED'), rseventsproHelper::route('index.php?option=com_rseventspro&layout=show&id='.rseventsproHelper::sef($this->data['event']->id,$this->data['event']->name),false));
			}
		}
		
		$this->lists	= $lists;
		
		parent::display($tpl);
	}
	
	public function getStatus($state) {
		if ($state == 0) {
			return '<font color="blue">'.JText::_('COM_RSEVENTSPRO_GLOBAL_STATUS_INCOMPLETE').'</font>';
		} elseif ($state == 1) {
			return '<font color="green">'.JText::_('COM_RSEVENTSPRO_GLOBAL_STATUS_COMPLETED').'</font>';
		} elseif ($state == 2) {
			return '<font color="red">'.JText::_('COM_RSEVENTSPRO_GLOBAL_STATUS_DENIED').'</font>';
		}
	}
	
	public function getUser($id) {
		if ($id > 0) {
			return JFactory::getUser($id)->get('username');
		} else return JText::_('COM_RSEVENTSPRO_GLOBAL_GUEST');
	}
	
	public function getNumberEvents($id, $type) {
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);
		$events	= 0;
		
		if ($type == 'categories') {
			$query->clear()
				->select($db->qn('e.id'))
				->from($db->qn('#__rseventspro_events','e'))
				->join('left', $db->qn('#__rseventspro_taxonomy','t').' ON '.$db->qn('e.id').' = '.$db->qn('t.ide'))
				->where($db->qn('t.type').' = '.$db->q('category'))
				->where($db->qn('t.id').' = '.(int) $id)
				->where($db->qn('e.published').' = 1');
			
			$db->setQuery($query);
			$eventids = $db->loadColumn();
			
			if (!empty($eventids)) {
				foreach ($eventids as $eid) {
					if (!rseventsproHelper::canview($eid)) 
						continue;
					$events++;
				}
			}
		} else if ($type == 'locations') {
			$query->clear()
				->select($db->qn('id'))
				->from($db->qn('#__rseventspro_events'))
				->where($db->qn('location').' = '.(int) $id)
				->where($db->qn('published').' = 1');
			
			
			$db->setQuery($query);
			$eventids = $db->loadColumn();
			
			if (!empty($eventids)) {
				foreach ($eventids as $eid) {
					if (!rseventsproHelper::canview($eid)) 
						continue;
					$events++;
				}
			}
		}
		
		if (!$events) return;
		return $events.' '.JText::plural('COM_RSEVENTSPRO_CALENDAR_EVENTS',$events);
	}
}