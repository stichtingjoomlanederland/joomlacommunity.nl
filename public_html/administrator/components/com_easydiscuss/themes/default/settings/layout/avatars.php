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

<div class="row">
	<div class="col-md-6">
		<div class="panel">
			<?php echo $this->html('panel.head', 'COM_EASYDISCUSS_AVATARS'); ?>

			<div id="option01" class="panel-body">
				<div class="form-horizontal">
					<div class="form-group">
						<div class="col-md-5 control-label">
							<?php echo $this->html('form.label', 'COM_EASYDISCUSS_ENABLE_AVATARS'); ?>
						</div>
						<div class="col-md-7">
							<?php echo $this->html('form.boolean', 'layout_avatar', $this->config->get('layout_avatar'));?>
						</div>
					</div>
					<div class="form-group">
						<div class="col-md-5 control-label">
							<?php echo $this->html('form.label', 'COM_EASYDISCUSS_ENABLE_AVATARS_IN_POST'); ?>
						</div>
						<div class="col-md-7">
							<?php echo $this->html('form.boolean', 'layout_avatar_in_post', $this->config->get('layout_avatar_in_post'));?>
						</div>
					</div>
					<div class="form-group">
						<div class="col-md-5 control-label">
							<?php echo $this->html('form.label', 'COM_EASYDISCUSS_ENABLE_AVATARS_POPBOX'); ?>
						</div>
						<div class="col-md-7">
							<?php echo $this->html('form.boolean', 'layout_avatar_popbox', $this->config->get('layout_avatar_popbox'));?>
						</div>
					</div>					
					<div class="form-group">
						<div class="col-md-5 control-label">
							<?php echo $this->html('form.label', 'COM_EASYDISCUSS_AVATARS_SIZE_PIXELS'); ?>
						</div>
						<div class="col-md-7">
							<input type="text" name="layout_avatarwidth" class="form-control form-control-sm text-center" value="<?php echo $this->config->get('layout_avatarwidth', '160' );?>" /> <span class="extra_text ml-5 mr-5">px</span>
						</div>
					</div>
					<div class="form-group">
						<div class="col-md-5 control-label">
							<?php echo $this->html('form.label', 'COM_EASYDISCUSS_AVATARS_THUMBNAIL_SIZE_PIXELS'); ?>
						</div>
						<div class="col-md-7">
							<input type="text"  name="layout_avatarthumbwidth" class="form-control form-control-sm text-center" value="<?php echo $this->config->get('layout_avatarthumbwidth', '60' );?>" /> <span class="extra_text ml-5 mr-5">px</span>
						</div>
					</div>
					<div class="form-group">
						<div class="col-md-5 control-label">
							<?php echo $this->html('form.label', 'COM_EASYDISCUSS_MAX_UPLOAD_SIZE'); ?>
						</div>
						<div class="col-md-7">
							<input type="text" name="main_upload_maxsize" class="form-control form-control-sm text-center" value="<?php echo $this->config->get('main_upload_maxsize', '0' );?>" />
							<span class="extra_text ml-5 mr-5"><?php echo JText::_( 'COM_EASYDISCUSS_MB' );?>
						</div>
					</div>
					<div class="form-group">
						<div class="col-md-5 control-label">
							<?php echo $this->html('form.label', 'COM_EASYDISCUSS_ORIGINAL_AVATAR_SIZE'); ?>
						</div>
						<div class="col-md-7">
							<input type="text"  name="layout_originalavatarwidth" class="form-control form-control-sm text-center" value="<?php echo $this->config->get('layout_originalavatarwidth', '400' );?>" /> <span class="extra_text ml-5 mr-5">px</span>
						</div>
					</div>
					<div class="form-group">
						<div class="col-md-5 control-label">
							<?php echo $this->html('form.label', 'COM_EASYDISCUSS_AVATAR_PATH'); ?>
						</div>
						<div class="col-md-7">
							<input type="text" name="main_avatarpath" class="form-control" value="<?php echo $this->config->get('main_avatarpath', 'images/discuss_avatar/' );?>" />
						</div>
					</div>

				</div>
			</div>
		</div>
	</div>

	<div class="col-md-6">
		<div class="panel">
			<?php echo $this->html('panel.head', 'COM_EASYDISCUSS_AVATAR_INTEGRATIONS'); ?>

			<div id="avatar-integrations" class="panel-body">
				<div class="form-horizontal">
					<div class="form-group">
						<div class="col-md-5 control-label">
							<?php echo $this->html('form.label', 'COM_EASYDISCUSS_AVATAR_LINK_INTEGRATION'); ?>
						</div>
						<div class="col-md-7">
							<?php echo $this->html('form.boolean', 'layout_avatarLinking', $this->config->get('layout_avatarLinking'));?>
						</div>
					</div>
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
				</div>
			</div>
		</div>
	</div>
</div>
