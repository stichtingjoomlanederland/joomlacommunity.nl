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

// data preparation
$layout = empty($displayData['data']['width']) ? 'responsive' : 'fixed';
$layout = empty($displayData['data']['layout']) ? $layout : $displayData['data']['layout'];
$width = empty($displayData['data']['width']) ? 300 : (int) $displayData['data']['width'];
$height = empty($displayData['data']['height']) ? 300 : (int) $displayData['data']['height'];

?>

<div class="wbamp-container wbamp-amp-tag wbamp-<?php echo $displayData['data']['type']; ?>">
	<amp-instagram width="<?php echo $width; ?>" height="<?php echo $height; ?>" layout="<?php echo $layout; ?>" data-shortcode="<?php echo $displayData['data']['shortcode']; ?>">
	</amp-instagram>
</div>
