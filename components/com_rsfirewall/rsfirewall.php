<?php
/**
 * @package    RSFirewall!
 * @copyright  (c) 2009 - 2016 RSJoomla!
 * @link       https://www.rsjoomla.com
 * @license    GNU General Public License http://www.gnu.org/licenses/gpl-3.0.en.html
 */

defined('_JEXEC') or die('Restricted access');

$app = JFactory::getApplication();

require_once JPATH_ADMINISTRATOR.'/components/com_rsfirewall/helpers/adapter.php';
require_once JPATH_ADMINISTRATOR.'/components/com_rsfirewall/helpers/config.php';
require_once JPATH_COMPONENT.'/controller.php';
	
$controller	= JControllerLegacy::getInstance('RSFirewall');

$task = $app->input->get('task');

$controller->execute($task);
$controller->redirect();