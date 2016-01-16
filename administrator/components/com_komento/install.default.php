<?php
/**
* @package		Komento
* @copyright	Copyright (C) 2012 Stack Ideas Private Limited. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* Komento is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Restricted access');

jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');

if(!defined('DS')) {
	define('DS',DIRECTORY_SEPARATOR);
}

class KomentoInstaller
{
	private $package		= 'paid';
	private $jinstaller		= null;
	private $manifest		= null;
	private $messages		= array();
	private $db				= null;
	private $installPath	= null;
	private $joomlaVersion	= null;

	public function __construct( JInstaller $jinstaller )
	{
		$this->db			= KomentoDBHelper::getDBO();
		$this->jinstaller	= $jinstaller;
		$this->manifest		= $this->jinstaller->getManifest();
		$this->installPath	= $this->jinstaller->getPath('source');
		$this->joomlaVersion= KomentoInstaller::getJoomlaVersion();
		$this->komentoComponentId = $this->getKomentoComponentId();
	}

	public function execute()
	{
		if( !$this->checkConfigParam() )
		{
			$this->setMessage( 'Warning : The system could not update old configuration data into json format.', 'warning' );
		}

		if( !$this->checkACLParam() )
		{
			$this->setMessage( 'Warning : The system could not update old acl data into json format.', 'warning' );
		}

		if( !$this->checkDB() )
		{
			$this->setMessage( 'Warning : The system encounter an error when it tries to update the database. Please kindly update the database manually.', 'warning' );
		}

		if( !$this->checkKonfig() )
		{
			$this->setMessage( 'Warning : The system encounter an error when it tries to create default konfig. Please kindly configure Komento manually.', 'warning' );
		}

		if( !$this->checkConfig() )
		{
			$this->setMessage( 'Warning : The system encounter an error when it tries to create default config. Please kindly configure Komento manually.', 'warning' );
		}

		if( !$this->checkACL() )
		{
			$this->setMessage( 'Warning : The system encounter an error when it tries to create default ACL settings. Please kindly configure ACL manually.', 'warning' );
		}

		if( !$this->checkMenu() )
		{
			$this->setMessage( 'Warning : The system encounter an error when it tries to update the menu item. Please kindly update the menu item manually.', 'warning' );
		}

		$this->checkAdminMenu();

		if( !$this->checkPlugins() )
		{
			$this->setMessage( 'Warning : The system encounter an error when it tries to install the user plugin. Please kindly install the plugin manually.', 'warning' );
		}

		if( !$this->checkMedia() )
		{
			$this->setMessage( 'Warning : The system could not copy files to Media folder. Please kindly check the media folder permission.', 'warning' );
		}

		if( !$this->checkModules() )
		{
			$this->setMessage( 'Warning : The system encounter an error when it tries to install the modules. Please kindly install the modules manually.', 'warning' );
		}

		if( !$this->checkMisc() )
		{
			$this->setMessage( 'Warning : The system could not update miscellaneous items.', 'warning' );
		}

		$this->checkFree();

		$this->setMessage( 'Success : Installation Completed. Thank you for choosing Komento.', 'info' );

	}

	/**
	 * We only support PHP 5 and above
	 */
	public static function checkPHP()
	{
		$phpVersion = floatval(phpversion());

		return ( $phpVersion >= 5 );
	}

	/**
	 * From time to time, any DB changes will be sync here
	 */
	private function checkDB()
	{
		return $this->getDatabaseUpdate()->update();
	}

	/**
	 * Make sure there's at least a default entry in configuration table
	 */
	private function checkKonfig()
	{
		$query	= 'SELECT COUNT(1) FROM ' . $this->db->nameQuote( '#__komento_configs' )
				. ' WHERE ' . $this->db->nameQuote( 'component' ) . ' = ' . $this->db->quote( 'com_komento' );

		$this->db->setQuery( $query );

		if( !$this->db->loadResult() )
		{
			$file		= JPATH_ROOT . DIRECTORY_SEPARATOR . 'administrator' . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_komento' . DIRECTORY_SEPARATOR . 'konfiguration.json';

			$content	= JFile::read( $file );

			$registry = KomentoInstaller::getRegistry( $content, 'json' );

			$obj		= new stdClass();
			$obj->component	= 'com_komento';
			$obj->params	= $registry->toString( 'json' );

			return $this->db->insertObject( '#__komento_configs', $obj );
		}

		return true;
	}

	private function checkConfig()
	{
		$query	= 'SELECT COUNT(1) FROM ' . $this->db->nameQuote( '#__komento_configs' )
				. ' WHERE ' . $this->db->nameQuote( 'component' ) . ' = ' . $this->db->quote( 'com_content' );

		$this->db->setQuery( $query );

		// If this is a fresh new installation
		if( !$this->db->loadResult() )
		{
			$file		= JPATH_ROOT . DIRECTORY_SEPARATOR . 'administrator' . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_komento' . DIRECTORY_SEPARATOR . 'configuration.json';

			$content = JFile::read( $file );

			$registry = KomentoInstaller::getRegistry( $content, 'json' );

			$registry->set( 'enable_komento', 1 );

			$obj		= new stdClass();
			$obj->component	= 'com_content';
			$obj->params	= $registry->toString( 'json' );

			return $this->db->insertObject( '#__komento_configs', $obj );
		}

		return true;
	}

	/**
	 * Create default ACL settings
	 */
	private function checkACL()
	{
		$query	= 'SELECT COUNT(*) FROM ' . $this->db->nameQuote( '#__komento_acl' );
		$this->db->setQuery( $query );

		if( !$this->db->loadResult() )
		{
			$json = KomentoInstaller::getJSON();

			$usergroupsPath = JPATH_ADMINISTRATOR . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_komento' . DIRECTORY_SEPARATOR . 'usergroupsacl.json';

			if( !JFile::exists( $usergroupsPath ) )
			{
				return false;
			}

			$usergroups = $json->decode( JFile::read( $usergroupsPath ) );

			$jversion = KomentoInstaller::getJoomlaVersion() == '1.5' ? 'j15' : 'j30';

			foreach( $usergroups->rules as $usergroup => $rules )
			{
				if( isset( $usergroups->mapping->$jversion->$usergroup ) )
				{
					$gid = $usergroups->mapping->$jversion->$usergroup;

					$string = $json->encode( $rules );

					$query = 'INSERT INTO ' . $this->db->nameQuote( '#__komento_acl' ) . ' VALUES (null, ' . $this->db->quote( $gid ) . ', ' . $this->db->quote( 'com_content' ) . ', ' . $this->db->quote( 'usergroup' ) . ', ' . $this->db->quote( $string ) . ')';

					$this->db->setQuery( $query );
					$this->db->query();
				}
			}
		}

		return true;
	}

	/**
	 * Make sure the menu items are correct, create if non.
	 */
	private function checkMenu()
	{
		// At the moment we skip frontend's menu
		return true;

		if ($this->komentoComponentId)
			return true;

		$mainMenutype = $this->getJoomlaDefaultMenutype();

		// Let's see if the menu item exists or not
		if( $this->joomlaVersion >= '1.6' )
		{
			$query	= 'SELECT COUNT(*) FROM ' . $this->db->nameQuote( '#__menu' )
					. ' WHERE ' . $this->db->nameQuote( 'link' ) . ' LIKE ' .  $this->db->Quote( '%option=com_komento%' )
					. ' AND `client_id`=' . $this->db->Quote( '0' )
					. ' AND `type`=' . $this->db->Quote( 'component' );
		} else {
			$query	= 'SELECT COUNT(*) FROM ' . $this->db->nameQuote( '#__menu' )
					. ' WHERE ' . $this->db->nameQuote( 'link' ) . ' LIKE ' .  $this->db->Quote( '%option=com_komento%' );
		}

		$this->db->setQuery( $query );

		// Update or create menu item
		if( $menuExists = $this->db->loadResult() )
		{
			if( $this->joomlaVersion >= '1.6' )
			{
				$query 	= 'UPDATE ' . $this->db->nameQuote( '#__menu' )
					. ' SET `component_id` = ' . $this->db->Quote( $this->komentoComponentId )
					. ' WHERE `link` LIKE ' . $this->db->Quote('%option=com_komento%')
					. ' AND `type` = ' . $this->db->Quote( 'component' )
					. ' AND `client_id` = ' . $this->db->Quote( '0' );
			}
			else
			{
				$query 	= 'UPDATE ' . $this->db->nameQuote( '#__menu' )
					. ' SET `componentid` = ' . $this->db->Quote( $this->komentoComponentId )
					. ' WHERE `link` LIKE ' . $this->db->Quote('%option=com_komento%');
			}

			$this->db->setQuery( $query );
			$this->db->query();
		}
		else
		{
			$query 	= 'SELECT ' . $this->db->nameQuote( 'ordering' )
					. ' FROM ' . $this->db->nameQuote( '#__menu' )
					. ' ORDER BY ' . $this->db->nameQuote( 'ordering' ) . ' DESC LIMIT 1';
			$this->db->setQuery( $query );
			$order 	= $this->db->loadResult() + 1;

			// hardcode the ordering
			$order = 99999;

			$table = JTable::getInstance( 'Menu', 'JTable' );

			if( $this->joomlaVersion >= '1.6' )
			{
				$table->menutype		= $mainMenutype;
				$table->title 			= 'Komento';
				$table->alias 			= 'Komento';
				$table->path 			= 'komento';
				$table->link 			= 'index.php?option=com_komento';
				$table->type 			= 'component';
				$table->published 		= '1';
				$table->parent_id 		= '1';
				$table->component_id	= $this->komentoComponentId;
				$table->ordering 		= $order;
				$table->client_id 		= '0';
				$table->language 		= '*';
				$table->setLocation('1', 'last-child');

			} else {

				$table->menutype	= $mainMenutype;
				$table->name		= 'Komento';
				$table->alias		= 'Komento';
				$table->link		= 'index.php?option=com_komento';
				$table->type		= 'component';
				$table->published	= '1';
				$table->parent		= '0';
				$table->componentid	= $this->komentoComponentId;
				$table->sublivel	= '';
				$table->ordering	= $order;
			}

			return $table->store();
		}
	}

	private function getKomentoComponentId()
	{
		if( $this->joomlaVersion >= '1.6' )
		{
			$query 	= 'SELECT ' . $this->db->nameQuote( 'extension_id' )
				. ' FROM ' . $this->db->nameQuote( '#__extensions' )
				. ' WHERE `element`=' . $this->db->Quote( 'com_komento' )
				. ' AND `type`=' . $this->db->Quote( 'component' );
		}
		else
		{
			$query 	= 'SELECT ' . $this->db->nameQuote( 'id' )
				. ' FROM ' . $this->db->nameQuote( '#__components' )
				. ' WHERE `option`=' . $this->db->Quote( 'com_komento' )
				. ' AND `parent`=' . $this->db->Quote( '0');
		}

		$this->db->setQuery( $query );

		return $this->db->loadResult();
	}

	private function getJoomlaDefaultMenutype()
	{
		$query	= 'SELECT `menutype` FROM ' . $this->db->nameQuote( '#__menu' )
				. ' WHERE ' . $this->db->nameQuote( 'home' ) . ' = ' . $this->db->quote( '1' );
		$this->db->setQuery( $query );

		return $this->db->loadResult();
	}

	/**
	 * There might be issues with the admin menu
	 */
	private function checkAdminMenu()
	{
		if( $this->joomlaVersion >= '1.6' && $this->komentoComponentId )
		{
			$query	= 'UPDATE '. $this->db->nameQuote( '#__menu' )
					. ' SET ' . $this->db->nameQuote( 'component_id' ) . ' = ' . $this->db->quote( $this->komentoComponentId )
					. ' WHERE ' . $this->db->nameQuote( 'client_id' ) . ' = ' . $this->db->quote( 1 )
					. ' AND ' . $this->db->nameQuote( 'title' ) . ' LIKE ' . $this->db->quote( 'com_komento%' )
					. ' AND ' . $this->db->nameQuote( 'component_id' ) . ' != ' . $this->komentoComponentId;
			$this->db->setQuery( $query );
			$this->db->query();
		}
	}

	/**
	 * Install default plugins
	 */
	private function checkPlugins()
	{
		$result = array();

		if( $this->joomlaVersion >= '3.0' )
		{
			$plugins = $this->manifest->plugins;

			if( $plugins instanceof SimpleXMLElement && count( $plugins ) )
			{
				foreach( $plugins->plugin as $plugin )
				{
					$plgDir = $this->installPath.DIRECTORY_SEPARATOR.'plugins'.DIRECTORY_SEPARATOR.$plugin->attributes()->plugin;

					if( JFolder::exists($plgDir) )
					{
						$jinstaller = new JInstaller;
						$result[]	= $jinstaller->install($plgDir);

						$type = (string) $jinstaller->manifest->attributes()->type;

						if (count($jinstaller->manifest->files->children()))
						{
							foreach ($jinstaller->manifest->files->children() as $file)
							{
								if ((string) $file->attributes()->$type)
								{
									$element = (string) $file->attributes()->$type;
									break;
								}
							}
						}

						$query	= ' UPDATE `#__extensions` SET `enabled` = ' . $this->db->quote( 1 )
								. ' WHERE `element` = ' . $this->db->quote( $element )
								. ' AND `folder` = ' . $this->db->quote( $plugin->attributes()->group )
								. ' AND `type` = ' . $this->db->quote( 'plugin' );
						$this->db->setQuery( $query );
						$result[] = $this->db->query();
					}
				}
			}
		}
		elseif( $this->joomlaVersion > '1.5' && $this->joomlaVersion < '3.0' )
		{
			//$plugins = $this->manifest->xpath('plugins/plugin');
			$plugins = $this->manifest->plugins;

			if( $plugins instanceof JXMLElement && count($plugins) )
			{
				foreach ($plugins->plugin as $plugin)
				{
					$plgDir = $this->installPath.DIRECTORY_SEPARATOR.'plugins'.DIRECTORY_SEPARATOR.$plugin->getAttribute('plugin');

					if( JFolder::exists($plgDir) )
					{
						$jinstaller = new JInstaller;
						$result[]	= $jinstaller->install($plgDir);

						$type = (string) $jinstaller->manifest->attributes()->type;

						if (count($jinstaller->manifest->files->children()))
						{
							foreach ($jinstaller->manifest->files->children() as $file)
							{
								if ((string) $file->attributes()->$type)
								{
									$element = (string) $file->attributes()->$type;
									break;
								}
							}
						}

						$query	= ' UPDATE `#__extensions` SET `enabled` = ' . $this->db->quote( 1 )
								. ' WHERE `element` = ' . $this->db->quote( $element )
								. ' AND `folder` = ' . $this->db->quote( $jinstaller->manifest->getAttribute('group') )
								. ' AND `type` = ' . $this->db->quote( 'plugin' );
						$this->db->setQuery( $query );
						$result[] = $this->db->query();
					}
				}
			}
		}
		else
		{
			$plugins = $this->jinstaller->_adapters['component']->manifest->getElementByPath('plugins');

			if( $plugins instanceof JSimpleXMLElement && count($plugins->children()) )
			{
				foreach ($plugins->children() as $plugin)
				{
					$plgDir = $this->installPath.DIRECTORY_SEPARATOR.'plugins'.DIRECTORY_SEPARATOR.$plugin->attributes('plugin');

					if( JFolder::exists($plgDir) )
					{
						$jinstaller = new JInstaller;
						$result[]	= $jinstaller->install($plgDir);

						$type = $jinstaller->_adapters['plugin']->manifest->attributes('type');

						// Set the installation path
						$element = $jinstaller->_adapters['plugin']->manifest->getElementByPath('files');
						if (is_a($element, 'JSimpleXMLElement') && count($element->children())) {
							$files = $element->children();
							foreach ($files as $file) {
								if ($file->attributes($type)) {
									$element = $file->attributes($type);
									break;
								}
							}
						}

						$query	= 'UPDATE `#__plugins` SET `published` = ' . $this->db->quote( 1 )
								. ' WHERE `element` = ' . $this->db->quote( $element )
								. ' AND `folder` = ' . $this->db->quote( $plugin->attributes('group') );
						$this->db->setQuery($query);
						$this->db->query();
					}
				}
			}
		}

		foreach ($result as $value)
		{
			if( !$value )
			{
				return false;
			}
		}

		return true;
	}

	/**
	 * Install default modules
	 */
	private function checkModules()
	{
		$result = array();

		if( $this->joomlaVersion >= '3.0' )
		{
			$modules = $this->manifest->modules;

			if( $modules instanceof SimpleXMLElement && count( $modules ) )
			{
				foreach( $modules->module as $module )
				{
					$modDir = $this->installPath.DIRECTORY_SEPARATOR.'modules'.DIRECTORY_SEPARATOR.$module->attributes()->module;

					if( JFolder::exists($modDir) )
					{
						$jinstaller = new JInstaller;
						$result[]	= $jinstaller->install($modDir);
					}
				}
			}
		}
		elseif( $this->joomlaVersion > '1.5' && $this->joomlaVersion < '3.0' )
		{
			$modules = $this->manifest->modules;

			if( $modules instanceof JXMLElement && count($modules) )
			{
				foreach ($modules->module as $module)
				{
					$modDir = $this->installPath.DIRECTORY_SEPARATOR.'modules'.DIRECTORY_SEPARATOR.$module->getAttribute('module');

					if( JFolder::exists($modDir) )
					{
						$jinstaller = new JInstaller;
						$result[]	= $jinstaller->install($modDir);
					}
				}
			}
		}
		else
		{
			$modules = $this->jinstaller->_adapters['component']->manifest->getElementByPath('modules');

			if( $modules instanceof JSimpleXMLElement && count($modules->children()) )
			{
				foreach ($modules->children() as $module)
				{
					$modDir = $this->installPath.'/modules/'.$module->attributes('module');

					if( JFolder::exists($modDir) )
					{
						$jinstaller = new JInstaller;
						$result[]	= $jinstaller->install($modDir);
					}
				}
			}
		}

		foreach ($result as $value)
		{
			if( !$value )
			{
				return false;
			}
		}

		return true;
	}

	private function checkMisc()
	{
		return $this->getDatabaseUpdate()->misc();
	}

	private function checkFree()
	{
		if( $this->package !== 'paid' )
		{
			// Delete all the themes
			// $themes = array( 'bubbo', 'cleo', 'elego', 'freso', 'minimo' );

			// foreach( $themes as $theme )
			// {
			// 	$path = JPATH_ROOT . '/components/com_komento/themes/' . $theme;

			// 	JFolder::delete( $path );
			// }
		}
	}

	private function checkConfigParam()
	{
		return $this->getDatabaseUpdate()->updateConfigParam();
	}

	private function checkACLParam()
	{
		return $this->getDatabaseUpdate()->updateACLParam();
	}

	private function extract( $archivename, $extractdir )
	{
		$archivename= JPath::clean( $archivename );
		$extractdir	= JPath::clean( $extractdir );

		return JArchive::extract( $archivename, $extractdir );
	}

	private function getDatabaseUpdate()
	{
		static $class = null;

		if( is_null( $class ) )
		{
			$class = new KomentoDatabaseUpdate( $this );
		}

		return $class;
	}

	/**
	 * Install the foundry folder
	 */
	private function checkMedia()
	{
		$foundryVersion = '3.1';

		// Copy media/com_komento
		// Overwrite all
		$mediaSource	= $this->installPath . DIRECTORY_SEPARATOR . 'media' . DIRECTORY_SEPARATOR . 'com_komento';
		$mediaDestina	= JPATH_ROOT . DIRECTORY_SEPARATOR . 'media' . DIRECTORY_SEPARATOR . 'com_komento';

		if( !JFolder::copy($mediaSource, $mediaDestina, '', true) )
		{
			return false;
		}

		// Copy media/foundry
		// Overwrite only if version is newer
		$mediaSource	= $this->installPath . DIRECTORY_SEPARATOR . 'media' . DIRECTORY_SEPARATOR . 'foundry';
		$mediaDestina	= JPATH_ROOT . DIRECTORY_SEPARATOR . 'media' . DIRECTORY_SEPARATOR . 'foundry';
		$overwrite		= false;
		$incomingVersion = '';
		$installedVersion = '';

		if(! JFolder::exists( $mediaDestina ) )
		{
			// foundry folder not found. just copy foundry folde without need to check.
			if (! JFolder::copy($mediaSource, $mediaDestina, '', true) )
			{
				return false;
			}

			return true;
		}

		if(	!($incomingVersion = (string) JFile::read( $mediaSource . DIRECTORY_SEPARATOR . $foundryVersion . DIRECTORY_SEPARATOR . 'version' )) )
		{
			// can't read the version number
			return false;
		}

		if( !JFile::exists($mediaDestina . DIRECTORY_SEPARATOR . $foundryVersion . DIRECTORY_SEPARATOR . 'version')
			|| !($installedVersion = (string) JFile::read( $mediaDestina . DIRECTORY_SEPARATOR . $foundryVersion . DIRECTORY_SEPARATOR . 'version' )) )
		{
			// foundry version not exists or need upgrade
			$overwrite = true;
		}

		$incomingVersion	= preg_replace('/[^a-zA-Z0-9\.]/i', '', $incomingVersion);
		$installedVersion	= preg_replace('/[^a-zA-Z0-9\.]/i', '', $installedVersion);

		if( $overwrite || version_compare($incomingVersion, $installedVersion) > 0 )
		{
			if( !JFolder::copy($mediaSource . DIRECTORY_SEPARATOR . $foundryVersion, $mediaDestina . DIRECTORY_SEPARATOR . $foundryVersion, '', true) )
			{
				return false;
			}
		}

		return true;
	}

	public static function getJoomlaVersion()
	{
		$jVerArr	= explode('.', JVERSION);
		$jVersion	= $jVerArr[0] . '.' . $jVerArr[1];

		return $jVersion;
	}

	public static function getRegistry( $contents = '', $type = 'ini' )
	{
		static $loaded = false;

		if( !$loaded )
		{
			require_once( JPATH_ROOT . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_komento' . DIRECTORY_SEPARATOR . 'classes' . DIRECTORY_SEPARATOR . 'registry.php' );
		}

		$registry = new KomentoRegistry( $contents, $type );

		return $registry;
	}

	public static function getJSON()
	{
		static $json = null;

		if( is_null( $json ) )
		{
			require_once( JPATH_ROOT . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_komento' . DIRECTORY_SEPARATOR . 'classes' . DIRECTORY_SEPARATOR . 'json.php' );

			$json = new KomentoJson();
		}

		return $json;
	}

	private function setMessage( $msg, $type )
	{
		$this->messages[] = array( 'type' => strtolower($type), 'message' => $msg );
	}

	public function getMessages()
	{
		return $this->messages;
	}

	public function setErrorLog( $file, $line )
	{
		$msg = 'Error at function: ' . $file . ' line ' . $line;
		$this->setMessage( $msg, 'warning' );
	}
}


class KomentoDatabaseUpdate
{
	protected $db	= null;
	protected $installer = null;

	public function __construct( $installer )
	{
		$this->db	= KomentoDBHelper::getDBO();

		$this->installer = $installer;
	}

	public function update()
	{
		// Reset and Alter Activities Table
		// Added in #[3c2d4f952a2bac28bb5da5aaa6d11e8576a3a2db], 18 April 2012
		if( $this->isColumnExists( '#__komento_activities', 'title' ) )
		{
			$query = 'ALTER TABLE `#__komento_activities` DROP COLUMN `title`';
			$this->db->setQuery( $query );
			if( !$this->db->query() )
			{
				$this->installer->setErrorLog( __FILE__, __LINE__ );
				return false;
			}
		}
		if( $this->isColumnExists( '#__komento_activities', 'url' ) )
		{
			$query = 'ALTER TABLE `#__komento_activities` DROP COLUMN `url`';
			$this->db->setQuery( $query );
			if( !$this->db->query() )
			{
				$this->installer->setErrorLog( __FILE__, __LINE__ );
				return false;
			}
		}
		if( $this->isColumnExists( '#__komento_activities', 'component' ) )
		{
			$query = 'ALTER TABLE `#__komento_activities` DROP COLUMN `component`';
			$this->db->setQuery( $query );
			if( !$this->db->query() )
			{
				$this->installer->setErrorLog( __FILE__, __LINE__ );
				return false;
			}
		}
		if( $this->isColumnExists( '#__komento_activities', 'cid' ) )
		{
			$query = 'DELETE FROM `#__komento_activities`';
			$this->db->setQuery( $query );
			if( !$this->db->query() )
			{
				$this->installer->setErrorLog( __FILE__, __LINE__ );
				return false;
			}

			$query = 'ALTER TABLE `#__komento_activities` DROP COLUMN `cid`';
			$this->db->setQuery( $query );
			if( !$this->db->query() )
			{
				$this->installer->setErrorLog( __FILE__, __LINE__ );
				return false;
			}
		}
		if( !$this->isColumnExists( '#__komento_activities', 'comment_id' ) )
		{
			$query = 'ALTER TABLE  `#__komento_activities` ADD `comment_id` BIGINT(20) NOT NULL AFTER `type`';
			$this->db->setQuery( $query );
			if( !$this->db->query() )
			{
				$this->installer->setErrorLog( __FILE__, __LINE__ );
				return false;
			}
		}

		// Fix reports menu link
		// Added in #[87179de3ebc4c226470d1d8f6d83e35daaa715c6], 16 May 2012
		if( KomentoInstaller::getJoomlaVersion() >= '1.6' )
		{
			$query = 'UPDATE `#__menu` SET `link` = ' . $this->db->quote( 'index.php?option=com_komento&view=reports' ) . ' WHERE `title` = ' . $this->db->quote( 'COM_KOMENTO_MENU_REPORTS' );
			$this->db->setQuery( $query );
			if( !$this->db->query() )
			{
				$this->installer->setErrorLog( __FILE__, __LINE__ );
				return false;
			}
		}
		else
		{
			$query = 'UPDATE `#__components` SET `admin_menu_link` = ' . $this->db->quote( 'option=com_komento&view=reports' ) . ' WHERE `name` = ' . $this->db->quote( 'COM_KOMENTO_MENU_REPORTS' );
			$this->db->setQuery( $query );
			if( !$this->db->query() )
			{
				$this->installer->setErrorLog( __FILE__, __LINE__ );
				return false;
			}
		}

		// Add published column to subscription table
		// Added in #[04b86b4c3feb30bda179a83873e7dcf165dfa668], 23 May 2012
		if( !$this->isColumnExists( '#__komento_subscription', 'published' ) )
		{
			$query = 'ALTER TABLE  `#__komento_subscription` ADD `published` tinyint(1) NOT NULL DEFAULT 0 AFTER `created`';
			$this->db->setQuery( $query );
			if( !$this->db->query() )
			{
				$this->installer->setErrorLog( __FILE__, __LINE__ );
				return false;
			}
		}

		// Add hashkeys table
		// Added in #[7daebcea665a143118dc8a6b3b88ee7b03f6b3a7], 19 June 2012
		if( !$this->isTableExists( '#__komento_hashkeys' ) )
		{
			$query = 'CREATE TABLE IF NOT EXISTS `#__komento_hashkeys` (
				`id` bigint(11) NOT NULL auto_increment,
				`uid` bigint(11) NOT NULL,
				`type` varchar(255) NOT NULL,
				`key` text NOT NULL,
				PRIMARY KEY  (`id`),
				KEY `uid` (`uid`),
				KEY `type` (`type`)
				) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1';

			$this->db->setQuery( $query );
			if( !$this->db->query() )
			{
				$this->installer->setErrorLog( __FILE__, __LINE__ );
				return false;
			}
		}

		// Add uploads table
		// Added in #[0df9f868f227d2db74c16cd9eba0a7e89e882ab7], 21 June 2012
		if( !$this->isTableExists( '#__komento_uploads' ) )
		{
			$query = 'CREATE TABLE IF NOT EXISTS `#__komento_uploads` (
				`id` int(11) NOT NULL AUTO_INCREMENT,
				`filename` text NOT NULL,
				`hashname` text NOT NULL,
				`path` text NULL,
				`created` datetime NOT NULL,
				`created_by` bigint(20) unsigned DEFAULT \'0\',
				`published` tinyint(1) NOT NULL,
				`mime` text NOT NULL,
				`size` text NOT NULL,
				PRIMARY KEY (`id`)
				) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1';

			$this->db->setQuery( $query );
			if( !$this->db->query() )
			{
				$this->installer->setErrorLog( __FILE__, __LINE__ );
				return false;
			}
		}

		// Add UID column to uploads table
		// Added in #[d1fdeaa0f5ab7dd1874cd88a38bdc869e71c7aa0], 25 June 2012
		if( !$this->isColumnExists( '#__komento_uploads', 'uid' ) )
		{
			$query = 'ALTER TABLE  `#__komento_uploads` ADD `uid` int(11) NULL AFTER `id`';
			$this->db->setQuery( $query );
			if( !$this->db->query() )
			{
				$this->installer->setErrorLog( __FILE__, __LINE__ );
				return false;
			}
		}

		// Add Komento Actions comment_id key to actions table
		// Added in #[eb86a6ced927a7f8483adf71515272d264618b58], 5 December 2012
		if( !$this->isIndexKeyExists( '#__komento_actions', 'komento_actions_comment_id' ) )
		{
			$query = 'ALTER TABLE `#__komento_actions` ADD INDEX `komento_actions_comment_id` (`comment_id`)';
			$this->db->setQuery( $query );
			if( !$this->db->query() )
			{
				$this->installer->setErrorLog( __FILE__, __LINE__ );
				return false;
			}
		}

		// Add Komento Comments depth column
		// Added in #[acfda1dd752c7a7daf496d381f54b4c93e344983], 10 December 2012
		if( !$this->isColumnExists( '#__komento_comments', 'depth' ) )
		{
			$query = 'ALTER TABLE  `#__komento_comments` ADD `depth` INT(11) NOT NULL DEFAULT \'0\' AFTER `rgt`';
			$this->db->setQuery( $query );
			if( !$this->db->query() )
			{
				$this->installer->setErrorLog( __FILE__, __LINE__ );
				return false;
			}
		}

		// Add Komento configs for backend comments listing configuration
		// Added in #[10224b46e3a0f4453fc8d9778c2dfb0ac87ab01c], 12 December 2012
		$this->db->setQuery( 'SELECT COUNT(1) FROM `#__komento_configs` WHERE `component` = "com_komento_comments_columns"' );
		if( !$this->db->loadResult() )
		{
			$registry = KomentoInstaller::getRegistry();
			$registry->set( 'column_comment', 1 );
			$registry->set( 'column_published', 1 );
			$registry->set( 'column_sticked', 1 );
			$registry->set( 'column_link', 1 );
			$registry->set( 'column_edit', 1 );
			$registry->set( 'column_component', 1 );
			$registry->set( 'column_article', 1 );
			$registry->set( 'column_date', 1 );
			$registry->set( 'column_author', 1 );
			$registry->set( 'column_id', 1 );
			$query = "INSERT INTO `#__komento_configs` VALUES('com_komento_comments_columns', '" . $registry->toString( 'json' ) . "')";
			$this->db->setQuery( $query );
			$this->db->query();
		}

		// Drop komento_frontend index key
		// Added in #[070cf32eca54802fbc7d2593b5c1ef880d7a5644], 2 Jan 2013
		if( $this->isIndexKeyExists( '#__komento_comments', 'komento_frontend' ) )
		{
			$query = 'ALTER TABLE `#__komento_comments` DROP INDEX `komento_frontend`';
			$this->db->setQuery( $query );
			if( !$this->db->query() )
			{
				$this->installer->setErrorLog( __FILE__, __LINE__ );
				return false;
			}
		}

		// Drop komento_frontend_threaded index key
		// Added in #[070cf32eca54802fbc7d2593b5c1ef880d7a5644], 2 Jan 2013
		if( $this->isIndexKeyExists( '#__komento_comments', 'komento_frontend_threaded' ) )
		{
			$query = 'ALTER TABLE `#__komento_comments` DROP INDEX `komento_frontend_threaded`';
			$this->db->setQuery( $query );
			if( !$this->db->query() )
			{
				$this->installer->setErrorLog( __FILE__, __LINE__ );
				return false;
			}
		}

		// Drop komento_component index key
		// Added in #[070cf32eca54802fbc7d2593b5c1ef880d7a5644], 2 Jan 2013
		if( $this->isIndexKeyExists( '#__komento_comments', 'komento_component' ) )
		{
			$query = 'ALTER TABLE `#__komento_comments` DROP INDEX `komento_component`';
			$this->db->setQuery( $query );
			if( !$this->db->query() )
			{
				$this->installer->setErrorLog( __FILE__, __LINE__ );
				return false;
			}
		}

		// Drop komento_cid index key
		// Added in #[070cf32eca54802fbc7d2593b5c1ef880d7a5644], 2 Jan 2013
		if( $this->isIndexKeyExists( '#__komento_comments', 'komento_cid' ) )
		{
			$query = 'ALTER TABLE `#__komento_comments` DROP INDEX `komento_cid`';
			$this->db->setQuery( $query );
			if( !$this->db->query() )
			{
				$this->installer->setErrorLog( __FILE__, __LINE__ );
				return false;
			}
		}

		// Drop komento_parent_id index key
		// Added in #[070cf32eca54802fbc7d2593b5c1ef880d7a5644], 2 Jan 2013
		if( $this->isIndexKeyExists( '#__komento_comments', 'komento_parent_id' ) )
		{
			$query = 'ALTER TABLE `#__komento_comments` DROP INDEX `komento_parent_id`';
			$this->db->setQuery( $query );
			if( !$this->db->query() )
			{
				$this->installer->setErrorLog( __FILE__, __LINE__ );
				return false;
			}
		}

		// Drop komento_lft index key
		// Added in #[070cf32eca54802fbc7d2593b5c1ef880d7a5644], 2 Jan 2013
		if( $this->isIndexKeyExists( '#__komento_comments', 'komento_lft' ) )
		{
			$query = 'ALTER TABLE `#__komento_comments` DROP INDEX `komento_lft`';
			$this->db->setQuery( $query );
			if( !$this->db->query() )
			{
				$this->installer->setErrorLog( __FILE__, __LINE__ );
				return false;
			}
		}

		// Drop komento_rgt index key
		// Added in #[],
		if( $this->isIndexKeyExists( '#__komento_comments', 'komento_rgt' ) )
		{
			$query = 'ALTER TABLE `#__komento_comments` DROP INDEX `komento_rgt`';
			$this->db->setQuery( $query );
			if( !$this->db->query() )
			{
				$this->installer->setErrorLog( __FILE__, __LINE__ );
				return false;
			}
		}

		// Drop komento_backend index key
		// Added in #[070cf32eca54802fbc7d2593b5c1ef880d7a5644], 2 Jan 2013
		if( $this->isIndexKeyExists( '#__komento_comments', 'komento_backend' ) )
		{
			$query = 'ALTER TABLE `#__komento_comments` DROP INDEX `komento_backend`';
			$this->db->setQuery( $query );
			if( !$this->db->query() )
			{
				$this->installer->setErrorLog( __FILE__, __LINE__ );
				return false;
			}
		}

		// Add komento_module_comments index key
		// Added in #[070cf32eca54802fbc7d2593b5c1ef880d7a5644], 2 Jan 2013
		if( !$this->isIndexKeyExists( '#__komento_comments', 'komento_module_comments' ) )
		{
			$query = 'ALTER TABLE `#__komento_comments` ADD INDEX `komento_module_comments` (`component`, `published`, `created`)';
			$this->db->setQuery( $query );
			if( !$this->db->query() )
			{
				$this->installer->setErrorLog( __FILE__, __LINE__ );
				return false;
			}
		}

		// Add komento_threaded index key
		// Added in #[070cf32eca54802fbc7d2593b5c1ef880d7a5644], 2 Jan 2013
		if( !$this->isIndexKeyExists( '#__komento_comments', 'komento_threaded' ) )
		{
			$query = 'ALTER TABLE `#__komento_comments` ADD INDEX `komento_threaded` (`component`, `cid`, `published`, `lft`)';
			$this->db->setQuery( $query );
			if( !$this->db->query() )
			{
				$this->installer->setErrorLog( __FILE__, __LINE__ );
				return false;
			}
		}

		// Add komento_threaded_reverse index key
		// Added in #[070cf32eca54802fbc7d2593b5c1ef880d7a5644], 2 Jan 2013
		if( !$this->isIndexKeyExists( '#__komento_comments', 'komento_threaded_reverse' ) )
		{
			$query = 'ALTER TABLE `#__komento_comments` ADD INDEX `komento_threaded_reverse` (`component`, `cid`, `published`, `rgt`)';
			$this->db->setQuery( $query );
			if( !$this->db->query() )
			{
				$this->installer->setErrorLog( __FILE__, __LINE__ );
				return false;
			}
		}

		// Add komento_backend index key
		// Added in #[070cf32eca54802fbc7d2593b5c1ef880d7a5644], 2 Jan 2013
		if( !$this->isIndexKeyExists( '#__komento_comments', 'komento_backend' ) )
		{
			$query = 'ALTER TABLE `#__komento_comments` ADD INDEX `komento_backend` (`parent_id`, `created`)';
			$this->db->setQuery( $query );
			if( !$this->db->query() )
			{
				$this->installer->setErrorLog( __FILE__, __LINE__ );
				return false;
			}
		}

		// Add `type` column to #__komento_mailq
		// Added in #[be308533026b83c0aa27afc35ad73939289dc676], 21 Jan 2013
		if( !$this->isColumnExists( '#__komento_mailq', 'type' ) )
		{
			$query = 'ALTER TABLE `#__komento_mailq` ADD COLUMN `type` varchar(10) NOT NULL DEFAULT \'text\' AFTER `created`';
			$this->db->setQuery( $query );
			if( !$this->db->query() )
			{
				$this->installer->setErrorLog( __FILE__, __LINE__ );
				return false;
			}
		}

		// Add Komento configs for backend pending listing configuration
		// Added in #[8a23f782d0ab42e23bff79cd58ca61614d1e5f8e], 19 May 2013
		$this->db->setQuery( 'SELECT COUNT(1) FROM `#__komento_configs` WHERE `component` = "com_komento_pending_columns"' );
		if( !$this->db->loadResult() )
		{
			$registry = KomentoInstaller::getRegistry();
			$registry->set( 'column_comment', 1 );
			$registry->set( 'column_published', 1 );
			$registry->set( 'column_link', 1 );
			$registry->set( 'column_edit', 1 );
			$registry->set( 'column_component', 1 );
			$registry->set( 'column_article', 1 );
			$registry->set( 'column_date', 1 );
			$registry->set( 'column_author', 1 );
			$registry->set( 'column_id', 1 );
			$query = "INSERT INTO `#__komento_configs` VALUES('com_komento_pending_columns', '" . $registry->toString( 'json' ) . "')";
			$this->db->setQuery( $query );
			$this->db->query();
		}

		// Add Komento configs for backend report listing configuration
		// Added in #[244ae7ea50992ee2a5cbe5fd08436e338ffa4355], 19 May 2013
		$this->db->setQuery( 'SELECT COUNT(1) FROM `#__komento_configs` WHERE `component` = "com_komento_reports_columns"' );
		if( !$this->db->loadResult() )
		{
			$registry = KomentoInstaller::getRegistry();
			$registry->set( 'column_comment', 1 );
			$registry->set( 'column_published', 1 );
			$registry->set( 'column_link', 1 );
			$registry->set( 'column_edit', 1 );
			$registry->set( 'column_component', 1 );
			$registry->set( 'column_article', 1 );
			$registry->set( 'column_date', 1 );
			$registry->set( 'column_author', 1 );
			$registry->set( 'column_id', 1 );
			$query = "INSERT INTO `#__komento_configs` VALUES('com_komento_reports_columns', '" . $registry->toString( 'json' ) . "')";
			$this->db->setQuery( $query );
			$this->db->query();
		}

		// Add params column in comments table
		// Added in #[650c1dbc65649e2c4ae8c95f10fc139827c0245c], 12 June 2013
		if( !$this->isColumnExists( '#__komento_comments', 'params' ) )
		{
			$query = 'ALTER TABLE `#__komento_comments` ADD COLUMN `params` text';
			$this->db->setQuery( $query );
			if( !$this->db->query() )
			{
				$this->installer->setErrorLog( __FILE__, __LINE__ );
				return false;
			}
		}

		// Add state column in hashkey table
		// Added in #[6299cb4d085dbc5a8a35b08f3c9be1440020709f], 2 September 2013
		if( !$this->isColumnExists( '#__komento_hashkeys', 'state' ) )
		{
			$query = 'ALTER TABLE `#__komento_hashkeys` ADD COLUMN `state` tinyint(1) NOT NULL DEFAULT 0';
			$this->db->setQuery( $query );
			if( !$this->db->query() )
			{
				$this->installer->setErrorLog( __FILE__, __LINE__ );
				return false;
			}
		}

		// Add ratings column in comments table
		if( !$this->isColumnExists( '#__komento_comments', 'ratings' ) )
		{
			$query = 'ALTER TABLE  `#__komento_comments` ADD `ratings` int(11) unsigned default 0';
			$this->db->setQuery( $query );
			if( !$this->db->query() )
			{
				$this->installer->setErrorLog( __FILE__, __LINE__ );
				return false;
			}
		}

		return true;
	}

	public function misc()
	{
		// Fix for JomSocial 2.8
		if( $this->isTableExists( '#__community_activities' ) )
		{
			$query  = 'UPDATE `#__community_activities` SET `app` = ' . $this->db->quote( 'komento' ) . ' WHERE `app` = ' . $this->db->quote( 'com_komento' );
			$this->db->setQuery( $query );

			if( !$this->db->query() )
			{
				$this->installer->setErrorLog( __FILE, __LINE__ );
			}
		}

		return true;
	}

	public function updateConfigParam()
	{
		$query = 'SELECT * FROM ' . $this->db->nameQuote( '#__komento_configs' );

		$this->db->setQuery( $query );

		$result = $this->db->loadObjectList();

		foreach( $result as $row )
		{
			if( ( substr( $row->params, 0, 1 ) != '{' ) && ( substr( $row->params, -1, 1 ) != '}' ) )
			{
				$registry = KomentoInstaller::getRegistry( $row->params, 'ini' );

				$row->params = $registry->toString( 'json' );

				$this->db->updateObject( '#__komento_configs', $row, 'component' );
			}
		}

		return true;
	}

	public function updateACLParam()
	{
		$query = 'SELECT * FROM ' . $this->db->nameQuote( '#__komento_acl' );

		$this->db->setQuery( $query );

		$result = $this->db->loadObjectList();

		foreach( $result as $row )
		{
			if( !empty( $row->rules ) )
			{
				$json = KomentoInstaller::getJSON();

				$rules = $json->decode( $row->rules );

				// foreach( $rules as $rule )
				// {
				// 	// Migrate over some old key
				// 	if( $rule == 'delete_attachment' )
				// 	{
				// 		$rule = 'delete_own_attachment';
				// 	}
				// 	if( $rule == 'stick_comment' )
				// 	{
				// 		$rule = 'stick_all_comment';
				// 	}

				// 	$data->$rule = $rule->value ? true : false;
				// }

				$newRule = 'read_others_comment';
				$rules->$newRule = true;
				
				$row->rules = $json->encode( $rules );

				$this->db->updateObject( '#__komento_acl', $row, 'id' );
			}
		}

		return true;
	}

	private function isTableExists( $tableName )
	{
		$query	= 'SHOW TABLES LIKE ' . $this->db->quote($tableName);
		$this->db->setQuery( $query );

		return (boolean) $this->db->loadResult();
	}

	private function isColumnExists( $tableName, $columnName )
	{
		$query	= 'SHOW FIELDS FROM ' . $this->db->nameQuote( $tableName );
		$this->db->setQuery( $query );

		$fields	= $this->db->loadObjectList();

		$result = array();

		foreach( $fields as $field )
		{
			$result[ $field->Field ]	= preg_replace( '/[(0-9)]/' , '' , $field->Type );
		}

		if( array_key_exists($columnName, $result) )
		{
			return true;
		}

		return false;
	}

	private function isIndexKeyExists( $tableName, $indexName )
	{
		$query	= 'SHOW INDEX FROM ' . $this->db->nameQuote( $tableName );
		$this->db->setQuery( $query );
		$indexes	= $this->db->loadObjectList();

		$result = array();

		foreach( $indexes as $index )
		{
			$result[ $index->Key_name ]	= preg_replace( '/[(0-9)]/' , '' , $index->Column_name );
		}

		if( array_key_exists($indexName, $result) )
		{
			return true;
		}

		return false;
	}
}

class KomentoDBHelper
{
	public static $helper = null;

	public static function getDBO()
	{
		if( is_null( self::$helper ) )
		{
			$version    = KomentoInstaller::getJoomlaVersion();
			$className	= 'KomentoDBJoomla15';

			if( $version >= '2.5' )
			{
				$className 	= 'KomentoDBJoomla30';
			}

			self::$helper   = new $className();
		}

		return self::$helper;

	}
}


class KomentoDBJoomla15
{
	public $db 		= null;

	public function __construct()
	{
		$this->db	= JFactory::getDBO();
	}

	public function loadColumn()
	{
		return $this->db->loadResultArray();
	}

	public function __call( $method , $args )
	{
		$refArray	= array();

		if( $args )
		{
			foreach( $args as &$arg )
			{
				$refArray[]	=& $arg;
			}
		}

		return call_user_func_array( array( $this->db , $method ) , $refArray );
	}
}


class KomentoDBJoomla30
{
	public $db 		= null;

	public function __construct()
	{
		$this->db	= JFactory::getDBO();
	}

	public function loadResultArray()
	{
		return $this->db->loadColumn();
	}

	public function nameQuote( $str )
	{
		return $this->db->quoteName( $str );
	}

	public function __call( $method , $args )
	{
		$refArray	= array();

		if( $args )
		{
			foreach( $args as &$arg )
			{
				$refArray[]	=& $arg;
			}
		}

		return call_user_func_array( array( $this->db , $method ) , $refArray );
	}
}

class KomentoMenuMaintenance
{
	static function removeAdminMenu()
	{
		$db = KomentoDBHelper::getDBO();
		$query  = 'DELETE FROM ' . $db->nameQuote( '#__menu' ) . ' WHERE ' . $db->nameQuote( 'link' ) . ' LIKE ' . $db->quote( '%com_komento%' ) . ' AND ' . $db->nameQuote( 'client_id' ) . ' = ' . $db->quote( '1' );

		$db->setQuery($query);
		$db->query();
	}
}
