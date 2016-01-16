<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' );

class rseventsproModelSettings extends JModelAdmin
{
	/**
	 * @var		string	The prefix to use with controller messages.
	 * @since	1.6
	 */
	protected $text_prefix = 'COM_RSEVENTSPRO';
	
	/**
	 * Method to get the record form.
	 *
	 * @param	array	$data		Data for the form.
	 * @param	boolean	$loadData	True if the form is to load its own data (default case), false if not.
	 *
	 * @return	mixed	A JForm object on success, false on failure
	 * @since	1.6
	 */
	public function getForm($data = array(), $loadData = true) {
		$jinput = JFactory::getApplication()->input;
		
		// Get the form.
		$form = $this->loadForm('com_rseventspro.settings', 'settings', array('control' => 'jform', 'load_data' => $loadData));
		if (empty($form))
			return false;
		
		return $form;
	}
	
	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return	mixed	The data for the form.
	 * @since	1.6
	 */
	protected function loadFormData() {
		$data = (array) $this->getConfig();
		
		if (isset($data['gallery_params'])) {
			$registry = new JRegistry;
			$registry->loadString($data['gallery_params']);
			$data['gallery'] = $registry->toArray();
		}
		
		return $data;
	}
	
	/**
	 * Method to get Tabs
	 *
	 * @return	mixed	The Joomla! Tabs.
	 * @since	1.6
	 */
	public function getTabs() {
		$tabs = new RSTabs('settings');
		return $tabs;
	}
	
	/**
	 * Method to get the configuration data.
	 *
	 * @return	mixed	The data for the configuration.
	 * @since	1.6
	 */
	public function getConfig() {
		return rseventsproHelper::getConfig();
	}
	
	/**
	 * Method to get the available layouts.
	 *
	 * @return	mixed	The available layouts.
	 * @since	1.6
	 */
	public function getLayouts() {
		$fields = array('general', 'dashboard', 'events', 'emails', 'maps', 'captcha', 'payments', 'sync', 'integrations');
		if (rseventsproHelper::isGallery())
			$fields[] = 'gallery';
		
		return $fields;
	}
	
	/**
	 * Method to get the social info.
	 *
	 * @return	mixed	The available social information.
	 * @since	1.6
	 */
	public function getSocial() {
		$options = array('cb' => false, 'js' => false, 'kunena' => false, 'fireboard' => false,
				'jcomments' => false, 'jomcomment' => false, 'rscomments' => false, 'k2' => false
		);
		
		if (file_exists(JPATH_SITE.'/components/com_comprofiler/comprofiler.php'))
			$options['cb'] = true;
		
		if (file_exists(JPATH_SITE.'/components/com_community/community.php'))
			$options['js'] = true;
		
		if (file_exists(JPATH_SITE.'/components/com_kunena/kunena.php'))
			$options['kunena'] = true;
		
		if (file_exists(JPATH_SITE.'/components/com_fireboard/fireboard.php'))
			$options['fireboard'] = true;
			
		if (file_exists(JPATH_SITE.'/components/com_jcomments/jcomments.php'))
			$options['jcomments'] = true;
		
		if (file_exists(JPATH_SITE.'/plugins/content/jom_comment_bot/jom_comment_bot.php'))
			$options['jomcomment'] = true;
			
		if (file_exists(JPATH_SITE.'/components/com_rscomments/helpers/rscomments.php'))
			$options['rscomments'] = true;
		
		if (file_exists(JPATH_SITE.'/components/com_k2/k2.php'))
			$options['k2'] = true;
		
		return $options;
	}
	
	/**
	 * Method to save configuration.
	 *
	 * @return	boolean		True if success.
	 * @since	1.6
	 */
	public function save($data) {
		// Save gallery params
		if (rseventsproHelper::isGallery()) {
			$gallery = isset($data['gallery']) ? $data['gallery'] : array();
			if (!empty($gallery)) {
				if (is_array($gallery['thumb_resolution']))
					$gallery['thumb_resolution'] = implode(',',$gallery['thumb_resolution']);
				
				if (is_array($gallery['full_resolution']))
					$gallery['full_resolution'] = implode(',',$gallery['full_resolution']);
				
				$registry = new JRegistry;
				$registry->loadArray($gallery);
				$data['gallery_params'] = $registry->toString();
				unset($data['gallery']);
			}
		}
		
		// Save iDeal files
		JFactory::getApplication()->triggerEvent('rseproIdealSaveSettings', array(array('data' => &$data)));
		
		$db		= $this->getDbo();
		$query	= $db->getQuery(true);
		
		$query->clear()
			->select('*')
			->from($db->qn('#__rseventspro_config'));
		
		$db->setQuery($query);
		if ($configuration = $db->loadObjectList()) {
			foreach($configuration as $config) {
				if (isset($data[$config->name])) {
					$query->clear()
						->update($db->qn('#__rseventspro_config'))
						->set($db->qn('value').' = '.$db->q(trim($data[$config->name])))
						->where($db->qn('name').' = '.$db->q($config->name));
					$db->setQuery($query);
					$db->execute();
				}
			}
		}
		
		return true;
	}
	
	/**
	 * Method to save Facebook token.
	 *
	 * @return	boolean		True if success.
	 * @since	1.6
	 */
	public function savetoken() {
		$db		= $this->getDbo();
		$query	= $db->getQuery(true);
		$config	= $this->getConfig();
		$token	= JFactory::getApplication()->input->getString('access_token');
		
		if (empty($token)) {
			$this->setError(JText::_('COM_RSEVENTSPRO_FACEBOOK_NO_CONNECTION'));
			return false;
		}
		
		require_once JPATH_SITE.'/components/com_rseventspro/helpers/facebook/facebook.php';
		$facebook = new RSEPROFacebook(array('appId'  => $config->facebook_appid, 'secret' => $config->facebook_secret, 'cookie' => true));
		$facebook->setAccessToken($token);
		$facebook->setExtendedAccessToken();
		$newtoken = $facebook->getPersistentData('access_token');
		$token = !empty($newtoken) ? $newtoken : $token;
		
		$query->clear()
			->update($db->qn('#__rseventspro_config'))
			->set($db->qn('value').' = '.$db->q(trim($token)))
			->where($db->qn('name').' = '.$db->q('facebook_token'));
		
		$db->setQuery($query);
		$db->execute();
		
		return true;
	}
	
	/**
	 * Method to import Facebook events.
	 *
	 * @return	boolean		True if success.
	 * @since	1.6
	 */
	public function facebook() {
		$db		= $this->getDbo();
		$query	= $db->getQuery(true);
		$config = $this->getConfig();
		$jform	= JFactory::getApplication()->input->get('jform', array(),'array');
		$allowed= $config->facebook_pages;
		$allowed= !empty($allowed) ? explode(',',$allowed) : '';
		
		if (empty($config->facebook_token)) {
			$this->setError(JText::_('COM_RSEVENTSPRO_FACEBOOK_NO_CONNECTION'));
			return false;
		}
		
		require_once JPATH_SITE.'/components/com_rseventspro/helpers/facebook/facebook.php';
		
		$container	= array();
		$facebook	= new RSEPROFacebook(array('appId'  => $config->facebook_appid, 'secret' => $config->facebook_secret, 'cookie' => true));
		$attachment =  array('access_token' => $config->facebook_token,'limit' => 200);
		
		try {
			$user		= $facebook->api('/me', 'GET', $attachment);
			$uid 		= $user['id'];
			$pages		= $facebook->api('/me/accounts?fields=id', 'GET', $attachment);
			$fbpages	= array();
			$fbpages[]	= $uid;
			$allevents	= array();
			
			if (!empty($pages) && !empty($pages['data'])) {
				foreach($pages['data'] as $page) {
					if (!empty($allowed)) {
						foreach ($allowed as $pid) {
							$pid = trim($pid);
							if ($pid == $page['id']) {
								$fbpages[] = $page['id'];
							}
						}
					} else {
						$fbpages[] = $page['id'];
					}
				}
			}
			
			// Get user events
			$events	= $facebook->api('/me/events', 'GET', $attachment);
			
			if (!empty($events) && !empty($events['data'])) {
				foreach ($events['data'] as $event) {
					$allevents[$event['id']] = $event;
				}
			}
			
			// Get page events
			if (!empty($fbpages)) {
				foreach ($fbpages as $pageid) {
					if ($pageEvents = $facebook->api('/'.$pageid.'/events', 'GET', $attachment)) {
						if (!empty($pageEvents) && !empty($pageEvents['data'])) {
							foreach ($pageEvents['data'] as $pageEvent) {
								$allevents[$pageEvent['id']] = $pageEvent;
							}
						}
					}
				}
			}
			
			// Parse events
			if (!empty($allevents)) {
				foreach ($allevents as $event) {
					$eobj = $facebook->api($event['id'], 'GET', $attachment);
					
					if (empty($eobj)) {
						continue;
					}
					
					$picture = $facebook->api(array('method' => 'fql.query','query' => 'select pic_big from event where eid = '.$event['id'].' '));
					
					$image = '';
					if (!empty($picture) && !empty($picture[0])) {
						$image = isset($picture[0]['pic_big']) ? $picture[0]['pic_big'] : '';
					}
					
					if (!empty($eobj) && !empty($eobj['owner']) && !empty($eobj['owner']['id'])) {
						if (!in_array($eobj['owner']['id'], $fbpages)) {
							continue;
						}
					}
					
					$ev					= new stdClass();
					$ev->id				= @$eobj['id'];
					$ev->name			= @$eobj['name'];
					$ev->description	= @$eobj['description'];
					
					if (isset($eobj['start_time'])) {
						$startDate = new DateTime($eobj['start_time'], isset($eobj['timezone']) ? new DateTimeZone($eobj['timezone']) : null);
					} else {
						$startDate = new DateTime();
					}
					
					$startDate->setTimezone(new DateTimeZone('UTC'));
					$start = $startDate->format('Y-m-d H:i:s');
					
					if (isset($eobj['end_time'])) {
						$endDate = new DateTime($eobj['end_time'], isset($eobj['timezone']) ? new DateTimeZone($eobj['timezone']) : null);
						$endDate->setTimezone(new DateTimeZone('UTC'));
						$end = $endDate->format('Y-m-d H:i:s');
						
						$allday = 0;
					} else {
						$end = JFactory::getDbo()->getNullDate();
						
						$allday = 1;
					}
					
					$ev->start			= $start;
					$ev->end			= $end;
					$ev->allday			= $allday;
					$ev->location		= isset($eobj['location']) ? $eobj['location'] : 'Facebook Location';
					$ev->street			= isset($eobj['venue']['street']) ? $eobj['venue']['street'] : '';
					$ev->city			= isset($eobj['venue']['city']) ? $eobj['venue']['city'] : '';
					$ev->state			= isset($eobj['venue']['state']) ? $eobj['venue']['state'] : '';
					$ev->country		= isset($eobj['venue']['country']) ? $eobj['venue']['country'] : '';
					$ev->lat			= isset($eobj['venue']['latitude']) ? $eobj['venue']['latitude'] : '';
					$ev->lon			= isset($eobj['venue']['longitude']) ? $eobj['venue']['longitude'] : '';
					$ev->image			= $image;
					
					$container[] = $ev; 
				}
			}
		} catch (Exception $e) {
			$this->setError($e->getMessage());
			return false;
		}
		
		$i = 0;
		if (!empty($container))
		{
			$idcategory = isset($jform['facebook_category']) ? $jform['facebook_category'] : $config->facebook_category;
			
			if (empty($idcategory))
			{
				$query->clear()
					->insert($db->qn('#__rseventspro_categories'))
					->set($db->qn('name').' = '.$db->q('Facebook events'));
				
				$db->setQuery($query);
				$db->execute();
				$idcategory = $db->insertid();
			}
			
			foreach ($container as $event) {
				$idlocation = isset($jform['facebook_location']) ? $jform['facebook_location'] : $config->facebook_location;
				
				// Check if the current event was already added
				$query->clear()
					->select('COUNT(id)')
					->from($db->qn('#__rseventspro_sync'))
					->where($db->qn('id').' = '.$db->q($event->id))
					->where($db->qn('from').' = '.$db->q('facebook'));
				
				$db->setQuery($query);
				$indb = $db->loadResult();
				
				if (!empty($indb)) {
					continue;
				}
				
				if (empty($idlocation)) {
					$address = $event->street;
					if (!empty($event->city))		$address .= ' , '.$event->city;
					if (!empty($event->state))		$address .= ' , '.$event->state;
					if (!empty($event->country))	$address .= ' , '.$event->country;
					
					
					$query->clear()
						->insert($db->qn('#__rseventspro_locations'))
						->set($db->qn('name').' = '.$db->q($event->location))
						->set($db->qn('address').' = '.$db->q($address))
						->set($db->qn('coordinates').' = '.$db->q($event->lat.','.$event->lon))
						->set($db->qn('published').' = '.$db->q(1));
					
					$db->setQuery($query);
					$db->execute();
					$idlocation = $db->insertid();
				}
				
				$query->clear()
					->insert($db->qn('#__rseventspro_events'))
					->set($db->qn('location').' = '.$db->q($idlocation))
					->set($db->qn('owner').' = '.$db->q(JFactory::getUser()->get('id')))
					->set($db->qn('name').' = '.$db->q($event->name))
					->set($db->qn('description').' = '.$db->q($event->description))
					->set($db->qn('start').' = '.$db->q($event->start))
					->set($db->qn('end').' = '.$db->q($event->end))
					->set($db->qn('allday').' = '.$db->q($event->allday))
					->set($db->qn('options').' = '.$db->q(rseventsproHelper::getDefaultOptions()))
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
					->set($db->qn('from').' = '.$db->q('facebook'));
				
				$db->setQuery($query);
				$db->execute();
				
				//create the thumb
				if (!empty($event->image)) {
					jimport('joomla.filesystem.file');
					$path = JPATH_SITE.'/components/com_rseventspro/assets/images/events/';
					
					$ext		= 'jpg';
					$filename	= $event->id;
					
					while (JFile::exists($path.$filename.'.'.$ext)) {
						$filename .= rand(1,999);
					}
					
					rseventsproHelper::resize($event->image, 0,	$path.$filename.'.'.$ext);
					
					$query->clear()
						->update($db->qn('#__rseventspro_events'))
						->set($db->qn('icon').' = '.$db->q($filename.'.'.$ext))
						->where($db->qn('id').' = '.$db->q($idevent));
					
					$db->setQuery($query);
					$db->execute();
				}
				$i++;
			}
		}
		
		if (!$container) {
			$this->setError(JText::_('COM_RSEVENTSPRO_FACEBOOK_NO_EVENTS_FOUND'));
			return false;
		}
		
		JFactory::getApplication()->enqueueMessage(JText::sprintf('COM_RSEVENTSPRO_FACEBOOK_IMPORTED_NEW_FOUND', $i, count($container)));
		return true;
	}
	
	/**
	 * Method to import Google events.
	 *
	 * @return	boolean		True if success.
	 * @since	1.6
	 */
	public function google() {
		require_once JPATH_SITE.'/components/com_rseventspro/helpers/google.php';
		
		$google		= new RSEPROGoogle();
		$response	= $google->parse();
		
		if (!$response) {
			$this->setError(JText::_('COM_RSEVENTSPRO_NO_EVENTS_IMPORTED'));
			return false;
		}
		
		$this->setState($this->getName() . '.gcevents', $response);
		return true;
	}
}