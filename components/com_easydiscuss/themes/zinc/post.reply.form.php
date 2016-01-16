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

<?php if ( $post->islock ) { ?>
<div class="alert alert-notice mt-20">
	<i class='icon-lock'></i>
	<?php if( DiscussHelper::isModerator( $post->category_id ) ){ ?>
		<?php echo JText::_('COM_EASYDISCUSS_POST_IS_CURRENTLY_LOCKED_BUT_MODERATOR'); ?>
	<?php } else { ?>
		<?php echo JText::_('COM_EASYDISCUSS_POST_IS_CURRENTLY_LOCKED'); ?>
	<?php } ?>
</div>
<?php } ?>

<?php if (!$post->islock || DiscussHelper::isModerator($post->category_id)){ ?>
<div class="discuss-user-reply" >
		<a name="respond" id="respond"></a>

		

		<!-- Note: please update form user_type = twitter | facebook | linkedin -->
		<!-- div class="control-group">
			<a href="javascript:void(0);" class="btn btn-mini btn-facebook"><i class="icon-facebook"></i> Reply with Facebook</a>
			<a href="javascript:void(0);" class="btn btn-mini btn-twitter"><i class="icon-twitter"></i> Reply with Twitter</a>
		</div -->

		<?php if( $access->canReply() ){ ?>
		
			<p><b><?php echo JText::_('COM_EASYDISCUSS_ENTRY_YOUR_RESPONSE'); ?></b></p>
			<?php echo $composer->getComposer(); ?>

		<?php } else { ?>

			<?php if( !$system->my->id ){ ?>
				<?php echo $this->loadTemplate( 'form.reply.login.php' ); ?>
			<?php } else { ?>
				<div class="alert alert-notice"><?php echo JText::_( 'COM_EASYDISCUSS_NOT_ALLOWED_TO_REPLY' );?></div>
			<?php } ?>

		<?php } ?>
</div>
<?php } ?>
