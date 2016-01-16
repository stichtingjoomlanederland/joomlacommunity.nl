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
?>
<?php if( $post->isQuestion() && $system->config->get( 'main_location_discussion') || $post->isReply() && $system->config->get( 'main_location_reply' ) ){ ?>
<script type="text/javascript">
EasyDiscuss
	.require()
	.script( 'location'  )
	.done(function($){

		$( '.locationForm' ).implement( EasyDiscuss.Controller.Location.Form,
		{
			<?php if ( $post->address ){ ?>
			initialLocation: "<?php echo $post->address; ?>",
			<?php } ?>

			// Map settings
			height: '250px',
			width: '100%',
			mapType : "ROADMAP",
			language: '<?php echo $system->config->get( 'main_location_language' );?>',
			"{locationInput}"		: "input[name=address]",
			"{locationLatitude}"	: "input[name=latitude]",
			"{locationLongitude}"	: "input[name=longitude]"
		});
});
</script>

<div class="discuss-location locationForm">
	<div class="discuss-location-map ">
		<label class="control-label small" for="shareAdress"><?php echo JText::_( 'COM_EASYDISCUSS_SHARE_LOCATION' );?> :</label>
		<div class="controls">
			<div class="input-append">
				<input type="text" id="shareAdress" name="address" class="input-xlarge has-icon publish-location loading" autocomplete="off" value="<?php echo $post->address; ?>" />
				<a class="btn btn-medium btn-location autoDetectButton" href="javascript: void(0);"><i class="icon-map-marker"></i></a>
				<a class="btn btn-medium btn-danger removeLocationButton" href="javascript:void(0);"><i class="icon-remove"></i></a>
			</div>
		</div>

		<div class="discuss-location-coords small locationCoords">
			<?php echo JText::_( 'COM_EASYDISCUSS_LATITUDE' );?>: <span class="latitudeDisplay"><?php echo $post->latitude; ?></span> ,
			<?php echo JText::_( 'COM_EASYDISCUSS_LONGITUDE' );?>: <span class="longitudeDisplay"><?php echo $post->longitude; ?></span>.
		</div>

		<input type="hidden" name="latitude" value="<?php echo $post->latitude;?>" />
		<input type="hidden" name="longitude" value="<?php echo $post->longitude;?>" />

		<div class="locationMap location-map" style="display: none; "></div>
	</div>
</div>
<?php } ?>
