<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
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
			<?php echo $this->html('panel.head', 'COM_EASYDISCUSS_SETTINGS_LOCATION_GENERAL', '', '/docs/easydiscuss/administrators/how-tos/location-services'); ?>

			<div class="panel-body">
				<div class="o-form-horizontal">
					<?php echo $this->html('settings.toggle', 'main_location_discussion', 'COM_EASYDISCUSS_SETTINGS_LOCATION_ENABLE_DISCUSSION'); ?>

					<?php echo $this->html('settings.toggle', 'main_location_reply', 'COM_EASYDISCUSS_SETTINGS_LOCATION_ENABLE_REPLIES'); ?>

					<?php echo $this->html('settings.textbox', 'main_location_gmaps_key', 'COM_EASYDISCUSS_SETTINGS_LOCATION_GENERAL_GOOGLE_MAPS_API_KEY'); ?>

					<?php echo $this->html('settings.toggle', 'main_location_static', 'COM_EASYDISCUSS_SETTINGS_LOCATION_STATIC_MAPS'); ?>

					<?php echo $this->html('settings.textbox', 'main_location_language', 'COM_EASYDISCUSS_SETTINGS_LOCATION_LANGUAGE', '', 
											array('size' => 5), 
											'<a href="https://developers.google.com/maps/faq#languagesupport" style="margin-left: 5px;" target="_blank">' . JText::_('COM_EASYDISCUSS_LOCATION_AVAILABLE_LANGUAGES') . '</a>', 
											'', 
											'center'
					); ?>

					<?php echo $this->html('settings.dropdown', 'main_location_map_type', 'COM_EASYDISCUSS_SETTINGS_LOCATION_MAP_TYPE', '',
							array('ROADMAP' => 'COM_EASYDISCUSS_LOCATION_ROADMAP', 'SATELLITE' => 'COM_EASYDISCUSS_LOCATION_SATELLITE', 'HYBRID' => 'COM_EASYDISCUSS_LOCATION_HYBRID', 'TERRAIN' => 'COM_EASYDISCUSS_LOCATION_TERRAIN')
					);?>

					<?php echo $this->html('settings.textbox', 'main_location_default_zoom', 'COM_EASYDISCUSS_SETTINGS_LOCATION_DEFAULT_ZOOM_LEVEL', '', array('size' => 5), '', '', 'center'); ?>
				</div>
			</div>
		</div>
	</div>

	<div class="col-md-6">
	</div>
</div>