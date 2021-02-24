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

	public function buttons($type = 'default')
	{
		// Get the default social buttons
		if ($type == 'default') {
			$folder = __DIR__ . '/buttons';

			$files = JFolder::files($folder, '.', false, true, array('index.html'));
			$buttons = $this->initButtons($files);

			return $buttons;
		}

		// Get the AddThis social buttons
		if ($type == 'addthis' && $this->config->get('addthis_pub_id')) {
			// Add the addthis script into the page
			$script = '<script type="text/javascript" src="//s7.addthis.com/js/300/addthis_widget.js#pubid=' . $this->config->get('addthis_pub_id') . '"></script>';
			$this->doc->addCustomTag($script);

			$file = __DIR__ . '/buttons/addthis.php';
			$buttons = $this->initButtons([$file]);

			return $buttons;
		}

		if ($type == 'sharethis' && $this->config->get('sharethis_prop_id')) {
			// Add the sharethis script into the page
			$script = '<script type="text/javascript" src="https://platform-api.sharethis.com/js/sharethis.js#property=' . $this->config->get('sharethis_prop_id') . '&product=inline-share-buttons" async="async"></script>';

			$this->doc->addCustomTag($script);

			$file = __DIR__ . '/buttons/sharethis.php';
			$buttons = $this->initButtons([$file]);

			return $buttons;
		}

		return false;
	}

	public function html($post, $position = 'vertical')
	{
		$buttons = $this->buttons($this->config->get('social_buttons_type'));

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
