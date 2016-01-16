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

/**
 * Notifications helper provides a set of methods
 * for the system.
 **/
class DiscussNotificationsHelper
{
	/**
	 * Formats and aggregates notification items.
	 *
	 * @access	public
	 * @param	Array	$items	An array of notification items
	 *
	 **/
	public function format( &$items , $group = false )
	{
		// Since these are just grouped items, fetch all child items.
		foreach( $items as $item )
		{
			$childs	= $this->getChilds( $item );

			$this->aggregate( $item , $childs );

			// Unique all authors
			$item->author		= array_unique( $item->author );

			// Reset the keys
			$item->author		= array_values( $item->author );

			// Get the author string
			$item->authorHTML	= $this->getAuthorHTML( $item->author );
			$item->title		= str_ireplace( '{authors}' , $item->authorHTML , $item->title );

			// Get the lapsed time
			$item->touched		= DiscussHelper::getHelper( 'Date' )->getLapsedTime( $item->latest );
		}

		if( $group )
		{
			$items	= $this->group( $items );
		}
	}

	/**
	 * Get the author html output
	 */
	private function getAuthorHTML( $authors )
	{
		// @TODO: Make this option configurable
		// This option sets the limit on the number of authors to be displayed in the notification
		$limit	= 3;

		$html 	= '';

		for( $i = 0; $i < count($authors); $i++ )
		{
			$profile	= DiscussHelper::getTable( 'Profile' );
			$profile->load( $authors[ $i ] );

			$html .= ' <b>' . $profile->getName() . '</b>';


			if( $i + 1 == $limit )
			{
				// Calculate the balance
				$balance	= count( $authors ) - ( $i + 1 );
				$html 		.= ' ' . DiscussHelper::getHelper( 'String' )->getNoun( 'COM_EASYDISCUSS_AND_OTHERS' , $balance , true );
				break;
			}

			if( isset( $authors[ $i + 2 ] ) )
			{
				$html .= JText::_( ',' );
			}
			else
			{
				if( isset( $authors[ $i + 1 ] ) )
				{
					$html .= ' ' . JText::_( 'COM_EASYDISCUSS_AND' );
				}
			}
		}

		return $html;
	}

	/**
	 * Retrieves the child notification items.
	 *
	 * @access	private
	 * @param	int	$cid	The parent id
	 * @param	int	$type	The parent type
	 * @param	int $target	The target user
	 **/
	private function getChilds( $parent )
	{
		$db		= DiscussHelper::getDBO();
		$query	= 'SELECT * FROM ' . $db->nameQuote( '#__discuss_notifications' ) . ' '
				. 'WHERE ' . $db->nameQuote( 'target' ) . '=' . $db->Quote( $parent->target ) . ' '
				. 'AND ' . $db->nameQuote( 'cid' ) . '=' . $db->Quote( $parent->cid ) . ' '
				. 'AND ' . $db->nameQuote( 'type' ) . '=' . $db->Quote( $parent->type ) . ' '
				. 'AND DATE_FORMAT( ' . $db->nameQuote( 'created' ) . ', "%Y%m%d" ) =' . $db->Quote( $parent->day ) . ' '
				. 'ORDER BY `created` ASC';

		$db->setQuery( $query );
		$childs	= $db->loadObjectList();

		return $childs;
	}

	/**
	 * Aggregates certain notification item
	 *
	 * @access	private
	 * @param	Object	$parent	The parent item.
	 * @param	Object	$childs	The notification item.
	 **/
	private function aggregate( &$parent , &$childs )
	{
		$parent->author	= array( $parent->author );

		foreach( $childs as $child )
		{
			$parent->author[]	= $child->author;
			$parent->latest		= $child->created;
		}
	}

	/**
	 * Group up items by days
	 *
	 * @access	private
	 * @param	Array	$items	An array of db items
	 */
	private function group( &$items )
	{
		$result	= array();
		$config 	= DiscussHelper::getConfig();

		foreach( $items as $item )
		{
			$date	= DiscussHelper::getDate( $item->created );
			$day	= $date->toFormat( '%A, %B %d %Y' );

			if( !isset( $result[ $day ] ) )
			{
				$result[ $day ]	= array();
			}

			$result[ $day ][]	= $item;
		}

		return $result;
	}
}
