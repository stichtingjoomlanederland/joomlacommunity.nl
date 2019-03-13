<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 - 2018 Stack Ideas Sdn Bhd. All rights reserved.
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
			<?php echo $this->html('panel.head', 'COM_EASYDISCUSS_DISPLAY'); ?>

			<div class="panel-body">
				<div class="form-horizontal">
					<?php echo $this->html('settings.textbox', 'layout_wrapper_sfx', 'COM_EASYDISCUSS_WRAPPERCLASS_SFX'); ?>
					<?php echo $this->html('settings.toggle', 'main_responsive', 'COM_EASYDISCUSS_ENABLE_RESPONSIVE'); ?>
					<?php echo $this->html('settings.toggle', 'layout_board_stats', 'COM_EASYDISCUSS_ENABLE_BOARD_STATISTICS'); ?>
					<?php echo $this->html('settings.textbox', 'layout_list_limit', 'COM_EASYDISCUSS_LIST_LIMIT', '', array('size' => 7, 'postfix' => 'Posts'), '', 'form-control-sm text-center'); ?>
					<?php echo $this->html('settings.textbox', 'layout_daystostaynew', 'COM_EASYDISCUSS_NUMBER_OF_DAYS_A_POST_STAY_AS_NEW', '', array('size' => 7, 'postfix' => 'Days'), '', 'form-control-sm text-center'); ?>
					<?php echo $this->html('settings.toggle', 'layout_zero_as_plural', 'COM_EASYDISCUSS_SETTINGS_ZERO_AS_PLURAL'); ?>
					<?php echo $this->html('settings.toggle', 'main_copyright_link_back', 'COM_EASYDISCUSS_ENABLE_POWERED_BY'); ?>
				</div>
			</div>
		</div>
	</div>

	<div class="col-md-6">

		<div class="panel">
			<?php echo $this->html('panel.head', 'COM_EASYDISCUSS_AVATARS'); ?>

			<div class="panel-body">
				<div class="form-horizontal">
					<?php echo $this->html('settings.toggle', 'layout_avatarLinking', 'COM_EASYDISCUSS_AVATAR_LINK_INTEGRATION'); ?>

					<div class="form-group">
						<div class="col-md-5 control-label">
							<?php echo $this->html('form.label', 'COM_EASYDISCUSS_AVATAR_INTEGRATION'); ?>
						</div>
						<div class="col-md-7">
							<?php
								$nameFormat = array();
								$avatarIntegration[] = JHTML::_('select.option', 'default', JText::_( 'Default' ) );
								$avatarIntegration[] = JHTML::_('select.option', 'easysocial', JText::_( 'EasySocial' ) );
								$avatarIntegration[] = JHTML::_('select.option', 'anahita', JText::_( 'Anahita' ) );
								$avatarIntegration[] = JHTML::_('select.option', 'communitybuilder', JText::_( 'Community Builder' ) );
								$avatarIntegration[] = JHTML::_('select.option', 'easyblog', JText::_( 'EasyBlog' ) );
								$avatarIntegration[] = JHTML::_('select.option', 'gravatar', JText::_( 'Gravatar' ) );
								$avatarIntegration[] = JHTML::_('select.option', 'jfbconnect', JText::_( 'JFBConnect' ) );
								$avatarIntegration[] = JHTML::_('select.option', 'jomsocial', JText::_( 'Jomsocial' ) );
								$avatarIntegration[] = JHTML::_('select.option', 'k2', JText::_( 'k2' ) );
								$avatarIntegration[] = JHTML::_('select.option', 'kunena', JText::_( 'Kunena' ) );
								$avatarIntegration[] = JHTML::_('select.option', 'jomwall', JText::_('JomWall'));
								$avatarIntegration[] = JHTML::_('select.option', 'phpbb', JText::_( 'PhpBB' ) );
								$avatarIntegration[] = JHTML::_('select.option', 'jsn', JText::_('JSN Profile'));
								
								$showdet = JHTML::_('select.genericlist', $avatarIntegration, 'layout_avatarIntegration', 'class="form-control"  ', 'value', 'text', $this->config->get('layout_avatarIntegration' , 'default' ) );
								echo $showdet;
							?>
						</div>
					</div>

					<div class="phpbbWrapper" style="<?php echo $this->config->get('layout_avatarIntegration') == 'phpbb' ? 'display: block;' : 'display: none;';?>">
						<div class="form-group">
							<div class="col-md-5 control-label">
								<?php echo $this->html('form.label', 'COM_EASYDISCUSS_PHPBB_PATH'); ?>
							</div>
						<div class="col-md-7">
								<input type="text" name="layout_phpbb_path" class="form-control" value="<?php echo $this->config->get('layout_phpbb_path', '' );?>" />
							</div>
						</div>
						<div class="form-group">
							<div class="col-md-5 control-label">
								<?php echo $this->html('form.label', 'COM_EASYDISCUSS_PHPBB_URL'); ?>
							</div>
						<div class="col-md-7">
								<input type="text" name="layout_phpbb_url" class="form-control" value="<?php echo $this->config->get('layout_phpbb_url', '' );?>" />
							</div>
						</div>
					</div>

					<?php echo $this->html('settings.toggle', 'layout_avatar', 'COM_EASYDISCUSS_ENABLE_AVATARS'); ?>
					<?php echo $this->html('settings.toggle', 'layout_avatar_in_post', 'COM_EASYDISCUSS_ENABLE_AVATARS_IN_POST'); ?>
					<?php echo $this->html('settings.toggle', 'layout_avatar_popbox', 'COM_EASYDISCUSS_ENABLE_AVATARS_POPBOX'); ?>
					<?php echo $this->html('settings.toggle', 'layout_badges_in_post', 'COM_ED_BADGES_IN_POST'); ?>
					<?php echo $this->html('settings.textbox', 'layout_avatarwidth', 'COM_EASYDISCUSS_AVATARS_SIZE_PIXELS', '', array('size' => 6, 'postfix' => 'px'), '', 'form-control-sm text-center'); ?>
					<?php echo $this->html('settings.textbox', 'layout_avatarthumbwidth', 'COM_EASYDISCUSS_AVATARS_THUMBNAIL_SIZE_PIXELS', '', array('size' => 6, 'postfix' => 'px'), '', 'form-control-sm text-center'); ?>
					<?php echo $this->html('settings.textbox', 'main_upload_maxsize', 'COM_EASYDISCUSS_MAX_UPLOAD_SIZE', '', array('size' => 6, 'postfix' => 'MB'), '', 'form-control-sm text-center'); ?>
					<?php echo $this->html('settings.textbox', 'layout_originalavatarwidth', 'COM_EASYDISCUSS_ORIGINAL_AVATAR_SIZE', '', array('size' => 6, 'postfix' => 'px'), '', 'form-control-sm text-center'); ?>
					<?php echo $this->html('settings.textbox', 'main_avatarpath', 'COM_EASYDISCUSS_AVATAR_PATH', '', array('defaultValue' => 'images/discuss_avatar/')); ?>

				</div>
			</div>
		</div>
	</div>
</div>
