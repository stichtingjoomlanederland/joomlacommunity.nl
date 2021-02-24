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
defined('_JEXEC') or die('Restricted access');
?>
<div id="location-<?php echo $editorId;?>" class="tab-pane ed-editor-tab__content">

	<div class="ed-editor-tab__content-note">
		<p><?php echo JText::_('COM_EASYDISCUSS_SHARE_LOCATION_INFO'); ?></p>
	</div>

	<div class="ed-location-form<?php echo $post->hasLocation() ? ' has-location' : '';?>" data-ed-location-form>
		<div class="ed-location-form__input-group">
			<input type="text" name="address" placeholder="<?php echo JText::_("COM_EASYDISCUSS_LOCATION_START_TYPING_ADDRESS");?>" data-ed-location-address value="<?php echo $post->address;?>" />
			
			<a href="javascript: void(0);" class="o-btn o-btn--default-o ed-location-form__btn-detect" data-ed-location-detect>
				<i class="fa fa-location-arrow"></i>
			</a>

			<a href="javascript:void(0);" class="o-btn o-btn--default-o ed-location-form__btn-remove" data-ed-location-remove>
				<i class="fa fa-times"></i>
			</a>
		</div>

		<div class="ed-location-form__map">
			<div id="map" class="ed-location-form__map-inner" style="height: 250px;" data-ed-location-map></div>
		</div>

		<input type="hidden" name="latitude" readonly data-ed-location-latitude value="<?php echo $post->latitude; ?>" />
		<input type="hidden" class="o-form-control" name="longitude" readonly data-ed-location-longitude value="<?php echo $post->longitude; ?>"/>
	</div>


	<?php //echo $this->output('site/forms/location.form', array('latitude' => $post->latitude, 'longitude' => $post->longitude, 'address' => $post->address, 'hasLocation' => $post->hasLocation())); ?>
</div>