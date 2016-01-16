<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 Stack Ideas Private Limited. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Restricted access');


class DiscussCustomfieldsRule extends JTable
{
	public $id				= null;
	public $field_id		= null;
	public $acl_id			= null;
	public $content_id		= null;
	public $content_type	= null;


	/**
	 * Constructor for this class.
	 *
	 * @return
	 * @param object $db
	 */
	public function __construct(& $db )
	{
		parent::__construct( '#__discuss_customfields_rule' , 'id' , $db );
	}

	public function load( $key = null, $customLoad = false )
	{
		if( $customLoad == false )
		{
			parent::load( $key );
		}

		$db = DiscussHelper::getDBO();

		$query = 'SELECT * FROM ' . $db->nameQuote( '#__discuss_customfields_rule' )
				. ' WHERE ' . $db->nameQuote( 'field_id' ) . '=' . $db->Quote( $key );
		$db->setQuery( $query );
		$results = $db->loadObjectList();

		return ( !$results )? false : $results;
	}
}
