<?php
/**
 * @package         Regular Labs Extension Manager
 * @version         7.4.5
 * 
 * @author          Peter van Westen <info@regularlabs.com>
 * @link            http://www.regularlabs.com
 * @copyright       Copyright © 2020 Regular Labs All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

defined('_JEXEC') or die;

require_once __DIR__ . '/script.install.helper.php';

class PlgActionlogRegularLabsManagerInstallerScript extends PlgActionlogRegularLabsManagerInstallerScriptHelper
{
	public $name           = 'REGULARLABSEXTENSIONMANAGER';
	public $alias          = 'regularlabsmanager';
	public $extension_type = 'plugin';
	public $plugin_folder  = 'actionlog';

	public function uninstall($adapter)
	{
		$this->uninstallComponent($this->extname);
	}
}
