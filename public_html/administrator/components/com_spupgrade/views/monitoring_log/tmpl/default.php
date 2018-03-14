<?php
/**
 * @package		SP Upgrade
 * @subpackage	Components
 * @copyright	KAINOTOMO PH LTD - All rights reserved.
 * @author		KAINOTOMO PH LTD
 * @link		http://www.kainotomo.com
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
*/
// No direct access to this file
defined('_JEXEC') or die('Restricted Access');
?>
<?php
$log = @file_get_contents(JPATH_BASE."/components/com_spupgrade/log.htm");
echo $log;
?>

