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

// data preparation
$layout = empty($displayData['data']['width']) ? 'responsive' : 'fixed';
$layout = empty($displayData['data']['layout']) ? $layout : $displayData['data']['layout'];
$width = empty($displayData['data']['width']) ? 450 : (int) $displayData['data']['width'];
$height = empty($displayData['data']['height']) ? 253 : (int) $displayData['data']['height'];

?>

<div class="wbamp-container wbamp-amp-tag wbamp-<?php echo $displayData['data']['type']; ?>">
	<amp-youtube width="<?php echo $width; ?>" height="<?php echo $height; ?>" layout="<?php echo $layout; ?>" data-videoid="<?php echo $displayData['data']['videoid']; ?>">
	</amp-youtube>
</div>
