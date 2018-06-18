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
?>
<div class="row">
	<div class="col-md-6">
		<div class="panel">
			<?php echo $this->html('panel.head', 'COM_EASYDISCUSS_SETTINGS_LOCATION_GENERAL'); ?>

			<div class="panel-body">
				<div class="form-horizontal">
					<?php echo $this->html('settings.toggle', 'main_location_discussion', 'COM_EASYDISCUSS_SETTINGS_LOCATION_ENABLE_DISCUSSION'); ?>

					<?php echo $this->html('settings.toggle', 'main_location_reply', 'COM_EASYDISCUSS_SETTINGS_LOCATION_ENABLE_REPLIES'); ?>

					<?php echo $this->html('settings.textbox', 'main_location_gmaps_key', 'COM_EASYDISCUSS_SETTINGS_LOCATION_GENERAL_GOOGLE_MAPS_API_KEY'); ?>

					<?php echo $this->html('settings.toggle', 'main_location_static', 'COM_EASYDISCUSS_SETTINGS_LOCATION_STATIC_MAPS'); ?>

					<div class="form-group">
						<div class="col-md-5 control-label">
							<?php echo $this->html('form.label', 'COM_EASYDISCUSS_SETTINGS_LOCATION_LANGUAGE'); ?>
						</div>
						<div class="col-md-7">
							<input type="text" name="main_location_language" value="<?php echo $this->config->get('main_location_language');?>" class="form-control form-control-sm text-center" />

							<a href="https://developers.google.com/maps/faq#languagesupport" style="margin-left: 5px;" target="_blank"><?php echo JText::_('COM_EASYDISCUSS_LOCATION_AVAILABLE_LANGUAGES');?></a>
						</div>
					</div>

					<div class="form-group">
						<div class="col-md-5 control-label">
							<?php echo $this->html('form.label', 'COM_EASYDISCUSS_SETTINGS_LOCATION_MAP_TYPE'); ?>
						</div>
						<div class="col-md-7">
							<?php echo $this->html('form.dropdown', 'main_location_map_type', 
										array('ROADMAP' => 'COM_EASYDISCUSS_LOCATION_ROADMAP', 
											'SATELLITE' => 'COM_EASYDISCUSS_LOCATION_SATELLITE', 
											'HYBRID' => 'COM_EASYDISCUSS_LOCATION_HYBRID', 
											'TERRAIN' => 'COM_EASYDISCUSS_LOCATION_TERRAIN'
										), $this->config->get('main_location_map_type')
									); ?>
						</div>
					</div>
					<div class="form-group">
						<div class="col-md-5 control-label">
							<?php echo $this->html('form.label', 'COM_EASYDISCUSS_SETTINGS_LOCATION_DEFAULT_ZOOM_LEVEL'); ?>
						</div>
						<div class="col-md-7">
							<input type="text" name="main_location_default_zoom" value="<?php echo $this->config->get('main_location_default_zoom');?>" class="form-control form-control-sm text-center" />
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="col-md-6">
	</div>
</div>