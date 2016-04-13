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

class EasyDiscussCron extends EasyDiscuss
{
	/**
	 * Executes during cron's initialization
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function execute()
	{
		// Get a list of hooks
		$hooks = JFolder::files(__DIR__ . '/hooks', '.', false, true);

		foreach ($hooks as $hook) {
			
			include_once($hook);

			$objectClass = str_ireplace('.php', '', basename($hook));
			$className = 'EasyDiscussCronHook' . $objectClass;

			$obj = new $className();

			$obj->execute();
		}
	}
}