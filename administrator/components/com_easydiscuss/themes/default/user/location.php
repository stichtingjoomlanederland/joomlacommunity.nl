<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 Stack Ideas Private Limited. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Restricted access');
?>
<div id="location" class="tab-pane locationForm">
	<div class="row">
		<div class="col-md-10">
			<div class="panel">
			<?php echo $this->html('panel.head', 'COM_EASYDISCUSS_SETTINGS_TAB_LOCATION'); ?>
				<div class="panel-body">
					<div class="form-horizontal">
						<div class="form-group">
							<div class="col-md-4 control-label">
	                            <?php echo $this->html('form.label', 'COM_EASYDISCUSS_USER_CURRENT_LOCATION'); ?>
	                        </div>

	                        <div class="col-md-8">
	                        	<?php $locations = array('latitude' => $profile->latitude, 'longitude' => $profile->longitude, 'address' => $profile->location, 'hasLocation' => $profile->hasLocation()) ?>
    							<?php echo $this->output('site/forms/location.form', $locations); ?>
							</div>

							<input type="hidden" name="latitude" value="<?php echo $profile->latitude;?>" data-ed-location-latitude />
							<input type="hidden" name="longitude" value="<?php echo $profile->longitude;?>" data-ed-location-longitude />

						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

