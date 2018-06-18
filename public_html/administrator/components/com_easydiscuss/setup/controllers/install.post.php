<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 - 2018 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

require_once(__DIR__ . '/controller.php');

class EasyDiscussControllerInstallPost extends EasyDiscussSetupController
{
	/**
	 * Post installation process
	 *
	 * @since	4.2.0
	 * @access	public
	 */
	public function execute()
	{
		$results = array();

		// Get the api key so that we can store it
		$key = $this->input->get('apikey', '', 'default');

		// Skip this when we are on development mode
		if ($this->isDevelopment()) {
			return $this->output($this->getResultObj('COM_EASYDISCUSS_INSTALLATION_DEVELOPER_MODE', true));
		}

		// Update the api key
		$this->updateConfig('main_apikey', ED_KEY);
		
		// ACL rules needs to be created first before anything else
		$results[] = $this->updateACL();

		// Create site menu
		$results[] = $this->createDefaultMenu('site');

		// created tag
		$results[] = $this->createDefaultTags();

		// Create category
		$results[] = $this->createDefaultCategory();

		// create badges
		$results[] = $this->createDefaultBadges();

		// create priorities
		$results[] = $this->createDefaultPriorities();

		// create badge rules
		$results[] = $this->createDefaultBadgeRules();

		// create points
		$results[] = $this->createDefaultPoints();

		// create post type
		$results[] = $this->createDefaultPostTypes();

		// Create sample post
		$results[] = $this->createSamplePost();

		// Update the manifest_cache in #__extensions table
		$this->updateManifestCache();

		// Delete the Easydiscuss from the Updates table
		$this->deleteUpdateRecord();

		// Update the toolbar colors
		$this->updateToolbarColors();

		// Change the default value for new features and theme
		// Only for 3.x-4.0
		if ($this->isUpgradeFrom3x()) {

			// update default values
			$this->changeDefaultValue();
		}

		$message = '';

		foreach ($results as $obj) {

			if ($obj === false) {
				continue;
			}

			$class = $obj->state ? 'success' : 'error';
			$message .= '<div class="text-' . $class . '">' . $obj->message . '</div>';
		}

		$this->setInfo($message, true);
		return $this->output();
	}

	public function isUpgradeFrom3x()
	{
		$db = ED::db();

		$query = array();
		$query[] = 'SELECT ' . $db->quoteName('params') . ' FROM ' . $db->quoteName('#__discuss_configs');
		$query[] = 'WHERE ' . $db->quoteName('name') . '=' . $db->Quote('scriptversion');
		$query = implode(' ', $query);

		$db->setQuery($query);
		$scriptversion = $db->loadResult();

		if ((int)$scriptversion == 3) {
			return true;
		} 
		
		return false;
	}

	public function changeDefaultValue()
	{
		$this->engine();

		// Skip this when we are on development mode
		if ($this->isDevelopment()) {
			return;
		}

		$model = ED::model('Settings');

		$configData = $model->_getParams();

		$registry = new JRegistry();
		$registry->loadString($configData);

		// If the user is updating from the previous version,
		// we need to turn off the features by default.
		$registry->set('main_mentions', 0);
		$registry->set('post_priority', 0);
		$registry->set('main_ban', 0);
		$registry->set('main_hits_session', 0);
		$registry->set('main_ratings', 0);
		$registry->set('main_work_schedule', 0);
		$registry->set('integrations_github', 0);
		$registry->set('main_anonymous_posting', 0);

		// Now we update the default theme
		// remove themes as simplistic no longer support
		$folders = array(
						JPATH_ROOT . '/components/com_easydiscuss/themes/simplistic'
					);

		// Go through each folders and remove them
		foreach ($folders as $folder) {
			$exists = JFolder::exists($folder);

			if ($exists) {
				JFolder::delete($folder);
			}
		}

		$currentTheme = $registry->get('layout_site_theme', '');

		if ($currentTheme == 'simplistic') {
			$registry->set('layout_site_theme', 'wireframe');
			$registry->set('layout_site_theme_base', 'wireframe');
		}

		$config = ED::table('Configs');
		$config->load('config');

		$config->name = 'config';
		$config->params	= $registry->toString('INI');

		$config->store();

		return true;
	}

	/**
	 * Retrieves the main menu item
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function getMainMenuType()
	{
		$this->engine();

		$db = ED::db();

		$query = array();
		$query[] = 'SELECT ' . $db->quoteName('menutype') . ' FROM ' . $db->quoteName('#__menu');
		$query[] = 'WHERE ' . $db->quoteName('home') . '=' . $db->Quote(1);
		$query = implode(' ', $query);

		$db->setQuery($query);
		$menuType = $db->loadResult();

		return $menuType;
	}


	public function createDefaultPoints()
	{
		$this->engine();

		// Skip this when we are on development mode
		if ($this->isDevelopment()) {
			return false;
		}

		$db = ED::db();

		$query	= 'SELECT COUNT(1) FROM ' . $db->nameQuote( '#__discuss_points' );

		$db->setQuery( $query );
		$count = $db->loadResult();

		if ($count > 0) {
			return false;
		}


		$query	= 'INSERT INTO ' . $db->nameQuote( '#__discuss_points' )
				. ' (' . $db->nameQuote( 'id' ) . ', ' . $db->nameQuote( 'rule_id' ) . ', ' . $db->nameQuote( 'title' ) . ', '
				. $db->nameQuote( 'created' ) . ', ' . $db->nameQuote( 'published' ) . ', ' . $db->nameQuote( 'rule_limit' ) . ') VALUES'
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
				. ' ("19", "19", "Unvote a reply", NOW(), "1", "-1"),'
				. ' ("20", "20", "Remove a question", NOW(), "1", "-1"),'
				. ' ("21", "21", "Reply rejected as answer", NOW(), "1", "-1"),'
				. ' ("22", "22", "Like your reply", NOW(), "1", "1"),'
				. ' ("23", "23", "Unlike your reply", NOW(), "1", "-1"),'
				. ' ("24", "24", "Like your discussion", NOW(), "1", "1"),'
				. ' ("25", "25", "Unlike your discussion", NOW(), "1", "-1")';

		$db->setQuery( $query );

		$db->query();

		return $this->getResultObj(JText::_('COM_EASYDISCUSS_INSTALLATION_DEFAULT_POINTS_CREATED'), true );


	}



	public function createDefaultBadgeRules()
	{
		$this->engine();

		// Skip this when we are on development mode
		if ($this->isDevelopment()) {
			return false;
		}

		$db = ED::db();

		$query	= 'INSERT IGNORE INTO ' . $db->nameQuote( '#__discuss_rules' )
				. ' (' . $db->nameQuote( 'id' ) . ', ' . $db->nameQuote( 'command' ) . ', ' . $db->nameQuote( 'title' ) . ', ' . $db->nameQuote( 'description' ) . ', '
				. $db->nameQuote( 'callback' ) . ', ' . $db->nameQuote( 'created' ) . ', ' . $db->nameQuote( 'published' ) . ') VALUES'
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
				. ' ("20", "easydiscuss.remove.discussion", "Remove a discussion", "This rule allows you to assign a badge or points for a user when they remove a discussion.", "", NOW(), "1"),'
				. ' ("21", "easydiscuss.rejectanswer.reply", "Reply rejected as answer", "This rule allows you to deduct points for a user when their accepted answer has been rejected.", "", NOW(), "1"),'
				. ' ("22", "easydiscuss.reply.like", "Like your reply", "This rule allows a user to gain points when someone like their own replies.", "", NOW(), "1"),'
				. ' ("23", "easydiscuss.reply.unlike", "Unlike your reply", "This rule allows a user points to be deducted when someone unlike their own replies.", "", NOW(), "1"),'
				. ' ("24", "easydiscuss.discussion.like", "Like your discussion", "This rule allows a user to gain points when someone like their own discussion post.", "", NOW(), "1"),'
				. ' ("25", "easydiscuss.discussion.unlike", "Unlike your discussion", "This rule allows a points to be deducted when someone unlike their own discussion post.", "", NOW(), "1")';

		$db->setQuery( $query );
		$db->query();

		return $this->getResultObj(JText::_('COM_EASYDISCUSS_INSTALLATION_DEFAULT_BADGE_RULES_CREATED'), true );

	}




	public function createDefaultBadges()
	{
		$this->engine();

		// Skip this when we are on development mode
		if ($this->isDevelopment()) {
			return false;
		}

		$db = ED::db();

		$query	= 'SELECT COUNT(1) FROM ' . $db->nameQuote( '#__discuss_badges' );

		$db->setQuery( $query );
		$count = $db->loadResult();

		if ($count > 0) {
			return false;
		}

		$query	= 'INSERT INTO ' . $db->nameQuote( '#__discuss_badges' )
				. ' (' . $db->nameQuote( 'id' ) . ', ' . $db->nameQuote( 'rule_id' ) . ', ' . $db->nameQuote( 'title' ) . ', ' . $db->nameQuote( 'description' ) . ', '
				. $db->nameQuote( 'avatar' ) . ', ' . $db->nameQuote( 'created' ) . ', ' . $db->nameQuote( 'published' ) . ', ' . $db->nameQuote( 'rule_limit' ) . ', ' . $db->nameQuote( 'alias' ) . ') VALUES'
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
				. ' ("11", "11", "Firestarter", "Posted 100 comments.", "firestarter.png", NOW(), "1", "100", "firestarter"),'
				. ' ("12", "22", "Notable Answerer", "User likes your replies 100 times.", "reply-like.png", NOW(), "1", "100", "notable-answerer"),'
				. ' ("13", "24", "Well-Known Questionnaire", "User likes your dicussions 50 times.", "discuss-like.png", NOW(), "1", "50", "well-known-questionnaire")';

		$db->setQuery( $query );
		$db->query();


		return $this->getResultObj(JText::_('COM_EASYDISCUSS_INSTALLATION_DEFAULT_BADGES_CREATED'), true );

	}

	public function createDefaultPriorities()
	{
		$this->engine();

		// Skip this when we are on development mode
		if ($this->isDevelopment()) {
			return false;
		}

		$db = ED::db();
		$query = 'SELECT COUNT(1) FROM ' . $db->nameQuote('#__discuss_priorities');

		$db->setQuery($query);
		$count = $db->loadResult();

		if ($count > 0) {
			return false;
		}

		$query = 'INSERT INTO ' . $db->nameQuote("#__discuss_priorities")
				. ' (' . $db->nameQuote('id') . ', ' . $db->nameQuote('title') . ', ' . $db->nameQuote('color') . ', ' . $db->nameQuote('created') . ') VALUES'
				. ' ("1", "Low", "#888888", NOW()),'
				. ' ("2", "Normal", "#229209", NOW()),'
				. ' ("3", "Critical", "#ff0000", NOW())';

		$db->setQuery($query);
		$db->query();

		return $this->getResultObj(JText::_('COM_EASYDISCUSS_INSTALLATION_DEFAULT_POST_PRIORITIES_CREATED'), true );
	}

	public function createDefaultPostTypes()
	{
		$this->engine();

		// Skip this when we are on development mode
		if ($this->isDevelopment()) {
			return false;
		}

		$db = ED::db();

		$query	= 'SELECT COUNT(1) FROM ' . $db->nameQuote( '#__discuss_post_types' );

		$db->setQuery( $query );
		$count = $db->loadResult();

		if ($count > 0) {
			return false;
		}


		$query	= 'INSERT INTO ' . $db->nameQuote( '#__discuss_post_types' )
				. ' (' . $db->nameQuote( 'id' ) . ', ' . $db->nameQuote( 'title' ) . ', ' . $db->nameQuote( 'suffix' ) . ', ' . $db->nameQuote( 'created' ) . ', ' . $db->nameQuote( 'published' ) . ', ' . $db->nameQuote( 'alias' ) . ') VALUES'
				. ' ("1", "Bug", "", NOW(), "1", "bug"),'
				. ' ("2", "Issue", "", NOW(), "1", "issue"),'
				. ' ("3", "Task", "", NOW(), "1", "task")';

		$db->setQuery( $query );
		$db->query();

		return $this->getResultObj(JText::_('COM_EASYDISCUSS_INSTALLATION_DEFAULT_POST_TYPES_CREATED'), true );

	}

	/**
	 * Retrieves the extension_id from Joomla's extension table
	 *
	 * @since	4.0.12
	 * @access	public
	 */
	public function getExtensionId()
	{
		$this->engine();

		$db = JFactory::getDBO();

		$query = array();
		$query[] = 'SELECT ' . $db->quoteName('extension_id') . ' FROM ' . $db->quoteName('#__extensions');
		$query[] = 'WHERE ' . $db->quoteName('element') . '=' . $db->Quote('com_easydiscuss');
		$query = implode(' ', $query);

		$db->setQuery($query);

		// Get the extension id
		$extensionId = $db->loadResult();

		return $extensionId;
	}

	/**
	 * Update the manifest cache
	 *
	 * @since   4.2.0
	 * @access  public
	 */
	public function updateManifestCache()
	{	
		$db = JFactory::getDBO();
		$extensionId = $this->getExtensionId();
		$manifest_details = JInstaller::parseXMLInstallFile(JPATH_ROOT. '/administrator/components/com_easydiscuss/easydiscuss.xml');
		$manifest = json_encode($manifest_details);

		// For some Joomla versions, there is no tables/Extension.php
		// Hence, the JTable::getInstance('Extension') will return null
		$table = JTable::getInstance('Extension');

		if ($table) {
			$exists = $table->load($extensionId);

			if (!$exists) {
				return false;
			}

			$table->manifest_cache = $manifest;
			$table->store();
		} else {
			$query	= 'UPDATE '. $db->quoteName('#__extensions')
					. ' SET ' . $db->quoteName('manifest_cache') . ' = ' . $db->Quote($manifest)
					. ' WHERE ' . $db->quoteName('extension_id') . ' = ' . $db->Quote($extensionId);
			$db->setQuery($query);
			$db->query();
		}
	}

	/**
	 * Delete record in updates table
	 *
	 * @since   4.2.0
	 * @access  public
	 */
	public function deleteUpdateRecord()
	{
		$db = JFactory::getDBO();

		$query = 'DELETE FROM ' . $db->quoteName('#__updates') . ' WHERE ' . $db->quoteName('extension_id') . '=' . $db->Quote($this->getExtensionId());
		$db->setQuery($query);
		$db->Query();
	}

	/**
	 * Create a new default blog menu
	 *
	 * @since	4.2.0
	 * @access	public
	 */
	public function createDefaultMenu()
	{
		// Include foundry framework
		$this->engine();

		// Skip this when we are on development mode
		if ($this->isDevelopment()) {
			return false;
		}

		$db = ED::db();

		// Get the extension id
		$extensionId = $this->getExtensionId();

		// Get the main menu that is used on the site.
		$menuType = $this->getMainMenuType();

		if (!$menuType) {
			return false;
		}

		// Get any menu items that are already created with com_easydiscuss
		$query = array();
		$query[] = 'SELECT COUNT(1) FROM ' . $db->quoteName('#__menu');
		$query[] = 'WHERE ' . $db->quoteName('link') . ' LIKE(' . $db->Quote('%index.php?option=com_easydiscuss%') . ')';
		$query[] = 'AND ' . $db->quoteName('type') . '=' . $db->Quote('component');
		$query[] = 'AND ' . $db->quoteName('client_id') . '=' . $db->Quote(0);

		$query = implode(' ', $query);
		$db->setQuery($query);

		$exists	= $db->loadResult();

		// If menu already exists, we need to ensure that all the existing menu's are now updated with the correct extension id
		if ($exists) {

			$query = array();
			$query[] = 'UPDATE ' . $db->quoteName('#__menu') . ' SET ' . $db->quoteName('component_id') . '=' . $db->Quote($extensionId);
			$query[] = 'WHERE ' . $db->quoteName('link') . ' LIKE (' . $db->Quote('%index.php?option=com_easydiscuss%') . ')';
			$query[] = 'AND ' . $db->quoteName('type') . '=' . $db->Quote('component');
			$query[] = 'AND ' . $db->quoteName('client_id') . '=' . $db->Quote(0);

			$query = implode(' ', $query);
			$db->setQuery($query);
			$db->Query();

			return $this->getResultObj(JText::_('COM_EASYDISCUSS_INSTALLATION_SITE_MENU_UPDATED'), true);
		}

		$menu = JTable::getInstance('Menu');
		$menu->menuType = $menuType;
		$menu->title = JText::_('COM_EASYDISCUSS_INSTALLATION_DEFAULT_MENU_DISCUSSIONS');
		$menu->alias = 'discussions';
		$menu->path = 'discussions';
		$menu->link = 'index.php?option=com_easydiscuss&view=index';
		$menu->type = 'component';
		$menu->published = 1;
		$menu->parent_id = 1;
		$menu->component_id = $extensionId;
		$menu->client_id = 0;
		$menu->language = '*';

		$menu->setLocation('1', 'last-child');

		$state 	= $menu->store();

		return $this->getResultObj(JText::_('COM_EASYDISCUSS_INSTALLATION_SITE_MENU_CREATED'), true );
	}

	private function createDefaultTags()
	{
		$this->engine();

		// Skip this when we are on development mode
		if ($this->isDevelopment()) {
			return false;
		}

		$db = ED::db();

		$query	= 'SELECT COUNT(*) FROM ' . $db->nameQuote( '#__discuss_tags' );
		$db->setQuery( $query );

		$count = $db->loadResult();

		if ($count > 0) {
			return false;
		}


		$suAdmin	= JFactory::getUser()->id;

		$query		= 'INSERT INTO `#__discuss_tags` ( `title`, `alias`, `created`, `published`, `user_id`) '
					. 'VALUES ( "General", "general", now(), 1, ' . $db->Quote($suAdmin) .' ), '
					. '( "Automotive", "automotive", now(), 1, ' . $db->Quote($suAdmin) .' ), '
					. '( "Sharing", "sharing", now(), 1, ' . $db->Quote($suAdmin) .' ), '
					. '( "Info", "info", now(), 1, ' . $db->Quote($suAdmin) .' ), '
					. '( "Discussions" , "discussions" , now() , 1 , ' . $db->Quote( $suAdmin ) . ')';

		$db->setQuery( $query );

		return $this->getResultObj(JText::_('COM_EASYDISCUSS_INSTALLATION_DEFAULT_TAGS_CREATED'), true );

	}

	private function createSamplePost()
	{
		$this->engine();

		// Skip this when we are on development mode
		if ($this->isDevelopment()) {
			return false;
		}

		$db = ED::db();

		$query	= 'SELECT COUNT(1) FROM ' . $db->nameQuote( '#__discuss_posts' ) . ' LIMIT 1';
		$db->setQuery( $query );

		$count = $db->loadResult();

		if ($count > 0) {
			return false;
		}

		$suAdmin	= JFactory::getUser()->id;

		$content = array();
		$content['thankyou'] = 'Thank you for choosing EasyDiscuss as your preferred discussion tool for your Joomla! website. We hope you find it useful in achieving your needs.';
		$content['congratulation'] = 'Congratulations! You have successfully installed EasyDiscuss and ready to post your first question!';

		$query		= 'INSERT IGNORE INTO `#__discuss_posts` ( `id`, `title`, `alias`, `created`, `modified`, `replied`, `content`, `published`, `featured`, `isresolve`, `user_id`, `parent_id`, `user_type`, `thread_id`, `preview`, `content_type`) '
					. 'VALUES ( 1, "Thank you for choosing EasyDiscuss", "thank-you-for-choosing-easydiscuss", now(), now(), now(), ' . $db->Quote($content['thankyou']) . ', 1, 1, 1,' . $db->Quote($suAdmin) .', 0, "member", 1, ' . $db->Quote($content['thankyou']) . ', "bbcode" ), '
					. '( "2", "Congratulations! You have successfully installed EasyDiscuss", "congratulations-succesfully-installed-easydiscuss", now(), now() , now(), ' . $db->Quote($content['congratulation']) . ', 1, 0, 1,' . $db->Quote($suAdmin) .', 0, "member", 2, ' . $db->Quote($content['congratulation']) . ', "bbcode"  ) ';

		$db->setQuery($query);
		$state = $db->query();

		if ($state) {

				// insert into thread table.
			$query		= 'INSERT IGNORE INTO `#__discuss_thread` ( `id`, `title`, `alias`, `created`, `modified`, `replied`, `content`, `published`, `featured`, `isresolve`, `user_id`, `user_type`, `post_id`, `preview`, `content_type`) '
						. 'VALUES ( 1, "Thank you for choosing EasyDiscuss", "thank-you-for-choosing-easydiscuss", now(), now(), now(), ' . $db->Quote($content['thankyou']) . ', 1, 1, 1,' . $db->Quote($suAdmin) .', "member", 1, ' . $db->Quote($content['thankyou']) . ', "bbcode" ), '
						. '( "2", "Congratulations! You have successfully installed EasyDiscuss", "congratulations-succesfully-installed-easydiscuss", now(), now() , now(), ' . $db->Quote($content['congratulation']) . ', 1, 0, 1,' . $db->Quote($suAdmin) .', "member", 2, ' . $db->Quote($content['congratulation']) . ', "bbcode" ) ';

			$db->setQuery($query);
			$db->query();


			// Create tag for sample post
			$query		= 'INSERT IGNORE INTO `#__discuss_tags` ( `id`, `title`, `alias`, `created`, `published`, `user_id`) '
						. 'VALUES ( "6", "Thank You", "thank-you", now(), 1, ' . $db->Quote($suAdmin) .' ), '
						. '( "7", "Congratulations", "congratulations", now(), 1, ' . $db->Quote($suAdmin) .' ) ';
			$db->setQuery( $query );
			$db->query();

			// Create posts tags records
			$query		= 'INSERT INTO `#__discuss_posts_tags` ( `post_id`, `tag_id`) '
						. 'VALUES ( "1", "6" ), '
						. '( "2", "7" ) ';
			$db->setQuery( $query );
			$db->query();

		}

		return $this->getResultObj(JText::_('COM_EASYDISCUSS_INSTALLATION_SAMPLE_POST_CREATED'), true );

	}

	/**
	 * Create a default category for the site
	 *
	 * @since	4.2.0
	 * @access	public
	 */
	public function createDefaultCategory()
	{
		$this->engine();

		// Skip this when we are on development mode
		if ($this->isDevelopment()) {
			return false;
		}

		// Check if there are already categories created
		$db = ED::db();

		$query = 'SELECT COUNT(1) FROM ' . $db->quoteName('#__discuss_category');
		$db->setQuery($query);
		$total = $db->loadResult();

		if ($total > 0) {
			return false;
		}

		$my = JFactory::getUser();

		$category = ED::table('Category');
		$category->title = 'Uncategorized';
		$category->alias = 'uncategorized';
		$category->created_by = $my->id;
		$category->created = ED::date()->toSql();
		$category->status = true;
		$category->published = 1;
		$category->ordering = 1;
		$category->lft = 1;
		$category->rgt = 2;
		$category->default = 1;
		$category->private = 2;

		$state = $category->store();

		// now update the permission for this category.
		if ($state) {
			$model = ED::model('Category');
			$model->updateACL($category->id, array(), null, true);
		}

		return $this->getResultObj(JText::_('COM_EASYDISCUSS_INSTALLATION_DEFAULT_CATEGORY_CREATED'), true );
	}

	/**
	 * Update the ACL
	 *
	 * @since	4.2.0
	 * @access	public
	 */
	public function updateACL()
	{
		$this->engine();

		// Skip this when we are on development mode
		if ($this->isDevelopment()) {
			return false;
		}

		$db = ED::db();

		// Intelligent fix to delete all records from the #__discuss_acl_group when it contains ridiculous amount of entries
		$query = 'SELECT COUNT(1) FROM ' . $db->nameQuote('#__discuss_acl_group');
		$db->setQuery($query);

		$total = $db->loadResult();

		if ($total > 20000) {
			$query = 'DELETE FROM ' . $db->nameQuote('#__discuss_acl_group');
			$db->setQuery($query);
			$db->Query();
		}

		// First, remove all records from the acl table.
		$query = 'DELETE FROM ' . $db->nameQuote('#__discuss_acl');
		$db->setQuery($query);
		$db->query();

		// Get the list of acl
		$contents = JFile::read(DISCUSS_ADMIN_ROOT . '/defaults/acl.json');
		$acls = json_decode($contents);

		foreach ($acls as $acl) {

			$query = array();
			$query[] = 'INSERT INTO ' . $db->nameQuote('#__discuss_acl') . '(' . $db->nameQuote('id') . ',' . $db->nameQuote('action') . ',' . $db->nameQuote('group') . ',' . $db->nameQuote('description') . ',' . $db->nameQuote('public') . ',' . $db->nameQuote('default') . ',' . $db->nameQuote('published') . ')';
			$query[] = 'VALUES(' . $db->Quote($acl->id) . ',' . $db->Quote($acl->action) . ',' . $db->Quote($acl->group) . ',' . $db->Quote($acl->desc) . ',' . $db->Quote($acl->public) . ',' . $db->Quote($acl->default) . ',' . $db->Quote($acl->published) . ')';
			$query = implode(' ', $query);

			$db->setQuery($query);
			$db->Query();
		}

		// Once the acl is initialized, we need to create default values for all the existing groups on the site.
		$this->assignACL();

		return $this->getResultObj(JText::_('COM_EASYDISCUSS_INSTALLATION_ACL_INITIALIZED'), true);
	}

	/**
	 * Assign acl rules to existing Joomla groups
	 *
	 * @since	4.2.0
	 * @access	public
	 */
	public function assignACL()
	{
		$this->engine();

		// Get the db
		$db = ED::db();

		// Retrieve all user groups from the site
		$query = array();
		$query[] = 'SELECT a.' . $db->nameQuote('id') . ', a.' . $db->nameQuote('title') . ' AS ' . $db->nameQuote('name') . ', COUNT(DISTINCT b.' . $db->nameQuote('id') . ') AS ' . $db->nameQuote('level');
		$query[] = ', GROUP_CONCAT(b.' . $db->nameQuote('id') . ' SEPARATOR \',\') AS ' . $db->nameQuote('parents');
		$query[] = 'FROM ' . $db->nameQuote('#__usergroups') . ' AS a';
		$query[] = 'LEFT JOIN ' . $db->nameQuote('#__usergroups') . ' AS b';
		$query[] = 'ON a.' . $db->nameQuote('lft') . ' > b.'  . $db->nameQuote('lft');
		$query[] = 'AND a.' . $db->nameQuote('rgt') . ' < b.' . $db->nameQuote('rgt');
		$query[] = 'GROUP BY a.' . $db->nameQuote('id');
		$query[] = 'ORDER BY a.' . $db->nameQuote('lft') . ' ASC';

		$query = implode(' ', $query);
		$db->setQuery($query);

		// Default values
		$groups = array();
		$result = $db->loadColumn();

		// Get a list of default acls
		$query = array();
		$query[] = 'SELECT ' . $db->nameQuote('id') . ' FROM ' . $db->nameQuote('#__discuss_acl');
		$query[] = 'ORDER BY ' . $db->nameQuote('id') . ' ASC';

		$query = implode(' ', $query);
		$db->setQuery($query);

		// Get those acls
		$installedAcls = $db->loadColumn();

		// Default admin groups
		$adminGroups = array(7, 8);

		if (!empty($result)) {

			foreach ($result as $id) {

				$id = (int) $id;

				// Every other group except admins and super admins should only have restricted access
				if (in_array($id, $adminGroups)) {
					$groups[$id] = $installedAcls;
				} else {

					$allowedAcl = array();

					// Default guest / public group
					if ($id == 1 || $id == 9) {
						$allowedAcl = array(1, 2, 3, 4);
					} else {
						// other groups
						$allowedAcl = array(1, 2, 3, 4, 25, 26, 30);
					}

					$groups[$id] = $allowedAcl;
				}
			}
		}

		// Go through each groups now
		foreach ($groups as $groupId => $acls) {

			// Now we need to insert the acl rules
			$query = array();
			$insertQuery = array();
			$query[] = 'SELECT COUNT(1) FROM ' . $db->nameQuote('#__discuss_acl_group');
			$query[] = 'WHERE ' . $db->nameQuote('content_id') . '=' . $db->Quote($groupId);
			$query[] = 'AND ' . $db->nameQuote('type') . '=' . $db->Quote('group');

			$query = implode(' ', $query);

			$db->setQuery($query);
			$exists = $db->loadResult() > 0 ? true : false;

			// Reinitialize the query again.
			$query = 'INSERT INTO ' . $db->nameQuote('#__discuss_acl_group') . ' (' . $db->nameQuote('content_id') . ',' . $db->nameQuote('acl_id') . ',' . $db->nameQuote('status') . ',' . $db->nameQuote('type') . ') VALUES';

			if (!$exists) {

				foreach ($acls as $acl) {
					$insertQuery[] = '(' . $db->Quote($groupId) . ',' . $db->Quote($acl) . ',' . $db->Quote('1') . ',' . $db->Quote('group') . ')';
				}

				//now we need to get the unassigend acl and set it to '0';
				$disabledACLs = array_diff($installedAcls, $acls);

				if ($disabledACLs) {
					foreach ($disabledACLs as $disabledAcl) {
						$insertQuery[] = '(' . $db->Quote($groupId) . ',' . $db->Quote($disabledAcl) . ',' . $db->Quote('0') . ',' . $db->Quote('group') . ')';
					}
				}

			} else {

				// Get a list of acl that is already associated with the group
				$sub = array();
				$sub[] = 'SELECT ' . $db->nameQuote('acl_id') . ' FROM ' . $db->nameQuote('#__discuss_acl_group');
				$sub[] = 'WHERE ' . $db->nameQuote('content_id') . '=' . $db->Quote($groupId);
				$sub[] = 'AND ' . $db->nameQuote('type') . '=' . $db->Quote('group');

				$sub = implode(' ', $sub);
				$db->setQuery($sub);

				$existingGroupAcl = $db->loadColumn();

				// Perform a diff to see which acl rules are missing
				$diff = array_diff($existingGroupAcl, $installedAcls);

				// If there's a difference,
				if ($diff) {
					foreach ($diff as $aclId) {

						$value = 0;

						if (in_array($aclId, $acls)) {
							$value = 1;
						}

						$insertQuery[] = '(' . $db->Quote($groupId) . ',' . $db->Quote($aclId) . ',' . $db->Quote($value) . ',' . $db->Quote('group') . ')';
					}
				}
			}

			// Only run this when there is something to insert
			if ($insertQuery) {
				$insertQuery = implode(',', $insertQuery);
				$query .= $insertQuery;

				$db->setQuery($query);
				$db->Query();
			}
		}

		return true;
	}

	/**
	 * Since version 4.2.0, we now use toolbar color settings instead
	 *
	 * @since	4.2.0
	 * @access	public
	 */
	public function updateToolbarColors()
	{
		$this->engine();

		$config = ED::config();
		$theme = $config->get('layout_site_theme');

		if ($theme == 'flatt') {
			$this->updateConfig('layout_toolbarcolor', '#475a64');
			$this->updateConfig('layout_toolbarbordercolor', '#475a64');
			$this->updateConfig('layout_toolbaractivecolor', '#596c78');
		}

		if ($theme == 'simplistic') {
			$this->updateConfig('layout_toolbarcolor', '#fafafa');
			$this->updateConfig('layout_toolbarbordercolor', '#e1e1e1');
			$this->updateConfig('layout_toolbaractivecolor', '#d6d6d6');
			$this->updateConfig('layout_toolbartextcolor', '#777777');
		}

		if ($theme == 'timeless') {
			$this->updateConfig('layout_toolbarcolor', '#ffffff');
			$this->updateConfig('layout_toolbarbordercolor', '#e1e1e1');
			$this->updateConfig('layout_toolbaractivecolor', '#ebebeb');
			$this->updateConfig('layout_toolbartextcolor', '#888888');
		}

		if ($theme == 'zinc') {
			$this->updateConfig('layout_toolbarcolor', '#F5F5F5');
			$this->updateConfig('layout_toolbarbordercolor', '#e1e1e1');
			$this->updateConfig('layout_toolbaractivecolor', '#d6d6d6');
			$this->updateConfig('layout_toolbartextcolor', '#888888');
		}
	}
}
