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

require_once DISCUSS_ADMIN_ROOT . '/views.php';

class EasyDiscussViewRanks extends EasyDiscussAdminView
{
	public function ajaxResetRank()
	{
		$ajax = DiscussHelper::getHelper( 'Ajax' );
		$userid = JRequest::getInt( 'userid' );
		$config = DiscussHelper::getConfig();
		$db = DiscussHelper::getDBO();

		$table = DiscussHelper::getTable('Ranksusers');
		// $table->load( '', $userid );

		if( !$table->load( '', $userid ))
		{
			$ajax->reject();
			return $ajax->send();
		}

		$table->delete();

		// If after delete but rank still does not update, it might because there are multiple record of ranks in the database record.
		// Because the delete function only delete one record. (In case his db messed up, which contains multiple records.)
		DiscussHelper::getHelper( 'Ranks' )->assignRank( $userid, $config->get( 'main_ranking_calc_type', 'posts' ) );

		$ajax->resolve();
		return $ajax->send();
	}
}
