<?php
/**
 * @package    PwtAcl
 *
 * @author     Sander Potjer - Perfect Web Team <extensions@perfectwebteam.com>
 * @copyright  Copyright (C) 2011 - 2018 Perfect Web Team. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link       https://extensions.perfectwebteam.com/pwt-acl
 */

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;

// Set flag that this is a parent file.
const _JEXEC = 1;

error_reporting(E_ALL | E_NOTICE);
ini_set('display_errors', 1);

// Load system defines
if (file_exists(dirname(__DIR__) . '/defines.php'))
{
	require_once dirname(__DIR__) . '/defines.php';
}

if (!defined('_JDEFINES'))
{
	define('JPATH_BASE', dirname(__DIR__));
	require_once JPATH_BASE . '/includes/defines.php';
}

require_once JPATH_LIBRARIES . '/import.legacy.php';
require_once JPATH_LIBRARIES . '/cms.php';

// Load the configuration
require_once JPATH_CONFIGURATION . '/configuration.php';

// Define JDEBUG
define('JDEBUG', false);

/**
 * This script will fix the #__assets table via the PWT ACL Diagnostics tool
 *
 * @since   3.0
 */
class PwtaclFix extends JApplicationCli
{
	/**
	 * Entry point for the script
	 *
	 * @return  void
	 * @since   3.0
	 * @throws  Exception
	 */
	public function doExecute()
	{
		// Run as administrator application
		Factory::getApplication('administrator');

		// Load the model.
		BaseDatabaseModel::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_pwtacl/models', 'PwtaclModel');

		// Start the rebuild
		$this->out('============================');
		$this->out('Start fixing Assets table');

		for ($step = 1; $step <= 14; $step++)
		{
			$this->out('Step ' . $step);

			try
			{
				/** @var PwtaclModelDiagnostics $model */
				$model = BaseDatabaseModel::getInstance('Diagnostics', 'PwtaclModel', array('ignore_request' => true));
				$model->runDiagnostics($step, true);
			}
			catch (Exception $e)
			{
				$this->out('Failed to rebuild');
			}
		}

		$this->out('Finished fixing Assets table');
		$this->out('============================');
	}
}

JApplicationCli::getInstance('PwtaclFix')->execute();
