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

require_once(DISCUSS_ADMIN_ROOT . '/views/views.php');

class EasyDiscussViewUsers extends EasyDiscussAdminView
{
	/**
	 * Renders the user's listing
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function display($tpl = null)
	{
		$url = JURI::root() . 'administrator/index.php?option=com_easydiscuss&amp;view=users&amp;tmpl=component&amp;browse=1&amp;browsefunction=selectUser&amp;prefix=acl';

		$theme = ED::themes();
		$theme->set('url', $url);

		$output = $theme->output('admin/users/dialog');

		return $this->ajax->resolve($output);
	}
}