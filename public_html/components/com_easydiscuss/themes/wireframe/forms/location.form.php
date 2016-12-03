<?php
/**
* @package      EasyDiscuss
* @copyright    Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
$hasLocation = isset($hasLocation) ? $hasLocation : false;
?>
<div class="ed-location-form<?php echo $hasLocation ? ' has-location' : '';?>" data-ed-location-form>
        <div class="input-group">
            <input type="text" name="address" placeholder="<?php echo JText::_("COM_EASYDISCUSS_LOCATION_START_TYPING_ADDRESS");?>" data-ed-location-address value="<?php echo $address;?>" />
            <span class="input-group-btn">
                <a href="javascript: void(0);" class="btn btn-default" data-ed-location-detect>
                    <i class="fa fa-location-arrow"></i>
                </a>

                <a href="javascript:void(0);" class="btn btn-danger" data-ed-location-remove>
                    <i class="fa fa-times"></i>
                </a>
            </span>
        </div>

        <div class="ed-location-form__map">
            <div id="map" class="ed-location-form__map-inner" style="height: 250px;" data-ed-location-map></div>
        </div>

        <div class="ed-location-form__coords">
            <div class="form-inline">
                <div class="form-group">
                    <?php echo JText::_('COM_EASYDISCUSS_LATITUDE');?>:
                    <input type="text" class="form-control input-sm" name="latitude" readonly data-ed-location-latitude value="<?php echo $latitude; ?>" />
                </div>

                <div class="form-group">
                    <?php echo JText::_('COM_EASYDISCUSS_LONGITUDE');?>:
                    <input type="text" class="form-control input-sm" name="longitude" readonly data-ed-location-longitude value="<?php echo $longitude; ?>"/>
                </div>
            </div>
        </div>
</div>
