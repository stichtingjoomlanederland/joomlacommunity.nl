<?php
/**
 * wbAMP - Accelerated Mobile Pages for Joomla!
 *
 * @author      Yannick Gaultier
 * @copyright   (c) Yannick Gaultier - Weeblr llc - 2016
 * @package     wbAmp
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @version     1.6.0.607
 * @date        2016-10-31
 */

// no direct access
defined('_JEXEC') or die;

if (empty($displayData['navigation_menu']))
{
	return;
}

?>
<nav class="wbamp-container wbamp-menu">
	<?php echo $displayData['navigation_menu']; ?>
</nav>
