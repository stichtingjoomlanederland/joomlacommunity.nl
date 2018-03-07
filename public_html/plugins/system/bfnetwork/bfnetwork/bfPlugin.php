<?php
/**
 * @package Blue Flame Network (bfNetwork)
 * @copyright Copyright (C) 2011, 2012, 2013, 2014, 2015, 2016, 2017 Blue Flame Digital Solutions Ltd. All rights reserved.
 * @license GNU General Public License version 3 or later
 * @link https://myJoomla.com/
 * @author Phil Taylor / Blue Flame Digital Solutions Limited.
 *
 * bfNetwork is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * bfNetwork is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this package.  If not, see http://www.gnu.org/licenses/
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

require_once 'bfLog.php';
require_once 'bfActivitylog.php';
require_once 'bfPreferences.php';

if (class_exists('JPlugin') && !class_exists('PlgSystemBfnetwork')) {
	class PlgSystemBfnetwork extends JPlugin
	{
		private $user;
		private $db;

		public function __construct($subject, $config = array())
		{
			$this->user = JFactory::getUser();
			$this->db = JFactory::getDbo();
			$prefs = new bfPreferences();
			$prefs->getPreferences(); // force creation of prefs file if needed

			parent::__construct($subject, $config);
		}

		public function ____EXAMPLE____($one, $options = array())
		{
			bfLog::log('____EXAMPLE____');

			bfActivitylog::getInstance()->log(
				$this->user->name,            //$who = 'not me!',
				$this->user->id,              //$who_id = 0,  User Id is not in
				'____EXAMPLE____',            //$what = 'dunno',
				'____EXAMPLE____',            //$where = 'er?',
				'0',                          //$where_id = 0,
				NULL,                         //$ip = NULL,
				NULL,                         //$useragent = NULL
				NULL,                         //$meta = NULL
				$options['action'],           //$options = NULL
				'alertname_something'         //$alertname = NULL
			);
		}

		public function onAfterInitialise()
		{
			bfLog::log(__METHOD__);
		}

		public function onAfterRender()
		{
			$prefs = new bfPreferences();
			$preferences = $prefs->getPreferences();

			if (property_exists($preferences, 'alerting_filewatchlist')) {
				$fileList = json_decode($preferences->alerting_filewatchlist);
			} else {
				$fileList = json_decode(json_encode($prefs->default_alerting_filewatchlist));
			}

			foreach ($fileList as $file) {

				if (!file_exists(JPATH_SITE . $file)) {
					continue;
				}

				$createLock = FALSE;

				$pathinfo = pathinfo($file);

				$md5LockFile = str_replace('//', '/', JPATH_SITE . $pathinfo['dirname'] . '/.myjoomla.' . basename($file) . '.md5');

				bfLog::log('LOCK FILE = ' . $md5LockFile);

				$currentMd5 = md5_file(JPATH_SITE . $file);
				bfLog::log('CURRENT MD5 = ' . $currentMd5);

				if (file_exists($md5LockFile)) {
					bfLog::log('LOCK FILE EXISTS = ' . $md5LockFile);
					$lastMd5 = file_get_contents($md5LockFile);
				} else {
					bfLog::log('LOCK FILE NOT EXISTS = ' . $md5LockFile);
					$lastMd5 = md5_file(JPATH_SITE . $file);

					bfLog::log("CREATING LOCK FILE with $currentMd5");
					// @ as not to upset crap servers :-(
					$res = @file_put_contents($md5LockFile, $currentMd5);
					bfLog::log('file_put_contents was = ' . $res);

					// if we could not write the lock file then bail!
					if (!file_exists($md5LockFile)){
						return;
					}
				}

				bfLog::log("COMPARING =   $lastMd5 !!! $currentMd5");
				if ($lastMd5 !== $currentMd5) {
					$createLock = TRUE;
					bfLog::log("ALERTING COMPARING !==  >   $lastMd5 !!! $currentMd5");
					bfActivitylog::getInstance()->log(
						'Someone',
						'-911',
						'modified file',
						$file,
						NULL,
						'system',
						NULL,
						NULL,
						NULL,
						'alerting_filewatchlist_alert'
					);
				}

				if ($createLock === TRUE) {
					bfLog::log("CREATING LOCK FILE with $currentMd5");
					// @ as not to upset crap servers :-(
					$res = @file_put_contents($md5LockFile, $currentMd5);
					bfLog::log('file_put_contents was = ' . $res);
				}
			}
			bfLog::log(__METHOD__);
		}

		public function onAfterRoute()
		{
			bfLog::log(__METHOD__);
		}

		public function onBeforeCompileHead()
		{
			bfLog::log(__METHOD__);
		}

		public function onBeforeRender()
		{
			bfLog::log(__METHOD__);
		}

		public function onCheckAnswer()
		{
			bfLog::log(__METHOD__);
		}

		public function onContentAfterDelete()
		{
			bfLog::log(__METHOD__);
		}

		public function onContentAfterDisplay()
		{
			bfLog::log(__METHOD__);
		}

		public function onContentAfterSave()
		{
			bfLog::log(__METHOD__);
		}

		public function onContentAfterTitle()
		{
			bfLog::log(__METHOD__);
		}

		public function onContentBeforeDelete()
		{
			bfLog::log(__METHOD__);
		}

		public function onContentBeforeDisplay()
		{
			bfLog::log(__METHOD__);
		}

		public function onContentBeforeSave()
		{
			bfLog::log(__METHOD__);
		}

		public function onContentChangeState()
		{
			bfLog::log(__METHOD__);
		}

		public function onContentPrepare()
		{
			bfLog::log(__METHOD__);
		}

		public function onContentPrepareData($form, $data)
		{
			bfLog::log(__METHOD__);
		}

		/**
		 * Alert when a users details are viewed
		 * Alert when someone views the Joomla Global Configuration
		 * Alert when someone saves the Joomla Global Configuration
		 * Alert when someone views options in any other extension
		 *
		 * @param $form
		 * @param $data
		 */
		public function onContentPrepareForm($form, $data)
		{
			bfLog::log(__METHOD__ . ' : ' . $_SERVER['REQUEST_METHOD'] . ' : ' . $form->getName());

			$jinput = JFactory::getApplication()->input;
			$option = $jinput->get('option', '', 'cmd');

			switch ($form->getName()) {
				case 'com_users.user':
					switch ($_SERVER['REQUEST_METHOD']) {
						case "GET":

							// a blank form, before creating a new user
							if ($data->id == 0) return;

							bfActivitylog::getInstance()->log(
								$this->user->name,
								$this->user->id,
								'viewed user details',
								$option,
								$this->getExtensionId($option),
								NULL,
								NULL,
								json_encode(array(
									'id' => $data->id,
									'username' => $data->username
								)),
								$form->getName(),
								'alerting_viewuser'
							);
							break;
						case "POST":
							break;
					}
					break;
				case 'com_config.application':

					switch ($_SERVER['REQUEST_METHOD']) {
						case "GET":
							bfActivitylog::getInstance()->log(
								$this->user->name,
								$this->user->id,
								'viewed Joomla Global Configuration page',
								'com_config',
								$this->getExtensionId($option),
								NULL,
								NULL,
								NULL,
								$form->getName(),
								'alerting_com_config_application_viewed'
							);
							break;

						case "POST":

							bfActivitylog::getInstance()->log(
								$this->user->name,
								$this->user->id,
								'saved Joomla Global Configuration page',
								'com_config',
								$this->getExtensionId($option),
								NULL,
								NULL,
								NULL,
								$form->getName(),
								'alerting_com_config_application_saved'
							);
							break;
					}
					break;
				case 'com_config.component':
					$com_name = $jinput->get('component', '', 'cmd');
					switch ($_SERVER['REQUEST_METHOD']) {
						case "GET":
							bfActivitylog::getInstance()->log(
								$this->user->name,
								$this->user->id,
								'viewed ' . $this->getExtensionName($com_name) . ' component Configuration page',
								'com_config',
								$this->getExtensionId($option),
								NULL,
								NULL,
								$com_name,
								$form->getName(),
								'alerting_com_config_component_viewed'
							);
							break;

						case "POST":
							define('_alerting_com_config_component_saved');
							bfActivitylog::getInstance()->log(
								$this->user->name,
								$this->user->id,
								'saved ' . $this->getExtensionName($com_name) . ' component Configuration page',
								'com_config',
								$this->getExtensionId($option),
								NULL,
								NULL,
								$com_name,
								$form->getName(),
								'alerting_com_config_component_saved'
							);
							break;
					}
					break;
			}
		}

		public function onContentSearch()
		{
			bfLog::log(__METHOD__);
		}

		public function onContentSearchAreas()
		{
			bfLog::log(__METHOD__);
		}

		public function onDisplay()
		{
			bfLog::log(__METHOD__);
		}

		public function onExtensionAfterInstall()
		{
			bfLog::log(__METHOD__);
		}

		/**
		 * Alert when someone saves options in any other extension
		 *
		 * @param $context
		 * @param $data
		 * @param $isNew
		 */
		public function onExtensionAfterSave($context, $data, $isNew)
		{
			bfLog::log(__METHOD__);

			if (defined('_alerting_com_config_component_saved')) return; // Joomla 3.5 fires this and onContentPrepareForm/POST

			/**
			 * Roksprocket Kills us :(
			 */
			if (!$data) return;
			if (!property_exists($data, 'element')) return;
			if (!$context) return;

			bfActivitylog::getInstance()->log(
				$this->user->name,
				$this->user->id,
				'saved ' . $this->getExtensionName($data->element) . ' configuration',
				'com_config',
				$this->getExtensionId('com_config'),
				NULL,
				NULL,
				json_encode($data),
				$context,
				'alerting_com_config_component_saved'
			);
		}

		public function onExtensionAfterUninstall()
		{
			bfLog::log(__METHOD__);
		}

		public function onExtensionAfterUpdate()
		{
			bfLog::log(__METHOD__);
		}

		function onExtensionBeforeInstall()
		{
			bfLog::log(__METHOD__);
		}

		public function onExtensionBeforeSave($context, $table, $isNew)
		{
			bfLog::log(__METHOD__);
		}

		public function onExtensionBeforeUninstall()
		{
			bfLog::log(__METHOD__);
		}

		public function onFinderAfterDelete()
		{
			bfLog::log(__METHOD__);
		}

		public function onFinderAfterSave()
		{
			bfLog::log(__METHOD__);
		}

		public function onFinderBeforeDelete()
		{
			bfLog::log(__METHOD__);
		}

		public function onFinderBeforeSave()
		{
			bfLog::log(__METHOD__);
		}

		public function onFinderCategoryChangeState()
		{
			bfLog::log(__METHOD__);
		}

		public function onFinderChangeState()
		{
			bfLog::log(__METHOD__);
		}

		public function onGetContent()
		{
			bfLog::log(__METHOD__);
		}

		public function onGetIcons()
		{
			bfLog::log(__METHOD__);
		}

		public function onGetInsertMethod()
		{
			bfLog::log(__METHOD__);
		}

		public function onGetWebServices()
		{
			bfLog::log(__METHOD__);
		}

		public function onInit()
		{
			bfLog::log(__METHOD__);
		}

		public function onInstallerAfterInstaller()
		{
			bfLog::log(__METHOD__);
		}

		public function onInstallerBeforeInstallation()
		{

			bfLog::log(__METHOD__);
		}

		public function onInstallerBeforeInstaller()
		{
			bfLog::log(__METHOD__);
		}

		public function onSave()
		{
			bfLog::log(__METHOD__);
		}

		public function onSearch()
		{
			bfLog::log(__METHOD__);
		}

		public function onSearchAreas()
		{
			bfLog::log(__METHOD__);
		}

		public function onSetContent()
		{
			bfLog::log(__METHOD__);
		}

		/**
		 * Alert when a Super Admin logs in to admin console
		 * Alert when a non-super admin attempts to login to admin
		 *
		 * @param $user - Note user's id is NOT in this array :-(
		 * @param $options
		 */
		public function onUserLogin($user, $options = array())
		{
			bfLog::log(__METHOD__);

			if (JFactory::getApplication()->getName() == 'administrator') {
				// Reload the user from the database
				$userFromDb = JFactory::getUser(JUserHelper::getUserId($user['username']));

				// Check the user is authorised to login here
				$result = (bool)$userFromDb->authorise($options['action']);

				$what = (FALSE === $result ? 'login attempt not authorised' : 'logged in');
				$alert = (FALSE === $result ? 'alerting_superadminfailedlogin' : 'alerting_superadminlogin');

				bfActivitylog::getInstance()->log(
					$userFromDb->name,
					$userFromDb->id,
					$what,
					'onUserLogin',
					'0',
					NULL,
					NULL,
					json_encode($options),
					$options['action'],
					$alert
				);
			}
		}

		/**
		 * Alert when a Super Admin logs out of the admin console
		 *
		 * @param $user
		 * @param $options
		 */
		public function onUserLogout($user, $options = array())
		{
			bfLog::log(__METHOD__);

			if (JFactory::getApplication()->getName() == 'administrator') {
				$userFromDb = JFactory::getUser(JUserHelper::getUserId($user['id']));

				bfActivitylog::getInstance()->log(
					$userFromDb->name,
					$user['id'],
					'logged out',
					'onUserLogout',
					'0',
					NULL,
					NULL,
					json_encode($options),
					($options['clientid'] == 1 ? 'core.logout.admin' : 'core.logout.site'),
					($options['clientid'] == 1 ? 'alerting_superadminlogout' : 'alerting_normaluserlogout')

				);
			}
		}

		/**
		 * After user group save event handler.
		 *
		 * @param $context
		 * @param $data
		 * @param $isNew
		 */
		public function onUserAfterSaveGroup($context, $data, $isNew)
		{
			bfLog::log(__METHOD__);
		}

		/**
		 * Before user group delete event handler.
		 *
		 * @param $group_properties
		 */
		public function onUserBeforeDeleteGroup($group_properties)
		{
			bfLog::log(__METHOD__);
		}

		/**
		 * After user group delete event handler.
		 *
		 * @param $group_properties
		 * @param $mysterious_arg
		 * @param $error
		 */
		public function onUserAfterDeleteGroup($group_properties, $mysterious_arg, $error)
		{
			bfLog::log(__METHOD__);
		}

		/**
		 * Alert when a new user is created
		 * Alert when a users details are saved
		 *
		 * @param $user
		 * @param $isNew
		 * @param $success
		 * @param $msg
		 */
		public function onUserAfterSave($user, $isNew, $success, $msg)
		{
			bfLog::log(__METHOD__);
			$jinput = JFactory::getApplication()->input;
			$com_name = $jinput->get('option', '', 'cmd');

			$loggedInUser = JFactory::getUser();

			if (TRUE === $isNew) {

				bfActivitylog::getInstance()->log(
					$loggedInUser->name,
					$loggedInUser->id,
					'created a new user',
					'onUserAfterSave',
					$this->getExtensionId($com_name),
					NULL,
					NULL,
					json_encode(array(
						'id' => $user['id'],
						'username' => $user['username']
					)),
					'com_users',
					'alerting_newuser'
				);
			} else {
				bfActivitylog::getInstance()->log(
					$loggedInUser->name,
					$loggedInUser->id,
					'updated user',
					'onUserAfterSave',
					$this->getExtensionId($com_name),
					NULL,
					NULL,
					json_encode(array(
						'id' => $user['id'],
						'username' => $user['username']
					)),
					'com_users',
					'alerting_saveuser'
				);
			}

		}

		/**
		 * After user delete event handler.
		 *
		 * @param $user
		 * @param $success
		 * @param $msg
		 */
		public function onUserAfterDelete($user, $success, $msg)
		{
			bfLog::log(__METHOD__);
		}

		/**
		 * Get the extension id from the db
		 *
		 * @param $element string
		 * @return int
		 */
		private function getExtensionId($element)
		{
			$sql = 'SELECT extension_id FROM #__extensions WHERE element = %s';
			$this->db->setQuery(sprintf($sql, $this->db->quote($element)));

			return (int)$this->db->loadResult();
		}

		/**
		 * convert com_something into a english string
		 *
		 * @param $com_name string
		 * @return string
		 */
		private function getExtensionName($com_name)
		{
			$lang = JFactory::getLanguage();
			$lang->load($com_name);
			$lang->load($com_name, JPATH_ADMINISTRATOR, 'en-GB', TRUE);
			$lang->load($com_name, JPATH_ADMINISTRATOR, NULL, TRUE);
			$lang->load($com_name, JPATH_ADMINISTRATOR . '/components/' . $com_name . '/', NULL, TRUE);
			$lang->load($com_name, JPATH_SITE, 'en-GB', TRUE);
			$lang->load($com_name, JPATH_SITE, NULL, TRUE);
			$lang->load($com_name, JPATH_SITE . '/components/' . $com_name . '/', NULL, TRUE);

			// convert some known crappiness :-(
			if ($com_name == 'com_jce') {
				$com_name = 'WF_ADMIN_TITLE';
			}

			return JText::_($com_name);
		}

		/**
		 * @use  $this->debug($user, $options);
		 */
		private function debug()
		{
			echo '<pre>';
			foreach (func_get_args() as $row) {
				var_dump($row);
			}
			echo '</pre>';
			die;
		}
	}
}