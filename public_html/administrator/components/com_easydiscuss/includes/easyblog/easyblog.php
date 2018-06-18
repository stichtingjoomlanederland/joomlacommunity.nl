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

class EasyDiscussEasyBlog extends EasyDiscuss
{
	/**
	 * Determines if EasyBlog is installed on the site.
	 *
	 * @since	4.2.0
	 * @access	public
	 */
	public function exists()
	{
		static $exists = null;

		if (is_null($exists)) {
			jimport('joomla.filesystem.file');

			$file = JPATH_ADMINISTRATOR . '/components/com_easyblog/includes/easyblog.php';
			$fileExists = JFile::exists($file);
			$enabled = JComponentHelper::isEnabled('com_easyblog');

			if (!$fileExists || !$enabled) {
				$exists = false;
				return $exists;
			}

			include_once($file);
			$exists = true;
		}

		return $exists;
	}

	/**
	 * Determines if the dropdown toolbar should be rendering easysocial items
	 *
	 * @since	4.2.0
	 * @access	public
	 */
	public function hasToolbar()
	{
		if (!$this->exists()) {
			return false;
		}

		if (!$this->config->get('integrations_easyblog_toolbar') || !$this->exists()) {
			return false;
		}

		return true;
	}

	/**
	 * Loads language file from EasyBlog
	 *
	 * @since	4.2.0
	 * @access	public
	 */
	public function loadLanguage()
	{
		static $loaded = null;

		if (is_null($loaded)) {
			JFactory::getLanguage()->load('com_easyblog', JPATH_ROOT);
			
			$loaded = true;	
		}
		
		return $loaded;
	}

	/**
	 * Renders the dropdown toolbar for EasyBlog
	 *
	 * @since	4.2.0
	 * @access	public
	 */
	public function getToolbarDropdown()
	{
		if (!$this->hasToolbar()) {
			return;
		}

		$this->loadLanguage();

		$config = EB::config();
		$acl = EB::acl();
		$showManage = false;

		if ($acl->get('add_entry') || $acl->get('create_post_templates') 
			|| (EB::isSiteAdmin() || ($acl->get('moderate_entry') || ($acl->get('manage_pending') && $acl->get('publish_entry')))) 
			|| $acl->get('create_category')
			|| $acl->get('create_tag')) {
			$showManage = true;
		}

		$theme = ED::themes();
		$theme->set('config', $config);
		$theme->set('acl', $acl);
		$theme->set('showManage', $showManage);
		
		$output = $theme->output('site/toolbar/easyblog');

		return $output;
	}
}
