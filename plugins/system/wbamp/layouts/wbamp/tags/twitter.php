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

// data preparation: size
if (!empty($displayData['data']['cards']) && $displayData['data']['cards'] == 'hidden')
{
	$defaultWidth = 327;
	$defaultHeight = 176;
}
else
{
	$defaultWidth = 250;
	$defaultHeight = 224;
}
$layout = empty($displayData['data']['width']) ? 'responsive' : 'container';
$layout = empty($displayData['data']['layout']) ? $layout : $displayData['data']['layout'];
$width = empty($displayData['data']['width']) ? $defaultWidth : (int) $displayData['data']['width'];
$height = empty($displayData['data']['height']) ? $defaultHeight : (int) $displayData['data']['height'];

?>
<div class="wbamp-container wbamp-amp-tag wbamp-<?php echo $displayData['data']['type']; ?>">
	<amp-twitter width="<?php echo $width; ?>" height="<?php echo $height; ?>" layout="<?php echo $layout; ?>"
	             data-tweetid="<?php echo $displayData['data']['tweetid']; ?>"
		<?php
		if (!empty($displayData['data']['cards']))
		{
			echo ' data-cards="' . $this->escape($displayData['data']['cards']) . '"';
		}
		if (!empty($displayData['data']['theme']))
		{
			echo 'data-theme="' . $this->escape($displayData['data']['theme']) . '"';
		}
		if (!empty($displayData['data']['align']))
		{
			echo 'data-align="' . $this->escape($displayData['data']['align']) . '"';
		}
		?>>
	</amp-twitter>
</div>
