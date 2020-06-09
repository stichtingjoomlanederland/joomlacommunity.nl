<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' );

class RseventsproModelSettings extends JModelAdmin
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
		
		if (isset($data['cancel_to'])) {
			$data['cancel_to'] = explode(',',$data['cancel_to']);
		}
		
		if (isset($data['gallery_params'])) {
			try {
				$registry = new JRegistry;
				$registry->loadString($data['gallery_params']);
				$data['gallery'] = $registry->toArray();
			} catch (Exception $e) {
				$data['gallery'] = array();
			}
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
		
		if (file_exists(JPATH_ADMINISTRATOR.'/components/com_rsmediagallery/helpers/integration.php'))
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
				'jcomments' => false, 'jomcomment' => false, 'rscomments' => false, 'k2' => false,
				'easydiscuss' => false, 'easysocial' => false
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
		
		if (file_exists(JPATH_SITE.'/components/com_easydiscuss/easydiscuss.php'))
			$options['easydiscuss'] = true;
		
		if (file_exists(JPATH_ADMINISTRATOR.'/components/com_easysocial/includes/foundry.php'))
			$options['easysocial'] = true;
		
		return $options;
	}
	
	/**
	 * Method to save configuration.
	 *
	 * @return	boolean		True if success.
	 * @since	1.6
	 */
	public function save($data) {
		$files		= JFactory::getApplication()->input->files->get('jform'); 
		$default	= isset($files['default_image']) ? $files['default_image'] : array();
		$app		= JFactory::getApplication();
		
		// Check the coordinates
		try {
			$data['google_maps_center'] = rseventsproHelper::checkCoordinates($data['google_maps_center']);
		} catch(Exception $e) {
			$data['google_maps_center'] = '';
			$app->enqueueMessage($e->getMessage(),'error');
		}
		
		// Set default image
		if ($default && $default['error'] == 0 && $default['size'] > 0) {
			jimport('joomla.filesystem.file');
			
			$extension = strtolower(JFile::getExt($default['name']));
			if (in_array($extension, array('jpg','jpeg','png','gif'))) {
				$file = JFile::makeSafe($default['name']);
				if (JFile::upload($default['tmp_name'], JPATH_SITE.'/components/com_rseventspro/assets/images/default/'.$file)) {
					$data['default_image'] = $file;
				}
			}
		}
		
		// Save Facebook pages
		if (isset($data['facebook_pages'])) {
			$data['facebook_pages'] = is_array($data['facebook_pages']) ? implode(',', $data['facebook_pages']) : $data['facebook_pages'];
		} else {
			$data['facebook_pages'] = '';
		}
		
		// Save Facebook groups
		if (isset($data['facebook_groups'])) {
			$data['facebook_groups'] = is_array($data['facebook_groups']) ? implode(',', $data['facebook_groups']) : $data['facebook_groups'];
		} else {
			$data['facebook_groups'] = '';
		}
		
		// Save Google calendars
		if (isset($data['google_calendars'])) {
			$data['google_calendars'] = is_array($data['google_calendars']) ? implode(',', $data['google_calendars']) : $data['google_calendars'];
		} else {
			$data['google_calendars'] = '';
		}
		
		// Save cancel email status
		if (isset($data['cancel_to'])) {
			$data['cancel_to'] = is_array($data['cancel_to']) ? implode(',', $data['cancel_to']) : $data['cancel_to'];
		} else {
			$data['cancel_to'] = '';
		}
		
		// Save gallery params
		if (file_exists(JPATH_ADMINISTRATOR.'/components/com_rsmediagallery/helpers/integration.php')) {
			$gallery = isset($data['gallery']) ? $data['gallery'] : array();
			if (!empty($gallery)) {
				if (is_array($gallery['thumb_resolution']))
					$gallery['thumb_resolution'] = implode(',',$gallery['thumb_resolution']);
				
				if (is_array($gallery['full_resolution']))
					$gallery['full_resolution'] = implode(',',$gallery['full_resolution']);
				
				try {
					$registry = new JRegistry;
					$registry->loadArray($gallery);
					$data['gallery_params'] = $registry->toString();
				} catch (Exception $e) {
					$data['gallery_params'] = array();
				}
				unset($data['gallery']);
			}
		}
		
		// Save iDeal files
		$app->triggerEvent('rseproIdealSaveSettings', array(array('data' => &$data)));
		
		$db		= $this->getDbo();
		$query	= $db->getQuery(true);
		
		$query->select('*')->from($db->qn('#__rseventspro_config'));
		$db->setQuery($query);
		$configuration = $db->loadColumn();
		
		foreach ($data as $name => $value) {
			$value = trim($value);
			
			if (in_array($name, $configuration)) {
				$query->clear()
					->update($db->qn('#__rseventspro_config'))
					->set($db->qn('value').' = '.$db->q($value))
					->where($db->qn('name').' = '.$db->q($name));
					
			} else {
				$query->clear()
					->insert($db->qn('#__rseventspro_config'))
					->set($db->qn('value').' = '.$db->q($value))
					->set($db->qn('name').' = '.$db->q($name));
			}
			
			$db->setQuery($query);
			$db->execute();
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
		$db			 = $this->getDbo();
		$query		 = $db->getQuery(true);
		$config		 = $this->getConfig();
		$redirectURI = JRoute::_('index.php?option=com_rseventspro&task=settings.savetoken', false, 1);
		$token		 = false;
		
		require_once JPATH_SITE.'/components/com_rseventspro/helpers/facebook/autoload.php';
		
		try {
			$facebook = new Facebook\Facebook(array(
				'app_id' => $config->facebook_appid,
				'app_secret' => $config->facebook_secret,
				'default_graph_version' => 'v4.0',
				'pseudo_random_string_generator' => 'openssl'
			));
			
			$helper = $facebook->getRedirectLoginHelper();
			$token	= $helper->getAccessToken($redirectURI);
			
			if (isset($token)) {
				if (!$token->isLongLived()) {
					$oAuth2Client	= $facebook->getOAuth2Client();
					$token			= $oAuth2Client->getLongLivedAccessToken($token);
				}
				
				$token	= $token->getValue();
			}
		} catch (Exception $e) {
			JFactory::getApplication()->enqueueMessage($e->getMessage(), 'error');
		}
		
		if ($token) {
			$query->clear()
				->update($db->qn('#__rseventspro_config'))
				->set($db->qn('value').' = '.$db->q(trim($token)))
				->where($db->qn('name').' = '.$db->q('facebook_token'));
			
			$db->setQuery($query);
			$db->execute();
			
			return true;
		}
		
		if ($error = JFactory::getApplication()->input->getString('error_message')) {
			JFactory::getApplication()->enqueueMessage($error, 'error');
		}
		
		return false;
	}
	
	/**
	 * Method to auth Google events.
	 *
	 * @return	boolean		True if success.
	 * @since	1.6
	 */
	public function google() {
		require_once JPATH_SITE.'/components/com_rseventspro/helpers/google.php';
		
		$google	= new RSEPROGoogle();
		
		return $google->saveToken();
	}
	
	/**
	 * Method to import Google events.
	 *
	 * @return	boolean		True if success.
	 * @since	1.6
	 */
	public function gimport() {
		require_once JPATH_SITE.'/components/com_rseventspro/helpers/google.php';
		
		try {
			$google		= new RSEPROGoogle();
			$response	= $google->parse();
			
			if (!$response) {
				$this->setError(JText::_('COM_RSEVENTSPRO_NO_EVENTS_IMPORTED'));
				return false;
			}
			
			$this->setState($this->getName() . '.gcevents', $response);
			return true;
		} catch (Exception $e) {
			$this->setError($e->getMessage());
			return false;
		}
	}
	
	public function getLogs() {
		$db		= JFactory::getDbo();
		$query	= $this->getLogQuery();
		
		$db->setQuery($query,JFactory::getApplication()->input->getInt('limitstart', 0), JFactory::getConfig()->get('list_limit'));
		return $db->loadObjectList();
	}
	
	public function getTotalLogs() {
		$db		= JFactory::getDbo();
		$query	= $this->getLogQuery(true);
		
		$db->setQuery($query);
		return $db->loadResult();
	}
	
	public function getPagination() {
		jimport('joomla.html.pagination');
		return new JPagination($this->getTotalLogs(), JFactory::getApplication()->input->getInt('limitstart', 0), JFactory::getConfig()->get('list_limit'));
	}
	
	protected function getLogQuery($count = false) {
		$db		= JFactory::getDbo();
		$query	= $db->getQuery();
		$type	= JFactory::getApplication()->input->get('from');
		$search	= JFactory::getApplication()->input->getString('search');
		$select = $count ? 'COUNT('.$db->qn('id').')' : '*';
		
		$query->clear()
			->select($select)
			->from($db->qn('#__rseventspro_sync_log'))
			->where($db->qn('type').' = '.$db->q($type))
			->order($db->qn('date').' DESC')
			->order($db->qn('id').' ASC');
		
		if ($search) {
			$search = $db->q('%'.$db->escape($search, true).'%');
			$query->where('('.$db->qn('name').' LIKE '.$search.' OR '.$db->qn('from').' LIKE '.$search.')');
		}
		
		return $query;
	}
	
	public function getLogin() {
		$config 	 = $this->getConfig();
		$redirectURI = JRoute::_('index.php?option=com_rseventspro&task=settings.savetoken', false, 1);
		
		if ($config->facebook_appid && $config->facebook_secret) {
			try {
				require_once JPATH_SITE.'/components/com_rseventspro/helpers/facebook/autoload.php';
				
				$facebook = new Facebook\Facebook(array(
					'app_id' => $config->facebook_appid,
					'app_secret' => $config->facebook_secret,
					'default_graph_version' => 'v4.0',
					'pseudo_random_string_generator' => 'openssl'
				));
				
				$helper = $facebook->getRedirectLoginHelper();
				$permissions = array('user_events', 'manage_pages', 'publish_to_groups');

				return $helper->getLoginUrl($redirectURI, $permissions);
				
			} catch (Exception $e) {
				JFactory::getApplication()->enqueueMessage($e->getMessage(), 'error');
			}
		}
		
		return false;
	}
	
	public function importFacebookEvents() {
		$config 		= rseventsproHelper::getConfig();
		$input			= JFactory::getApplication()->input;
		$init			= $input->getInt('init', 0);
		$step			= $input->getInt('step', 0);
		$total			= $input->getInt('total', 0);
		$fbpages		= $input->get('fbpages', array(), 'array');
		$owners			= $input->get('owners', array(), 'array');
		$names			= $input->get('names', array(), 'array');
		$types			= $input->get('types', array(), 'array');
		$allowedPages	= $config->facebook_pages;
		$allowedPages	= !empty($allowedPages) ? explode(',',$allowedPages) : '';
		$allowedGroups	= $config->facebook_groups;
		$allowedGroups	= !empty($allowedGroups) ? explode(',',$allowedGroups) : '';
		$profile		= isset($config->facebook_profile) ? $config->facebook_profile : 1;
		$data			= new stdClass();
		$reset			= false;
		$limit			= 50;
		
		try {
			require_once JPATH_SITE.'/components/com_rseventspro/helpers/facebook/autoload.php';
			
			$facebook = new Facebook\Facebook(array(
				'app_id' => $config->facebook_appid,
				'app_secret' => $config->facebook_secret,
				'default_graph_version' => 'v4.0',
				'default_access_token' => $config->facebook_token,
				'pseudo_random_string_generator' => 'openssl'
			));
			
			if ($init) {
				$fbRequest	= $facebook->get('me');
				$user		= $fbRequest->getDecodedBody();
				$uid 		= $user['id'];
				$fbpages	= array();
				$owners[]	= $uid;
				
				if ($profile) {
					$fbpages[] = 'me';
					$names['me'] = 'FBUSER';
					$types['me'] = 'user';
				}
				
				if (!empty($allowedPages)) {
					$fbRequest	= $facebook->get('me/accounts?fields=id,name&limit=200');
					$pages		= $fbRequest->getDecodedBody();
					
					if (!empty($pages) && !empty($pages['data'])) {
						foreach($pages['data'] as $page) {
							foreach ($allowedPages as $pid) {
								$pid = trim($pid);
								if ($pid == $page['id']) {
									$fbpages[] = $page['id'];
									$owners[] = $page['id'];
									$names[$page['id']] = $page['name'];
									$types[$page['id']] = 'page';
								}
							}
						}
					}
				}
				
				if (!empty($allowedGroups)) {
					$fbRequest	= $facebook->get('me/groups?fields=id,name&limit=200');
					$groups		= $fbRequest->getDecodedBody();
					
					if (!empty($groups) && !empty($groups['data'])) {
						foreach($groups['data'] as $group) {
							foreach ($allowedGroups as $pid) {
								$pid = trim($pid);
								if ($pid == $group['id']) {
									$fbpages[] = $group['id'];
									$owners[] = $group['id'];
									$names[$group['id']] = $group['name'];
									$types[$group['id']] = 'group';
								}
							}
						}
					}
				}
			}
			
			if (!empty($fbpages)) {
				reset($fbpages);
				if ($pageid = current($fbpages)) {
					$stop = false;
					$response = $facebook->get('/'.$pageid.'/events?fields=id,name,start_time,end_time,timezone,description,owner,cover,place,event_times&limit='.$limit);
					
					// Get the first page
					if ($feed = $response->getGraphEdge()) {
						// Parse the first page
						if ($step == 0) {
							if ($events = $feed->asArray()) {
								foreach ($events as $event) {
									$total += rseventsproHelper::parseFacebookEvent($event, $names[$pageid], $owners, $types[$pageid]);
								}
							}
						}
						
						// Go to the next page
						if ($step > 0) {
							for($i=0;$step>$i;$i++) {
								if ($feed) {
									$feed = $facebook->next($feed);
								} else {
									$stop = true;
								}
							}
							
							// Parse events from current page
							if ($feed) {
								$events = $feed->asArray();
								foreach ($events as $event) {
									$total += rseventsproHelper::parseFacebookEvent($event, $names[$pageid], $owners, $types[$pageid]);
								}
							} else {
								$stop = true;
							}
						}
					} else {
						$stop = true;
					}
					
					if ($stop) {
						// All pages were parsed, and we remove the page ID and reset the $step value
						array_shift($fbpages);
						$reset = true;
					}
				}
			}
			
			$data->fbpages	= $fbpages;
			$data->owners	= $owners;
			$data->names	= $names;
			$data->types	= $types;
			$data->total	= $total;
			$data->step		= $reset ? 0 : $step + 1;			
		} catch (Exception $e) {
			$data->message = $e->getMessage();
		}
		
		return json_encode($data);
	}
}