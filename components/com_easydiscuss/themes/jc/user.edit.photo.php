<?php
/**
 * @package		EasyDiscuss
 * @copyright	Copyright (C) 2010 Stack Ideas Private Limited. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 *
 * EasyDiscuss is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

defined('_JEXEC') or die('Restricted access');
?>

<div class="tab-item user-photo">

<?php if( $avatarIntegration == 'gravatar' ){ ?>
<p>
	<?php echo JText::_( 'COM_EASYDISCUSS_AVATARS_INTEGRATED_WITH');?> <a href="http://gravatar.com" target="_blank"><?php echo JText::_( 'COM_EASYDISCUSS_GRAVATAR' );?></a>.<br />
	<?php echo JText::_( 'COM_EASYDISCUSS_GRAVATAR_EMAIL' );?> <strong><?php echo $user->get( 'email' ); ?></strong>
</p>
<?php } ?>

<?php if( $config->get('layout_avatar') && $avatarIntegration == 'default' ) { ?>

	<p><?php echo JText::_( 'COM_EASYDISCUSS_PROFILE_AVATAR_DESC'); ?></p>
	<div class="control-group">
		<div id="file-upload-area">
			<input id="file-upload" type="file" name="Filedata" size="25"/>
		</div>
	</div>
	<div class="alert"><?php echo JText::sprintf( 'COM_EASYDISCUSS_PROFILE_AVATAR_CONDITION' , $configMaxSize, $size ); ?></div>

	<?php
		$originalAvatar = $profile->getOriginalAvatar();
		$croppable = ($originalAvatar !== false && $croppable);
	?>

	<?php if( $croppable ) { ?>

		<hr />

		<script type="text/javascript">
		EasyDiscuss.require()
			.script("avatar")
			.done(function($){
				$(".avatarCropper").implement(EasyDiscuss.Controller.Avatar);
			});
		</script>

		<div class="avatarCropper">
			<p>
				<?php echo JText::_( 'COM_EASYDISCUSS_ORIGINAL_IMAGE' );?>
			</p>
			<p>
				<span class="cropActions">
					<button class="startCropButton butt butt-default" type="button">
						<i class="i i-crop muted"></i>
						&nbsp;
						<?php echo JText::_( 'COM_EASYDISCUSS_CROP_IMAGE' );?>
					</button>
					<button class="saveCropButton butt butt-primary" type="button">
						<i class="i i-check muted"></i>
						&nbsp;
						<?php echo JText::_( 'COM_EASYDISCUSS_UPDATE_BUTTON');?>
					</button>
					<button class="stopCropButton butt butt-default" type="button">
						<?php echo JText::_( 'COM_EASYDISCUSS_CANCEL' ); ?>
					</button>
				</span>
				<?php if($croppable ){ ?>
				<a href="<?php echo DiscussRouter::_( 'index.php?option=com_easydiscuss&controller=profile&task=removePicture' );?>" class="butt butt-default">
					<i class="i i-times muted"></i>
					&nbsp;
					<?php echo JText::_( 'COM_EASYDISCUSS_REMOVE_PICTURE' ); ?></a>
				<?php } ?>
			</p>
			<div class="avatarContainer">
				<img src="<?php echo $originalAvatar; ?>" />
			</div>
			
			<hr>

			<p><?php echo JText::_( 'COM_EASYDISCUSS_YOUR_PICTURE' );?></p>

			<div class="avatarPreviewContainer">
				<img class="avatarPreviewPlaceholder" src="<?php echo $profile->getAvatar(false); ?>" />
				<img class="avatarPreview" src="<?php echo $originalAvatar; ?>" />
			</div>
		</div>

	<?php } else { ?>

		<p><?php echo JText::_( 'COM_EASYDISCUSS_YOUR_PICTURE' );?></p>
		<img src="<?php echo $profile->getAvatar(false); ?>" />

	<?php } ?>


<?php } else { ?>

	<?php echo JText::_('COM_EASYDISCUSS_PROFILE_AVATAR_DISABLED'); ?>

<?php } ?>

</div>
