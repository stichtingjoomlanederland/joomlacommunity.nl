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

if (empty($displayData['social_buttons']) || empty($displayData['social_buttons']['types']))
{
	return;
}

?>
<nav class="wbamp-container wbamp-social-buttons wbamp-icons-<?php echo $displayData['social_buttons']['theme'] ?> wbamp-<?php echo $displayData['social_buttons']['style'] ?>">
	<ul>
		<?php
		foreach ($displayData['social_buttons']['types'] as $buttonType)
		{
			echo ShlMvcLayout_Helper::render('wbamp.buttons.' . $buttonType, $displayData, WbampHelper_Runtime::$layoutsBasePaths);
		}
		?>
	</ul>
</nav>
