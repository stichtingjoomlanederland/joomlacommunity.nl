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
			<?php echo $this->html('panel.head', 'COM_EASYDISCUSS_AVATARS'); ?>

			<div class="panel-body">
				<div class="o-form-horizontal">
					<?php echo $this->html('settings.dropdown', 'layout_avatarIntegration', 'COM_EASYDISCUSS_AVATAR_INTEGRATION', '', 
						array(
							'default' => 'Default',
							'easysocial' => 'EasySocial',
							'communitybuilder' => 'Community Builder',
							'easyblog' => 'EasyBlog',
							'gravatar' => 'Gravatar',
							'jfbconnect' => 'JFBConnect',
							'jomsocial' => 'JomSocial',
							'k2' => 'K2',
							'kunena' => 'Kunena',
							'jomwall' => 'JomWall',
							'jsn' => 'JSN Profile'
						)
					);?>

					<?php echo $this->html('settings.toggle', 'layout_text_avatar', 'COM_ED_USE_TEXT_AVATARS'); ?>
					<?php echo $this->html('settings.toggle', 'layout_badges_in_post', 'COM_ED_BADGES_IN_POST'); ?>
					<?php echo $this->html('settings.textbox', 'layout_avatarwidth', 'COM_EASYDISCUSS_AVATARS_SIZE_PIXELS', '', array('size' => 6, 'postfix' => 'px'), '', '', 'text-center'); ?>
					<?php echo $this->html('settings.textbox', 'layout_avatarthumbwidth', 'COM_EASYDISCUSS_AVATARS_THUMBNAIL_SIZE_PIXELS', '', array('size' => 6, 'postfix' => 'px'), '', '', 'text-center'); ?>
					<?php echo $this->html('settings.textbox', 'main_upload_maxsize', 'COM_EASYDISCUSS_MAX_UPLOAD_SIZE', '', array('size' => 6, 'postfix' => 'MB'), '', '', 'text-center'); ?>
					<?php echo $this->html('settings.textbox', 'layout_originalavatarwidth', 'COM_EASYDISCUSS_ORIGINAL_AVATAR_SIZE', '', array('size' => 6, 'postfix' => 'px'), '', '', 'text-center'); ?>
					<?php echo $this->html('settings.textbox', 'main_avatarpath', 'COM_EASYDISCUSS_AVATAR_PATH', '', array('defaultValue' => 'images/discuss_avatar/')); ?>

				</div>
			</div>
		</div>
	</div>

	<div class="col-md-6">
	</div>
</div>