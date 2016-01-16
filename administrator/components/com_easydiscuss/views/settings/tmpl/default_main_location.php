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
<div class="row-fluid">
	<div class="span12 panel-title">
		<h2><?php echo JText::_( 'COM_EASYDISCUSS_SETTINGS_LOCATION_TITLE' );?></h2>
		<p style="margin: 0 0 15px;">
			<?php echo JText::_( 'COM_EASYDISCUSS_SETTINGS_LOCATION_DESC' );?>
		</p>
	</div>
</div>

<div class="row-fluid">
	<div class="span6">
		<div class="widget accordion-group">
			<div class="whead accordion-heading">
				<a href="javascript:void(0);" data-foundry-toggle="collapse" data-target="#location-general">
				<h6><?php echo JText::_( 'COM_EASYDISCUSS_SETTINGS_LOCATION_GENERAL' ); ?></h6>
				<i class="icon-chevron-down"></i>
				</a>
			</div>

			<div id="location-question" class="accordion-body collapse in">
				<div class="wbody">
					<div class="si-form-row">
						<div class="span5 form-row-label">
							<label>
								<?php echo JText::_( 'COM_EASYDISCUSS_SETTINGS_LOCATION_STATIC_MAPS' ); ?>
							</label>
						</div>
						<div class="span7"
							rel="ed-popover"
							data-placement="left"
							data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_SETTINGS_LOCATION_STATIC_MAPS' ); ?>"
							data-content="<?php echo JText::_('COM_EASYDISCUSS_SETTINGS_LOCATION_STATIC_MAPS_DESC'); ?>">
							<?php echo $this->renderCheckbox( 'main_location_static' , $this->config->get( 'main_location_static' ) );?>
						</div>
					</div>

					<div class="si-form-row">
						<div class="span5 form-row-label">
							<label>
								<?php echo JText::_( 'COM_EASYDISCUSS_SETTINGS_LOCATION_DEFAULT_MAP_WIDTH' ); ?>
							</label>
						</div>
						<div class="span7"
							rel="ed-popover"
							data-placement="left"
							data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_SETTINGS_LOCATION_DEFAULT_MAP_WIDTH' ); ?>"
							data-content="<?php echo JText::_('COM_EASYDISCUSS_SETTINGS_LOCATION_DEFAULT_MAP_WIDTH_DESC'); ?>">
							<input type="text" name="main_location_map_width" value="<?php echo $this->config->get( 'main_location_map_width' );?>" class="input-mini center" />
							<span><?php echo JText::_( 'COM_EASYDISCUSS_PIXELS' ); ?></span>
						</div>
					</div>

					<div class="si-form-row">
						<div class="span5 form-row-label">
							<label>
								<?php echo JText::_( 'COM_EASYDISCUSS_SETTINGS_LOCATION_DEFAULT_MAP_HEIGHT' ); ?>
							</label>
						</div>
						<div class="span7"
							rel="ed-popover"
							data-placement="left"
							data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_SETTINGS_LOCATION_DEFAULT_MAP_HEIGHT' ); ?>"
							data-content="<?php echo JText::_('COM_EASYDISCUSS_SETTINGS_LOCATION_DEFAULT_MAP_HEIGHT_DESC'); ?>">
							<input type="text" name="main_location_map_height" value="<?php echo $this->config->get( 'main_location_map_height' );?>" class="input-mini center" />
							<span><?php echo JText::_( 'COM_EASYDISCUSS_PIXELS' ); ?></span>
						</div>
					</div>

					<div class="si-form-row">
						<div class="span5 form-row-label">
							<label>
								<?php echo JText::_( 'COM_EASYDISCUSS_SETTINGS_LOCATION_LANGUAGE' ); ?>
							</label>
						</div>
						<div class="span7"
							rel="ed-popover"
							data-placement="left"
							data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_SETTINGS_LOCATION_LANGUAGE' ); ?>"
							data-content="<?php echo JText::_('COM_EASYDISCUSS_SETTINGS_LOCATION_LANGUAGE_DESC'); ?>">
							<input type="text" name="main_location_language" value="<?php echo $this->config->get( 'main_location_language' );?>" class="input-mini center" />

							<a target="_blank" href="https://spreadsheets.google.com/a/stackideas.com/pub?key=p9pdwsai2hDMsLkXsoM05KQ&amp;gid=1" class="mlm"><?php echo JText::_( 'COM_EASYDISCUSS_LOCATION_AVAILABLE_LANGUAGES' );?></a>
						</div>
					</div>

					<div class="si-form-row">
						<div class="span5 form-row-label">
							<label>
								<?php echo JText::_( 'COM_EASYDISCUSS_SETTINGS_LOCATION_MAP_TYPE' ); ?>
							</label>
						</div>
						<div class="span7"
							rel="ed-popover"
							data-placement="left"
							data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_SETTINGS_LOCATION_MAP_TYPE' ); ?>"
							data-content="<?php echo JText::_('COM_EASYDISCUSS_SETTINGS_LOCATION_MAP_TYPE_DESC'); ?>">
							<select class="inputbox full-width" name="main_location_map_type">
								<option value="ROADMAP"<?php echo $this->config->get( 'main_location_map_type' ) == 'ROADMAP' ? ' selected="selected"' : '';?>><?php echo JText::_( 'COM_EASYDISCUSS_LOCATION_ROADMAP' );?></option>
								<option value="SATELLITE"<?php echo $this->config->get( 'main_location_map_type' ) == 'SATELLITE' ? ' selected="selected"' : '';?>><?php echo JText::_( 'COM_EASYDISCUSS_LOCATION_SATELLITE' );?></option>
								<option value="HYBRID"<?php echo $this->config->get( 'main_location_map_type' ) == 'HYBRID' ? ' selected="selected"' : '';?>><?php echo JText::_( 'COM_EASYDISCUSS_LOCATION_HYBRID' );?></option>
								<option value="TERRAIN"<?php echo $this->config->get( 'main_location_map_type' ) == 'TERRAIN' ? ' selected="selected"' : '';?>><?php echo JText::_( 'COM_EASYDISCUSS_LOCATION_TERRAIN' );?></option>
							</select>
						</div>
					</div>

					<div class="si-form-row">
						<div class="span5 form-row-label">
							<label>
								<?php echo JText::_( 'COM_EASYDISCUSS_SETTINGS_LOCATION_DEFAULT_ZOOM_LEVEL' ); ?>
							</label>
						</div>
						<div class="span7"
							rel="ed-popover"
							data-placement="left"
							data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_SETTINGS_LOCATION_DEFAULT_ZOOM_LEVEL' ); ?>"
							data-content="<?php echo JText::_('COM_EASYDISCUSS_SETTINGS_LOCATION_DEFAULT_ZOOM_LEVEL_DESC'); ?>">
							<input type="text" name="main_location_default_zoom" value="<?php echo $this->config->get( 'main_location_default_zoom' );?>" class="input-mini center" />
						</div>
					</div>

					<div class="si-form-row">
						<div class="span5 form-row-label">
							<label>
								<?php echo JText::_( 'COM_EASYDISCUSS_SETTINGS_LOCATION_MIN_ZOOM_LEVEL' ); ?>
							</label>
						</div>
						<div class="span7"
							rel="ed-popover"
							data-placement="left"
							data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_SETTINGS_LOCATION_MIN_ZOOM_LEVEL' ); ?>"
							data-content="<?php echo JText::_('COM_EASYDISCUSS_SETTINGS_LOCATION_MIN_ZOOM_LEVEL_DESC'); ?>">
							<input type="text" name="main_location_min_zoom" value="<?php echo $this->config->get( 'main_location_min_zoom' );?>" class="input-mini center" />
						</div>
					</div>

					<div class="si-form-row">
						<div class="span5 form-row-label">
							<label>
								<?php echo JText::_( 'COM_EASYDISCUSS_SETTINGS_LOCATION_MAX_ZOOM_LEVEL' ); ?>
							</label>
						</div>
						<div class="span7"
							rel="ed-popover"
							data-placement="left"
							data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_SETTINGS_LOCATION_MAX_ZOOM_LEVEL' ); ?>"
							data-content="<?php echo JText::_('COM_EASYDISCUSS_SETTINGS_LOCATION_MAX_ZOOM_LEVEL_DESC'); ?>">
							<input type="text" name="main_location_max_zoom" value="<?php echo $this->config->get( 'main_location_max_zoom' );?>" class="input-mini center" />
						</div>
					</div>



				</div>
			</div>
		</div>
	</div>

	<div class="span6">
		<div class="widget accordion-group">
			<div class="whead accordion-heading">
				<a href="javascript:void(0);" data-foundry-toggle="collapse" data-target="#location-question">
				<h6><?php echo JText::_( 'COM_EASYDISCUSS_SETTINGS_LOCATION_DISCUSSION' ); ?></h6>
				<i class="icon-chevron-down"></i>
				</a>
			</div>

			<div id="location-question" class="accordion-body collapse in">
				<div class="wbody">
					<div class="si-form-row">
						<div class="span5 form-row-label">
							<label>
								<?php echo JText::_( 'COM_EASYDISCUSS_SETTINGS_LOCATION_ENABLE_DISCUSSION' ); ?>
							</label>
						</div>
						<div class="span7"
							rel="ed-popover"
							data-placement="left"
							data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_SETTINGS_LOCATION_ENABLE_DISCUSSION' ); ?>"
							data-content="<?php echo JText::_('COM_EASYDISCUSS_SETTINGS_LOCATION_ENABLE_DISCUSSION_DESC'); ?>">
							<?php echo $this->renderCheckbox( 'main_location_discussion' , $this->config->get( 'main_location_discussion' ) );?>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="widget accordion-group">
			<div class="whead accordion-heading">
				<a href="javascript:void(0);" data-foundry-toggle="collapse" data-target="#location-replies">
				<h6><?php echo JText::_( 'COM_EASYDISCUSS_SETTINGS_LOCATION_REPLIES' ); ?></h6>
				<i class="icon-chevron-down"></i>
				</a>
			</div>

			<div id="location-replies" class="accordion-body collapse in">
				<div class="wbody">
					<div class="si-form-row">
						<div class="span5 form-row-label">
							<label>
								<?php echo JText::_( 'COM_EASYDISCUSS_SETTINGS_LOCATION_ENABLE_REPLIES' ); ?>
							</label>
						</div>
						<div class="span7"
							rel="ed-popover"
							data-placement="left"
							data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_SETTINGS_LOCATION_ENABLE_REPLIES' ); ?>"
							data-content="<?php echo JText::_('COM_EASYDISCUSS_SETTINGS_LOCATION_ENABLE_REPLIES_DESC'); ?>">
							<?php echo $this->renderCheckbox( 'main_location_reply' , $this->config->get( 'main_location_reply' ) );?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

