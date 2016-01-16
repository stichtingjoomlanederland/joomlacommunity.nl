<?php
/**
* @version 1.0.0
* @package RSEvents!Pro 1.0.0
* @copyright (C) 2011 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' ); 

// PLEASE DO NOT CHANGE BELLOW!
require_once JPATH_SITE.'/components/com_rseventspro/helpers/version.php';
$version = new RSEventsProVersion();
define('RSEPRO_RS_PRODUCT','RSEvents!Pro');
define('RSEPRO_RS_VERSION', $version->long);
define('RSEPRO_RS_REVISION',$version->revision);
define('RSEPRO_RS_KEY',$version->key);
define('RSEPRO_RS_COPYRIGHT','&copy;2007-2012 www.rsjoomla.com');
define('RSEPRO_RS_LICENSE','GPL License');
define('RSEPRO_RS_AUTHOR','<a href="http://www.rsjoomla.com" target="_blank">www.rsjoomla.com</a>');

class rseventsproHelper
{
	protected static $groups = null;
	protected static $users = null;
	
	// Generate code for updates
	public static function genKeyCode() {
		if ($code = rseventsproHelper::getConfig('global_code')) {
			return md5($code.RSEPRO_RS_KEY);
		} else return '';
	}
	
	// Check for Joomla! version
	public static function isJ3() {
		return version_compare(JVERSION, '3.0', '>=');
	}
	
	// Get component configuration
	public static function getConfig($name = null, $type = 'none' , $default = null) {
		static $config;
		
		if (!is_object($config)) {
			$db		= JFactory::getDBO();
			$query	= $db->getQuery(true);
			$config	= new stdClass();
			
			$query->clear()->select('*')->from($db->qn('#__rseventspro_config'));
			$db->setQuery($query);
			$configuration = $db->loadObjectList();
			
			if (!empty($configuration)) {
				foreach ($configuration as $c) {
					$config->{$c->name} = $c->value;
				}
			}
		}
		
		if ($name != null) {
			if (isset($config->{$name})) {
				switch($type) {
					default:
					case 'none': return $config->{$name}; break;
					case 'int': return (int) $config->{$name}; break;
					case 'string': return (string) $config->{$name}; break;
					case 'bool': return (bool) $config->{$name}; break;
				}
			} else {
				return is_null($default) ? false : $default;
			}
		} else {
			return $config;
		}
	}
	
	// Load files and scripts
	public static function loadHelper() {
		JHTML::_('behavior.framework');
		
		if (version_compare(PHP_VERSION,'5.3.0','>='))
			error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_STRICT);
		
		// Load the language
		rseventsproHelper::loadLang();
		
		// Load javascript and style scripts
		rseventsproHelper::loadScripts();
		
		// Load the JHTML class
		require_once JPATH_SITE.'/components/com_rseventspro/helpers/html.php';
		
		// Load emails class
		require_once JPATH_SITE.'/components/com_rseventspro/helpers/emails.php';
		
		// Load date class
		require_once JPATH_SITE.'/components/com_rseventspro/helpers/date.php';
		
		// Delete incomplete events
		rseventsproHelper::incomplete();
		
		// Archive events
		rseventsproHelper::archive();
		
		// Payment rules
		rseventsproHelper::rules();
	}
	
	// Load language files
	public static function loadLang() {
		$lang = JFactory::getLanguage();
		$from = JFactory::getApplication()->isAdmin() ? JPATH_ADMINISTRATOR : JPATH_SITE;
		
		$lang->load('com_rseventspro', $from, 'en-GB', true);
		$lang->load('com_rseventspro', $from, $lang->getDefault(), true);
		$lang->load('com_rseventspro', $from, null, true);
	}
	
	// Load scripts
	public static function loadScripts() {
		$doc	= JFactory::getDocument();
		$app	= JFactory::getApplication();
		$view	= $app->input->get('view');
		
		// Load admin or site scripts
		if ($app->isAdmin()) {
			// Add CSS files
			$doc->addStyleSheet(JURI::root(true).'/administrator/components/com_rseventspro/assets/css/style.css?v='.RSEPRO_RS_REVISION);
			if (rseventsproHelper::isJ3()) {
				if (!in_array($view, array('event','events')))
					JHtml::_('formbehavior.chosen', 'select');
				else 
					JHtml::_('formbehavior.chosen', '.rschosen');
				
				$doc->addStyleSheet(JURI::root(true).'/administrator/components/com_rseventspro/assets/css/j3.css?v='.RSEPRO_RS_REVISION);
			}
			else {
				$doc->addStyleSheet(JURI::root(true).'/administrator/components/com_rseventspro/assets/css/j2.css?v='.RSEPRO_RS_REVISION);
			}
			
			// Add JS files
			$doc->addScript(JURI::root(true).'/administrator/components/com_rseventspro/assets/js/scripts.js?v='.RSEPRO_RS_REVISION);
			
			
		} else {
			// Add CSS files
			$doc->addStyleSheet(JURI::root(true).'/components/com_rseventspro/assets/css/style.css?v='.RSEPRO_RS_REVISION);
			
			if (rseventsproHelper::isJ3()) {
				$doc->addStyleSheet(JURI::root(true).'/components/com_rseventspro/assets/css/j3.css?v='.RSEPRO_RS_REVISION);
			} else {
				$doc->addStyleSheet(JURI::root(true).'/components/com_rseventspro/assets/css/j2.css?v='.RSEPRO_RS_REVISION);
			}
			
			// Add JS files
			$doc->addScript(JURI::root(true).'/components/com_rseventspro/assets/js/scripts.js?v='.RSEPRO_RS_REVISION);
		}
	}
	
	// Delete incomplete events
	public static function incomplete() {
		$db		= JFactory::getDBO();
		$query	= $db->getQuery(true);
		$config	= rseventsproHelper::getConfig();
		$now	= new RSDate();
		
		$now->setTZByID($now->getTZID());
		$now->convertTZ(new RSDate_Timezone('GMT'));
		$unix	= $now->getDate(RSDATE_FORMAT_UNIXTIME);
		
		if (!$config->incomplete || $config->incomplete_minutes_check + 60 > $unix) {
			return;
		}
		
		$query->clear()
			->update($db->qn('#__rseventspro_config'))
			->set($db->qn('value').' = '.$db->q($unix))
			->where($db->qn('name').' = '.$db->q('incomplete_minutes_check'));
		
		$db->setQuery($query);
		$db->execute();
		
		$seconds = ((int) $config->incomplete_minutes) * 60;
		
		$query->clear()
				->select($db->qn('id'))
				->from($db->qn('#__rseventspro_events'))
				->where($db->qn('completed').' = '.$db->q(0))
				->where($db->q($now->formatLikeDate('Y-m-d H:i:s')).' > '.$db->qn('created').' + INTERVAL '.(int) $seconds.' SECOND');
		
		$db->setQuery($query);
		if ($events = $db->loadColumn()) {
			foreach($events as $cid) {
				rseventsproHelper::remove($cid);
			}
		}
	}
	
	// Auto-archive events
	public static function archive() {
		$db		= JFactory::getDBO();
		$query	= $db->getQuery(true);
		$config	= rseventsproHelper::getConfig();
		$now	= new RSDate();
		$container = array();
		
		$now->setTZByID($now->getTZID());
		$now->convertTZ(new RSDate_Timezone('GMT'));
		$unix	= $now->getDate(RSDATE_FORMAT_UNIXTIME);
		
		if (!$config->auto_archive || $config->archive_check + 120 > $unix) {
			return false;
		}
		
		$query->clear()
				->update($db->qn('#__rseventspro_config'))
				->set($db->qn('value').' = '.$db->q($unix))
				->where($db->qn('name').' = '.$db->q('archive_check'));
		
		$db->setQuery($query);
		$db->execute();
		
		$seconds = rseventsproHelper::getConfig('archive_days','int') * 86400;
		
		$query->clear()
				->update($db->qn('#__rseventspro_events'))
				->set($db->qn('archived').' = '.$db->q(1))
				->set($db->qn('published').' = '.$db->q(2))
				->where($db->q($now->formatLikeDate('Y-m-d H:i:s')).' > '.$db->qn('end').' + INTERVAL '.(int) $seconds.' SECOND')
				->where($db->qn('end').' <> '.$db->q($db->getNullDate()))
				->where($db->qn('archived').' = '.$db->q(0));
		$db->setQuery($query);
		$db->execute();
		
		$query->clear()
			->select($db->qn('id'))->select($db->qn('start'))
			->from($db->qn('#__rseventspro_events'))
			->where($db->qn('allday').' = 1');
		
		$db->setQuery($query);
		if ($allDayEvents = $db->loadObjectList()) {
			foreach ($allDayEvents as $event) {
				$start	= rseventsproHelper::date($event->start,null,false,true);
				$start->setTZByID($start->getTZID());
				$start->convertTZ(new RSDate_Timezone('GMT'));
				$start	= $start->getDate(RSDATE_FORMAT_UNIXTIME);
				
				if ($unix > $start + $seconds)
					$container[] = $event->id;
			}
		}
		
		if (!empty($container)) {
			JArrayHelper::toInteger($container);
			
			$query->clear()
				->update($db->qn('#__rseventspro_events'))
				->set($db->qn('archived').' = '.$db->q(1))
				->set($db->qn('published').' = '.$db->q(2))
				->where($db->qn('id').' IN ('.implode(',',$container).')')
				->where($db->qn('archived').' = '.$db->q(0));
			
			$db->setQuery($query);
			$db->execute();
		}
		
		return true;
	}
	
	// Payment rules
	public static function rules() {
		$db		= JFactory::getDBO();
		$query	= $db->getQuery(true);
		$config	= rseventsproHelper::getConfig();
		$now	= JFactory::getDate();
		$unix	= $now->toUnix();
		
		if ($config->rules_check + 300 > $unix) {
			return;
		}
		
		$query->clear()
			->update($db->qn('#__rseventspro_config'))
			->set($db->qn('value').' = '.$db->q($unix))
			->where($db->qn('name').' = '.$db->q('rules_check'));
		
		$db->setQuery($query);
		$db->execute();
		
		// Get rules
		$query->clear()
			->select('*')
			->from($db->qn('#__rseventspro_rules'))
			->order($db->qn('payment').' DESC')
			->order($db->qn('interval').' ASC');
		
		$db->setQuery($query);
		$rules = $db->loadObjectList();
		
		if (empty($rules)) 
			return;
		
		foreach ($rules as $rule) {
			$interval = (int) $rule->interval;
			
			$query->clear()
				->select($db->qn('id'))
				->from($db->qn('#__rseventspro_users'))
				->where($db->qn('state').' = '.(int) $rule->status)
				->where($db->qn('gateway').' = '.$db->q($rule->payment))
				->where($db->q($now->toSql()).' > DATE_ADD('.$db->qn('date').', INTERVAL '.$interval.' HOUR)');
			
			$db->setQuery($query);
			$subscribers = $db->loadColumn();
			
			if (empty($subscribers)) 
				continue;
			
			foreach ($subscribers as $subscriber) {
				// Approve user
				if ($rule->rule == 1)
					rseventsproHelper::confirm($subscriber);
				// Deny user
				else if ($rule->rule == 2)
					rseventsproHelper::denied($subscriber);
				// Delete user
				else if ($rule->rule == 3) {
					$query->clear()
						->delete()
						->from($db->qn('#__rseventspro_user_tickets'))
						->where($db->qn('ids'). ' = '.(int) $subscriber);
					
					$db->setQuery($query);
					$db->execute();
					
					$query->clear()
						->delete()
						->from($db->qn('#__rseventspro_users'))
						->where($db->qn('id'). ' = '.(int) $subscriber);
					
					$db->setQuery($query);
					$db->execute();
				}
				// Send reminder to user
				else if ($rule->rule == 4) {
					$query->clear()
						->select('COUNT(id)')
						->from($db->qn('#__rseventspro_taxonomy'))
						->where($db->qn('type').' = '.$db->q('rule'))
						->where($db->qn('ide').' = '.$db->q($rule->id))
						->where($db->qn('id').' = '.(int) $subscriber);
					
					$db->setQuery($query);
					if (!$db->loadResult()) {
						rseventsproEmails::rule($subscriber,$rule->mid);
						
						$query->clear()
							->insert($db->qn('#__rseventspro_taxonomy'))
							->set($db->qn('type').' = '.$db->q('rule'))
							->set($db->qn('ide').' = '.$db->q($rule->id))
							->set($db->qn('id').' = '.(int) $subscriber);
						
						$db->setQuery($query);
						$db->execute();
					}
				}
			}
		}
	}
	
	// Create the backend submenu
	public static function subMenu() {
		$jinput = JFactory::getApplication()->input;
		$view   = $jinput->getCmd('view');
		$layout = $jinput->getCmd('layout');
		$views  = array('events','locations','categories','tags','subscriptions','payments','groups','imports','settings','update');
		
		JHtmlSidebar::addEntry(JText::_('COM_RSEVENTSPRO_SUBMENU_DASHBOARD'), 'index.php?option=com_rseventspro',(empty($view) && empty($layout)));
		
		foreach ($views as $theview) {
			if ($theview == 'categories') {
				JHtmlSidebar::addEntry(JText::_('COM_RSEVENTSPRO_SUBMENU_'.strtoupper($theview)), 'index.php?option=com_categories&extension=com_rseventspro');
			} else {
				if ($theview == 'update') {
					JHtmlSidebar::addEntry(JText::_('COM_RSEVENTSPRO_SUBMENU_'.strtoupper($theview)), 'index.php?option=com_rseventspro&layout='.strtolower($theview), ($theview == $layout) );
				} else {
					JHtmlSidebar::addEntry(JText::_('COM_RSEVENTSPRO_SUBMENU_'.strtoupper($theview)), 'index.php?option=com_rseventspro&view='.strtolower($theview), ($theview == $view) );
				}
			}
		}
	}
	
	// Check for paypal plugin
	public static function paypal() {
		$db		= JFactory::getDBO();
		$query	= $db->getQuery(true);
		
		$query->clear()
			->select($db->qn('enabled'))
			->from($db->qn('#__extensions'))
			->where($db->qn('type').' = '.$db->q('plugin'))
			->where($db->qn('folder').' = '.$db->q('system'))
			->where($db->qn('element').' = '.$db->q('rsepropaypal'));
			
		$db->setQuery($query);
		$enabled = $db->loadResult();
		
		if ($enabled && file_exists(JPATH_SITE.'/plugins/system/rsepropaypal/rsepropaypal.php'))
			return true;
		
		return false;
	}
	
	// Check for pdf plugin
	public static function pdf() {
		$db		= JFactory::getDBO();
		$query	= $db->getQuery(true);
		
		$query->clear()
			->select($db->qn('enabled'))
			->from($db->qn('#__extensions'))
			->where($db->qn('type').' = '.$db->q('plugin'))
			->where($db->qn('folder').' = '.$db->q('system'))
			->where($db->qn('element').' = '.$db->q('rsepropdf'));
			
		$db->setQuery($query);
		$enabled = $db->loadResult();
		
		if ($enabled && file_exists(JPATH_SITE.'/plugins/system/rsepropdf/rsepropdf.php'))
			return true;
		
		return false;
	}
	
	// Check for the iDeal plugin
	public static function ideal() {
		$db		= JFactory::getDBO();
		$query	= $db->getQuery(true);
		
		$query->clear()
			->select($db->qn('enabled'))
			->from($db->qn('#__extensions'))
			->where($db->qn('type').' = '.$db->q('plugin'))
			->where($db->qn('folder').' = '.$db->q('system'))
			->where($db->qn('element').' = '.$db->q('rseproideal'));
			
		$db->setQuery($query);
		$enabled = $db->loadResult();
		
		if ($enabled && file_exists(JPATH_SITE.'/plugins/system/rseproideal/rseproideal.php'))
			return true;
		
		return false;
	}
	
	// Check for RSMediaGallery!
	public static function isGallery() {
		if (file_exists(JPATH_ADMINISTRATOR.'/components/com_rsmediagallery/helpers/integration.php'))
			return true;
		
		return false;
	}
	
	// Get available payments
	public static function getPayments($addNone = false, $available = null) {
		$db		= JFactory::getDBO();
		$query	= $db->getQuery(true);
		$app	= JFactory::getApplication();
		
		$return = array();
		
		if ($addNone)
			$return[] = JHTML::_('select.option', 'none', JText::_( 'COM_RSEVENTSPRO_CONF_DEFAULT_PAYMENT_NONE' ) );				
		
		if (!is_null($available) && !empty($available)) {
			$registry = new JRegistry;
			$registry->loadString($available);
			$available = $registry->toArray();
		} else $available = '';
		
		//payment plugins
		$payment_methods = $app->triggerEvent('rsepro_addOptions');
		if (!empty($payment_methods)) {
			foreach ($payment_methods as $i => $item) {
				if (empty($item->value)) continue;
				if (!empty($available) && !in_array($item->value,$available)) continue;
				$return[] = $item;
			}
		}
		
		//database payments
		$query->clear()
			->select($db->qn('id','value'))
			->select($db->qn('name','text'))
			->from($db->qn('#__rseventspro_payments'))
			->where($db->qn('published').' = '.$db->q(1));
			
		$db->setQuery($query);
		$db_payments = $db->loadObjectList();
		
		if (!empty($db_payments)) {
			foreach ($db_payments as $payment) {
				if (!empty($available) && !in_array($payment->value,$available)) continue;
				$return[] = $payment;
			}
		}
		
		return $return;
	}
	
	// Get Rules
	public static function getRules($selected = null) {
		$rules =  array( 
			JHTML::_('select.option', 1, JText::_('COM_RSEVENTSPRO_RULE_APPROVE_SUBSCRIBER')),
			JHTML::_('select.option', 2, JText::_('COM_RSEVENTSPRO_RULE_DENY_SUBSCRIBER')),
			JHTML::_('select.option', 3, JText::_('COM_RSEVENTSPRO_RULE_DELETE_SUBSCRIBER')),
			JHTML::_('select.option', 4, JText::_('COM_RSEVENTSPRO_RULE_EMAIL_SUBSCRIBER'))
		);
		
		if (!is_null($selected)) {
			foreach ($rules as $rule)
				if ($rule->value == $selected)
					return $rule->text;
		}
		
		return $rules;
	}
	
	// Get statuses
	public static function getStatuses($selected = null) {
		$statuses = array( 
			JHTML::_('select.option', 0, JText::_('COM_RSEVENTSPRO_RULE_STATUS_INCOMPLETE')),
			JHTML::_('select.option', 1, JText::_('COM_RSEVENTSPRO_RULE_STATUS_COMPLETE')),
			JHTML::_('select.option', 2, JText::_('COM_RSEVENTSPRO_RULE_STATUS_DENIED'))
		);
		
		if (!is_null($selected)) {
			foreach ($statuses as $status)
				if ($status->value == $selected)
					return $status->text;
		}
		
		return $statuses;
	}
	
	// Get the list of all locations
	public static function getLocations() {
		$db			= JFactory::getDBO();
		$query		= $db->getQuery(true);
		
		$query->clear()
			->select($db->qn('id','value'))->select($db->qn('name','text'))
			->from($db->qn('#__rseventspro_locations'))
			->where($db->qn('published').' = '.$db->q(1))
			->order($db->qn('name').' ASC');
		
		$db->setQuery($query);
		return $db->loadObjectList();
	}
	
	// Get the list of all tags
	public static function getTags() {
		$db			= JFactory::getDBO();
		$query		= $db->getQuery(true);
		
		$query->clear()
			->select($db->qn('id','value'))->select($db->qn('name','text'))
			->from($db->qn('#__rseventspro_tags'))
			->where($db->qn('published').' = '.$db->q(1))
			->order($db->qn('name').' ASC');
		
		$db->setQuery($query);
		return $db->loadObjectList();
	}
	
	// Get the list of all events
	public static function getEvents($date = false) {
		$db			= JFactory::getDBO();
		$query		= $db->getQuery(true);
		
		$query->clear()
			->select($db->qn('id','value'))->select($db->qn('name','text'))->select('start')
			->from($db->qn('#__rseventspro_events'))
			->where($db->qn('published').' = '.$db->q(1))
			->order($db->qn('start').' ASC');
		
		$db->setQuery($query);
		$events =  $db->loadObjectList();
		
		if ($date) {
			foreach ($events as $i => $event) {
				$events[$i]->text = $event->text .' ('.rseventsproHelper::date($event->start).')';
			}
		}
		
		return $events;
	}
	
	// Resize images
	public static function resize($image, $width = 150, $path) {
		if (empty($image)) {
			return false;
		}
		
		jimport('joomla.filesystem.file');
		require_once JPATH_SITE.'/components/com_rseventspro/helpers/phpthumb/phpthumb.class.php';
		
		$thumb = new phpThumb();
		$thumb->src = $image;
		if (!empty($width)) 
			$thumb->w = (int) $width;
		$thumb->q = 75;
		$thumb->config_output_format = JFile::getExt($image);
		$thumb->config_error_die_on_error = true;
		$thumb->config_cache_disable_warning = true;
		$thumb->config_allow_src_above_docroot = true;
		$thumb->cache_filename = $path;
		
		if ($thumb->GenerateThumbnail()) {
			$thumb->RenderToFile($thumb->cache_filename);
			return true;
		}
		
		return false;
	}
	
	// Crop images
	public static function crop($image, $width = 150, $path) {
		if (empty($image)) {
			return false;
		}
		
		jimport('joomla.filesystem.file');
		require_once JPATH_SITE.'/components/com_rseventspro/helpers/phpthumb/phpthumb.class.php';
		$jinput = JFactory::getApplication()->input;
		
		$thumb = new phpThumb();
		$thumb->src = $image;
		$thumb->w = $width;
		$thumb->iar = 1;
		
		$thumb->sx = round($jinput->getInt('left'));
		$thumb->sy = round($jinput->getInt('top'));
		$thumb->sw = round($jinput->getInt('width'));
		$thumb->sh = round($jinput->getInt('height'));
		$thumb->zc = 0;
		$thumb->cache_filename = $path;
		
		if ($thumb->GenerateThumbnail()) {
			$thumb->RenderToFile($thumb->cache_filename);
			return true;
		}
		
		return false;
	}
	
	// Close the modal
	public static function modalClose($script = true) {
		$html = array();
		
		if ($script) $html[] = '<script type="text/javascript">';
		$html[] = 'window.parent.SqueezeBox.close();';
		if ($script) $html[] = '</script>';
		
		return implode("\n",$html);
	}
	
	// Load Chosen library
	public static function chosen() {
		if (rseventsproHelper::isJ3())
			return false;
		
		$doc = JFactory::getDocument();
		$doc->addScript(JURI::root().'components/com_rseventspro/assets/js/chosen/mootools-more-1.4.0.1.js?v='.RSEPRO_RS_REVISION);
		$doc->addScript(JURI::root().'components/com_rseventspro/assets/js/chosen/chosen.js?v='.RSEPRO_RS_REVISION);
		$doc->addStyleSheet(JURI::root().'components/com_rseventspro/assets/js/chosen/chosen.css?v='.RSEPRO_RS_REVISION);
		
		JText::script('COM_RSEVENTSPRO_SELECT_SOME_OPTIONS');
		JText::script('COM_RSEVENTSPRO_SELECT_AN_OPTION');
		JText::script('COM_RSEVENTSPRO_SELECT_NO_RESULTS_MATCH');
		
		return true;
	}
	
	// Get RSMediaGallery! tags
	public static function getGalleryTags() {
		if (!rseventsproHelper::isGallery()) { 
			return array();
		}
		
		$db		= JFactory::getDBO();
		$query	= $db->getQuery(true);
		
		$query->clear();
		$query->select('DISTINCT('.$db->qn('tag').')')
			->from($db->qn('#__rsmediagallery_tags'))
			->order($db->qn('tag').' ASC');
		
		$db->setQuery($query);
		if ($tags = $db->loadColumn()) {
			foreach ($tags as $tag)
				$return[] = JHTML::_('select.option', $tag, $tag);
			return $return;
		}
		return array();
	}
	
	// Get filter events
	public static function getFilterEvents($date = true, $registration = false, $ordering = 'ASC') {
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);
		$return = array();
		
		$query->clear()
			->select($db->qn('id'))->select($db->qn('name'))->select($db->qn('start'))
			->from($db->qn('#__rseventspro_events'))
			->order($db->qn('start').' '.$db->escape($ordering));
		
		if ($registration)
			$query->where($db->qn('registration').' = 1');
		
		$db->setQuery($query);
		if ($events = $db->loadObjectList()) {
			foreach ($events as $event) {
				if ($date) {
					$event->name .= ' ('.rseventsproHelper::date($event->start).')';
				}
				
				$return[] = JHtml::_('select.option', $event->id, $event->name);
			}
		}
		
		return $return;
	}
	
	// Get date	
	public static function date($input = 'now', $format = null, $replace = false, $object = false) {
		require_once JPATH_SITE.'/components/com_rseventspro/helpers/date.php';
		
		$app		= JFactory::getApplication();
		$config		= rseventsproHelper::getConfig();
		$tz			= JFactory::getConfig()->get('offset');
		$input		= is_null($input) || $input == 'now' ? gmdate('c') : $input;
		$date		= new RSDate($input);
		$format		= is_null($format) ? $config->global_date. ' '.$config->global_time : $format;
		
		if ($config->hideyear && !$app->isAdmin() && $replace) {
			if ((is_int($input) && date('Y') == date('Y',$input)) || date('Y') == date('Y',strtotime($input)))
				$format = str_replace(array('Y','y','o'),'',$format);
		}
		
		$date->setTZByID('GMT');
		$date->convertTZByID($tz);
		
		return $object ? $date : rseventsproHelper::translatedate($date->formatLikeDate($format));
	}
	
	// Get current timezone
	public static function getTimezone() {
		return JFactory::getConfig()->get('offset');
	}
	
	// Translate the date
	public static function translatedate($date) {
		$lang = JFactory::getLanguage()->load('com_rseventspro.dates',JPATH_SITE);
		
		$replace = array('Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday',
						 'January','February','March','April','May','June','July','August','September','October','November','December',
						 'Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec',
						 'Mon','Tue','Wed','Thu','Fri','Sat','Sun');
		
		$with	 = array(JText::_('COM_RSEVENTSPRO_MONDAY'),JText::_('COM_RSEVENTSPRO_TUESDAY'),JText::_('COM_RSEVENTSPRO_WEDNESDAY'),JText::_('COM_RSEVENTSPRO_THURSDAY'),JText::_('COM_RSEVENTSPRO_FRIDAY'),JText::_('COM_RSEVENTSPRO_SATURDAY'),JText::_('COM_RSEVENTSPRO_SUNDAY'),	JText::_('COM_RSEVENTSPRO_JANUARY'),JText::_('COM_RSEVENTSPRO_FEBRUARY'),JText::_('COM_RSEVENTSPRO_MARCH'),JText::_('COM_RSEVENTSPRO_APRIL'),JText::_('COM_RSEVENTSPRO_MAY'),JText::_('COM_RSEVENTSPRO_JUNE'),JText::_('COM_RSEVENTSPRO_JULY'),JText::_('COM_RSEVENTSPRO_AUGUST'),JText::_('COM_RSEVENTSPRO_SEPTEMBER'),JText::_('COM_RSEVENTSPRO_OCTOBER'),JText::_('COM_RSEVENTSPRO_NOVEMBER'),JText::_('COM_RSEVENTSPRO_DECEMBER'), JText::_('COM_RSEVENTSPRO_JANUARY_SHORT'),JText::_('COM_RSEVENTSPRO_FEBRUARY_SHORT'),JText::_('COM_RSEVENTSPRO_MARCH_SHORT'),JText::_('COM_RSEVENTSPRO_APRIL_SHORT'),JText::_('COM_RSEVENTSPRO_MAY_SHORT'),JText::_('COM_RSEVENTSPRO_JUNE_SHORT'),JText::_('COM_RSEVENTSPRO_JULY_SHORT'),JText::_('COM_RSEVENTSPRO_AUGUST_SHORT'),JText::_('COM_RSEVENTSPRO_SEPTEMBER_SHORT'),JText::_('COM_RSEVENTSPRO_OCTOBER_SHORT'),JText::_('COM_RSEVENTSPRO_NOVEMBER_SHORT'),JText::_('COM_RSEVENTSPRO_DECEMBER_SHORT'), JText::_('COM_RSEVENTSPRO_MONDAY_SHORT'),JText::_('COM_RSEVENTSPRO_TUESDAY_SHORT'),JText::_('COM_RSEVENTSPRO_WEDNESDAY_SHORT'),JText::_('COM_RSEVENTSPRO_THURSDAY_SHORT'),JText::_('COM_RSEVENTSPRO_FRIDAY_SHORT'),JText::_('COM_RSEVENTSPRO_SATURDAY_SHORT'),JText::_('COM_RSEVENTSPRO_SUNDAY_SHORT'));
		
		return str_replace($replace, $with, $date);
	}
	
	// Get user tickets
	public static function getUserTickets($id, $print = false) {
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		
		$query->clear()
			->select($db->qn('ut.quantity'))->select($db->qn('t.id'))
			->select($db->qn('t.name'))->select($db->qn('t.price'))
			->from($db->qn('#__rseventspro_user_tickets','ut'))
			->join('left', $db->qn('#__rseventspro_tickets','t').' ON '.$db->qn('t.id').' = '.$db->qn('ut.idt'))
			->where($db->qn('ut.ids').' = '. (int) $id);

		$db->setQuery($query);
		$tickets = $db->loadObjectList();
		
		// Print result
		if ($print) {
			$html = array();
			if (!empty($tickets)) {
				foreach ($tickets as $ticket) {
					if ($ticket->price > 0) {
						$html[] = $ticket->quantity. ' x '.$ticket->name.' ('.rseventsproHelper::currency($ticket->price).') <br />';
					} else {
						$html[] = $ticket->quantity. ' x '.$ticket->name.' ('.JText::_('COM_RSEVENTSPRO_GLOBAL_FREE').') <br />';
					}
				}
			}
			
			return implode("\n",$html);
		}
		
		return $tickets;
	}
	
	// Convert price
	public static function convertprice($price) {		
		return number_format($price, rseventsproHelper::getConfig('payment_decimals','int'), rseventsproHelper::getConfig('payment_decimal'), rseventsproHelper::getConfig('payment_thousands')); 
	}
	
	// Show formated price
	public static function currency($price) {
		// Get the payment mask
		$mask = rseventsproHelper::getConfig('payment_mask');		
		$mask = empty($mask) ? '%p %c' : $mask;
		
		// Get the currency
		$currency = rseventsproHelper::getConfig('payment_currency_sign');
		$currency = empty($currency) ? rseventsproHelper::getConfig('payment_currency') : $currency;
		
		// Convert price
		$price = rseventsproHelper::convertprice($price);
		
		// Return the payment
		return str_replace(array('%p','%c'),array($price,$currency),$mask);
	}
	
	// Get payment name
	public static function getPayment($pid) {
        $app = JFactory::getApplication();
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		
		$query->clear()
			->select($db->qn('name'))
			->from($db->qn('#__rseventspro_payments'))
			->where($db->qn('id').' = '.(int) $pid);
		
        $db->setQuery($query);
        $name =  $db->loadResult();
       
        if (!empty($name)) {
            return $name;
		}
       
        $gateway = $app->triggerEvent('rsepro_name',array(array('gateway' => $pid)));
       
        if (!empty($gateway) && isset($gateway[0])) {
            return $gateway[0];
		}
       
        return $pid == 'twoco' ? '2Checkout' : ucfirst($pid);
	}
	
	// Send activation email
	public static function confirm($ids, $registration = false) {
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);
		$total	= 0;
		$info	= '';
		
		// Load language
		JFactory::getLanguage()->load('com_rseventspro',JPATH_SITE);
		
		// Set the confirm status
		$query->clear()
			->update($db->qn('#__rseventspro_users'))
			->set($db->qn('state').' = 1')
			->where($db->qn('id').' = '.(int) $ids);
		
		$db->setQuery($query);
		$db->execute();
		
		// Get subscription details
		$query->clear()
			->select($db->qn('ide'))->select($db->qn('name'))->select($db->qn('email'))
			->select($db->qn('discount'))->select($db->qn('early_fee'))->select($db->qn('late_fee'))
			->select($db->qn('tax'))->select($db->qn('gateway'))->select($db->qn('ip'))->select($db->qn('coupon'))
			->from($db->qn('#__rseventspro_users'))
			->where($db->qn('id').' = '.(int) $ids);
		
		$db->setQuery($query);
		$subscription = $db->loadObject();
		
		// Get tickets
		$tickets = rseventsproHelper::getUserTickets($ids);
		
		if (!empty($tickets)) {
			foreach ($tickets as $ticket) {
				// Calculate the total
				if ($ticket->price > 0) {
					$price = $ticket->price * $ticket->quantity;
					$total += $price;
					$info .= $ticket->quantity . ' x ' .$ticket->name.' ('.rseventsproHelper::currency($ticket->price).') '.rseventsproHelper::getSeats($ids,$ticket->id).' <br />';
				} else {
					$info .= $ticket->quantity . ' x ' .$ticket->name.' ('.JText::_('COM_RSEVENTSPRO_GLOBAL_FREE').') <br />';
				}
			}
		}
		
		if (!empty($subscription->discount) && !empty($total)) {
			$total = $total - $subscription->discount;
		}
		
		if (!empty($subscription->early_fee) && !empty($total)) {
			$total = $total - $subscription->early_fee;
		}
		
		if (!empty($subscription->late_fee) && !empty($total)) {
			$total = $total + $subscription->late_fee;
		}
		
		if (!empty($subscription->tax) && !empty($total)) {
			$total = $total + $subscription->tax;
		}
		
		$ticketstotal		= rseventsproHelper::currency($total);
		$ticketsdiscount	= !empty($subscription->discount) ? rseventsproHelper::currency($subscription->discount) : '';
		$subscriptionTax	= !empty($subscription->tax) ? rseventsproHelper::currency($subscription->tax) : '';
		$lateFee			= !empty($subscription->late_fee) ? rseventsproHelper::currency($subscription->late_fee) : '';
		$earlyDiscount		= !empty($subscription->early_fee) ? rseventsproHelper::currency($subscription->early_fee) : '';
		$gateway			= rseventsproHelper::getPayment($subscription->gateway);
		$IP					= $subscription->ip;
		$coupon				= !empty($subscription->coupon) ? $subscription->coupon : '';
		$optionals			= array($info, $ticketstotal, $ticketsdiscount, $subscriptionTax, $lateFee, $earlyDiscount, $gateway, $IP, $coupon);
		
		if ($registration) {
			rseventsproEmails::registration($subscription->email, $subscription->ide, $subscription->name, $optionals);
		} else {
			rseventsproEmails::activation($subscription->email, $subscription->ide, $subscription->name, $optionals);
		}
		
		return true;
	}
	
	// Send denied email
	public static function denied($ids) {
		$db	= JFactory::getDbo();
		$query = $db->getQuery(true);
		
		// Load language
		JFactory::getLanguage()->load('com_rseventspro',JPATH_SITE);
		
		// Set the denied status
		$query->clear()
			->update($db->qn('#__rseventspro_users'))
			->set($db->qn('state').' = 2')
			->where($db->qn('id').' = '.(int) $ids);
			
		$db->setQuery($query);
		$db->execute();
		
		// Get subscription details
		$query->clear()
			->select($db->qn('ide'))->select($db->qn('name'))->select($db->qn('email'))
			->from($db->qn('#__rseventspro_users'))
			->where($db->qn('id').' = '.(int) $ids);
			
		$db->setQuery($query);
		$subscription = $db->loadObject();
		
		rseventsproEmails::denied($subscription->email, $subscription->ide, $subscription->name);
		return true;
	}
	
	// Export subscribers
	public static function exportSubscribersCSV($squery = null) {
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);
		$id		= JFactory::getApplication()->input->getInt('id', 0);
		$csv	= '';
		
		if (!$id)
			return;
		
		if (!$squery) {
			$query->clear()
				->select($db->qn('id'))->select($db->qn('name'))->select($db->qn('email'))->select($db->qn('date'))->select($db->qn('state'))
				->select($db->qn('gateway'))->select($db->qn('ip'))->select($db->qn('SubmissionId'))->select($db->qn('discount'))->select($db->qn('early_fee'))
				->select($db->qn('late_fee'))->select($db->qn('tax'))
				->from($db->qn('#__rseventspro_users'))
				->where($db->qn('ide').' = '.$id)
				->where($db->qn('state').' = 1');
			$db->setQuery($query);
		} else {
			$db->setQuery($squery);
		}
		
		$subscribers = $db->loadObjectList();
		
		if (rseventsproHelper::getConfig('export_headers')) {
			$csv .= '"'.JText::_('COM_RSEVENTSPRO_SUBSCRIBER_ID').'",';
			$csv .= '"'.JText::_('COM_RSEVENTSPRO_SUBSCRIBER_NAME').'",';
			$csv .= '"'.JText::_('COM_RSEVENTSPRO_SUBSCRIBER_EMAIL').'",';
			$csv .= '"'.JText::_('COM_RSEVENTSPRO_SUBSCRIBER_EXPORT_HEADER_DATE').'",';
			$csv .= '"'.JText::_('COM_RSEVENTSPRO_SUBSCRIBER_IP').'",';
			$csv .= '"'.JText::_('COM_RSEVENTSPRO_SUBSCRIBER_STATE').'",';
			$csv .= '"'.JText::_('COM_RSEVENTSPRO_SUBSCRIBER_PAYMENT').'",';
			$csv .= '"'.JText::_('COM_RSEVENTSPRO_SUBSCRIBER_EXPORT_HEADER_TICKETS').'",';
			$csv .= '"'.JText::_('COM_RSEVENTSPRO_SUBSCRIBER_EXPORT_HEADER_TOTAL').'"';
			
			if (file_exists(JPATH_SITE.'/components/com_rsform/rsform.php')) {
				$query->clear()
					->select($db->qn('form'))
					->from($db->qn('#__rseventspro_events'))
					->where($db->qn('id').' = '.$id);
				
				$db->setQuery($query);
				if ($formId = (int) $db->loadResult()) {
					$headers = rseventsproHelper::getHeaders($formId);
					$csv .= ','.'"'.implode('","',$headers).'"';
				}
			}
			
			$csv .= "\n";
		}
		
		
		if (!empty($subscribers)) {
			foreach ($subscribers as $subscriber) {
				$total				= 0;
				$purchasedtickets	= array();
				$tickets			= rseventsproHelper::getUserTickets($subscriber->id);
				
				if (!empty($tickets)) { 
					foreach ($tickets as $ticket) {
						if ($ticket->price > 0) {
							$purchasedtickets[] = $ticket->quantity. ' x '.$ticket->name . ' (' . rseventsproHelper::currency($ticket->price).')';
							$total += (int) $ticket->quantity * $ticket->price;
						} else {
							$purchasedtickets[] = $ticket->quantity. ' x '.$ticket->name . ' (' . JText::_('COM_RSEVENTSPRO_GLOBAL_FREE').')';
						}
					}
				}
				
				if ($subscriber->discount) {
					$total = $total - $subscriber->discount;
				}
				if ($subscriber->early_fee) {
					$total = $total - $subscriber->early_fee;
				}
				if ($subscriber->late_fee) {
					$total = $total + $subscriber->late_fee;
				}
				if ($subscriber->tax) {
					$total = $total + $subscriber->tax;
				}
				
				$stickets = !empty($purchasedtickets) ? implode('<br />',$purchasedtickets) : '';
				
				$csv .= '"'.$db->escape('RST-'.$subscriber->id).'",';
				$csv .= '"'.$db->escape($subscriber->name).'",';
				$csv .= '"'.$db->escape($subscriber->email).'",';
				$csv .= '"'.$db->escape(rseventsproHelper::date($subscriber->date,'Y-m-d H:i:s')).'",';
				$csv .= '"'.$db->escape($subscriber->ip).'",';
				$csv .= '"'.$db->escape(rseventsproHelper::getStatuses($subscriber->state)).'",';
				$csv .= '"'.$db->escape(rseventsproHelper::getPayment($subscriber->gateway)).'",';
				$csv .= '"'.$db->escape($stickets).'",';
				$csv .= '"'.$db->escape(rseventsproHelper::currency($total)).'"';
				
				if ($subscriber->SubmissionId) {
					if ($submissions = rseventsproHelper::getSubmission($subscriber->SubmissionId)) {
						$csv .= ','.'"'.implode('","',$submissions).'"';
					}
				}
				
				$csv .= "\n";
			}
		}
		
		$file = 'Event'.$id.'.csv';
		header("Content-type: text/csv; charset=UTF-8");
		header("Content-Disposition: attachment; filename=$file");
		echo rtrim($csv,"\n");
		JFactory::getApplication()->close();
	}
	
	// Get RSForm!Pro submissions
	public static function getSubmission($id) {
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);
		$secret = JFactory::getConfig()->get('secret');
		$data	= array();
		
		if (!file_exists(JPATH_SITE.'/components/com_rsform/rsform.php'))
			return false;
		
		$query->clear()
			->select($db->qn('FormId'))
			->from($db->qn('#__rsform_submissions'))
			->where($db->qn('SubmissionId').' = '.(int) $id);
		
		$db->setQuery($query);
		$formId = $db->loadResult();
		
		if (!$formId)
			return false;
		
		$query->clear()
			->select($db->qn('MultipleSeparator'))->select($db->qn('TextareaNewLines'))
			->from($db->qn('#__rsform_forms'))
			->where($db->qn('FormId').' = '.(int) $formId);
		
		$db->setQuery($query);
		$form = $db->loadObject();
		
		if (empty($form))
			return false;
		
		$form->MultipleSeparator = str_replace(array('\n', '\r', '\t'), array("\n", "\r", "\t"), $form->MultipleSeparator);
		
		$query->clear()
			->select($db->qn('c.ComponentTypeId'))->select($db->qn('p.ComponentId'))
			->select($db->qn('p.PropertyName'))->select($db->qn('p.PropertyValue'))
			->from($db->qn('#__rsform_components','c'))
			->join('left',$db->qn('#__rsform_properties','p').' ON '.$db->qn('c.ComponentId').' = '.$db->qn('p.ComponentId'))
			->where($db->qn('c.FormId').' = '.(int) $formId)
			->where($db->qn('c.Published').' = 1')
			->where($db->qn('p.PropertyName').' IN ('.$db->q('NAME').','.$db->q('WYSIWYG').')');
		
		$db->setQuery($query);
		$components = $db->loadObjectList();			
		$uploadFields 	= array();
		$multipleFields = array();
		$textareaFields = array();
		
		foreach ($components as $component) {
			// Upload fields
			if ($component->ComponentTypeId == 9) {
				$uploadFields[] = $component->PropertyValue;
			}
			// Multiple fields
			elseif (in_array($component->ComponentTypeId, array(3, 4))) {
				$multipleFields[] = $component->PropertyValue;
			}
			// Textarea fields
			elseif ($component->ComponentTypeId == 2) {
				if ($component->PropertyName == 'WYSIWYG' && $component->PropertyValue == 'NO')
					$textareaFields[] = $component->ComponentId;
			}
		}
		
		if (!empty($textareaFields)) {
			$query->clear()
				->select($db->qn('p.PropertyValue'))
				->from($db->qn('#__rsform_components','c'))
				->join('left',$db->qn('#__rsform_properties','p').' ON '.$db->qn('c.ComponentId').' = '.$db->qn('p.ComponentId'))
				->where($db->qn('c.ComponentId').' IN ('.implode(',',$textareaFields).')');
			
			$db->setQuery($query);
			$textareaFields = $db->loadColumn();
		}
		
		$query->clear()
			->select($db->qn('p.PropertyValue'))
			->from($db->qn('#__rsform_components','c'))
			->join('left',$db->qn('#__rsform_properties','p').' ON ('.$db->qn('c.ComponentId').' = '.$db->qn('p.ComponentId').' AND '.$db->qn('p.PropertyName').' = '.$db->q('NAME').' )')
			->join('left',$db->qn('#__rsform_component_types','ct').' ON '.$db->qn('c.ComponentTypeId').' = '.$db->qn('ct.ComponentTypeId'))
			->where($db->qn('c.FormId').' = '.(int) $formId)
			->where($db->qn('c.Published').' = 1')
			->where($db->qn('ct.ComponentTypeName').' NOT IN ('.$db->q('button').','.$db->q('captcha').','.$db->q('freeText').','.$db->q('imageButton').','.$db->q('submitButton').')')
			->where($db->qn('p.PropertyValue').' NOT IN ('.$db->q('RSEProName').','.$db->q('RSEProEmail').','.$db->q('RSEProTickets').','.$db->q('RSEProPayment').','.$db->q('RSEProCoupon').')')
			->order($db->qn('c.Order'));
		
		$db->setQuery($query);
		$headers = $db->loadColumn();
		ksort($headers);
		
		$query->clear()
			->select('*')
			->from($db->qn('#__rsform_submission_values'))
			->where($db->qn('SubmissionId').' = '.(int) $id);
		
		$db->setQuery($query);
		$results = $db->loadObjectList();
		
		if (!empty($results)) {
			$values = array();
			foreach ($results as $result) {
				if (in_array($result->FieldName,array('RSEProName','RSEProEmail','RSEProTickets','RSEProPayment','RSEProCoupon','formId'))) 
					continue;
				
				// Check if this is an upload field
				if (in_array($result->FieldName, $uploadFields) && !empty($result->FieldValue)) {
					$result->FieldValue = JURI::root().'index.php?option=com_rsform&amp;task=submissions.view.file&amp;hash='.md5($id.$secret.$result->FieldName);
				} else {
					// Check if this is a multiple field
					if (in_array($result->FieldName, $multipleFields))
						$result->FieldValue = str_replace("\n", $form->MultipleSeparator, $result->FieldValue);
					// Transform new lines
					elseif ($form->TextareaNewLines && in_array($result->FieldName, $textareaFields))
						$result->FieldValue = nl2br($result->FieldValue);
				}
				
				$values[$result->FieldName] = $result->FieldValue;
			}
			
			foreach ($headers as $i => $header) {
				if (isset($values[$header])) {
					$values[$header] = ereg_replace("\015(\012)?", "\012", $values[$header]);
					if (strpos($values[$header],"\n") !== false)
						$values[$header] = str_replace("\n",' ',$values[$header]);
					
					$values[$header] = str_replace(array('\\r','\\n','\\t'), array("\015","\012","\011"),$values[$header]);
					
					$data[] = $values[$header];
				} else $data[] = '';
			}
		}
		
		return $data;
	}
	
	// Get RSForm!Pro headers
	public static function getHeaders($formId) {
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);
		
		$query->clear()
			->select($db->qn('p.PropertyValue'))
			->from($db->qn('#__rsform_components','c'))
			->join('left',$db->qn('#__rsform_properties','p').' ON ('.$db->qn('c.ComponentId').' = '.$db->qn('p.ComponentId').' AND '.$db->qn('p.PropertyName').' = '.$db->q('NAME').' )')
			->join('left',$db->qn('#__rsform_component_types','ct').' ON '.$db->qn('c.ComponentTypeId').' = '.$db->qn('ct.ComponentTypeId'))
			->where($db->qn('c.FormId').' = '.(int) $formId)
			->where($db->qn('c.Published').' = 1')
			->where($db->qn('ct.ComponentTypeName').' NOT IN ('.$db->q('button').','.$db->q('captcha').','.$db->q('freeText').','.$db->q('imageButton').','.$db->q('submitButton').')')
			->where($db->qn('p.PropertyValue').' NOT IN ('.$db->q('RSEProName').','.$db->q('RSEProEmail').','.$db->q('RSEProTickets').','.$db->q('RSEProPayment').','.$db->q('RSEProCoupon').')')
			->order($db->qn('c.Order'));
		
		$db->setQuery($query);
		$headers = $db->loadColumn();
		ksort($headers);
		
		return $headers;
	}
	
	// Get Card details
	public static function getCardDetails($id) {
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);
		$return = new stdClass();
		
		$query->clear()
			->select('*')
			->from($db->qn('#__rseventspro_cards'))
			->where($db->qn('ids').' = '.$id);
		
		$db->setQuery($query);
		$details = $db->loadObject();
		
		if (!empty($details)) {
			require_once JPATH_SITE.'/components/com_rseventspro/helpers/crypt.php';
			
			$name = $details->card_fname. ' '.$details->card_lname;
			$crypt = new RseventsproCryptHelper(null,null,$name);
			
			$return->card_number = $crypt->decrypt($details->card_number);
			$return->card_csc = $crypt->decrypt($details->card_csc);
			$return->card_exp = $details->card_exp;
			$return->name =  $name;
		} else {
			$return->card_number = $return->card_csc = $return->card_exp = $return->name =  '';
		}
		
		return $return;
	}
	
	// Get RSForm!Pro details
	public static function getRSFormData($id) {
		if (!file_exists(JPATH_ADMINISTRATOR.'/components/com_rsform/helpers/rsform.php')) 
			return false;
		
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		
		$query->clear()
			->select($db->qn('SubmissionId'))
			->from($db->qn('#__rseventspro_users'))
			->where($db->qn('id').' = '.$id);
		
		$db->setQuery($query);
		$SubmissionId = (int) $db->loadResult();
		
		$query->clear()
			->select($db->qn('FormId'))
			->from($db->qn('#__rsform_submission_values'))
			->where($db->qn('SubmissionId').' = '.$SubmissionId);
		
		$db->setQuery($query);
		$formId = (int) $db->loadResult();
		
		
		$query->clear()
			->select($db->qn('p.PropertyValue'))
			->from($db->qn('#__rsform_components','c'))
			->join('left',$db->qn('#__rsform_properties','p').' ON ('.$db->qn('c.ComponentId').' = '.$db->qn('p.ComponentId').' AND '.$db->qn('p.PropertyName').' = '.$db->q('NAME').' )')
			->join('left',$db->qn('#__rsform_component_types','ct').' ON '.$db->qn('c.ComponentTypeId').' = '.$db->qn('ct.ComponentTypeId'))
			->where($db->qn('c.FormId').' = '.(int) $formId)
			->where($db->qn('c.Published').' = 1')
			->order($db->qn('c.Order'));
		
		$db->setQuery($query);
		$headers = $db->loadColumn();
		
		$query->clear()
			->select('DISTINCT '.$db->qn('FieldName'))
			->select($db->qn('FieldValue'))
			->from($db->qn('#__rsform_submission_values'))
			->where($db->qn('SubmissionId').' = '.$SubmissionId);
		
		$db->setQuery($query);
		$values = $db->loadObjectList();
		
		$fields		= array();
		$captions	= array();
		
		if (!empty($values)) {
			foreach ($headers as $header) {
				foreach ($values as $value) {
					if ($value->FieldName == $header) {
						if (strpos($value->FieldValue,JPATH_SITE) !== FALSE) {
							$value->FieldValue = str_replace(array(JPATH_SITE,'/'),array(substr(JURI::root(),0,-1),'/'),$value->FieldValue);
						}
						$fields[$value->FieldName] = $value->FieldValue;
					}
				}
			}
			unset($fields['RSEProName'],$fields['RSEProEmail'],$fields['RSEProTickets'],$fields['RSEProPayment'],$fields['RSEProCoupon'] ,$fields['formId']);
		}
		
		if (!empty($fields)) {
			foreach ($fields as $fieldname => $fieldvalue) {
				$query->clear()
					->select($db->qn('ComponentId'))
					->from($db->qn('#__rsform_properties'))
					->where($db->qn('PropertyName').' = '.$db->q('NAME'))
					->where($db->qn('PropertyValue').' = '.$db->q($fieldname));
				
				$db->setQuery($query);
				$componentid = (int) $db->loadResult();
				
				$query->clear()
					->select($db->qn('ComponentTypeId'))
					->from($db->qn('#__rsform_components'))
					->where($db->qn('ComponentId').' = '.$db->q($componentid))
					->where($db->qn('FormId').' = '.$db->q($formId));
				
				$db->setQuery($query);
				$componenttypeid = (int) $db->loadResult();
				
				if (in_array($componenttypeid, array(7,8,12,13,41)))
					continue;
				
				$query->clear()
					->select($db->qn('PropertyValue'))
					->from($db->qn('#__rsform_properties'))
					->where($db->qn('PropertyName').' = '.$db->q('CAPTION'))
					->where($db->qn('ComponentId').' = '.$db->q($componentid));
				
				$db->setQuery($query);
				$value = $db->loadResult();
				
				$captions[$fieldname] = array('name' => $value, 'value' => $fieldvalue);
			}
		}
		
		return $captions;
	}
	
	// Get filter translation table
	public static function translate($text) {
		switch ($text) {
			// columns & ordering
			case 'events':
				return JText::_('COM_RSEVENTSPRO_FILTER_NAME');
			break;
			
			case 'description':
				return JText::_('COM_RSEVENTSPRO_FILTER_DESCRIPTION');
			break;
			
			case 'locations':
				return JText::_('COM_RSEVENTSPRO_FILTER_LOCATION');
			break;
			
			case 'categories':
				return JText::_('COM_RSEVENTSPRO_FILTER_CATEGORY');
			break;
			
			case 'tags':
				return JText::_('COM_RSEVENTSPRO_FILTER_TAG');
			break;
			
			// operators
			case 'is':
				return JText::_('COM_RSEVENTSPRO_FILTER_CONDITION_IS');
			break;
			
			case 'isnot':
				return JText::_('COM_RSEVENTSPRO_FILTER_CONDITION_ISNOT');
			break;
			
			case 'contains':
				return JText::_('COM_RSEVENTSPRO_FILTER_CONDITION_CONTAINS');
			break;
			
			case 'notcontain':
				return JText::_('COM_RSEVENTSPRO_FILTER_CONDITION_NOTCONTAINS');
			break;
		}
	}
	
	// Get event categories
	public static function categories($id, $print = false) {
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		
		$query->clear()
			->select($db->qn('c.id'))->select($db->qn('c.title'))
			->from($db->qn('#__categories','c'))
			->join('left', $db->qn('#__rseventspro_taxonomy','t').' ON '.$db->qn('t.id').' = '.$db->qn('c.id'))
			->where($db->qn('t.ide').' = '.(int) $id)
			->where($db->qn('t.type').' = '.$db->q('category'))
			->where($db->qn('c.extension').' = '.$db->q('com_rseventspro'));
		
		$db->setQuery($query);
		$categories = $db->loadObjectList();
		
		if ($print) {
			$html = array();
			if (!empty($categories)) {
				foreach ($categories as $category) {
					$html[] = '<a href="'.JRoute::_('index.php?option=com_categories&task=category.edit&id='.$category->id.'&extension=com_rseventspro').'">'.$category->title.'</a>';
				}
			}
			
			return implode('<br />',$html);
		}
		
		return $categories;
	}
	
	// Get event tags
	public static function tags($id, $print = false) {
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		
		$query->clear()
			->select($db->qn('t.id'))->select($db->qn('t.name'))
			->from($db->qn('#__rseventspro_tags','t'))
			->join('left',$db->qn('#__rseventspro_taxonomy','tx').' ON '.$db->qn('tx.id').' = '.$db->qn('t.id'))
			->where($db->qn('tx.ide').' = '.(int) $id)
			->where($db->qn('tx.type').' = '.$db->q('tag'));
		
		$db->setQuery($query);
		$tags = $db->loadObjectList();
		
		if ($print) {
			$html = array();
			if (!empty($tags)) {
				foreach ($tags as $tag) {
					$html[] = '<a href="'.JRoute::_('index.php?option=com_rseventspro&task=tag.edit&id='.$tag->id).'">'.$tag->name.'</a>';
				}
			}
			
			return implode(' , ',$html);
		}
		
		return $tags;
	}
	
	// Get event rating
	public static function stars($id) {
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		
		$query->clear()
			->select('CEIL(IFNULL(SUM(id)/COUNT(id),0))')
			->from($db->qn('#__rseventspro_taxonomy'))
			->where($db->qn('ide').' = '.(int) $id)
			->where($db->qn('type').' = '.$db->q('rating'));
		
		$db->setQuery($query);
		return (int) $db->loadResult();
	}
	
	// Get filter values
	public static function filter() {
		$jinput		= JFactory::getApplication()->input;
		$db			= JFactory::getDbo();
		$query		= $db->getQuery(true);
		$condition	= $jinput->getCmd('condition');
		$search		= $jinput->getString('search');
		$type		= $jinput->getCmd('type');
		$method		= $jinput->getCmd('method');
		$table		= '';
		$column		= 'name';
		$operator	= '=';
		$html		= array();
		
		// Get table
		if ($type == 'events') {
			$table = '#__rseventspro_events';
		} elseif ($type == 'locations') {
			$table = '#__rseventspro_locations';
		} elseif ($type == 'categories') {
			$table = '#__categories';
			$column = 'title';
		} elseif ($type == 'tags') {
			$table = '#__rseventspro_tags';
		}
		
		if ($condition == 'is') {
			$operator = '=';
		} else if ($condition == 'isnot') {
			$operator = '<>';
		} else if ($condition == 'contains') {
			$operator = 'LIKE';
			$search = '%'.$search.'%';
		} else if ($condition == 'notcontain') {
			$operator = 'NOT LIKE';
			$search = '%'.$search.'%';
		}
		
		$query->clear()
			->select($db->qn($column))
			->from($db->qn($table))
			->where($db->qn($column).' '.$operator.' '.$db->q($search));
		
		if ($type == 'categories') {
			$query->where($db->qn('extension').' = '.$db->q('com_rseventspro'));
			
			if (JFactory::getApplication()->isSite()) {
				if (JLanguageMultilang::isEnabled()) {
					$query->where('language IN ('.$db->q(JFactory::getLanguage()->getTag()).','.$db->q('*').')');
				}
				
				$user	= JFactory::getUser();
				$groups	= implode(',', $user->getAuthorisedViewLevels());
				$query->where('access IN ('.$groups.')');
			}
		}
		
		$db->setQuery($query);
		$results = $db->loadColumn();
		
		if ($method == 'json') {
			return json_encode($results);
		}
		
		if (!empty($results)) {
			$html[] = '<li><a class="rs_close" href="javascript:void(0)" onclick="$(\'rs_results\').style.display = \'none\';"></a></li>';
			foreach ($results as $result) {
				$html[] = '<li><a href="javascript:void(0)" onclick="rs_add_option(\''.addslashes($result).'\')">'.$result.'</a></li>';
			}
		}
		
		return implode("\n", $html);
	}
	
	// Copy event
	public static function copy($eventID, $dates) {
		jimport('joomla.filesystem.file');
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);
		
		$query->clear()
			->select('*')
			->from($db->qn('#__rseventspro_events'))
			->where($db->qn('id').' = '.(int) $eventID);
		
		$db->setQuery($query);
		$row = $db->loadObject();
		
		// Clone the parent
		$clone		= clone($row);
		$parent		= $eventID;
		
		// Get the event icon
		$query->clear()
			->select($db->qn('icon'))
			->from($db->qn('#__rseventspro_events'))
			->where($db->qn('id').' = '.(int) $parent);
		
		$db->setQuery($query);
		$icon = $db->loadResult();
		
		$copy = !is_array($dates) ? true : false;
		
		if (!is_array($dates)) {
			$dates = array();
			$newobject = new stdClass();
			$newobject->date = $row->start;
			$newobject->task = 'insert';
			$newobject->id = '';
			$dates[] = $newobject;
		}
		
		if (!empty($dates)) {
			
			if (!$clone->allday) {
				$theend = rseventsproHelper::date($clone->end,null,false,true);
				$theend->setTZByID($theend->getTZID());
				$theend->convertTZ(new RSDate_Timezone('GMT'));
			}
			
			$thestart = rseventsproHelper::date($clone->start,null,false,true);
			$thestart->setTZByID($thestart->getTZID());
			$thestart->convertTZ(new RSDate_Timezone('GMT'));
			
			if (!$clone->allday) {
				$duration	= $theend->getDate(RSDATE_FORMAT_UNIXTIME) - $thestart->getDate(RSDATE_FORMAT_UNIXTIME);
			}
			
			// Get event categories, groups, tags
			$query->clear()
				->select($db->qn('type'))->select($db->qn('id'))
				->from($db->qn('#__rseventspro_taxonomy'))
				->where($db->qn('ide').' = '.(int) $parent)
				->where($db->qn('type').' IN ('.$db->q('category').','.$db->q('tag').','.$db->q('groups').')');
			
			$db->setQuery($query);
			$taxonomies = $db->loadObjectList();
			
			// Get event tickets
			$query->clear()
				->select($db->qn('name'))->select($db->qn('price'))
				->select($db->qn('seats'))->select($db->qn('user_seats'))
				->select($db->qn('description'))
				->from($db->qn('#__rseventspro_tickets'))
				->where($db->qn('ide').' = '.(int) $parent);
			
			$db->setQuery($query);
			$tickets = $db->loadObjectList();
			
			// Get event coupons
			$query->clear()
				->select($db->qn('id'))->select($db->qn('name'))
				->select($db->qn('from'))->select($db->qn('to'))
				->select($db->qn('usage'))->select($db->qn('discount'))
				->select($db->qn('type'))->select($db->qn('action'))
				->select($db->qn('groups'))
				->from($db->qn('#__rseventspro_coupons'))
				->where($db->qn('ide').' = '.(int) $parent);
			
			$db->setQuery($query);
			$coupons = $db->loadObjectList();
			
			// Get event files
			$query->clear()
				->select($db->qn('name'))->select($db->qn('location'))
				->select($db->qn('permissions'))
				->from($db->qn('#__rseventspro_files'))
				->where($db->qn('ide').' = '.(int) $parent);
			
			$db->setQuery($query);
			$files = $db->loadObjectList();
			
			$now = rseventsproHelper::date('now',null,false,true);
			
			$return = 0;
			$count = 1;
			foreach ($dates as $object) {
				// Get the task
				$task = $object->task;
				
				// Get the date
				$date = $object->date;
				
				// Get the id
				$id = $object->id;
				
				// Remove event
				if ($task == 'remove') {
					if (!empty($id)) {
						rseventsproHelper::remove($id);
					}
					continue;
				}
				
				// Get already used tickets
				$query->clear()
				->select('DISTINCT '.$db->qn('ut.idt'))
				->from($db->qn('#__rseventspro_user_tickets','ut'))
				->join('left', $db->qn('#__rseventspro_users','u').' ON '.$db->qn('u.id').' = '.$db->qn('ut.ids'))
				->where($db->qn('u.ide').' = '.(int) $id);
				
				$db->setQuery($query);
				$usedtickets = $db->loadColumn();
				
				if (!empty($usedtickets)) {
					JArrayHelper::toInteger($usedtickets);
				}
				
				// Update events
				if ($task == 'update') {
					// Delete categories, tags, repeating days, event groups, ratings
					$query->clear()
						->delete()
						->from($db->qn('#__rseventspro_taxonomy'))
						->where($db->qn('ide').' = '.(int) $id);
					
					$db->setQuery($query);
					$db->execute();
					
					// Delete coupon codes
					$query->clear()
						->select($db->qn('id'))
						->from($db->qn('#__rseventspro_coupons'))
						->where($db->qn('ide').' = '.(int) $id);
					
					$db->setQuery($query);
					if ($couponids = $db->loadColumn()) {
						JArrayHelper::toInteger($couponids);
						$query->clear()
							->delete()
							->from($db->qn('#__rseventspro_coupon_codes'))
							->where($db->qn('idc').' IN ('.implode(',',$couponids).')');
						
						$db->setQuery($query);
						$db->execute();
					}
					
					// Delete coupons
					$query->clear()
						->delete()
						->from($db->qn('#__rseventspro_coupons'))
						->where($db->qn('ide').' = '.(int) $id);
					
					$db->setQuery($query);
					$db->execute();
					
					// Delete tickets
					$query->clear()
						->delete()
						->from($db->qn('#__rseventspro_tickets'))
						->where($db->qn('ide').' = '.(int) $id);
					
					if (!empty($usedtickets))
						$query->where($db->qn('id').' NOT IN ('.implode(',',$usedtickets).')');
					
					$db->setQuery($query);
					$db->execute();
					
					//Get event files
					$query->clear()
						->select($db->qn('location'))
						->from($db->qn('#__rseventspro_files'))
						->where($db->qn('ide').' = '.(int) $id);
						
					$db->setQuery($query);
					$oldchildfiles = $db->loadColumn();
					
					if (!empty($oldchildfiles)) {
						foreach($oldchildfiles as $childfile) {
							if (JFile::exists(JPATH_SITE.'/components/com_rseventspro/assets/images/files/'.$childfile))
								JFile::delete(JPATH_SITE.'/components/com_rseventspro/assets/images/files/'.$childfile);
						}
					}
					
					//Event icon
					$query->clear()
						->select($db->qn('icon'))
						->from($db->qn('#__rseventspro_events'))
						->where($db->qn('id').' = '.(int) $id);
					
					$db->setQuery($query);
					$oldicon = $db->loadResult();
					
					// Delete event icon
					if (JFile::exists(JPATH_SITE.'/components/com_rseventspro/assets/images/events/'.$oldicon))
						JFile::delete(JPATH_SITE.'/components/com_rseventspro/assets/images/events/'.$oldicon);
					if (JFile::exists(JPATH_SITE.'/components/com_rseventspro/assets/images/events/thumbs/e_'.$oldicon))
						JFile::delete(JPATH_SITE.'/components/com_rseventspro/assets/images/events/thumbs/e_'.$oldicon);
					if (JFile::exists(JPATH_SITE.'/components/com_rseventspro/assets/images/events/thumbs/s_'.$oldicon))
						JFile::delete(JPATH_SITE.'/components/com_rseventspro/assets/images/events/thumbs/s_'.$oldicon);
					if (JFile::exists(JPATH_SITE.'/components/com_rseventspro/assets/images/events/thumbs/b_'.$oldicon))
						JFile::delete(JPATH_SITE.'/components/com_rseventspro/assets/images/events/thumbs/b_'.$oldicon);
				}
				
				$cstart = rseventsproHelper::date($date,null,false,true);
				$cstart->setTZByID($cstart->getTZID());
				$cstart->convertTZ(new RSDate_Timezone('GMT'));
				
				if (!$clone->allday) {
					$cend	= rseventsproHelper::date($date,null,false,true);
					$cend->setTZByID($cend->getTZID());
					$cend->convertTZ(new RSDate_Timezone('GMT'));
					$cend->addSeconds($duration);
				}
				
				$clonestart				= $cstart->formatLikeDate('Y-m-d H:i:s');
				
				if (!$clone->allday) {
					$cloneend			= $cend->formatLikeDate('Y-m-d H:i:s');
				}
				
				$clone->id				= $task == 'insert' ? null : $id;
				$clone->start			= $clonestart;
				$clone->end				= !$clone->allday ? $cloneend : $db->getNullDate();
				$clone->icon			= '';
				$clone->recurring		= '';
				$clone->repeat_interval	= '';
				$clone->repeat_type		= '';
				$clone->repeat_end		= '';
				$clone->repeat_also		= '';
				$clone->archived		= 0;
				
				// Adjust Start Registration, End Registration, Unsubscribe Date
				if ($row->start_registration == $db->getNullDate() || $row->start_registration == '') {
					$start_registration = false;
				} else {
					$start_registration	= rseventsproHelper::date($row->start_registration,null,false,true);
				}
				
				if ($row->end_registration == $db->getNullDate() || $row->end_registration == '') {
					$end_registration = false;
				} else {
					$end_registration	= rseventsproHelper::date($row->end_registration,null,false,true);
				}
				
				if ($row->unsubscribe_date == $db->getNullDate() || $row->unsubscribe_date == '') {
					$unsubscribe_date = false;
				} else {
					$unsubscribe_date	= rseventsproHelper::date($row->unsubscribe_date,null,false,true);
				}
				
				$interval	= $row->repeat_interval;
				if ($start_registration) {
					if ($row->repeat_type == 1)
						$start_registration->addDays($count * $interval);
					else if ($row->repeat_type == 2)
						$start_registration->addDays($count * 7 * $interval);
					else if ($row->repeat_type == 3)
						$start_registration->addMonths($count * $interval);
					else if ($row->repeat_type == 4)
						$start_registration->addYears($count * $interval);
					
					$start_registration = new RSDate($start_registration->formatLikeDate('Y-m-d H:i:s')); 
					$start_registration->setTZByID(rseventsproHelper::getTimezone());
					$start_registration->convertTZ(new RSDate_Timezone('GMT'));
					
					$start_registration = rseventsproHelper::date($start_registration->formatLikeDate('Y-m-d H:i:s'),null,false,true);
					$start_registration->setTZByID($start_registration->getTZID());
					$start_registration->convertTZ(new RSDate_Timezone('GMT'));
					
					$clone->start_registration = $start_registration->formatLikeDate('Y-m-d H:i:s');
				}
				
				if ($end_registration) {
					if ($row->repeat_type == 1)
						$end_registration->addDays($count * $interval);
					else if ($row->repeat_type == 2)
						$end_registration->addDays($count * 7 * $interval);
					else if ($row->repeat_type == 3)
						$end_registration->addMonths($count * $interval);
					else if ($row->repeat_type == 4)
						$end_registration->addYears($count * $interval);
					
					$end_registration = new RSDate($end_registration->formatLikeDate('Y-m-d H:i:s')); 
					$end_registration->setTZByID(rseventsproHelper::getTimezone());
					$end_registration->convertTZ(new RSDate_Timezone('GMT'));
					
					$end_registration = rseventsproHelper::date($end_registration->formatLikeDate('Y-m-d H:i:s'),null,false,true);
					$end_registration->setTZByID($end_registration->getTZID());
					$end_registration->convertTZ(new RSDate_Timezone('GMT'));
					
					$clone->end_registration = $end_registration->formatLikeDate('Y-m-d H:i:s');
				}
				
				if ($unsubscribe_date) {
					if ($row->repeat_type == 1)
						$unsubscribe_date->addDays($count * $interval);
					else if ($row->repeat_type == 2)
						$unsubscribe_date->addDays($count * 7 * $interval);
					else if ($row->repeat_type == 3)
						$unsubscribe_date->addMonths($count * $interval);
					else if ($row->repeat_type == 4)
						$unsubscribe_date->addYears($count * $interval);
					
					$unsubscribe_date = new RSDate($unsubscribe_date->formatLikeDate('Y-m-d H:i:s')); 
					$unsubscribe_date->setTZByID(rseventsproHelper::getTimezone());
					$unsubscribe_date->convertTZ(new RSDate_Timezone('GMT'));
					
					$unsubscribe_date = rseventsproHelper::date($unsubscribe_date->formatLikeDate('Y-m-d H:i:s'),null,false,true);
					$unsubscribe_date->setTZByID($unsubscribe_date->getTZID());
					$unsubscribe_date->convertTZ(new RSDate_Timezone('GMT'));
					
					$clone->unsubscribe_date = $unsubscribe_date->formatLikeDate('Y-m-d H:i:s');
				}
				
				if ($copy) {
					$clone->parent = 0;
					$clone->name = JText::_('COM_RSEVENTSPRO_GLOBAL_COPY_OF').' '.$clone->name;
					$clone->archived = 0;
				} else {
					$clone->parent		= $parent;
					$clone->archived	= 0;
				}
					
				// Save the new event
				if ($clone->id) {
					$db->updateObject('#__rseventspro_events', $clone, 'id');
				} else {
					$db->insertObject('#__rseventspro_events', $clone, 'id');
				}
				
				$return = $clone->id;
				
				// Add taxonomy
				if (!empty($taxonomies)) {
					foreach ($taxonomies as $taxonomy) {
						$query->clear()
							->insert($db->qn('#__rseventspro_taxonomy'))
							->set($db->qn('id').' = '.(int) $taxonomy->id)
							->set($db->qn('type').' = '.$db->q($taxonomy->type))
							->set($db->qn('ide').' = '.(int) $clone->id);
						
						$db->setQuery($query);
						$db->execute();
					}
				}
				
				//Add tickets
				if (!empty($tickets)) {
					foreach ($tickets as $ticket) {
						$ticketid = 0;
						if ($task == 'update') {
							if (!empty($usedtickets)) {
								$query->clear()
									->select($db->qn('id'))
									->from($db->qn('#__rseventspro_tickets'))
									->where($db->qn('name').' = '.$db->q($ticket->name))
									->where($db->qn('price').' = '.$db->q($ticket->price))
									->where($db->qn('ide').' = '.(int) $id);
								
								$db->setQuery($query);
								$ticketid = (int) $db->loadResult();
							}
						}
						
						if (!$ticketid) {
							$query->clear()
								->insert($db->qn('#__rseventspro_tickets'))
								->set($db->qn('ide').' = '.(int) $clone->id)
								->set($db->qn('name').' = '.$db->q($ticket->name))
								->set($db->qn('price').' = '.$db->q($ticket->price))
								->set($db->qn('seats').' = '.$db->q($ticket->seats))
								->set($db->qn('user_seats').' = '.$db->q($ticket->user_seats))
								->set($db->qn('description').' = '.$db->q($ticket->description));
						
							$db->setQuery($query);
							$db->execute();
						}
					}
				}
				
				// Add coupons
				if (!empty($coupons)) {
					foreach ($coupons as $coupon) {
						$query->clear()
							->insert($db->qn('#__rseventspro_coupons'))
							->set($db->qn('ide').' = '.(int) $clone->id)
							->set($db->qn('name').' = '.$db->q($coupon->name))
							->set($db->qn('from').' = '.$db->q($coupon->from))
							->set($db->qn('to').' = '.$db->q($coupon->to))
							->set($db->qn('usage').' = '.$db->q($coupon->usage))
							->set($db->qn('discount').' = '.$db->q($coupon->discount))
							->set($db->qn('type').' = '.$db->q($coupon->type))
							->set($db->qn('action').' = '.$db->q($coupon->action))
							->set($db->qn('groups').' = '.$db->q($coupon->groups));
						
						$db->setQuery($query);
						$db->execute();
						$couponID = $db->insertid();
						
						// Add coupon codes
						$query->clear()
							->select($db->qn('code'))
							->from($db->qn('#__rseventspro_coupon_codes'))
							->where($db->qn('idc').' = '.(int) $coupon->id);
						
						$db->setQuery($query);
						if ($couponcodes = $db->loadColumn()) {
							foreach ($couponcodes as $code) {
								$query->clear()
									->insert($db->qn('#__rseventspro_coupon_codes'))
									->set($db->qn('code').' = '.$db->q($code))
									->set($db->qn('idc').' = '.$db->q($couponID))
									->set($db->qn('used').' = 0');
								
								$db->setQuery($query);
								$db->execute();
							}
						}
						
					}
				}
				
				// Add files
				if (!empty($files)) {
					foreach ($files as $file) {
						$filename	= JFile::stripExt($file->location);
						$ext		= JFile::getExt($file->location);
						
						while(JFile::exists(JPATH_SITE.'/components/com_rseventspro/assets/images/files/'.$filename.'.'.$ext))
							$filename .= rand(1,999);
						
						if (JFile::copy(JPATH_SITE.'/components/com_rseventspro/assets/images/files/'.$file->location, JPATH_SITE.'/components/com_rseventspro/assets/images/files/'.$filename.'.'.$ext)) {
							$query->clear()
								->insert($db->qn('#__rseventspro_files'))
								->set($db->qn('ide').' = '.(int) $clone->id)
								->set($db->qn('name').' = '.$db->q($file->name))
								->set($db->qn('location').' = '.$db->q($filename.'.'.$ext))
								->set($db->qn('permissions').' = '.$db->q($file->permissions));
							
							$db->setQuery($query);
							$db->execute();
						}
					}
				}
				
				// Add event icon
				if (!empty($icon)) {
					$filename	= JFile::stripExt($icon);
					$ext		= JFile::getExt($icon);
					$path		= JPATH_SITE.'/components/com_rseventspro/assets/images/events/';
					
					while(JFile::exists($path.$filename.'.'.$ext))
						$filename .= rand(1,999);
					
					if (JFile::copy($path.$icon,$path.$filename.'.'.$ext)) {
						$query->clear()
							->update($db->qn('#__rseventspro_events'))
							->set($db->qn('icon').' = '.$db->q($filename.'.'.$ext))
							->where($db->qn('id').' = '.(int) $clone->id);
						
						$db->setQuery($query);
						$db->execute();
						
						if (JFile::exists($path.'thumbs/b_'.$icon))
							JFile::copy($path.'thumbs/b_'.$icon, $path.'thumbs/b_'.$filename.'.'.$ext);
						if (JFile::exists($path.'thumbs/s_'.$icon))
							JFile::copy($path.'thumbs/s_'.$icon, $path.'thumbs/s_'.$filename.'.'.$ext);
						if (JFile::exists($path.'thumbs/e_'.$icon))
							JFile::copy($path.'thumbs/e_'.$icon, $path.'thumbs/e_'.$filename.'.'.$ext);
					}
				}
				
				$count++;
			}
			
			if ($copy) 
				return $return;
		}
	}
	
	// Remove event
	public static function remove($id) {
		jimport('joomla.filesystem.file');
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		
		// Delete from sync db
		$query->clear()
			->delete()
			->from($db->qn('#__rseventspro_sync'))
			->where($db->qn('ide').' = '.(int) $id);
		
		$db->setQuery($query);
		$db->execute();
		
		// Delete coupon codes
		$query->clear()
			->select($db->qn('id'))
			->from($db->qn('#__rseventspro_coupons'))
			->where($db->qn('ide').' = '.(int) $id);
		
		$db->setQuery($query);
		if ($couponids = $db->loadColumn()) {
			JArrayHelper::toInteger($couponids);
			$query->clear()
				->delete()
				->from($db->qn('#__rseventspro_coupon_codes'))
				->where($db->qn('idc').' IN ('.implode(',',$couponids).')');
			
			$db->setQuery($query);
			$db->execute();
		}
		
		// Delete coupons
		$query->clear()
			->delete()
			->from($db->qn('#__rseventspro_coupons'))
			->where($db->qn('ide').' = '.(int) $id);
		
		$db->setQuery($query);
		$db->execute();
		
		// Delete tickets
		$query->clear()
			->delete()
			->from($db->qn('#__rseventspro_tickets'))
			->where($db->qn('ide').' = '.(int) $id);
		
		$db->setQuery($query);
		$db->execute();
		
		// Delete subscriptions
		$query->clear()
			->select($db->qn('id'))
			->from($db->qn('#__rseventspro_users'))
			->where($db->qn('ide').' = '.(int) $id);
		
		$db->setQuery($query);
		$subscriptions = $db->loadColumn();
		
		if (!empty($subscriptions)) {
			JArrayHelper::toInteger($subscriptions);
			
			$query->clear()
				->delete()
				->from($db->qn('#__rseventspro_users'))
				->where($db->qn('id').' IN ('.implode(',', $subscriptions).')');
			
			$db->setQuery($query);
			$db->execute();
			
			$query->clear()
				->delete()
				->from($db->qn('#__rseventspro_user_tickets'))
				->where($db->qn('ids').' IN ('.implode(',', $subscriptions).')');
				
			$db->setQuery($query);
			$db->execute();
		}
		
		// Delete categories, tags, repeating days, event groups, ratings
		$query->clear()
			->delete()
			->from($db->qn('#__rseventspro_taxonomy'))
			->where($db->qn('ide').' = '.(int) $id);
		
		$db->setQuery($query);
		$db->execute();
		
		//Get event files
		$query->clear()
			->select($db->qn('location'))
			->from($db->qn('#__rseventspro_files'))
			->where($db->qn('ide').' = '.(int) $id);
		
		$db->setQuery($query);
		$oldfiles = $db->loadColumn();
		
		if (!empty($oldfiles)) {
			foreach($oldfiles as $file) {
				if (JFile::exists(JPATH_SITE.'/components/com_rseventspro/assets/images/files/'.$file))
					JFile::delete(JPATH_SITE.'/components/com_rseventspro/assets/images/files/'.$file);
			}
		}
		
		// Delete files
		$query->clear()
			->delete()
			->from($db->qn('#__rseventspro_files'))
			->where($db->qn('ide').' = '.(int) $id);
		
		$db->setQuery($query);
		$db->execute();
		
		//Event icon
		$query->clear()
			->select($db->qn('icon'))
			->from($db->qn('#__rseventspro_events'))
			->where($db->qn('id').' = '.(int) $id);
			
		$db->setQuery($query);
		$icon = $db->loadResult();
		
		// Delete event icon
		if (JFile::exists(JPATH_SITE.'components/com_rseventspro/assets/images/events/'.$icon))
			JFile::delete(JPATH_SITE.'components/com_rseventspro/assets/images/events/'.$icon);
		if (JFile::exists(JPATH_SITE.'components/com_rseventspro/assets/images/events/thumbs/e_'.$icon))
			JFile::delete(JPATH_SITE.'components/com_rseventspro/assets/images/events/thumbs/e_'.$icon);
		if (JFile::exists(JPATH_SITE.'components/com_rseventspro/assets/images/events/thumbs/s_'.$icon))
			JFile::delete(JPATH_SITE.'components/com_rseventspro/assets/images/events/thumbs/s_'.$icon);
		if (JFile::exists(JPATH_SITE.'components/com_rseventspro/assets/images/events/thumbs/b_'.$icon))
			JFile::delete(JPATH_SITE.'components/com_rseventspro/assets/images/events/thumbs/b_'.$icon);
		
		// Delete event
		$query->clear()
			->delete()
			->from($db->qn('#__rseventspro_events'))
			->where($db->qn('id').' = '.(int) $id);
		
		$db->setQuery($query);
		$db->execute();
		
		return true;
	}
	
	// Load event edit scripts
	public static function loadEditEvent($scripts = true, $crop = false) {
		JHtml::_('behavior.framework', true);
		
		$doc = JFactory::getDocument();
		$app = JFactory::getApplication();
		$from = $app->isAdmin() ? 'administrator/' : '';
		$head = $doc->getHeadData();
		$js = array();
		
		$js[JURI::root().'components/com_rseventspro/assets/js/compat.js'] = array('mime' => 'text/javascript', 'defer' => false, 'async' => false);
		$head['scripts'] = array_merge($head['scripts'],$js);	
		$doc->setHeadData($head);
		
		$ie7 = '<!--[if IE 7]>'."\n".'<link href="'.JURI::root().$from.'components/com_rseventspro/assets/css/ie7.css" rel="stylesheet" type="text/css" />'."\n".'<![endif]-->'."\n";
		$modal = 'SqueezeBox.extend(SqueezeBox, {
				reposition: function(sizes) {
					sizes = sizes || window.getSize();
					this.overlay.setStyles({
						width: sizes.x,
						height: sizes.y
					});
					this.fireEvent(\'onMove\', [this.overlay, this.window, sizes]);
				}
			});';
		$modal .= "\n".'window.addEvent(\'domready\', function() {
	SqueezeBox.initialize({});
	SqueezeBox.assign($$(\'a.modal\'), {
		parse: \'rel\'
	});
});';
		
		
		
		if ($scripts) {
			if (rseventsproHelper::isJ3())
				JHtml::_('jquery.framework');
			else {
				$doc->addScript(JURI::root().'components/com_rseventspro/assets/js/jquery/jquery-1.9.1.js?v='.RSEPRO_RS_REVISION);
				$doc->addScript(JURI::root().'components/com_rseventspro/assets/js/jquery/jquery.noconflict.js?v='.RSEPRO_RS_REVISION);
			}
			
			$doc->addScript(JURI::root().'components/com_rseventspro/assets/js/jquery/autosuggest/jquery.autoSuggest.js?v='.RSEPRO_RS_REVISION);
			$doc->addScript(JURI::root().'components/com_rseventspro/assets/js/jquery/autosuggest/jquery.autoSuggest.script.js?v='.RSEPRO_RS_REVISION);
			$doc->addStyleSheet(JURI::root().'components/com_rseventspro/assets/js/jquery/autosuggest/autoSuggest.css?v='.RSEPRO_RS_REVISION);
			
			$doc->addScript(JURI::root().'components/com_rseventspro/assets/js/Observer.js?v='.RSEPRO_RS_REVISION);
			$doc->addScript(JURI::root().'components/com_rseventspro/assets/js/Autocompleter.js?v='.RSEPRO_RS_REVISION);
			$doc->addScript(JURI::root().'components/com_rseventspro/assets/js/Autocompleter.Request.js?v='.RSEPRO_RS_REVISION);
			$doc->addScript(JURI::root().'components/com_rseventspro/assets/js/modalbox.js?v='.RSEPRO_RS_REVISION);
			$doc->addScript(JURI::root().'components/com_rseventspro/assets/js/edit.js?v='.RSEPRO_RS_REVISION);
			$doc->addStyleSheet(JURI::root().$from.'components/com_rseventspro/assets/css/edit.css?v='.RSEPRO_RS_REVISION);
			$doc->addCustomTag($ie7);
			$doc->addScriptDeclaration($modal);
			
			if (!$app->isAdmin() && rseventsproHelper::isJ3())
				JHtml::_('formbehavior.chosen', '.rschosen');
		}
		
		if ($crop) {
			$doc->addScript(JURI::root().'components/com_rseventspro/assets/js/crop/effect-element.js?v='.RSEPRO_RS_REVISION);
			$doc->addScript(JURI::root().'components/com_rseventspro/assets/js/crop/movable.js?v='.RSEPRO_RS_REVISION);
			$doc->addScript(JURI::root().'components/com_rseventspro/assets/js/crop/resizable.js?v='.RSEPRO_RS_REVISION);
			$doc->addScript(JURI::root().'components/com_rseventspro/assets/js/crop/dg-crop.js?v='.RSEPRO_RS_REVISION);
			$doc->addStyleSheet(JURI::root().'components/com_rseventspro/assets/css/dg-crop.css?v='.RSEPRO_RS_REVISION);
		}
	}
	
	// Load RSEvents!Pro plugins
	public static function loadPlugins() {
		jimport('joomla.plugin.helper');
		JPluginHelper::importPlugin('rseventspro');
	}
	
	// Get locations filter
	public static function filterlocations() {
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$html = array();
		$permissions = rseventsproHelper::permissions();
		
		$query->clear()
			->select($db->qn('id'))->select($db->qn('name'))
			->from($db->qn('#__rseventspro_locations'))
			->where($db->qn('published').' = 1')
			->where($db->qn('name').' LIKE '.$db->q('%'.JFactory::getApplication()->input->getString('rs_location').'%'));
		
		$db->setQuery($query);
		if ($locations = $db->loadObjectList()) {
			foreach ($locations as $location) {
				$html[] = '<li id="'.$location->id.'">'.$location->name.'</li>';
			}
			
			if (JFactory::getApplication()->isAdmin())
				$html[] = '<li id="-" onclick="rs_add_loc();">'.JText::_('COM_RSEVENTSPRO_ADD_LOCATION',true).'</li>';
			else {
				if (!empty($permissions['can_add_locations']) || rseventsproHelper::admin())
					$html[] = '<li id="-" onclick="rs_add_loc();">'.JText::_('COM_RSEVENTSPRO_ADD_LOCATION',true).'</li>';
			}
		}
		
		return implode("\n",$html);
	}
	
	// Check repeats
	public static function repeats() {
		$dates	= array();
		$jinput	= JFactory::getApplication()->input;
		$repeat	= $jinput->getInt('interval',0);
		$type	= $jinput->getInt('type',0);
		$estart	= $jinput->getString('start');
		$eend	= $jinput->getString('end');
		$days	= $jinput->get('days',array(),'array()');
		
		$start	= rseventsproHelper::date($estart,null,false,true);
		$start->setTZByID($start->getTZID());
		$start->convertTZ(new RSDate_Timezone('GMT'));
		
		$stop	= rseventsproHelper::date($eend.'00:00:00',null,false,true);
		$stop->setTZByID($stop->getTZID());
		$stop->convertTZ(new RSDate_Timezone('GMT'));
		
		list($h, $m, $s) = explode(':', $start->formatLikeDate('H:i:s'), 3);
		$hours = $h*3600+$m*60+$s;
		
		JArrayHelper::toInteger($days);
		
		//switch on the type of the repetition
		if ($repeat > 0) {
			switch($type) {
				//Days
				case 1:
					while ($start <= $stop) {
						$start->addDays($repeat);
						if (!in_array($start->formatLikeDate('w'),$days)) continue;
						if($start > $stop) break;
						$dates[] = $start->formatLikeDate('Y-m-d H:i:s');
					}
				break;
				
				//Weeks
				case 2:
					while ($start <= $stop) {
						$start->addDays($repeat * 7);
						if($start > $stop) break;
						
						$Calc = new RSDate_Calc();
						$beginofweek = $Calc->beginOfWeek($start->formatLikeDate('d'),$start->formatLikeDate('m'),$start->formatLikeDate('Y'));
						$from = rseventsproHelper::date($beginofweek,null,false,true);
						$from->setTZByID($from->getTZID());
						$from->convertTZ(new RSDate_Timezone('GMT'));
						$from->addSeconds($hours);
						
						$Calc = new RSDate_Calc();
						$endofweek = $Calc->endOfWeek($start->formatLikeDate('d'),$start->formatLikeDate('m'),$start->formatLikeDate('Y'));
						$to = rseventsproHelper::date($endofweek,null,false,true);
						$to->setTZByID($to->getTZID());
						$to->convertTZ(new RSDate_Timezone('GMT'));
						$to->addSeconds($hours);
						
						if ($to > $stop) {
							$blank = $stop->getDate(RSDATE_FORMAT_UNIXTIME) + $hours;
							$to = rseventsproHelper::date($blank,null,false,true);
							$to->setTZByID($to->getTZID());
							$to->convertTZ(new RSDate_Timezone('GMT'));
						}
						
						if (in_array($from->formatLikeDate('w'),$days))
							$dates[] = $from->formatLikeDate('Y-m-d H:i:s');
						if (in_array($to->formatLikeDate('w'),$days))
							$dates[] = $to->formatLikeDate('Y-m-d H:i:s');
						
						while ($from <= $to) {
							$from->addDays(1);
							if($from > $to) break;
							if (in_array($from->formatLikeDate('w'),$days))
								$dates[] = $from->formatLikeDate('Y-m-d H:i:s');
						}
						
						if (!in_array($start->formatLikeDate('w'),$days)) continue;
						$dates[] = $start->formatLikeDate('Y-m-d H:i:s');
					}
				break;
				
				//Months
				case 3:
					while ($start <= $stop) {
						$start->addMonths($repeat);
						if($start > $stop) break;
						$dates[] = $start->formatLikeDate('Y-m-d H:i:s');
					}
				break;
				
				//Years
				case 4:
					while ($start <= $stop) {
						$start->addYears($repeat);
						if($start > $stop) break;
						$dates[] = $start->formatLikeDate('Y-m-d H:i:s');
					}
				break;
			}
			
			$dates = array_unique($dates);
			return count($dates);
		}
		
		return 0;
	}
	
	// Check if we can use the RSForm!Pro form in our registration process
	public static function checkform($fid, $id) {
		$db			= JFactory::getDbo();
		$query		= $db->getQuery(true);
		$messages	= array();
		
		jimport('joomla.plugin.helper');
		
		if (!file_exists(JPATH_SITE.'/components/com_rsform/rsform.php')) 
			return array('result' => false, 'message' => JText::_('COM_RSEVENTSPRO_RSFP_INSTALL_RSFORMPRO'));
		
		if (!file_exists(JPATH_SITE.'/plugins/system/rsfprseventspro/rsfprseventspro.php'))
			return array('result' => false, 'message' => JText::_('COM_RSEVENTSPRO_RSFP_INSTALL_PLUGIN'));
		
		if (!JPluginHelper::isEnabled('system','rsfprseventspro'))
			return array('result' => false, 'message' => JText::_('COM_RSEVENTSPRO_RSFP_UNPUBLISHED_PLUGIN'));
		
		$query->clear()
			->select($db->qn('registration'))->select($db->qn('discounts'))
			->from($db->qn('#__rseventspro_events'))
			->where($db->qn('id').' = '.(int) $id);
		
		$db->setQuery($query);
		$event = $db->loadObject();
		
		$query->clear()
			->select('COUNT('.$db->qn('id').')')
			->from($db->qn('#__rseventspro_tickets'))
			->where($db->qn('ide').' = '.(int) $id);
		
		$db->setQuery($query);
		$tickets = $db->loadResult();		
		
		if (!$event->registration)
			return array('result' => false, 'message' => JText::_('COM_RSEVENTSPRO_NO_REGISTRATION'));
		
		// Search for the Name component
		$query->clear()
			->select('COUNT('.$db->qn('ComponentId').')')
			->from($db->qn('#__rsform_components'))
			->where($db->qn('FormId').' = '.(int) $fid)
			->where($db->qn('Published').' = 1')
			->where($db->qn('ComponentTypeId').' = 30');
		
		$db->setQuery($query);
		$name = $db->loadResult();
		
		if (!$name)
			$messages[30] = JText::_('COM_RSEVENTSPRO_RSFP_NAME');
		
		// Search for the Email component
		$query->clear()
			->select('COUNT('.$db->qn('ComponentId').')')
			->from($db->qn('#__rsform_components'))
			->where($db->qn('FormId').' = '.(int) $fid)
			->where($db->qn('Published').' = 1')
			->where($db->qn('ComponentTypeId').' = 31');
		
		$db->setQuery($query);
		$email = $db->loadResult();
		
		if(!$email)
			$messages[31] = JText::_('COM_RSEVENTSPRO_RSFP_EMAIL');
		
		// Search for the Tickets component
		$query->clear()
			->select('COUNT('.$db->qn('ComponentId').')')
			->from($db->qn('#__rsform_components'))
			->where($db->qn('FormId').' = '.(int) $fid)
			->where($db->qn('Published').' = 1')
			->where($db->qn('ComponentTypeId').' = 32');
		
		$db->setQuery($query);
		$ticket = $db->loadResult();
		
		if ($tickets && !$ticket)
			$messages[32] = JText::_('COM_RSEVENTSPRO_RSFP_TICKETS');
		
		if (!$tickets && $ticket)
			$messages[32] = JText::_('COM_RSEVENTSPRO_RSFP_REMOVE_TICKETS');
		
		// Search for the Payments component
		$query->clear()
			->select('COUNT('.$db->qn('ComponentId').')')
			->from($db->qn('#__rsform_components'))
			->where($db->qn('FormId').' = '.(int) $fid)
			->where($db->qn('Published').' = 1')
			->where($db->qn('ComponentTypeId').' = 33');
		
		$db->setQuery($query);
		$payment = $db->loadResult();
		
		if ($tickets && !$payment)
			$messages[33] = JText::_('COM_RSEVENTSPRO_RSFP_PAYMENTS');
		
		if (!$tickets && $payment)
			$messages[33] = JText::_('COM_RSEVENTSPRO_RSFP_REMOVE_PAYMENTS');
		
		$query->clear()
			->select('COUNT('.$db->qn('ComponentId').')')
			->from($db->qn('#__rsform_components'))
			->where($db->qn('FormId').' = '.(int) $fid)
			->where($db->qn('Published').' = 1')
			->where($db->qn('ComponentTypeId').' = 34');
		
		$db->setQuery($query);
		$coupon = $db->loadResult();
		
		if ($event->discounts && !$coupon)
			$messages[34] = JText::_('COM_RSEVENTSPRO_RSFP_COUPONS');
		
		if (count($messages) == 1 && isset($messages[34]) && $event->discounts && $event->registration)
			return array('result' => true, 'message' => $messages[34]);
		
		if (empty($messages))
			return array('result' => true, 'message' => '');
		
		return array('result' => false, 'message' => implode('<br />',$messages));
	}
	
	// Get event default options
	public static function getDefaultOptions() {
		require_once JPATH_SITE.'/components/com_rseventspro/helpers/events.php';
		$event = RSEvent::getInstance();
		
		$defaults = $event->getDefaultOptions();
		$registry = new JRegistry;
		$registry->loadArray($defaults);
		return $registry->toString();
	}
	
	// Create sef alias
	public static function sef($id,$name) {
		return intval($id).':'.JFilterOutput::stringURLSafe($name);
	}
	
	// Route links
	public static function route($url, $xhtml=true, $Itemid='') {
		$app 		= JFactory::getApplication();
		$input		= $app->input;
		$current	= $input->getInt('Itemid',0);
		$option		= $input->get('option','');
		
		if (!$Itemid) {
			$menu 		= $app->getMenu();
			$menuItem 	= $menu->getActive();
			
			if (is_object($menuItem) && isset($menuItem->home) && empty($current)) {
				if ($menuItem->home) {
					$Itemid = $menuItem->id;
				}
			}
		}
		
		if (!$Itemid && $current && $option == 'com_rseventspro') {
			$Itemid = $current;
		}
		
		if ($Itemid) {
			$url .= (strpos($url, '?') === false) ? '?Itemid='.$Itemid : '&Itemid='.$Itemid;
		}
		
		return JRoute::_($url, $xhtml);
	}
	
	// Get page params
	public static function getParams() {
		$itemid = JFactory::getApplication()->input->getInt('Itemid',0);
		$params = null;
		
		if ($itemid) {
			$menu = JFactory::getApplication()->getMenu();
			$active = $menu->getItem($itemid);
			if ($active) {
				$params = $active->params;
			}
		}
		
		if (empty($params)) {
			$params = JFactory::getApplication()->getParams();
		}
		
		return $params;
	}
	
	// Get cached groups
	public static function getCachedGroupDetails($all = false) {
		static $group_acls_cache;
		if (empty($group_acls_cache)) {
			$db		= JFactory::getDbo();
			$query	= $db->getQuery(true);
			
			$query->clear();
			$all ? $query->select('*') : $query->select($db->qn('id'))->select($db->qn('jgroups'))->select($db->qn('jusers'));
			$query->from($db->qn('#__rseventspro_groups'));
			$query->order($db->qn('id').' ASC');
			
			$db->setQuery($query);
			$group_acls_cache = $db->loadObjectList();
		}
		
		return $group_acls_cache;
	}
	
	// Get user permissions
	public static function permissions() {
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);
		$user	= JFactory::getUser();
		$userid = $user->get('id');
		
		static $groups;
		if (empty($groups)) {
			$groups = rseventsproHelper::getCachedGroupDetails(true);
		}
		
		$permissions = array('can_edit_events' => 0, 'can_post_events' => 0, 'can_repeat_events' => 0, 'event_moderation' => 1, 'can_delete_events' => 0, 'can_register' => 1, 'can_unsubscribe' => 1, 'can_download' => 1, 'can_upload' => 0, 'can_create_categories' => 0, 'tag_moderation' => 0, 'can_add_locations' => 0, 'can_edit_locations' => 1, 'can_approve_events' => 0, 'can_approve_tags' => 0);
		
		if (!empty($groups)) {
			$rsgroups = array();
			
			foreach ($groups as $group) {
				if (!empty($group->jgroups)) {
					$registry = new JRegistry;
					$registry->loadString($group->jgroups);
					$joomlagroups = $registry->toArray();
					
					if (!empty($joomlagroups)) {
						$user_groups = JAccess::getGroupsByUser($user->id);
						
						if (!$user->guest) {
							foreach ($user_groups as $key => $value) {
								if ($value == 1) {
									unset($user_groups[$key]);
								}
							}
						}
						
						if (!empty($joomlagroups)) {
							foreach($user_groups as $ugroup) {
								if (in_array($ugroup,$joomlagroups)) {
									$rsgroups[] = $group;
								}
							}
						}
					}
				}
				
				if (!empty($group->jusers)) {
					$registry = new JRegistry;
					$registry->loadString($group->jusers);
					$joomlausers = $registry->toArray();
					
					if (!empty($joomlausers)) {
						if (in_array($userid,$joomlausers)) {
							$rsgroups[] = $group;
						}
					}
				}
			}
			
			if (!empty($rsgroups)) {
				foreach ($rsgroups as $group) {
					$group =  get_object_vars($group);
					foreach($group as $name => $value) {
						if (isset($permissions[$name])) {
							$permissions[$name] = $value;
						}
					}
				}
			}
		}
		
		return $permissions;
	}
	
	// Can I view this event ?
	public static function canview($id) {
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);
		$user	= JFactory::getUser();
		
		// If the user is an admin we let him view the event by default
		if (self::admin()) 
			return true;
		
		// Get the group list that are restricted to view this event
		$query->clear()
			->select($db->qn('g.jgroups'))->select($db->qn('g.jusers'))
			->from($db->qn('#__rseventspro_groups','g'))
			->join('left', $db->qn('#__rseventspro_taxonomy','tx').' ON '.$db->qn('tx.id').' = '.$db->qn('g.id'))
			->where($db->qn('tx.ide').' = '.(int) $id)
			->where($db->qn('tx.type').' = '.$db->q('groups'));
		
		$db->setQuery($query);
		$groups = $db->loadObjectList();
		
		if (!empty($groups)) {
			// Initialize the can view variable to true
			$canview = true;
			
			foreach ($groups as $group) {
				// Parse joomla groups
				if (!empty($group->jgroups)) {
					$registry = new JRegistry;
					$registry->loadString($group->jgroups);
					$jgroups = $registry->toArray();
					
					if (!empty($jgroups)) {
						// Get current users Joomla! groups
						$groups = JAccess::getGroupsByUser($user->get('id'));
						
						// If the user is logged in remove the Public group
						if (!$user->get('guest')) {
							foreach ($groups as $key => $value) {
								if ($value == 1) {
									unset($groups[$key]);
								}
							}
						}
						
						// If the user has multiple groups parse them
						foreach ($groups as $gr) {
							// If one of the users group is in the restricted groups we return false;
							if (in_array($gr,$jgroups)) {
								$canview = false;
							}
						}
					}
				}
				
				// Parse user ids
				// User id check overwrites the user group return
				if (!empty($group->jusers)) {
					$registry = new JRegistry;
					$registry->loadString($group->jusers);
					$jusers = $registry->toArray();
					
					if (!empty($jusers)) {
						$userid = $user->get('id');
						
						// If the current user id is in the restricted groups then we return false;
						if (in_array($userid,$jusers)) {
							$canview = false;
						}
					}
				}
				
				if (empty($group->jgroups) && empty($group->jusers)) 
					$canview = true;
			}
			
			return $canview;
		}
		
		return true;
	}
	
	// Is the current user an admin
	public static function admin() {
		$user	= JFactory::getUser();
		$admins = self::getAdminUsers();
		
		if (in_array($user->get('id'), $admins))
			return true;
		
		return false;
	}
	
	public static function getAdminGroups() {
		if (!is_array(self::$groups)) {
			$db 	= JFactory::getDbo();
			$query 	= $db->getQuery(true);
			$query->select($db->qn('id'))
				  ->from($db->qn('#__usergroups'));
			$db->setQuery($query);
			$groups = $db->loadColumn();
			
			self::$groups = array();
			foreach ($groups as $group_id) {
				if (JAccess::checkGroup($group_id, 'core.admin'))
					self::$groups[] = $group_id;
			}
			
			self::$groups = array_unique(self::$groups);
		}
		
		return self::$groups;
	}
	
	public static function getAdminUsers() {
		if (!is_array(self::$users)) {
			self::$users = array();
			
			if ($groups	= self::getAdminGroups()) {
				$db 	= JFactory::getDbo();
				$query 	= $db->getQuery(true);
				$query->select($db->qn('u.id'))
					  ->from($db->qn('#__user_usergroup_map','m'))
					  ->join('right', $db->qn('#__users','u').' ON ('.$db->qn('u.id').' = '.$db->qn('m.user_id').')')
					  ->where($db->qn('m.group_id').' IN ('.implode(',', $groups).')')
					  ->group($db->qn('u.id'));
				$db->setQuery($query);
				self::$users = $db->loadColumn();
			}
		}
		
		return self::$users;
	}
	
	// Check to see if the event is full
	public static function eventisfull($id) {
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);
		$array	= array();
		
		static $cache = array();
		
		if (empty($cache[$id])) {
			$query->clear()
				->select($db->qn('registration'))->select($db->qn('overbooking'))
				->select($db->qn('max_tickets'))->select($db->qn('max_tickets_amount'))
				->from($db->qn('#__rseventspro_events'))
				->where($db->qn('id').' = '.(int) $id);
			
			$db->setQuery($query);
			$cache[$id] = $db->loadObject();
		}
		
		if (isset($cache[$id]) && ($cache[$id]->registration == 0 || $cache[$id]->overbooking == 1)) 
			return false;
		
		//get the tickets 
		$query->clear()
			->select($db->qn('id'))->select($db->qn('price'))
			->select($db->qn('seats'))->select($db->qn('user_seats'))
			->from($db->qn('#__rseventspro_tickets'))
			->where($db->qn('ide').' = '.(int) $id);
		
		$db->setQuery($query);
		$tickets = $db->loadObjectList();
		
		$max = $cache[$id]->max_tickets == 1 ? $cache[$id]->max_tickets_amount : false;
		
		//parse tickets
		if(!empty($tickets)) {
			if ($max) {
				$arr_tickets = array();
				foreach($tickets as $ticket)
					$arr_tickets[] = $ticket->id;
				
				$query->clear()
					->select('SUM('.$db->qn('ut.quantity').')')
					->from($db->qn('#__rseventspro_user_tickets','ut'))
					->join('left', $db->qn('#__rseventspro_users','u').' ON '.$db->qn('u.id').' = '.$db->qn('ut.idt'))
					->where($db->qn('ut.idt').' IN ('.implode(',',$arr_tickets).')')
					->where($db->qn('u.state').' IN (0,1)');
				
				$db->setQuery($query);
				$quantity = $db->loadResult();
				
				if ($quantity && $quantity >= $max) 
					return true;
			}
			
			foreach($tickets as $ticket) {
				if (!empty($ticket->seats) && !empty($ticket->user_seats)) {
					$query->clear()
						->select('SUM('.$db->qn('ut.quantity').')')
						->from($db->qn('#__rseventspro_user_tickets','ut'))
						->join('left', $db->qn('#__rseventspro_users','u').' ON '.$db->qn('u.id').' = '.$db->qn('ut.ids'))
						->where($db->qn('ut.idt').' = '.(int) $ticket->id)
						->where($db->qn('u.state').' IN (0,1)');
				
					$db->setQuery($query);
					$quantity = $db->loadResult();
					
					if($ticket->seats <= $quantity) 
						continue;
				}
				$array[] = $ticket;
			}
		} else {
			if ($max) {
				$query->clear()
					->select('COUNT('.$db->qn('id').')')
					->from($db->qn('#__rseventspro_users'))
					->where($db->qn('ide').' = '.(int) $id)
					->where($db->qn('state').' IN (0,1)');
				
				$db->setQuery($query);
				$tickets = $db->loadResult();
				
				if ($tickets) {
					if ($tickets >= $max)
						return true;
					else return false;
				} else return false;
			} else return false;
		}
		
		return empty($array) ? true : false;
	}
	
	// Get a list of excluded events
	public static function excludeEvents() {
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);
		$ids	= array();
		
		$query->clear()
			->select($db->qn('ide'))
			->from($db->qn('#__rseventspro_taxonomy'))
			->where($db->qn('type').' = '.$db->q('groups'));
		
		$db->setQuery($query);
		if ($eventids = $db->loadColumn()) {
			foreach ($eventids as $id) {
				$query->clear()
					->select($db->qn('owner'))
					->from($db->qn('#__rseventspro_events'))
					->where($db->qn('id').' = '.(int) $id);
				
				$db->setQuery($query);
				$owner = (int) $db->loadResult();
				
				if (!rseventsproHelper::canview($id) && $owner != JFactory::getUser()->get('id')) {
					$ids[] = $id;
				}
			}
			
			if (!empty($ids)) {
				JArrayHelper::toInteger($ids);
				$ids = array_unique($ids);
			}
		}
		
		return $ids;
	}
	
	// Prepare all event details
	public static function details($id, $itemid = null) {
		$u			= JURI::getInstance();
		$db			= JFactory::getDbo();
		$query		= $db->getQuery(true);
		$app		= JFactory::getApplication();
		$root		= $u->toString(array('scheme','host'));
		$itemid		= !is_null($itemid) ? '&Itemid='.$itemid : '';
		$return		= array();
		
		$query->clear()
			->select($db->qn('e.id'))->select($db->qn('e.name'))->select($db->qn('e.start'))->select($db->qn('e.end'))
			->select($db->qn('e.description'))->select($db->qn('e.owner'))->select($db->qn('e.URL'))->select($db->qn('e.email'))
			->select($db->qn('e.phone'))->select($db->qn('e.registration'))->select($db->qn('e.comments'))->select($db->qn('e.icon'))->select($db->qn('e.featured'))
			->select($db->qn('e.start_registration'))->select($db->qn('e.end_registration'))->select($db->qn('e.unsubscribe_date'))->select($db->qn('e.payments'))
			->select($db->qn('e.options'))->select($db->qn('e.archived'))->select($db->qn('e.published'))->select($db->qn('e.completed'))->select($db->qn('e.allday'))
			->select($db->qn('e.show_registered'))->select($db->qn('e.sid'))->select($db->qn('l.id','locationid'))->select($db->qn('l.name','location'))
			->select($db->qn('l.url','locationlink'))->select($db->qn('l.address'))->select($db->qn('l.description','ldescription'))->select($db->qn('l.coordinates'))
			->select($db->qn('l.published','lpublished'))
			->from($db->qn('#__rseventspro_events','e'))
			->join('left',$db->qn('#__rseventspro_locations','l').' ON '.$db->qn('e.location').' = '.$db->qn('l.id'))
			->where($db->qn('e.id').' = '.(int) $id);
		
		$db->setQuery($query);
		$event = $db->loadObject();
		
		if (!empty($event->URL)) {
			if (substr($event->URL,0,4) != 'http')
				$event->URL = 'http://'.$event->URL;
		}
		
		// Set owner name
		$event->ownername = rseventsproHelper::getUser($event->owner);
		
		// Set the owner profile link
		$event->ownerprofile = rseventsproHelper::getProfile('owner', $event->owner);
		
		// Content trigger
		$event->description = JHtml::_('content.prepare',$event->description);
		
		// Event options
		if ($event->options) {
			$registry = new JRegistry;
			$registry->loadString($event->options);
			$event->options = $registry->toArray();
		}
		
		// Add event
		$return['event'] = $event;
		
		static $categs = array();
		if (empty($categs)) {
			$query->clear()
				->select($db->qn('tx.ide'))->select($db->qn('c.id'))
				->select($db->qn('c.title'))->select($db->qn('c.params'))
				->from($db->qn('#__categories','c'))
				->join('left',$db->qn('#__rseventspro_taxonomy','tx').' ON '.$db->qn('tx.id').' = '.$db->qn('c.id'))
				->where($db->qn('tx.type').' = '.$db->q('category'))
				->where($db->qn('c.published').' = 1')
				->where($db->qn('c.extension').' = '.$db->q('com_rseventspro'));
			
			if ($app->isSite()) {
				if (JLanguageMultilang::isEnabled()) {
					$query->where('c.language IN ('.$db->q(JFactory::getLanguage()->getTag()).','.$db->q('*').')');
				}
				
				$user	= JFactory::getUser();
				$groups	= implode(',', $user->getAuthorisedViewLevels());
				$query->where('c.access IN ('.$groups.')');
			}
			
			$db->setQuery($query);
			$allcategories = $db->loadObjectList();
			
			if (!empty($allcategories)) {
				foreach($allcategories as $c) {
					$categs[$c->ide][] = $c;
				}
			}
		}
		
		$categories = array();
		if (!empty($categs[$id])) {
			foreach ($categs[$id] as $cat) {
				if (rseventsproHelper::getConfig('color','int')) {
					$color = '';
					if ($cat->params) {
						$registry = new JRegistry;
						$registry->loadString($cat->params);
						$color = $registry->get('color');
					}
					
					$style = $color ? 'style="color: '.$color.'"' : '';
				} else $style = '';
				
				$categories[] = '<a href="'.$root.rseventsproHelper::route('index.php?option=com_rseventspro&category='.rseventsproHelper::sef($cat->id,$cat->title).$itemid).'" class="rs_cat_link" '.$style.'>'.$cat->title.'</a>';
			}
		}
		
		// Add categories
		$return['categories'] = !empty($categories) ? implode(' , ',$categories) : '';
		
		static $eventtags = null;
		if (!is_array($eventtags)) {
			$eventtags = array();
			
			$query->clear()
				->select($db->qn('tx.ide'))->select($db->qn('t.id'))->select($db->qn('t.name'))
				->from($db->qn('#__rseventspro_tags','t'))
				->join('left',$db->qn('#__rseventspro_taxonomy','tx').' ON '.$db->qn('tx.id').' = '.$db->qn('t.id'))
				->where($db->qn('tx.type').' = '.$db->q('tag'))
				->where($db->qn('t.published').' = 1');
			
			$db->setQuery($query);
			$alltags = $db->loadObjectList();
			
			if (!empty($alltags)) {
				foreach ($alltags as $t) {
					$eventtags[$t->ide][] = $t;
				}
			}
		}
		
		$tags = array();
		if (!empty($eventtags[$id])) {
			foreach ($eventtags[$id] as $tag) {
				$tags[] = '<a href="'.$root.rseventsproHelper::route('index.php?option=com_rseventspro&tag='.rseventsproHelper::sef($tag->id,$tag->name).$itemid).'" class="rs_tag_link">'.$tag->name.'</a>';
			}
		}
		
		// Add tags
		$return['tags'] = !empty($tags) ? implode(' , ',$tags) : '';
		
		// Add icons
		$image_b = '';
		$image_b_pdf = '';
		if (!empty($event->icon) && file_exists(JPATH_SITE.'/components/com_rseventspro/assets/images/events/thumbs/b_'.$event->icon)) {
			$image_b = JURI::root().'components/com_rseventspro/assets/images/events/thumbs/b_'.$event->icon.'?nocache='.uniqid('');
			$image_b_pdf = JPATH_SITE.'/components/com_rseventspro/assets/images/events/thumbs/b_'.$event->icon;
		}
		
		$image_s = '';
		$image_s_pdf = '';
		if (!empty($event->icon) && file_exists(JPATH_SITE.'/components/com_rseventspro/assets/images/events/thumbs/b_'.$event->icon)) {
			$image_s = JURI::root().'components/com_rseventspro/assets/images/events/thumbs/s_'.$event->icon.'?nocache='.uniqid('');
			$image_s_pdf = JPATH_SITE.'/components/com_rseventspro/assets/images/events/thumbs/s_'.$event->icon;
		}
		
		$image = '';
		$image_pdf = '';
		if (!empty($event->icon) && file_exists(JPATH_SITE.'/components/com_rseventspro/assets/images/events/'.$event->icon)) {
			$image = JURI::root().'components/com_rseventspro/assets/images/events/'.$event->icon;
			$image_pdf = JPATH_SITE.'/components/com_rseventspro/assets/images/events/'.$event->icon;
		}
		
		$return['image_b'] = $image_b;
		$return['image_b_pdf'] = $image_b_pdf;
		$return['image_s'] = $image_s;
		$return['image_s_pdf'] = $image_s_pdf;
		$return['image'] = $image;
		$return['image_pdf'] = $image_pdf;
		
		// Add tickets	
		static $eventtickets = array();
		if (empty($eventtickets)) {
			$query->clear()
				->select($db->qn('id'))->select($db->qn('ide'))->select($db->qn('name'))
				->select($db->qn('price'))->select($db->qn('seats'))
				->from($db->qn('#__rseventspro_tickets'));
			
			$db->setQuery($query);
			$alltickets = $db->loadObjectList();
			
			if (!empty($alltickets)) {
				foreach ($alltickets as $ticket) {
					$eventtickets[$ticket->ide][] = $ticket; 
				}
			}
		}
		
		$arr = array();
		$tickets = '';
		if (!empty($eventtickets[$id])) {
			foreach ($eventtickets[$id] as $ticket) {
				$query->clear()
					->select('SUM('.$db->qn('quantity').')')
					->from($db->qn('#__rseventspro_user_tickets'))
					->where($db->qn('idt').' = '.(int) $ticket->id);
				
				$db->setQuery($query);
				$purchased = $db->loadResult();
				
				if ($ticket->seats > 0) {
					$available = $ticket->seats - $purchased;
					if ($available > 0) {
						if ($ticket->price > 0) {
							$arr[] = $available. ' x '.$ticket->name.' ('.rseventsproHelper::currency($ticket->price).')';
						} else {
							$arr[] = $available. ' x '.$ticket->name.' ('.JText::_('COM_RSEVENTSPRO_GLOBAL_FREE').')';
						}
					}
				} else {
					if ($ticket->price > 0) {
						$arr[] = JText::_('COM_RSEVENTSPRO_GLOBAL_UNLIMITED'). ' '.$ticket->name.' ('.rseventsproHelper::currency($ticket->price).')';
					} else {
						$arr[] = JText::_('COM_RSEVENTSPRO_GLOBAL_UNLIMITED'). ' '.$ticket->name.' ('.JText::_('COM_RSEVENTSPRO_GLOBAL_FREE').')';
					}
				}
			}
		}
		
		if (!empty($arr)) {
			$tickets .= JText::_('COM_RSEVENTSPRO_GLOBAL_AVAILABLE_TICKETS');
			$tickets .= '<ul class="rs_av_tickets">';
			foreach ($arr as $ticket)
				$tickets .= '<li>'.$ticket.'</li>';
			$tickets .= '</ul>';
		}
		
		$return['tickets'] = $tickets;
		$return['ticket_info'] = $arr;
		
		// Add event files
		$return['files'] = rseventsproHelper::getEventFiles($event->id);
		
		// Get event repeated events
		$query->clear()
			->select($db->qn('id'))->select($db->qn('name'))->select($db->qn('start'))
			->select($db->qn('end'))->select($db->qn('allday'))
			->from($db->qn('#__rseventspro_events'))
			->where($db->qn('parent').' = '.(int) $event->id)
			->where($db->qn('completed').' = 1')
			->where($db->qn('published').' = 1');
		
		$db->setQuery($query);
		$events = $db->loadObjectList();
		
		$return['repeats'] = $events;
		
		$app->triggerEvent('rsepro_details',array(array('details'=>&$return)));
		
		return $return;
	}
	
	// Get the name of the user
	public static function getUser($uid, $type = 'owner') {
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);
		$user	= JFactory::getUser($uid);
		$option = $type == 'guest' ? rseventsproHelper::getConfig('user_display','int') : rseventsproHelper::getConfig('event_owner','int');
		
		if (!$uid) {
			return JText::_('COM_RSEVENTSPRO_GLOBAL_GUEST');
		}
		
		if ($option == 0) {
			return $user->name;
		} elseif ($option == 1) {
			return $user->username;
		} elseif ($option == 2) {
			if (file_exists(JPATH_SITE.'/components/com_community/libraries/core.php')) {
				include_once JPATH_SITE.'/components/com_community/libraries/core.php';
				$user  = CFactory::getUser($uid);
				return $user->getDisplayName();
			} else return $user->name;
		} elseif ($option == 3) {
			$query->clear()
				->select($db->qn('firstname'))->select($db->qn('middlename'))->select($db->qn('lastname'))
				->from($db->qn('#__comprofiler'))
				->where($db->qn('user_id').' = '.(int) $uid);
			
			$db->setQuery($query);
			$details = $db->loadObject();
			
			$name = $details->firstname.' '.$details->middlename.' '.$details->lastname;
			return !empty($name) ? $name : $user->name;
		} else return $user->name;
	}
	
	// Get event files
	public static function getEventFiles($id) {
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);
		
		static $files = null;
		if (!is_array($files)) {
			$files = array();
			
			$query->clear()
				->select('*')
				->from('#__rseventspro_files');
			
			$db->setQuery($query);
			$allfiles = $db->loadObjectList();
			
			if (!empty($allfiles)) {
				foreach ($allfiles as $f) {
					$files[$f->ide][] = $f;
				}
			}
		}
		
		if (!empty($files[$id])) {
			$array			= array();
			$permissions	= rseventsproHelper::permissions();
			$canDownload	= !empty($permissions['can_download']) || rseventsproHelper::admin() ? 1 : 0 ;
			$now			= rseventsproHelper::date('now',null,false,true);
			$now->setTZByID($now->getTZID());
			$now->convertTZ(new RSDate_Timezone('GMT'));
			$registered		= rseventsproHelper::registered($id);
			
			$query->clear()
				->select($db->qn('start'))->select($db->qn('end'))
				->from($db->qn('#__rseventspro_events'))
				->where($db->qn('id').' = '.(int) $id);
			
			$db->setQuery($query);
			$dates = $db->loadObject();
			
			$start = rseventsproHelper::date($dates->start,null,false,true);
			$start->setTZByID($start->getTZID());
			$start->convertTZ(new RSDate_Timezone('GMT'));
			
			$end = rseventsproHelper::date($dates->end,null,false,true);
			$end->setTZByID($end->getTZID());
			$end->convertTZ(new RSDate_Timezone('GMT'));
			
			//Determine in which interval you are (before/during/after)
			if($now < $start) $interval = 'before';
			if($now >= $start && $now < $end) $interval = 'during';
			if($now >= $end) $interval = 'after';
			
			foreach ($files[$id] as $file) {
				if(
					(
						!$registered && $canDownload && 
						(($interval == 'before' && $file->permissions[0]==1) || ($interval == 'during' && $file->permissions[1]==1) || ($interval == 'after' && $file->permissions[2]==1))
					)
				||
					(
						$registered && $canDownload && 
						(($interval == 'before' && $file->permissions[3]==1) || ($interval == 'during' && $file->permissions[4]==1) || ($interval == 'after' && $file->permissions[5]==1))
					)
				)
					$array[] = '<li><a href="'.JURI::root().'components/com_rseventspro/assets/images/files/'.$file->location.'" target="_blank">'.$file->name.'</a></li>';
			}
		}
		
		return !empty($array) ? '<ul class="rs_files">'.implode('',$array).'</ul>' : '';
	}
	
	// Check if the current user is registered to the event
	public static function registered($id) {
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);
		
		$query->clear()
			->select('COUNT('.$db->qn('id').')')
			->from($db->qn('#__rseventspro_users'))
			->where($db->qn('ide').' = '.(int) $id)
			->where($db->qn('idu').' = '.(int) JFactory::getUser()->get('id'));
		
		$db->setQuery($query);
		return $db->loadResult() ? true : false;
	}
	
	// Check to see if the event is ongoing
	public static function ongoing($id) {
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);
		$now	= JFactory::getDate()->toUnix();
		
		$query->clear()
			->select($db->qn('start'))->select($db->qn('end'))->select($db->qn('allday'))
			->from($db->qn('#__rseventspro_events'))
			->where($db->qn('id').' = '.(int) $id);
		
		$db->setQuery($query);
		$event = $db->loadObject();
		
		if ($event->allday) {
			$start	= rseventsproHelper::date($event->start,null,false,true);
			$start->setTZByID($start->getTZID());
			$start->convertTZ(new RSDate_Timezone('GMT'));
			$start	= $start->getDate(RSDATE_FORMAT_UNIXTIME);
			
			$end = rseventsproHelper::date($event->start,null,false,true);
			$end->addSeconds(86400);
			$end->setTZByID($end->getTZID());
			$end->convertTZ(new RSDate_Timezone('GMT'));
			$end	= $end->getDate(RSDATE_FORMAT_UNIXTIME);
			
			if ($start <= $now && $end >= $now)
				return true;
			
		} else {
			$start	= rseventsproHelper::date($event->start,null,false,true);
			$start->setTZByID($start->getTZID());
			$start->convertTZ(new RSDate_Timezone('GMT'));
			$start	= $start->getDate(RSDATE_FORMAT_UNIXTIME);
			
			$end 	= rseventsproHelper::date($event->end,null,false,true);
			$end->setTZByID($end->getTZID());
			$end->convertTZ(new RSDate_Timezone('GMT'));
			$end	= $end->getDate(RSDATE_FORMAT_UNIXTIME);
			
			if ($start <= $now && $end >= $now)
				return true;
		}
		
		return false;
	}
	
	// Check an event
	public static function check($id) {
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);
		$user	= JFactory::getUser();
		
		static $cachecount = array();
		static $cachepublish = array();
		
		// Does the event exist ?
		if (!isset($cachecount[$id])) {
			$query->clear()
				->select('COUNT('.$db->qn('id').')')
				->from($db->qn('#__rseventspro_events'))
				->where($db->qn('id').' = '.(int) $id);
			
			$db->setQuery($query);
			$count = $db->loadResult();
			$cachecount[$id] = $count;
		}
		
		if (!$count) 
			return false;
		
		// Is the event published ? 
		if (!isset($cachepublish[$id])) {
			$query->clear()
				->select($db->qn('published'))
				->from($db->qn('#__rseventspro_events'))
				->where($db->qn('id').' = '.(int) $id);
			
			$db->setQuery($query);
			$published = $db->loadResult();
			
			$cachepublish[$id] = $published;
		}
		
		$query->clear()
			->select($db->qn('owner'))
			->from($db->qn('#__rseventspro_events'))
			->where($db->qn('id').' = '.(int) $id);
		
		$db->setQuery($query);
		$owner = (int) $db->loadResult();
		
		if ($owner != $user->get('id')) {
			if (!$cachepublish[$id] && !rseventsproHelper::admin())
				return false;
		}
		
		return true;
	}
	
	// Get the number of repeats
	public static function getRepeats($id) {
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		
		$query->clear()
			->select('COUNT('.$db->qn('id').')')
			->from($db->qn('#__rseventspro_events'))
			->where($db->qn('parent').' = '.(int) $id);
		
		$db->setQuery($query);
		return $db->loadResult();
	}
	
	// Check for RSform!Pro plugin
	public static function rsform() {
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);
		
		$query->clear()
			->select($db->qn('enabled'))
			->from($db->qn('#__extensions'))
			->where($db->qn('type').' = '.$db->q('plugin'))
			->where($db->qn('folder').' = '.$db->q('system'))
			->where($db->qn('element').' = '.$db->q('rsfprseventspro'));
			
		$db->setQuery($query);
		$enabled = $db->loadResult();
		
		if ($enabled && file_exists(JPATH_SITE.'/plugins/system/rsfprseventspro/rsfprseventspro.php'))
			return true;
		
		return false;
	}
	
	// Load RSForm!Pro form
	public static function loadRSForm($fid) {
		$output = '';
		$helper = JPATH_ADMINISTRATOR.'/components/com_rsform/helpers/rsform.php';
		
		if(file_exists($helper)) {
			JFactory::getLanguage()->load('com_rsform',JPATH_SITE);
			
			require_once($helper);
			$output = RSFormProHelper::displayForm($fid);
		}

		return $output;
	}
	
	// Shorten text javascript
	public static function shortenjs($string, $id, $max = 255, $type = 1) {
		$text = '';
		
		if ($type == 1) {		
			$string = strip_tags($string);
			$size   = rseventsproHelper::getConfig('descr_length','int');
			
			if (!empty($size)) 
				$max = $size;
			
			$string_length = mb_strlen($string);		
			if ($max >= $string_length) 
				return $string;
			
			$intro = mb_substr($string,0,$max);
			$extra = mb_substr($string,$max,$string_length);
			$extra .= '<a href="javascript:void(0);" class="rsepro_minus" onclick="rsepro_description_off('.$id.')" onmouseover="document.getElementById(\'minusimg'.$id.'\').src=\''.JURI::root().'components/com_rseventspro/assets/images/minus_02.gif\'" onmouseout="document.getElementById(\'minusimg'.$id.'\').src=\''.JURI::root().'components/com_rseventspro/assets/images/minus_01.gif\'">';
			$extra .= '<img id="minusimg'.$id.'" src="'.JURI::root().'components/com_rseventspro/assets/images/minus_01.gif" alt="" /></a>';
			
			$text .= $intro;
			$text .= '<a id="rsehref'.$id.'" href="javascript:void(0);" class="rsepro_plus" onclick="rsepro_description_on('.$id.')" onmouseover="document.getElementById(\'plusimg'.$id.'\').src=\''.JURI::root().'components/com_rseventspro/assets/images/plus_02.gif\'" onmouseout="document.getElementById(\'plusimg'.$id.'\').src=\''.JURI::root().'components/com_rseventspro/assets/images/plus_01.gif\'">';
			$text .= '<img id="plusimg'.$id.'" src="'.JURI::root().'components/com_rseventspro/assets/images/plus_01.gif" alt="" /></a>';
			$text .= '<div id="rsedescription'.$id.'" class="rsepro_extra_off">'.$extra.'</div>';
		} else {
			$pattern = '#<hr\s+id=("|\')system-readmore("|\')\s*\/*>#i';
			
			if (preg_match($pattern, $string))
			{
				list($introtext, $fulltext) = preg_split($pattern, $string, 2);			
				$text .= $introtext;
				
				if ($fulltext) {
					$extra = $fulltext;
					$extra .= '<a href="javascript:void(0);" class="rsepro_minus" onclick="rsepro_description_off('.$id.')" onmouseover="document.getElementById(\'minusimg'.$id.'\').src=\''.JURI::root().'components/com_rseventspro/assets/images/minus_02.gif\'" onmouseout="document.getElementById(\'minusimg'.$id.'\').src=\''.JURI::root().'components/com_rseventspro/assets/images/minus_01.gif\'">';
					$extra .= '<img id="minusimg'.$id.'" src="'.JURI::root().'components/com_rseventspro/assets/images/minus_01.gif" alt="" /></a>';
					
					$text .= '<a id="rsehref'.$id.'" href="javascript:void(0);" class="rsepro_plus" onclick="rsepro_description_on('.$id.')" onmouseover="document.getElementById(\'plusimg'.$id.'\').src=\''.JURI::root().'components/com_rseventspro/assets/images/plus_02.gif\'" onmouseout="document.getElementById(\'plusimg'.$id.'\').src=\''.JURI::root().'components/com_rseventspro/assets/images/plus_01.gif\'">';
					$text .= '<img id="plusimg'.$id.'" src="'.JURI::root().'components/com_rseventspro/assets/images/plus_01.gif" alt="" /></a>';
					$text .= '<div id="rsedescription'.$id.'" class="rsepro_extra_off">'.$extra.'</div>';
				}
			} else {
				$text .= $string;
			}
		}
		
		return $text;
	}
	
	// Remove the readmore marker
	public static function removereadmore($text) {
		$pattern = '#<hr\s+id=("|\')system-readmore("|\')\s*\/*>#i';
		return preg_replace($pattern,'',$text);
	}
	
	// Get event options
	public static function options($id) {
		require_once JPATH_SITE.'/components/com_rseventspro/helpers/events.php';
		$event		= RSEvent::getInstance($id);
		$defaults	= $event->getDefaultOptions();
		$event		= $event->getEvent();
		$options	= $event->get('options');
		
		$registry = new JRegistry;
		$registry->loadString($options);
		if ($options = $registry->toArray()) {
			foreach ($defaults as $name => $value) {
				if (isset($options[$name])) {
					$defaults[$name] = $options[$name];
				}
			}
		}
		
		return $defaults;
	}
	
	// Display the gallery images
	public static function gallery($type, $id) {
		if (rseventsproHelper::isGallery()) {
			$db		= JFactory::getDbo();
			$query	= $db->getQuery(true);
			$registry = new JRegistry('gallery');
			
			require_once JPATH_ADMINISTRATOR.'/components/com_rsmediagallery/helpers/integration.php';
			$gallery = RSMediaGalleryIntegration::getInstance();
			
			$query->clear()
				->select($db->qn('gallery_tags'));
			
			if ($type == 'event')
				$query->from($db->qn('#__rseventspro_events'));
			else
				$query->from($db->qn('#__rseventspro_locations'));
			
			$query->where($db->qn('id').' = '.(int) $id);
			
			$db->setQuery($query);
			$tags = $db->loadResult();
			
			if ($tags) {
				$reg = new JRegistry;
				$reg->loadString($tags);
				$tags = $reg->toArray();
			}
			
			$params = rseventsproHelper::getConfig('gallery_params');
			$registry->loadString($params);
			
			return $gallery->display($tags, $registry);
		}
	}
	
	// Get the current user group
	public static function getUserGroups() {
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);
		$user = JFactory::getUser();
		$rsgroups = array();
		
		$userid = $user->get('id');
		
		static $groups;
		if (empty($groups))
			$groups = rseventsproHelper::getCachedGroupDetails();
		
		if (!empty($groups)) {
			foreach ($groups as $group) {
				if (!empty($group->jgroups)) {
					$registry = new JRegistry;
					$registry->loadString($group->jgroups);
					$joomlagroups = $registry->toArray();
					
					if (!empty($joomlagroups)) {
						$user_groups = JAccess::getGroupsByUser($user->id);
							
						if (!$user->guest) {
							foreach ($user_groups as $key => $value) {
								if ($value == 1) {
									unset($user_groups[$key]);
								}
							}
						}
							
						if (!empty($joomlagroups)) {
							foreach($user_groups as $ugroup) {
								if (in_array($ugroup,$joomlagroups)) {
									$rsgroups[] = $group->id;
								}
							}
						}
					}
				}
				
				if (!empty($group->jusers)) {
					$registry = new JRegistry;
					$registry->loadString($group->jusers);
					$joomlausers = $registry->toArray();
					
					if (!empty($joomlausers)) {
						if (in_array($userid,$joomlausers)) {
							$rsgroups[] = $group->id;
						}
					}
				}
			}
		}
		
		if (!empty($rsgroups)) {
			JArrayHelper::toInteger($rsgroups);
			$rsgroups = array_unique($rsgroups);
		}
		
		return $rsgroups;
	}
	
	// Set event metadata
	public static function metas($event) {
		$doc		= JFactory::getDocument();
		$options	= rseventsproHelper::options($event->id);
		
		if (!empty($options['enable_fb_like']) || rseventsproHelper::getConfig('event_comment','int') == 1) {
			$doc->addCustomTag('<meta property="fb:app_id" content="340486642645761" />');
			$doc->addCustomTag('<meta charset="utf-8">');
			$doc->addCustomTag('<meta property="og:url" content="'.htmlentities(rseventsproHelper::shareURL($event->id,$event->name,false)).'" />');
			
			if (!empty($event->description)) {
				$content = strip_tags($event->description);
				$content = trim(substr($content,0,255));
				$content .= ' [...]';
				$content = str_replace(array("\r","\n"),' ',$content);
				$doc->addCustomTag('<meta property="og:description" content="'.htmlentities($content,ENT_COMPAT,'UTF-8').'" />');
			}
			$doc->addCustomTag('<meta property="og:title" content="'.htmlentities($event->name,ENT_COMPAT,'UTF-8').'" />');
			$doc->addCustomTag('<meta property="og:type" content="article" />');
			
			if (!empty($event->icon) && file_exists(JPATH_SITE.'/components/com_rseventspro/assets/images/events/thumbs/s_'.$event->icon))			
				$doc->addCustomTag('<meta property="og:image" content="'.JURI::root().'components/com_rseventspro/assets/images/events/thumbs/s_'.$event->icon.'" />');
		}
		
		// Set a default page title
		$doc->setTitle($doc->getTitle().' - '.$event->name);
		if (trim($event->metaname) != '') {
			$event->metaname = str_replace('{eventname}',$event->name,$event->metaname);
			$doc->setTitle($event->metaname);
		}
		
		if(trim($event->metakeywords) !='') $doc->setMetaData('keywords',$event->metakeywords);
		if(trim($event->metadescription) !='') $doc->setDescription($event->metadescription);
		
		return true;
	}
	
	// Get the share link
	public static function shareURL($id, $name, $itemid = false) {
		$root = JURI::getInstance()->toString(array('scheme','host'));
		$itemid = $itemid ? '&Itemid=999999999' : '';
		
		return $root.rseventsproHelper::route('index.php?option=com_rseventspro&layout=show&id='.rseventsproHelper::sef($id,$name).$itemid);
	}
	
	// Set page title
	public static function metatitle() {
		$params	= rseventsproHelper::getParams();
		$config = JFactory::getConfig();
		$title	= $params->get('page_title', '');
		
		if (empty($title)) {
			$title = $config->get('sitename');
		} elseif ($config->get('sitename_pagetitles', 0) == 1) {
			$title = JText::sprintf('JPAGETITLE', $config->get('sitename'), $title);
		} elseif ($config->get('sitename_pagetitles', 0) == 2) {
			$title = JText::sprintf('JPAGETITLE', $title, $config->get('sitename'));
		}
		
		JFactory::getDocument()->setTitle($title);
	}
	
	// Set tax
	public static function setTax($total, $type, $value) {
		$tax = 0;
		
		if ($value > 0) {
			if ($type == 0) {
				$tax = $value;
			} else {
				$tax = $total * ($value / 100);
			}
		}
		return $tax > 0 ? $tax : 0;
	}
	
	// Get the user avatar
	public static function getAvatar($id, $email) {
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);
		$avatar = rseventsproHelper::getConfig('user_avatar');
		$html	= '';
		
		if (!empty($avatar)) {
			$user = JFactory::getUser($id);
			
			switch ($avatar) {
				// Gravatar
				case 'gravatar':
					$email = ($id == 0 && !empty($email)) ? md5(strtolower(trim($email))) : md5(strtolower(trim($user->get('email'))));
					$html .= '<img src="http://www.gravatar.com/avatar/'.$email.'?d='.urlencode(JURI::root().'components/com_rseventspro/assets/images/user.png').'" alt="Gravatar" class="rs_avatar" width="64" height="64" />';
				break;
				
				// Community Builder
				case 'comprofiler':
					$query->clear()
						->select($db->qn('avatar'))
						->from($db->qn('#__comprofiler'))
						->where($db->qn('user_id').' = '.(int) $id)
						->where($db->qn('avatarapproved').' = 1');
					
					$db->setQuery($query);
					if ($cavatar = $db->loadResult())
						$html .= '<img src="'.JURI::root().'images/comprofiler/tn'.$cavatar.'" alt="Community Builder Avatar" class="rs_avatar" width="64" height="64" />';
					else
						$html .= '<img src="'.JURI::root().'components/com_rseventspro/assets/images/user.png" alt="Community Builder Avatar" class="rs_avatar" width="64" height="64" />';
				break;
				
				// JomSocial
				case 'community':				
					if (file_exists(JPATH_SITE.'/components/com_community/libraries/core.php')) {
						include_once(JPATH_SITE.'/components/com_community/libraries/core.php');
						$user 		= CFactory::getUser($id);
						$avatarUrl	= $user->getThumbAvatar();
						$html .= '<img src="'.$avatarUrl.'" alt="JomSocial Avatar" class="rs_avatar" width="64" height="64" />';
					} else {
						$html .= '<img src="'.JURI::root().'components/com_rseventspro/assets/images/user.png" alt="JomSocial Avatar" class="rs_avatar" width="64" height="64" />';
					}
				break;
				
				// Kunena 
				case 'kunena':				
					$file = JPATH_ADMINISTRATOR.'/components/com_kunena/libraries/user/user.php';
					if (file_exists($file)) {
						require_once $file;
						$user = KunenaUser::getInstance($user_id);
						$html .= '<img src="'.$user->getAvatarURL().'" alt="Kunena Avatar" class="rs_avatar" width="64" height="64" />';
					} else 
						$html .= '<img src="'.JURI::root().'components/com_rseventspro/assets/images/user.png" alt="Kunena Avatar" class="rs_avatar" width="64" height="64" />';
				break;
				
				// Fireboard
				case 'fireboard':
					$query->clear()
						->select($db->qn('avatar'))
						->from($db->qn('#__fb_users'))
						->where($db->qn('userid').' = '.(int) $id);
					
					$db->setQuery($query);
					$fireboard = $db->loadResult();
				
				if (!empty($fireboard))
					$html .= '<img src="'.JURI::root().'images/fbfiles/avatars/'.$fireboard.'" alt="Fireboard Avatar" class="rs_avatar" width="64" height="64" />';
				else
					$html .= '<img src="'.JURI::root().'components/com_rseventspro/assets/images/user.png" alt="Fireboard Avatar" class="rs_avatar" width="64" height="64" />';
					
				break;
				
				// K2
				case 'k2':
					$query->clear()
						->select($db->qn('image'))
						->from($db->qn('#__k2_users'))
						->where($db->qn('userID').' = '.(int) $id);
					
					$db->setQuery($query);
					$k2 = $db->loadResult();
 
					if (!empty($k2) && file_exists(JPATH_SITE.'/media/k2/users/'.$k2)) {
						$html .= '<img src="'.JURI::root().'media/k2/users/'.$k2.'" alt="K2 Avatar" class="rs_avatar" height="64" />';
					} else {
						$html .= '<img src="'.JURI::root().'components/com_rseventspro/assets/images/user.png" alt="K2 Avatar" class="rs_avatar" width="64" height="64" />';
					}
				break;
				
			}
		}
		
		return $html;
	}
	
	// Get user profile link
	public static function getProfile($type, $id) {
		$profile = $type == 'guests' ? rseventsproHelper::getConfig('user_profile','int') : rseventsproHelper::getConfig('event_owner_profile','int');
		$url	 = '';
		
		if (!empty($profile)) {
			// JomSocial
			if ($profile == 1) {
				if (file_exists(JPATH_SITE.'/components/com_community/libraries/core.php')) {
					include_once(JPATH_SITE.'/components/com_community/libraries/core.php');
					$url = CRoute::_('index.php?option=com_community&view=profile&userid='.$id);
				}
			}
			// Community Builder
			else if ($profile == 2) {
				if (file_exists(JPATH_ADMINISTRATOR.'/components/com_comprofiler/plugin.foundation.php')) {
					include_once(JPATH_ADMINISTRATOR.'/components/com_comprofiler/plugin.foundation.php');
					global $_CB_framework;
					cbimport('cb.database');
					$url = $_CB_framework->userProfileUrl( $id, true );
				}
			}
		}
		
		return $url;
	}
	
	// Get comments
	public static function comments($id,$name) {
		$comment = rseventsproHelper::getConfig('event_comment','int');
		
		if (empty($comment)) return;
		
		switch ($comment) {
			// Facebook comments
			case 1:
				return '<div id="fb-root"></div><fb:comments href="'.rseventsproHelper::shareURL($id,$name,true).'" num_posts="5" width="650"></fb:comments>';
			break;
			
			// RSComments! integration
			case 2:
				if (file_exists(JPATH_SITE.'/components/com_rscomments/helpers/rscomments.php')) {
					require_once JPATH_SITE.'/components/com_rscomments/helpers/rscomments.php';
					return RSCommentsHelper::showRSComments('com_rseventspro',$id);
				}
			break;
			
			// JComments integration
			case 3:
				if (file_exists(JPATH_SITE.'/components/com_jcomments/jcomments.php')) {
					require_once JPATH_SITE.'/components/com_jcomments/jcomments.php';
					return JComments::showComments($id, 'com_rseventspro', $name);
				}
			break;

			// JomComments integration
			case 4:
				if (file_exists(JPATH_SITE.'/plugins/content/jom_comment_bot.php')) {
					require_once JPATH_SITE.'/plugins/content/jom_comment_bot.php';
					return jomcomment($id, 'com_rseventspro');
				}
			break;
		}
	}
	
	// Create the rating system
	public static function rating($id) {
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);
		$ip		= $_SERVER['REMOTE_ADDR'];
		
		$query->clear()
			->select('CEIL(IFNULL(SUM(id)/COUNT(id),0))')
			->from($db->qn('#__rseventspro_taxonomy'))
			->where($db->qn('type').' = '.$db->q('rating'))
			->where($db->qn('ide').' = '.(int) $id);
		
		$db->setQuery($query);
		$rating = $db->loadResult();
		
		//check if the user has already voted
		$query->clear()
			->select($db->qn('id'))
			->from($db->qn('#__rseventspro_taxonomy'))
			->where($db->qn('type').' = '.$db->q('rating'))
			->where($db->qn('extra').' = '.$db->q($ip))
			->where($db->qn('ide').' = '.(int) $id);
		
		$db->setQuery($query,0,1);
		$voted = $db->loadResult();		
		
		$return = '';
		$return .= '
		<ul class="rsepro_star_rating" id="rs_rating">
			<li id="rsepro_current_rating" class="rsepro_feedback_selected_'.$rating.'">&nbsp;</li>';

		
		if ($rating == 0 || !$voted ) {
			$return .='
			<li><a href="javascript: void(0);" onclick="rsepro_feedback(1, '.$id.');" class="rs_one_star" id="rsepro_feedback_1">&nbsp;</a></li>
			<li><a href="javascript: void(0);" onclick="rsepro_feedback(2, '.$id.');" class="rs_two_stars" id="rsepro_feedback_2">&nbsp;</a></li>
			<li><a href="javascript: void(0);" onclick="rsepro_feedback(3, '.$id.');" class="rs_three_stars" id="rsepro_feedback_3">&nbsp;</a></li>
			<li><a href="javascript: void(0);" onclick="rsepro_feedback(4, '.$id.');" class="rs_four_stars" id="rsepro_feedback_4">&nbsp;</a></li>
			<li><a href="javascript: void(0);" onclick="rsepro_feedback(5, '.$id.');" class="rs_five_stars" id="rsepro_feedback_5">&nbsp;</a></li>';
		}
		$return .= '</ul>';
		$return .= '<div id="rs_rating_loading"><img src="'.JURI::root().'components/com_rseventspro/assets/images/loading.gif" id="rs_loading_img" alt="" style="display:none;" /><span id="rs_rating_info"></span></div>';
		
		return $return;
	}
	
	// Get Cancel link
	public static function redirect($js = false,$message = null,$url = null,$reload = false, $sticky = false) {
		$link	= rseventsproHelper::getConfig('modal','int');
		$reload = $reload ? 'window.parent.location.reload();' : '';
		
		if ($reload) {
			if (empty($url))
				$redirect = 'window.parent.location.reload();';
			else 
				$redirect = 'window.parent.location = "'.addslashes($url).'";';
		} else $redirect = '';
		
		
		if ($link == 1) {
			if ($js)
			{
				$return = '<div class="rs_message_info">'.$message.'</div>';
				if (!$sticky)
					$return .= '<script type="text/javascript">window.top.setTimeout(\''.$redirect.'window.parent.jQuery.colorbox.close();\',1200);</script>';
				return $return;
			} 
			else return '<a href="javascript:void(0)" onclick="window.parent.jQuery.colorbox.close();">'.$message.'</a>';
		} 
		elseif ($link == 2) {
			if ($js) {
				$return = '<div class="rs_message_info">'.$message.'</div>';
				
				if (!$sticky)
					$return .= '<script type="text/javascript">window.top.setTimeout(\''.$redirect.'window.parent.SqueezeBox.close();\',1200);</script>';
				return $return;
			} 
			else return '<a href="javascript:void(0)" onclick="window.parent.SqueezeBox.close();">'.$message.'</a>';
		} else {
			if ($js) {
				JFactory::getApplication()->redirect($url,$message);
			} else return '<a href="'.$url.'">'.$message.'</a>';
		}
	}
	
	// Add payment log into database
	public static function savelog($log, $id, $update=true) {
		if (!$log || !$id)
			return false;
			
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		
		if (!is_array($log))
			$log = array($log);
		
		foreach ($log as $i => $item)
			$log[$i] = '<b>'.rseventsproHelper::date('now').'</b> '.$item;
		
		$log = implode("<br />", $log);
		
		$query->clear();
		$query->update($db->qn('#__rseventspro_users'));
		$query->where($db->qn('id').' = '.(int) $id);
		
		if ($update) {
			$query->set($db->qn('log').' = CONCAT('.$db->qn('log').','.$db->q('<br />'.$log).')');
		} else {
			$query->set($db->qn('log').' = '.$db->q($log));
		}
			
		$db->setQuery($query);
		return $db->execute();
	}
	
	// Save registration
	public static function saveRegistration($idsubmission) {
		jimport( 'joomla.application.component.model' );
		JModelLegacy::addIncludePath(JPATH_SITE.'/components/com_rseventspro/models/rseventspro.php');
		
		$model = JModelLegacy::getInstance('rseventsproModelRseventspro');
		return $model->subscribe($idsubmission);
	}
	
	// Get the discount value
	public static function discount($id, $total) {
		$db			= JFactory::getDbo();
		$query		= $db->getQuery(true);
		$jinput		= JFactory::getApplication()->input;
		$form		= $jinput->get('form',array(),'array');
		$coupon 	= $jinput->getString('coupon');
		$usergroups	= rseventsproHelper::getUserGroups();
		$now		= rseventsproHelper::date('now','Y-m-d H:i:s');
		$nowunix	= JFactory::getDate()->toUnix();
		$cids		= array();
		$discounts	= array();
		
		if (!empty($form['RSEProName']) && $jinput->get('option') == 'com_rseventspro')
			$coupon		= @$form['RSEProCoupon'];
		
		$coupon = trim($coupon);
		$thecoupon	= $coupon;
		
		// Get Coupons
		$query->clear()
			->select($db->qn('id'))->select($db->qn('groups'))
			->from($db->qn('#__rseventspro_coupons'))
			->where($db->qn('ide').' = '.(int) $id);
		
		$db->setQuery($query);
		$coupons = $db->loadObjectList();
		
		// Get Coupon Codes
		$query->clear()
			->select($db->qn('cc.code'))->select($db->qn('cc.used'))->select($db->qn('cc.idc'))
			->from($db->qn('#__rseventspro_coupon_codes','cc'))
			->join('left', $db->qn('#__rseventspro_coupons','c').' ON '.$db->qn('c.id').' = '.$db->qn('cc.idc'))
			->where($db->qn('c.ide').' = '.(int) $id);
		
		$db->setQuery($query);
		$codes = $db->loadObjectList();
		
		// Get coupon by code
		if (!empty($codes)) {
			foreach ($codes as $code) {
				$thecode = trim($code->code);
				
				if (empty($thecode))
					continue;
				
				if ($thecode == $coupon) {
					$cids[] = $code->idc;
				}
			}
		}
		
		// Get coupons by group
		if (!empty($coupons)) {
			foreach ($coupons as $c) {
				if (!empty($c->groups)) {
					$registry = new JRegistry;
					$registry->loadString($c->groups);
					$groups = $registry->toArray();
					
					if (!empty($groups)) {
						if (!empty($usergroups) && !empty($groups)) {
							foreach ($usergroups as $usergroup) {
								if (in_array($usergroup,$groups)) {
									$cids[] = $c->id;
								}
							}
						}
					}
				}
			}
		}
		
		if (!empty($cids)) {
			JArrayHelper::toInteger($cids);
			$cids = array_unique($cids);
			
			foreach ($cids as $cid) {
				$query->clear()
					->select($db->qn('from'))->select($db->qn('to'))->select($db->qn('usage'))
					->select($db->qn('discount'))->select($db->qn('type'))
					->from($db->qn('#__rseventspro_coupons'))
					->where($db->qn('id').' = '.(int) $cid);
				
				$db->setQuery($query);
				$coupon = $db->loadObject();
				
				// Get code details
				$query->clear()
					->select($db->qn('cc.id'))->select($db->qn('cc.used'))
					->from($db->qn('#__rseventspro_coupon_codes','cc'))
					->join('left', $db->qn('#__rseventspro_coupons','c').' ON '.$db->qn('c.id').' = '.$db->qn('cc.idc'))
					->where($db->qn('cc.code').' = '.$db->q($thecoupon))
					->where($db->qn('c.ide').' = '.(int) $id)
					->where($db->qn('c.id').' = '.(int) $cid);
				
				$db->setQuery($query);
				$code = $db->loadObject();
				
				// Have we reached the max limit for this coupon ?
				if (!empty($coupon->usage) && !empty($code))
					if ($code->used >= $coupon->usage)
						continue;
				
				if ($coupon->from == $db->getNullDate()) $coupon->from = '';
				if ($coupon->to == $db->getNullDate()) $coupon->to = '';
				
				$available = true;
				if (empty($coupon->from) && empty($coupon->to)) {
					$available = true;
				} elseif (!empty($coupon->from) && empty($coupon->to)) {
					$fromunix = JFactory::getDate($coupon->from)->toUnix();
					if ($fromunix <= $nowunix)
						$available = true;
					else $available = false;
				} elseif (empty($coupon->from) && !empty($coupon->to)) {
					$tounix = JFactory::getDate($coupon->to)->toUnix();
					if ($tounix <= $nowunix)
						$available = false;
					else $available = true;
				} else {
					$fromunix = JFactory::getDate($coupon->from)->toUnix();
					$tounix = JFactory::getDate($coupon->to)->toUnix();
					
					if (($fromunix <= $nowunix && $tounix >= $nowunix) || ($fromunix >= $nowunix && $tounix <= $nowunix))
						$available = true;
					else $available = false;
				}
				
				if (!$available) continue;
				
				$discount = 0;
				if ($coupon->type) {
					$discount = $total * ($coupon->discount / 100);
					if ($discount > $total) continue;
				} else  {
					$discount = $coupon->discount;
					if ($discount > $total) continue;
				}
				
				// Try to get the id of the code that will be used
				// If the coupon input has a value then we return the id of the found code
				// Else we try to find one code valid from the other coupons (This case is used only if one coupon has the "Apply discount instantly to" option enablled) 
				if (!empty($code))
					$codeid	  = $code->id;
				else  {
					$availableids = array();
					
					// Get all coupon codes
					$query->clear()
						->select($db->qn('id'))->select($db->qn('used'))
						->from($db->qn('#__rseventspro_coupon_codes'))
						->where($db->qn('idc').' = '.(int) $cid);
					
					$db->setQuery($query);
					if ($couponcodes = $db->loadObjectList()) {
						foreach ($couponcodes as $couponcode) {
							if (!empty($coupon->usage) && $couponcode->used >= $coupon->usage)
								continue;
							$availableids[] = $couponcode->id;
						}
					}
					
					// There are no codes available
					if (empty($availableids)) continue;
					
					// Get the first availble id
					$codeid = @$availableids[0];
				}
				
				if (!$codeid) continue;
				$discounts[$codeid] = $discount;
			}
		}
		
		if (!empty($discounts)) {
			arsort($discounts);
			foreach ($discounts as $couponid => $discount) {				
				return array('id' => $couponid, 'discount' => $discount);
				break;
			}
		}
		
		return 0;
	}
	
	// Check ticket 
	public static function checkticket($id) {
		if (!defined('RSEPRO_TICKETS_NOT_AVAILABLE')) {
			define('RSEPRO_TICKETS_NOT_AVAILABLE', -1);
			define('RSEPRO_TICKETS_UNLIMITED', 0);
		}
		
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);
		$seats	= RSEPRO_TICKETS_NOT_AVAILABLE;
		
		$query->clear()
			->select($db->qn('ide'))->select($db->qn('seats'))->select($db->qn('user_seats'))
			->from($db->qn('#__rseventspro_tickets'))
			->where($db->qn('id').' = '.(int) $id);
		
		$db->setQuery($query);
		if ($ticket = $db->loadObject()) {
			$query->clear()
				->select('SUM('.$db->qn('ut.quantity').')')
				->from($db->qn('#__rseventspro_user_tickets','ut'))
				->join('left', $db->qn('#__rseventspro_users','u').' ON '.$db->qn('u.id').' = '.$db->qn('ut.ids'))
				->where($db->qn('u.state').' IN (0,1)')
				->where($db->qn('ut.idt').' = '.(int) $id);
			
			$db->setQuery($query);
			$ticket->purchased = $db->loadResult();
			
			$query->clear()
				->select($db->qn('overbooking'))->select($db->qn('overbooking_amount'))
				->select($db->qn('max_tickets'))->select($db->qn('max_tickets_amount'))
				->from($db->qn('#__rseventspro_events'))
				->where($db->qn('id').' = '.(int) $ticket->ide);
			
			$db->setQuery($query);
			$event = $db->loadObject();
			
			if ($event->max_tickets) {
				$query->clear()
					->select('SUM('.$db->qn('ut.quantity').')')
					->from($db->qn('#__rseventspro_user_tickets','ut'))
					->join('left', $db->qn('#__rseventspro_users','u').' ON '.$db->qn('u.id').' = '.$db->qn('ut.ids'))
					->where($db->qn('u.state').' IN (0,1)')
					->where($db->qn('u.ide').' = '.(int) $ticket->ide);
				
				$db->setQuery($query);
				$all_tickets_purchased = $db->loadResult();
				
				$query->clear()
					->select('SUM('.$db->qn('ut.quantity').')')
					->from($db->qn('#__rseventspro_user_tickets','ut'))
					->join('left', $db->qn('#__rseventspro_users','u').' ON '.$db->qn('u.id').' = '.$db->qn('ut.ids'))
					->where($db->qn('u.state').' IN (0,1)')
					->where($db->qn('ut.idt').' = '.(int) $id);
				
				$db->setQuery($query);
				$all_tickets_purchased_from_ticket = $db->loadResult();
			}
			
			$seats = 1;
			
			if ($ticket->seats > 0) // fixed number of tickets
			{
				$available = $ticket->seats - $ticket->purchased; // how many tickets are available ?
				if ($event->overbooking && $event->overbooking_amount > 0) // is overbooking on
				{
					// if we have tickets available
					// and the number of available tickets is smaller than the max number of tickets a user can buy
					// we can take some tickets from the overbooking amount
					// eg. max tickets = 15, max user seats = 3, overbooking = 5, available = 1
					// the user will be presented with 3 available tickets since 1 available ticket and 2 more are added from the overbooking setting
					if ($available > 0 && $available < $ticket->user_seats && $available + $event->overbooking_amount >= $ticket->user_seats)
						$available = min($available + $event->overbooking_amount, $ticket->user_seats);
						
					// if we've purchased more than the allowed number of tickets it means that we've taken them from the overbooking amount
					// this means that $available have a negative value (representing the number of overbooked tickets)
					// if we have more overbooked tickets available, add them here
					// eg. available = -1, overbooking = 5, available = -1 + 5 = 4
					if ($available < 1)
						$available = $event->overbooking_amount + $available;
				}
				
				if ($ticket->user_seats > 0) // fixed number of tickets per user
				{
					if ($available > 0) // we have tickets available
						$seats = min($available, $ticket->user_seats); // how many tickets can this user purchase?
					else // we've reached our limit
						$seats = RSEPRO_TICKETS_NOT_AVAILABLE;
				}
				else // unlimited number of tickets per user
				{
					if ($available > 0) // we have tickets available
						$seats = $available; // the user can purchase a maximum number of the tickets available because he has no limit set
					else // we've reached our limit
						$seats = RSEPRO_TICKETS_NOT_AVAILABLE;
				}
			}
			else // unlimited number of tickets
			{
				if ($ticket->user_seats > 0) // fixed number of tickets per user
					$seats = $ticket->user_seats; // the limit is set per user since we have unlimited tickets
				else // unlimited number of tickets per user
					$seats = RSEPRO_TICKETS_UNLIMITED;
			}
			
			if ($event->max_tickets && $event->max_tickets_amount > 0) // do we have max attendance?
			{
				if ($all_tickets_purchased >= $event->max_tickets_amount) // if the limit is reached
					$seats = RSEPRO_TICKETS_NOT_AVAILABLE;
				else
				{
					$available = $event->max_tickets_amount - $all_tickets_purchased;
					if ($ticket->user_seats > 0) // fixed number of tickets per user
					{
						if ($ticket->user_seats > $available)
							$ticket->user_seats = $available;
						
						if ($ticket->seats) 
						{
							if ($all_tickets_purchased_from_ticket < $ticket->seats)
							{
								$seats = min($ticket->user_seats,$ticket->seats - $all_tickets_purchased_from_ticket);
							}
							else if ($all_tickets_purchased_from_ticket >= $ticket->seats)
							{
								$seats = RSEPRO_TICKETS_NOT_AVAILABLE;
							}
						}
						else
							$seats = min($available,$ticket->user_seats);
					}
					else
						$seats = $available;
					
					if ($seats < 0)
						$seats = RSEPRO_TICKETS_NOT_AVAILABLE;
				}
			}
		}
		
		return $seats;
	}
	
	// Create a new Joomla! user
	public static function returnUser($email , $name = null) {
		$db		= JFactory::getDbo();
		$params	= JComponentHelper::getParams('com_users');
		$config = JFactory::getConfig();
		$app	= JFactory::getApplication();
		$query	= $db->getQuery(true);
		$data	= new stdClass();
		
		// Check if the email is attached to an account
		$query->clear()
			->select($db->qn('id'))
			->from($db->qn('#__users'))
			->where($db->qn('email').' = '.$db->q($email));
		
		$db->setQuery($query);
		$userid = $db->loadResult();
		
		if (!empty($userid)) 
			return $userid;
		
		JFactory::getLanguage()->load('com_users',JPATH_SITE);
		
		// Construct the name and username
		$name		= trim($name);
		$username	= strtolower(str_replace(' ','.',$name));
		
		$query->clear()
			->select($db->qn('id'))
			->from($db->qn('#__users'))
			->where($db->qn('username').' = '.$db->q($username));
		
		$db->setQuery($query);
		if (intval($db->loadResult()) > 0 )
			$username = $username.rand(0,99);
		
		$password = JUserHelper::genRandomPassword(6);
		
		$data->name = $name;
		$data->username = $username;
		$data->email = $email;
		$data->email1 = $email;
		$data->email2 = $email;
		$data->password = $password;
		$data->password1 = $password;
		$data->password2 = $password;
		$data->groups = array($params->get('new_usertype', 2));
		
		// Get the dispatcher and load the users plugins.
		JPluginHelper::importPlugin('user');
		
		// Trigger the data preparation event.
		$results = $app->triggerEvent('onContentPrepareData', array('com_users.registration', $data));
		
		// Check for errors encountered while preparing the data.
		if (count($results) && in_array(false, $results, true)) {
			return false;
		}
		
		$data = (array) $data;
		
		$user = new JUser;

		// Get user activation option
		$useractivation = $params->get('useractivation',1);
		
		// Check if the user needs to activate their account.
		if (($useractivation == 1) || ($useractivation == 2)) {
			$data['activation'] = JApplication::getHash(JUserHelper::genRandomPassword());
			$data['block'] = 1;
		}
		
		// Bind the data.
		if (!$user->bind($data)) {
			$app->enqueueMessage(JText::sprintf('COM_USERS_REGISTRATION_BIND_FAILED', $user->getError()), 'warning');
			return false;
		}
		
		// Store the data.
		if (!$user->save()) {
			$app->enqueueMessage(JText::sprintf('COM_USERS_REGISTRATION_SAVE_FAILED', $user->getError()), 'warning');
			return false;
		}
		
		//auto approve users if CB is installed
		if (file_exists(JPATH_SITE.'/components/com_comprofiler/comprofiler.php')) {
			$query->clear()
				->insert($db->qn('#__comprofiler'))
				->set($db->qn('approved').' = 1')
				->set($db->qn('confirmed').' = 1')
				->set($db->qn('user_id').' = '.(int) $user->get('id'))
				->set($db->qn('id').' = '.(int) $user->get('id'));
			
			$db->setQuery($query);
			$db->execute();
		}
		
		// Compile the notification mail values.
		$data = $user->getProperties();
		$data['fromname']	= $config->get('fromname');
		$data['mailfrom']	= $config->get('mailfrom');
		$data['sitename']	= $config->get('sitename');
		$data['siteurl']	= JUri::root();
		
		// Handle account activation/confirmation emails.
		if ($useractivation == 2 || $useractivation == 1) {
			// Set the link to activate the user account.
			$uri = JURI::getInstance();
			$base = $uri->toString(array('scheme', 'user', 'pass', 'host', 'port'));
			$data['activate'] = $base.JRoute::_('index.php?option=com_users&task=registration.activate&token='.$data['activation'], false);

			$emailSubject	= JText::sprintf(
				'COM_USERS_EMAIL_ACCOUNT_DETAILS',
				$data['name'],
				$data['sitename']
			);

			$emailBody = JText::sprintf(
				'COM_USERS_EMAIL_REGISTERED_WITH_ACTIVATION_BODY',
				$data['name'],
				$data['sitename'],
				$data['siteurl'].'index.php?option=com_users&task=registration.activate&token='.$data['activation'],
				$data['siteurl'],
				$data['username'],
				$data['password_clear']
			);
		}
		else
		{
			$emailSubject	= JText::sprintf(
				'COM_USERS_EMAIL_ACCOUNT_DETAILS',
				$data['name'],
				$data['sitename']
			);

			$emailBody = JText::sprintf(
				'COM_RSEVENTSPRO_EMAIL_REGISTERED_BODY',
				$data['name'],
				$data['sitename'],
				$data['siteurl'],
				$data['username'],
				$data['password_clear']
			);
		}
		
		// Send the registration email.
		$return = JFactory::getMailer()->sendMail($data['mailfrom'], $data['fromname'], $data['email'], $emailSubject, $emailBody);
		
		if ($return == false)
			return false;
		
		return $user->get('id');
	}
	
	// Error handling
	public static function error($message, $url) {
		$error = self::getConfig('errors');
		
		// 500 error
		if ($error == 0) {
			JError::raiseError(500, $message);
		} elseif ($error == 1) { // 403 error
			JError::raiseError(403, $message);
		} else { // Redirect
			JFactory::getApplication()->redirect($url,$message, 'error');
		}
	}
	
	// Feed fix
	public static function feed() {
		$jinput = JFactory::getApplication()->input;
		
		if ($jinput->get('view') == 'calendar' && $jinput->get('format') == 'feed') {
			$jinput->set('view','rseventspro');
		}
	}
	
	// Parse styles
	public static function parseStyle($options, $unit = 'px') {
		$string = '';
		
		if (!empty($options)) {
			foreach ($options as $property => $value) {
				if (!empty($value))
					$string .= $property.': '.$value.$unit.'; ';
			}
		}
		
		return $string;
	}
	
	// Get tickets for tickets configuration
	public static function getTickets($id) {
		$app		= JFactory::getApplication();
		$db			= JFactory::getDbo();
		$query		= $db->getQuery(true);
		$user		= JFactory::getUser();
		$usergroups	= rseventsproHelper::getUserGroups();
		$joomgroups	= JAccess::getGroupsByUser($user->get('id'));
		$tickets	= array();
		$checkGroup	= !$app->isAdmin() && $app->input->get('layout','') == 'tickets';
		
		// If the user is logged in remove the Public group
		if (!$user->get('guest')) {
			foreach ($joomgroups as $key => $value) {
				if ($value == 1) {
					unset($joomgroups[$key]);
				}
			}
		}
		
		$query->clear()
			->select($db->qn('id'))->select($db->qn('name'))
			->select($db->qn('seats'))->select($db->qn('price'))
			->select($db->qn('user_seats'))->select($db->qn('position'))
			->select($db->qn('description'))->select($db->qn('groups'))
			->from($db->qn('#__rseventspro_tickets'))
			->where($db->qn('ide').' = '.(int) $id);
		
		$db->setQuery($query);
		if ($tickets = $db->loadObjectList()) {
			foreach ($tickets as $i => $ticket) {
				$registry = new JRegistry;
				$registry->loadString($ticket->position);
				$tickets[$i]->position = $registry->toArray();
				
				if ($checkGroup) {
					$skip = false;
					if (!empty($ticket->groups)) {
						$registry = new JRegistry;
						$registry->loadString($ticket->groups);
						$groups = $registry->toArray();
						
						// Check first in the RSEvents!Pro groups
						if (!empty($usergroups)) {
							if (!empty($groups)) {
								foreach ($usergroups as $usergroup) {
									if (!in_array($usergroup,$groups)) {
										$skip = true;
									}
								}
							}
						}
						
						// Check in the Joomla! groups section
						if (!empty($joomgroups) && empty($usergroups)) {
							if (!empty($groups)) {
								$query->clear()
									->select($this->_db->qn('jgroups'))->select($this->_db->qn('jusers'))
									->from($this->_db->qn('#__rseventspro_groups'))
									->where($this->_db->qn('id').' IN ('.implode(',',$groups).')');
								$this->_db->setQuery($query);
								if ($rseproGroups = $this->_db->loadObjectList()) {
									foreach ($rseproGroups as $rseproGroup) {
										// Check group users
										if (!empty($rseproGroup->jusers)) {
											$registry = new JRegistry;
											$registry->loadString($rseproGroup->jusers);
											$jusers = $registry->toArray();
											
											if (!empty($jusers)) {
												$userid = $user->get('id');
												
												if (!in_array($userid,$jusers)) {
													$skip = true;
												}
											}
										}
										
										// Check group joomla acl
										if (!empty($rseproGroup->jgroups)) {
											$registry = new JRegistry;
											$registry->loadString($rseproGroup->jgroups);
											$jgroups = $registry->toArray();
											
											if (!empty($jgroups)) {
												foreach ($joomgroups as $gr) {
													if (!in_array($gr,$jgroups)) {
														$skip = true;
													}
												}
											}
										}
									}
								}
							}
						}	
					}
					
					if ($skip)
						unset($tickets[$i]);
				}
			}
		}
		
		return $tickets;
	}
	
	// Get selected seats
	public static function getSelectedSeats($tid, $ids = null) {
		$db			= JFactory::getDbo();
		$query		= $db->getQuery(true);
		
		$query->clear()
			->select($db->qn('us.seat'))
			->from($db->qn('#__rseventspro_user_seats','us'))
			->join('LEFT', $db->qn('#__rseventspro_users','u').' ON '.$db->qn('u.id').' = '.$db->qn('us.ids'))
			->where($db->qn('u.state').' IN (0,1)')
			->where($db->qn('us.idt').' = '.(int) $tid);
			
		if ($ids) {
			$query->where($db->qn('us.ids').' = '.(int) $ids);
		}
		
		$db->setQuery($query);
		return $db->loadColumn();
	}
	
	// Get total number of used tickets
	public static function getUsedTickets($id) {
		$db			= JFactory::getDbo();
		$query		= $db->getQuery(true);
		
		$query->clear()
			->select('SUM('.$db->qn('ut.quantity').')')
			->from($db->qn('#__rseventspro_users','u'))
			->join('LEFT', $db->qn('#__rseventspro_user_tickets','ut').' ON '.$db->qn('ut.ids').' = '.$db->qn('u.id'))
			->where($db->qn('u.ide').' = '.(int) $id);
		$db->setQuery($query);
		return $db->loadResult();
	}
	
	// Get total number of unlimited seats purchased
	public static function getTotalUnlimited($tid, $ids) {
		$db			= JFactory::getDbo();
		$query		= $db->getQuery(true);
		
		$query->clear()
			->select($db->qn('quantity'))
			->from($db->qn('#__rseventspro_user_tickets'))
			->where($db->qn('idt').' = '.(int) $tid)
			->where($db->qn('ids').' = '.(int) $ids);
		
		$db->setQuery($query);
		return (int) $db->loadResult();
	}
	
	// Get seats
	public static function getSeats($ids, $idt) {
		$db			= JFactory::getDbo();
		$query		= $db->getQuery(true);
		
		$query->clear()
			->select($db->qn('seat'))
			->from($db->qn('#__rseventspro_user_seats'))
			->where($db->qn('idt').' = '.(int) $idt)
			->where($db->qn('ids').' = '.(int) $ids);
		
		$db->setQuery($query);
		if ($seats = $db->loadColumn()) {
			return JText::sprintf('COM_RSEVENTSPRO_SEATS',implode(',',$seats));
		}
	}
	
	public static function report($id) {
		$db			= JFactory::getDbo();
		$query		= $db->getQuery(true);
		$html		= '';
		
		$query->clear()
			->select('COUNT('.$db->qn('id').')')
			->from($db->qn('#__rseventspro_reports'))
			->where($db->qn('ide').' = '.(int) $id);
		
		$db->setQuery($query);
		$count = (int) $db->loadResult();
		
		if ($count) {
			$html .= '<a href="'.JRoute::_('index.php?option=com_rseventspro&view=events&layout=report&id='.$id).'" class="hasTip" title="'.JText::plural('COM_RSEVENTSPRO_NO_REPORTS',$count).'::">';
			$html .= '<img src="'.JURI::root().'administrator/components/com_rseventspro/assets/images/flag.png" alt="" />';
			$html .= '</a>';
		}
		
		return $html;
	}
	
	public static function getReports($id) {
		$db			= JFactory::getDbo();
		$query		= $db->getQuery(true);
		$data		= array(); 
		
		$query->clear()
			->select($db->qn('name'))
			->from($db->qn('#__rseventspro_events'))
			->where($db->qn('id').' = '.(int) $id);
		$db->setQuery($query);
		$name = $db->loadResult();
		
		$query->clear()
			->select('r.*')->select($db->qn('u.name'))
			->from($db->qn('#__rseventspro_reports','r'))
			->join('left',$db->qn('#__users','u').' ON '.$db->qn('u.id').' = '.$db->qn('r.idu'))
			->where($db->qn('r.ide').' = '.(int) $id);
		$db->setQuery($query);
		$reports = $db->loadObjectList();
		
		$data['name'] = $name;
		$data['data'] = $reports;
		
		return $data;
	}
	
	public static function getOptions() {
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);
		$groups = self::getUserGroups();
		
		$default = array('enable_rating' => 1,
			'enable_fb_like' => 1,
			'enable_twitter' => 1,
			'enable_gplus' => 1,
			'enable_linkedin' => 1,
			'start_date' => 1,
			'start_time' => 1,
			'end_date' => 1,
			'end_time' => 1,
			'show_description' =>1,
			'show_location' => 1,
			'show_categories' => 1,
			'show_tags' => 1,
			'show_files' => 1,
			'show_contact' => 1,
			'show_map' => 1,
			'show_export' => 1,
			'show_invite' => 1,
			'show_postedby' => 1,
			'show_repeats' => 1
		);
		
		if ($groups) {
			JArrayHelper::toInteger($groups);
			
			$query->clear()
				->select($db->qn('event'))
				->from($db->qn('#__rseventspro_groups'))
				->where($db->qn('id').' IN ('.implode(',',$groups).')');
			
			$db->setQuery($query);
			if ($options = $db->loadColumn()) {
				foreach ($options as $option) {
					$registry = new JRegistry;
					$registry->loadString($option);
					if ($groupOptions = $registry->toArray()) {
						foreach ($groupOptions as $property => $value) {
							if (isset($default[$property])) {
								$default[$property] = $value;
							}
						}
					}
				}
			}
		}
		
		return $default;
	}
	
	// Get the mask for dates
	public static function showMask($type, $options) {
		$config = rseventsproHelper::getConfig();
		
		if ($type == 'start') {
			if (isset($options['start_time']) && $options['start_time']) {
				return $config->global_date. ' '.$config->global_time;
			} else {
				return $config->global_date;
			}
		}
		
		if ($type == 'end') {
			if (isset($options['end_time']) && $options['end_time']) {
				return $config->global_date. ' '.$config->global_time;
			} else {
				return $config->global_date;
			}
		}
		
		return $config->global_date. ' '.$config->global_time;
	}
	
	// Replace content event
	public static function event($id,$itemid) {
		$app		= JFactory::getApplication();
		$doc		= JFactory::getDocument();
		$template	= $app->getTemplate();
		
		$class = self::isJ3() ? 'JViewLegacy' : 'JView';
		if ($class == 'JView') {
			jimport('joomla.application.component.view');
		}
		
		$view = new $class(array(
			'name' => 'rseventspro',
			'layout' => 'plugin',
			'base_path' => JPATH_SITE.'/components/com_rseventspro'
		));
		
		$view->addTemplatePath(JPATH_THEMES.'/'.$template.'/html/com_rseventspro/rseventspro');
		
		// Load custom css file
		if (file_exists(JPATH_THEMES.'/'.$template.'/html/com_rseventspro/rseventspro/plugin.css'))
			$doc->addStyleSheet(JURI::root(true).'/templates/'.$template.'/html/com_rseventspro/rseventspro/plugin.css?v='.RSEPRO_RS_REVISION);
		else
			$doc->addStyleSheet(JURI::root(true).'/components/com_rseventspro/assets/css/plugin.css?v='.RSEPRO_RS_REVISION);
		
		
		$view->id		= $id;
		$view->details	= rseventsproHelper::details($id, $itemid);
		$view->config	= rseventsproHelper::getConfig();
		$view->itemid	= $itemid ? '&Itemid='.$itemid : '';
		$event			= $view->details['event'];
		
		if (rseventsproHelper::canview($id) && $event->published && $event->completed) {
			return $view->loadTemplate();
		}
		
		return;
	}
	
	// Get the available seats for a ticket
	public static function getAvailable($ide, $idt) {
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);
		
		$query->clear()
			->select($db->qn('seats'))->select($db->qn('user_seats'))
			->from($db->qn('#__rseventspro_tickets'))
			->where($db->qn('id').' = '.(int) $idt);
		$db->setQuery($query);
		$ticket = $db->loadObject();
		
		if (!$ticket->seats) {
			return 0;
		} else {
			$query->clear()
				->select('SUM('.$db->qn('ut.quantity').')')
				->from($db->qn('#__rseventspro_users','u'))
				->join('left',$db->qn('#__rseventspro_user_tickets','ut').' ON '.$db->qn('ut.ids').' = '.$db->qn('u.id'))
				->where($db->qn('u.state').' IN (0,1)')
				->where($db->qn('u.ide').' = '.(int) $ide)
				->where($db->qn('ut.idt').' = '.(int) $idt);
			$db->setQuery($query);
			$purchased = (int) $db->loadResult();
			
			if ($ticket->user_seats) {
				$available = $ticket->seats - $purchased;
				return min($available,$ticket->user_seats);
			} else {
				return $ticket->seats - $purchased;
			}
		}
	}
	
	// Get subscriber details
	public static function getScan() {
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);
		$input	= JFactory::getApplication()->input;
		$ide	= $input->getInt('id',0);
		$ticket	= $input->getString('ticket','');
		$ids	= str_replace('RST-','',$ticket);
		
		$query->clear()
			->select($db->qn('name'))->select($db->qn('email'))->select($db->qn('date'))
			->select($db->qn('state'))->select($db->qn('gateway'))->select($db->qn('ip'))
			->select($db->qn('discount'))->select($db->qn('early_fee'))->select($db->qn('late_fee'))
			->select($db->qn('tax'))->select($db->qn('coupon'))->select($db->qn('idu'))
			->from($db->qn('#__rseventspro_users'))
			->where($db->qn('id').' = '.(int) $ids);
		$db->setQuery($query);
		if ($subscriber = $db->loadObject()) {
			$details = array();
			$details['subscriber'] = $subscriber;
			
			$query->clear()
				->select($db->qn('t.name'))->select($db->qn('t.price'))
				->select($db->qn('ut.quantity'))
				->from($db->qn('#__rseventspro_tickets','t'))
				->join('left',$db->qn('#__rseventspro_user_tickets','ut').' ON '.$db->qn('t.id').' = '.$db->qn('ut.idt'))
				->where($db->qn('ut.ids').' = '.(int) $ids);
			$db->setQuery($query);
			$tickets = $db->loadObjectList();
			$details['tickets'] = $tickets;
			
			return $details;
		}
		
		if (!empty($ticket))
			return JText::_('COM_RSEVENTSPRO_SUBSCRIBER_NOT_FOUND');
		else return false;
	}
	
	// Check if a subscriber has seats assigned
	public static function hasSeats($id) {
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);
		
		$query->clear()
			->select('COUNT('.$db->qn('id').')')
			->from($db->qn('#__rseventspro_user_seats'))
			->where($db->qn('ids').' = '.(int) $id);
		$db->setQuery($query);
		return $db->loadResult();
	}
}