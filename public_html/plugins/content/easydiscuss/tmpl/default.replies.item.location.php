<?php
/**
 * @package		EasyDiscuss
 * @copyright	Copyright (C) 2010 Stack Ideas Private Limited. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 *
 * EasyDiscuss is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */
defined('_JEXEC') or die('Restricted access');

if( !$post->latitude || !$post->longitude || !$post->address )
{
	return;
}
?>
<div class="discuss-item-location row-fluid mt-15 postLocation" data-id="<?php echo $post->id;?>" id="location-<?php echo $post->id;?>">
<hr />
	<div class="ph-10">
		<div class="row-fluid discuss-item-location-hd">
			<h3 class="pull-left"><?php echo JText::_( 'COM_EASYDISCUSS_LOCATION' );?>
				<span class="ml-10">
				[ <a href="http://www.google.com/maps?q=<?php echo urlencode( $post->address );?>&amp;hl=en" target="_blank" class="map-link"><?php echo JText::_( 'COM_EASYDISCUSS_VIEW_LARGER_MAP' );?></a> ]
				</span>
			</h3>
			<div class="pull-right">
				<a href="javascript:void(0);" class="removeLocation"><i class="icon-remove" rel="ed-tooltip"></i></a>
			</div>
		</div>
		<?php if( DiscussHelper::isModerator( $post->category_id ) ){ ?>
		<div><i class="icon-map-marker"></i> <?php echo $post->address; ?></div>
		<?php } ?>

		<?php if( $system->config->get( 'main_location_static' ) ){ ?>
			<div class="map-images">
				<img src="http://maps.googleapis.com/maps/api/staticmap?center=<?php echo $post->latitude;?>,<?php echo $post->longitude;?>&language=<?php echo $system->config->get( 'main_location_language'); ?>&maptype=<?php echo strtolower( $system->config->get( 'main_location_map_type' ) );?>&zoom=<?php echo $system->config->get( 'main_location_default_zoom');?>&size=<?php echo $system->config->get( 'main_location_map_width');?>x<?php echo $system->config->get( 'main_location_map_height');?>&sensor=true&markers=color:red|label:S|<?php echo $post->latitude;?>,<?php echo $post->longitude;?>" />
			</div>
		<?php } else { ?>
			<script type="text/javascript">
			EasyDiscuss.require()
				.script('location')
				.done(function($){

					$( '.postLocation' ).implement(
						"EasyDiscuss.Controller.Location.Map",
						{
							mapType: '<?php echo $system->config->get( 'main_location_map_type');?>',
							width: '<?php echo $system->config->get( 'main_location_map_width' );?>',
							height: '<?php echo $system->config->get( 'main_location_map_height' );?>',
							maxZoom: <?php echo $system->config->get( 'main_location_max_zoom' );?>,
							minZoom: <?php echo $system->config->get( 'main_location_min_zoom' );?>,
							zoom: <?php echo $system->config->get( 'main_location_default_zoom' );?>,
							useStaticMap: <?php echo $system->config->get( 'main_location_static' ) ? 'true' : 'false';?>,
							language: '<?php echo $system->config->get( 'main_location_language' );?>',
							locations: [
								{

									"latitude": "<?php echo $post->latitude; ?>",
									"longitude": "<?php echo $post->longitude; ?>",
									"address": "<?php echo $post->address; ?>"
								}
							]
						}
					)
				});
			</script>

			<div class="postMap-<?php echo $post->id;?> mt-10">
				<div class="locationMap location-map" style="width: <?php echo $system->config->get( 'main_location_map_width');?> !important;height: <?php echo $system->config->get( 'main_location_map_height');?>px !important;">
					<textarea class="locationData" style="display: none;">
						{
							"mapType": "<?php echo $system->config->get( 'main_location_map_type');?>",
							"width": "<?php echo $system->config->get( 'main_location_map_width' );?>",
							"height": "<?php echo $system->config->get( 'main_location_map_height' );?>",
							"maxZoom": "<?php echo $system->config->get( 'main_location_max_zoom' );?>",
							"minZoom": "<?php echo $system->config->get( 'main_location_min_zoom' );?>",
							"zoom": "<?php echo $system->config->get( 'main_location_default_zoom' );?>",
							"useStaticMap": <?php echo $system->config->get( 'main_location_static' ) ? 'true' : 'false';?>,
							"language": "<?php echo $system->config->get( 'main_location_language' );?>",
							"locations": [
								{

									"latitude": "<?php echo $post->latitude; ?>",
									"longitude": "<?php echo $post->longitude; ?>",
									"address": "<?php echo $post->address; ?>"
								}
							]
						}
					</textarea>
				</div>
			</div>
		<?php } ?>
	</div>
</div>
