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

class EasyDiscussSharer extends EasyDiscuss
{
	public function initButtons($files)
	{
		$buttons = array();

		foreach ($files as $file) {

			require_once($file);

			$id = str_ireplace('.php', '', basename($file));

			$className = 'EasyDiscussSharerButton' . ucfirst($id);
			$button = new $className();

			// If button is not enabled, do not process.
			if (!$button->enabled()) {
				continue;
			}

			$buttons[] = $button;
		}

		return $buttons;
	}

	public function buttons()
	{
		$folder = __DIR__ . '/buttons';

		$files = JFolder::files($folder, '.', false, true, array('index.html'));

		$buttons = $this->initButtons($files);

		return $buttons;
	}

	public function html($post, $position = 'vertical')
	{
		$buttons = $this->buttons();

		if (!$buttons) {
			return false;
		}

		$theme = ED::themes();
		$theme->set('post', $post);
		$theme->set('position', $position);
		$theme->set('buttons', $buttons);

		$output = $theme->output('site/widgets/sharer');

		return $output;
	}
}
