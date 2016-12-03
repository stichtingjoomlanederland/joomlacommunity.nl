<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

class EasyDiscussMaintenance extends EasyDiscuss
{
	public $nullDate	= '';
	public $nowDate		= '';
	public $hasRan		= false;

	/**
	 * Variable to hold error set by scripts
	 * @var String
	 */
	public $error;

	public function __construct()
	{
		// Initiate some expensive functions and store them in class variable
		$db = ED::db();

		$this->nullDate	= method_exists($db, 'getNullDate') ? $db->getNullDate() : '0000-00-00 00:00:00';
		$this->nullDate	= $db->Quote($this->nullDate);

		// Get the current date
		$date = ED::date();

		// Set the current date
		$this->nowDate	= $db->quote($date->toSql());
	}


	/**
	 * Get the available scripts and returns the script object in an array
	 *
	 * @access public
	 * @param  String    $from The version to pull from
	 * @return Array           Array of script objects
	 */
	public function getScripts($from = null)
	{
		$files = $this->getScriptFiles($from);

		$result = array();

		foreach ($files as $file) {
			$classname = $this->getScriptClassName($file);

			if ($classname === false) {
				continue;
			}

			$class = new $classname;

			$result[] = $class;
		}

		return $result;
	}

	/**
	 * Get the available script files and return the file path in an array
	 *
	 * @author Port from EasySocial
	 * @since  5.0
	 * @access public
	 * @param  String    $from The version to pull from
	 * @return Array           Array of script paths
	 */
	public function getScriptFiles($from = null, $operator = '>')
	{
		$files = array();

		// If from is empty, means it is a new installation, and new installation we do not want maintenance to run
		// Explicitly changed backend maintenance to pass in 'all' to get all the scripts instead.
		if (empty($from)) {
			return $files;
		}

		if ($from === 'all') {

			$phpFiles = JFolder::files(DISCUSS_ADMIN_UPDATES, '.php$', true, true);

			if ($phpFiles) {
				$files = array_merge($files, $phpFiles);
			}

		} else {
			$folders = JFolder::folders(DISCUSS_ADMIN_UPDATES);

			if (!empty($folders)) {
				foreach ($folders as $folder) {
					// We don't want things from "manual" folder
					if ($folder === 'manual') {
						continue;
					}

					// We cannot do $folder > $from because '1.2.8' > '1.2.15' is TRUE
					// We want > $from by default, NOT >= $from, unless manually specified through $operator
					if (version_compare($folder, $from, $operator)) {
						$fullpath = DISCUSS_ADMIN_UPDATES . '/' . $folder;

						$files = array_merge($files, JFolder::files($fullpath, '.php$', false, true));
					}
				}
			}
		}

		return $files;
	}

	/**
	 * Get the script class name
	 *
	 * @author Port from EasySocial
	 * @since  5.0
	 * @access public
	 * @param  String    $file The path of the script
	 * @return String          The class name of the script
	 */
	public function getScriptClassName($file)
	{
		static $classnames = array();

		if (!isset($classnames[$file]))
		{
			if (!JFile::exists($file))
			{
				$this->setError('Script file not found: ' . $file);
				$classnames[$file] = false;
				return false;
			}

			require_once($file);

			$filename = basename($file, '.php');

			$classname = 'EasyDiscussMaintenanceScript' . $filename;

			if (!class_exists($classname)) {
				$this->setError('Class not found: ' . $classname);
				$classnames[$file] = false;
				return false;
			}

			$classnames[$file] = $classname;
		}

		return $classnames[$file];
	}


	/**
	 * Wraooer function to execute the script
	 *
	 * @author Port from EasySocial
	 * @since  5.0
	 * @access public
	 * @param  String/SocialMaintenanceScript    $file The path of the script or the script object
	 * @return Boolean          State of the script execution result
	 */
	public function runScript($file)
	{
		$class = null;

		if (is_string($file))
		{
			$classname = $this->getScriptClassName($file);

			if ($classname === false)
			{
				return false;
			}

			$class = new $classname;
		}

		if (is_object($file))
		{
			$class = $file;
		}

		if (!$class instanceof EasyDiscussMaintenanceScript) {
			$this->setError('Class ' . $classname . ' is not an instance of EasyDiscussMaintenanceScript');
			return false;
		}

		$state = true;

		// Clear the error
		$this->error = null;

		try
		{
			$state = $class->main();
		}
		catch (Exception $e)
		{
			$this->setError($e->getMessage());
			return false;
		}

		if (!$state)
		{
			if ($class->hasError())
			{
				$this->setError($class->getError());
			}

			return false;
		}

		return true;
	}

	/**
	 * Get the script title
	 *
	 * @author Port from EasySocial
	 * @since  5.0
	 * @access public
	 * @param  String    $file The path of the script
	 * @return String          The title of the script
	 */
	public function getScriptTitle($file)
	{
		$classname = $this->getScriptClassName($file);

		if ($classname === false)
		{
			return false;
		}

		$vars 	= get_class_vars($classname);
		return JText::_($vars['title']);
	}

	/**
	 * Get the script description
	 *
	 * @author Jason Rey <jasonrey@stackideas.com>
	 * @since  1.2
	 * @access public
	 * @param  String    $file The path of the script
	 * @return String          The description of the script
	 */
	public function getScriptDescription($file)
	{
		$classname = $this->getScriptClassName($file);

		if ($classname === false)
		{
			return false;
		}

		$vars 	= get_class_vars($classname);
		return JText::_($vars['description']);
	}

	/**
	 * Checks if there are any error generated by executing the script
	 *
	 * @author Port from EasySocial
	 * @since  5.0
	 * @access public
	 * @return boolean   True if there is an error
	 */
	public function hasError()
	{
		return !empty($this->error);
	}

	/**
	 * Performs some maintenance here.
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function pruneNotifications()
	{
		$db = ED::db();
		$date = ED::date();

		$config = ED::config();
		$days = $config->get('notifications_history', 30);

		$query	= 'DELETE FROM ' . $db->nameQuote( '#__discuss_notifications' ) . ' '
				. 'WHERE ' . $db->nameQuote( 'created' ) . ' <= DATE_SUB( ' . $db->Quote( $date->toSql() ) . ' , INTERVAL ' . $days . ' DAY )';

		$db->setQuery($query);
		$db->query();

		return true;
	}

	public function run()
	{
		if( $this->hasRan )
		{
			return;
		}

		// 1. Lock new post (with proper lockdate)
		// 2. Lock older post (without lockdate)
		//     2.1. First fill empty lock date for posts with replies
		//         2.1.1. Find empty lock date
		//         2.1.2. Update emtpy lock date
		//         2.1.3. Lock expired posts
		//     2.2. Lastly fill empty lock date for posts without replies
		//         2.2.1. repeat sub-steps in 2.1
		//     2.3. Lock all expired posts.

		$config		= DiscussHelper::getConfig();

		$userLastRepliedInterval = (int) $config->get( 'main_daystolock_afterlastreplied' );
		$userPostCreatedInterval = (int) $config->get( 'main_daystolock_aftercreated' );

		if( empty($userLastRepliedInterval) && empty($userPostCreatedInterval) )
		{
			// both is zero. this also means the auto lock feature is not required.
			return;
		}

		if( $userLastRepliedInterval || $userPostCreatedInterval )
		{
			$this->lock();
			$this->hasRan = true;
		}


		if( $config->get( 'main_lock_newpost_only' ) )
		{
			return;
		}

		$db			= DiscussHelper::getDBO();

		$query	= ' UPDATE `#__discuss_posts` SET lockdate = CASE'
				. ' WHEN replied = ' . $this->nullDate . ' THEN DATE_ADD(created, INTERVAL ' . $userPostCreatedInterval . ' DAY)'
				. ' ELSE DATE_ADD(replied, INTERVAL ' . $userPostCreatedInterval . ' DAY) END'
				. ' WHERE parent_id = 0 AND islock = 0 AND published = 1'
				. ' AND lockdate = ' . $this->nullDate;
		$db->setQuery( $query );
		$db->query();

		// alternative
		/*
		if( $userLastRepliedInterval > 0 )
		{
			$query	= ' SELECT a.id, a.created, MAX(b.created) AS lastreplied FROM `#__discuss_posts` AS a'
					. ' LEFT JOIN `#__discuss_posts` AS b ON b.parent_id = a.id'
					. ' WHERE b.parent_id > 0 AND a.parent_id = 0 AND a.islock = 0 AND a.published = 1'
					. ' AND a.lockdate = ' . $this->nullDate
					. ' GROUP BY a.id';
			$db->setQuery( $query );
			$result = $db->loadObjectList();

			if( count($result) > 0 )
			{
				foreach ($result as $item)
				{
					$query	= ' UPDATE `#__discuss_posts`'
							. ' SET `lockdate` = DATE_ADD(' . $item->lastreplied . ', INTERVAL ' . $userPostCreatedInterval . ' DAY)'
							. ' WHERE `id` = ' . $db->quote( $item->id );
					$db->setQuery( $query );
					$db->query();
				}
			}
		}

		if( $userPostCreatedInterval > 0 )
		{
			$query	= ' SELECT `id` FROM `#__discuss_posts`'
					. ' WHERE `parent_id` = 0 AND `islock` = 0 AND `published` = 1'
					. ' AND `lockdate` = ' . $this->nullDate;
			$db->setQuery( $query );

			if( $db->loadResult() > 0 )
			{
				$query	= ' UPDATE `#__discuss_posts` SET `lockdate` = DATE_ADD(`created`, INTERVAL ' . $userPostCreatedInterval . ' DAY)'
						. ' WHERE `parent_id` = 0 AND `islock` = 0 AND `published` = 1'
						. ' AND `lockdate` = ' . $this->nullDate;
				$db->setQuery( $query );
				$db->query();
			}
		}
		*/
		// alternative end

		$this->lock();
	}

	public function lock()
	{
		$db		= DiscussHelper::getDBO();
		$query	= 'UPDATE `#__discuss_posts` SET `islock` = 1'
				. ' WHERE `islock` = 0'
				. ' AND `parent_id` = 0'
				. ' AND `published` = 1'
				. ' AND `lockdate` != ' . $this->nullDate
				. ' AND `lockdate` <= ' . $this->nowDate;
		$db->setQuery( $query );
		$db->query();
	}
}
