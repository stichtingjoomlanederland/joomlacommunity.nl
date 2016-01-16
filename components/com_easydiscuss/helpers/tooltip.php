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
include_once DISCUSS_CLASSES . '/json.php';

class DiscussTooltipHelper
{
	/*
	 * Returns a html formatted string for a standard tooltip.
	 *
	 * @param	$userId		The subject's user id.
	 * @return	$html		A string representing the tooltip's html
	 */
	public function getHTML( $content, $options )
	{
		$json = new Services_JSON();
		$options = $json->encode($options);

		$themes	= new CodeThemes();
		$themes->set( 'content'	, $content );
		$themes->set( 'options'	, $options );

		return $themes->fetch( 'tooltip.php' );
	}

	/*
	 * Returns a html formatted string for the blogger's tooltip.
	 *
	 * @param	$userId		The subject's user id.
	 * @return	$html		A string representing the tooltip's html
	 */
	public function getPosterHTML( $userId, $options )
	{
		$user	= DiscussHelper::getTable( 'Profile' );
		$user->load( $userId );

		$json = new Services_JSON();
		$options = $json->encode($options);

		$themes	= new DiscussThemes();
		$themes->set( 'user'	, $user );
		$themes->set( 'options' , $options );

		return $themes->fetch( 'tooltip.poster.php' );
	}

	public function getLastRepliesHTML( $postId='0', $options )
	{
		$db 	= DiscussHelper::getDBO();

		$query	= 'SELECT DISTINCT a.`user_id`';
		$query	.= ' FROM `#__discuss_posts` as a';
		$query	.= ' WHERE a.`published` = ' . $db->Quote('1');
		$query	.= ' AND a.`parent_id` = ' . $db->Quote($postId);
		$query	.= ' AND a.`user_type` = ' . $db->Quote('member');
		$query	.= ' AND a.`user_id` != ' . $db->Quote('0');
		$query	.= ' LIMIT 8';

		$db->setQuery($query);
		$replies = $db->loadObjectList();

		$json = new Services_JSON();
		$options = $json->encode($options);

		$themes	= new DiscussThemes();
		$themes->set( 'replies'	, $replies );
		$themes->set( 'options' , $options );

		return $themes->fetch( 'tooltip.lastreplies.php' );
	}

}
