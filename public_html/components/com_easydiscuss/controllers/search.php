<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 - 2017 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

class EasyDiscussControllerSearch extends EasyDiscussController
{
	/**
	 * Performs search in EasyDiscuss
	 *
	 * @since	4.0.16
	 * @access	public
	 */
	public function query()
	{
		// Check for request forgeries
		ED::checkToken();

		// Get the query
		$query 	= $this->input->get('query', '', 'string');

		$tags = $this->input->get('tags', array(), 'array');
		$categories = $this->input->get('categories', array(), 'array');
		$postType = $this->input->get_('post_type', '', 'default');

		$catQuery = '';

		if ($categories) {
			$i = 0;
			foreach($categories as $item) {
				$catQuery .= "&categories[$i]=" . (int) $item;
				$i++;
			}
		}

		$tagQuery = '';
		
		if ($tags) {
			$i = 0;
			foreach($tags as $item) {
				$catQuery .= "&tags[$i]=" . (int) $item;
				$i++;
			}
		}

		if ($postType) {
			$postTypeQuery = '&post_type=' . $postType;
		}

		$url = EDR::_('view=search&query=' . $query . $catQuery . $tagQuery . $postTypeQuery, false);
		$this->app->redirect($url);
	}

}
