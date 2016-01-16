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

class DiscussPostsReference extends JTable
{
	public $id				= null;
	public $post_id			= null;
	public $reference_id	= null;
	public $extension		= null;

	public function __construct(& $db )
	{
		parent::__construct( '#__discuss_posts_references' , 'id' , $db );
	}

	public function loadByExtension( $referenceId , $extension )
	{
		$db		= DiscussHelper::getDBO();

		$query	= 'SELECT * FROM ' . $db->nameQuote( $this->_tbl ) . ' '
				. 'WHERE ' . $db->nameQuote( 'reference_id' ) . '=' . $db->Quote( $referenceId ) . ' '
				. 'AND ' . $db->nameQuote( 'extension' ) . '=' . $db->Quote( $extension );
		$db->setQuery( $query );

		$result	= $db->loadObject();

		if( !$result )
		{
			return false;
		}

		parent::bind( $result );

		return true;
	}
}
