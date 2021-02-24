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

class EasyDiscussViewRanks extends EasyDiscussAdminView
{
	public function resetRank()
	{
		$userid = $this->input->get('userid');
	
		$table = ED::table('Ranksusers');
		$table->load($userid, true);

		// If there is no data for this user, just skip
		if (!$table->id) {
			$this->ajax->resolve();
			return $this->ajax->send();
		}

		$table->delete($userid);

		// If after delete but rank still does not update, it might because there are multiple record of ranks in the database record.
		// Because the delete function only delete one record. (In case his db messed up, which contains multiple records.)
		// ED::Ranks()->assignRank($userid, $this->config->get('main_ranking_calc_type', 'posts'));

		// log the current action into database.
		$actionlog = ED::actionlog();
        $actionlogUserPermalink = $actionlog->normalizeActionLogUserPermalink($userid);
        $author = ED::user($userid);

		$actionlog->log('COM_ED_ACTIONLOGS_AUTHOR_RESET_RANK', 'user', array(
			'authorLink' => $actionlogUserPermalink,
			'authorName' => $author->getName()
		));

		$this->ajax->resolve();
		return $this->ajax->send();
	}
}
