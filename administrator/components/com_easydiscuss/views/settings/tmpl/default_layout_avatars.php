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
<script type="text/javascript">
EasyDiscuss.ready(function($){

	$( '#layout_avatarIntegration' ).bind( 'change' , function(){

		if( $(this).val() == 'phpbb' )
		{
			$( '.phpbbWrapper' ).show();
		}
		else
		{
			$( '.phpbbWrapper' ).hide();
		}
	});
});
</script>

<div class="row-fluid">
	<div class="span12 panel-title">
		<h2><?php echo JText::_( 'COM_EASYDISCUSS_SETTINGS_LAYOUT_AVATARS_TITLE' );?></h2>
		<p style="margin: 0 0 15px;">
			<?php echo JText::_( 'COM_EASYDISCUSS_SETTINGS_LAYOUT_AVATARS_DESC' );?>
		</p>
	</div>
</div>

<div class="row-fluid ">
	<div class="span6">

		<div class="widget accordion-group">
			<div class="whead accordion-heading">
				<a href="javascript:void(0);" data-foundry-toggle="collapse" data-target="#option01">
				<h6><?php echo JText::_( 'COM_EASYDISCUSS_AVATARS' ); ?></h6>
				<i class="icon-chevron-down"></i>
				</a>
			</div>

			<div id="option01" class="accordion-body collapse in">
				<div class="wbody">
					<div class="si-form-row">
						<div class="span5 form-row-label">
							<label>
								<?php echo JText::_( 'COM_EASYDISCUSS_ENABLE_AVATARS' ); ?>
							</label>
						</div>
						<div class="span7"
							rel="ed-popover"
							data-placement="left"
							data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_ENABLE_AVATARS' ); ?>"
							data-content="<?php echo JText::_('COM_EASYDISCUSS_ENABLE_AVATARS_DESC'); ?>"
						>
							<?php echo $this->renderCheckbox( 'layout_avatar' , $this->config->get( 'layout_avatar' ) );?>
						</div>
					</div>
					<div class="si-form-row">
						<div class="span5 form-row-label">
							<label>
								<?php echo JText::_( 'COM_EASYDISCUSS_ENABLE_AVATARS_IN_POST' ); ?>
							</label>
						</div>
						<div class="span7"
							rel="ed-popover"
							data-placement="left"
							data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_ENABLE_AVATARS_IN_POST' ); ?>"
							data-content="<?php echo JText::_('COM_EASYDISCUSS_ENABLE_AVATARS_IN_POST_DESC'); ?>"
						>
							<?php echo $this->renderCheckbox( 'layout_avatar_in_post' , $this->config->get( 'layout_avatar_in_post' ) );?>
						</div>
					</div>
					<div class="si-form-row">
						<div class="span5 form-row-label">
							<label>
								<?php echo JText::_( 'COM_EASYDISCUSS_AVATARS_SIZE_PIXELS' ); ?>
							</label>
						</div>
						<div class="span7"
							rel="ed-popover"
							data-placement="left"
							data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_AVATARS_SIZE_PIXELS' ); ?>"
							data-content="<?php echo JText::_('COM_EASYDISCUSS_AVATARS_SIZE_PIXELS_DESC'); ?>"
						>
							<input type="text" name="layout_avatarwidth" class="input-mini center" value="<?php echo $this->config->get('layout_avatarwidth', '160' );?>" /> <span class="extra_text" style="margin-right: 5px;">x</span> <input type="text" name="layout_avatarheight" class="input-mini center" value="<?php echo $this->config->get('layout_avatarheight', '160' );?>" />
						</div>
					</div>
					<div class="si-form-row">
						<div class="span5 form-row-label">
							<label>
								<?php echo JText::_( 'COM_EASYDISCUSS_AVATARS_THUMBNAIL_SIZE_PIXELS' ); ?>
							</label>
						</div>
						<div class="span7"
							rel="ed-popover"
							data-placement="left"
							data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_AVATARS_THUMBNAIL_SIZE_PIXELS' ); ?>"
							data-content="<?php echo JText::_('COM_EASYDISCUSS_AVATARS_THUMBNAIL_SIZE_PIXELS_DESC'); ?>"
						>
							<input type="text"  name="layout_avatarthumbwidth" class="input-mini center" value="<?php echo $this->config->get('layout_avatarthumbwidth', '60' );?>" /> <span class="extra_text" style="margin-right: 5px;">x</span> <input  type="text" name="layout_avatarthumbheight" class="input-mini center" value="<?php echo $this->config->get('layout_avatarthumbheight', '60' );?>" />
						</div>
					</div>
					<div class="si-form-row">
						<div class="span5 form-row-label">
							<label>
								<?php echo JText::_( 'COM_EASYDISCUSS_MAX_UPLOAD_SIZE' ); ?>
							</label>
						</div>
						<div class="span7"
							rel="ed-popover"
							data-placement="left"
							data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_MAX_UPLOAD_SIZE' ); ?>"
							data-content="<?php echo JText::_('COM_EASYDISCUSS_MAX_UPLOAD_SIZE_DESC'); ?>"
						>
							<input type="text" name="main_upload_maxsize" class="input-mini center" value="<?php echo $this->config->get('main_upload_maxsize', '0' );?>" />
							<span><?php echo JText::_( 'COM_EASYDISCUSS_MB' );?>
						</div>
					</div>
					<div class="si-form-row">
						<div class="span5 form-row-label">
							<label>
								<?php echo JText::_( 'COM_EASYDISCUSS_ORIGINAL_AVATAR_SIZE' ); ?>
							</label>
						</div>
						<div class="span7"
							rel="ed-popover"
							data-placement="left"
							data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_ORIGINAL_AVATAR_SIZE' ); ?>"
							data-content="<?php echo JText::_('COM_EASYDISCUSS_ORIGINAL_AVATAR_SIZE_DESC'); ?>"
						>
							<input type="text"  name="layout_originalavatarwidth" class="input-mini center" value="<?php echo $this->config->get('layout_originalavatarwidth', '400' );?>" /> <span class="extra_text" style="margin-right: 5px;">x</span> <input  type="text" name="layout_originalavatarheight" class="input-mini center" value="<?php echo $this->config->get('layout_originalavatarheight', '400' );?>" />
						</div>
					</div>
					<div class="si-form-row">
						<div class="span5 form-row-label">
							<label>
								<?php echo JText::_( 'COM_EASYDISCUSS_AVATAR_PATH' ); ?>
							</label>
						</div>
						<div class="span7"
							rel="ed-popover"
							data-placement="left"
							data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_AVATAR_PATH' ); ?>"
							data-content="<?php echo JText::_('COM_EASYDISCUSS_AVATAR_PATH_DESC'); ?>"
						>
							<input type="text" name="main_avatarpath" class="input-full" value="<?php echo $this->config->get('main_avatarpath', 'images/discuss_avatar/' );?>" />
						</div>
					</div>

				</div>
			</div>
		</div>
	</div>

	<div class="span6">
		<div class="widget accordion-group">
			<div class="whead accordion-heading">
				<a href="javascript:void(0);" data-foundry-toggle="collapse" data-target="#avatar-integrations">
				<h6><?php echo JText::_( 'COM_EASYDISCUSS_AVATAR_INTEGRATIONS' ); ?></h6>
				<i class="icon-chevron-down"></i>
				</a>
			</div>

			<div id="avatar-integrations" class="accordion-body collapse in">
				<div class="wbody">
					<div class="si-form-row">
						<div class="span5 form-row-label">
							<label>
								<?php echo JText::_( 'COM_EASYDISCUSS_AVATAR_LINK_INTEGRATION' ); ?>
							</label>
						</div>
						<div class="span7"
							rel="ed-popover"
							data-placement="left"
							data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_AVATAR_LINK_INTEGRATION' ); ?>"
							data-content="<?php echo JText::_('COM_EASYDISCUSS_AVATAR_LINK_INTEGRATION_DESC'); ?>">
							<?php echo $this->renderCheckbox( 'layout_avatarLinking' , $this->config->get( 'layout_avatarLinking' ) ); ?>
						</div>
					</div>

					<div class="si-form-row">
						<div class="span5 form-row-label">
							<label><?php echo JText::_( 'COM_EASYDISCUSS_AVATAR_INTEGRATION' ); ?></label>
						</div>
						<div class="span7"
							rel="ed-popover"
							data-placement="left"
							data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_AVATAR_INTEGRATION' ); ?>"
							data-content="<?php echo JText::_('COM_EASYDISCUSS_AVATAR_INTEGRATION_DESC'); ?>">
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
								$avatarIntegration[] = JHTML::_('select.option', 'phpbb', JText::_( 'PhpBB' ) );
								$showdet = JHTML::_('select.genericlist', $avatarIntegration, 'layout_avatarIntegration', 'class="full-width" size="1" ', 'value', 'text', $this->config->get('layout_avatarIntegration' , 'default' ) );
								echo $showdet;
							?>
						</div>
					</div>

					<div class="phpbbWrapper" style="<?php echo $this->config->get( 'layout_avatarIntegration' ) == 'phpbb' ? 'display: block;' : 'display: none;';?>">
						<div class="si-form-row">
							<div class="span5 form-row-label">
								<label>
									<?php echo JText::_( 'COM_EASYDISCUSS_PHPBB_PATH' ); ?>
								</label>
							</div>
							<div class="span7"
								rel="ed-popover"
								data-placement="left"
								data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_PHPBB_PATH' ); ?>"
								data-content="<?php echo JText::_('COM_EASYDISCUSS_PHPBB_PATH_DESC'); ?>"
							>
								<input type="text" name="layout_phpbb_path" class="input-full" value="<?php echo $this->config->get('layout_phpbb_path', '' );?>" />
							</div>
						</div>
						<div class="si-form-row">
							<div class="span5 form-row-label">
								<label>
									<?php echo JText::_( 'COM_EASYDISCUSS_PHPBB_URL' ); ?>
								</label>
							</div>
							<div class="span7"
								rel="ed-popover"
								data-placement="left"
								data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_PHPBB_URL' ); ?>"
								data-content="<?php echo JText::_('COM_EASYDISCUSS_PHPBB_URL_DESC'); ?>"
							>
								<input type="text" name="layout_phpbb_url" class="input-full" value="<?php echo $this->config->get('layout_phpbb_url', '' );?>" />
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
