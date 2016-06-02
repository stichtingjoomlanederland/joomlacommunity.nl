<?php
/**
 * wbAMP - Accelerated Mobile Pages for Joomla!
 *
 * @author      Yannick Gaultier
 * @copyright   (c) Yannick Gaultier - Weeblr llc - 2016
 * @package     wbAmp
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @version     1.3.1.490
 * @date        2016-05-18
 */

// no direct access
defined('_JEXEC') or die;

if (empty($displayData['navigation_menu']))
{
	return;
}

?>
<div class="wbamp-container wbamp-menu wbamp-menu-slide">
	<nav class="wbamp-menu-slide">
		<button type="button" class="menu-open">
			<span class="menu-icon-bar"></span>
			<span class="menu-icon-bar"></span>
			<span class="menu-icon-bar"></span>
		</button>
		<div class="menu-content">
			<?php echo $displayData['navigation_menu']; ?>
		</div>
	</nav>
	<button type="button" class="menu-close"></button>
</div>
