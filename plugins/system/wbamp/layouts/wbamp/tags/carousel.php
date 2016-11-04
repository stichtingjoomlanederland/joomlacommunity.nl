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

// data preparation
$layout = empty($displayData['data']['width']) ? 'responsive' : 'fixed';
$layout = empty($displayData['data']['layout']) ? $layout : $displayData['data']['layout'];
$subtype = empty($displayData['data']['subtype']) ? 'carousel' : $displayData['data']['subtype'];
if ($subtype == 'carousel' && $layout == 'responsive')
{
	// not supported yet
	$layout = 'fixed';
}

// size
$width = empty($displayData['data']['width']) ? 300 : (int) $displayData['data']['width'];
$height = empty($displayData['data']['height']) ? 300 : (int) $displayData['data']['height'];

// other
$controls = !empty($displayData['data']['controls']) ? ' controls' : '';
$loop = $subtype == 'slides' && !empty($displayData['data']['loop']) ? ' loop' : '';
$autoplay = $subtype == 'slides' && !empty($displayData['data']['autoplay']) ? ' autoplay' : '';
$moduleId = empty($displayData['data']['module_id']) ? 0 : (int) $displayData['data']['module_id'];
if (!empty($moduleId))
{
	$moduleContent = WbampHelper_Modules::getHtmlModuleContent($moduleId);
}

?>

<div class="wbamp-container wbamp-amp-tag wbamp-<?php echo $displayData['data']['type']; ?>">
	<amp-carousel width="<?php echo $width; ?>" height="<?php echo $height; ?>" layout="<?php echo $layout; ?>"
	              type="<?php echo $displayData['data']['subtype']; ?>"
		<?php echo $loop; ?>
		<?php echo $autoplay; ?>
	>
		<?php if (!empty($moduleContent))
		{
			echo $moduleContent;
		}
		?>
	</amp-carousel>
</div>
