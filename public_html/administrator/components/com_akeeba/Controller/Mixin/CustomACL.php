<?php
/**
 * @package   AkeebaBackup
 * @copyright Copyright (c)2006-2016 Nicholas K. Dionysopoulos
 * @license   GNU General Public License version 3, or later
 */

namespace Akeeba\Backup\Admin\Controller\Mixin;

// Protect from unauthorized access
defined('_JEXEC') or die();

use RuntimeException;
use JText;

trait CustomACL
{
	protected function onBeforeExecute(&$task)
	{
		$this->akeebaBackupACLCheck($this->view);
	}

	/**
	 * Checks if the currently logged in user has the required ACL privileges to access the current view. If not, a
	 * RuntimeException is thrown.
	 *
	 * @return  void
	 */
	protected function akeebaBackupACLCheck($view)
	{
		// Akeeba Backup-specific ACL checks. All views not listed here are limited by the akeeba.configure privilege.
		$viewACLMap = [
			'ControlPanel' => 'core.manage',
			'Backup'       => 'akeeba.backup',
			'Upload'       => 'akeeba.backup',
			'Manage'       => 'akeeba.download',
			'Log'          => 'akeeba.download',
			'S3Import'     => 'akeeba.download',
			'Restore'      => 'akeeba.download',
			'RemoteFiles'  => 'akeeba.download',
			'Discover'     => 'akeeba.download',
			'Transfer'     => 'akeeba.download',
		];

		$privilege = 'akeeba.configure';

		if (array_key_exists($view, $viewACLMap))
		{
			$privilege = $viewACLMap[$view];
		}

		// If an empty privilege is defined do not perform any ACL checks
		if (empty($privilege))
		{
			return;
		}

		if (!$this->container->platform->authorise($privilege, 'com_akeeba'))
		{
			throw new RuntimeException(JText::_('JERROR_ALERTNOAUTHOR'), 403);
		}
	}
}