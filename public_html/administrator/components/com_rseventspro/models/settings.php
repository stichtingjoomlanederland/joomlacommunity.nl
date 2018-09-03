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
		$redirectURI = JRoute::_('index.php?option=com_rseventspro&task=settings.savetoken', false, true);
		$token		 = false;
		
		require_once JPATH_SITE.'/components/com_rseventspro/helpers/facebook/autoload.php';
		
		try {
			$facebook = new Facebook\Facebook(array(
				'app_id' => $config->facebook_appid,
				'app_secret' => $config->facebook_secret,
				'default_graph_version' => 'v2.10'
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
	 * Method to import Facebook events.
	 *
	 * @return	boolean		True if success.
	 * @since	1.6
	 */
	public function facebook() {
		$config 	= $this->getConfig();
		$jform		= JFactory::getApplication()->input->get('jform', array(),'array');
		$parsed		= 0;
		$total		= 0;
		
		if (empty($config->facebook_token)) {
			$this->setError(JText::_('COM_RSEVENTSPRO_FACEBOOK_NO_CONNECTION'));
			return false;
		}
		
		try {
			list($parsed, $total) = rseventsproHelper::facebookEvents($jform);
		} catch (Exception $e) {
			$this->setError($e->getMessage());
			return false;
		}
		
		if (!$total) {
			$this->setError(JText::_('COM_RSEVENTSPRO_FACEBOOK_NO_EVENTS'));
			return false;
		}
		
		if (!$parsed) {
			$this->setError(JText::_('COM_RSEVENTSPRO_FACEBOOK_NO_EVENTS_IMPORTED'));
			return false;
		}
		
		JFactory::getApplication()->enqueueMessage(JText::sprintf('COM_RSEVENTSPRO_FACEBOOK_IMPORT_SUCCESS', $parsed));
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
		$google->saveToken();
		$response	= $google->parse();
		
		if (!$response) {
			$this->setError(JText::_('COM_RSEVENTSPRO_NO_EVENTS_IMPORTED'));
			return false;
		}
		
		$this->setState($this->getName() . '.gcevents', $response);
		return true;
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
		$redirectURI = JRoute::_('index.php?option=com_rseventspro&task=settings.savetoken', false, true);
		
		if ($config->facebook_appid && $config->facebook_secret) {
			try {
				require_once JPATH_SITE.'/components/com_rseventspro/helpers/facebook/autoload.php';
				
				$facebook = new Facebook\Facebook(array(
					'app_id' => $config->facebook_appid,
					'app_secret' => $config->facebook_secret,
					'default_graph_version' => 'v2.10'
				));
				
				$helper = $facebook->getRedirectLoginHelper();
				$permissions = array('user_events', 'manage_pages');

				return $helper->getLoginUrl($redirectURI, $permissions);
				
			} catch (Exception $e) {
				JFactory::getApplication()->enqueueMessage($e->getMessage(), 'error');
			}
		}
		
		return false;
	}
}