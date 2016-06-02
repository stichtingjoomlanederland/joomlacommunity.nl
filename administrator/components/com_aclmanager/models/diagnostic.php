<?php
/**
 * @package		ACL Manager for Joomla
 * @copyright 	Copyright (c) 2011-2016 Sander Potjer
 * @license 	GNU General Public License version 3 or later
 */

// No direct access.
defined('_JEXEC') or die;

// Load framework base classes
jimport('joomla.application.component.modellist');
jimport('joomla.access.rules');

/**
 * Aclmanager Model
 */
class AclmanagerModelDiagnostic extends JModelList
{
	public function getOrphanAssets()
	{
		// Initialise variable
		$db 					= JFactory::getDBO();
		$view					= JFactory::getApplication()->input->get('view');
		$params 				= JComponentHelper::getParams('com_aclmanager');
		$diagnosticlimit 		= $params->get('diagnosticlimit',250);
		$acl_categorymanager 	= $params->get('acl_categorymanager',1);
		$acl_modules 			= $params->get('acl_modules',1);

		if (($assets = parent::getItems())) {

			$issues = array();
			$issuecount = 0;

			// Get components
			$query	= $db->getQuery(true);
			$query->select(
				$this->getState(
					'list.select',
					'a.extension_id AS id, a.element AS name')
			);
			$query->from('#__extensions AS a');
			$query->where('type = '. $db->quote('component'));
			if($acl_categorymanager == false){
				$query->where('a.element not like \'com_categories\'');
			}
			$query->order('a.element ASC');
			$db->setQuery($query);
			$extensions = $db->loadObjectList('name');

			// Get categories
			$query	= $db->getQuery(true);
			$query->select(
				$this->getState(
					'list.select',
					'a.id AS id, a.asset_id AS asset_id, a.parent_id AS parent_id, a.level AS level, a.extension AS extension')
			);
			$query->from('#__categories AS a');
			$db->setQuery($query);
			$categories = $db->loadObjectList('asset_id');

			// Prepare category data
			foreach ($categories as $category) {
				$categories_id[$category->id] = $category;
				$categories_extensions[] = $category->extension;
			}

			// Get content
			$query	= $db->getQuery(true);
			$query->select(
				$this->getState(
					'list.select',
					'a.asset_id AS asset_id, a.catid AS catid')
			);
			$query->from('#__content AS a');
			$db->setQuery($query);
			$content = $db->loadObjectList('asset_id');

			// Get modules
			if (version_compare(JVERSION, '3.2', 'ge')) {
				$query	= $db->getQuery(true);
				$query->select(
					$this->getState(
						'list.select',
						'a.id AS id, a.asset_id AS asset_id')
				);
				$query->from('#__modules AS a');
				$db->setQuery($query);
				$modules = $db->loadObjectList('asset_id');
			} elseif($acl_modules) {
				$query	= $db->getQuery(true);
				$query->select(
					$this->getState(
						'list.select',
						'a.id AS id')
				);
				$query->from('#__modules AS a');
				$db->setQuery($query);
				$modules = $db->loadObjectList('id');

				foreach($modules as $module) {
					$module->name = 'com_modules.module.'.$module->id;
				}
			}

			foreach ($assets as $asset)
			{
				if(!strpos($asset->name,'.')) {
					$asset->component = $asset->name;
					$asset->type = 'component';
				} elseif (strpos($asset->name, '.category.')) {
					$asset->component = substr($asset->name, 0, strpos($asset->name, '.'));
					$asset->type = 'category';
				} elseif (strpos($asset->name, '.article.')) {
					$asset->component = substr($asset->name, 0, strpos($asset->name, '.'));
					$asset->type = 'article';
				} elseif (strpos($asset->name, '.module.')) {
					$asset->component = substr($asset->name, 0, strpos($asset->name, '.'));
					$asset->type = 'module';
				} else {
					$asset->component = substr($asset->name, 0, strpos($asset->name, '.'));
					$asset->type = 'thirdparty';
				}

				// Component
				if (($asset->type == 'component') && ($asset->component != 'com_massmail')) {
					if(!isset($extensions[$asset->name]->name)) {
						$asset->correct_parent = '';
						$asset->correct_level = '';
						$asset->correct_rules = '';
						$issues[] = $asset;
						$issuecount++;
					}
				// Category
				} elseif ($asset->type == 'category' && in_array($asset->component, $categories_extensions)) {
					if(!isset($categories[$asset->id])) {
						$asset->correct_parent = '';
						$asset->correct_level = '';
						$asset->correct_rules = '';
						$issues[] = $asset;
						$issuecount++;
					}
				// Article
				} elseif ($asset->type == 'article') {
					if(!isset($content[$asset->id]->catid)) {
						$asset->correct_parent = '';
						$asset->correct_level = '';
						$asset->correct_rules = '';
						$issues[] = $asset;
						$issuecount++;
					}
				// Module
				} elseif (($asset->type == 'module') && ($asset->component == 'com_modules')) {
					if (version_compare(JVERSION, '3.2', 'ge')) {
						if(!isset($modules[$asset->id]->id)) {
							$asset->correct_parent = '';
							$asset->correct_level = '';
							$asset->correct_rules = '';
							$issues[] = $asset;
							$issuecount++;
						}
					} else {
						$asset_name_parts = explode('.', $asset->name);
						$asset->module_id = $asset_name_parts[2];
						if(!isset($modules[$asset->module_id]->id)) {
							$asset->correct_parent = '';
							$asset->correct_level = '';
							$asset->correct_rules = '';
							$issues[] = $asset;
							$issuecount++;
						}
					}
				}

				// Break if too many issues
				if($issuecount == $diagnosticlimit) {
					break;
				}

				// Don't check all issues on home view
				if(($view == 'home') && $issues) {
					break;
				}
			}
		}

		return($issues);
	}
	/**
	 * Override getItems method.
	 */
	public function getAssetIssues()
	{
		// Initialise variable
		$db 				= JFactory::getDBO();
		$view				= JFactory::getApplication()->input->get('view');
		$params				= JComponentHelper::getParams('com_aclmanager');
		$diagnosticlimit 	= $params->get('diagnosticlimit',250);
		$acl_modules 		= $params->get('acl_modules',1);

		if (($assets = parent::getItems())) {

			$default_asset = '{}';
			$component_asset = '{"core.admin":[],"core.manage":[]}';
			$category_asset = '{"core.create":[],"core.delete":[],"core.edit":[],"core.edit.state":[],"core.edit.own":[]}';
			$article_asset = '{"core.delete":[],"core.edit":[],"core.edit.state":[]}';
			$module_asset = '{"core.delete":[],"core.edit":[],"core.edit.state":[]}';
			$core_noassets = array('com_admin','com_config','com_cpanel','com_login','com_mailto','com_massmail','com_wrapper','com_ajax','com_contenthistory');
			$issues = array();
			$issuecount = 0;

			$assets_name = $this->getListQuery();
			$db->setQuery($assets_name);
			$assets_name = $db->loadObjectList('name');

			// Get categories
			$query	= $db->getQuery(true);
			$query->select(
				$this->getState(
					'list.select',
					'a.id AS id, a.asset_id AS asset_id, a.parent_id AS parent_id, a.level AS level, a.extension AS extension')
			);
			$query->from('#__categories AS a');
			$db->setQuery($query);
			$categories = $db->loadObjectList('asset_id');

			// Prepare category data
			foreach ($categories as $category) {
				$categories_id[$category->id] = $category;
				$categories_extensions[] = $category->extension;
			}

			// Get content
			$query	= $db->getQuery(true);
			$query->select(
				$this->getState(
					'list.select',
					'a.asset_id AS asset_id, a.catid AS catid')
			);
			$query->from('#__content AS a');
			$db->setQuery($query);
			$content = $db->loadObjectList('asset_id');

			foreach ($assets as $asset)
			{
				if($asset->name == 'root.1') {
					$asset->type = 'config';
				} elseif(!strpos($asset->name,'.')) {
					$asset->component = $asset->name;
					$asset->type = 'component';
				} elseif (strpos($asset->name, '.category.')) {
					$asset->component = substr($asset->name, 0, strpos($asset->name, '.'));
					$asset->type = 'category';
				} elseif (strpos($asset->name, '.article.')) {
					$asset->component = substr($asset->name, 0, strpos($asset->name, '.'));
					$asset->type = 'article';
				} elseif (strpos($asset->name, '.module.')) {
					$asset->component = substr($asset->name, 0, strpos($asset->name, '.'));
					$asset->type = 'module';
				} else {
					$asset->component = substr($asset->name, 0, strpos($asset->name, '.'));
					$asset->type = 'thirdparty';
				}

				$asset->object = explode('.', $asset->name);

				$asset->correct_parent = '';
				$asset->correct_level = '';
				$asset->correct_rules = $asset->rules;

				// Root
				if ($asset->type == 'config') {
					$asset->correct_parent = 0;
					$asset->correct_level = 0;
				// Component
				} elseif ($asset->type == 'component') {
					$asset->correct_parent = 1;
					$asset->correct_level = 1;
					if (in_array($asset->name,$core_noassets,true)) {
						if($asset->rules !='{}') {
							$asset->correct_rules = $default_asset;
						}
					} elseif (($asset->rules == '') || ($asset->rules == '{}') || ($asset->rules == $component_asset)) {
						$xmlfile = JPATH_ADMINISTRATOR.'/components/'.$asset->name.'/access.xml';
						if(is_file($xmlfile)){
							$actions = JAccess::getActionsFromFile($xmlfile);
							$actionname = array();
							foreach($actions as $action) {
								$actionname[] = '"'.$action->name.'":[]';
							}
							$asset->correct_rules = '{'.implode(',', $actionname).'}';
						} else {
							$asset->correct_rules = $component_asset;
						}
					}
				// Category
				} elseif ($asset->type == 'category' && in_array($asset->component, $categories_extensions)) {
					if(isset($categories[$asset->id])) {
						$catinfo = $categories[$asset->id];
					}

					if (isset($catinfo)) {
						if ($catinfo->parent_id == 1) {
							$asset->correct_parent = $assets_name[$asset->component]->id;
						} elseif($catinfo->parent_id !=0) {
							$asset->correct_parent = $categories_id[$catinfo->parent_id]->asset_id;
						}
						$asset->correct_level = ($catinfo->level + 1);
						// Prevent parent_id = 0
						if($asset->correct_parent == 0) {
							$asset->correct_parent = $assets_name[$asset->component]->id;
							$asset->correct_level = $assets_name[$asset->component]->level + 1;
							if($view == 'diagnostic') {
								$object_url = JRoute::_('index.php?option=com_categories&task=category.edit&id='.$asset->object[2].'&extension='.$asset->component, false);
								JFactory::getApplication()->enqueueMessage(JText::sprintf('COM_ACLMANAGER_DIAGNOSTIC_ISSUE_DETECTED_CATEGORY', $object_url, $asset->title), 'error');
							}
						}
					}

					if (($asset->rules == '') || ($asset->rules == '{}') || ($asset->rules == '{"core.create":[],"core.delete":[],"core.edit":[],"core.edit.state":[]}') || ($asset->rules == $category_asset)) {
						$xmlfile = JPATH_ADMINISTRATOR.'/components/'.$asset->component.'/access.xml';
						if(is_file($xmlfile)){
							$actions = JAccess::getActionsFromFile($xmlfile, "/access/section[@name='category']/");
							$actionname = array();
							foreach($actions as $action) {
								$actionname[] = '"'.$action->name.'":[]';
							}
							$asset->correct_rules = '{'.implode(',', $actionname).'}';
						} else {
							$asset->correct_rules = $category_asset;
						}
					}
				// Article
				} elseif ($asset->type == 'article') {
					if(isset($content[$asset->id]->catid)) {
						$catid = $content[$asset->id]->catid;
						if(isset($categories_id[$catid])) {
							$catinfo = $categories_id[$catid];
						}

						$asset->correct_parent = $catinfo->asset_id;
						$asset->correct_level = ($catinfo->level + 2);
					}
					if (($asset->rules == '') || ($asset->rules == '{}') || ($asset->rules == $category_asset)) {
						$asset->correct_rules = $article_asset;
					}
					// Prevent parent_id = 0
					if($asset->correct_parent == 0) {
						$asset->correct_parent = $assets_name[$asset->component]->id;
						$asset->correct_level = $assets_name[$asset->component]->level + 1;
						if($view == 'diagnostic') {
							$object_url = JRoute::_('index.php?option=com_content&task=article.edit&id='.$asset->object[2], false);
							JFactory::getApplication()->enqueueMessage(JText::sprintf('COM_ACLMANAGER_DIAGNOSTIC_ISSUE_DETECTED_ARTICLE', $object_url, $asset->title), 'error');
						}
					}
				// Module
				} elseif ($asset->type == 'module') {
					$asset->correct_parent = $assets_name[$asset->component]->id;
					$asset->correct_level = 2;
					if (($asset->rules == '') || ($asset->rules == '{}')) {
						$asset->correct_rules = $module_asset;
					}
				} else {
					$asset->correct_parent = $asset->parent;
					$asset->correct_level = $asset->level;
					if ($asset->rules == '') {
						$asset->correct_rules = $default_asset;
					}
					// Prevent parent_id = 0
					if($asset->correct_parent == 0) {
						$asset->correct_parent = $assets_name['root.1']->id;
						$asset->correct_level = 1;
					}
				}

				// Get list of the issues
				if(($asset->parent != $asset->correct_parent) || ($asset->level != $asset->correct_level) || ($asset->rules != $asset->correct_rules)) {
					$issues[] = $asset;
					$issuecount++;
				}

				// Break if too many issues
				if($issuecount == $diagnosticlimit) {
					break;
				}

				// Don't check all issues on home view
				if(($view == 'home') && $issues) {
					break;
				}
			}
		}

		return($issues);
	}

	/**
	 * Override getItems method.
	 */
	public function getMissingAssets()
	{
		$db						= $this->getDbo();
		$view					= JFactory::getApplication()->input->get('view');
		$params 				= JComponentHelper::getParams('com_aclmanager');
		$diagnosticlimit 		= $params->get('diagnosticlimit',250);
		$acl_categorymanager 	= $params->get('acl_categorymanager',1);
		$acl_modules 			= $params->get('acl_modules',1);
		$default_asset 			= '{}';
		$component_asset 		= '{"core.admin":[],"core.manage":[]}';
		$category_asset 		= '{"core.create":[],"core.delete":[],"core.edit":[],"core.edit.state":[],"core.edit.own":[]}';
		$article_asset 			= '{"core.delete":[],"core.edit":[],"core.edit.state":[]}';
		$module_asset		 	= '{"core.delete":[],"core.edit":[],"core.edit.state":[]}';
		$core_noassets = array('com_admin','com_config','com_cpanel','com_login','com_mailto','com_massmail','com_wrapper','com_ajax','com_contenthistory');

		$assets_name = $this->getListQuery();
		$db->setQuery($assets_name);
		$assets_name = $db->loadObjectList('name');

		// Missing assets array
		$issues = array();
		$issuecount = 0;

		// Check for root asset
		if(!isset($assets_name['root.1'])){
			$root->title = 'Root Asset';
			$root->name = 'root.1';
			$root->type = 'config';
			$root->correct_level = 0;
			$root->correct_parent = 0;
			$root->correct_rules = '{"core.login.site":{"6":1,"2":1},"core.login.admin":{"6":1},"core.login.offline":[],"core.admin":{"8":1},"core.manage":{"7":1},"core.create":{"6":1,"3":1},"core.delete":{"6":1},"core.edit":{"6":1,"4":1},"core.edit.state":{"6":1,"5":1},"core.edit.own":{"6":1,"3":1}}';
			$root->asset_id = '1';
			$issues[] = $root;
		}

		// Get missing components assets
		$components_assets = array();
		$assets = parent::getItems();
		foreach($assets as $asset) {
			if(!strpos($asset->name, '.')) {
				$components_assets[] = $asset->name;
			}
		}

		// Get components
		$query	= $db->getQuery(true);
		$query->select(
			$this->getState(
				'list.select',
				'a.extension_id AS id, a.element AS name')
		);
		$query->from('#__extensions AS a');
		$query->where('type = '. $db->quote('component'));
		if($acl_categorymanager == false){
			$query->where('a.element not like \'com_categories\'');
		}
		$query->order('a.element ASC');
		$db->setQuery($query);
		$extensions = $db->loadObjectList();

		// Loop extensions
		foreach($extensions as $extension) {
			$extension->name = strtolower($extension->name);
			$comprefix = strpos($extension->name,'com_');
			if($comprefix === false) {
				$extension->name = 'com_'.$extension->name;
			}
			$extension->title = $extension->name;
			$extension->level = '';
			$extension->correct_level = 1;
			$extension->parent = '';
			$extension->correct_parent = 1;
			$extension->rules = '';
			if (in_array($extension->name,$core_noassets,true)) {
				$extension->correct_rules = $default_asset;
			} else {
				$xmlfile = JPATH_ADMINISTRATOR.'/components/'.$extension->name.'/access.xml';
				if(is_file($xmlfile)){
					$actions = JAccess::getActionsFromFile($xmlfile);
					$actionname = array();
					foreach($actions as $action) {
						$actionname[] = '"'.$action->name.'":[]';
					}
					$extension->correct_rules = '{'.implode(',', $actionname).'}';
				} else {
					$extension->correct_rules = $component_asset;
				}
			}
			$extension->id = '';
			$extension->asset_id = '';
			$extension->type = 'component';
			if (!in_array($extension->name, $components_assets)) {
			    $issues[] = $extension;
			    $issuecount++;
			}

			// Break if too many issues
			if($issuecount == $diagnosticlimit) {
				break;
			}

			// Don't check all issues on home view
			if(($view == 'home') && $issues) {
				break;
			}
		}


		// Get missing categories assets
		$category_assets = array();
		foreach($assets as $asset) {
			if(strpos($asset->name, '.category.')) {
				$category_assets[] = $asset->name;
			}
		}

		// Get categories
		$query	= $db->getQuery(true);
		$query->select(
			$this->getState(
				'list.select',
				'a.id AS catid, a.asset_id AS asset_id, a.parent_id AS parent_id, a.level AS level, a.extension AS extension, a.title AS title')
		);
		$query->from('#__categories AS a');
		$query->where('a.id <> 1');
		$db->setQuery($query);
		$categories = $db->loadObjectList();

		// Prepare category data
		foreach ($categories as $category) {
			$categories_id[$category->catid] = $category;
		}

		// Loop categories
		foreach($categories as $category) {
			$category->title = $category->title;
			$category->name = $category->extension.'.category.'.$category->catid;
			$category->correct_level = $category->level+1;
			$category->level = '';
			$category->correct_parent = 1;
			if ($category->parent_id == 1) {
				$category->correct_parent = $assets_name[$category->extension]->id;
			} elseif($category->parent_id !=0) {
				$category->correct_parent = $categories_id[$category->parent_id]->asset_id;
			}
			$category->parent = '';
			$category->rules = '';
			$xmlfile = JPATH_ADMINISTRATOR.'/components/'.$category->extension.'/access.xml';
			if(is_file($xmlfile)){
				$actions = JAccess::getActionsFromFile($xmlfile, "/access/section[@name='category']/");
				$actionname = array();
				foreach($actions as $action) {
					$actionname[] = '"'.$action->name.'":[]';
				}
				$category->correct_rules = '{'.implode(',', $actionname).'}';
			} else {
				$category->correct_rules = $category_asset;
			}
			$category->id = $category->asset_id;
			$category->type = 'category';
			if (!in_array($category->extension.'.category.'.$category->catid, $category_assets)) {
			    $issues[] = $category;
			    $issuecount++;
			}

			// Break if too many issues
			if($issuecount == $diagnosticlimit) {
				break;
			}

			// Don't check all issues on home view
			if(($view == 'home') && $issues) {
				break;
			}
		}

		// Get missing article assets
		$article_assets = array();
		foreach($assets as $asset) {
			if(strpos($asset->name, '.article.')) {
				$article_assets[] = $asset->name;
			}
		}

		// Get articles
		$query	= $db->getQuery(true);
		$query->select(
			$this->getState(
				'list.select',
				'a.id AS articleid, a.asset_id AS asset_id, a.catid AS catid, a.title AS title')
		);
		$query->from('#__content AS a');
		$db->setQuery($query);
		$content = $db->loadObjectList();

		// Loop articles
		foreach($content as $article) {
			$article->title = $article->title;
			$article->name = 'com_content.article.'.$article->articleid;
			if(isset($categories_id[$article->catid])) {
				$catinfo = $categories_id[$article->catid];
			}
			$article->correct_parent = $catinfo->asset_id;
			$article->correct_level = ($catinfo->level + 2);
			$article->level = '';
			$article->parent = '';
			$article->rules = '';
			$article->correct_rules = $article_asset;
			$article->id = $article->asset_id;
			$article->type = 'article';
			if (!in_array('com_content.article.'.$article->articleid, $article_assets)) {
			    $issues[] = $article;
			    $issuecount++;
			}

			// Break if too many issues
			if($issuecount == $diagnosticlimit) {
				break;
			}

			// Don't check all issues on home view
			if(($view == 'home') && $issues) {
				break;
			}
		}
		// Get missing module assets
		if ((version_compare(JVERSION, '3.2', 'ge')) || ($acl_modules))  {

			$module_assets = array();
			foreach($assets as $asset) {
				if(strpos($asset->name, '.module.')) {
					$module_assets[] = $asset->name;
				}
			}
		}

		if (version_compare(JVERSION, '3.2', 'ge')) {
			// Get modules
			$query	= $db->getQuery(true);
			$query->select(
				$this->getState(
					'list.select',
					'a.id AS moduleid, a.asset_id AS asset_id, a.title AS title')
			);
			$query->from('#__modules AS a');
			$db->setQuery($query);
			$modules = $db->loadObjectList();

			// Loop modules
			foreach($modules as $module) {
				$module->title = $module->title;
				$module->name = 'com_modules.module.'.$module->moduleid;
				$module->correct_parent = $assets_name['com_modules']->id;
				$module->correct_level = 2;
				$module->level = '';
				$module->parent = '';
				$module->rules = '';
				$module->correct_rules = $module_asset;
				$module->id = $module->asset_id;
				$module->type = 'module';
				if (!in_array('com_modules.module.'.$module->moduleid, $module_assets)) {
				    $issues[] = $module;
				    $issuecount++;
				}

				// Break if too many issues
				if($issuecount == $diagnosticlimit) {
					break;
				}

				// Don't check all issues on home view
				if(($view == 'home') && $issues) {
					break;
				}
			}

		} elseif ($acl_modules) {
			// Get modules
			$query	= $db->getQuery(true);
			$query->select(
				$this->getState(
					'list.select',
					'a.id AS moduleid, a.title AS title')
			);
			$query->from('#__modules AS a');
			$db->setQuery($query);
			$modules = $db->loadObjectList();

			// Loop modules
			foreach($modules as $module) {
				$module->title = $module->title;
				$module->name = 'com_modules.module.'.$module->moduleid;
				$module->correct_parent = $assets_name['com_modules']->id;
				$module->correct_level = 2;
				$module->level = '';
				$module->parent = '';
				$module->rules = '';
				$module->correct_rules = $module_asset;
				$module->id = '';
				$module->type = 'module';
				if (!in_array('com_modules.module.'.$module->moduleid, $module_assets)) {
				    $issues[] = $module;
				    $issuecount++;
				}

				// Break if too many issues
				if($issuecount == $diagnosticlimit) {
					break;
				}

				// Don't check all issues on home view
				if(($view == 'home') && $issues) {
					break;
				}
			}
		}

		return $issues;
	}

	/**
	 * Override getItems method.
	 */
	public function getAdminConflicts()
	{
		// Variables
		$db = JFactory::getDbo();

		// Get required backend menu Access Level
		$query = $db->getQuery(true);
		$query->select('access');
		$query->from('#__modules');
		$query->where('module = '. $db->quote('mod_menu'));
		$query->where('client_id = 1');
		$db->setQuery($query);
		$backend_menu_access = $db->loadResult();

		// Get the backend Access Level rules
		$query = $db->getQuery(true);
		$query->select('rules');
		$query->from('#__viewlevels');
		$query->where('id = '. (int) $backend_menu_access);
		$db->setQuery($query);
		$rules = json_decode($db->loadResult());

		// Get User Groups with backend Access Level
		$nestedgroups = array();
		foreach($rules as $rule) {
			$nestedgroups[] = $this->getNestedGroups($rule);
		}
		$backendviewgroups = array();
		foreach($nestedgroups as $nestedgroup) {
			foreach($nestedgroup as $id) {
				$backendviewgroups[] = $id;
			}
		}
		$backendviewgroups = array_unique($backendviewgroups);

		// Get User Groups with backend login
		$query = $db->getQuery(true);
		$query->select('id, parent_id, title');
		$query->from('#__usergroups');
		$db->setQuery($query);
		$groups = $db->loadObjectList();

		$backend_login = array();
		foreach ($groups as $group) {
			if ((JAccess::checkGroup($group->id, 'core.login.admin')) || (JAccess::checkGroup($group->id, 'core.admin'))) {
				$backend_login[] = $group;
			}
		}

		// Get User Groups with backend login but no Access Level
		$noaccess = array();
		foreach($backend_login as $backend) {
			if (!in_array($backend->id, $backendviewgroups)) {
			    $noaccess[] = $backend;
			}
		}
		// Filter to User Groups to fix only
		$noaccessids = array();
		foreach($noaccess as $group) {
			$noaccessids[] = $group->id;
		}
		$groupstofix = array();
		foreach($noaccess as $group) {
			if (!in_array($group->parent_id, $noaccessids)) {
			    $groupstofix[] = $group;
			}
		}

		return $groupstofix;

	}

	public static function getNestedGroups($groupId)
	{
		$userGroups = array();
		$userGroupPaths = array();
		// Preload all groups
		if (empty($userGroups)) {
			$db = JFactory::getDbo();
			$query = $db->getQuery(true)
				->select('parent.id, parent.lft, parent.rgt')
				->from('#__usergroups AS parent')
				->order('parent.lft');
			$db->setQuery($query);
			$userGroups = $db->loadObjectList('id');
		}

		// Make sure groupId is valid
		if (!array_key_exists($groupId, $userGroups)) {
			return array();
		}

		// Get parent groups and leaf group
		if (!isset($userGroupPaths[$groupId])) {
			$userGroupPaths[$groupId] = array();

			foreach ($userGroups as $group) {
				if ($group->rgt <= $userGroups[$groupId]->rgt && $group->lft >= $userGroups[$groupId]->lft) {
					$userGroupPaths[$groupId][] = $group->id;
				}
			}
		}

		return $userGroupPaths[$groupId];
	}


	public function fixAssetIssues()
	{

		$issues = $this->getAssetIssues();
		$db = JFactory::getDBO();
		if($issues) {
			$total = count($issues);
			foreach ($issues as $issue) {
				$query = $this->_db->getQuery(true);
				$query->update('#__assets');
				if($issue->parent != $issue->correct_parent) {
					$query->set('parent_id = ' . (int) $issue->correct_parent);
				}
				if($issue->level != $issue->correct_level) {
					$query->set('level = ' . (int) $issue->correct_level);
				}
				if($issue->rules != $issue->correct_rules) {
					$query->set('rules = ' . $db->quote($issue->correct_rules));
				}
				$query->where('id = ' . (int) $issue->id);
				$this->_db->setQuery($query);
				if (!$this->_db->query())
				{
					$this->setError(JText::_('COM_ACLMANAGER_DIAGNOSTIC_ASSET_ISSUES_FAILED'));
					return false;
				}
			}
			if($total == 1){
				JFactory::getApplication()->enqueueMessage(JText::sprintf('COM_ACLMANAGER_DIAGNOSTIC_ASSET_ISSUES_FIXED', $total));
			} else {
				JFactory::getApplication()->enqueueMessage(JText::sprintf('COM_ACLMANAGER_DIAGNOSTIC_ASSET_ISSUES_FIXED_PLURAL', $total));
			}
		}

		$success = $this->rebuild();
		if($success) {
			JFactory::getApplication()->enqueueMessage(JText::_('COM_ACLMANAGER_DIAGNOSTIC_REBUILD_SUCCESS'));
		} else {
			JError::raiseWarning( 100, JText::_('COM_ACLMANAGER_DIAGNOSTIC_REBUILD_FAILED' ));
		}
	}

	public function fixOrphanAssets()
	{
		$issues = $this->getOrphanAssets();
		$db = JFactory::getDBO();

		if($issues) {
			$ids = array();
			$total = count($issues);
			foreach ($issues as $issue) {
				$ids[] = $issue->id;
			}
			$assetsids = (implode(',', $ids));

			// Remove orphans
			$query = $this->_db->getQuery(true);
			$query->delete('#__assets');
			$query->where('id IN ('.$assetsids.')');
			$this->_db->setQuery($query);
			if (!$this->_db->query())
			{
				$this->setError(JText::_('COM_ACLMANAGER_DIAGNOSTIC_ORPHAN_ASSETS_FAILED'));
				return false;
			}

			if($total == 1){
				JFactory::getApplication()->enqueueMessage(JText::sprintf('COM_ACLMANAGER_DIAGNOSTIC_ORPHAN_ASSETS_FIXED', $total));
			} else {
				JFactory::getApplication()->enqueueMessage(JText::sprintf('COM_ACLMANAGER_DIAGNOSTIC_ORPHAN_ASSETS_FIXED_PLURAL', $total));
			}
		}

		$success = $this->rebuild();
		if($success) {
			JFactory::getApplication()->enqueueMessage(JText::_('COM_ACLMANAGER_DIAGNOSTIC_REBUILD_SUCCESS'));
		} else {
			JError::raiseWarning( 100, JText::_('COM_ACLMANAGER_DIAGNOSTIC_REBUILD_FAILED' ));
		}
	}


	public function fixMissingAssets()
	{
		$assets_missing = $this->getMissingAssets();
		$db = JFactory::getDBO();
		$query = $this->_db->getQuery(true);
		$query->select('rgt');
		$query->from('#__assets');
		$query->where('id = 1');
		$db->setQuery($query);
		$rightId = $db->loadResult();

		$assets_name = $this->getListQuery();
		$db->setQuery($assets_name);
		$assets_name = $db->loadObjectList('id');

		if($assets_missing) {
			$total = count($assets_missing);
			foreach ($assets_missing as $asset) {
				$rightId++;
				$query = $this->_db->getQuery(true);
				if (array_key_exists($asset->asset_id, $assets_name)) {
					$query->update('#__assets');
					$query->where('id = ' . $db->quote($asset->asset_id));
				} else {
					$query->insert('#__assets');
					$query->set('id = ' . $db->quote($asset->asset_id));
				}
				$query->set('parent_id = ' . (int) ($asset->correct_parent));
				$query->set('level = ' . (int) ($asset->correct_level));
				$query->set('lft = ' . (int) $rightId);
				$query->set('rgt = ' . (int) ($rightId+1));
				$query->set('name = ' . $db->quote($asset->name));
				$query->set('title = ' . $db->quote($asset->title));
				$query->set('rules = ' . $db->quote($asset->correct_rules));

				$this->_db->setQuery($query);
				if (!$this->_db->query())
				{
					$this->setError(JText::_('COM_ACLMANAGER_DIAGNOSTIC_MISSING_ASSETS_FAILED'));
					return false;
				}

				if($asset->asset_id == 0) {
					$asset_id = $db->insertid();
					if($asset->type == 'category'){
						$query = $this->_db->getQuery(true);
						$query->update('#__categories');
						$query->where('id = ' . $db->quote($asset->catid));
						$query->set('asset_id = ' . (int) ($asset_id));
						$this->_db->setQuery($query);
						$this->_db->query();
					} elseif($asset->type == 'article'){
						$query = $this->_db->getQuery(true);
						$query->update('#__content');
						$query->where('id = ' . $db->quote($asset->articleid));
						$query->set('asset_id = ' . (int) ($asset_id));
						$this->_db->setQuery($query);
						$this->_db->query();
					} elseif(($asset->type == 'module') && (version_compare(JVERSION, '3.2', 'ge'))) {
						$query = $this->_db->getQuery(true);
						$query->update('#__modules');
						$query->where('id = ' . $db->quote($asset->moduleid));
						$query->set('asset_id = ' . (int) ($asset_id));
						$this->_db->setQuery($query);
						$this->_db->query();
					}
				}

				$rightId++;
			}

			if($total == 1){
				JFactory::getApplication()->enqueueMessage(JText::sprintf('COM_ACLMANAGER_DIAGNOSTIC_MISSING_ASSETS_FIXED', $total));
			} else {
				JFactory::getApplication()->enqueueMessage(JText::sprintf('COM_ACLMANAGER_DIAGNOSTIC_MISSING_ASSETS_FIXED_PLURAL', $total));
			}
		}

		$success = $this->rebuild();
		if($success) {
			JFactory::getApplication()->enqueueMessage(JText::_('COM_ACLMANAGER_DIAGNOSTIC_REBUILD_SUCCESS'));
		} else {
			JError::raiseWarning( 100, JText::_('COM_ACLMANAGER_DIAGNOSTIC_REBUILD_FAILED' ));
		}
	}

	public function fixAdminConflicts()
	{
		// Variables
		$db = JFactory::getDbo();

		// Get required backend menu Access Level
		$query = $db->getQuery(true);
		$query->select('access');
		$query->from('#__modules');
		$query->where('module = '. $db->quote('mod_menu'));
		$query->where('client_id = 1');
		$db->setQuery($query);
		$backend_menu_access = $db->loadResult();

		// Get the backend Access Level rules
		$query = $db->getQuery(true);
		$query->select('rules');
		$query->from('#__viewlevels');
		$query->where('id = '. (int) $backend_menu_access);
		$db->setQuery($query);
		$rules = json_decode($db->loadResult());

		// Get User Groups to fix
		$groupstofix = $this->getAdminConflicts();
		$total = count($groupstofix);

		$add = array();
		foreach ($groupstofix as $group) {
			$add[] = (int) $group->id;
		}

		// Prepare new rule
		$newrule = array_merge($rules,$add);
		$newrule = json_encode($newrule);
		echo($newrule);

		// Get the backend Access Level rules
		$query = $db->getQuery(true);
		//$query->select('rules');
		$query->update('#__viewlevels');
		$query->set('rules = ' . $db->quote($newrule));
		$query->where('id = '. (int) $backend_menu_access);
		$db->setQuery($query);

		if (!$this->_db->query()) {
			$this->setError(JText::_('COM_ACLMANAGER_DIAGNOSTIC_ADMIN_ACCESS_CONFLICTS_FAILED'));
			return false;
		}

		if($total == 1){
			JFactory::getApplication()->enqueueMessage(JText::sprintf('COM_ACLMANAGER_DIAGNOSTIC_ADMIN_ACCESS_CONFLICTS_FIXED', $total));
		} else {
			JFactory::getApplication()->enqueueMessage(JText::sprintf('COM_ACLMANAGER_DIAGNOSTIC_ADMIN_ACCESS_CONFLICTS_FIXED_PLURAL', $total));
		}
	}

	public function rebuild($parentId = null, $leftId = 0, $level = 0, $path = '')
	{
		// If no parent is provided, try to find it.
		if ($parentId === null)
		{
			// Get the root item.
			$parentId = 1;
			if ($parentId === false)
			{
				return false;
			}

		}

		// Build the structure of the recursive query.
		if (!isset($this->_cache['rebuild.sql']))
		{
			$query = $this->_db->getQuery(true);
			$query->select('id');
			$query->from('#__assets');
			$query->where('parent_id = %d');

			// If the table has an ordering field, use that for ordering.
			if (property_exists($this, 'ordering'))
			{
				$query->order('parent_id, ordering, lft');
			}
			else
			{
				$query->order('parent_id, lft');
			}
			$this->_cache['rebuild.sql'] = (string) $query;
		}

		// Assemble the query to find all children of this node.
		$this->_db->setQuery(sprintf($this->_cache['rebuild.sql'], (int) $parentId));
		$children = $this->_db->loadObjectList();

		// The right value of this node is the left value + 1
		$rightId = $leftId + 1;

		// execute this function recursively over all children
		foreach ($children as $node)
		{
			// $rightId is the current right value, which is incremented on recursion return.
			// Increment the level for the children.
			// Add this item's alias to the path (but avoid a leading /)
			$rightId = $this->rebuild($node->{'id'}, $rightId, $level + 1);

			// If there is an update failure, return false to break out of the recursion.
			if ($rightId === false)
			{
				return false;
			}
		}

		$query = $this->_db->getQuery(true);
		$query->update('#__assets');
		$query->set('lft = ' . (int) $leftId);
		$query->set('rgt = ' . (int) $rightId);
		$query->set('level = ' . (int) $level);
		$query->where('id = ' . (int) $parentId);
		$this->_db->setQuery($query);

		// If there is an update failure, return false to break out of the recursion.
		if (!$this->_db->query())
		{
			$e = new JException(JText::sprintf('JLIB_DATABASE_ERROR_REBUILD_FAILED', get_class($this), $this->_db->getErrorMsg()));
			$this->setError($e);
			return false;
		}

		// Return the right value of this node + 1.
		return $rightId + 1;
	}


	/**
	 * Build an SQL query to load the list data.
	 */
	protected function getListQuery()
	{
		// Initialise variables.
		$db		= $this->getDbo();
		$query	= $db->getQuery(true);
		$params = JComponentHelper::getParams('com_aclmanager',1);

		// Override list limit for print view
		$layout	= JFactory::getApplication()->input->get('layout',null);
		$this->setState('list.limit', 0);

		// Select the required fields from the table.
		$query->select(
			$this->getState(
				'list.select',
				'a.id AS id, a.name AS name, a.title AS title, a.level AS level, a.parent_id AS parent, a.rules AS rules')
		);
		$query->from('#__assets AS a');
		$query->order('a.title ASC');
		return $query;
	}
}