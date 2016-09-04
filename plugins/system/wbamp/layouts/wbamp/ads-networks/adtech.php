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

$id = empty($displayData['ad_id']) ? '1' : $displayData['ad_id'];


?>

<div class="wbamp-container wbamp-ad">
	<div class="wbamp-amp-tag wbamp-adtech" id="wbamp-adtech-<?php echo $displayData['ad_id']; ?>">
		<amp-ad width="<?php echo $displayData['params']->get('ad_width', 300); ?>"
		        height="<?php echo $displayData['params']->get('ad_height', 250); ?>"
		        type="adtech"
		        src="<?php echo $displayData['params']->get('adtech-src', ''); ?>">
		</amp-ad>
	</div>
</div>
