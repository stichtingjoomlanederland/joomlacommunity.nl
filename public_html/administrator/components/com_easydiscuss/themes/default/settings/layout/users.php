<?php
/**
* @package      EasyDiscuss
* @copyright    Copyright (C) 2010 - 2018 Stack Ideas Sdn Bhd. All rights reserved.
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
			<?php echo $this->html('panel.head', 'COM_EASYDISCUSS_SETTINGS_LAYOUT_USERS_DISPLAY');?>
			<div class="panel-body">
				<div class="form-horizontal">
					<div class="form-group">
						<div class="col-md-5 control-label">
							<?php echo $this->html('form.label', 'COM_EASYDISCUSS_DISPLAY_NAME_FORMAT'); ?>
						</div>
						<div class="col-md-7">
							<?php
								$nameFormat = array();
								$nameFormat[] = JHTML::_('select.option', 'name', JText::_( 'COM_EASYDISCUSS_DISPLAY_NAME_FORMAT_REAL_NAME' ) );
								$nameFormat[] = JHTML::_('select.option', 'username', JText::_( 'COM_EASYDISCUSS_DISPLAY_NAME_FORMAT_USERNAME' ) );
								$nameFormat[] = JHTML::_('select.option', 'nickname', JText::_( 'COM_EASYDISCUSS_DISPLAY_NAME_FORMAT_NICKNAME' ) );
								$showdet = JHTML::_('select.genericlist', $nameFormat, 'layout_nameformat', 'class="form-control"  ', 'value', 'text', $this->config->get('layout_nameformat' , 'name' ) );
								echo $showdet;
							?>
						</div>
					</div>

					<?php echo $this->html('settings.toggle', 'layout_user_online', 'COM_EASYDISCUSS_SHOW_ONLINE_STATE'); ?>
					<?php echo $this->html('settings.toggle', 'layout_timelapse', 'COM_EASYDISCUSS_SHOW_TIMELAPSE'); ?>
					<?php echo $this->html('settings.toggle', 'main_description_visibility', 'COM_EASYDISCUSS_DESCRIPTION_ENABLE'); ?>
					<?php echo $this->html('settings.toggle', 'main_signature_visibility', 'COM_EASYDISCUSS_SIGNATURE_ENABLE'); ?>
					<?php echo $this->html('settings.toggle', 'main_profile_public', 'COM_EASYDISCUSS_ALLOW_PUBLIC_USERS_TO_VIEW_PROFILE'); ?>
				</div>
			</div>
		</div>

		<div class="panel">
			<?php echo $this->html('panel.head', 'COM_EASYDISCUSS_SETTINGS_LAYOUT_MEMBERS_TITLE'); ?>

			<div class="panel-body">
				<div class="form-horizontal">
					<?php echo $this->html('settings.toggle', 'main_user_listings', 'COM_EASYDISCUSS_ALLOW_USER_LISTINGS'); ?>
					<?php echo $this->html('settings.textbox', 'main_exclude_members', 'COM_EASYDISCUSS_EXCLUDE_MEMBERS'); ?>
				</div>
			</div>
		</div>
	</div>

	<div class="col-md-6">
		<div class="panel">
			<?php echo $this->html('panel.head', 'COM_EASYDISCUSS_EDIT_PROFILE'); ?>

			<div class="panel-body">
				<div class="form-horizontal">
					<?php echo $this->html('settings.toggle', 'layout_profile_showaccount', 'COM_EASYDISCUSS_LAYOUT_PROFILE_SHOW_ACCOUNT'); ?>
					<?php echo $this->html('settings.toggle', 'layout_profile_showsocial', 'COM_EASYDISCUSS_LAYOUT_PROFILE_SHOW_SOCIAL'); ?>
					<?php echo $this->html('settings.toggle', 'layout_profile_showlocation', 'COM_EASYDISCUSS_LAYOUT_PROFILE_SHOW_LOCATION'); ?>
					<?php echo $this->html('settings.toggle', 'layout_profile_showurl', 'COM_EASYDISCUSS_LAYOUT_PROFILE_SHOW_URL'); ?>
					<?php echo $this->html('settings.toggle', 'layout_profile_showsite', 'COM_EASYDISCUSS_LAYOUT_PROFILE_SHOW_SITE_DETAILS'); ?>
				</div>
			</div>
		</div>
	</div>
</div>