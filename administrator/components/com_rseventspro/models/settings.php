<?php
/**
* @version 1.0.0
* @package RSEvents!Pro 1.0.0
* @copyright (C) 2011 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' );
jimport( 'joomla.application.component.model' );

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
		
		if (rseventsproHelper::ideal()) {
			if (file_exists(JPATH_SITE.'/components/com_rseventspro/helpers/ideal/files/ing/cert.cer')) {
				$ing_cer_label = '<span class="icon-ok"></span> '.JText::_('COM_RSEVENTSPRO_CONF_PAYMENT_IDEAL_CER');
			} else {
				$ing_cer_label = '<span class="icon-cancel"></span> '.JText::_('COM_RSEVENTSPRO_CONF_PAYMENT_IDEAL_CER');
			}
			
			if (file_exists(JPATH_SITE.'/components/com_rseventspro/helpers/ideal/files/ing/priv.pem')) {
				$ing_pem_label = '<span class="icon-ok"></span> '.JText::_('COM_RSEVENTSPRO_CONF_PAYMENT_IDEAL_PEM');
			} else {
				$ing_pem_label = '<span class="icon-cancel"></span> '.JText::_('COM_RSEVENTSPRO_CONF_PAYMENT_IDEAL_PEM');
			}
			
			if (file_exists(JPATH_SITE.'/components/com_rseventspro/helpers/ideal/files/rabobank/cert.cer')) {
				$rabo_cer_label = '<span class="icon-ok"></span> '.JText::_('COM_RSEVENTSPRO_CONF_PAYMENT_IDEAL_CER');
			} else {
				$rabo_cer_label = '<span class="icon-cancel"></span> '.JText::_('COM_RSEVENTSPRO_CONF_PAYMENT_IDEAL_CER');
			}
			
			if (file_exists(JPATH_SITE.'/components/com_rseventspro/helpers/ideal/files/rabobank/priv.pem')) {
				$rabo_pem_label = '<span class="icon-ok"></span> '.JText::_('COM_RSEVENTSPRO_CONF_PAYMENT_IDEAL_PEM');
			} else {
				$rabo_pem_label = '<span class="icon-cancel"></span> '.JText::_('COM_RSEVENTSPRO_CONF_PAYMENT_IDEAL_PEM');
			}
			
			$form->setFieldAttribute('ideal_ing_cer','label', $ing_cer_label);
			$form->setFieldAttribute('ideal_ing_pem','label', $ing_pem_label);
			$form->setFieldAttribute('ideal_rabo_cer','label', $rabo_cer_label);
			$form->setFieldAttribute('ideal_rabo_pem','label', $rabo_pem_label);
		}
		
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
		$fields = array('general', 'dashboard', 'events', 'emails', 'maps', 'payments', 'sync');
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
		if (rseventsproHelper::ideal()) {
			jimport('joomla.filesystem.file');
			$files = JFactory::getApplication()->input->files->get('jform');
			
			// ING
			if (isset($files['ideal_ing_cer']) && !empty($files['ideal_ing_cer'])) {
				$cer = $files['ideal_ing_cer'];
				if ($cer['error'] == 0 && $cer['size'] > 0) {
					if (strtolower(JFile::getExt($cer['name'])) == 'cer') {
						JFile::upload($cer['tmp_name'], JPATH_SITE.'/components/com_rseventspro/helpers/ideal/files/ing/cert.cer');
						$data['ideal_ing_cer'] = 1;
					} else {
						JFactory::getApplication()->enqueueMessage(JText::_('COM_RSEVENTSPRO_CONF_PAYMENT_IDEAL_CER_ERROR'),'error');
					}
				}
			}
			
			if (isset($files['ideal_ing_pem']) && !empty($files['ideal_ing_pem'])) {
				$pem = $files['ideal_ing_pem'];
				if ($pem['error'] == 0 && $pem['size'] > 0) {
					if (strtolower(JFile::getExt($pem['name'])) == 'pem') {
						JFile::upload($pem['tmp_name'], JPATH_SITE.'/components/com_rseventspro/helpers/ideal/files/ing/priv.pem');
						$data['ideal_ing_pem'] = 1;
					} else {
						JFactory::getApplication()->enqueueMessage(JText::_('COM_RSEVENTSPRO_CONF_PAYMENT_IDEAL_PEM_ERROR'),'error');
					}
				}
			}
			
			// RABOBANK
			if (isset($files['ideal_rabo_cer']) && !empty($files['ideal_rabo_cer'])) {
				$cer = $files['ideal_rabo_cer'];
				if ($cer['error'] == 0 && $cer['size'] > 0) {
					if (strtolower(JFile::getExt($cer['name'])) == 'cer') {
						JFile::upload($cer['tmp_name'], JPATH_SITE.'/components/com_rseventspro/helpers/ideal/files/rabobank/cert.cer');
						$data['ideal_rabo_cer'] = 1;
					} else {
						JFactory::getApplication()->enqueueMessage(JText::_('COM_RSEVENTSPRO_CONF_PAYMENT_IDEAL_CER_ERROR'),'error');
					}
				}
			}
			
			if (isset($files['ideal_rabo_pem']) && !empty($files['ideal_rabo_pem'])) {
				$pem = $files['ideal_rabo_pem'];
				if ($pem['error'] == 0 && $pem['size'] > 0) {
					if (strtolower(JFile::getExt($pem['name'])) == 'pem') {
						JFile::upload($pem['tmp_name'], JPATH_SITE.'/components/com_rseventspro/helpers/ideal/files/rabobank/priv.pem');
						$data['ideal_rabo_pem'] = 1;
					} else {
						JFactory::getApplication()->enqueueMessage(JText::_('COM_RSEVENTSPRO_CONF_PAYMENT_IDEAL_PEM_ERROR'),'error');
					}
				}
			}
		} else {
			$data['ideal_ing_cer'] = 0;
			$data['ideal_ing_pem'] = 0;
			$data['ideal_rabo_cer'] = 0;
			$data['ideal_rabo_pem'] = 0;
		}
		
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
		$token	= JFactory::getApplication()->input->getString('access_token');
		
		if (empty($token)) {
			$this->setError(JText::_('COM_RSEVENTSPRO_FACEBOOK_NO_CONNECTION'));
			return false;
		}
		
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
		
		if (empty($config->facebook_token)) {
			$this->setError(JText::_('COM_RSEVENTSPRO_FACEBOOK_NO_CONNECTION'));
			return false;
		}
		
		require_once JPATH_SITE.'/components/com_rseventspro/helpers/facebook/facebook.php';
		
		$container	= array();
		$facebook	= new RSFacebook(array('appId'  => $config->facebook_appid, 'secret' => $config->facebook_secret, 'cookie' => true));
		$attachment =  array('access_token' => $config->facebook_token,'limit' => 100);
		
		try {
			$user	= $facebook->api('/me', 'GET', $attachment);
			$uid 	= $user['id'];
			$events	= $facebook->api('/me/events', 'GET', $attachment);
			
			if (!empty($events) && !empty($events['data'])) {
				foreach ($events['data'] as $event) {
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
						if ($eobj['owner']['id'] != $uid) {
							continue;
						}
					}
					
					$ev					= new stdClass();
					$ev->id				= @$eobj['id'];
					$ev->name			= @$eobj['name'];
					$ev->description	= @$eobj['description'];
					$ev->start			= isset($eobj['start_time']) ? strtotime($eobj['start_time']) : time();
					$ev->end			= isset($eobj['end_time']) ? strtotime($eobj['end_time']) : time() + 7200;
					$ev->start			= JFactory::getDate($ev->start)->toSql();
					$ev->end			= JFactory::getDate($ev->end)->toSql();
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
				
				if(!empty($indb)) {
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
					
					$file		= $event->image;
					$file		= str_replace(array('http://','/'),array('','\\'),$file);
					$ext		= JFile::getExt($file);
					$filename	= JFile::getName(JFile::stripExt($file));
					
					while(JFile::exists($path.$filename.'.'.$ext))
						$filename .= rand(1,999);
					
					rseventsproHelper::resize($event->image, 0,							$path.$filename.'.'.$ext);
					rseventsproHelper::resize($event->image, $config->icon_big_width,	$path.'thumbs/b_'.$filename.'.'.$ext);
					rseventsproHelper::resize($event->image, $config->icon_small_width,	$path.'thumbs/s_'.$filename.'.'.$ext);
					rseventsproHelper::resize($event->image, 188,						$path.'thumbs/e_'.$filename.'.'.$ext);
					
					$query->clear()
						->insert($db->qn('#__rseventspro_events'))
						->set($db->qn('icon').' = '.$db->q($filename.'.'.$ext))
						->where($db->qn('id').' = '.$db->q($idevent));
					
					$db->setQuery($query);
					$db->execute();
				}
				$i++;
			}
		}
		
		if (!$i) {
			$this->setError(JText::_('COM_RSEVENTSPRO_NO_EVENTS_IMPORTED'));
			return false;
		}
		
		$this->setState($this->getName() . '.fbevents', $i);
		return true;
	}
	
	/**
	 * Method to import Google events.
	 *
	 * @return	boolean		True if success.
	 * @since	1.6
	 */
	public function google() {
		$config		= $this->getConfig();
		$username	= $config->google_username;
		$password	= $config->google_password;
		
		if (empty($username) || empty($password)) {
			$this->setError(JText::_('COM_RSEVENTSPRO_EMPTY_GOOGLE_DETAILS'));
			return false;
		}
		
		require_once JPATH_SITE.'/components/com_rseventspro/helpers/google.php';
		
		$google		= new RSEPROGoogleCalendar($username,$password);
		$response	= $google->parse();
		$errors		= $google->getErrors();
		
		if (!empty($errors)) {
			$this->setError(implode('<br/>',$errors));
			return false;
		}
		
		if (!$response) {
			$this->setError(JText::_('COM_RSEVENTSPRO_NO_EVENTS_IMPORTED'));
			return false;
		}
		
		$this->setState($this->getName() . '.gcevents', $response);
		return true;
	}
}