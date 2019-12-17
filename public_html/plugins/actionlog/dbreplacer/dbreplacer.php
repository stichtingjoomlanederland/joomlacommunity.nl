<?php
/**
 * @package         DB Replacer
 * @version         6.3.5PRO
 * 
 * @author          Peter van Westen <info@regularlabs.com>
 * @link            http://www.regularlabs.com
 * @copyright       Copyright © 2019 Regular Labs All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

defined('_JEXEC') or die;

use RegularLabs\Library\ArrayHelper as RL_Array;
use RegularLabs\Library\Log as RL_Log;

if ( ! is_file(JPATH_LIBRARIES . '/regularlabs/autoload.php'))
{
	return;
}

require_once JPATH_LIBRARIES . '/regularlabs/autoload.php';

/**
 * Plugin that logs User Actions
 */
class PlgActionlogDBReplacer
	extends \RegularLabs\Library\ActionLogPlugin
{
	public $name  = 'DB_REPLACER';
	public $alias = 'dbreplacer';

	public function onAfterDatabaseReplace($context, $table_name)
	{
		if (strpos($context, $this->option) === false)
		{
			return;
		}

		if ( ! RL_Array::find(['*', 'replacement'], $this->events))
		{
			return;
		}

		$languageKey = 'DBR_ACTIONLOGS_REPLACEMENT';

		$message = [
			'table_name'     => (string) $table_name,
			'extension_name' => $this->name,
			'extension_link' => 'index.php?option=com_dbreplacer',
		];

		RL_Log::add($message, $languageKey, $context);
	}
}
