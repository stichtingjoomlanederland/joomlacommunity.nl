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

require_once(__DIR__ . '/constants.php');
require_once(__DIR__ . '/router.php');

jimport('joomla.filesystem.file');

if (!function_exists('dump')) {

	function dump()
	{
		$args = func_get_args();

		echo '<pre>';

		foreach ($args as $arg) {
			var_dump($arg);
		}
		echo '</pre>';
		exit;
	}
}

class EasyDiscuss
{
	public $config = null;
	public $jConfig = null;
	public $app = null;
	public $input = null;
	public $my = null;
	public $doc = null;
	public $access = null;

	protected $error = null;

	public function __construct()
	{
		if (!defined('ED_CLI')) {
			$this->doc = JFactory::getDocument();
			$this->config = ED::config();
			$this->jConfig = ED::jConfig();
			$this->app = JFactory::getApplication();
			$this->input = ED::request();
			$this->my = JFactory::getUser();
			$this->acl = ED::acl();
			$this->isSiteAdmin = ED::isSiteAdmin();

			// 1. Detect if this is an api call
			if ($this->doc->getType() == 'json') {

				$userId = $this->input->get('userId', '', 'int');
				$auth = $this->input->get('auth', '', 'default');

				$isValid = false;
				if ($userId && $auth) {
					$user = ED::user($userId);

					if ($auth == $user->auth) {
						$isValid = true;
					}
				}

				if ($isValid) {
					$this->my = JFactory::getUser($user->id);
					$this->acl = ED::acl($user->id);
					$this->isSiteAdmin = ED::isSiteAdmin($user->id);
				}
			}

		}
	}

	public function setError($message)
	{
		$this->error = JText::_($message);
	}

	public function getError()
	{
		return $this->error;
	}
}
