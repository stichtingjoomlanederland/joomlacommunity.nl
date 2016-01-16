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
	<p>
		<input type="text" id="shareAdress" name="address" class="form-control publish-location loading" autocomplete="off" value="<?php echo $post->address; ?>" placeholder="<?php echo JText::_( 'COM_EASYDISCUSS_SHARE_LOCATION' );?>" />
	</p>
	<p>
		<a class="butt butt-default btn-location autoDetectButton" href="javascript: void(0);">
			<i class="i i-map-marker muted"></i> 
			&nbsp;
			<?php echo JText::_( 'COM_EASYDISCUSS_AUTO_DETECT_LOCATION' );?>
		</a>
		<a class="butt butt-default removeLocationButton" href="javascript:void(0);">
			<i class="i i-times muted"></i> 
			&nbsp;
			<?php echo JText::_( 'COM_EASYDISCUSS_CANCEL' );?>
		</a>
	</p>
	<p class="discuss-location-coords muted locationCoords">
		<?php echo JText::_( 'COM_EASYDISCUSS_LATITUDE' );?>: <span class="latitudeDisplay"><?php echo $post->latitude; ?></span> ,
		<?php echo JText::_( 'COM_EASYDISCUSS_LONGITUDE' );?>: <span class="longitudeDisplay"><?php echo $post->longitude; ?></span>.
	</p>

	<input type="hidden" name="latitude" value="<?php echo $post->latitude;?>" />
	<input type="hidden" name="longitude" value="<?php echo $post->longitude;?>" />

	<div class="locationMap location-map" style="display: none; "></div>
</div>
<?php } ?>
