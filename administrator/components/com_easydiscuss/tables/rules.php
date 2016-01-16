<?php
/**
 * @package		EasyDiscuss
 * @copyright	Copyright (C) 2010 Stack Ideas Private Limited. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 *
 * EasyDiscuss is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */
defined('_JEXEC') or die('Restricted access');

class DiscussRules extends JTable
{
	public $id			= null;
	public $command		= null;
	public $title		= null;
	public $description	= null;

	/** Not implemented yet **/
	public $callback	= null;

	public $created		= null;
	public $published	= null;

	/**
	 * Constructor for this class.
	 *
	 * @return
	 * @param object $db
	 */
	public function __construct(& $db )
	{
		parent::__construct( '#__discuss_rules' , 'id' , $db );
	}

	/**
	 * Test if a specific rule / command already exists on the system.
	 *
	 * @access	public
	 * @param	string	$command	The command name to test for.
	 * @return	boolean	True if exists, false otherwise.
	 **/
	public function exists( $command )
	{
		$db		= DiscussHelper::getDBO();

		$query	= 'SELECT COUNT(1) FROM ' . $db->nameQuote( $this->_tbl ) . ' '
				. 'WHERE ' . $db->nameQuote( 'command' ) . '=' . $db->Quote( $command );

		$db->setQuery( $query );

		return $db->loadResult() > 0;
	}

}
