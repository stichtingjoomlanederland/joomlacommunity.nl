<?php
/**
 * @package         DB Replacer
 * @version         6.3.9PRO
 * 
 * @author          Peter van Westen <info@regularlabs.com>
 * @link            http://www.regularlabs.com
 * @copyright       Copyright © 2021 Regular Labs All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

defined('_JEXEC') or die;

require_once __DIR__ . '/script.install.helper.php';

class PlgActionlogDBReplacerInstallerScript extends PlgActionlogDBReplacerInstallerScriptHelper
{
	public $name           = 'DBREPLACER';
	public $alias          = 'dbreplacer';
	public $extension_type = 'plugin';
	public $plugin_folder  = 'actionlog';

	public function uninstall($adapter)
	{
		$this->uninstallComponent($this->extname);
	}
}
