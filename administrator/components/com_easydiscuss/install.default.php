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

if( !class_exists( 'EasyDiscussDbJoomla15' ) )
{
	class EasyDiscussDbJoomla15
	{
		public $db 		= null;

		public function __construct()
		{
			$this->db	= JFactory::getDBO();
		}

		public function __call( $method , $args )
		{
			$refArray	= array();

			if( $args )
			{
				foreach( $args as &$arg )
				{
					$refArray[]	=& $arg;
				}
			}

			return call_user_func_array( array( $this->db , $method ) , $refArray );
		}
	}
}


if( !class_exists( 'EasyDiscussDbJoomla30' ) )
{
	class EasyDiscussDbJoomla30
	{
		public $db 		= null;

		public function __construct()
		{
			$this->db	= JFactory::getDBO();
		}

		public function loadResultArray()
		{
			return $this->db->loadColumn();
		}

		public function nameQuote( $str )
		{
			return $this->db->quoteName( $str );
		}

		public function __call( $method , $args )
		{
			$refArray	= array();

			if( $args )
			{
				foreach( $args as &$arg )
				{
					$refArray[]	=& $arg;
				}
			}

			return call_user_func_array( array( $this->db , $method ) , $refArray );
		}
	}
}


class EasyDiscussInstaller
{
	private $jinstaller		= null;
	private $manifest		= null;
	private $messages		= array();
	private $db				= null;
	private $installPath	= null;
	private $joomlaVersion	= null;

	public function __construct( JInstaller $jinstaller )
	{
		$this->db			= $this->db();
		$this->jinstaller	= $jinstaller;
		$this->manifest		= $this->jinstaller->getManifest();
		$this->installPath	= $this->jinstaller->getPath('source');
		$this->joomlaVersion= $this->getJoomlaVersion();
		$this->componentId	= $this->getDiscussId();
	}

	public static function getDbo()
	{
		static $db = null;

		if( !$db ) {
			$jVerArr	= explode('.', JVERSION);
			$jVersion	= $jVerArr[0] . '.' . $jVerArr[1];

			$className	= 'EasyDiscussDBJoomla15';

			if( $jVersion >= '2.5' )
			{
				$className 	= 'EasyDiscussDBJoomla30';
			}

			$db = new $className();
		}

		return $db;
	}

	public function db()
	{
		$version	= $this->getJoomlaVersion();
		$className	= 'EasyDiscussDBJoomla15';

		if( $version >= '2.5' )
		{
			$className 	= 'EasyDiscussDBJoomla30';
		}

		$db = new $className();

		return $db;
	}

	/**
	 * From time to time, any DB changes will be sync here
	 */
	private function checkDB()
	{
		$check = new EasyDiscussDatabaseUpdate( $this->db );

		if( !$check->update() )
		{
			$this->setMessage( 'Warning : The system encounter an error when it tries to update the database. Please kindly update the database manually.', 'warning' );
		}
	}

	private function checkConfig()
	{
		$query = 'SELECT COUNT(*) FROM ' . $this->db->nameQuote('#__discuss_configs')
				. ' WHERE ' . $this->db->nameQuote('name') . ' = ' . $this->db->quote('config');

		$this->db->setQuery($query);

		if (!$this->db->loadResult()) {

			$config = JPATH_ADMINISTRATOR . '/components/com_easydiscuss/configuration.ini';
			$contents = JFile::read($config);

			$obj = new stdClass();
			$obj->name = 'config';
			$obj->params = $contents;

			if (!$this->db->insertObject('#__discuss_configs', $obj))
			{
				$this->setMessage('Warning : The system encounter an error when it tries to create default config. Please kindly proceed to the configuration and save manually.', 'warning');
			}
		}
	}

	private function checkCategory()
	{
		$query	= 'SELECT COUNT(*) FROM ' . $this->db->nameQuote( '#__discuss_category' );

		$this->db->setQuery( $query );

		if( !$this->db->loadResult() )
		{
			$suAdmin	= $this->getSuperAdminId();

			$query	= "INSERT IGNORE INTO `#__discuss_category` (`id`, `created_by`, `title`, `alias`, `created`, `status`, `published`, `ordering`, `private`, `default`, `level`, `lft`, `rgt`)";
			$query	.= " VALUES ('1', " . $this->db->quote($suAdmin) .", 'Uncategorized', 'uncategorized', now(), 0, 1, 0, 0, 1, 0, 1, 2)";

			$this->db->setQuery( $query );

			if( !$this->db->query() )
			{
				$this->setMessage( 'Warning : The system encounter an error when it tries to create default discuss categories. Please kindly create the categories manually.', 'warning' );
			}
		}
	}

	private function getSuperAdminId()
	{
		if( $this->joomlaVersion >= '1.6' )
		{
			$saUsers	= $this->getSAUsersIds();

			$result = '42';
			if(count($saUsers) > 0)
			{
				$result = $saUsers['0'];
			}
		}
		else
		{
			$query = 'SELECT `id` FROM `#__users`';
			$query .= ' WHERE (LOWER( usertype ) = ' . $this->db->Quote('super administrator');
			$query .= ' OR `gid` = ' . $this->db->Quote('25') . ')';
			$query .= ' ORDER BY `id` ASC';
			$query .= ' LIMIT 1';

			$this->db->setQuery($query);
			$result = $this->db->loadResult();

			$result = (empty($result)) ? '62' : $result;
		}

		return $result;
	}

	private function getSAUsersIds()
	{
		$query = 'SELECT a.`id`, a.`title`';
		$query	.= ' FROM `#__usergroups` AS a';
		$query	.= ' LEFT JOIN `#__usergroups` AS b ON a.lft > b.lft AND a.rgt < b.rgt';
		$query	.= ' GROUP BY a.id';
		$query	.= ' ORDER BY a.lft ASC';

		$this->db->setQuery($query);
		$result = $this->db->loadObjectList();

		$saGroup = array();
		foreach($result as $group)
		{
			if(JAccess::checkGroup($group->id, 'core.admin'))
			{
				$saGroup[] = $group;
			}
		}

		// Now we got all the SA groups. Time to get the users
		$saUsers = array();
		if(count($saGroup) > 0)
		{
			foreach($saGroup as $sag)
			{
				$userArr	= JAccess::getUsersByGroup($sag->id);
				if(count($userArr) > 0)
				{
					foreach($userArr as $user)
					{
						$saUsers[] = $user;
					}
				}
			}
		}

		return $saUsers;
	}

	private function checkTag()
	{
		$query	= 'SELECT COUNT(*) FROM ' . $this->db->nameQuote( '#__discuss_tags' );
		$this->db->setQuery( $query );

		if( !$this->db->loadResult() )
		{
			$suAdmin	= $this->getSuperAdminId();
			$query		= 'INSERT INTO `#__discuss_tags` ( `title`, `alias`, `created`, `published`, `user_id`) '
						. 'VALUES ( "General", "general", now(), 1, ' . $this->db->Quote($suAdmin) .' ), '
						. '( "Automotive", "automotive", now(), 1, ' . $this->db->Quote($suAdmin) .' ), '
						. '( "Sharing", "sharing", now(), 1, ' . $this->db->Quote($suAdmin) .' ), '
						. '( "Info", "info", now(), 1, ' . $this->db->Quote($suAdmin) .' ), '
						. '( "Discussions" , "discussions" , now() , 1 , ' . $this->db->Quote( $suAdmin ) . ')';

			$this->db->setQuery( $query );

			if( !$this->db->query() )
			{
				$this->setMessage( 'Warning : The system encounter an error when it was trying to create the default tags. Please kindly create the tags manually.', 'warning' );
			}
		}
	}

	private function checkDiscussion()
	{
		$query	= 'SELECT COUNT(1) FROM ' . $this->db->nameQuote( '#__discuss_posts' ) . ' LIMIT 1';
		$this->db->setQuery( $query );

		if( !$this->db->loadResult() )
		{
			$suAdmin	= $this->getSuperAdminId();

			$content = array();
			$content['thankyou'] = 'Thank you for choosing EasyDiscuss as your preferred discussion tool for your Joomla! website. We hope you find it useful in achieving your needs.';
			$content['congratulation'] = 'Congratulations! You have successfully installed EasyDiscuss and ready to post your first question!';

			$query		= 'INSERT IGNORE INTO `#__discuss_posts` ( `id`, `title`, `alias`, `created`, `modified`, `replied`, `content`, `published`, `featured`, `isresolve`, `user_id`, `parent_id`, `user_type`) '
						. 'VALUES ( "1", "Thank you for choosing EasyDiscuss", "thank-you-for-choosing-easydiscuss", now(), now(), now(), ' . $this->db->Quote($content['congratulation']) . ', 1, 1, 1,' . $this->db->Quote($suAdmin) .', 0, "member" ), '
						. '( "2", "Congratulations! You have successfully installed EasyDiscuss", "congratulations-succesfully-installed-easydiscuss", now(), now() , now(), ' . $this->db->Quote($content['thankyou']) . ', 1, 0, 1,' . $this->db->Quote($suAdmin) .', 0, "member" ) ';
			$this->db->setQuery( $query );
			if( !$this->db->query() )
			{
				$this->setMessage( 'Warning : The system encounter an error when it was trying to create some sample posts.', 'warning' );
			}

			// Create tag for sample post
			$query		= 'INSERT IGNORE INTO `#__discuss_tags` ( `id`, `title`, `alias`, `created`, `published`, `user_id`) '
						. 'VALUES ( "6", "Thank You", "thank-you", now(), 1, ' . $this->db->Quote($suAdmin) .' ), '
						. '( "7", "Congratulations", "congratulations", now(), 1, ' . $this->db->Quote($suAdmin) .' ) ';
			$this->db->setQuery( $query );
			$this->db->query();

			// Create posts tags records
			$query		= 'INSERT INTO `#__discuss_posts_tags` ( `post_id`, `tag_id`) '
						. 'VALUES ( "1", "6" ), '
						. '( "2", "7" ) ';
			$this->db->setQuery( $query );
			$this->db->query();
		}
	}

	private function checkACL()
	{
		// Truncate the table before recreating the default acl rules.
		$query = 'TRUNCATE TABLE ' . $this->db->nameQuote('#__discuss_acl');
		$this->db->setQuery( $query );

		if( !$this->db->query() )
		{
			$this->setMessage( 'Fatal Error : The system encounter an error when it tries to truncate the acl rules table. Please kindly check your database permission and try again.', 'warning' );
		}

		$query	= 'INSERT INTO ' . $this->db->nameQuote( '#__discuss_acl' )
				. ' (' . $this->db->nameQuote( 'id' ) . ', ' . $this->db->nameQuote( 'action' ) . ', ' . $this->db->nameQuote( 'default' ) . ', ' . $this->db->nameQuote( 'description' ) . ', '
				. $this->db->nameQuote( 'published' ) . ', ' . $this->db->nameQuote( 'ordering' ) . ', ' . $this->db->nameQuote( 'public' ) . ') VALUES'
				. ' ("1", "add_reply", "1", "COM_EASYDISCUSS_ACL_OPTION_ADD_REPLY_DESC", "1", "0", "1"),'
				. ' ("2", "add_question", "1", "COM_EASYDISCUSS_ACL_OPTION_ADD_QUESTION_DESC", "1", "0", "1"),'
				. ' ("3", "add_attachment", "1", "COM_EASYDISCUSS_ACL_OPTION_ADD_ATTACHMENT_DESC", "1", "0", "1"),'
				. ' ("4", "add_tag", "1", "COM_EASYDISCUSS_ACL_OPTION_ADD_TAG_DESC", "1", "0", "1"),'
				. ' ("5", "edit_reply", "0", "COM_EASYDISCUSS_ACL_OPTION_EDIT_REPLY_DESC", "1", "0", "0"),'
				. ' ("6", "delete_reply", "0", "COM_EASYDISCUSS_ACL_OPTION_DELETE_REPLY_DESC", "1", "0", "0"),'
				. ' ("7", "mark_answered", "0", "COM_EASYDISCUSS_ACL_OPTION_MARK_ANSWERED_DESC", "1", "0", "0"),'
				. ' ("8", "lock_discussion", "0", "COM_EASYDISCUSS_ACL_OPTION_LOCK_DISCUSSION_DESC", "1", "0", "0"),'
				. ' ("9", "edit_question", "0", "COM_EASYDISCUSS_ACL_OPTION_EDIT_QUESTION_DESC", "1", "0", "0"),'
				. ' ("10", "delete_question", "0", "COM_EASYDISCUSS_ACL_OPTION_DELETE_QUESTION_DESC", "1", "0", "0"),'
				. ' ("11", "delete_attachment", "0", "COM_EASYDISCUSS_ACL_OPTION_DELETE_ATTACHMENT_DESC", "1", "0", "0"),'
				. ' ("12", "add_comment", "0", "COM_EASYDISCUSS_ACL_OPTION_ADD_COMMENT_DESC", "1", "0", "1"),'
				. ' ("13", "delete_comment", "0", "COM_EASYDISCUSS_ACL_OPTION_DELETE_COMMENT_DESC", "1", "0", "0"),'
				. ' ("14", "feature_post", "0", "COM_EASYDISCUSS_ACL_OPTION_FEATURE_POST_DESC", "1", "0", "0"),'
				. ' ("15", "send_report", "0", "COM_EASYDISCUSS_ACL_OPTION_SEND_REPORT_DESC", "1", "0", "1"),'
				. ' ("16", "mark_on_hold", "0", "COM_EASYDISCUSS_ACL_OPTION_MARK_ON_HOLD_DESC", "1", "0", "0"),'
				. ' ("17", "mark_accepted", "0", "COM_EASYDISCUSS_ACL_OPTION_MARK_ACCEPTED_DESC", "1", "0", "0"),'
				. ' ("18", "mark_working_on", "0", "COM_EASYDISCUSS_ACL_OPTION_MARK_WORKING_ON_DESC", "1", "0", "0"),'
				. ' ("19", "mark_rejected", "0", "COM_EASYDISCUSS_ACL_OPTION_MARK_REJECTED_DESC", "1", "0", "0"),'
				. ' ("20", "mark_no_status", "0", "COM_EASYDISCUSS_ACL_OPTION_MARK_NO_STATUS_DESC", "1", "0", "0"),'
				. ' ("21", "edit_branch", "0", "COM_EASYDISCUSS_ACL_OPTION_EDIT_BRANCH_DESC", "1", "0", "0"),'
				. ' ("22", "show_signature", "0", "COM_EASYDISCUSS_ACL_OPTION_SHOW_SIGNATURE_DESC", "1", "0", "1"),'
				. ' ("23", "delete_own_question", "0", "COM_EASYDISCUSS_ACL_OPTION_DELETE_OWN_QUESTION_DESC", "1", "0", "0"),'
				. ' ("24", "delete_own_replies", "0", "COM_EASYDISCUSS_ACL_OPTION_DELETE_OWN_REPLIES_DESC", "1", "0", "0"),'
				. ' ("25", "edit_own_reply", "0", "COM_EASYDISCUSS_ACL_OPTION_EDIT_OWN_REPLIES_DESC", "1", "0", "0"),'
				. ' ("26", "edit_own_question", "0", "COM_EASYDISCUSS_ACL_OPTION_EDIT_OWN_QUESTION_DESC", "1", "0", "0")';


		$this->db->setQuery( $query );
		if( !$this->db->query() )
		{
			$this->setMessage( 'Fatal Error : The system encounter an error when it tries to create the ACL rules. Please kindly check your database permission and try again.', 'warning' );
		}

		// Update user group acl rules
		$userGroup	= array();

		if( $this->joomlaVersion >= '1.6' )
		{
			// Get all user group for 1.6
			$query = 'SELECT a.id, a.title AS `name`, COUNT(DISTINCT b.id) AS level';
			$query .= ' , GROUP_CONCAT(b.id SEPARATOR \',\') AS parents';
			$query .= ' FROM #__usergroups AS a';
			$query .= ' LEFT JOIN `#__usergroups` AS b ON a.lft > b.lft AND a.rgt < b.rgt';
			$query .= ' GROUP BY a.id';
			$query .= ' ORDER BY a.lft ASC';

			$this->db->setQuery($query);
			$userGroups = $this->db->loadAssocList();

			$defaultAcl = array(1, 2, 3, 4, 5);

			if(!empty($userGroups)) {
				foreach($userGroups as $value) {
					switch($value['id']) {
						case '1':
							//default guest group in joomla 1.6
							$userGroup[$value['id']] = array();
							break;
						case '7':
							//default administrator group in joomla 1.6
							$userGroup[$value['id']] = 'all';
						case '8':
							//default super user group in joomla 1.6
							$userGroup[$value['id']] = 'all';
							break;
						default:
							//every other group
							$userGroup[$value['id']] = $defaultAcl;
					}
				}
			}
		} else {
			$defaultAcl = array(1, 2, 3, 4, 5);

			//28 Public frontend
			$userGroup[28] = $defaultAcl;
			//18 registered
			$userGroup[18] = $defaultAcl;
			//19 author
			$userGroup[19] = $defaultAcl;
			//20 editor
			$userGroup[20] = $defaultAcl;
			//21 publisher
			$userGroup[21] = $defaultAcl;
			//23 manager
			$userGroup[23] = $defaultAcl;
			//24 administrator
			$userGroup[24] = 'all';
			//25 super administrator
			$userGroup[25] = 'all';
		}

		// Getting all acl rules.
		$query = 'SELECT `id` FROM `#__discuss_acl` ORDER BY `id` ASC';
		$this->db->setQuery($query);
		$aclTemp = $this->db->loadResultArray();

		$aclRules			= array();
		$aclRulesAllEnabled	= array();
		//do not use array_fill_keys for lower php compatibility. use old-fashion way. sigh.
		foreach($aclTemp as $item)
		{
			$aclRules[$item]			= 0;
			$aclRulesAllEnabled[$item]	= 1;
		}

		$mainQuery = array();
		foreach($userGroup as $uKey => $uGroup)
		{
			$query = 'SELECT COUNT(1) FROM `#__discuss_acl_group` WHERE `content_id` = ' . $this->db->Quote($uKey);
			$query .= ' AND `type` = ' . $this->db->Quote('group');

			$this->db->setQuery($query);
			$result = $this->db->loadResult();

			if(empty($result))
			{
				$udAcls = array();

				if( is_array($uGroup))
				{
					$udAcls	= $aclRules;

					foreach($uGroup as $uAcl)
					{
						$udAcls[$uAcl] = 1;
					}
				}
				else if($uGroup == 'all')
				{
					$udAcls = $aclRulesAllEnabled;
				}

				foreach($udAcls as $key	=> $value)
				{
					$str			= '(' . $this->db->Quote($uKey) . ', ' . $this->db->Quote($key) . ', ' . $this->db->Quote($value) . ', ' . $this->db->Quote('group') .')';
					$mainQuery[]	= $str;
				}
			}//end if empty
		}//end foreach usergroup

		if( !empty($mainQuery) )
		{
			$query = 'INSERT INTO `#__discuss_acl_group` (`content_id`, `acl_id`, `status`, `type`) VALUES ';
			$query .= implode(',', $mainQuery);

			$this->db->setQuery($query);
			$this->db->query();

			if($this->db->getErrorNum())
			{
				$this->setMessage( 'Fatal Error : The system encounter an error when it tries to create the user groups ACL rules. Please kindly check your database permission and try again.', 'warning' );
			}
		}
	}

	private function checkBadges()
	{
		$query	= 'SELECT COUNT(1) FROM ' . $this->db->nameQuote( '#__discuss_badges' );

		$this->db->setQuery( $query );
		$count = $this->db->loadResult();

		if( !$count )
		{
			$query	= 'INSERT INTO ' . $this->db->nameQuote( '#__discuss_badges' )
					. ' (' . $this->db->nameQuote( 'id' ) . ', ' . $this->db->nameQuote( 'rule_id' ) . ', ' . $this->db->nameQuote( 'title' ) . ', ' . $this->db->nameQuote( 'description' ) . ', '
					. $this->db->nameQuote( 'avatar' ) . ', ' . $this->db->nameQuote( 'created' ) . ', ' . $this->db->nameQuote( 'published' ) . ', ' . $this->db->nameQuote( 'rule_limit' ) . ', ' . $this->db->nameQuote( 'alias' ) . ') VALUES'
					. ' ("1", "1", "Motivator", "Voted replies 100 times.", "motivator.png", NOW(), "1", "100", "motivator"),'
					. ' ("2", "2", "Hole-in-One", "Accepted 50 replies as answers.", "hole-in-one.png", NOW(), "1", "50", "hole-in-one"),'
					. ' ("3", "3", "Smile Seeker", "Liked 100 discussions.", "busybody.png", NOW(), "1", "100", "busybody"),'
					. ' ("4", "4", "Love Fool", "Liked 100 replies.", "love-fool.png", NOW(), "1", "100", "love-fool"),'
					. ' ("5", "5", "Vanity Monster", "Updated 5 avatars in profile.", "vanity-monster.png", NOW(), "1", "5", "vanity-monster"),'
					. ' ("6", "6", "Sherlock Holmes", "Started 10 discussions.", "sherlock-holmes.png", NOW(), "1", "10", "sherlock-holmes"),'
					. ' ("7", "7", "The Voice", "Posted 100 replies.", "the-voice.png", NOW(), "1", "100", "the-voice"),'
					. ' ("8", "8", "Bookworm", "Read 50 discussions.", "bookworm.png", NOW(), "1", "50", "bookworm"),'
					. ' ("9", "9", "Peacemaker", "Updated 50 discussions to resolved.", "peacemaker.png", NOW(), "1", "50", "peacemaker"),'
					. ' ("10", "10", "Attention!", "Updated profile 50 times.", "attention.png", NOW(), "1", "50", "attention"),'
					. ' ("11", "11", "Firestarter", "Posted 100 comments.", "firestarter.png", NOW(), "1", "100", "firestarter")';

			$this->db->setQuery( $query );

			if(! $this->db->query() )
			{
				$this->setMessage( 'Fatal Error : The system encounter an error when it tries to create the default rules. Please kindly check your database permission and try again.', 'warning' );
			}
		}
	}

	private function checkPostTypes()
	{
		$query	= 'SELECT COUNT(1) FROM ' . $this->db->nameQuote( '#__discuss_post_types' );

		$this->db->setQuery( $query );
		$count = $this->db->loadResult();

		if( !$count )
		{
			$query	= 'INSERT INTO ' . $this->db->nameQuote( '#__discuss_post_types' )
					. ' (' . $this->db->nameQuote( 'id' ) . ', ' . $this->db->nameQuote( 'title' ) . ', ' . $this->db->nameQuote( 'suffix' ) . ', ' . $this->db->nameQuote( 'created' ) . ', ' . $this->db->nameQuote( 'published' ) . ', ' . $this->db->nameQuote( 'alias' ) . ') VALUES'
					. ' ("1", "Bug", "", NOW(), "1", "bug"),'
					. ' ("2", "Issue", "", NOW(), "1", "issue"),'
					. ' ("3", "Task", "", NOW(), "1", "task")';

			$this->db->setQuery( $query );

			if(! $this->db->query() )
			{
				$this->setMessage( 'Fatal Error : The system encounter an error when it tries to create the default post types. Please kindly check your database permission and try again.', 'warning' );
			}
		}
	}

	private function checkRules()
	{
		$query	= 'SELECT COUNT(1) FROM ' . $this->db->nameQuote( '#__discuss_rules' );

		$this->db->setQuery( $query );
		$count = $this->db->loadResult();
		if( empty($count) )
		{
			$query	= 'INSERT INTO ' . $this->db->nameQuote( '#__discuss_rules' )
					. ' (' . $this->db->nameQuote( 'id' ) . ', ' . $this->db->nameQuote( 'command' ) . ', ' . $this->db->nameQuote( 'title' ) . ', ' . $this->db->nameQuote( 'description' ) . ', '
					. $this->db->nameQuote( 'callback' ) . ', ' . $this->db->nameQuote( 'created' ) . ', ' . $this->db->nameQuote( 'published' ) . ') VALUES'
					. ' ("1", "easydiscuss.vote.reply", "Vote a reply", "This rule allows you to assign a badge for a user when they vote a reply.", "", NOW(), "1"),'
					. ' ("2", "easydiscuss.answer.reply", "Reply accepted as answer", "This rule allows you to assign a badge for a user when their reply is accepted as an answer.", "", NOW(), "1"),'
					. ' ("3", "easydiscuss.like.discussion", "Like a discussion", "This rule allows you to assign a badge for a user when they like a discussion.", "", NOW(), "1"),'
					. ' ("4", "easydiscuss.like.reply", "Like a reply", "This rule allows you to assign a badge for a user when they like a reply.", "", NOW(), "1"),'
					. ' ("5", "easydiscuss.new.avatar", "Updates profile picture", "This rule allows you to assign a badge for a user when they upload a profile picture.", "", NOW(), "1"),'
					. ' ("6", "easydiscuss.new.discussion", "New Discussion", "This rule allows you to assign a badge for a user when they create a new discussion.", "", NOW(), "1"),'
					. ' ("7", "easydiscuss.new.reply", "New Reply", "This rule allows you to assign a badge for a user when they reply to discussion.", "", NOW(), "1"),'
					. ' ("8", "easydiscuss.read.discussion", "Read a discusison", "This rule allows you to assign a badge for a user when they read a discussion.", "", NOW(), "1"),'
					. ' ("9", "easydiscuss.resolved.discussion", "Update discussion to resolved", "This rule allows you to assign a badge for a user when they mark their discussion as resolved.", "", NOW(), "1"),'
					. ' ("10", "easydiscuss.update.profile", "Updates profile", "This rule allows you to assign a badge for a user when they update their profile.", "", NOW(), "1"),'
					. ' ("11", "easydiscuss.new.comment", "New Comment", "This rule allows you to assign a badge for a user when they create a new comment.", "", NOW(), "1"),'
					. ' ("12", "easydiscuss.unlike.discussion", "Unlike a discussion", "This rule allows you to deduct points for a user when they unlike a discussion.", "", NOW(), "1"),'
					. ' ("13", "easydiscuss.unlike.reply", "Unlike a reply", "This rule allows you to deduct points for a user when they unlike a reply.", "", NOW(), "1"),'
					. ' ("14", "easydiscuss.remove.reply", "Remove a reply", "This rule allows you to assign a badge for a user when they remove a reply.", "", NOW(), "1"),'
					. ' ("15", "easydiscuss.vote.answer", "Vote an answer", "This rule allows you to assign points for a user when they vote an answer.", "", NOW(), "1"),'
					. ' ("16", "easydiscuss.unvote.answer", "Unvote an answer", "This rule allows you to assign points for a user when they vote down an answer.", "", NOW(), "1"),'
					. ' ("17", "easydiscuss.vote.question", "Vote a question", "This rule allows you to assign points for a user when they vote a question.", "", NOW(), "1"),'
					. ' ("18", "easydiscuss.unvote.question", "Unvote a question", "This rule allows you to assign points for a user when they vote down a question.", "", NOW(), "1"),'
					. ' ("19", "easydiscuss.unvote.reply", "Unvote a reply", "This rule allows you to assign a badge or points for a user when they vote down a reply.", "", NOW(), "1"),'
					. ' ("20", "easydiscuss.remove.discussion", "Remove a discussion", "This rule allows you to assign a badge or points for a user when they remove a discussion.", "", NOW(), "1")';

			$this->db->setQuery( $query );

			if( !$this->db->query() )
			{
				$this->setMessage( 'Fatal Error : The system encounter an error when it tries to create the default rules. Please kindly check your database permission and try again.', 'warning' );
			}
		}
		else
		{
			$query = 'SELECT COUNT(id) FROM `#__discuss_rules` WHERE `command` = ' . $this->db->quote('easydiscuss.remove.reply');
			$this->db->setQuery($query);
			if( !$this->db->loadResult() )
			{
				$query = "INSERT INTO `#__discuss_rules`(`command`, `title`, `description`, `callback`, `created`, `published`) VALUES ('easydiscuss.remove.reply', 'Remove a reply', 'This rule allows you to assign a badge for a user when they remove a reply.', '', NOW(), 1);";
				$this->db->setQuery($query);
				if( !$this->db->query() ) return false;
			}

			$query = 'SELECT COUNT(id) FROM `#__discuss_rules` WHERE `command` = ' . $this->db->quote('easydiscuss.vote.answer');
			$this->db->setQuery($query);
			if( !$this->db->loadResult() )
			{
				$query = "INSERT INTO `#__discuss_rules`(`command`, `title`, `description`, `callback`, `created`, `published`) VALUES ( 'easydiscuss.vote.answer', 'Vote an answer', 'This rule allows you to assign a badge for a user when they remove a reply.', '', NOW(), 1);";
				$this->db->setQuery($query);
				if( !$this->db->query() ) return false;
			}

			$query = 'SELECT COUNT(id) FROM `#__discuss_rules` WHERE `command` = ' . $this->db->quote('easydiscuss.unvote.answer');
			$this->db->setQuery($query);
			if( !$this->db->loadResult() )
			{
				$query = "INSERT INTO `#__discuss_rules`(`command`, `title`, `description`, `callback`, `created`, `published`) VALUES ( 'easydiscuss.unvote.answer', 'Unvote an answer', 'This rule allows you to assign points for a user when they vote down an answer.', '', NOW(), 1);";
				$this->db->setQuery($query);
				if( !$this->db->query() ) return false;
			}

			$query = 'SELECT COUNT(id) FROM `#__discuss_rules` WHERE `command` = ' . $this->db->quote('easydiscuss.vote.question');
			$this->db->setQuery($query);
			if( !$this->db->loadResult() )
			{
				$query = "INSERT INTO `#__discuss_rules`(`command`, `title`, `description`, `callback`, `created`, `published`) VALUES ( 'easydiscuss.vote.question', 'Vote a question', 'This rule allows you to assign points for a user when they vote a question.', '', NOW(), 1);";
				$this->db->setQuery($query);
				if( !$this->db->query() ) return false;
			}

			$query = 'SELECT COUNT(id) FROM `#__discuss_rules` WHERE `command` = ' . $this->db->quote('easydiscuss.unvote.question');
			$this->db->setQuery($query);
			if( !$this->db->loadResult() )
			{
				$query = "INSERT INTO `#__discuss_rules`(`command`, `title`, `description`, `callback`, `created`, `published`) VALUES ( 'easydiscuss.unvote.question', 'Unvote a question', 'This rule allows you to assign points for a user when they vote down a question.', '', NOW(), 1);";
				$this->db->setQuery($query);
				if( !$this->db->query() ) return false;
			}

			$query = 'SELECT COUNT(id) FROM `#__discuss_rules` WHERE `command` = ' . $this->db->quote('easydiscuss.unvote.reply');
			$this->db->setQuery($query);
			if( !$this->db->loadResult() )
			{
				$query = "INSERT INTO `#__discuss_rules`(`command`, `title`, `description`, `callback`, `created`, `published`) VALUES ( 'easydiscuss.unvote.reply', 'Unvote a reply', 'This rule allows you to assign a badge or points for a user when they vote down a reply.', '', NOW(), 1);";
				$this->db->setQuery($query);
				if( !$this->db->query() ) return false;
			}
		}
	}

	private function checkPoints()
	{
		$query	= 'SELECT COUNT(1) FROM ' . $this->db->nameQuote( '#__discuss_points' );

		$this->db->setQuery( $query );
		$count = $this->db->loadResult();

		if( empty($count) )
		{
			$query	= 'INSERT INTO ' . $this->db->nameQuote( '#__discuss_points' )
					. ' (' . $this->db->nameQuote( 'id' ) . ', ' . $this->db->nameQuote( 'rule_id' ) . ', ' . $this->db->nameQuote( 'title' ) . ', '
					. $this->db->nameQuote( 'created' ) . ', ' . $this->db->nameQuote( 'published' ) . ', ' . $this->db->nameQuote( 'rule_limit' ) . ') VALUES'
					. ' ("1", "1", "Vote a reply", NOW(), "1", "1"),'
					. ' ("2", "2", "Reply accepted as answer", NOW(), "1", "1"),'
					. ' ("3", "3", "Like a discussion", NOW(), "1", "1"),'
					. ' ("4", "4", "Like a reply", NOW(), "1", "1"),'
					. ' ("5", "5", "Updates profile picture", NOW(), "1", "1"),'
					. ' ("6", "6", "New Discussion", NOW(), "1", "2"),'
					. ' ("7", "7", "New Reply", NOW(), "1", "1"),'
					. ' ("8", "8", "Read a discusison", NOW(), "1", "0"),'
					. ' ("9", "9", "Update discussion to resolved", NOW(), "1", "0"),'
					. ' ("10", "10", "Updates profile", NOW(), "1", "0"),'
					. ' ("11", "11", "New Comment", NOW(), "1", "1"),'
					. ' ("12", "12", "Unlike a discussion", NOW(), "1", "-1"),'
					. ' ("13", "13", "Unlike a reply", NOW(), "1", "-1"),'
					. ' ("14", "14", "Remove a reply", NOW(), "1", "0"),'
					. ' ("15", "15", "Vote an answer", NOW(), "1", "1"),'
					. ' ("16", "16", "Unvote an answer", NOW(), "1", "-1"),'
					. ' ("17", "17", "Vote a question", NOW(), "1", "1"),'
					. ' ("18", "18", "Unvote a question", NOW(), "1", "-1"),'
					. ' ("19", "19", "Unvote a reply", NOW(), "1", "-1")';

			$this->db->setQuery( $query );

			if( !$this->db->query() )
			{
				$this->setMessage( 'Fatal Error : The system encounter an error when it tries to create the default points. Please kindly check your database permission and try again.', 'warning' );
			}
		}
	}

	private function checkFoundry()
	{
		// Copy media/foundry
		// Overwrite only if version is newer
		$mediaSource		= $this->installPath . '/foundry';
		$mediaDestina		= JPATH_ROOT . '/media/foundry';
		$overwrite			= false;
		$incomingVersion	= '';
		$installedVersion	= '';

		if( !JFolder::exists( $mediaDestina ) )
		{
			// foundry folder not found. just copy foundry folder without need to check.
			if( !JFolder::copy($mediaSource, $mediaDestina, '', true) )
			{
				return false;
			}
		}

		// We don't have a a constant of Foundry's version, so we'll
		// find the folder name as the version number. We assumed there's
		// only ONE folder in foundry that come with the installer.
		$folder	= JFolder::folders($mediaSource);
		$folder = '/' . $folder[0];

		if(	!($incomingVersion = (string) JFile::read( $mediaSource . $folder . '/version' )) )
		{
			// Can't read the version number
			return false;
		}

		if( !JFile::exists($mediaDestina . $folder . '/version' )
			|| !($installedVersion = (string) JFile::read( $mediaDestina . $folder . '/version' )) )
		{
			// Foundry version not exists or need upgrade
			$overwrite = true;
		}

		$incomingVersion	= preg_replace('/[^0-9\.]/i', '', $incomingVersion);
		$installedVersion	= preg_replace('/[^0-9\.]/i', '', $installedVersion);

		if( $overwrite || version_compare($incomingVersion, $installedVersion) >= 0 )
		{
			if( !JFolder::copy($mediaSource . $folder, $mediaDestina . $folder, '', true) )
			{
				return false;
			}
		}

		// Double check if the styles folder is missing or not. Compile stylesheet error issue by customers.
		if( !JFolder::exists( $mediaDestina . $folder . '/styles' ) )
		{
			if( !JFolder::copy($mediaSource . $folder . '/styles', $mediaDestina . $folder . '/styles', '', true) )
			{
				return false;
			}
		}

		return true;
	}

	private function copyMediaFiles()
	{
		jimport('joomla.filesystem.file');
		jimport('joomla.filesystem.folder');

		$mediaSource	= $this->installPath.'/media/';
		$mediaDestina	= JPATH_ROOT.'/media/com_easydiscuss/';

		if(! JFolder::exists( $mediaDestina ) ) {

			// If media/com_easydiscuss did not exist, overwrite everything
			$state 	= JFolder::copy($mediaSource, $mediaDestina, '', true);

			if (!$state) {
				$this->setMessage( 'Warning: The system could not copy files to Media folder. Please kindly check the media folder permission.', 'warning' );
			}

		} else {

			// Overwrite all the old files with new ones execpt attachments folder
			$files		= array( 'config.php', 'index.html' );
			$folders 	= array('badges', 'images', 'resources', 'scripts', 'styles');

			foreach ($folders as $folder) {

				$state 	= JFolder::copy($mediaSource . $folder, $mediaDestina . $folder, '', true);

				if (!$state) {
					return false;
				}
			}

			foreach ($files as $file) {

				$source 	= $mediaSource . $file;
				$dest 		= $mediaDestina . $file;
				$state 		= JFile::copy($source, $dest, null, false);

				if (!$state) {
					return false;
				}
			}
		}

		return true;
	}

	private function checkPlugin()
	{
		$result = array();

		if($this->joomlaVersion > '1.5')
		{
			$plugins = $this->manifest->plugins;

			if( $plugins instanceof JXMLElement && count($plugins) )
			{
				foreach ($plugins->plugin as $plugin)
				{
					$plgDir = $this->installPath.'/plugins/'.$plugin->getAttribute('plugin');

					if( JFolder::exists($plgDir) )
					{
						$jinstaller = new JInstaller;
						$result[]	= $jinstaller->install($plgDir);

						$type = (string) $jinstaller->manifest->attributes()->type;

						if (count($jinstaller->manifest->files->children()))
						{
							foreach ($jinstaller->manifest->files->children() as $file)
							{
								if ((string) $file->attributes()->$type)
								{
									$element = (string) $file->attributes()->$type;
									break;
								}
							}
						}

						$query	= ' UPDATE `#__extensions` SET `enabled` = ' . $this->db->quote( 1 )
								. ' WHERE `element` = ' . $this->db->quote( $element )
								. ' AND `folder` = ' . $this->db->quote( $jinstaller->manifest->getAttribute('group') )
								. ' AND `type` = ' . $this->db->quote( 'plugin' );
						$this->db->setQuery( $query );
						$result[] = $this->db->query();
					}
				}
			}
		}
		else
		{
			//$plugins = $this->jinstaller->_adapters['component']->manifest->getElementByPath('plugins');
			$plugins = $this->jinstaller->_manifest->document->plugins[0];

			if( $plugins instanceof JSimpleXMLElement && count($plugins->children()) )
			{
				foreach ($plugins->children() as $plugin)
				{
					$plgDir = $this->installPath.'/plugins/'.$plugin->attributes('plugin');

					if( JFolder::exists($plgDir) )
					{
						$jinstaller = new JInstaller;
						$result[]	= $jinstaller->install($plgDir);

						$type = $jinstaller->_adapters['plugin']->manifest->attributes('type');

						// Set the installation path
						$element = $jinstaller->_adapters['plugin']->manifest->getElementByPath('files');
						if (is_a($element, 'JSimpleXMLElement') && count($element->children())) {
							$files = $element->children();
							foreach ($files as $file) {
								if ($file->attributes($type)) {
									$element = $file->attributes($type);
									break;
								}
							}
						}

						$query	= 'UPDATE `#__plugins` SET `published` = ' . $this->db->quote( 1 )
								. ' WHERE `element` = ' . $this->db->quote( $element )
								. ' AND `folder` = ' . $this->db->quote( $plugin->attributes('group') );
						$this->db->setQuery($query);
						$this->db->query();
					}
				}
			}
		}

		foreach ($result as $value)
		{
			if( !$value )
			{
				$this->setMessage( 'Warning : The system encounter an error when it tries to install the user plugin. Please kindly install the plugin manually.', 'warning' );
			}
		}
	}

	//Parse SQL file
	private function parseDiscussSQLFile()
	{
		$sqlfile = JPATH_ROOT . '/administrator/components/com_easydiscuss/install.mysql.utf8.sql';

		if( !JFile::exists($sqlfile) )
		{
			return false;
		}

		$buffer = file_get_contents($sqlfile);

		if ($buffer === false)
		{
			return false;
		}

		$queries = $this->db->splitSql($buffer);

		if (count($queries) == 0)
		{
			// No queries to process
			return 0;
		}

		foreach ($queries as $query)
		{
			$query = trim($query);

			if ($query != '' && $query{0} != '#')
			{
				$this->db->setQuery($query);

				if (!$this->db->query())
				{
					return false;
				}
			}
		}

		return true;
	}

	public function checkMenuItem()
	{
		if( $this->joomlaVersion >= '1.6' )
		{
			$query	= 'SELECT COUNT(*) FROM ' . $this->db->nameQuote( '#__menu' ) . ' '
				. 'WHERE ' . $this->db->nameQuote( 'link' ) . ' LIKE ' . $this->db->Quote( '%option=com_easydiscuss%') . ' '
				. 'AND `client_id`=' . $this->db->Quote( '0' ) . ' '
				. 'AND `type`=' . $this->db->Quote( 'component' ) . ' '
				. 'AND `menutype` !=' . $this->db->Quote( 'main' );
		}
		else
		{
			$query	= 'SELECT COUNT(*) FROM ' . $this->db->nameQuote( '#__menu' ) . ' '
				. 'WHERE ' . $this->db->nameQuote( 'link' ) . ' LIKE ' . $this->db->Quote( '%option=com_easydiscuss%');
		}

		$this->db->setQuery( $query );
		$result = $this->db->loadResult();

		$cid = $this->getDiscussId();

		if( !$cid )
		{
			return false;
		}

		if ( !$result )
		{
			if( $this->getJoomlaVersion() >= '1.6' )
			{
				$table = JTable::getInstance('Menu', 'JTable', array());

				$table->menutype		= 'mainmenu';
				$table->title			= 'Discussions';
				$table->alias			= 'discussions';
				$table->path			= 'discussions';
				$table->link			= 'index.php?option=com_easydiscuss&view=index';
				$table->type			= 'component';
				$table->published		= '1';
				$table->parent_id		= '1';
				$table->component_id	= $cid;
				$table->client_id		= '0';
				$table->language		= '*';

				$table->setLocation('1', 'last-child');

				if(!$table->store()){
					$status = false;
				}
			}
			else
			{
				$query	= 'SELECT ' . $this->db->nameQuote( 'ordering' ) . ' '
						. 'FROM ' . $this->db->nameQuote( '#__menu' ) . ' '
						. 'ORDER BY ' . $this->db->nameQuote( 'ordering' ) . ' DESC LIMIT 1';
				$this->db->setQuery( $query );
				$order	= $this->db->loadResult() + 1;

				$status = true;

				// Update the existing menu items.
				$query	= 'INSERT INTO ' . $this->db->nameQuote( '#__menu' )
					. '('
						. $this->db->nameQuote( 'menutype' ) . ', '
						. $this->db->nameQuote( 'name' ) . ', '
						. $this->db->nameQuote( 'alias' ) . ', '
						. $this->db->nameQuote( 'link' ) . ', '
						. $this->db->nameQuote( 'type' ) . ', '
						. $this->db->nameQuote( 'published' ) . ', '
						. $this->db->nameQuote( 'parent' ) . ', '
						. $this->db->nameQuote( 'componentid' ) . ', '
						. $this->db->nameQuote( 'sublevel' ) . ', '
						. $this->db->nameQuote( 'ordering' ) . ' '
					. ') '
					. 'VALUES('
						. $this->db->quote( 'mainmenu' ) . ', '
						. $this->db->quote( 'Discussions' ) . ', '
						. $this->db->quote( 'discussions' ) . ', '
						. $this->db->quote( 'index.php?option=com_easydiscuss&view=index' ) . ', '
						. $this->db->quote( 'component' ) . ', '
						. $this->db->quote( '1' ) . ', '
						. $this->db->quote( '0' ) . ', '
						. $this->db->quote( $cid ) . ', '
						. $this->db->quote( '0' ) . ', '
						. $this->db->quote( $order ) . ' '
					. ') ';

				$this->db->setQuery( $query );
				$this->db->query();

				if($this->db->getErrorNum())
				{
					$status = false;
				}
			}

			return true;
		}

		// Update menu items
		if( $this->joomlaVersion >= '1.6' )
		{
			$query	= 'UPDATE ' . $this->db->nameQuote( '#__menu' ) . ' '
				. 'SET component_id=' . $this->db->Quote( $cid ) . ' '
				. 'WHERE link LIKE ' . $this->db->Quote('%option=com_easydiscuss%') . ' '
				. 'AND `type`=' . $this->db->Quote( 'component' ) . ' '
				. 'AND `menutype` = ' . $this->db->Quote( 'mainmenu' ) . ' '
				. 'AND `client_id`=' . $this->db->Quote( '0' );
		}
		else
		{
			$query	= 'UPDATE ' . $this->db->nameQuote( '#__menu' ) . ' '
				. 'SET componentid=' . $this->db->Quote( $cid ) . ' '
				. 'WHERE link LIKE ' . $this->db->Quote('%option=com_easydiscuss%');
		}

		$this->db->setQuery( $query );
		$this->db->query();

		return true;
	}

	public static function checkMenu()
	{
		$db 			= self::getDbo();
		$joomlaVersion  = self::getJoomlaVersion2();

		if( $joomlaVersion >= '1.6' )
		{
			$query	= 'SELECT COUNT(*) FROM ' . $db->nameQuote( '#__menu' ) . ' '
				. 'WHERE ' . $db->nameQuote( 'link' ) . ' LIKE ' . $db->Quote( '%option=com_easydiscuss%') . ' '
				. 'AND `client_id`=' . $db->Quote( '0' ) . ' '
				. 'AND `type`=' . $db->Quote( 'component' ) . ' '
				. 'AND `menutype` !=' . $db->Quote( 'main' );
		}
		else
		{
			$query	= 'SELECT COUNT(*) FROM ' . $db->nameQuote( '#__menu' ) . ' '
				. 'WHERE ' . $db->nameQuote( 'link' ) . ' LIKE ' . $db->Quote( '%option=com_easydiscuss%');
		}

		$db->setQuery( $query );
		$result = $db->loadResult();

		$cid = self::getDiscussId2();

		if( !$cid )
		{
			return false;
		}

		if ( empty( $result ) )
		{
			if( $joomlaVersion >= '1.6' )
			{
				$table = JTable::getInstance('Menu', 'JTable', array());

				$table->menutype		= 'mainmenu';
				$table->title			= 'Discussions';
				$table->alias			= 'discussions';
				$table->path			= 'discussions';
				$table->link			= 'index.php?option=com_easydiscuss&view=index';
				$table->type			= 'component';
				$table->published		= '1';
				$table->parent_id		= '1';
				$table->component_id	= $cid;
				$table->client_id		= '0';
				$table->language		= '*';

				$table->setLocation('1', 'last-child');

				if(!$table->store()){
					$status = false;
				}
			}
			else
			{
				$query	= 'SELECT ' . $db->nameQuote( 'ordering' ) . ' '
						. 'FROM ' . $db->nameQuote( '#__menu' ) . ' '
						. 'ORDER BY ' . $db->nameQuote( 'ordering' ) . ' DESC LIMIT 1';
				$db->setQuery( $query );
				$order	= $db->loadResult() + 1;

				$status = true;

				// Update the existing menu items.
				$query	= 'INSERT INTO ' . $db->nameQuote( '#__menu' )
					. '('
						. $db->nameQuote( 'menutype' ) . ', '
						. $db->nameQuote( 'name' ) . ', '
						. $db->nameQuote( 'alias' ) . ', '
						. $db->nameQuote( 'link' ) . ', '
						. $db->nameQuote( 'type' ) . ', '
						. $db->nameQuote( 'published' ) . ', '
						. $db->nameQuote( 'parent' ) . ', '
						. $db->nameQuote( 'componentid' ) . ', '
						. $db->nameQuote( 'sublevel' ) . ', '
						. $db->nameQuote( 'ordering' ) . ' '
					. ') '
					. 'VALUES('
						. $db->quote( 'mainmenu' ) . ', '
						. $db->quote( 'Discussions' ) . ', '
						. $db->quote( 'discussions' ) . ', '
						. $db->quote( 'index.php?option=com_easydiscuss&view=index' ) . ', '
						. $db->quote( 'component' ) . ', '
						. $db->quote( '1' ) . ', '
						. $db->quote( '0' ) . ', '
						. $db->quote( $cid ) . ', '
						. $db->quote( '0' ) . ', '
						. $db->quote( $order ) . ' '
					. ') ';

				$db->setQuery( $query );
				$db->query();

				if($db->getErrorNum())
				{
					$status = false;
				}
			}

			return true;
		}

		// Update menu items
		if( $joomlaVersion >= '1.6' )
		{
			$query	= 'UPDATE ' . $db->nameQuote( '#__menu' ) . ' '
				. 'SET component_id=' . $db->Quote( $cid ) . ' '
				. 'WHERE link LIKE ' . $db->Quote('%option=com_easydiscuss%') . ' '
				. 'AND `type`=' . $db->Quote( 'component' ) . ' '
				. 'AND `menutype` = ' . $db->Quote( 'mainmenu' ) . ' '
				. 'AND `client_id`=' . $db->Quote( '0' );
		}
		else
		{
			$query	= 'UPDATE ' . $db->nameQuote( '#__menu' ) . ' '
				. 'SET componentid=' . $db->Quote( $cid ) . ' '
				. 'WHERE link LIKE ' . $db->Quote('%option=com_easydiscuss%');
		}

		$db->setQuery( $query );
		$db->query();

		return true;
	}

	public function getDiscussId()
	{
		$db = $this->db;

		if( $this->joomlaVersion >= '1.6' )
		{
			$query	= 'SELECT ' . $db->nameQuote( 'extension_id' ) . ' '
				. 'FROM ' . $db->nameQuote( '#__extensions' ) . ' '
				. 'WHERE `element`=' . $db->Quote( 'com_easydiscuss' ) . ' '
				. 'AND `type`=' . $db->Quote( 'component' ) . ' ';
		}
		else
		{
			$query	= 'SELECT ' . $db->nameQuote( 'id' ) . ' '
				. 'FROM ' . $db->nameQuote( '#__components' ) . ' '
				. 'WHERE `option`=' . $db->Quote( 'com_easydiscuss' ) . ' '
				. 'AND `parent`=' . $db->Quote( '0');
		}

		$db->setQuery( $query );

		return $db->loadResult();
	}


	public static function getDiscussId2()
	{
		$db = self::getDbo();

		if( self::getJoomlaVersion2() >= '1.6' )
		{
			$query	= 'SELECT ' . $db->nameQuote( 'extension_id' ) . ' '
				. 'FROM ' . $db->nameQuote( '#__extensions' ) . ' '
				. 'WHERE `element`=' . $db->Quote( 'com_easydiscuss' ) . ' '
				. 'AND `type`=' . $db->Quote( 'component' ) . ' ';
		}
		else
		{
			$query	= 'SELECT ' . $db->nameQuote( 'id' ) . ' '
				. 'FROM ' . $db->nameQuote( '#__components' ) . ' '
				. 'WHERE `option`=' . $db->Quote( 'com_easydiscuss' ) . ' '
				. 'AND `parent`=' . $db->Quote( '0');
		}

		$db->setQuery( $query );

		return $db->loadResult();
	}

	public function clearCache()
	{
		$adminTheme = JPATH_ADMINISTRATOR . '/components/com_easydiscuss/themes';
		$siteTheme = JPATH_ROOT . '/components/com_easydiscuss/themes';
		$module = JPATH_ROOT 	. '/modules';

		$paths	= array( $adminTheme , $siteTheme , $module );
		$count 	= 0;
		foreach( $paths as $path )
		{
			$cachedFiles 	= JFolder::files( $path , 'style.less.cache' , true , true );

			foreach( $cachedFiles as $file )
			{
				$count++;
				JFile::delete( $file );
			}
		}
	}

	public function execute()
	{
		// Update Db columns first before proceed.
		$this->checkDB();

		// Create default config
		$this->checkConfig();

		// Create default category
		$this->checkCategory();

		// Create default tags
		$this->checkTag();

		// Create default discussion
		$this->checkDiscussion();

		// Update acl
		$this->checkACL();

		// Check badges
		$this->checkBadges();

		$this->checkPostTypes();

		// Create default rules
		$this->checkRules();

		// Create default points
		// only run this after checkRules executed
		$this->checkPoints();

		// Install Foundry Javascript Framework
		if( !$this->checkFoundry() )
		{
			$this->setMessage( 'Warning: The system could not update Foundry Javascript Framework. Please kindly check the media folder permission.', 'warning' );
		}

		// Copy media files
		$this->copyMediaFiles();

		// Install user plugin
		$this->checkPlugin();

		if( $this->joomlaVersion == '1.5' )
		{
			$this->checkMenuItem();
		}

		$this->migrateJomSocialStreamNameSpace();

		$this->migratePostContentType();

		$this->clearCache();

		$this->setMessage( 'Success : Installation Completed. Thank you for choosing EasyDiscuss as your discussion solution.', 'info' );
	}

	public function updatedb()
	{
		if( !$this->parseDiscussSQLFile() )
		{
			$this->setMessage( 'Warning : Parse SQL file error.', 'warning' );
		}

		// Create default config
		$this->checkConfig();

		// Update Db columns first before proceed.
		$this->checkDB();

		// Create default category
		$this->checkCategory();

		// Create default tags
		$this->checkTag();

		// Create default discussion
		$this->checkDiscussion();

		// Update acl
		$this->checkACL();

		// Check badges
		$this->checkBadges();

		$this->checkPostTypes();

		// Create default rules
		$this->checkRules();

		// Create default points
		// only run this after checkRules executed
		$this->checkPoints();

		$this->migrateJomSocialStreamNameSpace();

		$this->migratePostContentType();
	}


	public function migratePostContentType()
	{
		jimport('joomla.registry.registry');

		$db = self::getDbo();

		$query 		= 'SELECT ' . $db->nameQuote( 'params' ) . ' FROM ' . $db->nameQuote( '#__discuss_configs' );
		$query 		.= ' WHERE ' . $db->nameQuote( 'name' ) . '=' . $db->Quote( 'config' );

		$db->setQuery( $query );
		$rawParams 	= $db->loadResult();

		if( empty($rawParams) )
		{
			return true;
		}

		$config 		= new JRegistry();
		$editorType 	= '';

		if( $this->getJoomlaVersion() >= '1.6' )
		{
			$config->loadString( $rawParams , 'INI');
			$editorType = $config->get('layout_editor', '');
		}
		else
		{
			$config->loadINI( $rawParams );
			$editorType = $config->getValue('layout_editor', '');
		}

		if( $editorType == 'bbcode' )
		{

			$query  = 'update `#__discuss_posts` set `content_type` = ' . $db->Quote('bbcode');
			$query  .= ' where `content_type` is null';

			$db->setQuery($query);
			$db->query();
		}
		else
		{
			// replies
			$query  = 'update `#__discuss_posts` set `content_type` = ' . $db->Quote('bbcode');
			$query  .= ' where `content_type` is null';
			$query  .= ' and `parent_id` > 0';

			$db->setQuery($query);
			$db->query();

			// question
			$query  = 'update `#__discuss_posts` set `content_type` = ' . $db->Quote('html');
			$query  .= ' where `content_type` is null';
			$query  .= ' and `parent_id` = 0';

			$db->setQuery($query);
			$db->query();
		}
	}


	public function migrateJomSocialStreamNameSpace()
	{
		jimport('joomla.filesystem.file');
		jimport('joomla.filesystem.folder');

		$jsCoreFile	= JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_community'.DIRECTORY_SEPARATOR.'community.php';

		if(JFile::exists( $jsCoreFile ))
		{
			$db = self::getDbo();

			$query  = 'UPDATE `#__community_activities` SET `app` = ' . $db->Quote( 'easydiscuss' ) . ' WHERE `app` = ' . $db->Quote( 'com_easydiscuss' );

			$db->setQuery($query);
			$db->query();
		}
	}

	public static function removeAdminMenu()
	{
		$query	= '	DELETE FROM `#__menu` WHERE link LIKE \'%com_easydiscuss%\' AND client_id = \'1\'';

		$db = self::getDbo();
		$db->setQuery($query);
		return $db->query();
	}

	private function getJoomlaVersion()
	{
		$jVerArr	= explode('.', JVERSION);
		$jVersion	= $jVerArr[0] . '.' . $jVerArr[1];

		return $jVersion;
	}

	public static function getJoomlaVersion2()
	{
		$jVerArr	= explode('.', JVERSION);
		$jVersion	= $jVerArr[0] . '.' . $jVerArr[1];

		return $jVersion;
	}

	private function setMessage( $msg, $type )
	{
		$this->messages[] = array( 'type' => strtolower($type), 'message' => $msg );
	}

	public static function fixMenuIds()
	{
		// only joomla 1.6 or above will be called this function

		$db 				= self::getDbo();
		$executeStepTwo    	= true;
		$element = 'COM_EASYDISCUSS';

		$query  = 'SELECT a.id';
		$query  .= ' FROM `#__menu` as a';
		$query  .= ' WHERE a.`parent_id` = 1';
		$query  .= ' AND a.`client_id` = 1';
		$query  .= ' AND a.`component_id` = 0';
		$query  .= ' AND a.`title` = ' . $db->quote( strtolower( $element ) );

		$db->setQuery($query);
		$invalidRow = $db->loadResult();

		if( $invalidRow )
		{
			//found invalid menu with component_id = 0.
			$query  = 'SELECT `extension_id`';
			$query  .= ' FROM `#__extensions`';
			$query  .= ' WHERE `element` = ' . $db->Quote($element);
			$query  .= ' AND `type` = ' . $db->Quote('component');
			$db->setQuery($query);

			$extension_id = $db->loadResult();

			//now we are ready to fix all the invalid admin menu
			$query  = 'UPDATE `#__menu` SET `component_id` = ' . $db->Quote($extension_id);
			$query  .= ' WHERE parent_id = 1';
			$query  .= ' AND client_id = 1';
			$query  .= ' AND component_id = 0';
			$query  .= ' AND `title` LIKE ' . $db->Quote( $element . '%');

			$db->setQuery($query);
			$db->query();

			$executeStepTwo = false;
		}

		if( $executeStepTwo )
		{
			$cid	= self::getDiscussId2();

			$query  = 'select `component_id` from `#__menu`';
			$query	.= ' where menutype = ' . $db->Quote('main');
			$query	.= ' and client_id = 1';
			$query	.= ' and title like ' . $db->Quote( $element . '%');
			$query  .= ' LIMIT 1';

			$db->setQuery($query);
			$result = $db->loadResult();

			if( !empty( $result ) )
			{
				if( $cid != $result )
				{
					// the compoent id is not match. update it.
					$query  = 'UPDATE `#__menu` SET `component_id` = ' . $db->Quote($cid);
					$query  .= ' WHERE menutype = ' . $db->Quote('main');;
					$query  .= ' AND client_id = 1';
					$query  .= ' AND component_id = ' . $db->Quote($result);
					$query  .= ' AND `title` LIKE ' . $db->Quote( $element . '%');

					$db->setQuery($query);
					$db->query();
				}
			}//end if
		}//end if steptwo
	}

	public function getMessages()
	{
		return $this->messages;
	}
}

class EasyDiscussDatabaseUpdate
{
	protected $db	= null;

	public function __construct( &$db )
	{
		$this->db = EasyDiscussInstaller::getDbo();

	}

	public function update()
	{
		if( !$this->isColumnExists( '#__discuss_posts' , 'category_id' ) )
		{
			$query = 'ALTER TABLE `#__discuss_posts` ADD `category_id` BIGINT( 20 ) UNSIGNED NOT NULL DEFAULT 1 AFTER `content` ';
			$this->db->setQuery($query);
			if( !$this->db->query() ) return false;
		}

		if( !$this->isColumnExists( '#__discuss_posts' , 'answered' ) )
		{
			$query = 'ALTER TABLE `#__discuss_posts` ADD `answered` TINYINT( 1 ) NULL DEFAULT 0';
			$this->db->setQuery($query);
			if( !$this->db->query() ) return false;

			$query = 'ALTER TABLE `#__discuss_posts` ADD INDEX `discuss_post_answered` ( `answered` )';
			$this->db->setQuery($query);
			if( !$this->db->query() ) return false;
		}

		if( !$this->isColumnExists( '#__discuss_posts' , 'params' ) )
		{
			$query = 'ALTER TABLE `#__discuss_posts` ADD `params` TEXT NULL';
			$this->db->setQuery($query);
			if( !$this->db->query() ) return false;
		}

		if( !$this->isColumnExists( '#__discuss_users' , 'latitude' ) )
		{
			$query	= 'ALTER TABLE `#__discuss_users` ADD `latitude` VARCHAR(255) NULL DEFAULT NULL';
			$this->db->setQuery( $query );
			if( !$this->db->query() ) return false;
		}

		if( !$this->isColumnExists( '#__discuss_users' , 'longitude' ) )
		{
			$query	= 'ALTER TABLE `#__discuss_users` ADD `longitude` VARCHAR(255) NULL DEFAULT NULL';
			$this->db->setQuery( $query );
			if( !$this->db->query() ) return false;
		}

		if( !$this->isColumnExists( '#__discuss_users' , 'location' ) )
		{
			$query	= 'ALTER TABLE `#__discuss_users` ADD `location` TEXT NOT NULL';
			$this->db->setQuery( $query );
			if( !$this->db->query() ) return false;
		}

		if( !$this->isColumnExists( '#__discuss_mailq' , 'ashtml' ) )
		{
			$query = 'ALTER TABLE `#__discuss_mailq` ADD `ashtml` tinyint(1) NOT NULL';
			$this->db->setQuery($query);
			if( !$this->db->query() ) return false;
		}

		if( !$this->isColumnExists( '#__discuss_notifications' , 'favicon' ) )
		{
			$query = 'ALTER TABLE `#__discuss_notifications` ADD `favicon` TEXT NOT NULL';
			$this->db->setQuery($query);
			if( !$this->db->query() ) return false;
		}

		if( !$this->isColumnExists( '#__discuss_notifications' , 'component' ) )
		{
			$query = 'ALTER TABLE `#__discuss_notifications` ADD `component` VARCHAR(255) NOT NULL';
			$this->db->setQuery($query);
			if( !$this->db->query() ) return false;
		}

		if( !$this->isIndexKeyExists('#__discuss_posts', 'discuss_post_category') )
		{
			//if this index key is not present, then its an upgrade from 1.1.1866

			$query = 'alter table `#__discuss_posts` add index `discuss_post_category` (`category_id`)';
			$this->db->setQuery($query);
			if( !$this->db->query() ) return false;

			$query = 'alter table `#__discuss_posts` add index `discuss_post_query1` (`published`, `parent_id`, `answered`, `id`)';
			$this->db->setQuery($query);
			if( !$this->db->query() ) return false;

			$query = 'alter table `#__discuss_posts` add index `discuss_post_query2` (`published`, `parent_id`, `answered`, `replied`)';
			$this->db->setQuery($query);
			if( !$this->db->query() ) return false;

			$query = 'alter table `#__discuss_posts` add index `discuss_post_query3` (`published`, `parent_id`, `category_id`, `created`)';
			$this->db->setQuery($query);
			if( !$this->db->query() ) return false;

			$query = 'alter table `#__discuss_posts` add index `discuss_post_query4` (`published`, `parent_id`, `category_id`, `id`)';
			$this->db->setQuery($query);
			if( !$this->db->query() ) return false;

			$query = 'alter table `#__discuss_posts` add index `discuss_post_query5` (`published`, `parent_id`, `created`)';
			$this->db->setQuery($query);
			if( !$this->db->query() ) return false;

			$query = 'alter table `#__discuss_posts` add index `discuss_post_query6` (`published`, `parent_id`, `id`)';
			$this->db->setQuery($query);
			if( !$this->db->query() ) return false;

			$query = 'alter table `#__discuss_votes` add index `discuss_user_id` (`user_id`)';
			$this->db->setQuery($query);
			if( !$this->db->query() ) return false;

			$query = 'alter table `#__discuss_category` add index `discuss_cat_mod_categories1` (`published`, `private`, `id`)';
			$this->db->setQuery($query);
			if( !$this->db->query() ) return false;

			$query = 'alter table `#__discuss_category` add index `discuss_cat_mod_categories2` (`published`, `private`, `ordering`)';
			$this->db->setQuery($query);
			if( !$this->db->query() ) return false;

			$query = 'alter table `#__discuss_tags` add index `discuss_tags_query1` (`published`, `id`)';
			$this->db->setQuery($query);
			if( !$this->db->query() ) return false;
		}

		if( !$this->isColumnExists( '#__discuss_users' , 'points' ) )
		{
			$query = 'ALTER TABLE `#__discuss_users` ADD `points` BIGINT DEFAULT 0 NOT NULL';
			$this->db->setQuery($query);
			if( !$this->db->query() ) return false;
		}

		if( !$this->isColumnExists( '#__discuss_users' , 'signature' ) )
		{
			$query = 'ALTER TABLE `#__discuss_users` ADD `signature` TEXT NOT NULL ';
			$this->db->setQuery($query);
			if( !$this->db->query() ) return false;
		}

		if( !$this->isColumnExists( '#__discuss_category' , 'params' ) )
		{
			$query	= 'ALTER TABLE `#__discuss_category` ADD `params` TEXT NOT NULL';
			$this->db->setQuery( $query );
			if( !$this->db->query() ) return false;
		}

		if( !$this->isColumnExists( '#__discuss_posts' , 'password' ) )
		{
			$query = 'ALTER TABLE `#__discuss_posts` ADD `password` TEXT NULL';
			$this->db->setQuery($query);
			if( !$this->db->query() ) return false;
		}

		if( !$this->isIndexKeyExists('#__discuss_posts', 'discuss_post_titlecontent') )
		{
			$query = 'ALTER TABLE `#__discuss_posts` ADD FULLTEXT `discuss_post_titlecontent` (`title`, `content`)';
			$this->db->setQuery($query);
			if( !$this->db->query() ) return false;

			$query = 'ALTER TABLE `#__discuss_tags` ADD FULLTEXT `discuss_tags_title` (`title`)';
			$this->db->setQuery($query);
			if( !$this->db->query() ) return false;
		}


		if( !$this->isIndexKeyExists('#__discuss_notifications', 'discuss_notification') )
		{

			$query = 'ALTER TABLE `#__discuss_notifications` ADD INDEX `discuss_notification` (`target`, `state`, `cid`, `created`, `id`)';
			$this->db->setQuery($query);
			if( !$this->db->query() ) return false;

			$query = 'ALTER TABLE `#__discuss_notifications` ADD INDEX `discuss_notification_created` (`created`)';
			$this->db->setQuery($query);
			if( !$this->db->query() ) return false;

			$query = 'ALTER TABLE `#__discuss_badges` ADD INDEX `discuss_badges_alias` (`alias`)';
			$this->db->setQuery($query);
			if( !$this->db->query() ) return false;

			$query = 'ALTER TABLE `#__discuss_badges` ADD INDEX `discuss_badges_published` (`published`)';
			$this->db->setQuery($query);
			if( !$this->db->query() ) return false;

			$query = 'ALTER TABLE `#__discuss_points` ADD INDEX `discuss_points_rule` (`rule_id`)';
			$this->db->setQuery($query);
			if( !$this->db->query() ) return false;

			$query = 'ALTER TABLE `#__discuss_points` ADD INDEX `discuss_points_published` (`published`)';
			$this->db->setQuery($query);
			if( !$this->db->query() ) return false;

			$query = 'ALTER TABLE `#__discuss_rules` ADD INDEX `discuss_rules_command` (`command` (255))';
			$this->db->setQuery($query);
			if( !$this->db->query() ) return false;

			$query = 'ALTER TABLE `#__discuss_ranks` ADD INDEX `discuss_ranks_range` (`start`, `end`)';
			$this->db->setQuery($query);
			if( !$this->db->query() ) return false;

			$query = 'ALTER TABLE `#__discuss_oauth` ADD INDEX `discuss_oauth_type` (`type`)';
			$this->db->setQuery($query);
			if( !$this->db->query() ) return false;

		}

		if( !$this->isColumnExists( '#__discuss_category' , 'container' ) )
		{
			$query = 'ALTER TABLE `#__discuss_category` ADD `container` TINYINT( 3 ) NOT NULL DEFAULT 0';
			$this->db->setQuery($query);
			if( !$this->db->query() ) return false;
		}

		if( !$this->isColumnExists('#__discuss_comments', 'sent') )
		{
			$query = 'ALTER TABLE `#__discuss_comments` ADD `sent` TINYINT( 1 ) NOT NULL DEFAULT 0';
			$this->db->setQuery($query);
			if( !$this->db->query() ) return false;
		}

		if( !$this->isColumnExists('#__discuss_comments', 'lft') )
		{
			$query = 'ALTER TABLE `#__discuss_comments` ADD `lft` INT( 11 ) NOT NULL DEFAULT 0';
			$this->db->setQuery($query);
			if( !$this->db->query() ) return false;
		}

		// if( !$this->isColumnExists('#__discuss_comments', 'parent_id') )
		// {
		// 	$query = ' ALTER TABLE `#__discuss_comments` ADD `parent_id` INT( 11 ) NOT NULL DEFAULT 0, '
		// 			. 'ADD `sent` TINYINT( 1 ) NOT NULL DEFAULT 0, '
		// 			. 'ADD `lft` INT( 11 ) NOT NULL DEFAULT 0, '
		// 			. 'ADD `rgt` INT( 11 ) NOT NULL DEFAULT 0 ';

		// 	$this->db->setQuery($query);
		// 	if( !$this->db->query() ) return false;
		// }

		if( !$this->isColumnExists('#__discuss_posts', 'lockdate') )
		{
			$query	= 'ALTER TABLE `#__discuss_posts` ADD `lockdate` DATETIME NOT NULL DEFAULT \'0000-00-00 00:00:00\' AFTER  `islock`';
			$this->db->setQuery($query);
			if( !$this->db->query() ) return false;
		}

		if( !$this->isColumnExists('#__discuss_polls', 'multiple_polls') )
		{
			$query	= 'ALTER TABLE `#__discuss_polls` ADD `multiple_polls` TINYINT( 1 ) NULL DEFAULT NULL';
			$this->db->setQuery($query);
			if( !$this->db->query() ) return false;
		}

		$query = 'SELECT COUNT(id) FROM `#__discuss_category_acl_item` WHERE `action` = ' . $this->db->quote('viewreply');
		$this->db->setQuery($query);
		$hasRow	= $this->db->loadResult();

		$query = 'SELECT COUNT(id) FROM `#__discuss_category_acl_item` WHERE `id` = ' . $this->db->quote(4);
		$this->db->setQuery($query);
		$hasId = $this->db->loadResult();
		if( !$hasRow && !$hasId )
		{
			$query = "INSERT INTO `#__discuss_category_acl_item`(`id`, `action`, `description`, `published`, `default`) VALUES ('4', 'viewreply', 'can view the category replies.', 1, 1);";
			$this->db->setQuery($query);
			if( !$this->db->query() ) return false;
		}

		// @since 3.0
		if( !$this->isColumnExists( '#__discuss_users' , 'edited' ) )
		{
			// #__discuss_users.`edited` column
			// #__discuss_users.`posts_read` column

			$query 	= 'ALTER TABLE ' . $this->db->nameQuote( '#__discuss_users' ) . ' ADD ' . $this->db->nameQuote( 'edited' ) . ' INT NOT NULL DEFAULT 0';
			$query .= ', ADD ' . $this->db->nameQuote( 'posts_read' ) . ' TEXT NULL';
			$this->db->setQuery( $query );
			if( !$this->db->query() ) return false;
		}

		if( !$this->isColumnExists( '#__discuss_votes' , 'session_id' ) )
		{
			// #__discuss_votes.`session_id` column

			$query 	= 'ALTER TABLE ' . $this->db->nameQuote( '#__discuss_votes' ) . ' ADD ' . $this->db->nameQuote( 'session_id' ) . ' VARCHAR( 200 ) DEFAULT NULL';
			$this->db->setQuery( $query );
			if( !$this->db->query() ) return false;
		}

		if( !$this->isColumnExists( '#__discuss_posts' , 'legacy' ) )
		{
			// #__discuss_posts.`legacy` column

			$query 	= 'ALTER TABLE ' . $this->db->nameQuote( '#__discuss_posts' ) . ' ADD ' . $this->db->nameQuote( 'legacy' ) . ' TINYINT(1) DEFAULT 1';
			$query .= ', ADD ' . $this->db->nameQuote( 'address' ) . ' TEXT NULL';
			$query .= ', ADD ' . $this->db->nameQuote( 'latitude' ) . ' VARCHAR(255) DEFAULT NULL';
			$query .= ', ADD ' . $this->db->nameQuote( 'longitude' ) . ' VARCHAR(255) DEFAULT NULL';
			$this->db->setQuery( $query );
			if( !$this->db->query() ) return false;

			$query 	= 'ALTER TABLE ' . $this->db->nameQuote( '#__discuss_posts' ) . ' ADD INDEX `unread_category_posts` (`published`, `parent_id`, `legacy`, `category_id`, `id`)';
			$this->db->setQuery( $query );
			if( !$this->db->query() ) return false;

		}

		if( !$this->isColumnExists( '#__discuss_acl' , 'public' ) )
		{
			// #__discuss_acl.`public` column

			$query 	= 'ALTER TABLE ' . $this->db->nameQuote( '#__discuss_acl' ) . ' ADD ' . $this->db->nameQuote( 'public' ) . ' TINYINT(1) DEFAULT 0';
			$this->db->setQuery( $query );
			if( !$this->db->query() ) return false;
		}

		if( $this->isIndexKeyExists('#__discuss_posts', 'discuss_post_parentid') )
		{
			// alter column definition on index #__discuss_acl.`discuss_post_parentid`

			$query 	= 'ALTER TABLE ' . $this->db->nameQuote( '#__discuss_posts' ) . ' DROP INDEX `discuss_post_parentid`';
			$this->db->setQuery( $query );

			if( $this->db->query() )
			{
				$query 	= 'ALTER TABLE ' . $this->db->nameQuote( '#__discuss_posts' ) . ' ADD INDEX `discuss_post_parentid` (`published`, `parent_id`)';
				$this->db->setQuery( $query );
				$this->db->query();
			}
		}

		if( !$this->isIndexKeyExists('#__discuss_customfields_rule', 'cf_rule_field_id') )
		{
			//indexes for custom fields related tables.

			$query 	= 'ALTER TABLE ' . $this->db->nameQuote( '#__discuss_customfields_rule' ) . ' ADD INDEX `cf_rule_field_id` (`field_id`)';
			$this->db->setQuery( $query );
			$this->db->query();

			$query 	= 'ALTER TABLE ' . $this->db->nameQuote( '#__discuss_customfields_rule' ) . ' ADD INDEX `cf_rule_acl_types` (`content_type`, `acl_id`, `content_id`)';
			$this->db->setQuery( $query );
			$this->db->query();


			$query 	= 'ALTER TABLE ' . $this->db->nameQuote( '#__discuss_customfields_value' ) . ' ADD INDEX `cf_value_field_id` (`field_id`)';
			$this->db->setQuery( $query );
			$this->db->query();

			$query 	= 'ALTER TABLE ' . $this->db->nameQuote( '#__discuss_customfields_value' ) . ' ADD INDEX `cf_value_field_post` (`field_id`, `post_id`)';
			$this->db->setQuery( $query );
			$this->db->query();

			$query 	= 'ALTER TABLE ' . $this->db->nameQuote( '#__discuss_customfields_value' ) . ' ADD INDEX `cf_value_field_postid` (`post_id`)';
			$this->db->setQuery( $query );
			$this->db->query();

		}

		if( !$this->isIndexKeyExists('#__discuss_polls', 'polls_posts') )
		{
			$query 	= 'ALTER TABLE ' . $this->db->nameQuote( '#__discuss_polls' ) . ' ADD INDEX `polls_posts` (`post_id`, `id`)';
			$this->db->setQuery( $query );
			$this->db->query();
		}

		if( !$this->isColumnExists('#__discuss_badges_users', 'custom') )
		{
			$query 	= 'ALTER TABLE ' . $this->db->nameQuote( '#__discuss_badges_users' ) . ' ADD `custom` TEXT NOT NULL';
			$this->db->setQuery( $query );
			$this->db->query();
		}

		if( !$this->isIndexKeyExists('#__discuss_comments', 'discuss_comment_post_created') )
		{
			$query 	= 'ALTER TABLE ' . $this->db->nameQuote( '#__discuss_comments' ) . ' ADD INDEX `discuss_comment_post_created` (`post_id`, `created`)';
			$this->db->setQuery( $query );
			$this->db->query();
		}

		if( !$this->isIndexKeyExists('#__discuss_posts', 'discuss_post_last_reply') )
		{
			$query 	= 'ALTER TABLE ' . $this->db->nameQuote( '#__discuss_posts' ) . ' ADD INDEX `discuss_post_last_reply` ( `parent_id`, `id` )';
			$this->db->setQuery( $query );
			$this->db->query();
		}

		if( !$this->isColumnExists('#__discuss_posts', 'content_type') )
		{
			$query	= 'ALTER TABLE ' . $this->db->nameQuote( '#__discuss_posts' ) . ' ADD ' . $this->db->nameQuote( 'content_type' ) . ' VARCHAR( 25 ) NULL';
			$this->db->setQuery($query);
			$this->db->query();


			//we need to do some data migration here
		}

		if( !$this->isColumnExists( '#__discuss_polls_users' , 'session_id' ) )
		{
			$query 	= 'ALTER TABLE ' . $this->db->nameQuote( '#__discuss_polls_users' ) . ' ADD ' . $this->db->nameQuote( 'session_id' ) . ' VARCHAR( 200 ) DEFAULT NULL';
			$this->db->setQuery( $query );
			$this->db->query();
		}

		if( !$this->isColumnExists( '#__discuss_polls_question' , 'locked' ) )
		{
			$query 	= 'ALTER TABLE ' . $this->db->nameQuote( '#__discuss_polls_question' ) . ' ADD ' . $this->db->nameQuote( 'locked' ) . ' TINYINT( 1 ) NULL DEFAULT 0';
			$this->db->setQuery( $query );
			$this->db->query();
		}

		if( !$this->isColumnExists( '#__discuss_users' , 'site' ) )
		{
			$query 	= 'ALTER TABLE ' . $this->db->nameQuote( '#__discuss_users' ) . ' ADD ' . $this->db->nameQuote( 'site' ) . ' TEXT';
			$this->db->setQuery( $query );
			$this->db->query();
		}

		if( !$this->isColumnExists( '#__discuss_posts' , 'post_status' ) )
		{
			$query = 'ALTER TABLE `#__discuss_posts` ADD `post_status` TINYINT( 1 ) NOT NULL DEFAULT 0';
			$this->db->setQuery( $query );
			$this->db->query();
		}

		if( !$this->isColumnExists( '#__discuss_posts' , 'post_type' ) )
		{
			$query = 'ALTER TABLE `#__discuss_posts` ADD `post_type` VARCHAR( 255 ) NOT NULL, add index `idx_post_type` ( `post_type` )';
			$this->db->setQuery( $query );
			$this->db->query();
		}


		// since 3.2
		// below is the fix the wrong post_type implementation in 3.1
		if( !$this->isIndexKeyExists( '#__discuss_post_types' , 'idx_alias' ) )
		{
			$query = 'ALTER TABLE `#__discuss_posts` modify `post_type` VARCHAR( 255 ) NOT NULL, add index `idx_post_type` ( `post_type` )';
			$this->db->setQuery( $query );
			$this->db->query();

			$query = 'ALTER TABLE `#__discuss_post_types` modify `title` VARCHAR( 255 ) NOT NULL, add index `idx_alias` ( `alias` )';
			$this->db->setQuery( $query );
			$this->db->query();
		}


		if( !$this->isColumnExists( '#__discuss_posts' , 'ip' ) )
		{
			$query = 'ALTER TABLE `#__discuss_posts` ADD `ip` VARCHAR( 255 ) NOT NULL';
			$this->db->setQuery( $query );
			$this->db->query();
		}

		if( !$this->isColumnExists( '#__discuss_users_history' , 'content_id' ) )
		{
			$query = 'ALTER TABLE `#__discuss_users_history` ADD `content_id` BIGINT( 20 ) UNSIGNED NOT NULL';
			$this->db->setQuery( $query );
			$this->db->query();
		}

		if( !$this->isIndexKeyExists('#__discuss_migrators', 'idx_external_id') )
		{
			$query 	= 'ALTER TABLE ' . $this->db->nameQuote( '#__discuss_migrators' ) . ' ADD INDEX `idx_external_id` (`external_id`)';
			$this->db->setQuery( $query );
			$this->db->query();
		}

		if( !$this->isColumnExists('#__discuss_posts', 'private') )
		{
			$query	= 'ALTER TABLE `#__discuss_posts` ADD `private` TINYINT(3) NOT NULL DEFAULT "0" COMMENT "Determines if the discussion should be a private ticket or not." , ADD INDEX (`private`) ;';
			$this->db->setQuery($query);

			if (!$this->db->query()) {
				return false;
			}
		}

		if( !$this->isIndexKeyExists('#__discuss_favourites', 'idx_fav_postid') )
		{
			$query 	= 'ALTER TABLE ' . $this->db->nameQuote( '#__discuss_favourites' ) . ' ADD INDEX `idx_fav_postid` (`post_id`)';
			$this->db->setQuery( $query );
			$this->db->query();
		}

		if( !$this->isIndexKeyExists('#__discuss_posts', 'idx_post_replied') )
		{
			$query 	= 'ALTER TABLE ' . $this->db->nameQuote( '#__discuss_posts' ) . ' ADD INDEX `idx_post_replied` (`replied`)';
			$this->db->setQuery( $query );
			$this->db->query();

			$query 	= 'ALTER TABLE ' . $this->db->nameQuote( '#__discuss_posts' ) . ' ADD INDEX `idx_post_created` (`created`)';
			$this->db->setQuery( $query );
			$this->db->query();

			$query 	= 'ALTER TABLE ' . $this->db->nameQuote( '#__discuss_posts' ) . ' ADD INDEX `idx_post_private` (`private`)';
			$this->db->setQuery( $query );
			$this->db->query();

			$query 	= 'ALTER TABLE ' . $this->db->nameQuote( '#__discuss_posts' ) . ' ADD INDEX `idx_post_search1` (`published`, `parent_id`, `private`, `replied`)';
			$this->db->setQuery( $query );
			$this->db->query();

			$query 	= 'ALTER TABLE ' . $this->db->nameQuote( '#__discuss_posts' ) . ' ADD INDEX `idx_post_search2` (`published`, `private`, `created`)';
			$this->db->setQuery( $query );
			$this->db->query();

			$query 	= 'ALTER TABLE ' . $this->db->nameQuote( '#__discuss_posts' ) . ' ADD INDEX `idx_post_search1a` (`published`, `parent_id`, `private`)';
			$this->db->setQuery( $query );
			$this->db->query();

			$query 	= 'ALTER TABLE ' . $this->db->nameQuote( '#__discuss_posts' ) . ' ADD INDEX `idx_post_search2a` (`published`, `private`)';
			$this->db->setQuery( $query );
			$this->db->query();
		}

		if( !$this->isIndexKeyExists('#__discuss_posts', 'idx_user_replies') )
		{
			$query 	= 'ALTER TABLE ' . $this->db->nameQuote( '#__discuss_posts' ) . ' ADD INDEX `idx_user_replies` (`user_id`, `published`, `parent_id`)';
			$this->db->setQuery( $query );
			$this->db->query();
		}

		if( !$this->isIndexKeyExists('#__discuss_posts', 'idx_post_cat_published') )
		{
			$query 	= 'ALTER TABLE ' . $this->db->nameQuote( '#__discuss_posts' ) . ' ADD INDEX `idx_post_cat_published` (`category_id`, `published`)';
			$this->db->setQuery( $query );
			$this->db->query();
		}

		if( !$this->isIndexKeyExists('#__discuss_mailq', 'idx_mailq_pending') )
		{
			$query 	= 'ALTER TABLE ' . $this->db->nameQuote( '#__discuss_mailq' ) . ' ADD INDEX `idx_mailq_pending` (`status`, `created`)';
			$this->db->setQuery( $query );
			$this->db->query();
		}

		if( !$this->isIndexKeyExists('#__discuss_category', 'idx_cat_childs') )
		{
			$query 	= 'ALTER TABLE ' . $this->db->nameQuote( '#__discuss_category' ) . ' ADD INDEX `idx_cat_childs` (`parent_id`, `published`, `lft`)';
			$this->db->setQuery( $query );
			$this->db->query();
		}

		return true;
	}

	private function isTableExists( $tableName )
	{
		$query	= 'SHOW TABLES LIKE ' . $this->db->quote($tableName);
		$this->db->setQuery( $query );

		return (boolean) $this->db->loadResult();
	}

	private function isColumnExists( $tableName, $columnName )
	{
		$query	= 'SHOW FIELDS FROM ' . $this->db->nameQuote( $tableName );
		$this->db->setQuery( $query );

		$fields	= $this->db->loadObjectList();

		$result = array();

		if( !empty($fields) )
		{
			foreach( $fields as $field )
			{
				$result[ $field->Field ]	= preg_replace( '/[(0-9)]/' , '' , $field->Type );
			}
		}

		if( array_key_exists($columnName, $result) )
		{
			return true;
		}

		return false;
	}

	private function isIndexKeyExists( $tableName, $indexName )
	{
		$query	= 'SHOW INDEX FROM ' . $this->db->nameQuote( $tableName );
		$this->db->setQuery( $query );
		$indexes	= $this->db->loadObjectList();

		$result = array();

		foreach( $indexes as $index )
		{
			$result[ $index->Key_name ]	= preg_replace( '/[(0-9)]/' , '' , $index->Column_name );
		}

		if( array_key_exists($indexName, $result) )
		{
			return true;
		}

		return false;
	}

	private function getJoomlaVersion()
	{
		$jVerArr	= explode('.', JVERSION);
		$jVersion	= $jVerArr[0] . '.' . $jVerArr[1];

		return $jVersion;
	}
}
