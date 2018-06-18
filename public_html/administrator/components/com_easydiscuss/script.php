<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 - 2018 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');

class com_EasyDiscussInstallerScript
{
	/**
	 * Triggered after the installation is completed
	 *
	 * @since	4.2.0
	 * @access	public
	 */
	public function postflight()
	{
		ob_start();
		include(__DIR__ . '/setup.html');

		$contents = ob_get_contents();
		ob_end_clean();

		echo $contents;
	}


	/**
	 * Triggered before the installation is complete
	 *
	 * @since	4.2
	 * @access	public
	 */
	public function preflight()
	{
		// During the preflight, we need to create a new installer file in the temporary folder
		$file = JPATH_ROOT . '/tmp/easydiscuss.installation';

		// Determines if the installation is a new installation or old installation.
		$obj = new stdClass();
		$obj->new = false;
		$obj->step = 1;
		$obj->status = 'installing';

		$contents = json_encode($obj);

		if (!JFile::exists($file)) {
			JFile::write($file, $contents);
		}

		// remove old constant.php if exits.
		$this->removeConstantFile();

		if ($this->isUpgradeFrom3x()) {

			// remove older helper files
			$this->removeOldHelpers();
		}

		// now let check the eb config
		$this->checkEDVersionConfig();

	}

	/**
	 * Responsible to check ed configs db version
	 *
	 * @since	4.2.0
	 * @access	public
	 */
	public function checkEDVersionConfig()
	{
		// if there is the config table but no dbversion, we know this upgrade is coming from pior 5.0. lets add on dbversion into config table.
		if ($this->isUpgradeFrom3x()) {

			// get current installed ed version.
			$xmlfile = JPATH_ROOT. '/administrator/components/com_easydiscuss/easydiscuss.xml';

			// set this to version prior 3.8.0 so that it will execute the db script from 3.9.0 as well incase
			// this upgrade is from very old version.
			$version = '3.1.0';

			if (JFile::exists($xmlfile)) {
				$contents = JFile::read($xmlfile);
				$parser = simplexml_load_string($contents);
				$version = $parser->xpath('version');
				$version = (string) $version[0];
			}

			$db = JFactory::getDBO();

			// ok, now we got the version. lets add this version into dbversion.
			$query = 'INSERT INTO ' . $db->quoteName('#__discuss_configs') . ' (`name`, `params`) VALUES';
			$query .= ' (' . $db->Quote('dbversion') . ',' . $db->Quote($version) . '),';
			$query .= ' (' . $db->Quote('scriptversion') . ',' . $db->Quote($version) . ')';

			$db->setQuery($query);
			$db->query();
		}
	}

	/**
	 * Determines if EasyDiscuss was upgraded from version 3.x
	 *
	 * @since	4.2.0
	 * @access	public
	 */
	private function isUpgradeFrom3x()
	{
		static $isUpgrade = null;

		if (is_null($isUpgrade)) {

			$isUpgrade = false;

			$db = JFactory::getDBO();

			$jConfig = JFactory::getConfig();
			$prefix = $jConfig->get('dbprefix');

			$query = "SHOW TABLES LIKE '" . $prefix . "discuss_configs%'";
			$db->setQuery($query);

			$result = $db->loadResult();

			if ($result) {
				// this is an upgrade. lets check if the upgrade from 3.x or not.
				$query = 'SELECT ' . $db->quoteName('params') . ' FROM ' . $db->quoteName('#__discuss_configs') . ' WHERE ' . $db->quoteName('name') . '=' . $db->Quote('dbversion');
				$db->setQuery($query);

				$exists = $db->loadResult();
				if (!$exists) {
					$isUpgrade = true;
				}
			}
		}

		return $isUpgrade;
	}

	/**
	 * Responsible to remove old constant.php file to avoid redefine of same constant error
	 *
	 * @since	4.2.0
	 * @access	public
	 */
	public function removeConstantFile()
	{
		$file = JPATH_ROOT. '/components/com_easydiscuss/constants.php';

		if (JFile::exists($file)) {
			JFile::delete($file);
		}
	}

	/**
	 * Responsible to remove old helper files
	 *
	 * @since	4.2.0
	 * @access	public
	 */
	public function removeOldHelpers()
	{
		$path = JPATH_ROOT . '/components/com_easydiscuss/helpers';

		if (JFolder::exists($path)) {
			JFolder::delete($path);
		}
	}

	/**
	 * Unpublish all modules and plugins
	 *
	 * @since	4.2.0
	 * @access	public
	 */
	public function uninstall()
	{
		$this->unpublishModules();
	}

	/**
	 * Unpublish EasyBlog modules from the site
	 *
	 * @since	4.2.0
	 * @access	public
	 */
	public function unpublishModules()
	{
		$db = JFactory::getDBO();

		$modules = JFolder::folders(JPATH_ROOT . '/modules', 'mod_easydiscuss*');

		$query = array();

		$modulesQuery = '';
		
		foreach ($modules as $module) {
			$modulesQuery .= ($modules) ? ',' . $db->Quote($module) : $db->Quote($module);
		}

		$query[] = 'UPDATE ' . $db->quoteName('#__modules') . ' SET ' . $db->quoteName('published') . '=' . $db->Quote('0');
		$query[] = 'WHERE ' . $db->quoteName('module') . ' IN (' . $modulesQuery . ')';
		$query[] = 'AND ' . $db->quoteName('published') . '=' . $db->Quote('1');

		$query = implode(' ', $query);
		$db->setQuery($query);
		
		$state = false;

		if (method_exists($db, 'query')) {
			return $db->query();
		}

		return $db->execute();
	}

	public function update()
	{
	}
}
