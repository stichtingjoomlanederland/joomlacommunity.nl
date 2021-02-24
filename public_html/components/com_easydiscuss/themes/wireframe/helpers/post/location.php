<?php
/**
* @package      EasyDiscuss
* @copyright    Copyright (C) Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<div class="o-card o-card--ed-post-widget" data-ed-location>
	<div class="o-card__body l-stack">
		<div class="o-title-01">
			<?php echo JText::_('COM_EASYDISCUSS_LOCATION'); ?>
		</div>

		<div class="ed-location l-stack">
			<div>
				<a href="http://www.google.com/maps?q=<?php echo urlencode($post->address);?>&amp;hl=en" target="_blank">
					<i class="fa fa-map-marker-alt"></i>&nbsp; <?php echo $post->address; ?>
				</a>
			</div>

			<div data-ed-location-map-wrapper>
				<?php if ($this->config->get('main_location_static')) { ?>
					<div class="ed-location__map-static" style="background-image:url('<?php echo ED::getMapRequestURL($post); ?>');min-height: 200px;height: 200px;"></div>
					<style>
					@media print {
						.ed-location__map-static:before {
							content: url('<?php echo ED::getMapRequestURL($post, true); ?>');
						}
					}
					</style>
				<?php } else { ?>
					<iframe 
						width="100%" 
						height="200px" 
						frameborder="0" 
						style="border:0" 
						src="<?php echo ED::getMapRequestURL($post); ?>" 
						allowfullscreen
					></iframe>
				<?php } ?>
			</div>
		</div>
	</div>
</div>