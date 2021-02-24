<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

ED::import('admin:/tables/table');

class DiscussRules extends EasyDiscussTable
{
	public $id = null;
	public $command	= null;
	public $title = null;
	public $description	= null;
	public $callback = null;
	public $created	= null;
	public $published = null;

	public function __construct(& $db)
	{
		parent::__construct('#__discuss_rules', 'id', $db);
	}

	/**
	 * Test if a specific rule / command already exists on the system.
	 *
	 * @access	public
	 * @param	string	$command	The command name to test for.
	 * @return	boolean	True if exists, false otherwise.
	 **/
	public function exists($command)
	{
		$db	= ED::db();

		$query = 'SELECT COUNT(1) FROM ' . $db->nameQuote($this->_tbl) . ' '
				. 'WHERE ' . $db->nameQuote('command') . '=' . $db->Quote($command);

		$db->setQuery($query);

		return $db->loadResult() > 0;
	}

	public function delete($pk = null)
	{
		// retrieve the badge title here
		$ruleTitle = $this->title;

		$state = parent::delete($pk);

		// log the current action into database.
		$actionlog = ED::actionlog();
		$actionlog->log('COM_ED_ACTIONLOGS_DELETED_RULES', 'badges', array(
			'ruleTitle' => JText::_($ruleTitle)
		));

		return $state;
	}	
}
