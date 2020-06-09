<?php
/**
* @package RSComments!
* @copyright (C) 2014 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' );

class com_rscommentsInstallerScript 
{
	public function install($parent) {}
	
	public function preflight($type, $parent) {
		$app		= JFactory::getApplication();
		$jversion	= new JVersion();
		
		if (!$jversion->isCompatible('3.8.0')) {
			$app->enqueueMessage('Please upgrade to at least Joomla! 3.8.0 before continuing!', 'error');
			return false;
		}
		
		return true;
	}

	public function postflight($type, $parent) {
		$db			= JFactory::getDbo();
		$query		= $db->getQuery(true);
		$installer	= new JInstaller();

		if ($type == 'update') {
			$db->setQuery("SHOW COLUMNS FROM `#__rscomments_groups` WHERE `Field` = 'joomla'");
			if ($db->loadResult()) {
				$db->setQuery("DELETE FROM `#__rscomments_groups` WHERE `joomla` = 0");
				$db->execute();
			}
		}
		
		// Run queries
		$this->runSql();
		
		// Install the system plugin
		$installer->install($parent->getParent()->getPath('source').'/extra/plg_system/');

		$query->clear()
			->update($db->qn('#__extensions'))
			->set($db->qn('enabled').' = 1')
			->where($db->qn('element').' = '.$db->q('rscomments'))
			->where($db->qn('type').' = '.$db->q('plugin'))
			->where($db->qn('folder').' = '.$db->q('system'));
		$db->setQuery($query);
		$db->execute();

		// Install the content plugin
		$installer->install($parent->getParent()->getPath('source').'/extra/plg_content/');

		$query->clear()
			->update($db->qn('#__extensions'))
			->set($db->qn('enabled').' = 1')
			->where($db->qn('element').' = '.$db->q('rscomments'))
			->where($db->qn('type').' = '.$db->q('plugin'))
			->where($db->qn('folder').' = '.$db->q('content'));
		$db->setQuery($query);
		$db->execute();

		// Install the editors_xtd plugin
		$installer->install($parent->getParent()->getPath('source').'/extra/plg_editors_xtd/');

		$query->clear()
			->update($db->qn('#__extensions'))
			->set($db->qn('enabled').' = 1')
			->where($db->qn('element').' = '.$db->q('rscomments'))
			->where($db->qn('type').' = '.$db->q('plugin'))
			->where($db->qn('folder').' = '.$db->q('editors-xtd'));
		$db->setQuery($query);
		$db->execute();
		
		// Install the updater plugin
		$installer->install($parent->getParent()->getPath('source').'/extra/plg_installer/');
		
		$query->clear()
			->update($db->qn('#__extensions'))
			->set($db->qn('enabled').' = 1')
			->where($db->qn('element').' = '.$db->q('rscomments'))
			->where($db->qn('type').' = '.$db->q('plugin'))
			->where($db->qn('folder').' = '.$db->q('installer'));
		$db->setQuery($query);
		$db->execute();

		//Install sh404sef plugin
		if (JFolder::exists(JPATH_SITE.'/components/com_sh404sef/sef_ext'))
			JFile::copy($parent->getParent()->getPath('source').'/extra/sef_ext/com_rscomments.php', JPATH_SITE.'/components/com_sh404sef/sef_ext/com_rscomments.php');
		
		$db->setQuery("SELECT `IdGroup` FROM `#__rscomments_groups` WHERE `gid` = 1");
		if (!$db->loadResult()) {
			$permissions = 'a:28:{s:12:"new_comments";s:1:"1";s:16:"edit_own_comment";s:1:"0";s:18:"delete_own_comment";s:1:"0";s:13:"edit_comments";s:1:"0";s:15:"delete_comments";s:1:"0";s:6:"bbcode";s:1:"1";s:13:"vote_comments";s:1:"1";s:21:"auto_subscribe_thread";s:1:"0";s:12:"close_thread";s:1:"0";s:12:"enable_reply";s:1:"1";s:16:"publish_comments";s:1:"0";s:11:"autopublish";s:1:"0";s:11:"show_emails";s:1:"0";s:7:"view_ip";s:1:"0";s:7:"captcha";s:1:"1";s:8:"censored";s:1:"1";s:13:"flood_control";s:1:"1";s:11:"check_names";s:1:"1";s:7:"bb_bold";s:1:"1";s:9:"bb_italic";s:1:"1";s:12:"bb_underline";s:1:"1";s:9:"bb_stroke";s:1:"1";s:8:"bb_quote";s:1:"1";s:8:"bb_lists";s:1:"0";s:8:"bb_image";s:1:"0";s:6:"bb_url";s:1:"0";s:7:"bb_code";s:1:"0";s:9:"bb_videos";s:1:"0";}';
			$db->setQuery("INSERT IGNORE INTO `#__rscomments_groups` (`IdGroup`, `GroupName`, `gid`, `permissions`) VALUES(1, 'Public', 1, ".$db->q($permissions).");");
			$db->execute();
		}
		
		
		
		$query->clear()
			->select($db->qn('extension_id'))
			->from($db->qn('#__extensions'))
			->where($db->qn('element').' = '.$db->q('com_rscomments'))
			->where($db->qn('type').' = '.$db->q('component'));
		$db->setQuery($query);
		$extension_id = $db->loadResult();
		
		if ($type == 'install') {
			// Add default configuration when installing the first time RSComments!
			if ($extension_id) {
				$default = '{"global_register_code":"","date_format":"d.m.Y H:i","enable_rss":"1","template":"LightScheme","authorname":"name","enable_title_field":"1","enable_website_field":"1","nofollow_rel":"1","enable_smiles":"1","enable_bbcode":"1","enable_votes":"1","enable_subscription":"1","show_subcription_checkbox":"1","terms":"1","enable_upload":"0","max_size":10,"allowed_extensions":"jpg\\r\\ntxt\\r\\n","min_comm_len":10,"max_comm_len":1000,"show_counter":"1","form_accordion":"0","show_form":"1","enable_modified":"1","avatar":"gravatar","user_social_link":"","default_order":"DESC","nr_comments":10,"email_notification":"0","notification_emails":"","show_no_comments":"1","captcha":"0","captcha_chars":5,"captcha_lines":"1","captcha_cases":"0","rec_public":"","rec_private":"","rec_themes":"red","akismet_key":"","flood_interval":"30","word_length":"15","no_follow":"1","forbiden_names":"admin\\r\\nadministrator\\r\\nmoderator\\r\\n","censored_words":"","replace_censored":"******","blocked_users":"","enable_reports":"1","enable_captcha_reports":"1","enable_email_reports":"0","report_emails":"","negative_count":"10","load_bootstrap":"0","backend_jquery":"1","frontend_jquery":"1","blocked_ips":"","fontawesome":"1","fontawesome_admin":"1","show_labels":"0","enable_location":"0","store_ip":"1","consent":"1","anonymous":"0","enable_name_field":"1","enable_email_field":"1","modal":"1","cache":"1","enable_usercomments":"1"}';
				$query->clear()
					->update($db->qn('#__extensions'))
					->set($db->qn('params').' = '.$db->q($default))
					->where($db->qn('extension_id').' = '.(int) $extension_id);
				$db->setQuery($query);
				$db->execute();
			}
		}
		
		if ($type == 'update') {
			$columns = $db->getTableColumns('#__rscomments_comments');
			if (!isset($columns['IdParent'])) {
				$db->setQuery("ALTER TABLE `#__rscomments_comments` ADD `IdParent` INT( 15 ) NOT NULL AFTER `IdComment`");
				$db->execute();
			}
			
			// Add the old config to the new one
			$db->setQuery("SHOW TABLES FROM `".JFactory::getConfig()->get('db')."` LIKE '%".JFactory::getConfig()->get('dbprefix')."rscomments_config%'");
			if ($db->loadResult()) {
				$db->setQuery("SELECT `name`, `value` FROM `#__rscomments_config`");
				if ($configuration = $db->loadObjectList()) {
					$messages = array();
					$config = array();
					foreach ($configuration as $conf) {
						if ($conf->name == 'sections') continue;
						if ($conf->name == 'comments_closed' || $conf->name == 'comments_denied' || $conf->name == 'comments_subscriptions' || $conf->name == 'comments_notification') { 
							$messages[$conf->name] = $conf->value;
							continue;
						}
						if ($conf->name == 'author_name') $conf->name = 'authorname';
						if ($conf->name == 'categories') {
							if (empty($conf->value))
								continue;
							else {
								$conf->value = explode(',',$conf->value);
							}
						}
						
						$config[$conf->name] = $conf->value;
					}
					
					$config['enable_modified'] = 1;
					
					$reg = new JRegistry();
					$reg->loadArray($config);
					$confdata = $reg->toString();
					
					$query->clear();
					$query->update('`#__extensions`')->set('`params` = '.$db->q($confdata))->where('`extension_id` = '.(int) $extension_id);
					$db->setQuery($query);
					$db->execute();
					
					if (!empty($messages)) {
						foreach ($messages as $name => $value) {
							if ($name == 'comments_subscriptions') $name = 'subscription_message';
							if ($name == 'comments_notification') $name = 'notification_message';
							$db->setQuery("SELECT `id` FROM `#__rscomments_messages` WHERE `tag` = 'en-GB' AND `type` = ".$db->q($name)." ");
							if ($messageid = $db->loadResult()) {
								$db->setQuery("UPDATE `#__rscomments_messages` SET `content` = ".$db->q($value)." WHERE `id` = ".(int) $messageid." ");
								$db->execute();
							} else {
								$db->setQuery("INSERT INTO `#__rscomments_messages` SET `content` = ".$db->q($value).", `type` = ".$db->q($name).", `tag` = 'en-GB' ");
								$db->execute();
							}
						}
					}
				}
			}
			
			$db->setQuery("SHOW TABLES FROM `".JFactory::getConfig()->get('db')."` LIKE '%".JFactory::getConfig()->get('dbprefix')."rscomments_terms%'");
			if ($db->loadResult()) {
				$db->setQuery("SELECT `tag`, `content` FROM `#__rscomments_terms`");
				if ($terms = $db->loadObjectList()) {
					foreach ($terms as $term) {
						$db->setQuery("SELECT `id` FROM `#__rscomments_messages` WHERE `tag` = ".$db->q($term->tag)." AND `type` = 'terms' ");
						if ($messageid = $db->loadResult()) {
							$db->setQuery("UPDATE `#__rscomments_messages` SET `content` = ".$db->q($term->content)." WHERE `id` = ".(int) $messageid." ");
							$db->execute();
						} else {
							$db->setQuery("INSERT INTO `#__rscomments_messages` SET `content` = ".$db->q($term->content).", `type` = 'terms', `tag` = ".$db->q($term->tag)." ");
							$db->execute();
						}
					}
				}
			}
			
			// Do we have mb4 utf8 support?
			$hasUTF8mb4Support = $db->hasUTF8mb4Support();
			
			// Set default values on database fields
			if ($tables = $db->getTableList()) {
				foreach ($tables as $table) {
					if (strpos($table, $db->getPrefix().'rscomments') !== false) {
						
						// Change table collation
						if ($hasUTF8mb4Support) {
							$db->setQuery('SHOW TABLE STATUS WHERE name like '.$db->q($table));
							if ($tableDetails = $db->loadObject()) {
								if (strpos(strtolower($tableDetails->Collation), 'utf8_general') !== false) {
									$db->setQuery('ALTER TABLE '.$db->qn($table).' DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_unicode_ci;');
									$db->execute();
								}
							}
						}
						
						if ($fields = $db->getTableColumns($table, false)) {
							foreach ($fields as $field) {
								$fieldType = strtolower($field->Type);
								$fieldKey = strtolower($field->Key);
								$collation = strtolower($field->Collation);
								
								if ($hasUTF8mb4Support && strpos($collation, 'utf8_general') !== false && (strpos($fieldType, 'varchar') !== false || strpos($fieldType, 'text') !== false)) {
									$db->setQuery('ALTER TABLE '.$db->qn($table).' CHANGE '.$db->qn($field->Field).' '.$db->qn($field->Field).' '.$field->Type.' CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;');
									$db->execute();
								}
								
								if (strpos($fieldType, 'int') !== false || strpos($fieldType, 'float') !== false|| strpos($fieldType, 'decimal') !== false) {
									if ($fieldKey != 'pri') {
										$db->setQuery('ALTER TABLE '.$db->qn($table).' ALTER '.$db->qn($field->Field).' SET DEFAULT '.$db->q(0));
										$db->execute();
									}
								} elseif (strpos($fieldType, 'varchar') !== false) {
									$db->setQuery('ALTER TABLE '.$db->qn($table).' ALTER '.$db->qn($field->Field).' SET DEFAULT '.$db->q(''));
									$db->execute();
								} elseif (strpos($fieldType, 'datetime') !== false) {
									$db->setQuery('ALTER TABLE '.$db->qn($table).' ALTER '.$db->qn($field->Field).' SET DEFAULT '.$db->q($db->getNullDate()));
									$db->execute();
								}
							}
						}
					}
				}
			}
			
			$db->setQuery("SHOW COLUMNS FROM `#__rscomments_groups` WHERE `Field` = 'joomla'");
			if ($db->loadResult()) {
				$db->setQuery("ALTER TABLE `#__rscomments_groups` DROP `joomla`");
				$db->execute();
			}
			
			$db->setQuery("SHOW COLUMNS FROM `#__rscomments_comments` WHERE `Field` = 'modified_by'");
			if (!$db->loadResult()) {
				$db->setQuery("ALTER TABLE `#__rscomments_comments` ADD `modified_by` INT NOT NULL AFTER `date`");
				$db->execute();
			}
			
			$db->setQuery("SHOW COLUMNS FROM `#__rscomments_comments` WHERE `Field` = 'modified'");
			if (!$db->loadResult()) {
				$db->setQuery("ALTER TABLE `#__rscomments_comments` ADD `modified` DATETIME NOT NULL AFTER `modified_by`");
				$db->execute();
			}
			
			// Remove the configuration  table
			$db->setQuery("DROP TABLE IF EXISTS `#__rscomments_config`");
			$db->execute();
			
			// Remove the terms table
			$db->setQuery("DROP TABLE IF EXISTS `#__rscomments_terms`");
			$db->execute();
			
			# Version 1.12.0
			$db->setQuery("SELECT `id` FROM `#__rscomments_messages` WHERE `type` = ".$db->q('report_message')." AND `tag` = ".$db->q('en-GB')." ");
			if (!$db->loadResult()) {
				$db->setQuery("INSERT IGNORE INTO `#__rscomments_messages` (`id`, `type`, `tag`, `content`) VALUES(NULL, 'report_message', 'en-GB', '<p>Hello,</p>\r\n<p>\"{user}\" reported the comment written by \"{author}\" on {date}.</p>\r\n<p>The report reason was :</p>\r\n<p>\"{report}\"</p>\r\n<p>Click <a href=\"{preview}\">here</a> to view the comment.</p>');");
				$db->execute();
			}
			
			# Version 1.13.3
			$db->setQuery("SHOW COLUMNS FROM `#__rscomments_comments` WHERE `Field` = 'location'");
			if (!$db->loadResult()) {
				$db->setQuery("ALTER TABLE `#__rscomments_comments` ADD `location` VARCHAR( 255 ) NOT NULL AFTER `file`");
				$db->execute();
			}
			
			$db->setQuery("SHOW COLUMNS FROM `#__rscomments_comments` WHERE `Field` = 'coordinates'");
			if (!$db->loadResult()) {
				$db->setQuery("ALTER TABLE `#__rscomments_comments` ADD `coordinates` VARCHAR( 255 ) NOT NULL AFTER `location`");
				$db->execute();
			}
			
			// Replace emoticons path
			$db->setQuery("SELECT `id`, `with` FROM `#__rscomments_emoticons`");
			if ($emoticons = $db->loadObjectList()) {
				foreach ($emoticons as $emoticon) {
					$image = str_replace('components/com_rscomments/assets/images/emoticons/', 'media/com_rscomments/images/emoticons/', $emoticon->with);
					$db->setQuery('UPDATE `#__rscomments_emoticons` SET `with` = '.$db->q($image).' WHERE `id` = '.$db->q($emoticon->id));
					$db->execute();
				}
			}
			
			# Version 1.13.11
			$db->setQuery("SHOW COLUMNS FROM `#__rscomments_comments` WHERE `Field` = 'hash'");
			if (!$db->loadResult()) {
				$db->setQuery("ALTER TABLE `#__rscomments_comments` ADD `hash` VARCHAR( 32 ) NOT NULL AFTER `ip`");
				$db->execute();
			}
			
			$db->setQuery("SHOW COLUMNS FROM `#__rscomments_comments` WHERE `Field` = 'sid'");
			if (!$db->loadResult()) {
				$db->setQuery("ALTER TABLE `#__rscomments_comments` ADD `sid` VARCHAR( 255 ) NOT NULL AFTER `hash`");
				$db->execute();
			}
			
			$db->setQuery("SHOW COLUMNS FROM `#__rscomments_comments` WHERE `Field` = 'anonymous'");
			if (!$db->loadResult()) {
				$db->setQuery("ALTER TABLE `#__rscomments_comments` ADD `anonymous` TINYINT( 2 ) NOT NULL AFTER `coordinates`");
				$db->execute();
			}
			
			$db->setQuery("SHOW COLUMNS FROM `#__rscomments_comments` WHERE `Field` = 'emails'");
			if (!$db->loadResult()) {
				$db->setQuery("ALTER TABLE `#__rscomments_comments` ADD `emails` TINYINT( 2 ) NOT NULL AFTER `anonymous`");
				$db->execute();
			}
			
			$db->setQuery("SHOW COLUMNS FROM `#__rscomments_votes` WHERE `Field` = 'ip'");
			$ipField = $db->loadObject();
			if (strtolower($ipField->Type) == 'varchar(15)') {
				$db->setQuery("ALTER TABLE `#__rscomments_votes` CHANGE `ip` `ip` VARCHAR(32) NOT NULL DEFAULT ''");
				$db->execute();
			}
			
			$db->setQuery("SELECT `params` FROM `#__extensions` WHERE `extension_id` = ".(int) $extension_id);
			if ($params = $db->loadResult()) {
				$registry = new JRegistry;
				$registry->loadString($params);
				
				// Update config
				$newconfig = array('enable_reports' => 1, 'enable_captcha_reports' => 1, 'enable_email_reports' => 0, 'report_emails' => '', 'negative_count' => '10', 'blocked_ips' => '', 'fontawesome' => '1', 'show_labels' => '0', 'enable_location' => '0', 'fontawesome_admin' => '1', 'store_ip' => 1, 'consent' => 1, 'anonymous' => 0, 'enable_name_field' => 1, 'enable_email_field' => 1, 'modal' => 1, 'cache' => 1, 'enable_usercomments' => 1);
				
				foreach ($newconfig as $name => $value) {
					if (is_null($registry->get($name, null))) {
						$registry->set($name,$value);
					}
				}
				
				$db->setQuery("UPDATE `#__extensions` SET `params` = ".$db->q($registry->toString())." WHERE `extension_id` = ".(int) $extension_id);
				$db->execute();
			}
			
			# Version 1.13.13
			JFactory::getApplication()->enqueueMessage('If your template does have overrides for RSComments!, please make sure to correct them accordingly.', 'error');
		}
		
		$messages = $this->checkAddons();
		$this->showinstall($messages);
	}
	
	protected function runSql() {
		$db = JFactory::getDbo();
		
		$sqlfile = JPATH_ADMINISTRATOR.'/components/com_rscomments/install.sql';
		$buffer = file_get_contents($sqlfile);
		if ($buffer === false) {
			throw new Exception(JText::_('JLIB_INSTALLER_ERROR_SQL_READBUFFER'), 1);
			return false;
		}

		jimport('joomla.installer.helper');
		$queries = $db->splitSql($buffer);
		if (count($queries) == 0) {
			// No queries to process
			return 0;
		}
		
		// Process each query in the $queries array (split out of sql file).
		foreach ($queries as $query) {
			$query = trim($query);
			if ($query != '' && $query{0} != '#') {
				$db->setQuery($query);
				if (!$db->execute()) {
					throw new Exception(JText::_('JLIB_INSTALLER_ERROR_SQL_ERROR'), 1);
					return false;
				}
			}
		}
	}
	
	public function uninstall($parent) {
		$db			= JFactory::getDbo();
		$query		= $db->getQuery(true);
		$installer	= new JInstaller();

		// Remove the system plugin
		$query->clear()
			->select($db->qn('extension_id'))
			->from($db->qn('#__extensions'))
			->where($db->qn('element').' = '.$db->q('rscomments'))
			->where($db->qn('type').' = '.$db->q('plugin'))
			->where($db->qn('folder').' = '.$db->q('system'));
		$db->setQuery($query,0,1);
		$plugin = $db->loadResult();
		
		if ($plugin) 
			$installer->uninstall('plugin', $plugin);

		// Remove the content plugin
		$query->clear()
			->select($db->qn('extension_id'))
			->from($db->qn('#__extensions'))
			->where($db->qn('element').' = '.$db->q('rscomments'))
			->where($db->qn('type').' = '.$db->q('plugin'))
			->where($db->qn('folder').' = '.$db->q('content'));
		$db->setQuery($query,0,1);
		$plugin = $db->loadResult();
		
		if ($plugin) 
			$installer->uninstall('plugin', $plugin);

		// Remove the editors-xtd plugin
		$query->clear()
			->select($db->qn('extension_id'))
			->from($db->qn('#__extensions'))
			->where($db->qn('element').' = '.$db->q('rscomments'))
			->where($db->qn('type').' = '.$db->q('plugin'))
			->where($db->qn('folder').' = '.$db->q('editors-xtd'));
		$db->setQuery($query,0,1);
		$plugin = $db->loadResult();
		
		if ($plugin) 
			$installer->uninstall('plugin', $plugin);
		
		// Remove the updater plugin
		$query->clear()
			->select($db->qn('extension_id'))
			->from($db->qn('#__extensions'))
			->where($db->qn('element').' = '.$db->q('rscomments'))
			->where($db->qn('type').' = '.$db->q('plugin'))
			->where($db->qn('folder').' = '.$db->q('installer'));
		$db->setQuery($query,0,1);
		$iplugin = $db->loadResult();
		
		if ($iplugin) 
			$installer->uninstall('plugin', $iplugin);
			
		$this->showUninstall();
	}
	
	protected function checkAddons() {
		$messages	= array();
		$lang		= JFactory::getLanguage();
		
		$plugins = array(
			'jacomment' => '1.1',
			'jcomments' => '1.1',
			'komento' => '1.1'
		);
		
		// Check plugins version
		if ($installed = $this->getPlugins($plugins)) {
			foreach ($installed as $plugin) {
				$file = JPATH_SITE.'/plugins/'.$plugin->folder.'/'.$plugin->element.'/'.$plugin->element.'.xml';
				if (file_exists($file)) {
					$xml = file_get_contents($file);
					
					if ($this->checkVersion($xml, $plugins[$plugin->element], '>') || strpos($xml, '<extension') === false) {
						$lang->load($plugin->element, JPATH_ADMINISTRATOR);
						$this->disableExtension($plugin->extension_id);
						$messages[] = 'Please update the plugin "'.JText::_($plugin->name).'" manually.';
					}
				}
			}
		}
		
		$modules = array(
			'mod_rscomments_latest' => '1.3'
		);
		
		// Check modules version
		if ($installed = $this->getModules($modules)) {
			foreach ($installed as $module) {
				$file = JPATH_SITE.'/modules/'.$module->element.'/'.$module->element.'.xml';
				if (file_exists($file)) {
					$xml = file_get_contents($file);
					
					if ($this->checkVersion($xml, $modules[$module->element], '>') || strpos($xml, '<install') !== false) {
						$lang->load($module->element, JPATH_SITE);
						$this->unpublishModule($module->element);
						$messages[] = 'Please update the module "'.JText::_($module->name).'" manually.';
					}
				}
			}
		}
		
		return $messages;
	}
	
	protected function disableExtension($extension_id) {
		$db = JFactory::getDbo();
		
		$query = $db->getQuery(true)->update('#__extensions')
			->set($db->qn('enabled').'='.$db->q(0))
			->where($db->qn('extension_id').'='.$db->q($extension_id));
		
		$db->setQuery($query);
		$db->execute();
	}
	
	protected function unpublishModule($module) {
		$db = JFactory::getDbo();
		
		$query = $db->getQuery(true)->update('#__modules')
			->set($db->qn('published').'='.$db->q(0))
			->where($db->qn('module').'='.$db->q($module));
		
		$db->setQuery($query);
		$db->execute();
	}
	
	protected function getModules($modules) {
		$db			= JFactory::getDbo();
		$elements	= array_keys($modules);
		
		$query = $db->getQuery(true)->select('*')
			->from('#__extensions')
			->where($db->qn('type').'='.$db->q('module'))
			->where($db->qn('element').' IN ('.$this->quoteImplode($elements).')');
		$db->setQuery($query);
		
		return $db->loadObjectList();
	}
	
	protected function getPlugins($plugins) {
		$db			= JFactory::getDbo();
		$elements	= array_keys($plugins);
		
		$query = $db->getQuery(true)->select('*')
			->from('#__extensions')
			->where($db->qn('type').'='.$db->q('plugin'))
			->where($db->qn('folder').' = '.$db->q('rscomments'))
			->where($db->qn('element').' IN ('.$this->quoteImplode($elements).')');
		$db->setQuery($query);
		
		return $db->loadObjectList();
	}
	
	protected function quoteImplode($array) {
		$db = JFactory::getDbo();
		foreach ($array as $k => $v) {
			$array[$k] = $db->q($v);
		}
		
		return implode(',', $array);
	}
	
	protected function escape($string) {
		return htmlentities($string, ENT_COMPAT, 'utf-8');
	}
	
	protected function checkVersion($string, $version, $operator = '>') {
		preg_match('#<version>(.*?)<\/version>#is',$string,$match);
		if (isset($match) && isset($match[1])) {
			return version_compare($version,$match[1],$operator);
		}
		
		return false;
	}
	
	// Set the install message
	public function showinstall($messages) {
?>
<style type="text/css">
#rsc-installer-left {
	float: left;
	width: 270px;
	padding: 5px;
	margin: 20px 0 20px 0;
}

#rsc-installer-right {
	float: left;
}

.version-history {
	margin: 0 0 2em 0;
	padding: 0;
	list-style-type: none;
}

.version-history > li {
	margin: 0 0 0.5em 0;
	padding: 0 0 0 4em;
}

.version,
.version-new,
.version-fixed,
.version-upgraded {
	float: left;
	font-size: 0.8em;
	margin-left: -4.9em;
	width: 4.5em;
	color: white;
	text-align: center;
	font-weight: bold;
	text-transform: uppercase;
	-webkit-border-radius: 4px;
	-moz-border-radius: 4px;
	border-radius: 4px;
}

.version { background: #000; }
.version-new { background: #7dc35b; }
.version-fixed { background: #e9a130; }
.version-upgraded { background: #61b3de; }

.com-rscomments-button {
	display: inline-block;
	background: #459300 none repeat scroll 0 0;
	color: #fff !important;
	cursor: pointer;
	margin-bottom: 10px;
    padding: 7px;
	text-decoration: none !important;
}

.rsc-messages {
	padding: 8px 35px 8px 14px;
	margin-bottom: 18px;
	text-shadow: 0 1px 0 rgba(255,255,255,0.5);
	background-color: #f2dede;
	border-color: #ebccd1;
	color: #a94442;
	-webkit-border-radius: 4px;
	-moz-border-radius: 4px;
	border-radius: 4px;
}

.rsc-messages > p {
    margin: 0 0 5px !important;
}
</style>

<div id="rsc-installer-left">
	<img src="<?php echo JUri::root(); ?>media/com_rscomments/images/rscomments-box.jpg" alt="RSComments! Box" />
</div>
<div id="rsc-installer-right">
	<?php if ($messages) { ?>
	<div class="rsc-messages">
		<?php foreach ($messages as $message) { ?>
			<p><i class="icon-info"></i> <?php echo $message; ?></p>
		<?php } ?>
	</div>
	<?php } ?>
	
	<h2>Changelog v1.13.19</h2>
	<ul class="version-history">
		<li><span class="version-fixed">Fix</span> The comment vote was not visible for users who haven't voted yet.</li>
		<li><span class="version-upgraded">Upg</span> All RSComments! tabels are utf8mb4.</li>
	</ul>
	<a class="com-rscomments-button" href="index.php?option=com_rscomments">Start using RSComments!</a>
	<a class="com-rscomments-button" href="http://www.rsjoomla.com/support/documentation/view-knowledgebase/95-rscomments.html" target="_blank">Read the RSComments! User Guide</a>
	<a class="com-rscomments-button" href="http://www.rsjoomla.com/customer-support/tickets.html" target="_blank">Get Support!</a>
</div>
<div style="clear: both;"></div>
<?php
	}
	
	public function showUninstall() {
		echo 'RSComments! component has been successfully uninstaled!';
	}
}