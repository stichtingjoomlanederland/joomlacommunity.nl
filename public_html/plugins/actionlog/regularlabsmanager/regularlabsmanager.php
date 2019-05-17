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

if ( ! is_file(JPATH_LIBRARIES . '/regularlabs/autoload.php'))
{
	return;
}

require_once JPATH_LIBRARIES . '/regularlabs/autoload.php';

/**
 * Plugin that logs User Actions
 */
class PlgActionlogRegularLabsManager
	extends \RegularLabs\Library\ActionLogPlugin
{
	public $name  = 'REGULAR_LABS_EXTENSION_MANAGER';
	public $alias = 'regularlabsmanager';
}
