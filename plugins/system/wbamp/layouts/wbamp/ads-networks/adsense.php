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

$id = empty($displayData['ad_id']) ? '1' : $displayData['ad_id'];

?>

<div class="wbamp-container wbamp-ad">
	<div class="wbamp-amp-tag wbamp-adsense" id="wbamp-adsense-<?php echo $id; ?>">
		<amp-ad width="<?php echo $displayData['params']->get('ad_width', 300); ?>"
		        height="<?php echo $displayData['params']->get('ad_height', 250); ?>"
		        type="adsense"
		        data-ad-client="<?php echo $displayData['params']->get('adsense-ad-client'); ?>"
		        data-ad-slot="<?php echo $displayData['params']->get('adsense-ad-slot', ''); ?>">
		</amp-ad>
	</div>
</div>
