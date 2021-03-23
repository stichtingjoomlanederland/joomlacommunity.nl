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

class EasyDiscussControllerModules extends EasyDiscussController
{
	public function __construct()
	{
		parent::__construct();

		$this->checkAccess('discuss.manage.modules');
	}

	/**
	 * Re-synchronize modules
	 *
	 * @since	5.0.0
	 * @access	private
	 */
	private function rediscover()
	{
		$model = ED::model('Modules');
		$manifest = $model->discoverManifest($this->config->get('main_apikey'));

		return $manifest;
	}

	/**
	 * Discover modules list from services repository
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public function discover()
	{
		$manifest = $this->rediscover();

		// Do something if we are unable to retrieve the full manifest list
		if (!$manifest) {
			return $this->ajax->reject('Something went awry while fetching the manifest list. Please contact our support team for more information.');
		}

		// Decode the result
		if ($manifest->state != 200) {
			$return = base64_encode('index.php?option=com_easysocial&view=modules');

			return $this->ajax->reject('Something went awry while fetching the manifest list. Please contact our support team for more information.');
		}

		return $this->ajax->resolve();
	}

	/**
	 * Installs a module
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public function install()
	{
		$ids = $this->input->get('cid', [], 'int');

		foreach ($ids as $id) {
			$package = ED::table('Package');
			$package->load((int) $id);

			$state = $package->install();

			if (!$state) {
				ED::setMessage($package->getError(), 'danger');

				return $this->redirectToView('modules');
			}
		}

		// Rediscover the modules
		$this->rediscover();

		ED::setMessage('Selected modules is now installed on the site', 'success');

		$this->redirectToView('modules');
	}

	/**
	 * Uninstalls a module from the site
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public function uninstall()
	{
		$ids = $this->input->get('cid', [], 'int');

		foreach ($ids as $id) {
			$package = ED::table('Package');
			$package->load((int) $id);

			$package->uninstall();
		}

		$this->rediscover();

		ED::setMessage('Selected modules is now uninstalled from the site', 'success');
		$this->redirectToView('modules');
	}
}
