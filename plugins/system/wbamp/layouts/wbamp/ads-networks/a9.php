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
	<div class="wbamp-amp-tag wbamp-a9" id="wbamp-a9-1">
		<amp-ad width="<?php echo $displayData['params']->get('ad_width', 300); ?>"
		        height="<?php echo $displayData['params']->get('ad_height', 250); ?>"
		        type="a9"
		        data-aax_size="<?php echo $displayData['params']->get('ad_width', 300) . 'x' . $displayData['params']->get('ad_height', 250); ?>"
		        data-aax_pubname="<?php echo $displayData['params']->get('a9-aax_pubname', ''); ?>"
		        data-aax_src="<?php echo $displayData['params']->get('a9-aax_src', ''); ?>">
		</amp-ad>
	</div>
</div>
