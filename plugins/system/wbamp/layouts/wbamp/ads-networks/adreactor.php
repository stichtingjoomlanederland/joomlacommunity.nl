<?php
/**
 * wbAMP - Accelerated Mobile Pages for Joomla!
 *
 * @author      Yannick Gaultier
 * @copyright   (c) Yannick Gaultier - Weeblr llc - 2016
 * @package     wbAmp
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @version     1.4.2.551
 * @date        2016-07-19
 */

// no direct access
defined('_JEXEC') or die;
?>

<div class="wbamp-container wbamp-ad">
	<div class="wbamp-amp-tag wbamp-adreactor" id="wbamp-adreactor-1">
		<amp-ad width="<?php echo $displayData['params']->get('ad_width', 300); ?>"
		        height="<?php echo $displayData['params']->get('ad_height', 250); ?>"
		        type="adreactor"
		        data-pid="<?php echo $displayData['params']->get('adreactor-pid'); ?>"
		        data-zid="<?php echo $displayData['params']->get('adreactor-zid', ''); ?>"
		        data-custom3="<?php echo $displayData['params']->get('adreactor-custom3', ''); ?>">
		</amp-ad>
	</div>
</div>
