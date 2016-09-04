<?php
/**
 * wbAMP - Accelerated Mobile Pages for Joomla!
 *
 * @author      Yannick Gaultier
 * @copyright   (c) Yannick Gaultier - Weeblr llc - 2016
 * @package     wbAmp
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @version     1.5.0.585
 * @date        2016-08-25
 */

// no direct access
defined('_JEXEC') or die;

if (empty($displayData['navigation_menu']))
{
	return;
}

?>
<amp-sidebar id='wbamp_sidebar_1' layout='nodisplay' class="wbamp-sidebar" side="<?php echo $displayData['navigation_menu_side']; ?>">
	<div class="wbamp-menu wbamp-<?php echo $displayData['navigation_menu_side']; ?>">
		<?php echo $displayData['navigation_menu']; ?>
	</div>
	<button type="button" class="wbamp-sidebar-button menu-close" on='tap:wbamp_sidebar_1.close'>&times;</button>
</amp-sidebar>

<div class="wbamp-wrapper">
	<div class="wbamp-container wbamp-sidebar-control wbamp-<?php echo $displayData['navigation_menu_side']; ?>">
		<button type="button" class="wbamp-sidebar-button menu-open" on='tap:wbamp_sidebar_1.open'>
			<span class="menu-icon-bar"></span>
			<span class="menu-icon-bar"></span>
			<span class="menu-icon-bar"></span>
		</button>
	</div>
</div>

