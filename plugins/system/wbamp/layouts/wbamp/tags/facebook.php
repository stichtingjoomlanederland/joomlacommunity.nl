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

/**
 * Using parameters:
 *
 * if user set a width, we do not use layout = responsive (otherwise the default)
 * as this cause width parameter to be ignored
 * if a layout has been set by user, this override the calculated one
 */

$layout = empty($displayData['data']['width']) ? 'responsive' : 'fixed';
$layout = empty($displayData['data']['layout']) ? $layout : $displayData['data']['layout'];
$width = empty($displayData['data']['width']) ? 300 : (int) $displayData['data']['width'];
$height = empty($displayData['data']['height']) ? 250 : 'height="' . $displayData['data']['height'];
$isVideo = !empty($displayData['data']['subtype']) && $displayData['data']['subtype'] == 'videos';
$href = 'https://www.facebook.com/' . $displayData['data']['user'] . '/' . $displayData['data']['subtype'] . '/' . $displayData['data']['id'];
?>

<div class="wbamp-container wbamp-amp-tag wbamp-<?php echo $displayData['data']['type']; ?>">
	<amp-facebook width="<?php echo $width; ?>" height="<?php echo $height; ?>" layout="<?php echo $layout; ?>"
	              data-href="<?php echo $href; ?>"
		<?php
		if ($isVideo)
		{
			echo 'data-embed-as="video"';
		}
		?>>
	</amp-facebook>
</div>
