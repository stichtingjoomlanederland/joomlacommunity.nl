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

class EasyDiscussThemesHelperAssignment
{
	/**
	 * Generates the assignment dropdown
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public function dropdown(EasyDiscussPost $post)
	{
		static $cache = [];

		if (!isset($cache[$post->id])) {

			$moderator = null;

			if ($post->getAssignment()) {
				$moderator = $post->assignee;
			}

			$theme = ED::themes();
			$theme->set('moderator', $moderator);
			$theme->set('post', $post);
			$output = $theme->output('site/helpers/assignment/dropdown');

			return $output;			
		}


	}
}
