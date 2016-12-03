<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' );

class rseventsproModelImports extends JModelLegacy
{
	protected $_tz = 0;
	
	/**
	 * Constructor.
	 */
	public function __construct() {
		parent::__construct();
		$this->_tz = JFactory::getApplication()->input->getInt('offset',0) * 3600;
	}
	
	/**
	 * Method to set the side bar.
	 */
	public function getSidebar() {
		if (rseventsproHelper::isJ3()) {
			return JHtmlSidebar::render();
		}
		
		return;
	}
	
	/*
	 *	Method to get available items
	 */
	public function getItems() {
		$db = JFactory::getDbo();
		
		$items = array('rsevents' => false, 'jevents' => false, 'jcalpro' => false,
			'ohanah' => false, 'eventlist' => false, 'eventlistbeta' => false);
		
		if (file_exists(JPATH_ADMINISTRATOR.'/components/com_rsevents/rsevents.php'))
			$items['rsevents'] = true;
		
		if (file_exists(JPATH_ADMINISTRATOR.'/components/com_jevents/jevents.php'))
			$items['jevents'] = true;
		
		if (file_exists(JPATH_ADMINISTRATOR.'/components/com_jcalpro/jcalpro.xml'))
			$items['jcalpro'] = true;
		
		if (file_exists(JPATH_ADMINISTRATOR.'/components/com_ohanah/ohanah.php'))
			$items['ohanah'] = true;
		
		$beta = false;
		$elist = file_exists(JPATH_ADMINISTRATOR.'/components/com_eventlist/admin.eventlist.php');
		if ($elist) {
			$db->setQuery("DESCRIBE #__eventlist_cats_event_relations");
			$beta = $db->loadObjectList();
		}
		
		if ($elist && empty($beta))
			$items['eventlist'] = true;
		
		if ($elist && !empty($beta))
			$items['eventlistbeta'] = true;
		
		return $items;
	}
	
	/*
	 *	Method to get the locations list
	 */
	public function getLocations() {
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$default = array(JHTML::_('select.option', 0, JText::_('COM_RSEVENTSPRO_IMPORT_LOCATION_DEFAULT')));
		
		$query->clear()
			->select($db->qn('id','value'))
			->select($db->qn('name','text'))
			->from($db->qn('#__rseventspro_locations'))
			->where($db->qn('published').' = 1');
		$db->setQuery($query);
		if ($locations = $db->loadObjectList()) {
			return array_merge($default, $locations);
		}
		
		return $default;
	}
	
	/**
	 * Method to get time offsets.
	 */
	public function getOffsets() {
		return array(
			JHTML::_('select.option', -12, '-12:00'),
			JHTML::_('select.option', -11, '-11:00'),
			JHTML::_('select.option', -10, '-10:00'),
			JHTML::_('select.option', -9.5, '-09:30'),
			JHTML::_('select.option', -9, '-09:00'),
			JHTML::_('select.option', -8, '-08:00'),
			JHTML::_('select.option', -7, '-07:00'),
			JHTML::_('select.option', -6, '-06:00'),
			JHTML::_('select.option', -5, '-05:00'),
			JHTML::_('select.option', -4.5, '-04:30'),
			JHTML::_('select.option', -4, '-04:00'),
			JHTML::_('select.option', -3.5, '-03:30'),
			JHTML::_('select.option', -3, '-03:00'),
			JHTML::_('select.option', -2, '-02:00'),
			JHTML::_('select.option', -1, '-01:00'),
			JHTML::_('select.option', 0, '00:00'),
			JHTML::_('select.option', 1, '+01:00'),
			JHTML::_('select.option', 2, '+02:00'),
			JHTML::_('select.option', 3, '+03:00'),
			JHTML::_('select.option', 3.5, '+03:30'),
			JHTML::_('select.option', 4, '+04:00'),
			JHTML::_('select.option', 4.5, '+04:30'),
			JHTML::_('select.option', 5, '+05:00'),
			JHTML::_('select.option', 5.5, '+05:30'),
			JHTML::_('select.option', 5.75, '+05:45'),
			JHTML::_('select.option', 6, '+06:00'),
			JHTML::_('select.option', 6.5, '+06:30'),
			JHTML::_('select.option', 7, '+07:00'),
			JHTML::_('select.option', 8, '+08:00'),
			JHTML::_('select.option', 8.75, '+08:00'),
			JHTML::_('select.option', 9, '+09:00'),
			JHTML::_('select.option', 9.5, '+09:30'),
			JHTML::_('select.option', 10, '+10:00'),
			JHTML::_('select.option', 10.5, '+10:30'),
			JHTML::_('select.option', 11, '+11:00'),
			JHTML::_('select.option', 11.5, '+11:30'),
			JHTML::_('select.option', 12, '+12:00'),
			JHTML::_('select.option', 12.75, '+12:45'),
			JHTML::_('select.option', 13, '+13:00'),
			JHTML::_('select.option', 14, '+14:00')
		);
	}
	
	
	public function rsevents() {
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);
		
		// Get events
		$query->clear()
			->select($db->qn('IdEvent'))->select($db->qn('IdLocation'))->select($db->qn('IdUser'))->select($db->qn('EventName'))->select($db->qn('EventURL'))
			->select($db->qn('EventPhone'))->select($db->qn('EventEmail'))->select($db->qn('EventDescription'))->select($db->qn('EventOverbooking'))->select($db->qn('EventStartDate'))
			->select($db->qn('EventEndDate'))->select($db->qn('EventEnableRegistration'))->select($db->qn('EventShowGuest'))->select($db->qn('EventIcon'))->select($db->qn('EventEnableComments'))
			->select($db->qn('published'))->select($db->qn('EventAutoApprove'))->select($db->qn('EventKeywords'))->select($db->qn('EventMetaDescription'))->select($db->qn('EventPageTitle'))
			->select($db->qn('EventStartRegistration'))->select($db->qn('EventEndRegistration'))->select($db->qn('SubscriptionNotification'))->select($db->qn('early_discount_type'))->select($db->qn('early_discount_value'))
			->select($db->qn('early_discount_deadline'))->select($db->qn('late_fee_type'))->select($db->qn('late_fee_value'))->select($db->qn('late_fee_date'))
			->from($db->qn('#__rsevents_events'))
			->where($db->qn('IdParent').' = 0');
		
		$db->setQuery($query);
		$events = $db->loadObjectList();
		
		if (!empty($events)) {
			// GET RSEvents! categories
			$query->clear()
				->select($db->qn('IdCategory'))
				->from($db->qn('#__rsevents_events_cat'));
			
			$db->setQuery($query);
			$categories = $db->loadColumn();
			
			$thecategories = array();
			if (!empty($categories)) {
				JArrayHelper::toInteger($categories);
				$categories = array_unique($categories);
				
				foreach ($categories as $category) {
					$query->clear()
						->select($db->qn('CategoryName'))->select($db->qn('CategoryColor'))
						->select($db->qn('CategoryDescription'))->select($db->qn('published'))
						->from($db->qn('#__rsevents_categories'))
						->where($db->qn('IdCategory').' = '.(int) $category);
					
					$db->setQuery($query);
					$cat = $db->loadObject();
					
					if (!empty($cat)) {
						$data = array();
						$data['published'] = $cat->published;
						$data['title'] = $cat->CategoryName;
						$data['description'] = $cat->CategoryDescription;
						$data['parent_id'] = 1;
						$registry = new JRegistry;
						$registry->loadArray(array('color' => $cat->CategoryColor));
						$data['params'] = $registry->toString();
						
						$newcategory = $this->_savecategory($data);
						$thecategories[$category] = $newcategory;
					}
				}
			}
			
			// GET RSEvents! locations
			$query->clear()
				->select($db->qn('IdLocation'))->select($db->qn('LocationName'))->select($db->qn('LocationDescription'))->select($db->qn('LocationURL'))
				->select($db->qn('LocationCity'))->select($db->qn('LocationAddress'))->select($db->qn('LocationZip'))->select($db->qn('LocationState'))
				->select($db->qn('LocationCountry'))->select($db->qn('LocationLat'))->select($db->qn('LocationLon'))->select($db->qn('published'))
				->from($db->qn('#__rsevents_locations'));
			
			$db->setQuery($query);
			$locations = $db->loadObjectList();
			
			$thelocations = array();
			if (!empty($locations)) {
				foreach ($locations as $location) {
						// IMPORT locations
						$address = $location->LocationAddress;
						if (!empty($location->LocationZip)) $address .= ' , '.$location->LocationZip;
						if (!empty($location->LocationCity)) $address .= ' , '.$location->LocationCity;
						if (!empty($location->LocationState)) $address .= ' , '.$location->LocationState;
						if (!empty($location->LocationCountry)) $address .= ' , '.$location->LocationCountry;
						$coordinates = !empty($location->LocationLat) && !empty($location->LocationLon) ? $location->LocationLat.','.$location->LocationLon : '';
						
						$data = array();
						$data['name'] = $location->LocationName;
						$data['url'] = $location->LocationURL;
						$data['address'] = $address;
						$data['description'] = $location->LocationDescription;
						$data['coordinates'] = $coordinates;
						$data['published'] = $location->published;
						
						$newlocation = $this->_savelocation($data);
						$thelocations[$location->IdLocation] = $newlocation;
					}
			}
			
			foreach ($events as $event) {
				$id = $this->repeatevent($event,0,$thecategories,$thelocations);
				if ($id) {
					$counter++;
					
					// Check for children
					$query->clear()
						->select($db->qn('IdEvent'))->select($db->qn('IdLocation'))->select($db->qn('IdUser'))->select($db->qn('EventName'))->select($db->qn('EventURL'))->select($db->qn('EventPhone'))->select($db->qn('EventEmail'))->select($db->qn('EventDescription'))->select($db->qn('EventOverbooking'))->select($db->qn('EventStartDate'))->select($db->qn('EventEndDate'))->select($db->qn('EventEnableRegistration'))->select($db->qn('EventShowGuest'))->select($db->qn('EventIcon'))->select($db->qn('EventEnableComments'))->select($db->qn('published'))->select($db->qn('EventAutoApprove'))->select($db->qn('EventKeywords'))->select($db->qn('EventMetaDescription'))->select($db->qn('EventPageTitle'))->select($db->qn('EventStartRegistration'))->select($db->qn('EventEndRegistration'))->select($db->qn('SubscriptionNotification'))->select($db->qn('early_discount_type'))->select($db->qn('early_discount_value'))->select($db->qn('early_discount_deadline'))->select($db->qn('late_fee_type'))->select($db->qn('late_fee_value'))->select($db->qn('late_fee_date'))
						->from($db->qn('#__rsevents_events'))
						->where($db->qn('IdParent').' = '.(int) $event->IdEvent);
					
					
					$db->setQuery($query);
					$childs = $db->loadObjectList();
					
					if (!empty($childs)) {
						foreach ($childs as $child) {
							$this->repeatevent($child,$id,$thecategories,$thelocations);
							$counter++;
						}
					}
				}
			}
		}
		
		if ($counter) {
			return $counter;
		}
		
		$this->setError(JText::_('COM_RSEVENTSPRO_IMPORT_NO_DATA'));
		return false;
	}
	
	
	protected function repeatevent($event, $parent, $thecategories, $thelocations) {
		jimport('joomla.filesystem.file');
		
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);
		$size	= 0;
		
		$query->clear()
			->select($db->qn('ConfigValue'))
			->from($db->qn('#__rsevents_config'))
			->where($db->qn('ConfigName').' = '.$db->q('event.icon.small'));
		
		$db->setQuery($query);
		$small = (int) $db->loadResult();
		
		$query->clear()
			->select($db->qn('ConfigValue'))
			->from($db->qn('#__rsevents_config'))
			->where($db->qn('ConfigName').' = '.$db->q('event.icon.big'));
		
		$db->setQuery($query);
		$big = (int) $db->loadResult();
		
		if (!empty($big) || !empty($small))
			$size = max($big,$size);
		
		$start = $event->EventStartDate + $this->_tz;
		$end = $event->EventEndDate + $this->_tz;
		
		// IMPORT events
		$data = array();
		$data['name'] = $event->EventName;
		$data['start'] = JFactory::getDate($start)->toSql();
		$data['end'] = JFactory::getDate($end)->toSql();
		$data['description'] = $event->EventDescription;
		$data['location'] = (int) $thelocations[$event->IdLocation];
		$data['owner'] = (int) $event->IdUser;
		$data['URL'] = $event->EventURL;
		$data['email'] = $event->EventEmail;
		$data['phone'] = $event->EventPhone;
		$data['metaname'] = $event->EventPageTitle;
		$data['metakeywords'] = $event->EventKeywords;
		$data['metadescription'] = $event->EventMetaDescription;
		$data['notify_me'] = $event->SubscriptionNotification;
		$data['overbooking'] = $event->EventOverbooking;
		$data['show_registered'] = $event->EventShowGuest;
		$data['automatically_approve'] = $event->EventAutoApprove;
		$data['early_fee'] = $event->early_discount_value;
		$data['late_fee'] = $event->late_fee_value;
		$data['published'] = $event->published;
		$data['registration'] = (int) $event->EventEnableRegistration;
		$data['comments'] = (int) $event->EventEnableComments;
		$data['completed'] = 1;
		$data['parent'] = (int) $parent;
		$data['early_fee_type'] = $event->early_discount_type == 0 ? 1 : 0;
		$data['late_fee_type'] = $event->late_fee_type == 0 ? 1 : 0;
		$data['early_fee_end'] = !empty($event->early_discount_deadline) ? JFactory::getDate($event->early_discount_deadline + $this->_tz)->toSql() : '';
		$data['late_fee_start'] = !empty($event->late_fee_date) ? JFactory::getDate($event->late_fee_date + $this->_tz)->toSql() : '';
		$data['start_registration'] = !empty($event->EventStartRegistration) ? JFactory::getDate($event->EventStartRegistration + $this->_tz)->toSql() : '';
		$data['end_registration'] = !empty($event->EventEndRegistration) ? JFactory::getDate($event->EventEndRegistration + $this->_tz)->toSql() : '';
		
		$idevent = $this->_saveevent($data);
		
		// IMPORT categories
		$query->clear()
			->select($db->qn('IdCategory'))
			->from($db->qn('#__rsevents_events_cat'))
			->where($db->qn('IdEvent').' = '.(int) $event->IdEvent);
		
		$db->setQuery($query);
		$evcategories = $db->loadColumn();
		
		if (!empty($evcategories)) {
			foreach ($evcategories as $evcategory) {
				$evcategory = (int) $evcategory;
				$evcat = isset($thecategories[$evcategory]) ? $thecategories[$evcategory] : 0;
				
				if (!empty($evcat)) {
					$query->clear()
						->insert($db->qn('#__rseventspro_taxonomy'))
						->set($db->qn('type').' = '.$db->q('category'))
						->set($db->qn('id').' = '.(int) $evcat)
						->set($db->qn('ide').' = '.(int) $idevent);
					
					$db->setQuery($query);
					$db->execute();
				}
			}
		}
		
		// GET RSEvents! tickets & coupons
		if ($event->EventEnableRegistration) {
			$ticket_relation = array();
			
			$query->clear()
				->select($db->qn('IdTicket'))->select($db->qn('TicketName'))->select($db->qn('TicketPrice'))
				->select($db->qn('TicketMaxAudience'))->select($db->qn('TicketsPerUser'))->select($db->qn('TicketDescription'))
				->from($db->qn('#__rsevents_tickets'))
				->where($db->qn('IdEvent').' = '.(int) $event->IdEvent);
			
			$db->setQuery($query);
			$tickets = $db->loadObjectList();
			
			if (!empty($tickets)) {
				foreach ($tickets as $ticket) {
					$query->clear()
						->insert($db->qn('#__rseventspro_tickets'))
						->set($db->qn('name').' = '.$db->q($ticket->TicketName))
						->set($db->qn('price').' = '.$db->q($ticket->TicketPrice))
						->set($db->qn('seats').' = '.$db->q($ticket->TicketMaxAudience))
						->set($db->qn('user_seats').' = '.$db->q($ticket->TicketsPerUser))
						->set($db->qn('description').' = '.$db->q($ticket->TicketDescription))
						->set($db->qn('ide').' = '.$db->q($idevent));
					
					$db->setQuery($query);
					$db->execute();
					$tid = $db->insertid();
					$ticket_relation[$ticket->IdTicket] = $tid;
				}
			}
			
			$query->clear()
				->select($db->qn('CuponName'))->select($db->qn('CuponCode'))->select($db->qn('CuponType'))->select($db->qn('CuponValue'))->select($db->qn('CouponStart'))
				->select($db->qn('CouponEnd'))->select($db->qn('CouponLimit'))->select($db->qn('CouponAction'))
				->from($db->qn('#__rsevents_cupons'))
				->where($db->qn('IdEvent').' = '.(int) $event->IdEvent);
			
			$db->setQuery($query);
			$coupons = $db->loadObjectList();
			
			if (!empty($coupons)) {
				$query->clear()
					->update($db->qn('#__rseventspro_events'))
					->set($db->qn('discounts').' = 1')
					->where($db->qn('id').' = '.(int) $idevent);
				
				$db->setQuery($query);
				$db->execute();
				
				foreach ($coupons as $coupon) {
					if (empty($coupon->CouponStart)) {
						$cstart = $db->getNullDate();
					} else {
						$cstart = $coupon->CouponStart + $this->_tz;
						$cstart = JFactory::getDate($cstart)->toSql();
					}
					
					if (empty($coupon->CouponEnd)) {
						$cend = $db->getNullDate();
					} else {
						$cend = $coupon->CouponEnd + $this->_tz;
						$cend = JFactory::getDate($cend)->toSql();
					}
					
					$query->clear()
						->insert($db->qn('#__rseventspro_coupons'))
						->set($db->qn('name').' = '.$db->q($coupon->CuponName))
						->set($db->qn('from').' = '.$db->q($cstart))
						->set($db->qn('to').' = '.$db->q($cend))
						->set($db->qn('usage').' = '.$db->q($coupon->CouponLimit))
						->set($db->qn('discount').' = '.$db->q($coupon->CuponValue))
						->set($db->qn('type').' = '.$db->q($coupon->CuponType))
						->set($db->qn('action').' = '.$db->q($coupon->CouponAction))
						->set($db->qn('ide').' = '.$idevent);
					
					$db->setQuery($query);
					$db->execute();
					$idc = $db->insertid();
					
					if (!empty($coupon->CuponCode)) {
						$codes = explode("\n",$coupon->CuponCode);
						if(!empty($codes)) {
							foreach ($codes as $code) {				
								$code = trim($code);
								$query->clear()
									->insert($db->qn('#__rseventspro_coupon_codes'))
									->set($db->qn('code').' = '.$db->q($code))
									->set($db->qn('idc').' = '.(int) $idc)
									->set($db->qn('used').' = 0');
								
								
								$db->setQuery($query);
								$db->execute();
							}
						}
					}
				}
			}
			
			// Get subscribers
			$query->clear()
				->select($db->qn('IdSubscription'))->select($db->qn('IdUser'))->select($db->qn('LastName'))->select($db->qn('FirstName'))
				->select($db->qn('Email'))->select($db->qn('SubscriptionState'))->select($db->qn('SubscriptionDate'))->select($db->qn('UserIp'))
				->select($db->qn('Discount'))->select($db->qn('early_fee'))->select($db->qn('late_fee'))->select($db->qn('tax'))
				->from($db->qn('#__rsevents_subscriptions'))
				->where($db->qn('IdEvent').' = '.(int) $event->IdEvent);
			
			$db->setQuery($query);
			$subscriptions = $db->loadObjectList();
			
			if (!empty($subscriptions)) {
				foreach ($subscriptions as $subscription) {
					if ($subscription->SubscriptionState == 1)
						$state = 1;
					else if ($subscription->SubscriptionState == -1)
						$state = 2;
					else 
						$state = 0;
					
					$sdate = $subscription->SubscriptionDate + $this->_tz;
					
					$query->clear()
						->insert($db->qn('#__rseventspro_users'))
						->set($db->qn('ide').' = '.(int) $idevent)
						->set($db->qn('idu').' = '.(int) $subscription->IdUser)
						->set($db->qn('name').' = '.$db->q($subscription->LastName.' '.$subscription->FirstName))
						->set($db->qn('email').' = '.$db->q($subscription->Email))
						->set($db->qn('date').' = '.$db->q(JFactory::getDate($sdate)->toSql()))
						->set($db->qn('state').' = '.$db->q($state))
						->set($db->qn('ip').' = '.$db->q($subscription->UserIp))
						->set($db->qn('discount').' = '.$db->q($subscription->Discount))
						->set($db->qn('early_fee').' = '.$db->q($subscription->early_fee))
						->set($db->qn('late_fee').' = '.$db->q($subscription->late_fee))
						->set($db->qn('tax').' = '.$db->q($subscription->tax));
					
					$db->setQuery($query);
					$db->execute();
					$sid = $db->insertid();
					
					$query->clear()
						->select($db->qn('IdTicket'))->select($db->qn('TicketsSubscribed'))
						->from($db->qn('#__rsevents_subscription_tickets'))
						->where($db->qn('IdSubscription').' = '.(int) $subscription->IdSubscription);
					
					$db->setQuery($query);
					$tickets_purchased = $db->loadObjectList();
					
					if (!empty($tickets_purchased)) {
						foreach ($tickets_purchased as $ticketp) {
							$query->clear()
								->insert($db->qn('#__rseventspro_user_tickets'))
								->set($db->qn('ids').' = '.(int) $sid)
								->set($db->qn('idt').' = '.(int) $ticket_relation[$ticketp->IdTicket])
								->set($db->qn('quantity').' = '.(int) $ticketp->TicketsSubscribed);
							
							$db->setQuery($query);
							$db->execute();
						}
					}
				}
			}
		}
		
		// Get RSEvents! files
		$query->clear()
			->select($db->qn('FileName'))->select($db->qn('FileLocation'))->select($db->qn('FilePermission'))
			->from($db->qn('#__rsevents_files'))
			->where($db->qn('IdEvent').' = '.(int) $event->IdEvent);
		
		$db->setQuery($query);
		$files = $db->loadObjectList();
		
		$fpath	= JPATH_SITE.'/components/com_rsevents/assets/files/';
		$rspath	= JPATH_SITE.'/components/com_rseventspro/assets/images/files/';
		
		if (!empty($files)) {
			foreach ($files as $file) {
				if (JFile::exists($fpath.$file->FileLocation))
				{
					$fil	= $file->FileLocation;
					$fil	= JFile::makeSafe($fil);
					$ext	= JFile::getExt($fil);
					$filename = JFile::getName(JFile::stripExt($fil));
					
					while(JFile::exists($rspath.$filename.'.'.$ext))
						$filename .= rand(1,999);
					
					if (JFile::copy($fpath.$file->FileLocation, $rspath.$filename.'.'.$ext)) {
						$query->clear()
							->insert($db->qn('#__rseventspro_files'))
							->set($db->qn('name').' = '.$db->q($file->FileName))
							->set($db->qn('permissions').' = '.$db->q($file->FilePermission))
							->set($db->qn('location').' = '.$db->q($filename.'.'.$ext))
							->set($db->qn('ide').' = '.(int) $idevent);
					
						$db->setQuery($query);
						$db->execute();
					}
				}
			}
		}
		
		// Get the RSEvents! icon
		if (!empty($event->EventIcon)) {
			$path = JPATH_SITE.'/components/com_rseventspro/assets/images/events/';
			$ipath = JPATH_SITE.'/components/com_rsevents/assets/images/thumbs/';
			$icon = str_replace('.'.JFile::getExt($event->EventIcon),'_'.$size.'.'.JFile::getExt($event->EventIcon),$event->EventIcon);
			
			if (file_exists($ipath.$icon)) {
				$ext = JFile::getExt($icon);
				if (in_array(strtolower($ext),array('jpg','png','jpeg'))) {
					$file = JFile::makeSafe($icon);
					$filename = JFile::getName(JFile::stripExt($file));
					
					while(JFile::exists($path.$filename.'.'.$ext))
						$filename .= rand(1,999);
					
					if (JFile::copy($ipath.$icon,$path.$filename.'.'.$ext)) {
						$query->clear()
							->update($db->qn('#__rseventspro_events'))
							->set($db->qn('icon').' = '.$db->q($filename.'.'.$ext))
							->set($db->qn('properties').' = '.$db->q(''))
							->where($db->qn('id').' = '.(int) $idevent);
						
						$db->setQuery($query);
						$db->execute();
					}
				}
			}
		}
		
		return $idevent;
	}
	
	public function jevents() {
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		
		// Get categories
		// Get all categories used in events
		$query->clear()
			->select($db->qn('catid'))
			->from($db->qn('#__jevents_vevent'));
		
		$db->setQuery($query);
		$jevcategories = $db->loadColumn();			
		
		$condition = false;
		if (!empty($jevcategories)) {
			JArrayHelper::toInteger($jevcategories);
			$jevcategories = array_unique($jevcategories);
			$condition = true;
			$condition = " AND id IN (".implode(',',$jevcategories).") ";
		}
		
		$query->clear()
			->select($db->qn('id'))->select($db->qn('title'))->select($db->qn('description'))
			->select($db->qn('published'))
			->from($db->qn('#__categories'))
			->where($db->qn('extension').' = '.$db->q('com_jevents'));
			
		if ($condition)
			$query->where($db->qn('id').' IN ('.implode(',',$jevcategories).')');
		
		$db->setQuery($query);
		$jecategories = $db->loadObjectList();
		
		if (empty($jecategories)) {
			$this->setError(JText::_('COM_RSEVENTSPRO_IMPORT_NO_DATA'));
			return false;
		}
		
		foreach ($jecategories as $i => $jecategory) {
			$registry = new JRegistry;
			$registry->loadString($jecategory->params);
			$jecategories[$i]->color = $registry->get('catcolour','');
		}
		
		// Add the default JEvents location
		$query->clear()
			->insert($db->qn('#__rseventspro_locations'))
			->set($db->qn('name').' = '.$db->q('JEvents'))
			->set($db->qn('published').' = 1');
		
		$db->setQuery($query);
		$db->execute();
		$idlocation = $db->insertid();
		
		// Prepare events
		if (!empty($jecategories)) {
			$events = array();
			
			foreach ($jecategories as $category) {
				// Check if the category has events
				$query->clear()
					->select('COUNT(ev_id)')
					->from($db->qn('#__jevents_vevent'))
					->where($db->qn('catid').' = '.(int) $category->id);
				
				$db->setQuery($query);
				$count = $db->loadResult();
				
				if (!$count) continue;
				
				// Create the category
				$data = array();
				$data['published'] = $category->published;
				$data['title'] = $category->title;
				$data['description'] = $category->description;
				$data['parent_id'] = 1;
				$registry = new JRegistry;
				$registry->loadArray(array('color' => $category->color));
				$data['params'] = $registry->toString();
				
				$category_id = $this->_savecategory($data);
				
				// Get the event details
				$query->clear()
					->select($db->qn('ed.dtstart'))->select($db->qn('ed.dtend'))->select($db->qn('ed.description'))->select($db->qn('ed.summary'))
					->select($db->qn('ed.state'))->select($db->qn('e.created_by'))
					->from($db->qn('#__jevents_vevent','e'))
					->join('left', $db->qn('#__jevents_vevdetail','ed').' ON '.$db->qn('ed.evdet_id').' = '.$db->qn('e.ev_id'))
					->where($db->qn('e.catid').' = '.(int) $category->id);
				
				$db->setQuery($query);
				$jevents = $db->loadObjectList();
				
				// Add JEvents events to the $events container
				foreach ($jevents as $event) {
					$eventcontainer = new stdClass();
					$eventcontainer->name = $event->summary;
					$eventcontainer->description = $event->description;
					$eventcontainer->location = $idlocation;
					$eventcontainer->start = $event->dtstart;
					$eventcontainer->end = $event->dtend;
					$eventcontainer->published = $event->state;
					$eventcontainer->owner = $event->created_by;
					
					$events[$category_id][] = $eventcontainer;
				}
			}
		}
		
		$counter = 0;
		
		// Parse JEvents events
		if (!empty($events)) {
			foreach ($events as $category => $event) {
				foreach ($event as $jevent) {
					$start = $jevent->start + $this->_tz;
					$end = $jevent->end + $this->_tz;
					
					$data = array();
					$data['name'] = $jevent->name;
					$data['description'] = $jevent->description;
					$data['location'] = $jevent->location;
					$data['start'] = JFactory::getDate($start)->toSql();
					$data['end'] = JFactory::getDate($end)->toSql();
					$data['published'] = $jevent->published;
					$data['owner'] = $jevent->owner;
					$data['completed'] = 1;
					
					if ($idevent = $this->_saveevent($data)) {
						$query->clear()
							->insert($db->qn('#__rseventspro_taxonomy'))
							->set($db->qn('type').' = '.$db->q('category'))
							->set($db->qn('id').' = '.(int) $category)
							->set($db->qn('ide').' = '.(int) $idevent);
						
						$db->setQuery($query);
						$db->execute();
						
						$counter++;
					}
				}
			}
		}
		
		if ($counter) {
			return $counter;
		}
		
		$this->setError(JText::_('COM_RSEVENTSPRO_IMPORT_NO_DATA'));
		return false;
	}
	
	public function jcalpro() {
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		
		// Get categories
		$query->clear()
			->select($db->qn('cat_id'))->select($db->qn('cat_name'))->select($db->qn('description'))
			->select($db->qn('color'))->select($db->qn('published'))
			->from($db->qn('#__jcalpro2_categories'));
		
		$db->setQuery($query);
		$categories = $db->loadObjectList();
		
		if (empty($categories)) {
			$this->setError(JText::_('COM_RSEVENTSPRO_IMPORT_NO_DATA'));
			return false;
		}
		
		// Add the default JCalPro location
		$query->clear()
			->insert($db->qn('#__rseventspro_locations'))
			->set($db->qn('name').' = '.$db->q('JCalPro'))
			->set($db->qn('published').' = 1');
		
		$db->setQuery($query);
		$db->execute();
		$idlocation = $db->insertid();
		
		$events = array();
		
		// Prepare events
		if (!empty($categories)) {
			foreach ($categories as $category) {
				// Check if the category has events
				$query->clear()
					->select('COUNT(extid)')
					->from($db->qn('#__jcalpro2_events'))
					->where($db->qn('cat').' = '.(int) $category->cat_id);
					
				$db->setQuery($query);
				$count = $db->loadResult();
				
				if (!$count) continue;
				
				// Create the category
				$data = array();
				$data['published'] = $category->published;
				$data['title'] = $category->cat_name;
				$data['description'] = $category->description;
				$data['parent_id'] = 1;
				$registry = new JRegistry;
				$registry->loadArray(array('color' => $category->color));
				$data['params'] = $registry->toString();
				
				$category_id = $this->_savecategory($data);
				
				// Get the event details
				$query->clear()
					->select($db->qn('owner_id'))->select($db->qn('title'))->select($db->qn('description'))->select($db->qn('start_date'))
					->select($db->qn('end_date'))->select($db->qn('published'))
					->from($db->qn('#__jcalpro2_events'))
					->where($db->qn('cat').' = '.(int) $category->cat_id);
				
				$db->setQuery($query);
				$jcevents = $db->loadObjectList();
				
				// Add JCalPro events to the $events container
				foreach ($jcevents as $event) {
					$eventcontainer = new stdClass();
					$eventcontainer->name = $event->title;
					$eventcontainer->description = $event->description;
					$eventcontainer->location = $idlocation;
					$eventcontainer->start = $event->start_date;
					$eventcontainer->end = $event->end_date;
					$eventcontainer->published = $event->published;
					$eventcontainer->owner = $event->owner_id;
					
					$events[$category_id][] = $eventcontainer;
				}
			}
		}
		
		$counter = 0;
		
		// Parse JCalPro events
		if (!empty($events)) {
			foreach ($events as $category => $event) {
				foreach ($event as $theevent) {
					$data = array();
					$data['name'] = $theevent->name;
					$data['description'] = $theevent->description;
					$data['location'] = $theevent->location;
					$data['start'] = $theevent->start;
					$data['end'] = $theevent->end;
					$data['published'] = $theevent->published;
					$data['owner'] = $theevent->owner;
					$data['completed'] = 1;
					
					if ($idevent = $this->_saveevent($data)) {
						$query->clear()
							->insert($db->qn('#__rseventspro_taxonomy'))
							->set($db->qn('type').' = '.$db->q('category'))
							->set($db->qn('id').' = '.(int) $category)
							->set($db->qn('ide').' = '.(int) $idevent);
						
						$db->setQuery($query);
						$db->execute();
						
						$counter++;
					}
				}
			}
		}
		
		if ($counter)
			return $counter;
		
		$this->setError(JText::_('COM_RSEVENTSPRO_IMPORT_NO_DATA'));
		return false;
	}
	
	public function eventlistbeta() {
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		
		// Get events
		$query->clear()
			->select($db->qn('id'))->select($db->qn('locid'))->select($db->qn('dates'))
			->select($db->qn('enddates'))->select($db->qn('times'))->select($db->qn('endtimes'))->select($db->qn('title'))
			->select($db->qn('created_by'))->select($db->qn('datdescription'))->select($db->qn('published'))
			->from($db->qn('#__eventlist_events'));
		
		
		$db->setQuery($query);
		$eevents = $db->loadObjectList();
		
		if (empty($eevents)) {
			$this->setError(JText::_('COM_RSEVENTSPRO_IMPORT_NO_DATA'));
			return false;
		}
		
		// Create a default location for events that don't have a location
		$query->clear()
			->select('COUNT(id)')
			->from('#__eventlist_events')
			->where('locid'.' = 0');
		
		$db->setQuery($query);
		if ($db->loadResult()) {
			$query->clear()
				->insert($db->qn('#__rseventspro_locations'))
				->set($db->qn('name').' = '.$db->q('Eventlist'))
				->set($db->qn('published').' = '.$db->q('1'));
			
			$db->setQuery($query);
			$db->execute();
			$default_location = $db->insertid();
		}
		
		$thelocations = array();
		$query->clear()
			->select($db->qn('locid'))
			->from($db->qn('#__eventlist_events'));
		
		$db->setQuery($query);
		$locations = $db->loadColumn();
		
		if (!empty($locations)) {
			JArrayHelper::toInteger($locations);
			$locations = array_unique($locations);
			
			foreach ($locations as $location) {
				$query->clear()
					->select($db->qn('venue'))->select($db->qn('url'))->select($db->qn('street'))->select($db->qn('plz'))
					->select($db->qn('city'))->select($db->qn('state'))->select($db->qn('country'))->select($db->qn('locdescription'))
					->select($db->qn('published'))
					->from($db->qn('#__eventlist_venues'))
					->where($db->qn('id').' = '.(int) $location);
				
				$db->setQuery($query);
				$elocation = $db->loadObject();
				
				$address = $elocation->street;
				if (!empty($elocation->plz)) $address .= ' , '.$elocation->plz;
				if (!empty($elocation->city)) $address .= ' , '.$elocation->city;
				if (!empty($elocation->state)) $address .= ' , '.$elocation->state;
				if (!empty($elocation->country)) $address .= ' , '.$elocation->country;
				
				$data = array();
				$data['name'] = $elocation->venue;
				$data['url'] = $elocation->url;
				$data['address'] = $address;
				$data['description'] = $elocation->locdescription;
				$data['published'] = $elocation->published;
				
				$newlocation = $this->_savelocation($data);
				$thelocations[$location] = $newlocation;
			}
		}
		
		$thecategories = array();
		$query->clear()
			->select($db->qn('catid'))
			->from($db->qn('#__eventlist_cats_event_relations'));
			
		$db->setQuery($query);
		$categories = $db->loadColumn();
		
		if (!empty($categories)) {
			JArrayHelper::toInteger($categories);
			$categories = array_unique($categories);
			
			foreach ($categories as $category) {
				$query->clear()
					->select($db->qn('catname'))->select($db->qn('catdescription'))->select($db->qn('published'))->select($db->qn('color'))
					->from($db->qn('#__eventlist_categories'))
					->where($db->qn('id').' = '.(int) $category);
				
				$db->setQuery($query);
				$evcategory = $db->loadObject();
				
				$data = array();
				$data['published'] = $evcategory->published;
				$data['title'] = $evcategory->catname;
				$data['description'] = $evcategory->catdescription;
				$data['parent_id'] = 1;
				$registry = new JRegistry;
				$registry->loadArray(array('color' => $evcategory->color));
				$data['params'] = $registry->toString();
				
				$newcategory = $this->_savecategory($data);
				$thecategories[$category] = $newcategory;
			}
		}
		
		$container = array();
		
		if (!empty($eevents)) {
			foreach ($eevents as $event) {				
				$array = array();
				$query->clear()
					->select($db->qn('catid'))
					->from($db->qn('#__eventlist_cats_event_relations'))
					->where($db->qn('itemid').' = '.(int) $event->id);
				
				$db->setQuery($query);
				$ecategories = $db->loadColumn();
				JArrayHelper::toInteger($ecategories);
				
				if (!empty($ecategories))
					foreach ($ecategories as $cat)
						$array[] = $thecategories[$cat];
				
				
				$eventcontainer = new stdClass();
				$eventcontainer->name = $event->title;
				$eventcontainer->description = $event->datdescription;
				$eventcontainer->location = $event->locid == 0 ? $default_location : $thelocations[$event->locid];
				$eventcontainer->oldcategories = $array;
				$eventcontainer->start = $event->dates.' '.(!is_null($event->times) ? $event->times : '00:00:00');
				$eventcontainer->end = $event->enddates.' '.(!is_null($event->endtimes) ? $event->endtimes : '00:00:00') ;
				$eventcontainer->published = $event->published;
				$eventcontainer->owner = $event->created_by;
				
				$container[] = $eventcontainer;
			}
		}
		
		$counter = 0;
		
		// Parse Eventlist events
		if (!empty($container)) {
			foreach ($container as $event) {
				$data = array();
				$data['name'] = $event->name;
				$data['description'] = $event->description;
				$data['location'] = $event->location;
				$data['start'] = $event->start;
				$data['end'] = $event->end;
				$data['published'] = $event->published;
				$data['owner'] = $event->owner;
				$data['completed'] = 1;
				
				if ($idevent = $this->_saveevent($data)) {
					$categories = $event->oldcategories;
					if (!empty($categories)) {
						foreach ($categories as $category) {
							$query->clear()
							->insert($db->qn('#__rseventspro_taxonomy'))
							->set($db->qn('type').' = '.$db->q('category'))
							->set($db->qn('id').' = '.(int) $category)
							->set($db->qn('ide').' = '.(int) $idevent);
						
							$db->setQuery($query);
							$db->execute();
						}
					}
					
					$counter++;
				}
			}
		}
		
		if ($counter)
			return $counter;
		
		$this->setError(JText::_('COM_RSEVENTSPRO_IMPORT_NO_DATA'));
		return false;
	}
	
	public function eventlist() {
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		
		// Get events
		$query->clear()
			->select($db->qn('id'))->select($db->qn('locid'))->select($db->qn('catsid'))->select($db->qn('dates'))
			->select($db->qn('enddates'))->select($db->qn('times'))->select($db->qn('endtimes'))->select($db->qn('title'))
			->select($db->qn('created_by'))->select($db->qn('datdescription'))->select($db->qn('published'))
			->from($db->qn('#__eventlist_events'));
		
		$db->setQuery($query);
		$eevents = $db->loadObjectList();
		
		if (empty($eevents)) {
			$this->setError(JText::_('COM_RSEVENTSPRO_IMPORT_NO_DATA'));
			return false;
		}
		
		// Create a default location for events that don't have a location
		$query->clear()
			->select('COUNT(id)')
			->from('#__eventlist_events')
			->where('locid'.' = 0');
		
		$db->setQuery($query);
		if ($db->loadResult()) {
			$query->clear()
				->insert($db->qn('#__rseventspro_locations'))
				->set($db->qn('name').' = '.$db->q('Eventlist'))
				->set($db->qn('published').' = '.$db->q('1'));
			
			$db->setQuery($query);
			$db->execute();
			$default_location = $db->insertid();
		}
		
		$thelocations = array();
		$query->clear()
			->select($db->qn('locid'))
			->from($db->qn('#__eventlist_events'));
			
		$db->setQuery($query);
		$locations = $db->loadColumn();
		
		if (!empty($locations))
			foreach ($locations as $location) {
				$query->clear()
					->select($db->qn('venue'))->select($db->qn('url'))->select($db->qn('street'))->select($db->qn('plz'))
					->select($db->qn('city'))->select($db->qn('state'))->select($db->qn('country'))->select($db->qn('locdescription'))
					->select($db->qn('published'))
					->from($db->qn('#__eventlist_venues'))
					->where($db->qn('id').' = '.(int) $location);
				
				$db->setQuery($query);
				$elocation = $db->loadObject();
				
				$address = $elocation->street;
				if (!empty($elocation->plz)) $address .= ' , '.$elocation->plz;
				if (!empty($elocation->city)) $address .= ' , '.$elocation->city;
				if (!empty($elocation->state)) $address .= ' , '.$elocation->state;
				if (!empty($elocation->country)) $address .= ' , '.$elocation->country;
				
				$data = array();
				$data['name'] = $elocation->venue;
				$data['url'] = $elocation->url;
				$data['address'] = $address;
				$data['description'] = $elocation->locdescription;
				$data['published'] = $elocation->published;
				
				$newlocation = $this->_savelocation($data);
				$thelocations[$location] = $newlocation;
			}
		
		$thecategories = array();
		$query->clear()
			->select($db->qn('catsid'))
			->from($db->qn('#__eventlist_events'));
		
		$db->setQuery($query);
		$categories = $db->loadColumn();
		
		if (!empty($categories)) {
			JArrayHelper::toInteger($categories);
			$categories = array_unique($categories);
			
			foreach ($categories as $category) {
				$query->clear()
					->select($db->qn('catname'))->select($db->qn('catdescription'))->select($db->qn('published'))
					->from($db->qn('#__eventlist_categories'))
					->where($db->qn('id').' = '.(int) $category);
				
				$db->setQuery($query);
				$evcategory = $db->loadObject();
				
				$data = array();
				$data['published'] = $evcategory->published;
				$data['title'] = $evcategory->catname;
				$data['description'] = $evcategory->catdescription;
				$data['parent_id'] = 1;
				
				$newcategory = $this->_savecategory($data);
				$thecategories[$category] = $newcategory;
			}
		}
		
		$container = array();
		
		if (!empty($eevents)) {
			foreach ($eevents as $event) {
				$eventcontainer = new stdClass();
				$eventcontainer->name = $event->title;
				$eventcontainer->description = $event->datdescription;
				$eventcontainer->location = $event->locid == 0 ? $default_location : $thelocations[$event->locid];
				$eventcontainer->start = $event->dates.' '.(!is_null($event->times) ? $event->times : '00:00:00');
				$eventcontainer->end = $event->enddates.' '.(!is_null($event->endtimes) ? $event->endtimes : '00:00:00') ;
				$eventcontainer->published = $event->published;
				$eventcontainer->owner = $event->created_by;
				
				$container[$event->catsid][] = $eventcontainer;
			}
		}
		
		$counter = 0;
		
		if (!empty($container)) {
			foreach ($container as $category => $event) {
				foreach ($event as $theevent) {
					$data = array();
					$data['name'] = $theevent->name;
					$data['description'] = $theevent->description;
					$data['location'] = $theevent->location;
					$data['start'] = $theevent->start;
					$data['end'] = $theevent->end;
					$data['published'] = $theevent->published;
					$data['owner'] = $theevent->owner;
					$data['completed'] = 1;
					
					if ($idevent = $this->_saveevent($data)) {
						$query->clear()
							->insert($db->qn('#__rseventspro_taxonomy'))
							->set($db->qn('type').' = '.$db->q('category'))
							->set($db->qn('id').' = '.(int) $thecategories[$category])
							->set($db->qn('ide').' = '.(int) $idevent);
						
						$db->setQuery($query);
						$db->execute();
						
						$counter++;
					}
				}
			}
		}
		
		if ($counter)
			return $counter;
		
		$this->setError(JText::_('COM_RSEVENTSPRO_IMPORT_NO_DATA'));
		return false;
	}
	
	public function ohanah() {
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
	
		$counter = 0;
		
		// Get events
		$query->clear()
			->select($db->qn('ohanah_event_id'))->select($db->qn('ohanah_category_id'))->select($db->qn('title'))->select($db->qn('description'))
			->select($db->qn('date'))->select($db->qn('start_time'))->select($db->qn('end_date'))->select($db->qn('end_time'))->select($db->qn('created_by'))
			->select($db->qn('enabled'))->select($db->qn('ohanah_venue_id'))->select($db->qn('end_time_enabled'))
			->from($db->qn('#__ohanah_events'));
		
		$db->setQuery($query);
		$events = $db->loadObjectList();
		
		if (!empty($events)) {
			$query->clear()
				->select($db->qn('ohanah_venue_id'))->select($db->qn('title'))->select($db->qn('description'))
				->select($db->qn('address'))->select($db->qn('latitude'))->select($db->qn('longitude'))
				->select($db->qn('enabled'))
				->from($db->qn('#__ohanah_venues'));
			
			$db->setQuery($query);
			$locations = $db->loadObjectList();
			
			$query->clear()
				->select($db->qn('ohanah_category_id'))->select($db->qn('title'))
				->select($db->qn('enabled'))->select($db->qn('description'))
				->from($db->qn('#__ohanah_categories'));
			
			$db->setQuery($query);
			$categories = $db->loadObjectList();
			
			$thelocations = array();
			if (!empty($locations)) {
				foreach ($locations as $location) {
					$query->clear()
						->select('COUNT(ohanah_event_id)')
						->from($db->qn('#__ohanah_events'))
						->where($db->qn('ohanah_venue_id').' = '.(int) $location->ohanah_venue_id);
					
					$db->setQuery($query);
					if ($db->loadResult()) {
						// IMPORT locations
						$coodinates = !empty($location->latitude) && !empty($location->longitude) ? $location->latitude.','.$location->longitude : '';
						
						$data = array();
						$data['name'] = $location->title;
						$data['address'] = $location->address;
						$data['description'] = $location->description;
						$data['published'] = $location->enabled;
						$data['coordinates'] = $coordinates;
						
						$newlocation = $this->_savelocation($data);
						$thelocations[$location->ohanah_venue_id] = $newlocation;
					}
				}
			}
			
			$thecategories = array();
			if (!empty($categories)) {
				foreach ($categories as $category) {
					$query->clear()
						->select('COUNT(ohanah_event_id)')
						->from($db->qn('#__ohanah_events'))
						->where($db->qn('ohanah_category_id').' = '.(int) $category->ohanah_category_id);
					
					$db->setQuery($query);
					if ($db->loadResult()) {
						// IMPORT categories
						$data = array();
						$data['published'] = $category->enabled;
						$data['title'] = $category->title;
						$data['description'] = $category->description;
						$data['parent_id'] = 1;
						
						$newcategory = $this->_savecategory($data);
						$thecategories[$category->ohanah_category_id] = $newcategory;
					}
				}
			}
			
			if (!empty($events)) {
				foreach ($events as $event) {
					// IMPORT events
					$startDate	= $event->date.' '.$event->start_time;
					$endDate	= $event->end_date.' '.$event->end_time;					
					$start		= JFactory::getDate($startDate, rseventsproHelper::getTimezone());
					$end		= JFactory::getDate($endDate, rseventsproHelper::getTimezone());
					
					$data = array();
					$data['name'] = $event->title;
					$data['description'] = $event->description;
					$data['location'] = $thelocations[$event->ohanah_venue_id];
					$data['start'] = $start->format('Y-m-d H:i:s');
					$data['end'] = $end->format('Y-m-d H:i:s');
					$data['published'] = $event->enabled;
					$data['owner'] = $event->created_by;
					$data['completed'] = 1;
					
					if ($idevent = $this->_saveevent($data)) {
						$query->clear()
							->insert($db->qn('#__rseventspro_taxonomy'))
							->set($db->qn('type').' = '.$db->q('category'))
							->set($db->qn('id').' = '.(int) $thecategories[$event->ohanah_category_id])
							->set($db->qn('ide').' = '.(int) $idevent);
					
						$db->setQuery($query);
						$db->execute();
						
						$counter++;
					}
				}
			}
		}
		
		if ($counter)
			return $counter;
		
		$this->setError(JText::_('COM_RSEVENTSPRO_IMPORT_NO_DATA'));
		return false;
	}
	
	public function csv() {
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		
		jimport('joomla.filesystem.file');
		
		$config		= JFactory::getConfig();
		$format		= JFactory::getApplication()->input->getString('dateformat','Y-m-d');
		$file		= JFactory::getApplication()->input->files->get('events');
		$tmp		= $config->get('tmp_path');
		$uid		= JFactory::getUser()->get('id');
		$count		= 0;
		$nulldate	= $db->getNullDate();
		
		if (JFile::getExt($file['name']) != 'csv') {
			$this->setError(JText::_('COM_RSEVENTSPRO_INVALID_CSV_FILE'));
			return false;
		}
		
		if ($file['error'] == 0 && $file['size'] > 0) {
			$upload = JFile::upload($file['tmp_name'],$tmp.'/rseventspro.csv');
			
			if ($upload) {
				ini_set('auto_detect_line_endings', true);
				setlocale(LC_ALL, 'en_US.UTF-8');
				$csvfile = $tmp.'/rseventspro.csv';
				$content = array();
				
				if (($handle = fopen($csvfile, 'r')) !== FALSE)  {
					while (($data = fgetcsv($handle, 4096, ',')) !== FALSE) {
						if (count($data) == 1 && $data[0] == '') continue;
						if (count($data) == 9 || count($data) == 11 || count($data) == 12) {
							$content[] = $data;
						} else continue;
					}
					fclose($handle);
				}
				
				if (!empty($content)) {
					$eventCategoriesCache = array();
					$eventTagsCache = array();
					
					foreach ($content as $event) {
						$allday		= 0;
						$location	= JFactory::getApplication()->input->getInt('location',0);
						$category	= JFactory::getApplication()->input->getInt('category',0);
						$name		= !empty($event[0]) ? $event[0] : JText::_('COM_RSEVENTSPRO_NEW_EVENT');
						$start		= !empty($event[1]) ? $event[1] : JFactory::getDate()->format($format.' H:i:s');
						$end		= @$event[2];
						$description= !empty($event[3]) ? $event[3] : '';
						$url		= !empty($event[4]) ? $event[4] : '';
						$email		= !empty($event[5]) ? $event[5] : '';
						$phone		= !empty($event[6]) ? $event[6] : '';
						$lname		= !empty($event[7]) ? $event[7] : '';
						$laddress	= !empty($event[8]) ? $event[8] : '';
						$catname	= !empty($event[9]) ? $event[9] : 'Events';
						$catdesc	= !empty($event[10]) ? $event[10] : '';
						$tags		= !empty($event[11]) ? $event[11] : '';
						$start		= $this->transformDate($start,$format);
						$end		= $this->transformDate($end,$format);
						
						if ($location == 0 && !empty($lname)) {
							$data = array();
							$data['name'] = $lname;
							$data['address'] = $laddress;
							$data['published'] = 1;
							
							$query->clear()
								->select($db->qn('id'))
								->from($db->qn('#__rseventspro_locations'))
								->where($db->qn('name').' = '.$db->q($lname))
								->where($db->qn('address').' = '.$db->q($laddress));
							$db->setQuery($query);
							$location = (int) $db->loadResult();
							
							if (!$location)
								$location = $this->_savelocation($data);
						}
						
						// Categories
						$eventCategories = array();
						if ($category) {
							$eventCategories[] = $category;
						} else {
							$categories     = explode('|', $catname);
							$descriptions   = explode('|', $catdesc);

							foreach ($categories as $i => $category) {
								$data = array(
									'published'     => 1,
									'title'         => $category,
									'description'   => isset($descriptions[$i]) ? $descriptions[$i] : '',
									'parent_id'     => 1
								);

								// Cache the category results so we don't query the db often
								$hash = md5($data['title'].'|'.$data['description']);
								if (empty($eventCategoriesCache[$hash])) {
									// Do we have a category with the same details in the database?
									$query->clear()
										->select($db->qn('id'))
										->from($db->qn('#__categories'))
										->where($db->qn('title').' = '.$db->q($data['title']))
										->where($db->qn('description').' = '.$db->q($data['description']));
									$db->setQuery($query);
									if ($category = (int) $db->loadResult()) {
										// Add its ID to the list of cached categories
										$eventCategoriesCache[$hash] = $category;
									} else {
										// Add the new category
										$eventCategoriesCache[$hash] = $this->_savecategory($data);
									}
								}
								
								$eventCategories[] = $eventCategoriesCache[$hash];
							}
						}
						
						// Tags
						$eventTags	= array();
						if ($tags) {
							if ($tags = explode('|', $tags)) {
								foreach ($tags as $tag) {
									$tdata = array(
										'published' => 1,
										'name'     => $tag
									);
									
									// Cache the tag results so we don't query the db often
									$hash = md5($tdata['name']);
									if (empty($eventTagsCache[$hash])) {
										// Do we have a category with the same details in the database?
										$query->clear()
											->select($db->qn('id'))
											->from($db->qn('#__rseventspro_tags'))
											->where($db->qn('name').' = '.$db->q($tdata['name']));
										$db->setQuery($query);
										if ($tagid = (int) $db->loadResult()) {
											// Add its ID to the list of cached tags
											$eventTagsCache[$hash] = $tagid;
										} else {
											// Add the new tag
											$eventTagsCache[$hash] = $this->_savetag($tdata);
										}
									}
									
									$eventTags[] = $eventTagsCache[$hash];
								}
							}
						}
						
						$start = JFactory::getDate($start);
						$start->modify($this->_tz.' seconds');
						$start = $start->toSql();
						
						if (empty($end) || $end == $nulldate) {
							$end = $nulldate;
							$allday = 1;
						} else {
							$end = JFactory::getDate($end);
							$end->modify($this->_tz.' seconds');
							$end = $end->toSql();
						}
						
						if ($allday) {
							$start = explode(' ',$start);
							$start = $start[0].' 00:00:00';
						}
						
						// Save event
						$data = array();
						$data['from'] = 'import';
						$data['name'] = $name;
						$data['description'] = $description;
						$data['location'] = $location;
						$data['start'] = $start;
						$data['end'] = $end;
						$data['URL'] = $url;
						$data['email'] = $email;
						$data['phone'] = $phone;
						$data['published'] = 1;
						$data['owner'] = $uid;
						$data['completed'] = 1;
						$data['allday'] = $allday;
						
						if ($idevent = $this->_saveevent($data)) {
							foreach ($eventCategories as $eventCategory) {
								$query->clear()
									->insert($db->qn('#__rseventspro_taxonomy'))
									->set($db->qn('type').' = '.$db->q('category'))
									->set($db->qn('id').' = '.(int) $eventCategory)
									->set($db->qn('ide').' = '.(int) $idevent);

								$db->setQuery($query);
								$db->execute();
							}
							
							foreach ($eventTags as $eventTag) {
								$query->clear()
									->insert($db->qn('#__rseventspro_taxonomy'))
									->set($db->qn('type').' = '.$db->q('tag'))
									->set($db->qn('id').' = '.(int) $eventTag)
									->set($db->qn('ide').' = '.(int) $idevent);

								$db->setQuery($query);
								$db->execute();
							}
							
							$count++;
						}
					}
					
					if ($count)
						return $count;
				} else {
					$this->setError(JText::_('COM_RSEVENTSPRO_CHECK_IMPORTED_FILE'));
				}
				
				JFile::delete($tmp.'/rseventspro.csv');
			} else {
				$this->setError(JText::_('COM_RSEVENTSPRO_IMPORT_NO_DATA'));
			}
		} else {
			$this->setError(JText::_('COM_RSEVENTSPRO_INVALID_FILE'));
		}
		
		return false;
	}
	
	
	/**
	 *	Method to save category
	 *
	 *	@var $data
	 *
	 *	@return int
	 */
	protected function _savecategory($data) {
		$data['extension'] = 'com_rseventspro';
		$data['language'] = '*';
		
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true)->select($db->qn('id'))->from($db->qn('#__categories'));
		
		foreach ($data as $key => $value) {
			$query->where($db->qn($key).' = '.$db->q($value));
		}
		
		$db->setQuery($query);
		if ($catID = (int) $db->loadResult()) {
			return $catID;
		} else {
			$table = JTable::getInstance('Category', 'rseventsproTable');
			$table->setLocation($data['parent_id'], 'last-child');
			$table->save($data);
			$table->rebuildPath($table->id);
			$table->rebuild($table->id, $table->lft, $table->level, $table->path);
			
			return $table->id;
		}
	}
	
	/**
	 *	Method to save location
	 *
	 *	@var $object
	 *
	 *	@return boolean
	 */
	protected function _savelocation($data) {
		$table = JTable::getInstance('Location', 'rseventsproTable');
		$table->save($data);
		return $table->id;
	}
	
	/**
	 *	Method to save tag
	 *
	 *	@var $object
	 *
	 *	@return boolean
	 */
	protected function _savetag($data) {
		$table = JTable::getInstance('Tag', 'rseventsproTable');
		$table->save($data);
		return $table->id;
	}
	
	/**
	 *	Method to save event
	 *
	 *	@var $object
	 *
	 *	@return boolean
	 */
	protected function _saveevent($data) {
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);
		$model	= JModelLegacy::getInstance('Event','rseventsproModel',  array('ignore_request' => true));
		
		if ($model->save($data)) {
			$eid			= $model->getState('event.id');
			$defaultOptions = rseventsproHelper::getDefaultOptions();
			
			$query->clear()
				->update($db->qn('#__rseventspro_events'))
				->set($db->qn('options').' = '.$db->q($defaultOptions))
				->where($db->qn('id').' = '.(int) $eid);
			$db->setQuery($query);
			$db->execute();
			
			return $eid;
		}
		
		return false;
	}
	
	/**
	 *	Method to transform a date to the standard MySql date format
	 *
	 *	@return string
	 */
	protected function transformDate($date, $format) {
		$nulldate = JFactory::getDbo()->getNullDate();
		if (empty($date) || $date == $nulldate || $date == '00/00/0000 00:00:00' || $date == '00.00.0000 00:00:00' || $date == '00 00 0000 00:00:00') {
			return $nulldate;
		}
		
		if ($format == 'Y-m-d') {
			return $date;
		} elseif ($format == 'Y/m/d') {
			return str_replace('/','-',$date);
		} elseif ($format == 'Y.m.d') {
			return str_replace('.','-',$date);
		} elseif ($format == 'Y m d') {
			$date = explode(' ',$date);
			$date = $date[0].'-'.$date[1].'-'.$date[2].' '.$date[3];
			return $date;
		} elseif ($format == 'd-m-Y') {
			$regex = '#(\d{1,2})\-(\d{1,2})\-(\d{1,4})\s(.*)#s';
			if (preg_match($regex,$date,$match)) {
				if (!empty($match)) {
					return $match[3].'-'.$match[2].'-'.$match[1].' '.$match[4];
				}
			}
		} elseif ($format == 'd/m/Y') {
			$regex = '#(\d{1,2})\/(\d{1,2})\/(\d{1,4})\s(.*)#s';
			if (preg_match($regex,$date,$match)) {
				if (!empty($match)) {
					return $match[3].'-'.$match[2].'-'.$match[1].' '.$match[4];
				}
			}
		} elseif ($format == 'd.m.Y') {
			$regex = '#(\d{1,2})\.(\d{1,2})\.(\d{1,4})\s(.*)#s';
			if (preg_match($regex,$date,$match)) {
				if (!empty($match)) {
					return $match[3].'-'.$match[2].'-'.$match[1].' '.$match[4];
				}
			}
		} elseif ($format == 'd m Y') {
			$regex = '#(\d{1,2})\s(\d{1,2})\s(\d{1,4})\s(.*)#s';
			if (preg_match($regex,$date,$match)) {
				if (!empty($match)) {
					return $match[3].'-'.$match[2].'-'.$match[1].' '.$match[4];
				}
			}
		} elseif ($format == 'm-d-Y') {
			$regex = '#(\d{1,2})\-(\d{1,2})\-(\d{1,4})\s(.*)#s';
			if (preg_match($regex,$date,$match)) {
				if (!empty($match)) {
					return $match[3].'-'.$match[1].'-'.$match[2].' '.$match[4];
				}
			}
		} elseif ($format == 'm/d/Y') {
			$regex = '#(\d{1,2})\/(\d{1,2})\/(\d{1,4})\s(.*)#s';
			if (preg_match($regex,$date,$match)) {
				if (!empty($match)) {
					return $match[3].'-'.$match[1].'-'.$match[2].' '.$match[4];
				}
			}
		} elseif ($format == 'm.d.Y') {
			$regex = '#(\d{1,2})\.(\d{1,2})\.(\d{1,4})\s(.*)#s';
			if (preg_match($regex,$date,$match)) {
				if (!empty($match)) {
					return $match[3].'-'.$match[1].'-'.$match[2].' '.$match[4];
				}
			}
		} elseif ($format == 'm d Y') {
			$regex = '#(\d{1,2})\s(\d{1,2})\s(\d{1,4})\s(.*)#s';
			if (preg_match($regex,$date,$match)) {
				if (!empty($match)) {
					return $match[3].'-'.$match[1].'-'.$match[2].' '.$match[4];
				}
			}
		}
		
		return $date;
	}
}