<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined( '_JEXEC' ) or die( 'Restricted access' );

class com_rseventsproInstallerScript 
{	
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
		$this->installprocess($type, $parent);
		
		$messages = $this->checkAddons();
		
		$this->showinstall($messages);
	}
	
	public function uninstall($parent) {
		$db			= JFactory::getDbo();
		$installer	= new JInstaller();
		
		$db->setQuery('SELECT '.$db->qn('extension_id').' FROM '.$db->qn('#__extensions').' WHERE '.$db->qn('element').' = '.$db->q('rseventspro').' AND '.$db->qn('folder').' = '.$db->q('installer').' AND '.$db->qn('type').' = '.$db->q('plugin').' LIMIT 1');
		$plg_id = $db->loadResult();
		if ($plg_id) $installer->uninstall('plugin', $plg_id);
		
		$db->setQuery('SELECT '.$db->qn('extension_id').' FROM '.$db->qn('#__extensions').' WHERE '.$db->qn('element').' = '.$db->q('rseventspro').' AND '.$db->qn('folder').' = '.$db->q('provacy').' AND '.$db->qn('type').' = '.$db->q('plugin').' LIMIT 1');
		$priv_id = $db->loadResult();
		if ($priv_id) $installer->uninstall('plugin', $priv_id);
	}
	
	// Install - Update process
	public function installprocess($type, $parent) {
		$db	= JFactory::getDbo();
		$installer = new JInstaller();
		
		$installer->install($parent->getParent()->getPath('source').'/other/plg_installer');
		$db->setQuery('UPDATE '.$db->qn('#__extensions').' SET '.$db->qn('enabled').' = 1 WHERE '.$db->qn('element').' = '.$db->q('rseventspro').' AND '.$db->qn('type').' = '.$db->q('plugin').' AND '.$db->qn('folder').' = '.$db->q('installer'));
		$db->execute();
		
		$installer->install($parent->getParent()->getPath('source').'/other/plg_rseventsproprivacy');
		$db->setQuery('UPDATE '.$db->qn('#__extensions').' SET '.$db->qn('enabled').' = 1 WHERE '.$db->qn('element').' = '.$db->q('rseventspro').' AND '.$db->qn('type').' = '.$db->q('plugin').' AND '.$db->qn('folder').' = '.$db->q('privacy'));
		$db->execute();
		
		
		if ($type == 'update') {
			// REV 4
			
			// Check for the sync field
			$db->setQuery("SHOW COLUMNS FROM `#__rseventspro_events` WHERE `Field` = 'sync'");
			if (!$db->loadResult()) {
				$db->setQuery("ALTER TABLE `#__rseventspro_events` ADD `sync` TINYINT( 1 ) NOT NULL");
				$db->execute();
			}

			// Check for the sid field
			$db->setQuery("SHOW COLUMNS FROM `#__rseventspro_events` WHERE `Field` = 'sid'"); 
			if (!$db->loadResult()) {
				$db->setQuery("ALTER TABLE `#__rseventspro_events` ADD `sid` VARCHAR( 255 ) NOT NULL");
				$db->execute();
			}

			// Check for the lang field
			$db->setQuery("SHOW COLUMNS FROM `#__rseventspro_users` WHERE `Field` = 'lang'");
			if (!$db->loadResult()) {
				$db->setQuery("ALTER TABLE `#__rseventspro_users` ADD `lang` VARCHAR( 10 ) NOT NULL");
				$db->execute();
			}

			// Check for the coupon field
			$db->setQuery("SHOW COLUMNS FROM `#__rseventspro_users` WHERE `Field` = 'coupon'");
			if (!$db->loadResult()) {
				$db->setQuery("ALTER TABLE `#__rseventspro_users` ADD `coupon` VARCHAR( 255 ) NOT NULL");
				$db->execute();
			}
			
			// Check and remove the 'code' field from the coupons table
			$db->setQuery("SHOW COLUMNS FROM `#__rseventspro_coupons` WHERE `Field` = 'code'");
			if ($db->loadResult()) {
				// Get coupon codes and add them in the new coupon codes table
				$db->setQuery("SELECT `id`, `code`, `used` FROM `#__rseventspro_coupons`");
				if ($coupons = $db->loadObjectList()) {
					foreach ($coupons as $coupon) {
						if (!empty($coupon->code)) {
							$codes = explode("\n",$coupon->code);
							if(!empty($codes)) {
								foreach ($codes as $code) {				
									$code = trim($code);
									$db->setQuery("INSERT INTO `#__rseventspro_coupon_codes` SET `code` = '".$db->escape($code)."', `idc` = ".(int) $coupon->id.", `used` = ".(int) $coupon->used." ");
									$db->execute();
								}
							}
						}
					}
				}
				
				$db->setQuery("ALTER TABLE `#__rseventspro_coupons` DROP `code`");
				$db->execute();
			}

			// Check and remove the 'used' field from the coupons table
			$db->setQuery("SHOW COLUMNS FROM `#__rseventspro_coupons` WHERE `Field` = 'used'");
			if ($db->loadResult()) {
				$db->setQuery("ALTER TABLE `#__rseventspro_coupons` DROP `used`");
				$db->execute();
			}
			
			// Set the tax_value field to float
			$db->setQuery("SHOW COLUMNS FROM `#__rseventspro_payments` WHERE `Field` = 'tax_value'");
			$paymentsTable = $db->loadObject();
			if ($paymentsTable->Type == 'int(11)') {
				$db->setQuery("ALTER TABLE `#__rseventspro_payments` CHANGE `tax_value` `tax_value` FLOAT NOT NULL");
				$db->execute();
			}
			
			// Check for the 'allday' field on the events table
			$db->setQuery("SHOW COLUMNS FROM `#__rseventspro_events` WHERE `Field` = 'allday'");
			if (!$db->loadResult()) {
				$db->setQuery("ALTER TABLE `#__rseventspro_events` ADD `allday` TINYINT( 1 ) NOT NULL");
				$db->execute();
			}
			
			// Check for the 'notify_me_unsubscribe' field on the events table
			$db->setQuery("SHOW COLUMNS FROM `#__rseventspro_events` WHERE `Field` = 'notify_me_unsubscribe'");
			if (!$db->loadResult()) {
				$db->setQuery("ALTER TABLE `#__rseventspro_events` ADD `notify_me_unsubscribe` TINYINT( 1 ) NOT NULL AFTER `notify_me`");
				$db->execute();
			}
			
			// Check for the 'ideal' field on the subscribers table
			$db->setQuery("SHOW COLUMNS FROM `#__rseventspro_users` WHERE `Field` = 'ideal'");
			if (!$db->loadResult()) {
				$db->setQuery("ALTER TABLE `#__rseventspro_users` ADD `ideal` VARCHAR( 100 ) NOT NULL");
				$db->execute();
			}
			
			// Update groups table
			$db->setQuery("SHOW COLUMNS FROM `#__rseventspro_groups` WHERE `Field` = 'can_add_locations'");
			if (!$db->loadResult()) {
				$db->setQuery("ALTER TABLE `#__rseventspro_groups` ADD `can_add_locations` TINYINT( 1 ) NOT NULL");
				$db->execute();
			}
			
			$db->setQuery("SHOW COLUMNS FROM `#__rseventspro_groups` WHERE `Field` = 'can_create_categories'");
			if (!$db->loadResult()) {
				$db->setQuery("ALTER TABLE `#__rseventspro_groups` ADD `can_create_categories` TINYINT( 1 ) NOT NULL");
				$db->execute();
			}
			
			$db->setQuery("SHOW COLUMNS FROM `#__rseventspro_groups` WHERE `Field` = 'can_delete_events'");
			if (!$db->loadResult()) {
				$db->setQuery("ALTER TABLE `#__rseventspro_groups` ADD `can_delete_events` TINYINT( 1 ) NOT NULL");
				$db->execute();
			}
			
			$db->setQuery("SHOW COLUMNS FROM `#__rseventspro_groups` WHERE `Field` = 'can_download'");
			if (!$db->loadResult()) {
				$db->setQuery("ALTER TABLE `#__rseventspro_groups` ADD `can_download` TINYINT( 1 ) NOT NULL");
				$db->execute();
			}
			
			$db->setQuery("SHOW COLUMNS FROM `#__rseventspro_groups` WHERE `Field` = 'can_edit_events'");
			if (!$db->loadResult()) {
				$db->setQuery("ALTER TABLE `#__rseventspro_groups` ADD `can_edit_events` TINYINT( 1 ) NOT NULL");
				$db->execute();
			}
			
			$db->setQuery("SHOW COLUMNS FROM `#__rseventspro_groups` WHERE `Field` = 'can_edit_locations'");
			if (!$db->loadResult()) {
				$db->setQuery("ALTER TABLE `#__rseventspro_groups` ADD `can_edit_locations` TINYINT( 1 ) NOT NULL");
				$db->execute();
			}
			
			$db->setQuery("SHOW COLUMNS FROM `#__rseventspro_groups` WHERE `Field` = 'can_post_events'");
			if (!$db->loadResult()) {
				$db->setQuery("ALTER TABLE `#__rseventspro_groups` ADD `can_post_events` TINYINT( 1 ) NOT NULL");
				$db->execute();
			}
			
			$db->setQuery("SHOW COLUMNS FROM `#__rseventspro_groups` WHERE `Field` = 'can_register'");
			if (!$db->loadResult()) {
				$db->setQuery("ALTER TABLE `#__rseventspro_groups` ADD `can_register` TINYINT( 1 ) NOT NULL");
				$db->execute();
			}
			
			$db->setQuery("SHOW COLUMNS FROM `#__rseventspro_groups` WHERE `Field` = 'can_repeat_events'");
			if (!$db->loadResult()) {
				$db->setQuery("ALTER TABLE `#__rseventspro_groups` ADD `can_repeat_events` TINYINT( 1 ) NOT NULL");
				$db->execute();
			}
			
			$db->setQuery("SHOW COLUMNS FROM `#__rseventspro_groups` WHERE `Field` = 'can_unsubscribe'");
			if (!$db->loadResult()) {
				$db->setQuery("ALTER TABLE `#__rseventspro_groups` ADD `can_unsubscribe` TINYINT( 1 ) NOT NULL");
				$db->execute();
			}
			
			$db->setQuery("SHOW COLUMNS FROM `#__rseventspro_groups` WHERE `Field` = 'can_upload'");
			if (!$db->loadResult()) {
				$db->setQuery("ALTER TABLE `#__rseventspro_groups` ADD `can_upload` TINYINT( 1 ) NOT NULL");
				$db->execute();
			}
			
			$db->setQuery("SHOW COLUMNS FROM `#__rseventspro_groups` WHERE `Field` = 'event_moderation'");
			if (!$db->loadResult()) {
				$db->setQuery("ALTER TABLE `#__rseventspro_groups` ADD `event_moderation` TINYINT( 1 ) NOT NULL");
				$db->execute();
			}
			
			$db->setQuery("SHOW COLUMNS FROM `#__rseventspro_groups` WHERE `Field` = 'tag_moderation'");
			if (!$db->loadResult()) {
				$db->setQuery("ALTER TABLE `#__rseventspro_groups` ADD `tag_moderation` TINYINT( 1 ) NOT NULL");
				$db->execute();
			}
			
			$db->setQuery("SHOW COLUMNS FROM `#__rseventspro_groups` WHERE `Field` = 'can_approve_events'");
			if (!$db->loadResult()) {
				$db->setQuery("ALTER TABLE `#__rseventspro_groups` ADD `can_approve_events` TINYINT( 1 ) NOT NULL");
				$db->execute();
			}
			
			$db->setQuery("SHOW COLUMNS FROM `#__rseventspro_groups` WHERE `Field` = 'can_approve_tags'");
			if (!$db->loadResult()) {
				$db->setQuery("ALTER TABLE `#__rseventspro_groups` ADD `can_approve_tags` TINYINT( 1 ) NOT NULL");
				$db->execute();
			}
			
			// Update groups table with data
			$tables = $db->getTableList();
			if (in_array($db->getPrefix().'rseventspro_group_permissions', $tables)) {
				$db->setQuery("SELECT * FROM `#__rseventspro_group_permissions`");
				if ($permissions = $db->loadObjectList()) {
					foreach ($permissions as $permission) {
						$db->setQuery("UPDATE #__rseventspro_groups SET `".$permission->name."` = '".$db->escape($permission->value)."' WHERE `id` = '".(int) $permission->id."' ");
						$db->execute();
					}
				}
			}
			
			// Drop groups permissions table
			$db->setQuery("DROP TABLE IF EXISTS `#__rseventspro_group_permissions`");
			$db->execute();
			
			// Update Categories
			if (in_array($db->getPrefix().'rseventspro_categories', $tables)) {
				$db->setQuery("SHOW COLUMNS FROM `#__rseventspro_categories` WHERE `Field` = 'ordering'");
				if ($db->loadResult()) {
					$db->setQuery("SELECT `id`, `parent` FROM `#__rseventspro_categories`");
					if ($tmpCategories = $db->loadObjectList()) {
						$categories = array();
						$parents = array();
						foreach ($tmpCategories as $category) {
							$parents[$category->id] = $category->parent;
						}
						
						$tree = $levels = array();
						$this->renderTree($tmpCategories,$tree,$levels);
						$flatCateories = $this->renderFlatTree($tree);
						
						if (!empty($flatCateories)) {
							foreach ($flatCateories as $flatCategory) {
								$db->setQuery("SELECT `id`, `parent`, `name`, `color`, `description`, `published` FROM `#__rseventspro_categories` WHERE id = ".(int) $flatCategory."");
								$categories[] = $db->loadObject();
							}
						}
						
						$newids = array();
						$newids[0] = 1;
						$uid = JFactory::getUser()->get('id');
						
						JTable::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_categories/tables/');
						foreach ($categories as $category) {
							$table = JTable::getInstance('Category', 'CategoriesTable');
							
							$table->id = null;
							$table->title = $category->name;
							$table->alias = JApplicationHelper::stringURLSafe($category->name);
							$table->extension = 'com_rseventspro';
							$table->setLocation($newids[$parents[$category->id]], 'last-child');
							$table->description = $category->description;
							$table->created_user_id = $uid;
							$table->language = '*';
							$table->published = $category->published;
							$registry = new JRegistry();
							$registry->loadArray(array('color' => $category->color));
							$table->params	= $registry->toString();
							
							
							$table->store();
							$table->rebuildPath($table->id);
							$table->rebuild($table->id, $table->lft, $table->level, $table->path);
							$newids[$category->id] = $table->id;
						}
						
						unset($newids[0]);
						
						if (!empty($newids)) {
							$db->setQuery("SELECT `ide`, `id` FROM `#__rseventspro_taxonomy` WHERE `type` = 'category'");
							if ($relations = $db->loadObjectList()) {
								$db->setQuery("DELETE FROM `#__rseventspro_taxonomy` WHERE `type` = 'category'");
								$db->execute();
							}
							
							foreach ($relations as $relation) {
								if (isset($newids[$relation->id])) {
									$db->setQuery("INSERT INTO #__rseventspro_taxonomy SET `ide` = ".(int) $relation->ide." , `id` = ".(int) $newids[$relation->id].", `type` = 'category'");
									$db->execute();
								}
							}
							
							// Update calendar menus
							$db->setQuery("SELECT `id`, `params` FROM #__menu WHERE `link` LIKE 'index.php?option=com_rseventspro&view=calendar'");
							if ($calendarMenus = $db->loadObjectList()) {
								foreach ($calendarMenus as $calendarMenu) {
									$registry = new JRegistry;
									$registry->loadString($calendarMenu->params);
		
									$categories = $registry->get('categories');
									if (!empty($categories)) {
										$categories = explode(',',$categories);
										foreach ($categories as $i => $category) {
											$categories[$i] = $newids[$category];
										}
									} else {
										$categories = '';
									}
									
									$locations = $registry->get('locations');
									$locations = !empty($locations) ? explode(',',$locations) : '';
									
									$tags = $registry->get('tags');
									$tags = !empty($tags) ? explode(',',$tags) : '';
									
									$registry->set('categories',$categories);
									$registry->set('locations',$locations);
									$registry->set('tags',$tags);
									
									$db->setQuery("UPDATE `#__menu` SET `params` = ".$db->q($registry->toString())." WHERE `id` = ".(int) $calendarMenu->id." ");
									$db->execute();
								}
							}
							
							// Update events menus
							$db->setQuery("SELECT `id`, `params` FROM #__menu WHERE `link` LIKE 'index.php?option=com_rseventspro&view=rseventspro'");
							if ($eventsMenus = $db->loadObjectList()) {
								foreach ($eventsMenus as $eventsMenu) {
									$registry = new JRegistry;
									$registry->loadString($eventsMenu->params);
		
									$categories = $registry->get('categories');
									if (!empty($categories)) {
										$categories = explode(',',$categories);
										foreach ($categories as $i => $category) {
											$categories[$i] = $newids[$category];
										}
									} else {
										$categories = '';
									}
									
									$locations = $registry->get('locations');
									$locations = !empty($locations) ? explode(',',$locations) : '';
									
									$tags = $registry->get('tags');
									$tags = !empty($tags) ? explode(',',$tags) : '';
									
									$registry->set('categories',$categories);
									$registry->set('locations',$locations);
									$registry->set('tags',$tags);
									
									$db->setQuery("UPDATE `#__menu` SET `params` = ".$db->q($registry->toString())." WHERE `id` = ".(int) $eventsMenu->id." ");
									$db->execute();
								}
							}
							
							// Update map menus
							$db->setQuery("SELECT `id`, `params` FROM #__menu WHERE `link` LIKE 'index.php?option=com_rseventspro&view=rseventspro&layout=map'");
							if ($mapMenus = $db->loadObjectList()) {
								foreach ($mapMenus as $mapMenu) {
									$registry = new JRegistry;
									$registry->loadString($mapMenu->params);
		
									$categories = $registry->get('categories');
									if (!empty($categories)) {
										$categories = explode(',',$categories);
										foreach ($categories as $i => $category) {
											$categories[$i] = $newids[$category];
										}
									} else {
										$categories = '';
									}
									
									$locations = $registry->get('locations');
									$locations = !empty($locations) ? explode(',',$locations) : '';
									
									$tags = $registry->get('tags');
									$tags = !empty($tags) ? explode(',',$tags) : '';
									
									$registry->set('categories',$categories);
									$registry->set('locations',$locations);
									$registry->set('tags',$tags);
									
									$db->setQuery("UPDATE `#__menu` SET `params` = ".$db->q($registry->toString())." WHERE `id` = ".(int) $mapMenu->id." ");
									$db->execute();
								}
							}
						}
						
						// Drop groups permissions table
						$db->setQuery("DROP TABLE IF EXISTS `#__rseventspro_categories`");
						$db->execute();
					}
				}
			}
			
			// Check for the 'enable' field in the emails table
			$db->setQuery("SHOW COLUMNS FROM `#__rseventspro_emails` WHERE `Field` = 'enable'");
			if (!$db->loadResult()) {
				$db->setQuery("ALTER TABLE `#__rseventspro_emails` ADD `enable` TINYINT( 1 ) NOT NULL AFTER `type`");
				$db->execute();
			}
			
			// Set enable option to the notification emails
			$db->setQuery("SELECT `value` FROM `#__rseventspro_config` WHERE name = 'email_registration_enable'");
			$registration = $db->loadResult();
			
			if (!is_null($registration)) {
				$db->setQuery("UPDATE `#__rseventspro_emails` SET `enable` = ".(int) $registration." WHERE `type` = 'registration'");
				$db->execute();
				$db->setQuery("DELETE FROM `#__rseventspro_config` WHERE `name` = 'email_registration_enable'");
				$db->execute();
			}
			
			$db->setQuery("SELECT `value` FROM `#__rseventspro_config` WHERE name = 'email_activation_enable'");
			$activation = $db->loadResult();
			
			if (!is_null($activation)) {
				$db->setQuery("UPDATE `#__rseventspro_emails` SET `enable` = ".(int) $activation." WHERE `type` = 'activation'");
				$db->execute();
				$db->setQuery("DELETE FROM `#__rseventspro_config` WHERE `name` = 'email_activation_enable'");
				$db->execute();
			}
			
			$db->setQuery("SELECT `value` FROM `#__rseventspro_config` WHERE name = 'email_unsubscribe_enable'");
			$unsubscribe = $db->loadResult();
			
			if (!is_null($unsubscribe)) {
				$db->setQuery("UPDATE `#__rseventspro_emails` SET `enable` = ".(int) $unsubscribe." WHERE `type` = 'unsubscribe'");
				$db->execute();
				$db->setQuery("DELETE FROM `#__rseventspro_config` WHERE `name` = 'email_unsubscribe_enable'");
				$db->execute();
			}
			
			$db->setQuery("SELECT `value` FROM `#__rseventspro_config` WHERE name = 'email_denied_enable'");
			$denied = $db->loadResult();
			
			if (!is_null($denied)) {
				$db->setQuery("UPDATE `#__rseventspro_emails` SET `enable` = ".(int) $denied." WHERE `type` = 'denied'");
				$db->execute();
				$db->setQuery("DELETE FROM `#__rseventspro_config` WHERE `name` = 'email_denied_enable'");
				$db->execute();
			}
			
			$db->setQuery("SELECT `value` FROM `#__rseventspro_config` WHERE name = 'email_reminder_enable'");
			$reminder = $db->loadResult();
			
			if (!is_null($reminder)) {
				$db->setQuery("UPDATE `#__rseventspro_emails` SET `enable` = ".(int) $reminder." WHERE `type` = 'reminder'");
				$db->execute();
				$db->setQuery("DELETE FROM `#__rseventspro_config` WHERE `name` = 'email_reminder_enable'");
				$db->execute();
			}
			
			$db->setQuery("SELECT `value` FROM `#__rseventspro_config` WHERE name = 'email_preminder_enable'");
			$preminder = $db->loadResult();
			
			if (!is_null($preminder)) {
				$db->setQuery("UPDATE `#__rseventspro_emails` SET `enable` = ".(int) $preminder." WHERE `type` = 'preminder'");
				$db->execute();
				$db->setQuery("DELETE FROM `#__rseventspro_config` WHERE `name` = 'email_preminder_enable'");
				$db->execute();
			}
			
			$db->setQuery("SELECT `value` FROM `#__rseventspro_config` WHERE name = 'email_invite_enable'");
			$invite = $db->loadResult();
			
			if (!is_null($invite)) {
				$db->setQuery("UPDATE `#__rseventspro_emails` SET `enable` = ".(int) $invite." WHERE `type` = 'invite'");
				$db->execute();
				$db->setQuery("DELETE FROM `#__rseventspro_config` WHERE `name` = 'email_invite_enable'");
				$db->execute();
			}
			
			// UPDATE event parameters
			$db->setQuery("SELECT `id`, `repeat_also`, `payments`, `options`, `gallery_tags`, `properties` FROM `#__rseventspro_events`");
			if ($events = $db->loadObjectList()) {
				foreach ($events as $event) {
					$repeat_also = $payments = $options = $gallery_tags = $properties = '';
					
					if (!empty($event->repeat_also)) {
						if (!$this->isJSON($event->repeat_also)) {
							$repeat_also = @unserialize($event->repeat_also);
							if ($repeat_also !== false) {
								$registry = new JRegistry;
								$registry->loadArray($repeat_also);
								$repeat_also = $registry->toString();
							}
						}
					}
					
					if (!empty($event->options)) {
						if (!$this->isJSON($event->options)) {
							$options = @unserialize($event->options);
							if ($options !== false) {
								$registry = new JRegistry;
								$registry->loadArray($options);
								$options = $registry->toString();
							}
						}
					}
					
					if (!empty($event->payments)) {
						if (!$this->isJSON($event->payments)) {
							$payments = explode(',',$event->payments);
							if ($payments !== false) {
								$registry = new JRegistry;
								$registry->loadArray($payments);
								$payments = $registry->toString();
							}
						}
					}
					
					if (!empty($event->gallery_tags)) {
						if (!$this->isJSON($event->gallery_tags)) {
							$gallery_tags = explode(',',$event->gallery_tags);
							if ($gallery_tags !== false) {
								$registry = new JRegistry;
								$registry->loadArray($gallery_tags);
								$gallery_tags = $registry->toString();
							}
						}
					}
					
					if (!empty($event->properties)) {
						if (!$this->isJSON($event->properties)) {
							$properties = explode(',',$event->properties);
							if ($properties !== false) {
								$registry = new JRegistry;
								$registry->loadArray($properties);
								$properties = $registry->toString();
							}
						}
					}
					
					if ($repeat_also) {
						$db->setQuery("UPDATE `#__rseventspro_events` SET `repeat_also` = '".$db->escape($repeat_also)."' WHERE `id` = ".(int) $event->id." ");
						$db->execute();
					}
					
					if ($payments) {
						$db->setQuery("UPDATE `#__rseventspro_events` SET `payments` = '".$db->escape($payments)."' WHERE `id` = ".(int) $event->id." ");
						$db->execute();
					}
					
					if ($options) {
						$db->setQuery("UPDATE `#__rseventspro_events` SET `options` = '".$db->escape($options)."' WHERE `id` = ".(int) $event->id." ");
						$db->execute();
					}
					
					if ($gallery_tags) {
						$db->setQuery("UPDATE `#__rseventspro_events` SET `gallery_tags` = '".$db->escape($gallery_tags)."' WHERE `id` = ".(int) $event->id." ");
						$db->execute();
					}
					
					if ($properties) {
						$db->setQuery("UPDATE `#__rseventspro_events` SET `properties` = '".$db->escape($properties)."' WHERE `id` = ".(int) $event->id." ");
						$db->execute();
					}
				}
			}
			
			// UPDATE locations parameters
			$db->setQuery("SELECT `id`, `gallery_tags` FROM `#__rseventspro_locations`");
			if ($locations = $db->loadObjectList()) {
				foreach ($locations as $location) {
					$gallery_tags = '';
					
					if (!empty($location->gallery_tags)) {
						if (!$this->isJSON($location->gallery_tags)) {
							$gallery_tags = explode(',',$location->gallery_tags);
							if ($gallery_tags !== false) {
								$registry = new JRegistry;
								$registry->loadArray($gallery_tags);
								$gallery_tags = $registry->toString();
							}
						}
					}
					
					if ($gallery_tags) {
						$db->setQuery("UPDATE `#__rseventspro_locations` SET `gallery_tags` = '".$db->escape($gallery_tags)."' WHERE `id` = ".(int) $location->id." ");
						$db->execute();
					}
				}
			}
			
			// Update menu
			$db->setQuery("SELECT `id`, `link` FROM #__menu WHERE `link` LIKE '%index.php?option=com_rseventspro&view=rseventspro&layout=show&cid=%'");
			if ($eventsLinks = $db->loadObjectList()) {
				$pattern = '#cid=([0-9]+)#is';
				foreach ($eventsLinks as $eventsLink) {
					preg_match($pattern,$eventsLink->link,$matches);
					if (!empty($matches[1])) $id = $matches[1]; else $id = 0;
					$db->setQuery("UPDATE `#__menu` SET `link` = 'index.php?option=com_rseventspro&view=rseventspro&layout=show&id=".(int) $id."' WHERE `id` = ".(int) $eventsLink->id." ");
					$db->execute();
				}
			}
			
			// START REV 5 UPDATE
			$db->setQuery("ALTER TABLE `#__rseventspro_events` CHANGE `late_fee` `late_fee` FLOAT NOT NULL");
			$db->execute();
			
			$db->setQuery("ALTER TABLE `#__rseventspro_events` CHANGE `early_fee` `early_fee` FLOAT NOT NULL");
			$db->execute();
			
			$db->setQuery("SHOW COLUMNS FROM `#__rseventspro_tickets` WHERE `Field` = 'position'");
			if (!$db->loadResult()) {
				$db->setQuery("ALTER TABLE `#__rseventspro_tickets` ADD `position` TEXT NOT NULL");
				$db->execute();
			}
			
			$db->setQuery("SHOW COLUMNS FROM `#__rseventspro_tickets` WHERE `Field` = 'groups'");
			if (!$db->loadResult()) {
				$db->setQuery("ALTER TABLE `#__rseventspro_tickets` ADD `groups` TEXT NOT NULL");
				$db->execute();
			}
			
			$db->setQuery("SHOW COLUMNS FROM `#__rseventspro_events` WHERE `Field` = 'ticketsconfig'");
			if (!$db->loadResult()) {
				$db->setQuery("ALTER TABLE `#__rseventspro_events` ADD `ticketsconfig` TINYINT( 1 ) NOT NULL");
				$db->execute();
			}
			
			$db->setQuery("SHOW COLUMNS FROM `#__rseventspro_events` WHERE `Field` = 'featured'");
			if (!$db->loadResult()) {
				$db->setQuery("ALTER TABLE `#__rseventspro_events` ADD `featured` TINYINT( 1 ) NOT NULL");
				$db->execute();
			}
			
			$db->setQuery("SHOW COLUMNS FROM `#__rseventspro_groups` WHERE `Field` = 'event'");
			if (!$db->loadResult()) {
				$db->setQuery("ALTER TABLE `#__rseventspro_groups` ADD `event` TEXT NOT NULL");
				$db->execute();
			}
			// END REV 5 UPDATE
			
			// START VERSION 1.6.0 UPDATE
			
			$db->setQuery("SHOW COLUMNS FROM `#__rseventspro_events` WHERE `Field` = 'hits'");
			if (!$db->loadResult()) {
				$db->setQuery("ALTER TABLE `#__rseventspro_events` ADD `hits` INT( 11 ) NOT NULL");
				$db->execute();
			}
			
			$db->setQuery("SHOW COLUMNS FROM `#__rseventspro_events` WHERE `Field` = 'repeat_on_type'");
			if (!$db->loadResult()) {
				$db->setQuery("ALTER TABLE `#__rseventspro_events` ADD `repeat_on_type` TINYINT( 1 ) NOT NULL AFTER `repeat_also`");
				$db->execute();
			}
			
			$db->setQuery("SHOW COLUMNS FROM `#__rseventspro_events` WHERE `Field` = 'repeat_on_day'");
			if (!$db->loadResult()) {
				$db->setQuery("ALTER TABLE `#__rseventspro_events` ADD `repeat_on_day` TINYINT( 2 ) NOT NULL AFTER `repeat_on_type`");
				$db->execute();
			}
			
			$db->setQuery("SHOW COLUMNS FROM `#__rseventspro_events` WHERE `Field` = 'repeat_on_day_order'");
			if (!$db->loadResult()) {
				$db->setQuery("ALTER TABLE `#__rseventspro_events` ADD `repeat_on_day_order` TINYINT( 1 ) NOT NULL AFTER `repeat_on_day`");
				$db->execute();
			}
			
			$db->setQuery("SHOW COLUMNS FROM `#__rseventspro_events` WHERE `Field` = 'repeat_on_day_type'");
			if (!$db->loadResult()) {
				$db->setQuery("ALTER TABLE `#__rseventspro_events` ADD `repeat_on_day_type` VARCHAR( 25 ) NOT NULL AFTER `repeat_on_day_order`");
				$db->execute();
			}
			
			$db->setQuery("SHOW COLUMNS FROM `#__rseventspro_users` WHERE `Field` = 'create_user'");
			if (!$db->loadResult()) {
				$db->setQuery("ALTER TABLE `#__rseventspro_users` ADD `create_user` TINYINT( 1 ) NOT NULL");
				$db->execute();
			}
			
			$db->setQuery("SHOW COLUMNS FROM `#__rseventspro_users` WHERE `Field` = 'confirmed'");
			if (!$db->loadResult()) {
				$db->setQuery("ALTER TABLE `#__rseventspro_users` ADD `confirmed` TINYINT( 1 ) NOT NULL");
				$db->execute();
			}
			
			// END VERSION 1.6.0 UPDATE
			
			// Version 1.7.1
			$db->setQuery("SHOW COLUMNS FROM `#__rseventspro_events` WHERE `Field` = 'timezone'");
			if (!$db->loadResult()) {
				$db->setQuery("ALTER TABLE `#__rseventspro_events` ADD `timezone` VARCHAR( 255 ) NOT NULL");
				$db->execute();
			}
			
			// Version 1.8.0
			$db->setQuery("SHOW COLUMNS FROM `#__rseventspro_events` WHERE `Field` = 'aspectratio'");
			if (!$db->loadResult()) {
				$db->setQuery("ALTER TABLE `#__rseventspro_events` ADD `aspectratio` TINYINT( 1 ) NOT NULL");
				$db->execute();
				$db->setQuery("UPDATE `#__rseventspro_events` SET `aspectratio` = 1");
				$db->execute();
			}
			
			$db->setQuery("SHOW COLUMNS FROM `#__rseventspro_events` WHERE `Field` = 'exclude_dates'");
			if (!$db->loadResult()) {
				$db->setQuery("ALTER TABLE `#__rseventspro_events` ADD `exclude_dates` TEXT NOT NULL AFTER `repeat_on_day_type`");
				$db->execute();
			}
			
			$db->setQuery("SHOW COLUMNS FROM `#__rseventspro_tickets` WHERE `Field` = 'price'");
			if ($columns = $db->loadObject()) {
				if ($columns->Type == 'float') {
					$db->setQuery("ALTER TABLE `#__rseventspro_tickets` CHANGE `price` `price` DECIMAL( 20, 3 ) NOT NULL");
					$db->execute();
				}
			}
			
			$db->setQuery("SHOW COLUMNS FROM `#__rseventspro_groups` WHERE `Field` = 'restricted_categories'");
			if (!$db->loadResult()) {
				$db->setQuery("ALTER TABLE `#__rseventspro_groups` ADD `restricted_categories` TEXT NOT NULL");
				$db->execute();
			}
			
			$db->setQuery("SELECT `ide` FROM `#__rseventspro_taxonomy` WHERE `type` = 'postreminder'");
			if ($preminders = $db->loadColumn()) {
				foreach ($preminders as $preminder) {
					$db->setQuery("SELECT DISTINCT `email` FROM `#__rseventspro_users` WHERE `ide` = ".(int) $preminder." AND `state` = 1");
					if ($emails = $db->loadColumn()) {
						foreach ($emails as $email) {
							$db->setQuery("INSERT IGNORE INTO `#__rseventspro_taxonomy` SET `type` = 'preminder', `ide` = ".(int) $preminder.", `id` = '1', `extra` = ".$db->q($email)." ");
							$db->execute();
						}
					}
				}
				
				$db->setQuery("DELETE FROM `#__rseventspro_taxonomy` WHERE `type` = 'postreminder'");
				$db->execute();
			}
			
			// Version 1.9.0
			$db->setQuery("SHOW COLUMNS FROM `#__rseventspro_events` WHERE `Field` = 'itemid'");
			if (!$db->loadResult()) {
				$db->setQuery("ALTER TABLE `#__rseventspro_events` ADD `itemid` INT( 11 ) NOT NULL");
				$db->execute();
			}
			
			$db->setQuery("SHOW COLUMNS FROM `#__rseventspro_tickets` WHERE `Field` = 'order'");
			if (!$db->loadResult()) {
				$db->setQuery("ALTER TABLE `#__rseventspro_tickets` ADD `order` INT( 11 ) NOT NULL");
				$db->execute();
			}
			
			$db->setQuery("SHOW COLUMNS FROM `#__rseventspro_groups` WHERE `Field` = 'can_change_options'");
			if (!$db->loadResult()) {
				$db->setQuery("ALTER TABLE `#__rseventspro_groups` ADD `can_change_options` TINYINT( 1 ) NOT NULL AFTER `can_approve_tags`");
				$db->execute();
			}
			
			// Version 1.10.0
			$db->setQuery("SHOW COLUMNS FROM `#__rseventspro_tickets` WHERE `Field` = 'attach'");
			if (!$db->loadResult()) {
				$db->setQuery("ALTER TABLE `#__rseventspro_tickets` ADD `attach` TINYINT( 1 ) NOT NULL AFTER `groups`");
				$db->execute();
			}
			
			$db->setQuery("SHOW COLUMNS FROM `#__rseventspro_tickets` WHERE `Field` = 'layout'");
			if (!$db->loadResult()) {
				$db->setQuery("ALTER TABLE `#__rseventspro_tickets` ADD `layout` LONGTEXT NOT NULL AFTER `attach`");
				$db->execute();
			}
			
			$db->setQuery("SHOW COLUMNS FROM `#__rseventspro_events` WHERE `Field` = 'small_description'");
			if (!$db->loadResult()) {
				$db->setQuery("ALTER TABLE `#__rseventspro_events` ADD `small_description` TEXT NOT NULL AFTER `description`");
				$db->execute();
			}
			
			// Index
			$db->setQuery("SHOW INDEX FROM #__rseventspro_events WHERE Key_name = 'location'");
			if (!$db->loadObject()) {
				$db->setQuery("ALTER TABLE `#__rseventspro_events` ADD INDEX (`location`)");
				$db->execute();
			}
			
			$db->setQuery("SHOW INDEX FROM #__rseventspro_events WHERE Key_name = 'owner'");
			if (!$db->loadObject()) {
				$db->setQuery("ALTER TABLE `#__rseventspro_events` ADD INDEX (`owner`)");
				$db->execute();
			}
			
			$db->setQuery("SHOW INDEX FROM #__rseventspro_events WHERE Key_name = 'completed'");
			if (!$db->loadObject()) {
				$db->setQuery("ALTER TABLE `#__rseventspro_events` ADD INDEX (`completed`)");
				$db->execute();
			}
			
			$db->setQuery("SHOW INDEX FROM #__rseventspro_events WHERE Key_name = 'published'");
			if (!$db->loadObject()) {
				$db->setQuery("ALTER TABLE `#__rseventspro_events` ADD INDEX (`published`)");
				$db->execute();
			}
			
			$db->setQuery("SHOW INDEX FROM #__rseventspro_events WHERE Key_name = 'published_2'");
			if (!$db->loadObject()) {
				$db->setQuery("ALTER TABLE `#__rseventspro_events` ADD INDEX (`published`, `completed`)");
				$db->execute();
			}
			
			$db->setQuery("SHOW INDEX FROM #__rseventspro_tickets WHERE Key_name = 'ide'");
			if (!$db->loadObject()) {
				$db->setQuery("ALTER TABLE `#__rseventspro_tickets` ADD INDEX (`ide`)");
				$db->execute();
			}
			
			$db->setQuery("SHOW INDEX FROM #__rseventspro_tickets WHERE Key_name = 'price'");
			if (!$db->loadObject()) {
				$db->setQuery("ALTER TABLE `#__rseventspro_tickets` ADD INDEX (`price`)");
				$db->execute();
			}
			
			// Set event ticket layout to individual tickets
			$db->setQuery("SHOW COLUMNS FROM `#__rseventspro_events` WHERE `Field` = 'ticket_pdf'");
			if ($db->loadResult()) {
				$db->setQuery("SELECT `id`, `ticket_pdf`, `ticket_pdf_layout` FROM `#__rseventspro_events`");
				if ($events = $db->loadObjectList()) {
					foreach ($events as $event) {
						if (empty($event->ticket_pdf) && empty($event->ticket_pdf_layout)) continue;
						
						$db->setQuery('UPDATE `#__rseventspro_tickets` SET `attach` = '.$db->q($event->ticket_pdf).', `layout` = '.$db->q($event->ticket_pdf_layout).' WHERE `ide` = '.$db->q($event->id).' ');
						$db->execute();
					}
				}
				
				// Remove table fields
				$db->setQuery("ALTER TABLE `#__rseventspro_events` DROP `ticket_pdf`");
				$db->execute();
				$db->setQuery("ALTER TABLE `#__rseventspro_events` DROP `ticket_pdf_layout`");
				$db->execute();
			}
			
			$db->setQuery("SHOW COLUMNS FROM `#__rseventspro_locations` WHERE `Field` = 'marker'");
			if (!$db->loadResult()) {
				$db->setQuery("ALTER TABLE `#__rseventspro_locations` ADD `marker` VARCHAR( 255 ) NOT NULL AFTER `coordinates`");
				$db->execute();
			}
			
			// Set default values on database fields
			if ($tables = $db->getTableList()) {
				foreach ($tables as $table) {
					if (strpos($table, $db->getPrefix().'rseventspro') !== false) {
						if ($fields = $db->getTableColumns($table, false)) {
							foreach ($fields as $field) {
								$fieldType = strtolower($field->Type);
								$fieldKey = strtolower($field->Key);
								
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
			
			$db->setQuery("SHOW COLUMNS FROM `#__rseventspro_users` WHERE `Field` = 'hash'");
			if (!$db->loadResult()) {
				$db->setQuery("ALTER TABLE `#__rseventspro_users` ADD `hash` VARCHAR( 255 ) NOT NULL DEFAULT '' AFTER `create_user`");
				$db->execute();
			}
			
			$db->setQuery("SHOW COLUMNS FROM `#__rseventspro_tickets` WHERE `Field` = 'from'");
			if (!$db->loadResult()) {
				$db->setQuery("ALTER TABLE `#__rseventspro_tickets` ADD `from` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' AFTER `layout`");
				$db->execute();
			}
			
			$db->setQuery("SHOW COLUMNS FROM `#__rseventspro_tickets` WHERE `Field` = 'to'");
			if (!$db->loadResult()) {
				$db->setQuery("ALTER TABLE `#__rseventspro_tickets` ADD `to` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' AFTER `from`");
				$db->execute();
			}
			
			try {			
				$db->setQuery("SHOW COLUMNS FROM `#__rseventspro_discounts` WHERE `Field` = 'cart_tickets'");
				if (!$db->loadResult()) {
					$db->setQuery("ALTER TABLE `#__rseventspro_discounts` ADD `cart_tickets` INT(3) NOT NULL DEFAULT '0' AFTER `different_tickets`");
					$db->execute();
				}
			} catch (Exception $e) {}
			
			// Set enable option to the notification emails
			$token = false;
			$db->setQuery("SELECT `value` FROM `#__rseventspro_config` WHERE name = 'facebook_appid'");
			if ($fbappid = $db->loadResult()) {
				if ($fbappid == '340486642645761') {
					$db->setQuery("UPDATE `#__rseventspro_config` SET `value` = '' WHERE name = 'facebook_appid'");
					$db->execute();
					$token = true;
				}
			}
			
			$db->setQuery("SELECT `value` FROM `#__rseventspro_config` WHERE name = 'facebook_secret'");
			if ($fbsecret = $db->loadResult()) {
				if ($fbsecret == 'fea413f9a085e01555de0e93848c2c4a') {
					$db->setQuery("UPDATE `#__rseventspro_config` SET `value` = '' WHERE name = 'facebook_secret'");
					$db->execute();
					$token = true;
				}
			}
			
			if ($token) {
				$db->setQuery("UPDATE `#__rseventspro_config` SET `value` = '' WHERE name = 'facebook_token'");
				$db->execute();
			}
			
			$db->setQuery("SHOW TABLES LIKE '".$db->getPrefix()."rseventspro_sync_log'");
			if ($db->loadResult()) {
				$db->setQuery("SHOW COLUMNS FROM `#__rseventspro_sync_log` WHERE `Field` = 'page'");
				if ($columns = $db->loadObject()) {
					if (strtolower($columns->Type) == 'int(2)') {
						$db->setQuery("ALTER TABLE `#__rseventspro_sync_log` CHANGE `page` `page` VARCHAR(255) NOT NULL DEFAULT ''");
						$db->execute();
					}
				}
			}
			
			
			$updateData = array();
			$updateData[] = array('table' => '#__rseventspro_events', 'field' => 'rsvp', 'type' => 'TINYINT(2)', 'default' => '0');
			$updateData[] = array('table' => '#__rseventspro_events', 'field' => 'rsvp_quota', 'type' => 'INT(11)', 'default' => '0');
			$updateData[] = array('table' => '#__rseventspro_events', 'field' => 'rsvp_guests', 'type' => 'TINYINT(2)', 'default' => '0');
			$updateData[] = array('table' => '#__rseventspro_events', 'field' => 'rsvp_start', 'type' => 'DATETIME', 'default' => '0000-00-00 00:00:00');
			$updateData[] = array('table' => '#__rseventspro_events', 'field' => 'rsvp_end', 'type' => 'DATETIME', 'default' => '0000-00-00 00:00:00');
			$updateData[] = array('table' => '#__rseventspro_events', 'field' => 'rsvp_going', 'type' => 'TINYINT(2)', 'default' => '0');
			$updateData[] = array('table' => '#__rseventspro_events', 'field' => 'rsvp_interested', 'type' => 'TINYINT(2)', 'default' => '0');
			$updateData[] = array('table' => '#__rseventspro_events', 'field' => 'rsvp_notgoing', 'type' => 'TINYINT(2)', 'default' => '0');
			$updateData[] = array('table' => '#__rseventspro_events', 'field' => 'event_ended', 'type' => 'TEXT');
			$updateData[] = array('table' => '#__rseventspro_events', 'field' => 'event_full', 'type' => 'TEXT');
			$updateData[] = array('table' => '#__rseventspro_groups', 'field' => 'can_select_speakers', 'type' => 'TINYINT(2)', 'default' => '1');
			$updateData[] = array('table' => '#__rseventspro_groups', 'field' => 'can_add_speaker', 'type' => 'TINYINT(2)', 'default' => '0');
			
			foreach ($updateData as $data) {
				$checkQuery = 'SHOW COLUMNS FROM '.$db->qn($data['table']).' WHERE '.$db->qn('Field').' = '.$db->q($data['field']);
				$updateQuery = 'ALTER TABLE '.$db->qn($data['table']).' ADD '.$db->qn($data['field']).' '.$data['type'].' NOT NULL';
				
				if (isset($data['default'])) $updateQuery .= " DEFAULT '".$data['default']."'";
				if (isset($data['after'])) $updateQuery .= ' AFTER '.$db->q($data['after']);
				
				$db->setQuery($checkQuery);
				if (!$db->loadResult()) {
					$db->setQuery($updateQuery);
					$db->execute();
				}
			}
			
			// Run queries
			$sqlfile = JPATH_ADMINISTRATOR.'/components/com_rseventspro/install.mysql.sql';
			$buffer = file_get_contents($sqlfile);
			if ($buffer === false) {
				throw new Exception(JText::_('JLIB_INSTALLER_ERROR_SQL_READBUFFER'), 1);
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
						throw new Exception(JText::sprintf('JLIB_INSTALLER_ERROR_SQL_ERROR', $db->stderr(true)), 1);
					}
				}
			}
			
			// Add old rating to the new rating table
			$db->setQuery("SELECT * FROM `#__rseventspro_taxonomy` WHERE `type` = 'rating'");
			if ($ratings = $db->loadObjectList()) {
				foreach ($ratings as $rating) {
					$db->setQuery('INSERT INTO `#__rseventspro_rating` SET `ide` = '.$db->q($rating->ide).', `value` = '.$db->q($rating->id).', `ip` = '.$db->q($rating->extra));
					$db->execute();
				}
				
				$db->setQuery("DELETE FROM `#__rseventspro_taxonomy` WHERE `type` = 'rating'");
				$db->execute();
			}
		}
		
		$jversion = new JVersion();
		if ($jversion->isCompatible('3.0')) {
			if ($content = JTable::getInstance('Contenttype', 'JTable')) {
				if (!$content->load(array('type_alias' => 'com_rseventspro.categories'))) {
					$content->save(array(
						'type_title' => 'RSEvents! Pro Category',
						'type_alias' => 'com_rseventspro.category',
						'table'		 => '{"special":{"dbtable":"#__categories","key":"id","type":"Category","prefix":"JTable","config":"array()"},"common":{"dbtable":"#__ucm_content","key":"ucm_id","type":"Corecontent","prefix":"JTable","config":"array()"}}',
						'field_mappings' => '{"common":{"core_content_item_id":"id","core_title":"title","core_state":"published","core_alias":"alias","core_created_time":"created_time","core_modified_time":"modified_time","core_body":"description", "core_hits":"hits","core_publish_up":"null","core_publish_down":"null","core_access":"access", "core_params":"params", "core_featured":"null", "core_metadata":"metadata", "core_language":"language", "core_images":"null", "core_urls":"null", "core_version":"version", "core_ordering":"null", "core_metakey":"metakey", "core_metadesc":"metadesc", "core_catid":"parent_id", "core_xreference":"null", "asset_id":"asset_id"}, "special":{"parent_id":"parent_id","lft":"lft","rgt":"rgt","level":"level","path":"path","extension":"extension","note":"note"}}',
					));
				}
			}
		}
		
		// Unpublish the RSMediaGallery! plugin
		$db->setQuery("SELECT `extension_id`, `name` FROM `#__extensions` WHERE `type` = 'plugin' AND `element` = 'rsmediagallery' AND `folder` = 'rseventspro'");
		if ($gallery = $db->loadObject()) {
			$db->setQuery("UPDATE `#__extensions` SET `enabled` = 0 , `name` = '".$db->escape($gallery->name.' (Plugin no longer available!) ')."' WHERE `extension_id` = ".(int) $gallery->extension_id." ");
			$db->execute();
		}
	}
	
	// Set the install message
	public function showinstall($messages) {
?>
<style type="text/css">
#rsepro-installer-left {
	float: left;
	width: 18%;
	padding: 5px;
}

#rsepro-installer-right {
	float: left;
	padding: 5px;
	width: 70%;
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

.com-rseventspro-button {
	display: inline-block;
	background: #459300 none repeat scroll 0 0;
	color: #fff !important;
	cursor: pointer;
	margin-bottom: 10px;
    padding: 7px;
	text-decoration: none !important;
}

.rsepro-messages {
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

.rsepro-messages > p {
    margin: 0 0 5px !important;
}
</style>

<div id="rsepro-installer-left">
	<img src="<?php echo JUri::root(); ?>media/com_rseventspro/images/rseventspro-logo.png" alt="RSEvents!Pro" />
</div>
<div id="rsepro-installer-right">
	<?php if ($messages) { ?>
	<div class="rsepro-messages">
		<?php foreach ($messages as $message) { ?>
			<p><i class="icon-info"></i> <?php echo $message; ?></p>
		<?php } ?>
	</div>
	<?php } ?>
	<h2>Changelog v1.12.2</h2>
	<ul class="version-history">
		<li><span class="version-upgraded">Upg</span> Calendar layout speed improvements.</li>
	</ul>
	<a class="com-rseventspro-button" href="index.php?option=com_rseventspro">Go to RSEvents!Pro</a>
	<a class="com-rseventspro-button" href="https://www.rsjoomla.com/support/documentation/rseventspro.html" target="_blank">Read the Documentation</a>
	<a class="com-rseventspro-button" href="https://www.rsjoomla.com/customer-support/tickets.html" target="_blank">Get Support!</a>
</div>
<div style="clear: both;"></div>
<?php
	}
	
	protected function renderTree($array, &$tree=array(), &$levels=array(), $parent=0, $level=0) {
		foreach ($array as $row) {
			if ($row->parent == $parent) {
				$levels[$row->id] 	= $level;
				$tree[$row->id]		= array();
				$this->renderTree($array, $tree[$row->id], $levels, $row->id, $level+1);
			}
		}
	}
	
	protected function renderFlatTree($tree) {
		$list = array();
		foreach($tree as $key => $children) {
			$list[] = $key;
			if (count($children)) {
				$tmp_list = $this->renderFlatTree($children);
				foreach ($tmp_list as $tmp_key)
					$list[] = $tmp_key;
			}
		}

		return $list;
	}
	
	protected function isJSON($string) {
		$data 	= json_decode($string);
		
		if (version_compare(PHP_VERSION,'5.3.0','>='))
			$valid	= json_last_error() == JSON_ERROR_NONE;
		else $valid = !is_null($data);
		
		if ($valid) {
			return is_array($data) || is_object($data);
		} else return $valid;
	}
	
	protected function checkAddons() {
		$messages	= array();
		$lang		= JFactory::getLanguage();
		
		$plugins = array(
			'content' => array(
				'rseventspro' => '1.1'
			),
			'system' => array(
				'rsepropdf' => '1.13',
				'rsfprseventspro' => '1.5.0',
				'rsepro2co' => '1.1',
				'rseproanzegate' => '1.2',
				'rseproauthorize' => '1.2',
				'rseproeway' => '1.5',
				'rseproideal' => '1.5',
				'rsepromygate' => '1.1',
				'rsepropaypal' => '1.2',
				'rseprovmerchant' => '1.2',
				'rseprostripe' => '1.1',
				'rseprooffline' => '1.3'
			)
		);
		
		// Check plugins version
		if ($installed = $this->getPlugins($plugins)) {
			foreach ($installed as $plugin) {
				$file = JPATH_SITE.'/plugins/'.$plugin->folder.'/'.$plugin->element.'/'.$plugin->element.'.xml';
				if (file_exists($file)) {
					$xml		= file_get_contents($file);
					$version	= $plugins[$plugin->folder][$plugin->element];
					
					if ($this->checkVersion($xml, $version, '>') || strpos($xml, '<extension') === false) {
						$lang->load($plugin->element, JPATH_ADMINISTRATOR);
						$this->disableExtension($plugin->extension_id);
						$messages[] = 'Please update the plugin "'.JText::_($plugin->name).'" manually.';
					}
				}
			}
		}
		
		$modules = array(
			'mod_rseventspro_archived' => '1.3',
			'mod_rseventspro_attendees' => '1.3',
			'mod_rseventspro_calendar' => '1.7',
			'mod_rseventspro_categories' => '1.3',
			'mod_rseventspro_events' => '1.4',
			'mod_rseventspro_featured' => '1.3',
			'mod_rseventspro_location' => '1.2',
			'mod_rseventspro_locations' => '1.3',
			'mod_rseventspro_map' => '1.6',
			'mod_rseventspro_popular' => '1.3',
			'mod_rseventspro_search' => '1.5',
			'mod_rseventspro_slider' => '1.7',
			'mod_rseventspro_tags' => '1.1',
			'mod_rseventspro_upcoming' => '1.2'
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
		$folders	= array_keys($plugins);
		$elements	= array();
		
		foreach ($folders as $folder) {
			$elements = array_merge($elements , array_keys($plugins[$folder]));
		}
		
		$query = $db->getQuery(true)->select('*')
			->from('#__extensions')
			->where($db->qn('type').'='.$db->q('plugin'))
			->where($db->qn('folder').' IN ('.$this->quoteImplode($folders).')')
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
}