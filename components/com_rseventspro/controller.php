<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' ); 

class rseventsproController extends JControllerLegacy
{
	/**
	 *	Main constructor
	 *
	 * @return void
	 */
	public function __construct() {
		parent::__construct();
	}
	
	/**
	 *	Method to display location results
	 *
	 * @return void
	 */
	public function locations() {
		echo rseventsproHelper::filterlocations();
		JFactory::getApplication()->close();
	}
	
	/**
	 *	Method to verify a certain coupon code
	 *
	 * @return void
	 */
	public function verify() {
		$db			= JFactory::getDbo();
		$query		= $db->getQuery(true);
		$id			= JFactory::getApplication()->input->getInt('id');
		$coupon		= JFactory::getApplication()->input->getString('coupon');
		$nowunix	= JFactory::getDate()->toUnix();
		$available	= false;
		$data		= false;
		
		$query->clear()
			->select($db->qn('cc.id'))->select($db->qn('cc.used'))->select($db->qn('c.from'))
			->select($db->qn('c.to'))->select($db->qn('c.usage'))
			->from($db->qn('#__rseventspro_coupon_codes','cc'))
			->join('left', $db->qn('#__rseventspro_coupons','c').' ON '.$db->qn('cc.idc').' = '.$db->qn('c.id'))
			->where($db->qn('c.ide').' = '.$id)
			->where($db->qn('cc.code').' = '.$db->q($coupon));
		
		$db->setQuery($query);
		$data = $db->loadObject();
		
		if (empty($data)) {
			$query->clear()
				->select($db->qn('used'))->select($db->qn('from'))
				->select($db->qn('to'))->select($db->qn('usage'))
				->select($db->qn('events'))
				->from($db->qn('#__rseventspro_discounts'))
				->where($db->qn('code').' = '.$db->q($coupon));
			$db->setQuery($query);
			if ($discount = $db->loadObject()) {
				$registry = new JRegistry;
				$registry->loadString($discount->events);
				$events = $registry->toArray();
				
				// Event assignment is set to All events
				if ($discount->apply_to == 1) {
					$data = $discount;
				} else if ($discount->apply_to == 2) {
					// Event assignment is set to Selected events
					if (in_array($id,$events)) {
						$data = $discount;
					}
				} else {
					// Event assignment is set to All except those selected
					if (!in_array($id,$events)) {
						$data = $discount;
					}
				}
			}
		}
		
		if ($data) {
			$available = true;
			if (!empty($data->usage) && !empty($data->used))
				if ($data->used >= $data->usage)
					$available = false;
			
			if ($available) {
				if ($data->from == $db->getNullDate()) $data->from = '';
				if ($data->to == $db->getNullDate()) $data->to = '';
				
				if (empty($data->from) && empty($data->to)) {
					$available = true;
				} elseif (!empty($data->from) && empty($data->to)) {
					$fromunix = JFactory::getDate($data->from)->toUnix();
					if ($fromunix <= $nowunix)
						$available = true;
					else $available = false;
				} elseif (empty($data->from) && !empty($data->to)) {
					$tounix = JFactory::getDate($data->to)->toUnix();
					if ($tounix <= $nowunix)
						$available = false;
					else $available = true;
				} else {
					$fromunix = JFactory::getDate($data->from)->toUnix();
					$tounix = JFactory::getDate($data->to)->toUnix();
					
					if (($fromunix <= $nowunix && $tounix >= $nowunix) || ($fromunix >= $nowunix && $tounix <= $nowunix))
						$available = true;
					else $available = false;
				}
			}
		}
		
		echo 'RS_DELIMITER0';
		if ($available) {
			echo JText::_('COM_RSEVENTSPRO_COUPON_OK');
		} else echo JText::_('COM_RSEVENTSPRO_COUPON_ERROR');
		echo 'RS_DELIMITER1';
		
		JFactory::getApplication()->close();
	}
	
	
	
	/**
	 *	Method to clear filters
	 *
	 * @return void
	 */
	public function clear() {
		$app		= JFactory::getApplication();
		$itemid		= $app->input->getInt('Itemid');
		$parent		= $app->input->getInt('parent');
		$from		= $app->input->get('from');
		
		$app->setUserState('com_rseventspro.events.filter_columns'.$itemid.$parent,array());
		$app->setUserState('com_rseventspro.events.filter_operators'.$itemid.$parent,array());
		$app->setUserState('com_rseventspro.events.filter_values'.$itemid.$parent,array());
		
		if ($from == 'map')
			return $this->setRedirect(rseventsproHelper::route('index.php?option=com_rseventspro&layout=map',false));
		else
			return $this->setRedirect(rseventsproHelper::route('index.php?option=com_rseventspro&layout=default',false));
	}
	
	/**
	 *	Method to load search results
	 *
	 * @return void
	 */
	public function filter() {
		$method = JFactory::getApplication()->input->get('method','');
		if (!$method) echo 'RS_DELIMITER0';
		echo rseventsproHelper::filter();
		if (!$method) echo 'RS_DELIMITER1';
		JFactory::getApplication()->close();
	}
	
	/**
	 *	Method to get the number of allowed tickets a users can purchase
	 *
	 * @return string
	 */
	public function tickets() {
		$id = JFactory::getApplication()->input->getInt('id');
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		
		$query->clear()
			->select($db->qn('description'))
			->from($db->qn('#__rseventspro_tickets'))
			->where($db->qn('id').' = '.$id);
		
		$db->setQuery($query);
		$ticket_description = $db->loadResult();
		$seats = rseventsproHelper::checkticket($id);
		
		echo 'RS_DELIMITER0'.$seats.'|'.$ticket_description.'RS_DELIMITER1';
		JFactory::getApplication()->close();
	}
	
	
	/**
	 *	Method to generate the captcha image
	 *
	 * @return image
	 */
	public function captcha() {
		require_once JPATH_SITE.'/components/com_rseventspro/helpers/captcha/image.php';
		$captcha = new RSCaptcha();
	}
	
	/**
	 *	Method to check captcha
	 *
	 * @return int
	 */
	public function checkcaptcha() {
		$session	= JFactory::getSession();
		$input		= JFactory::getApplication()->input;
		$secret		= $input->getString('secret');
		$response	= $input->getString('recaptcha');
		$ip		  	= $input->server->get('REMOTE_ADDR');
		$config		= rseventsproHelper::getConfig();
		$key		= $config->recaptcha_secret_key;
		
		echo 'RS_DELIMITER0';
		
		if ($config->captcha == 1) {
			echo ($session->get('security_number') == $secret) ? 1 : 0;
		} else {
			try {
				jimport('joomla.http.factory');
				$http = JHttpFactory::getHttp();
				if ($request = $http->get('https://www.google.com/recaptcha/api/siteverify?secret='.urlencode($key).'&response='.urlencode($response).'&remoteip='.urlencode($ip))) {
					$json = json_decode($request->body);
					$captcha_response = $json->success;
				}
			} catch (Exception $e) {
				$captcha_response = false;
			}
			
			echo (int) $captcha_response;
		}
		
		echo 'RS_DELIMITER1';
		JFactory::getApplication()->close();
	}
	
	/**
	 *	Method to show payment form
	 *
	 * @return 
	 */
	public function payment() {
		$db			= JFactory::getDbo();
		$query		= $db->getQuery(true);
		$app		= JFactory::getApplication();
		$method 	= $app->input->getString('method');
		$hash		= $app->input->getString('hash');
		$currency	= rseventsproHelper::getConfig('payment_currency');
		
		$query->clear()
			->select($db->qn('u.id'))->select($db->qn('u.ide'))->select($db->qn('u.idu'))->select($db->qn('u.name'))
			->select($db->qn('u.email'))->select($db->qn('u.discount'))->select($db->qn('u.early_fee'))->select($db->qn('u.late_fee'))
			->select($db->qn('u.tax'))->select($db->qn('u.verification'))->select($db->qn('u.state'))
			->from($db->qn('#__rseventspro_users','u'))
			->where('MD5(CONCAT('.$db->qn('u.id').','.$db->qn('u.name').','.$db->qn('u.email').')) = '.$db->q($hash));
		
		$db->setQuery($query);
		$details = $db->loadObject();
		
		if (empty($details)) {
			$this->setMessage(JText::_('COM_RSEVENTSPRO_INVALID_SUBSCRIPTION'),'error');
			return $this->setRedirect(rseventsproHelper::route('index.php?option=com_rseventspro',false));
		}
		
		if ($details->state == 1 || $details->state == 2) {
			$query->clear()
				->select($db->qn('id'))->select($db->qn('name'))
				->from($db->qn('#__rseventspro_events'))
				->where($db->qn('id').' = '.$db->q($details->ide));
			$db->setQuery($query);
			$event = $db->loadObject();
			
			if ($details->state == 1) {
				$this->setMessage(JText::_('COM_RSEVENTSPRO_SUBSCRIPTION_STATE_COMPLETE'));
			} else {
				$this->setMessage(JText::_('COM_RSEVENTSPRO_SUBSCRIPTION_STATE_DENIED'));
			}
			
			return $this->setRedirect(rseventsproHelper::route('index.php?option=com_rseventspro&layout=show&id='.rseventsproHelper::sef($event->id, $event->name),false,rseventsproHelper::itemid($event->id)));
		}
		
		$query->clear()
			->select($db->qn('ut.quantity'))->select($db->qn('t.id'))
			->select($db->qn('t.name'))->select($db->qn('t.price'))
			->from($db->qn('#__rseventspro_user_tickets','ut'))
			->join('left', $db->qn('#__rseventspro_tickets','t').' ON '.$db->qn('t.id').' = '.$db->qn('ut.idt'))
			->where($db->qn('ut.ids').' = '.(int) $details->id);
		
		$db->setQuery($query);
		$tickets = $db->loadObjectList();
		
		$app->triggerEvent('rsepro_showForm',array(array('method'=>&$method, 'details'=>&$details, 'tickets'=>&$tickets, 'currency'=>&$currency)));
	}
	
	/**
	 *	Method to process the payment form
	 *
	 * @return 
	 */
	public function process() {
		$app = JFactory::getApplication();
		$data = $app->input->get->request;
		$app->triggerEvent('rsepro_processForm',array(array('data'=>&$data)));
	}
	
	/**
	 *	Method to calculate event repeats
	 *
	 * @return int
	 */
	public function repeats() {
		require_once JPATH_SITE . '/components/com_rseventspro/helpers/recurring.php';
		
		$input		= JFactory::getApplication()->input;
		$registry	= new JRegistry;
		
		$registry->set('interval', $input->getInt('interval',0));
		$registry->set('type', $input->getInt('type',0));
		$registry->set('start', $input->getString('start'));
		$registry->set('end', $input->getString('end'));
		$registry->set('days', $input->get('days',array(),'array'));
		$registry->set('also', $input->get('also',array(),'array'));
		$registry->set('exclude', $input->get('exclude',array(),'array'));
		
		$registry->set('repeat_on_type', $input->getInt('repeat_on_type',0));
		$registry->set('repeat_on_day', $input->getInt('repeat_on_day',0));
		$registry->set('repeat_on_day_order', $input->getInt('repeat_on_day_order',0));
		$registry->set('repeat_on_day_type', $input->getInt('repeat_on_day_type',0));
		
		$recurring = RSEventsProRecurring::getInstance($registry);
		$dates = $recurring->getDates(true);
		
		echo 'RS_DELIMITER0';
		echo count($dates);
		echo 'RS_DELIMITER1';
		JFactory::getApplication()->close();
	}
	
	/**
	 *	Method to get ajax search results
	 *
	 * @return string
	 */
	public function ajax() {
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);
		$search = JFactory::getApplication()->input->getString('search');
		$itemid = JFactory::getApplication()->input->getInt('iid');
		$opener = JFactory::getApplication()->input->getInt('opener',0);
		
		$query->clear()
			->select($db->qn('id'))->select($db->qn('name'))
			->from($db->qn('#__rseventspro_events'))
			->where('('.$db->qn('name').' LIKE '.$db->q('%'.$search.'%').' OR '.$db->qn('description').' LIKE '.$db->q('%'.$search.'%').' )')
			->where($db->qn('completed').' = 1')
			->where($db->qn('published').' = 1');
		
		$db->setQuery($query);
		$events = $db->loadObjectList();
		
		$open = !$opener ? 'target="_blank"' : '';
		
		$html = 'RS_DELIMITER0';
		if (!empty($events)) {
			$html .= '<li class="rsepro_ajax_close"><a href="javascript:void(0);" onclick="rsepro_ajax_close();"></a></li>';
			foreach ($events as $event) {
				if (!rseventsproHelper::canview($event->id)) 
					continue;
				
				$iid	= rseventsproHelper::itemid($event->id);
				$iid	= empty($iid) ? $itemid : $iid;
				
				$html .= '<li><a '.$open.' href="'.rseventsproHelper::route('index.php?option=com_rseventspro&layout=show&id='.rseventsproHelper::sef($event->id,$event->name),true,$iid).'">'.$event->name.'</a></li>';
			}
		}
		$html .= 'RS_DELIMITER1';
		
		echo $html;
		JFactory::getApplication()->close();
	}
	
	/**
	 *	Method to publish a moderated event
	 *
	 * @return
	 */
	public function activate() {
		$db				= JFactory::getDbo();
		$query			= $db->getQuery(true);
		$key			= JFactory::getApplication()->input->getString('key','');
		$permissions	= rseventsproHelper::permissions();
		$admin			= rseventsproHelper::admin();
		$juser			= JFactory::getUser();
		$lang			= JFactory::getLanguage();
		$sid			= JFactory::getSession()->getId();
		$userid			= (int) $juser->get('id');
		
		if (!empty($key)) {
			$query->clear()
				->select($db->qn('id'))->select($db->qn('name'))->select($db->qn('sid'))
				->select($db->qn('location'))->select($db->qn('owner'))
				->from($db->qn('#__rseventspro_events'))
				->where('MD5(CONCAT('.$db->q('event').','.$db->qn('id').')) = '.$db->q($key));
			
			$db->setQuery($query,0,1);
			$event = $db->loadObject();
			
			// Do not allow a event owner to approve its own event
			if ($event->sid == $sid || (int) $event->owner == $userid) {
				JFactory::getApplication()->enqueueMessage(JText::_('COM_RSEVENTSPRO_CANNOT_APPROVE_OWN_EVENT'),'error');
				return $this->setRedirect(rseventsproHelper::route('index.php?option=com_rseventspro&layout=show&id='.rseventsproHelper::sef($event->id,$event->name),false,rseventsproHelper::itemid($event->id)));
			}
			
			if ($admin || !empty($permissions['can_edit_events']) || !empty($permissions['can_approve_events'])) {
				if (!empty($event)) {
					$query->clear()
						->update($db->qn('#__rseventspro_locations'))
						->set($db->qn('published').' = 1')
						->where($db->qn('id').' = '.(int) $event->location);
					
					$db->setQuery($query);
					$db->execute();
					
					$query->clear()
						->update($db->qn('#__rseventspro_events'))
						->set($db->qn('published').' = 1')
						->set($db->qn('approved').' = 0')
						->where($db->qn('id').' = '.(int) $event->id);
					
					$db->setQuery($query);
					if ($db->execute()) {
						// Send approval email
						$owner	= JFactory::getUser($event->owner);
						$to		= $owner->get('email');
						$name	= $owner->get('name');
						rseventsproEmails::approval($to, $event->id, $name, $lang->getTag());
						
						return $this->setRedirect(rseventsproHelper::route('index.php?option=com_rseventspro&layout=show&id='.rseventsproHelper::sef($event->id,$event->name),false,rseventsproHelper::itemid($event->id)),JText::_('COM_RSEVENTSPRO_EVENT_PUBLISHED'));
					}
				}
			}
		}
		
		return $this->setRedirect(rseventsproHelper::route('index.php?option=com_rseventspro&layout=default',false),JText::_('COM_RSEVENTSPRO_GLOBAL_PERMISSION_DENIED'),'error');
	}
	
	/**
	 *	Method to publish a moderated tag
	 *
	 * @return
	 */
	public function tagactivate() {
		$db				= JFactory::getDbo();
		$query			= $db->getQuery(true);
		$key			= JFactory::getApplication()->input->getString('key','');
		$permissions	= rseventsproHelper::permissions();
		$admin			= rseventsproHelper::admin();
		
		if (!empty($key)) {
			$query->clear()
				->select('*')
				->from('#__rseventspro_tags')
				->where('MD5(CONCAT('.$db->q('tag').','.$db->qn('id').')) = '.$db->q($key));
			
			$db->setQuery($query,0,1);
			$tag = $db->loadObject();
			
			if (($admin || !empty($permissions['can_approve_tags'])) && $tag) {
				$query->clear()
					->update($db->qn('#__rseventspro_tags'))
					->set($db->qn('published').' = 1')
					->where($db->qn('id').' = '.(int) $tag->id);
				
				$db->setQuery($query);
				if ($db->execute())
					return $this->setRedirect(rseventsproHelper::route('index.php?option=com_rseventspro&layout=default',false), JText::_('COM_RSEVENTSPRO_EVENT_TAG_PUBLISHED'));
			}
		}
		
		return $this->setRedirect(rseventsproHelper::route('index.php?option=com_rseventspro&layout=default',false),JText::_('COM_RSEVENTSPRO_GLOBAL_PERMISSION_DENIED'),'error');
	}
	
	/**
	 *	Method to send reminders
	 *
	 * @return
	 */
	public function reminder() {
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);
		$user	= JFactory::getUser();
		$lang	= JFactory::getLanguage();
		$id		= JFactory::getApplication()->input->getInt('id');
		$sid	= JFactory::getSession()->getId();
		
		$msg = JText::_('COM_RSEVENTSPRO_GLOBAL_PERMISSION_DENIED');
		
		$query->clear()
			->select($db->qn('id'))->select($db->qn('name'))
			->select($db->qn('sid'))->select($db->qn('owner'))
			->from($db->qn('#__rseventspro_events'))
			->where($db->qn('id').' = '.$db->q($id));
		
		$db->setQuery($query);
		$event = $db->loadObject();
		
		if (rseventsproHelper::admin() || ($user->get('id') == $event->owner && !$user->get('guest')) || $sid == $event->sid) {
			$query->clear()
				->select('DISTINCT '.$db->qn('email'))->select($db->qn('name'))
				->from($db->qn('#__rseventspro_users'))
				->where($db->qn('ide').' = '.(int) $id)
				->where($db->qn('state').' IN (0,1)');
			
			$db->setQuery($query);
			$subscribers = $db->loadObjectList();
			
			if (!empty($subscribers)) {
				foreach ($subscribers as $subscriber) {
					rseventsproEmails::reminder($subscriber->email,$id,$subscriber->name, $lang->getTag());
				}
			}
			
			$msg = JText::_('COM_RSEVENTSPRO_EVENT_REMINDERS_SENT');
		}
		
		return $this->setRedirect(rseventsproHelper::route('index.php?option=com_rseventspro&layout=show&id='.rseventsproHelper::sef($event->id,$event->name),false,rseventsproHelper::itemid($event->id)), $msg);
	}
	
	/**
	 *	Method to send auto reminders
	 *
	 * @return
	 */
	public function autoreminder() {
		// no need to edit below
		
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);
		$squery	= $db->getQuery(true);
		$lang	= JFactory::getLanguage();
		
		// number of days - you can change this to the number of days that you require
		$days			= rseventsproHelper::getConfig('email_reminder_days','int');
		$now			= JFactory::getDate()->toSql();
		$days_offset	= $days * 86400;
		
		$squery->clear()
			->select('DISTINCT '.$db->qn('ide'))
			->from($db->qn('#__rseventspro_taxonomy'))
			->where($db->qn('type').' = '.$db->q('reminder'));
		
		$query->clear()
			->select($db->qn('id'))
			->from($db->qn('#__rseventspro_events'))
			->where($db->qn('published').' = 1')
			->where($db->qn('completed').' = 1');
		
		if (!rseventsproHelper::getConfig('email_reminder_run','int')) {
			//before the event will end
			$query->where($db->q($now).' > DATE_SUB('.$db->qn('end').', INTERVAL '.$days_offset.' SECOND)');
			$query->where($db->q($now).' < '.$db->qn('end'));
			$query->where($db->qn('id').' NOT IN ('.$squery.')');
		} else {
			//before the event will start
			$query->where($db->q($now).' > DATE_SUB('.$db->qn('start').', INTERVAL '.$days_offset.' SECOND)');
			$query->where($db->q($now).' < '.$db->qn('start'));
			$query->where($db->qn('id').' NOT IN ('.$squery.')');
		}
		
		$db->setQuery($query);
		$events = $db->loadColumn();
		if (empty($events))
			JFactory::getApplication()->close();
		
		foreach ($events as $cid) {
			$query->clear()
				->select($db->qn('id'))->select($db->qn('name'))
				->from($db->qn('#__rseventspro_events'))
				->where($db->qn('id').' = '.(int) $cid);
			
			$db->setQuery($query);
			$row = $db->loadObject();
			if (empty($row)) continue;
			
			echo JText::sprintf('COM_RSEVENTSPRO_EVENT_SENDING_REMINDERS',$row->name);
			
			$query->clear()
				->insert($db->qn('#__rseventspro_taxonomy'))
				->set($db->qn('type').' = '.$db->q('reminder'))
				->set($db->qn('ide').' = '.(int) $row->id)
				->set($db->qn('id').' = 1');
			
			$db->setQuery($query);
			$db->execute();
			
			//get subscribers 
			$query->clear()
				->select('DISTINCT '.$db->qn('email'))->select($db->qn('name'))
				->from($db->qn('#__rseventspro_users'))
				->where($db->qn('ide').' = '.(int) $row->id)
				->where($db->qn('state').' IN (0,1)');
			
			$db->setQuery($query);
			$subscribers = $db->loadObjectList();
			
			if (!empty($subscribers)) {
				foreach ($subscribers as $subscriber) {
					rseventsproEmails::reminder($subscriber->email,$row->id,$subscriber->name, $lang->getTag());
				}
			}
		}
		
		JFactory::getApplication()->close();
	}
	
	/**
	 *	Method to send post reminders
	 *
	 * @return
	 */
	public function postreminder() {
		$db		= JFactory::getDBO();
		$query	= $db->getQuery(true);
		$user	= JFactory::getUser();
		$lang	= JFactory::getLanguage();
		$id		= JFactory::getApplication()->input->getInt('id');
		$sid	= JFactory::getSession()->getId();
		$msg	= JText::_('COM_RSEVENTSPRO_GLOBAL_PERMISSION_DENIED');
		$type	= rseventsproHelper::getConfig('postreminder','int');
		
		$query->clear()
			->select($db->qn('id'))->select($db->qn('name'))->select($db->qn('end'))
			->select($db->qn('sid'))->select($db->qn('owner'))
			->select($db->qn('start'))->select($db->qn('allday'))
			->from($db->qn('#__rseventspro_events'))
			->where($db->qn('published').' = 1')
			->where($db->qn('completed').' = 1')
			->where($db->qn('id').' = '.$db->q($id));
		
		$db->setQuery($query);
		if ($event = $db->loadObject()) {
			$now = JFactory::getDate()->toUnix();
			
			if ($event->allday) {
				$date = JFactory::getDate($event->start);
				$date->modify('+1 days');
				$endunix = $date->toUnix();
			} else {
				$endunix = JFactory::getDate($event->end)->toUnix();
			}
			
			if ($endunix < $now && (rseventsproHelper::admin() || ($user->get('id') == $event->owner && !$user->get('guest')) || $event->sid == $sid)) {
				$query->clear()
					->select('DISTINCT '.$db->qn('email'))->select($db->qn('name'))
					->from($db->qn('#__rseventspro_users'))
					->where($db->qn('ide').' = '.(int) $id)
					->where($db->qn('state').' = 1');
				
				$db->setQuery($query);
				$subscribers = $db->loadObjectList();
				
				if (!empty($subscribers)) {
					foreach ($subscribers as $subscriber) {
						if ($type == 0) {
							$query->clear()
								->select($db->qn('id'))
								->from($db->qn('#__rseventspro_taxonomy'))
								->where($db->qn('type').' = '.$db->q('preminder'))
								->where($db->qn('ide').' = '.$db->q($event->id))
								->where($db->qn('extra').' = '.$db->q($subscriber->email));
							$db->setQuery($query);
							if ($db->loadResult()) {
								continue;
							}
						}
						
						rseventsproEmails::postreminder($subscriber->email,$id,$subscriber->name,$lang->getTag());
						
						if ($type == 0) {
							$query->clear()
								->select('MAX('.$db->qn('id').')')
								->from($db->qn('#__rseventspro_taxonomy'))
								->where($db->qn('type').' = '.$db->q('preminder'))
								->where($db->qn('ide').' = '.$db->q($event->id));
							$db->setQuery($query);
							$id = (int) $db->loadResult() + 1;
							
							$query->clear()
								->insert($db->qn('#__rseventspro_taxonomy'))
								->set($db->qn('type').' = '.$db->q('preminder'))
								->set($db->qn('ide').' = '.$db->q($event->id))
								->set($db->qn('id').' = '.$db->q($id))
								->set($db->qn('extra').' = '.$db->q($subscriber->email));
							$db->setQuery($query);
							$db->execute();
						}
					}
				}
				$msg = JText::_('COM_RSEVENTSPRO_EVENT_POSTREMINDERS_SENT');
			}
		
			return $this->setRedirect(rseventsproHelper::route('index.php?option=com_rseventspro&layout=show&id='.rseventsproHelper::sef($event->id,$event->name),false,rseventsproHelper::itemid($event->id)), $msg);
		}
	}
	
	/**
	 *	Method to send auto post reminders
	 *
	 * @return
	 */
	public function autopostreminder() {
		$db		= JFactory::getDBO();
		$query	= $db->getQuery(true);
		$lang	= JFactory::getLanguage();
		$config	= rseventsproHelper::getConfig();
		$type	= $config->postreminder;
		$hash	= JFactory::getApplication()->input->getString('hash');
		$now	= JFactory::getDate()->toUnix();
		
		if ($config->auto_postreminder) {
			$secret = $config->postreminder_hash;
			
			if ($hash == $secret) {
				$query->clear()
					->select($db->qn('id'))->select($db->qn('name'))
					->select($db->qn('start'))->select($db->qn('end'))
					->select($db->qn('allday'))
					->from($db->qn('#__rseventspro_events'))
					->where($db->qn('published').' = 1')
					->where($db->qn('completed').' = 1');
				
				$db->setQuery($query);
				$events = $db->loadObjectList();
				
				foreach ($events as $event) {
					if ($event->allday) {
						$date = JFactory::getDate($event->start);
						$date->modify('+1 days');
						$endunix = $date->toUnix();
					} else {
						$endunix = JFactory::getDate($event->end)->toUnix();
					}
					
					if ($endunix < $now) {
						$query->clear()
							->select('DISTINCT '.$db->qn('email'))->select($db->qn('name'))
							->from($db->qn('#__rseventspro_users'))
							->where($db->qn('ide').' = '.(int) $event->id)
							->where($db->qn('state').' = 1');
						
						$db->setQuery($query);
						$subscribers = $db->loadObjectList();
						
						if (!empty($subscribers)) {
							foreach ($subscribers as $subscriber) {
								if ($type == 0) {
									$query->clear()
										->select($db->qn('id'))
										->from($db->qn('#__rseventspro_taxonomy'))
										->where($db->qn('type').' = '.$db->q('preminder'))
										->where($db->qn('ide').' = '.$db->q($event->id))
										->where($db->qn('extra').' = '.$db->q($subscriber->email));
									$db->setQuery($query);
									if ($db->loadResult()) {
										continue;
									}
								}
								
								echo 'Event ('.$event->name.') - Email ('.$subscriber->email.') <br />';
								rseventsproEmails::postreminder($subscriber->email,$event->id,$subscriber->name,$lang->getTag());
								
								if ($type == 0) {
									$query->clear()
										->select('MAX('.$db->qn('id').')')
										->from($db->qn('#__rseventspro_taxonomy'))
										->where($db->qn('type').' = '.$db->q('preminder'))
										->where($db->qn('ide').' = '.$db->q($event->id));
									$db->setQuery($query);
									$id = (int) $db->loadResult() + 1;
									
									$query->clear()
										->insert($db->qn('#__rseventspro_taxonomy'))
										->set($db->qn('type').' = '.$db->q('preminder'))
										->set($db->qn('ide').' = '.$db->q($event->id))
										->set($db->qn('id').' = '.$db->q($id))
										->set($db->qn('extra').' = '.$db->q($subscriber->email));
									$db->setQuery($query);
									$db->execute();
								}
							}
						}
					}
				}
			}
		}
		
		JFactory::getApplication()->close();
	}
	
	/**
	 *	Method to calculate the total
	 *
	 * @return
	 */
	public function total() {
		$app 		= JFactory::getApplication();
		$jinput		= $app->input;
		$db 		= JFactory::getDBO();
		$query		= $db->getQuery(true);
		$quantity	= $jinput->getInt('quantity',1);
		$tickets	= $jinput->get('tickets',array(),'array');
		$payment	= $jinput->getString('payment');
		$coupon		= $jinput->getString('coupon');
		$idevent	= $jinput->getInt('idevent',0);
		$type		= $jinput->getCmd('type','');
		$now		= JFactory::getDate();
		$nowunix	= $now->toUnix();
		$total		= 0;
		$discount	= 0;
		$info		= array();
		$return		= array();
		$cname		= '';
		$couponid	= 0;
		
		$eventtickets = array();
		
		if (!empty($tickets)) {
			// Get event
			$query->clear()
				->select($db->qn('discounts'))->select($db->qn('early_fee'))->select($db->qn('early_fee_type'))
				->select($db->qn('early_fee_end'))->select($db->qn('late_fee'))->select($db->qn('late_fee_type'))
				->select($db->qn('late_fee_start'))
				->from($db->qn('#__rseventspro_events'))
				->where($db->qn('id').' = '.(int) $idevent);
			
			$db->setQuery($query);
			$event = $db->loadObject();
			
			foreach ($tickets as $tid => $quantity) {
				$checkticket = rseventsproHelper::checkticket($tid);
				if ($checkticket == RSEPRO_TICKETS_NOT_AVAILABLE) continue;
				
				$query->clear()
					->select($db->qn('price'))
					->from($db->qn('#__rseventspro_tickets'))
					->where($db->qn('id').' = '.(int) $tid);
				
				$db->setQuery($query);
				$price = $db->loadResult();
				
				if ($checkticket > RSEPRO_TICKETS_UNLIMITED && $quantity > $checkticket) $quantity = $checkticket;
				
				$eventtickets[$tid] = $quantity;
				
				// Calculate the total
				if ($price > 0) {
					$price = $price * $quantity;
					if ($event->discounts) {
						$eventdiscount = rseventsproHelper::discount($idevent,$price);
						if (is_array($eventdiscount)) {
							$query->clear()
								->select($db->qn('c.action'))
								->from($db->qn('#__rseventspro_coupons','c'))
								->join('left', $db->qn('#__rseventspro_coupon_codes','cc').' ON '.$db->qn('cc.idc').' = '.$db->qn('c.id'))
								->where($db->qn('cc.id').' = '.(int) $eventdiscount['id']);
							
							$db->setQuery($query);
							$couponaction = (int) $db->loadResult();
							
							if ($couponaction == 0)
								$discount += $eventdiscount['discount'] * $quantity;
							$couponid = $eventdiscount['id'];
						}
					}
					$total += $price;
				}
			}
			
			if ($event->discounts) {
				$eventdiscount = rseventsproHelper::discount($idevent,$total);
				if (is_array($eventdiscount)) {
					$query->clear()
						->select($db->qn('c.action'))
						->from($db->qn('#__rseventspro_coupons','c'))
						->join('left', $db->qn('#__rseventspro_coupon_codes','cc').' ON '.$db->qn('cc.idc').' = '.$db->qn('c.id'))
						->where($db->qn('cc.id').' = '.(int) $eventdiscount['id']);
					
					$db->setQuery($query);
					$couponaction = $db->loadResult();
					
					if ($couponaction == 1)
						$discount += $eventdiscount['discount'];
					$couponid = $eventdiscount['id'];
				}
			}
			
			if ($event->discounts && $couponid) {
				$query->clear()
					->select($db->qn('c.name'))
					->from($db->qn('#__rseventspro_coupons','c'))
					->join('left',$db->qn('#__rseventspro_coupon_codes','cc').' ON '.$db->qn('cc.idc').' = '.$db->qn('c.id'))
					->where($db->qn('cc.id').' = '.(int) $couponid);
				
				$db->setQuery($query);
				$cname = $db->loadResult();
			}
			
			// Check for a global discount, and if found ignore the event discount
			if ($event->discounts) {
				if ($globalDiscount = rseventsproHelper::globalDiscount($idevent, $total, $eventtickets, $payment)) {
					$discount = $globalDiscount['discount'];
					$cname = $globalDiscount['name'];
				}
			}
			
			// Update the total after the discount
			$total = $total - $discount;
			
			if ($discount) {
				$info[] = JText::sprintf('COM_RSEVENTSPRO_DISCOUNT_ADDED',rseventsproHelper::currency($discount));
				$return['discount'] = rseventsproHelper::currency($discount);
				$return['discountname'] = $cname;
			}
			
			// Apply early fee
			if ($total > 0 && $event->discounts) {
				if (!empty($event->early_fee_end) && $event->early_fee_end != $db->getNullDate()) {
					$early_fee_unix = JFactory::getDate($event->early_fee_end)->toUnix();
					if ($early_fee_unix > $nowunix) {
						$early = rseventsproHelper::setTax($total,$event->early_fee_type,$event->early_fee);
						$total = $total - $early;
						
						if ($early) {
							$info[] = JText::sprintf('COM_RSEVENTSPRO_EARLY_FEE_ADDED',rseventsproHelper::currency($early));
							$return['earlybooking'] = rseventsproHelper::currency($early);
						}
					}
				}
			}
			
			// Apply late fee
			if ($total > 0 && $event->discounts) {
				if (!empty($event->late_fee_start) && $event->late_fee_start != $db->getNullDate()) {
					$late_fee_unix = JFactory::getDate($event->late_fee_start)->toUnix();
					if ($late_fee_unix < $nowunix) {
						$late = rseventsproHelper::setTax($total,$event->late_fee_type,$event->late_fee);
						$total = $total + $late;
						
						if ($late) {
							$info[] = JText::sprintf('COM_RSEVENTSPRO_LATE_FEE_ADDED',rseventsproHelper::currency($late));
							$return['latefee'] = rseventsproHelper::currency($late);
						}
					}
				}
			}
			
			// Apply tax
			// Check to see if the selected payment type is a wire payment
			$query->clear()
				->select($db->qn('id'))->select($db->qn('name'))
				->select($db->qn('tax_type'))->select($db->qn('tax_value'))
				->from($db->qn('#__rseventspro_payments'))
				->where($db->qn('id').' = '.(int) $payment);
			
			$db->setQuery($query);
			$wire = $db->loadObject();
			
			if ($total > 0) {
				if (!empty($wire)) {
					$tax = rseventsproHelper::setTax($total,$wire->tax_type,$wire->tax_value);
					$total = $total + $tax;
					
					if ($tax) {
						$info[] = JText::sprintf('COM_RSEVENTSPRO_TAX_ADDED',rseventsproHelper::currency($tax));
						$return['tax'] = rseventsproHelper::currency($tax);
					}
					
				} else {
					$plugintaxes = $app->triggerEvent('rsepro_tax',array(array('method'=>&$payment, 'total'=>$total)));
					
					if (!empty($plugintaxes))
						foreach ($plugintaxes as $plugintax)
							if (!empty($plugintax)) $tax = $plugintax;
					
					$total = $total + $tax;
					
					if ($tax) {
						$info[] = JText::sprintf('COM_RSEVENTSPRO_TAX_ADDED',rseventsproHelper::currency($tax));
						$return['tax'] = rseventsproHelper::currency($tax);
					}
				}
			}
		}
		
		$total 	= $total < 0 ? 0 : $total;
		$total 	= rseventsproHelper::currency($total);
		$info	= '|'.implode('<br />',$info);
		$return['total'] = $total;
		
		if ($type == 'json') {
			echo json_encode($return);
		} else {
			header('Content-type: text/html; charset=utf-8');
			echo 'RS_DELIMITER0'.$total.$info.'RS_DELIMITER1';
		}
		
		exit();
	}
	
	public function loadfile() {
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);
		$id		= JFactory::getApplication()->input->getInt('id');
		
		$query->select('*')
			->from($db->qn('#__rseventspro_files'))
			->where($db->qn('id').' = '.$id);
		
		$db->setQuery($query);
		if ($file = $db->loadObject()) {
			if ($file->permissions == '') {
				$file->permissions = '000000';
			}
		}
		
		echo json_encode($file);
		JFactory::getApplication()->close();
	}
	
	public function ticket() {
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);
		$id		= JFactory::getApplication()->input->getInt('id');
		
		$query->select('*')
			->from($db->qn('#__rseventspro_tickets'))
			->where($db->qn('id').' = '.$id);
		
		$db->setQuery($query);
		if ($ticket = $db->loadObject()) {
			$response = new stdClass();
			$response->name			= $ticket->name;
			$response->price		= rseventsproHelper::currency($ticket->price);
			$response->tprice		= $ticket->price;
			$response->mask			= rseventsproHelper::currency(0,true);
			$response->description	= $ticket->description;
			
			$response->payment_decimals	= rseventsproHelper::getConfig('payment_decimals','int');
			$response->payment_decimal	= rseventsproHelper::getConfig('payment_decimal');
			$response->payment_thousands	= rseventsproHelper::getConfig('payment_thousands');
			
			echo json_encode($response);
		}
		
		JFactory::getApplication()->close();
	}
	
	public function singleticket() {
		$db		  = JFactory::getDbo();
		$query	  = $db->getQuery(true);
		$id		  = JFactory::getApplication()->input->getInt('id');
		$quantity = JFactory::getApplication()->input->getInt('quantity',1);
		$return	  = new stdClass();
		
		$return->seats = rseventsproHelper::checkticket($id);
		
		$query->select('*')
			->from($db->qn('#__rseventspro_tickets'))
			->where($db->qn('id').' = '.$id);
		
		$db->setQuery($query);
		if ($ticket = $db->loadObject()) {
			$return->name			= $ticket->name;
			$return->price			= rseventsproHelper::currency($ticket->price);
			$return->tprice			= $ticket->price;
			$return->mask			= rseventsproHelper::currency(0,true);
			$return->description	= $ticket->description;
			
			$return->payment_decimals	= rseventsproHelper::getConfig('payment_decimals','int');
			$return->payment_decimal	= rseventsproHelper::getConfig('payment_decimal');
			$return->payment_thousands	= rseventsproHelper::getConfig('payment_thousands');
		}
		
		echo json_encode($return);
		JFactory::getApplication()->close();
	}
	
	public function image() {
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);
		$app	= JFactory::getApplication();
		$input	= $app->input;
		$width	= $input->getString('width','small');
		$height	= $input->getInt('height',0);
		
		if ($width == 'big') {
			$width = (int) rseventsproHelper::getConfig('icon_big_width');
		} elseif ($width == 'small') {
			$width = (int) rseventsproHelper::getConfig('icon_small_width');
		} else {
			$width = (int) $width;
		}
		
		$query->select($db->qn('name'))
			->select($db->qn('icon'))->select($db->qn('properties'))
			->from($db->qn('#__rseventspro_events'))
			->where($db->qn('id').' = '.$db->q($input->getInt('id')));
		$db->setQuery($query);
		if ($event = $db->loadObject()) {
			$cache = JFactory::getCache('com_rseventspro');
			$cache->setCaching(true);
			if ($data = $cache->call(array('rseventsproHelper', 'createImage'), $event, $width, $height)) {
				@ob_end_clean();
				require_once JPATH_SITE.'/components/com_rseventspro/helpers/phpthumb/phpthumb.functions.php';
				header('Content-Type: '.phpthumb_functions::ImageTypeToMIMEtype($data['ext']));
				header('Content-Disposition: inline; filename="' . JFilterOutput::stringURLSafe($event->name) . '"');
				echo $data['content'];
				$app->close();
			}
		}
		
		return false;
	}
	
	public function timezone() {
		$input		= JFactory::getApplication()->input;
		$timezone	= $input->getString('timezone');
		$return		= base64_decode($input->getString('return'));
		$session	= JFactory::getSession();
		
		$session->set('rsepro.timezone', $timezone);
		
		$this->setRedirect($return);
	}
	
	// Trigger plugin functions
	public function trigger() {
		JFactory::getApplication()->triggerEvent('rsepro_frontTrigger');
	}
}