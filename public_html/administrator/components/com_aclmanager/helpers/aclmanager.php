<?php
/**
 * @package		ACL Manager for Joomla
 * @copyright 	Copyright (c) 2011-2016 Sander Potjer
 * @license 	GNU General Public License version 3 or later
 */

// No direct access.
defined('_JEXEC') or die;

/**
 * ACL Manager component helper.
 */
class AclmanagerHelper
{
	/**
	 * Configure the Linkbar.
	 */
	public static function addSubmenu($vName)
	{
		// Control Panel
		JSubMenuHelper::addEntry(
			JText::_('COM_CPANEL'),
			'index.php?option=com_aclmanager&view=home',
			$vName == 'home'
		);

		// Diagnostic
		if(JFactory::getUser()->authorise('aclmanager.diagnostic', 'com_aclmanager')){
			JSubMenuHelper::addEntry(
				JText::_('COM_ACLMANAGER_SUBMENU_DIAGNOSTIC'),
				'index.php?option=com_aclmanager&view=diagnostic',
				$vName == 'diagnostic'
			);
		}

		// Update
		if(JFactory::getUser()->authorise('core.admin', 'com_aclmanager')){
			JSubMenuHelper::addEntry(
				JText::_('COM_ACLMANAGER_SUBMENU_UPDATE'),
				'index.php?option=com_aclmanager&view=liveupdate',
				$vName == 'liveupdate'
			);
		}
	}

	/**
	 * Get a list of the components with categories for filtering.
	 */
	static function getComponentList()
	{
		// Initialise variable
		$db 	= JFactory::getDbo();
		$query	= $db->getQuery(true);

		// Get components with permissions
		$query->select('name AS value, name AS text, level AS level, rules AS rules')
			->from('#__assets')
			->where('level = 1')
			->where('rules !='.$db->Quote('{}'));

		$options = $db->setQuery($query)->loadObjectList();

		if (count($options)) {
			foreach ($options as $key=>$option) {
				// Translate component name
				$option->text = JText::_($option->text);
			}

			// Sort by component name
			$lang = JFactory::getLanguage();
			JArrayHelper::sortObjects($options, 'text', 1, true, $lang->getLocale());
		}

		return $options;
	}

	/**
	 * Get a yes/no list for filtering.
	 */
	static function getYesNoList()
	{
		$options = array(
      		JHTML::_('select.option', '1', JText::_('JYES') ),
      		JHTML::_('select.option', '0', JText::_('JNO') )
    	);

		return $options;
	}

	/**
	 * Load system language files from installed extensions.
	 */
	static function getLanguages()
	{
		// Initialise variable
		$lang 	= JFactory::getLanguage();
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);

		// Load frontend default language file
		$lang->load('', JPATH_SITE, null, false, false)
		||	$lang->load('', JPATH_SITE, $lang->getDefault(), false, false);

		// Get all active extensions
		$query->select('element AS value')
			->from('#__extensions')
			->where('enabled >= 1')
			->where('type ='.$db->Quote('component'));

		$languages = $db->setQuery($query)->loadObjectList();

		if (count($languages)) {

			foreach ($languages as &$language)
			{
				// Load system language files for all extensions
				$extension 	= $language->value;
				$source 	= JPATH_ADMINISTRATOR . '/components/' . $extension;
				$lang->load("$extension.sys", JPATH_ADMINISTRATOR, $lang->getDefault(), false, false)
				||	$lang->load("$extension.sys", $source, $lang->getDefault(), false, false)
				||  $lang->load("$extension.sys", JPATH_ADMINISTRATOR, null, false, false)
				||	$lang->load("$extension.sys", $source, null, false, false);

				// Load com_config language file
				if ($language->value == 'com_config'){
					$lang->load($extension, JPATH_ADMINISTRATOR, $lang->getDefault(), false, false)
					||	$lang->load($extension, $source, $lang->getDefault(), false, false)
					||  $lang->load($extension, JPATH_ADMINISTRATOR, null, false, false)
					||	$lang->load($extension, $source, null, false, false);
				}
			}
		}

		return $languages;
	}

	/**
	 * Get name of user group.
	 */
	static function groupName($groupid)
	{
		// Initialise variable
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);

		// Get all active extensions
		$query->select('title')
			->from('#__usergroups')
			->where('id = '.$groupid);

		$groupname = $db->setQuery($query)->loadObjectList();
		$groupname = $groupname[0]->title;

		return $groupname;
	}


	/**
	 * Return display information of assetaction.
	 */
	static function assetAction($asset,$action,$group,$user)
	{
		$check 	= isset($asset->checks[$action]) ? $asset->checks[$action] : null; // Check calculated permission for asset
		$rule 	= isset($asset->rule[$action]) ? $asset->rule[$action] : null; // Check permission for asset
		$assetaction = new stdClass();
		$assetaction->class = '';
		$assetaction->icon = '';
		$assetaction->image = '';
		$assetaction->value = '';
		$assetaction->check = '';
		$assetaction->su = '';

		if($group==0) {
			$su = JAccess::check($user, 'core.admin', 1);

			if (($su) || ($check === true)) {
				// Inherited allowed
				$assetaction->icon = 'icon allow';
				$assetaction->image = 'tick';
				$assetaction->check = 1;
			} else {
				// Inherited denied
				$assetaction->icon = 'icon deny';
				$assetaction->image = 'publish_x';
				$assetaction->check = 0;
			}
		} else {
			$su = JAccess::checkGroup($group, 'core.admin', 1);

			if(($su) && ($asset->id == 1) && ($action == 'core.admin')) {
				// Allowed for asset
				$assetaction->class = 'allowed';
				$assetaction->icon = 'icon allow';
				$assetaction->image = 'tick';
				$assetaction->value = 1;
				$assetaction->check = 1;
			} elseif ($su) {
				$assetaction->icon = 'icon allowinactive';
				$assetaction->image = 'icon-16-allow';
				$assetaction->check = 1;
				$assetaction->su = 1;
			} elseif ($check === null) {
				// Not set
				$assetaction->icon = 'icon link notset';
				$assetaction->image = 'disabled';
				$assetaction->check = '';
			} elseif (($check === false) && ($rule === true)) {
				// Conflict (inherited deny, allowed on asset)
				$assetaction->class = 'conflict';
				$assetaction->icon = 'icon link conflict';
				$assetaction->image = 'publish_y';
				$assetaction->value = 1;
				$assetaction->check = 0;
			} elseif (($check === true) && ($rule === true)) {
				// Allowed for asset
				$assetaction->class = 'allowed';
				$assetaction->icon = 'icon link allow';
				$assetaction->image = 'tick';
				$assetaction->value = 1;
				$assetaction->check = 1;
			} elseif ($check === true) {
				// Inherited allowed
				$assetaction->icon = 'icon link allowinactive';
				$assetaction->image = 'icon-16-allow';
				$assetaction->check = 1;
			} elseif (($check === false) && ($rule === false)) {
				// Denied for asset
				$assetaction->class = 'denied';
				$assetaction->icon = 'icon link deny';
				$assetaction->image = 'publish_x';
				$assetaction->value = 0;
				$assetaction->check = 0;
			} elseif ($check === false) {
				// Inherited denied
				$assetaction->icon = 'icon locked';
				$assetaction->image = 'icon-16-locked';
				$assetaction->check = 0;
			}
		}

		return $assetaction;
	}

	/**
	 * Build asset links.
	 */
	static function asset($assets,$groupid)
	{
		// Initialise variable
		$app 					= JFactory::getApplication('administrator');
		$view					= $app->input->get('view','home');
		$layout					= $app->input->get('layout',null);
		$params 				= JComponentHelper::getParams('com_aclmanager');
		$acl_categorymanager 	= $params->get('acl_categorymanager',1);

		if($layout =='print') {
			$print = true;
		} else {
			$print = false;
		}

		// Get ids of trashed assets
		$trashed_categories = AclmanagerHelper::trashedCategories();
		$trashed_content = AclmanagerHelper::trashedContent();

		foreach ($assets as $i => $asset) {
			$asset->targetid 	= substr(strrchr($asset->name,'.'),1);
			preg_match('/\.(.*?)\./s', $asset->name, $types);
			if($asset->level == 1) {
				$asset->component = $asset->name;
			} else {
				$asset->component = substr($asset->name, 0, stripos($asset->name, ".") );
			}

			// Installation Manager -> Extension Manager correction
			if($asset->component == 'com_installer') {
				$asset->component = 'MOD_MENU_EXTENSIONS_EXTENSION_MANAGER';
			}

			// Check for trashed objects
			if (in_array($asset->id,$trashed_categories) || in_array($asset->id,$trashed_content)) {
				$asset->status = '<em>['.JText::_('JTRASHED').']</em>';
				$asset->class = 'trashed';
			} else {
				$asset->status = '';
				$asset->class = '';
			}

			// Check for trashed objects
			if(JFactory::getUser()->authorise('core.admin', $asset->component)) {
				$asset->config = true;
			} else {
				$asset->config = false;
			}

			// Check for ACL Support
			$accessfile = JPATH_ADMINISTRATOR.'/components/'.$asset->name.'/access.xml';
			$configfile = JPATH_ADMINISTRATOR.'/components/'.$asset->name.'/config.xml';

			$permissions = false;

			if((is_file($accessfile)) && (is_file($configfile))) {
				$permissions = true;
			} elseif(is_file($configfile)) {
				$xml 		= simplexml_load_file($configfile);
				foreach($xml->children()->fieldset as $fieldset)
				{
					if ('permissions' == (string) $fieldset['name']) {
						$permissions = true;
					}
				}
			}

			if ($asset->name == 'root.1'){
				// Global configuration
				if ((JFactory::getUser()->authorise('core.admin', 'com_config')) && (!$print)) {
					$asset->url =  '<a href="'.JRoute::_('index.php?option=com_config').'">'.JText::_('COM_CONFIG_GLOBAL_CONFIGURATION').'</a>';
				} else {
					$asset->url =  '<span class="title">'.JText::_('COM_CONFIG_GLOBAL_CONFIGURATION').'</span>';
				}
				$asset->type = 'config';
				$asset->icon = 'config';
			} elseif ($asset->level == 1) {
				// Component level
				if ((JFactory::getUser()->authorise('core.manage', $asset->component)) && ($permissions) && (!$print)) {
					$asset->url =  '<a href="'.JRoute::_('index.php?option='.$asset->name).'">'.JText::_($asset->component).'</a>';
				} elseif ((JFactory::getUser()->authorise('core.manage', $asset->component)) && (!$print)) {
					$asset->url =  '<span class="icon-16-aclmanager hasTip" title="'.JText::_('COM_ACLMANAGER_TABLE_ADDED_SUPPORT_TITLE').'::'.JText::_('COM_ACLMANAGER_TABLE_ADDED_SUPPORT_DESC').'"></span><a href="'.JRoute::_('index.php?option='.$asset->name).'">'.JText::_($asset->component).'</a>';
				} elseif (!$permissions) {
					$asset->url =  '<span class="icon-16-aclmanager hasTip" title="'.JText::_('COM_ACLMANAGER_TABLE_ADDED_SUPPORT_TITLE').'::'.JText::_('COM_ACLMANAGER_TABLE_ADDED_SUPPORT_DESC').'"></span><span class="title">'.JText::_($asset->title).'</span>';
				} else {
					$asset->url =  '<span class="title">'.JText::_($asset->title).'</span>';
				}
				$asset->type = 'component';
				$asset->icon = 'component';
			} elseif (stripos($asset->name, "category")) {
				// Category level
				if($acl_categorymanager) {
					if ((JFactory::getUser()->authorise('core.manage', $asset->component)) && (JFactory::getUser()->authorise('core.manage', 'com_categories')) && (!$print)) {
						$asset->url =  '<a href="'.JRoute::_('index.php?option=com_categories&task=category.edit&id='.$asset->targetid.'&extension='.$asset->component).'">'.$asset->title.' '.$asset->status.'</a>';
					} else {
						$asset->url =  '<span class="title">'.JText::_($asset->title).' '.$asset->status.'</span>';
					}
				} else {
					if ((JFactory::getUser()->authorise('core.manage', $asset->component)) && (!$print)) {
						$asset->url =  '<a href="'.JRoute::_('index.php?option=com_categories&task=category.edit&id='.$asset->targetid.'&extension='.$asset->component).'">'.$asset->title.' '.$asset->status.'</a>';
					} else {
						$asset->url =  '<span class="title">'.JText::_($asset->title).' '.$asset->status.'</span>';
					}
				}
				$asset->type	= 'category';
				$asset->icon	= 'category';
			} elseif (stripos($asset->name, "article")) {
				// Article level
				if ((JFactory::getUser()->authorise('core.manage', $asset->component)) && (!$print)) {
					$asset->url =  '<a href="'.JRoute::_('index.php?option='.$asset->component.'&task=article.edit&id='.$asset->targetid.'&extension='.$asset->component).'">'.$asset->title.' '.$asset->status.'</a>';
				} else {
					$asset->url =  '<span class="title">'.JText::_($asset->title).' '.$asset->status.'</span>';
				}
				$asset->type	= $types[1];
				$asset->icon	= 'article';
			} elseif (stripos($asset->name, "module")) {
				// Module type
				if ((JFactory::getUser()->authorise('core.manage', $asset->component)) && (!$print)) {
					$asset->url =  '<a href="'.JRoute::_('index.php?option='.$asset->component.'&task=module.edit&id='.$asset->targetid).'">'.$asset->title.' '.$asset->status.'</a>';
				} else {
					$asset->url =  '<span class="title">'.JText::_($asset->title).' '.$asset->status.'</span>';
				}
				$asset->type	= $types[1];
				$asset->icon	= 'module';
			} else {
				// Third Party
				if ((JFactory::getUser()->authorise('core.manage', $asset->component)) && (!$print)) {
					$asset->url =  '<a href="'.JRoute::_('index.php?option='.$asset->component).'">'.$asset->title.'</a>';
				} else {
					$asset->url =  '<span class="title">'.JText::_($asset->title).'</span>';
				}
				$asset->type	= $types[1];
				$asset->icon	= 'article';
			}
		}

		return $assets;
	}

	/**
	 * Check for display action.
	 */
	static function displayAction($action,$level)
	{
		$corelogin 	= strpos($action,'core.login');
		$coreadmin 	= strpos($action,'core.admin');
		$coremanage	= strpos($action,'core.manage');

		$displayaction = (
			(($level >=2) && (($corelogin === false) && ($coreadmin === false) && ($coremanage === false)))
			||
			(($level == 1) && ($corelogin === false))
			||
			($level == 0)
		);

		return $displayaction;
	}

	/**
	 * Check for third party action.
	 */
	static function thirdCheck($rule,$component,$third)
	{
		$core = strpos($rule,'core.');
		if ($core === false){
			$third = 1;

			$lang = JFactory::getLanguage();
			$source 	= JPATH_ADMINISTRATOR . '/components/' . $component;
			$lang->load($component, JPATH_ADMINISTRATOR, null, false, false)
			||	$lang->load($component, $source, null, false, false)
			||	$lang->load($component, JPATH_ADMINISTRATOR, $lang->getDefault(), false, false)
			||	$lang->load($component, $source, $lang->getDefault(), false, false);
		}

		return $third;
	}

	/**
	 * Get actions.
	 */
	static function getActions()
	{
		// Initialise variable
		$actions	= array();
		if (version_compare(JVERSION, '3.2', 'ge')) {
			$filename 	= JPATH_ADMINISTRATOR.'/components/com_config/model/form/application.xml';
		} else {
			$filename 	= JPATH_ADMINISTRATOR.'/components/com_config/models/forms/application.xml';
		}
		$xml 		= simplexml_load_file($filename);

		foreach($xml->children()->fieldset as $fieldset)
		{
			if ('permissions' == (string) $fieldset['name']) {
				foreach ($fieldset->children() as $field)
				{
					if ('rules' == (string) $field['name']) {
						foreach ($field->children() as $action)
						{
							// Overwrite Super Admin to Configure for overview
							if(($action['title']) == 'JACTION_ADMIN_GLOBAL') {
								$action['title'] ='JACTION_ADMIN';
								$action['description'] ='JACTION_ADMIN_COMPONENT_DESC';
							}

							// Overwrite Access Component description
							if(($action['title']) == 'JACTION_MANAGE') {
								$action['description'] ='JACTION_MANAGE_COMPONENT_DESC';
							}

							$action['clean'] = str_replace('.','',$action['name']);

							$actions[(string) $action['title']] = array(
								(string) $action['name'],
								(string) $action['description'],
								(string) $action['clean']
							);
						}
						break;
						break;
						break;
					}
				}
			}
		}

		// Temporary way to add core.options
		$extra['JACTION_OPTIONS']    = array();
		$extra['JACTION_OPTIONS'][0] = 'core.options';
		$extra['JACTION_OPTIONS'][1] = 'JACTION_OPTIONS_COMPONENT_DESC';
		$extra['JACTION_OPTIONS'][2] = 'coreoptions';
		array_splice($actions, 4, 0, $extra);

		return $actions;
	}

	/**
	 *  Check for login action.
	 */
	static function loginActions($actions)
	{
		$loginactions=0;
		foreach($actions as $action) {
			$pos = strpos($action[0], 'core.login');
			if($pos !== false) {
				$loginactions++;
			}
		}

		return $loginactions;
	}

	/**
	 * Get trashed categories list.
	 */
	static function trashedCategories()
	{
		// Get trashed categories
		$db 	= JFactory::getDbo();
		$query	= $db->getQuery(true);
		$query->select('asset_id')
			->from('#__categories')
			->where('published = -2');
		$trashed_categories = $db->setQuery($query)->loadColumn();

		return $trashed_categories;
	}

	/**
	 * Get trashed content list.
	 */
	static function trashedContent()
	{
		// Get trashed content
		$db 	= JFactory::getDbo();
		$query	= $db->getQuery(true);
		$query->select('asset_id')
			->from('#__content')
			->where('state = -2');
		$trashed_content = $db->setQuery($query)->loadColumn();

		return $trashed_content;
	}

	/**
	 * Check for additional action.
	 */
	static function additionalAction($action)
	{
		$third = true;

		if(($action->name) == ('core.login.site')) {
			$third = false;
		} elseif(($action->name) == ('core.login.admin')) {
			$third = false;
		} elseif(($action->name) == ('core.login.offline')) {
			$third = false;
		} elseif(($action->name) == ('core.admin')) {
			$third = false;
		} elseif(($action->name) == ('core.manage')) {
			$third = false;
		} elseif(($action->name) == ('core.create')) {
			$third = false;
		} elseif(($action->name) == ('core.delete')) {
			$third = false;
		} elseif(($action->name) == ('core.edit')) {
			$third = false;
		} elseif(($action->name) == ('core.edit.state')) {
			$third = false;
		} elseif(($action->name) == ('core.edit.own')) {
			$third = false;
		}

		return $third;
	}

}