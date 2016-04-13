<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

require_once(DISCUSS_ROOT . '/views/views.php');

class EasyDiscussViewUsers extends EasyDiscussView
{
	/**
	 * Search for users on the site
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function search()
	{
		$query = $this->input->get('query', '', 'default');
		$exclude = $this->input->get('exclude', '', 'int');

		$model = ED::model('Users');
		$result = $model->search($query, $exclude);
		$users = array();

		if ($result) {
			foreach ($result as $row) {

				$profile = ED::user($row->id);

				$user = new stdClass();
				$user->id = $row->id;
				$user->title = $profile->getName();
				$user->avatar = $profile->getAvatar();
				
				$users[] = $user;
			}
		}
		
		$this->ajax->resolve($users);
	}
}
