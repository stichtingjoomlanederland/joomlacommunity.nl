<?php
/**
 * @package         Regular Labs Extension Manager
 * @version         7.4.2
 * 
 * @author          Peter van Westen <info@regularlabs.com>
 * @link            http://www.regularlabs.com
 * @copyright       Copyright © 2019 Regular Labs All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

defined('_JEXEC') or die;

require_once __DIR__ . '/script.install.helper.php';

class PlgActionlogRegularLabsManagerInstallerScript extends PlgActionlogRegularLabsManagerInstallerScriptHelper
{
	public $name           = 'REGULAR_LABS_EXTENSION_MANAGER';
	public $alias          = 'regularlabsmanager';
	public $extension_type = 'plugin';
	public $plugin_folder  = 'actionlog';

	public function uninstall($adapter)
	{
		$this->uninstallComponent($this->extname);
	}
}
