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

class EasyDiscussViewGiphy extends EasyDiscussView
{
	/**
	 * Search for GIFs and stickers of GIPHY via query
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public function search()
	{
		ED::requireLogin();

		// Get the search query input
		$query = $this->input->get('query', '', 'string');

		// Determine whether it is coming from the story form or not
		$type = $this->input->get('type', '', 'string');

		$giphy = ED::giphy();

		// Search and get the data
		$data = $giphy->getData($type, $query);

		$hasGiphies = true;

		if (!$data) {
			$data = false;
			$hasGiphies = false;
		}

		$theme = ED::themes();
		$theme->set('giphies', $data);
		$theme->set('type', $type);

		$html = $theme->output('site/composer/forms/giphy.list');

		return $this->ajax->resolve($hasGiphies, $html);
	}
}
