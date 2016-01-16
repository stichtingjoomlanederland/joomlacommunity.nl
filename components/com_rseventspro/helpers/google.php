<?php
/**
* @version 1.0.0
* @package RSEvents!Pro 1.0.0
* @copyright (C) 2011 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' );

class RSEPROGoogleCalendar
{
	/*
	*	Google username
	*/
	protected $_username;
	
	/*
	*	Google password
	*/
	protected $_password;
	
	/*
	*	Errors
	*/
	protected $_errors = array();
	
	/*
	*	Constructor
	*/
	
	public function __construct($user, $pass) {
		$this->_username = $user;
		$this->_password = $pass;
	}
	
	/*
	*	Insert events in database
	*/
	
	public function parse() {
		$db		= JFactory::getDBO();
		$query	= $db->getQuery(true);
		$user	= JFactory::getUser();
		$events	= $this->getEvents();
		$jform	= JFactory::getApplication()->input->get('jform',array(),'array');
		$idcat	= isset($jform['google_category']) ? $jform['google_category'] : rseventsproHelper::getConfig('google_category','int');
		
		if (empty($idcat)) {
			$query->clear()
				->insert($db->qn('#__rseventspro_categories'))
				->set($db->qn('name').' = '.$db->q('Google Calendar'));
			
			$db->setQuery($query);
			$db->execute();
			$idcat = $db->insertid();
		}
		
		$i = 0;
		if (empty($events)) 
			return;
		
		foreach ($events as $event) {
			$idlocation = isset($jform['google_location']) ? $jform['google_location'] : rseventsproHelper::getConfig('google_location','int');
			
			//check if the current event was already added
			$query->clear()
				->select('COUNT(id)')
				->from($db->qn('#__rseventspro_sync'))
				->where($db->qn('id').' = '.$db->q($event->id))
				->where($db->qn('from').' = '.$db->q('gcalendar'));
				
			$db->setQuery($query);
			$indb = $db->loadResult();
			
			if(!empty($indb)) 
				continue;
			
			if (empty($idlocation)) {
				$location = !empty($event->location) ? $event->location : 'Google calendar locations';
				
				$query->clear()
					->insert($db->qn('#__rseventspro_locations'))
					->set($db->qn('name').' = '.$db->q($location))
					->set($db->qn('address').' = '.$db->q($location));
				
				$db->setQuery($query);
				$db->execute();
				$idlocation = $db->insertid();
			}
			
			$query->clear()
				->insert($db->qn('#__rseventspro_events'))
				->set($db->qn('location').' = '.$db->q($idlocation))
				->set($db->qn('owner').' = '.$db->q($user->get('id')))
				->set($db->qn('name').' = '.$db->q($event->name))
				->set($db->qn('description').' = '.$db->q($event->description))
				->set($db->qn('start').' = '.$db->q($event->start))
				->set($db->qn('end').' = '.$db->q($event->end))
				->set($db->qn('completed').' = '.$db->q(1))
				->set($db->qn('published').' = '.$db->q(1));
			
			$db->setQuery($query);
			$db->execute();
			$idevent = $db->insertid();
			
			$query->clear()
				->insert($db->qn('#__rseventspro_taxonomy'))
				->set($db->qn('ide').' = '.$db->q($idevent))
				->set($db->qn('id').' = '.$db->q($idcategory))
				->set($db->qn('type').' = '.$db->q('category'));
			
			$db->setQuery($query);
			$db->execute();
			
			$query->clear()
				->insert($db->qn('#__rseventspro_sync'))
				->set($db->qn('id').' = '.$db->q($event->id))
				->set($db->qn('ide').' = '.$db->q($idevent))
				->set($db->qn('from').' = '.$db->q('gcalendar'));
			
			$db->setQuery($query);
			$db->execute();
			
			$i++;
		}
		
		return $i;
	}
	
	/*
	*	Get and parse events
	*/
	
	protected function getEvents() {
		$returns	= array();
		$events		= array();
		$login_url	= "https://www.google.com/accounts/ClientLogin";
		$this->_username = stristr($this->_username,'@') ? $this->_username : $this->_username.'@gmail.com';
		
		
		$fields = array(
		'Email'       => $this->_username,
		'Passwd'      => $this->_password,
		'service'     => 'cl', // cl = Google calendar
		'source'      => 'rseventspro-google-calendar-grabber',
		'accountType' => 'HOSTED_OR_GOOGLE',
		);

		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL,$login_url);
		curl_setopt($curl, CURLOPT_POST, 1);
		curl_setopt($curl, CURLOPT_POSTFIELDS,$fields);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); 
		$result = curl_exec($curl);
		
		
		if (empty($result)) {
			$this->addError(JText::_('COM_RSEVENTSPRO_GOOGLE_ERROR_1'));
		}
		
		$lines = explode("\n",$result);
		if (!empty($lines)) {
			foreach ($lines as $line) {
				$line = trim($line);
				if(!$line) continue;
				list($k,$v) = explode('=',$line,2);
				$returns[$k] = $v;
			}
		}
		curl_close($curl);

		if (empty($returns['Auth'])) {
			if (isset($returns['Error'])) 
				$this->addError($returns['Error']);
			else 
				$this->addError(JText::_('COM_RSEVENTSPRO_GOOGLE_ERROR_2'));
			return;
		}
		
		//$url = "https://www.google.com/calendar/feeds/$this->_username/private/full?alt=jsonc&max-results=250";
		$header = array( 'Authorization: GoogleLogin auth=' . $returns['Auth'] );
		$url	= "https://www.google.com/calendar/feeds/default/allcalendars/full?alt=jsonc";
		$result = $this->getData($header,$url,1);
		$data	= json_decode($result);
		
		if (empty($data)) {
			$this->addError(JText::_('COM_RSEVENTSPRO_GOOGLE_ERROR_3'));
			return;
		}
		
		$allevents = array();
		foreach ($data->data->items as $item) {
			if ($feed = $this->getData($header, $item->eventFeedLink.'?alt=jsonc', 1)) {
				$feed = json_decode($feed);
				
				if ($feed->data->items) {
					$allevents = array_merge($allevents, $feed->data->items);
				}
			}
		}
		
		if (!empty($allevents)) {
			foreach ($allevents as $item) {
				$event				= new stdClass();
				$event->id			= $item->id;
				$event->name		= $item->title;
				$event->description = $item->details;
				$event->location	= $item->location;
				
				if (isset($item->when)) {
					$dates = $item->when[0];
					$allday = false;
				
					if (strpos($dates->start,'T') === false)
						$allday = true;
					
					$start = new RSDate($dates->start);				
					if ($allday)
						$start->setHourMinuteSecond(0,0,0);
						
					$start->setTZByID($start->getTZID());
					$start->convertTZ(new RSDate_Timezone('GMT'));
					
					if ($allday) {
						$end = new RSDate();
						$end->copy($start);
						$end->addSeconds(86399);
					} else {
						$end = new RSDate($dates->end);
						$end->setTZByID($end->getTZID());
						$end->convertTZ(new RSDate_Timezone('GMT'));
					}
					
					$event->start = $start->formatLikeDate('Y-m-d H:i:s');
					$event->end = $end->formatLikeDate('Y-m-d H:i:s');
				}
				
				if (isset($item->recurrence)) {
					$estart = rseventsproHelper::date('now',null,false,true);
					$estart->setTZByID($estart->getTZID());
					$estart->convertTZ(new RSDate_Timezone('GMT'));
					
					$event->start = $estart->formatLikeDate('Y-m-d H:i:s');
					$estart->addSeconds(7200);
					$event->end = $estart->formatLikeDate('Y-m-d H:i:s');
					
					$lines = explode("\n",$item->recurrence);
					if (!empty($lines[0])) {
						$line = explode(':',$lines[0]);
						if (!empty($line[1])) {	
							$startd = new RSDate($line[1]);
							$startd->setTZByID($startd->getTZID());
							$startd->convertTZ(new RSDate_Timezone('GMT'));
							
							$event->start = $startd->formatLikeDate('Y-m-d H:i:s');
						}
					}
					
					if (!empty($lines[1])) {
						$line = explode(':',$lines[1]);
						if (!empty($line[1])) {
							$endd = new RSDate($line[1]);
							$endd->setTZByID($endd->getTZID());
							$endd->convertTZ(new RSDate_Timezone('GMT'));
							
							$event->end = $endd->formatLikeDate('Y-m-d H:i:s');
						}
					}
				}
				$events[] = $event;
			}
		}
		
		return $events;
	}
	
	/*
	*	Get data from server
	*/
	
	protected function getData($header, $url, $type = 1) {
		$original = $header;
		$headers = $this->getHeaders($header,$url);
		
		$redirect = false;
		$redirect_url = false;
		
		$headers = explode("\n",$headers);
		foreach($headers as $header) {
			$header = trim($header);
			if(strpos($header,'Location:') !== FALSE) {
				$redirect = true;
				$redirect_url = trim(str_replace('Location: ','',$header));
			}
		}
		
		$c_url = $redirect ? $redirect_url : $url;
		
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $c_url);
		curl_setopt($curl, CURLOPT_HTTPHEADER, $original);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); 

		$result = curl_exec($curl);
		curl_close($curl);
		
		return $result;
	}
	
	
	/*
	*	Get data from server
	*/
	
	protected function getHeaders($header, $url) {
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_HEADER, 1);
		curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl, CURLOPT_NOBODY, true);

		$result = curl_exec($curl);
		curl_close($curl);	
		
		return $result;
	}
	
	
	/*
	*	Add errors
	*/
	
	protected function addError($error) {
		if (isset($this->_errors)) {
			if (in_array($error,$this->_errors)) {
				return $this->_errors;
			}
		}
		
		$this->_errors[] = $error;
	}
	
	/*
	*	Get errors
	*/
	
	public function getErrors() {
		return $this->_errors;
	}
}