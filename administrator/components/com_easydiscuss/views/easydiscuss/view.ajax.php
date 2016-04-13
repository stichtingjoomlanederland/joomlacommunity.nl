<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

require_once(DISCUSS_ADMIN_ROOT . '/views/views.php');

class EasyDiscussViewEasyDiscuss extends EasyDiscussAdminView
{
	/**
	 * Responsible to retrieve the latest version of EasyDiscuss
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function versionChecks()
	{
		// Get the current version installed on the site
		$local = ED::getLocalVersion();

		// Get the latest version from the server
		$server = ED::getVersion();

		if (!$server) {
			return $this->ajax->reject(JText::_('COM_EASYDISCUSS_UNABLE_TO_UPDATE_SERVER'));
		}

		$outdated = version_compare($local, $server) === -1;

		return $this->ajax->resolve($local, $server, $outdated);
	}
}
