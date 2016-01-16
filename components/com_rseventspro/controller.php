<?php
/**
* @version 1.0.0
* @package RSEvents!Pro 1.0.0
* @copyright (C) 2011 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' ); 
jimport( 'joomla.application.component.controller' );

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
		
		$query->clear()
			->select($db->qn('cc.id'))->select($db->qn('cc.used'))->select($db->qn('c.from'))
			->select($db->qn('c.to'))->select($db->qn('c.usage'))
			->from($db->qn('#__rseventspro_coupon_codes','cc'))
			->join('left', $db->qn('#__rseventspro_coupons','c').' ON '.$db->qn('cc.idc').' = '.$db->qn('c.id'))
			->where($db->qn('c.ide').' = '.$id)
			->where($db->qn('cc.code').' = '.$db->q($coupon));
		
		$db->setQuery($query);
		if ($data = $db->loadObject()) {
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
		$session = JFactory::getSession();
		$secret  = JFactory::getApplication()->input->getString('secret');

		echo 'RS_DELIMITER0';
		echo ($session->get('security_number') == $secret) ? 1 : 0;
		echo 'RS_DELIMITER1';
		JFactory::getApplication()->close();
	}
	
	/**
	 *	Method to connect to Yahoo! or Gmail
	 *
	 * @return string
	 */
	public function connect() {
		require_once JPATH_SITE.'/components/com_rseventspro/helpers/invite.php';
		
		$username = JFactory::getApplication()->input->getString('username');
		$password = JFactory::getApplication()->input->getString('password');
		$type	  = JFactory::getApplication()->input->getString('type');
		
		echo 'RS_DELIMITER0';
		
		if ($type == 'yahoo')
			echo RSYahoo::results($username,$password);
		else if ($type = 'gmail')
			echo RSGoogle::results($username,$password);
		
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
			->select($db->qn('u.tax'))->select($db->qn('u.verification'))
			->from($db->qn('#__rseventspro_users','u'))
			->where('MD5(CONCAT('.$db->qn('u.id').','.$db->qn('u.name').','.$db->qn('u.email').')) = '.$db->q($hash));
		
		$db->setQuery($query);
		$details = $db->loadObject();
		
		$query->clear()
			->select($db->qn('ut.quantity'))->select($db->qn('t.name'))->select($db->qn('t.price'))
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
		echo 'RS_DELIMITER0';
		echo rseventsproHelper::repeats();
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
				
				$html .= '<li><a '.$open.' href="'.rseventsproHelper::route('index.php?option=com_rseventspro&layout=show&id='.rseventsproHelper::sef($event->id,$event->name),true,$itemid).'">'.$event->name.'</a></li>';
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
		$user			= $juser->get('id') > 0 ? $juser->get('id') : JFactory::getSession()->getId();
		
		if (!empty($key)) {
			$query->clear()
				->select($db->qn('id'))->select($db->qn('name'))
				->select($db->qn('location'))->select($db->qn('owner'))
				->from($db->qn('#__rseventspro_events'))
				->where('MD5(CONCAT('.$db->q('event').','.$db->qn('id').')) = '.$db->q($key));
			
			$db->setQuery($query,0,1);
			$event = $db->loadObject();
			
			if ($admin || (!empty($permissions['can_edit_events']) && $user != $event->owner) || !empty($permissions['can_approve_events'])) {
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
						return $this->setRedirect(rseventsproHelper::route('index.php?option=com_rseventspro&layout=show&id='.rseventsproHelper::sef($event->id,$event->name),false),JText::_('COM_RSEVENTSPRO_EVENT_PUBLISHED'));
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
		
		return $this->setRedirect(rseventsproHelper::route('index.php?option=com_rseventspro&layout=show&id='.rseventsproHelper::sef($event->id,$event->name), false), $msg);
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
		
		$msg = JText::_('COM_RSEVENTSPRO_GLOBAL_PERMISSION_DENIED');
		
		$query->clear()
			->select($db->qn('id'))->select($db->qn('name'))->select($db->qn('end'))
			->select($db->qn('sid'))->select($db->qn('owner'))
			->select($db->qn('start'))->select($db->qn('allday'))
			->from($db->qn('#__rseventspro_events'))
			->where($db->qn('id').' = '.$db->q($id));
		
		$db->setQuery($query);
		if ($event = $db->loadObject()) {
			$now			= rseventsproHelper::date('now',null,false,true);
			$now->setTZByID($now->getTZID());
			$now->convertTZ(new RSDate_Timezone('GMT'));
			$nowunix = $now->getDate(RSDATE_FORMAT_UNIXTIME);
			
			if ($event->allday) {
				$date = rseventsproHelper::date($event->start,null,false,true);
				$date->addSeconds(86400);
				$date->setTZByID($date->getTZID());
				$date->convertTZ(new RSDate_Timezone('GMT'));
				$endunix = $date->getDate(RSDATE_FORMAT_UNIXTIME);
			} else {
				$endunix = JFactory::getDate($event->end)->toUnix();
			}
			
			if ($endunix < $nowunix && (rseventsproHelper::admin() || ($user->get('id') == $event->owner && !$user->get('guest')) || $event->sid == $sid)) {
				$query->clear()
					->select('DISTINCT '.$db->qn('email'))->select($db->qn('name'))
					->from($db->qn('#__rseventspro_users'))
					->where($db->qn('ide').' = '.(int) $id)
					->where($db->qn('state').' = 1');
				
				$db->setQuery($query);
				$subscribers = $db->loadObjectList();
				
				if (!empty($subscribers)) {
					foreach ($subscribers as $subscriber) {
						rseventsproEmails::postreminder($subscriber->email,$id,$subscriber->name,$lang->getTag());
					}
				}
				$msg = JText::_('COM_RSEVENTSPRO_EVENT_POSTREMINDERS_SENT');
			}
		
			return $this->setRedirect(rseventsproHelper::route('index.php?option=com_rseventspro&layout=show&id='.rseventsproHelper::sef($event->id,$event->name), false), $msg);
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
		$hash	= JFactory::getApplication()->input->getString('hash');
		
		if ($config->auto_postreminder) {
			$secret = $config->postreminder_hash;
			
			if ($hash == $secret) {
				$now			= rseventsproHelper::date('now',null,false,true);
				$now->setTZByID($now->getTZID());
				$now->convertTZ(new RSDate_Timezone('GMT'));
				$nowunix = $now->getDate(RSDATE_FORMAT_UNIXTIME);
			
				$query->clear()
					->select($db->qn('id'))->select($db->qn('start'))
					->select($db->qn('end'))->select($db->qn('allday'))
					->from($db->qn('#__rseventspro_events'));
				$db->setQuery($query);
				$events = $db->loadObjectList();
				
				foreach ($events as $event) {
					if ($event->allday) {
						$date = rseventsproHelper::date($event->start,null,false,true);
						$date->addSeconds(86400);
						$date->setTZByID($date->getTZID());
						$date->convertTZ(new RSDate_Timezone('GMT'));
						$endunix = $date->getDate(RSDATE_FORMAT_UNIXTIME);
					} else {
						$endunix = JFactory::getDate($event->end)->toUnix();
					}
					
					if ($endunix < $nowunix) {
						$query->clear()
							->select('DISTINCT '.$db->qn('email'))->select($db->qn('name'))
							->from($db->qn('#__rseventspro_users'))
							->where($db->qn('ide').' = '.(int) $event->id)
							->where($db->qn('state').' = 1');
						
						$db->setQuery($query);
						$subscribers = $db->loadObjectList();
						
						if (!empty($subscribers)) {
							foreach ($subscribers as $subscriber) {
								rseventsproEmails::postreminder($subscriber->email,$event->id,$subscriber->name,$lang->getTag());
							}
						}
					}
				}
			}
		}
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
		$now		= JFactory::getDate();
		$nowunix	= $now->toUnix();
		$total		= 0;
		$discount	= 0;
		$info		= array();
		
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
				}
			}
			
			// Update the total after the discount
			$total = $total - $discount;
			
			if ($discount) {
				$info[] = JText::sprintf('COM_RSEVENTSPRO_DISCOUNT_ADDED',rseventsproHelper::currency($discount));
			}
			
			// Apply early fee
			if ($total > 0) {
				if (!empty($event->early_fee_end) && $event->early_fee_end != $db->getNullDate()) {
					$early_fee_unix = JFactory::getDate($event->early_fee_end)->toUnix();
					if ($early_fee_unix > $nowunix) {
						$early = rseventsproHelper::setTax($total,$event->early_fee_type,$event->early_fee);
						$total = $total - $early;
						
						if ($early) {
							$info[] = JText::sprintf('COM_RSEVENTSPRO_EARLY_FEE_ADDED',rseventsproHelper::currency($early));
						}
					}
				}
			}
			
			// Apply late fee
			if ($total > 0) {
				if (!empty($event->late_fee_start) && $event->late_fee_start != $db->getNullDate()) {
					$late_fee_unix = JFactory::getDate($event->late_fee_start)->toUnix();
					if ($late_fee_unix < $nowunix) {
						$late = rseventsproHelper::setTax($total,$event->late_fee_type,$event->late_fee);
						$total = $total + $late;
						
						if ($late) {
							$info[] = JText::sprintf('COM_RSEVENTSPRO_LATE_FEE_ADDED',rseventsproHelper::currency($late));
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
					}
					
				} else {
					$plugintaxes = $app->triggerEvent('rsepro_tax',array(array('method'=>&$payment, 'total'=>$total)));
					
					if (!empty($plugintaxes))
						foreach ($plugintaxes as $plugintax)
							if (!empty($plugintax)) $tax = $plugintax;
					
					$total = $total + $tax;
					
					if ($tax) {
						$info[] = JText::sprintf('COM_RSEVENTSPRO_TAX_ADDED',rseventsproHelper::currency($tax));
					}
				}
			}
		}
		
		$total 	= $total < 0 ? 0 : $total;
		$total 	= rseventsproHelper::currency($total);
		$info	= '|'.implode('<br />',$info);
		header('Content-type: text/html; charset=utf-8');
		echo 'RS_DELIMITER0'.$total.$info.'RS_DELIMITER1';
		exit();
	}
}