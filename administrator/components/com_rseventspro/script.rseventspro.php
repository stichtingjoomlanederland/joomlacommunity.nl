<?php
/**
* @version 1.0.0
* @package RSEvents!Pro 1.0.0
* @copyright (C) 2012 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined( '_JEXEC' ) or die( 'Restricted access' );

class com_rseventsproInstallerScript 
{
	public function install($parent) {}
	
	public function preflight($type, $parent) {
		$app		= JFactory::getApplication();
		$jversion	= new JVersion();
		
		if (!$jversion->isCompatible('2.5.5')) {
			$app->enqueueMessage('Please upgrade to at least Joomla! 2.5.5 before continuing!', 'error');
			return false;
		}
		
		return true;
	}

	public function postflight($type, $parent) {
		$this->installprocess($type);
		
		$messages = array(
			'plugins' 	=> array(),
			'modules' 	=> array(),
			'messages' 	=> array()
		);
		
		$this->checkPlugins($messages, $parent);
		$this->showinstall($messages);
	}
	
	public function uninstall($parent) {}
	
	// Install - Update process
	public function installprocess($type) {
		$db		= JFactory::getDbo();
		
		if ($type == 'update') {
			
			// REV 5 
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
							$table->alias = JApplication::stringURLSafe($category->name);
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
			$db->setQuery("SELECT `id`, `repeat_also`, `payments`, `options`, `gallery_tags` FROM `#__rseventspro_events`");
			if ($events = $db->loadObjectList()) {
				foreach ($events as $event) {
					$repeat_also = $payments = $options = $gallery_tags = '';
					
					if (!empty($event->repeat_also)) {
						if (!$this->isJSON($event->repeat_also)) {
							$repeat_also = unserialize($event->repeat_also);
							if ($repeat_also !== false) {
								$registry = new JRegistry;
								$registry->loadArray($repeat_also);
								$repeat_also = $registry->toString();
							}
						}
					}
					
					if (!empty($event->options)) {
						if (!$this->isJSON($event->options)) {
							$options = unserialize($event->options);
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
			
			
			// Run queries
			$sqlfile = JPATH_ADMINISTRATOR.'/components/com_rseventspro/install.mysql.utf8.sql';
			$buffer = file_get_contents($sqlfile);
			if ($buffer === false) {
				JError::raiseWarning(1, JText::_('JLIB_INSTALLER_ERROR_SQL_READBUFFER'));
				return false;
			}
			
			jimport('joomla.installer.helper');
			$queries = JInstallerHelper::splitSql($buffer);
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
						JError::raiseWarning(1, JText::sprintf('JLIB_INSTALLER_ERROR_SQL_ERROR', $db->stderr(true)));
						return false;
					}
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
.version-history {
	margin: 0 0 2em 0;
	padding: 0;
	list-style-type: none;
}
.version-history > li {
	margin: 0 0 0.5em 0;
	padding: 0 0 0 4em;
}
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

.version-new {
	background: #7dc35b;
}
.version-fixed {
	background: #e9a130;
}
.version-upgraded {
	background: #61b3de;
}

.install-ok {
	background: #7dc35b;
	color: #fff;
	padding: 3px;
}

.install-not-ok {
	background: #E9452F;
	color: #fff;
	padding: 3px;
}

#installer-left {
	float: left;
	width: 230px;
	padding: 5px;
}

#installer-right {
	float: left;
}

.com-rseventspro-button {
	display: inline-block;
	background: #459300 url(components/com_rseventspro/assets/images/bg-button-green.gif) top left repeat-x !important;
	border: 1px solid #459300 !important;
	padding: 2px;
	color: #fff !important;
	cursor: pointer;
	margin: 0;
	-webkit-border-radius: 5px;
    -moz-border-radius: 5px;
    border-radius: 5px;
	text-decoration: none !important;
}

.big-warning {
	background: #FAF0DB;
	border: solid 1px #EBC46F;
	padding: 5px;
}

.big-warning b {
	color: red;
}

.big-info {
	background: #D5FFDC;
	border: solid 1px #EBC46F;
	padding: 5px;
}

</style>
<div id="installer-left">
	<img src="components/com_rseventspro/assets/images/rseventspro-box.png" alt="RSEvents!Pro Box" />
</div>
<div id="installer-right">
	<?php if ($messages['messages']) { ?>
	<p class="big-info">
		<?php foreach ($messages['messages'] as $message) { ?>
			<?php echo $message; ?> <br />
		<?php } ?>
	</p>
	<?php } ?>
	<?php if ($messages['plugins']) { ?>
		<p class="big-warning"><b>Warning!</b> The following plugins have been temporarily disabled to prevent any errors being shown on your website. Please <a href="http://www.rsjoomla.com/downloads.html" target="_blank">download the latest versions</a> from your account and update your installation before enabling them.</p>
		<?php foreach ($messages['plugins'] as $plugin) { ?>
		<p><?php echo $this->escape($plugin->name); ?> ...
			<b class="install-<?php echo $plugin->status; ?>"><?php echo $plugin->text; ?></b>
		</p>
		<?php } ?>
	<?php } ?>
	<?php if ($messages['modules']) { ?>
		<p class="big-warning"><b>Warning!</b> The following modules have been temporarily disabled to prevent any errors being shown on your website. Please <a href="http://www.rsjoomla.com/downloads.html" target="_blank">download the latest versions</a> from your account and update your installation before enabling them.</p>
		<?php foreach ($messages['modules'] as $module) { ?>
		<p><?php echo $this->escape($module->name); ?> ...
			<b class="install-<?php echo $module->status; ?>"><?php echo $module->text; ?></b>
		</p>
		<?php } ?>
	<?php } ?>
	<ul class="version-history">
		<li><span class="version-upgraded">Upg</span> Calendar legend (You can choose the type of the calendar legend: table legend or dropdown).</li>
		<li><span class="version-new">New</span> Featured events.</li>
		<li><span class="version-new">New</span> Create custom seat charts for your events.</li>
		<li><span class="version-new">New</span> Restrict tickets based on the RSEvents!Pro groups.</li>
		<li><span class="version-new">New</span> Event reporting tool.</li>
		<li><span class="version-new">New</span> AJAX autocomplete when adding tags to an event.</li>
		<li><span class="version-new">New</span> Content plugin. (Add events to your Joomla! Articles)</li>
		<li><span class="version-new">New</span> Archived module.</li>
		<li><span class="version-new">New</span> Featured module.</li>
	</ul>
	<a class="com-rseventspro-button" href="index.php?option=com_rseventspro">Start using RSEvents!Pro</a>
	<a class="com-rseventspro-button" href="http://www.rsjoomla.com/support/documentation/view-knowledgebase/170-rseventspro.html" target="_blank">Read the RSEvents!Pro User Guide</a>
	<a class="com-rseventspro-button" href="http://www.rsjoomla.com/customer-support/tickets.html" target="_blank">Get Support!</a>
</div>
<div style="clear: both;"></div>
<?php
	}
	
	// Set the uninstall message
	public function showUninstall() {
		echo 'RSEvents!Pro component has been successfully uninstaled!';
	}
	
	protected function renderTree($array, &$tree=array(), &$levels=array(), $parent=0, $level=0) {
		foreach ($array as $row) {
			if ($row->parent == $parent) {
				$levels[$row->id] 	= $level;
				$tree[$row->id] 		= array();
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
	
	protected function checkPlugins(&$messages, $parent = null) {
		$lang = JFactory::getLanguage();
		$plugins = array(
			'rsepropdf',
			'rsfprseventspro',
			'rsepro2co',
			'rseproauthorize',
			'rseprooffline',
			'rsepropaypal',
			'rseventspro'
		);
		
		if ($installed = $this->getPlugins($plugins)) {
			// need to update old plugins
			foreach ($installed as $plugin) {
				$file = JPATH_SITE.'/plugins/'.$plugin->folder.'/'.$plugin->element.'/'.$plugin->element.'.xml';
				if (file_exists($file)) {
					$xml = file_get_contents($file);
					
					// Check for old 1.5 plugins
					if (strpos($xml, '<extension') === false) {
						$this->disableExtension($plugin->extension_id);
						
						$status = 'warning';
						$text	= 'Disabled';
						
						$messages['plugins'][] = (object) array(
							'name' 		=> $plugin->name,
							'status' 	=> $status,
							'text'		=> $text
						);
					} else {
						if ($plugin->element == 'rsfprseventspro') {
							$this->installPlugin($parent, 'rsform');
							$messages['messages'][] = 'The plugin "'.$plugin->name.'"  has been successfully updated.';
						}
						
						if ($plugin->element == 'rsepropdf') {
							$messages['messages'][] = 'Please update the plugin "'.$plugin->name.'" manually.';
						}
					}
				}
			}
		}
		
		$modules = array(
			'mod_rseventspro_attendees',
			'mod_rseventspro_calendar',
			'mod_rseventspro_categories',
			'mod_rseventspro_location',
			'mod_rseventspro_locations',
			'mod_rseventspro_map',
			'mod_rseventspro_search',
			'mod_rseventspro_slider',
			'mod_rseventspro_upcoming'
		);
		
		if ($installed = $this->getModules($modules)) {
			foreach ($installed as $module) {
				$file = JPATH_SITE.'/modules/'.$module->element.'/'.$module->element.'.xml';
				if (file_exists($file)) {
					$xml = file_get_contents($file);
					
					if (strpos($xml, '<install') !== false) {
						$this->disableExtension($module->extension_id);
						
						$messages['modules'][] = (object) array(
							'name' 		=> $module->name,
							'status' 	=> 'warning',
							'text'		=> 'Disabled'
						);
					} else {
						if ($module->element == 'mod_rseventspro_calendar') {
							$lang->load('mod_rseventspro_calendar.sys',JPATH_SITE);
							$this->installModule($parent, 'mod_rseventspro_calendar');
							$messages['messages'][] = 'The module "'.JText::_($module->name).'"  has been successfully updated.';
						}
					}
				}
			}
		}
	}
	
	protected function disableExtension($extension_id) {
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->update('#__extensions')
			  ->set($db->quoteName('enabled').'='.$db->quote(0))
			  ->where($db->quoteName('extension_id').'='.$db->quote($extension_id));
		$db->setQuery($query);
		$db->execute();
	}
	
	protected function getModules($element) {
		$db 	= JFactory::getDbo();
		$query 	= $db->getQuery(true);
		$one	= false;
		if (!is_array($element)) {
			$element = array($element);
			$one = true;
		}
		
		$query->select('*')
			  ->from('#__extensions')
			  ->where($db->quoteName('type').'='.$db->quote('module'))
			  ->where($db->quoteName('element').' IN ('.$this->quoteImplode($element).')');
		$db->setQuery($query);
		
		return $one ? $db->loadObject() : $db->loadObjectList();
	}
	
	protected function getPlugins($element) {
		$db 	= JFactory::getDbo();
		$query 	= $db->getQuery(true);
		$one	= false;
		if (!is_array($element)) {
			$element = array($element);
			$one = true;
		}
		
		$query->select('*')
			  ->from('#__extensions')
			  ->where($db->quoteName('type').'='.$db->quote('plugin'))
			  ->where($db->quoteName('folder').' IN ('.$this->quoteImplode(array('search', 'system')).')')
			  ->where($db->quoteName('element').' IN ('.$this->quoteImplode($element).')');
		$db->setQuery($query);
		
		return $one ? $db->loadObject() : $db->loadObjectList();
	}
	
	protected function quoteImplode($array) {
		$db = JFactory::getDbo();
		foreach ($array as $k => $v) {
			$array[$k] = $db->quote($v);
		}
		
		return implode(',', $array);
	}
	
	protected function escape($string) {
		return htmlentities($string, ENT_COMPAT, 'utf-8');
	}
	
	protected function installPlugin($parent, $plugin) {
		// Get a new installer
		$installer = new JInstaller();
		
		// Install the module
		$installer->install($parent->getParent()->getPath('source').'/other/plugins/'.$plugin);
	}
	
	protected function installModule($parent, $module) {
		// Get a new installer
		$installer = new JInstaller();
		
		// Install the module
		$installer->install($parent->getParent()->getPath('source').'/other/modules/'.$module);
	}
}