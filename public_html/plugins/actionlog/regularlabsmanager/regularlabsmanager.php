<?php
/**
 * @package         Regular Labs Extension Manager
 * @version         7.4.9
 * 
 * @author          Peter van Westen <info@regularlabs.com>
 * @link            http://www.regularlabs.com
 * @copyright       Copyright © 2021 Regular Labs All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

use RegularLabs\Library\Document as RL_Document;

defined('_JEXEC') or die;

if ( ! is_file(JPATH_LIBRARIES . '/regularlabs/autoload.php'))
{
	return;
}

require_once JPATH_LIBRARIES . '/regularlabs/autoload.php';

if ( ! RL_Document::isJoomlaVersion(3))
{
	return;
}

if (true)
{
	class PlgActionlogRegularLabsManager
		extends \RegularLabs\Library\ActionLogPlugin
	{
		public $name  = 'REGULARLABSEXTENSIONMANAGER';
		public $alias = 'regularlabsmanager';
	}
}
