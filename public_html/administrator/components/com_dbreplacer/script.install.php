<?php
/**
 * @package         DB Replacer
 * @version         6.4.0PRO
 * 
 * @author          Peter van Westen <info@regularlabs.com>
 * @link            http://regularlabs.com
 * @copyright       Copyright © 2021 Regular Labs All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

defined('_JEXEC') or die;

require_once __DIR__ . '/script.install.helper.php';

class Com_DBReplacerInstallerScript extends Com_DBReplacerInstallerScriptHelper
{
	public $alias          = 'dbreplacer';
	public $extension_type = 'component';
	public $name           = 'DBREPLACER';

	public function onAfterInstall($route)
	{
		$this->deleteOldFiles();
		$this->fixAssetsRules();

		return parent::onAfterInstall($route);
	}

	private function deleteOldFiles()
	{
		$this->delete(
			[
				JPATH_SITE . '/components/com_dbreplacer',
			]
		);
	}
}
