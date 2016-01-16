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

require_once dirname( __FILE__ ) . '/model.php';

class EasyDiscussModelPost_Types extends EasyDiscussModel
{
	function __construct()
	{
		parent::__construct();
	}

	public function getTypes()
	{
		$db		= DiscussHelper::getDBO();
		$query	= 'SELECT * FROM ' . $db->nameQuote( '#__discuss_post_types' )
				. ' WHERE ' . $db->nameQuote( 'published' ) . '=' . $db->quote( 1 )
				. ' ORDER BY ' . $db->nameQuote( 'title' ) . ' ASC';

		$db->setQuery( $query );
		$result	= $db->loadObjectList();

		return $result;
	}

	public function getTitle( $alias = null )
	{
		$db		= DiscussHelper::getDBO();
		$query	= 'SELECT `title` FROM ' . $db->nameQuote( '#__discuss_post_types' )
				. ' WHERE ' . $db->nameQuote( 'alias' ) . '=' . $db->quote( $alias )
				. ' AND ' . $db->nameQuote( 'published' ) . '=' . $db->quote( 1 );

		$db->setQuery( $query );
		$result	= $db->loadResult();

		return $result;
	}

	public function getSuffix( $alias = null )
	{
		$db		= DiscussHelper::getDBO();
		$query	= 'SELECT `suffix` FROM ' . $db->nameQuote( '#__discuss_post_types' )
				. ' WHERE ' . $db->nameQuote( 'alias' ) . '=' . $db->quote( $alias )
				. ' AND ' . $db->nameQuote( 'published' ) . '=' . $db->quote( 1 );

		$db->setQuery( $query );
		$result	= $db->loadResult();

		return $result;
	}
}
