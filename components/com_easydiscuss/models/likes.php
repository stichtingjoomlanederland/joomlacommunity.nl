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

class EasyDiscussModelLikes extends EasyDiscussModel
{
	function isLike( $type, $contentId, $userId )
	{
		$db		= DiscussHelper::getDBO();

		$query	= 'SELECT `id` FROM `#__discuss_likes`';
		$query	.= ' WHERE `type` = ' . $db->Quote( $type );

		$query	.= ' AND `content_id` = ' . $db->Quote($contentId);
		$query	.= ' AND `created_by` = ' . $db->Quote($userId);

		$db->setQuery($query);
		$result = $db->loadResult();
		return $result;
	}

	function getTotalLikes( $postId )
	{

		$db		= DiscussHelper::getDBO();

		$query	= 'SELECT `id` FROM `#__discuss_likes`';
		$query	.= ' WHERE `content_id` = ' . $db->Quote($postId);

		$db->setQuery($query);
		$result = $db->loadObjectList();

		if( !empty($result) )
		{
			$result = count($result);
		}
		else
		{
			$result = 0;
		}

		return $result;
	}
}
