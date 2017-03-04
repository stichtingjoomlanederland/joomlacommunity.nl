<?php
/**
* @package      EasyDiscuss
* @copyright    Copyright (C) 2010 - 2017 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Restricted access');

require_once(DISCUSS_ADMIN_ROOT . '/includes/maintenance/dependencies.php');

class EasyDiscussMaintenanceScriptUpdateDefaultPoints extends EasyDiscussMaintenanceScript
{
	public static $title = "Update Default Points Rule";
	public static $description = "Update default points rule for discussion and reply likes";

	public function main()
	{
		$exists = $this->checkExistingData();

		if ($exists) {
			return true;
		}

		$db = ED::db();

		// Since we already select ignore the rules during the installation, we know that the id start from 21
		$id = 21;

		$pointsRules = $this->getPointsRules($id);

		// Total Rules
		$total = count($pointsRules);

		$queryPoints = 'INSERT INTO ' . $db->nameQuote('#__discuss_points');
		$queryPoints .= ' (' . $db->nameQuote('rule_id') . ', ' . $db->nameQuote('title') . ', ';
		$queryPoints .= $db->nameQuote('created') . ', ' . $db->nameQuote('published') . ', ' . $db->nameQuote('rule_limit') . ') VALUES';

		$i = 0;
		$badgesRuleArray = array();

		foreach ($pointsRules as $point) {
			$i++;
			$queryPoints .= ' ("' . $point->rule_id . '", "' . $point->title . '", NOW(), "1", "' . $point->rule_limit . '")';

			if ($i != $total) {
				$queryPoints .= ',';
			}
		}

		$db->setQuery($queryPoints);
		$db->query();

		// Insert badges
		$queryBadges = 'INSERT INTO ' . $db->nameQuote('#__discuss_badges');
		$queryBadges .= ' (' . $db->nameQuote('rule_id') . ', ' . $db->nameQuote('title') . ', ' . $db->nameQuote('description') . ', ';
		$queryBadges .= $db->nameQuote('avatar') . ', ' . $db->nameQuote('created') . ', ' . $db->nameQuote('published') . ', ' . $db->nameQuote('rule_limit') . ', ' . $db->nameQuote('alias') . ') VALUES';

		$id1 = $id + 1;
		$id3 = $id + 3;

		// The badges is using unique 1 and 3 of the rules above.
		$queryBadges .=' ("' . $id1 . '", "Notable Answerer", "User likes your replies 100 times.", "reply-like.png", NOW(), "1", "100", "notable-answerer"),';
		$queryBadges .= ' ("' . $id3 . '", "Well-Known Questionnaire", "User likes your dicussions 50 times.", "discuss-like.png", NOW(), "1", "50", "well-known-questionnaire")';

		$db->setQuery($queryBadges);
		$db->query();

		return true;
	}

	private function getPointsRules($id)
	{
		// Rules data
		$pointsRules = array(
				array('title' => 'Like your reply', 'rule_limit' => '1'),
				array('title' => 'Unlike your reply', 'rule_limit' => '-1'),
				array('title' => 'Like your discussion', 'rule_limit' => '1'),
				array('title' => 'Unlike your discussion', 'rule_limit' => '-1')
			);

		$points = array();

		foreach ($pointsRules as $point) {

			$obj = new stdClass();

			// Increament rule id
			$id++;

			$obj->rule_id = $id;
			$obj->title = $point['title'];
			$obj->rule_limit = $point['rule_limit'];

			$points[] = $obj;
		}

		return $points;
	}

	private function checkExistingData()
	{
		$db = ED::db();

		$queryWhere = 'SELECT id FROM `#__discuss_rules` WHERE `command` IN ("easydiscuss.reply.like", "easydiscuss.reply.unlike", "easydiscusss.discussion.like", "easydiscuss.discussion.unlike")';

		$query = 'SELECT count(*) FROM `#__discuss_points` WHERE `rule_id` IN (' . $queryWhere . ')';

		$db->setQuery($query);
		$exist = $db->loadResult();

		if ($exist) {
			return true;
		}

		return false;
	}	
}