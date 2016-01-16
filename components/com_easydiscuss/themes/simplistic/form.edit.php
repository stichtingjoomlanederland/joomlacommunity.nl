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

<div class="discuss-composer <?php echo $composer->classname; ?> discuss-composer-<?php echo $composer->operation; ?>"
	 data-id="<?php echo $composer->id; ?>"
	 data-editortype="<?php echo $composer->editorType ?>"
	 data-operation="<?php echo $composer->operation; ?>"
	 >

	<div class="alert replyNotification" style="display: none;"></div>

	<div class="discuss-story">
		<div class="discuss-content">
			<form name="dc_submit" autocomplete="off" class="form-horizontal" action="<?php echo DiscussRouter::_('index.php?option=com_easydiscuss&controller=posts&task=reply'); ?>" method="post">

				<div class="discuss-form">

					<div class="fd-cf">
						<?php echo $composer->getEditor(); ?>
					</div>

					<?php if ($system->config->get('main_location_reply')) { ?>
					<div class="control-group">
						<?php echo $this->loadTemplate('form.location.php'); ?>
					</div>
					<?php } ?>

					<div class="control-group">
						<?php echo $composer->getFields(); ?>
					</div>

					<?php if ($captcha = $this->getRecaptcha()) { ?>
					<div class="control-group">
						<hr/>
						<div class="respond-recaptcha mt-10"><?php echo $captcha; ?></div>
					</div>
					<?php }else if( DiscussHelper::getHelper( 'Captcha' )->showCaptcha() ){ ?>
						<?php echo DiscussHelper::getHelper( 'Captcha' )->getHTML();?>
					<?php } ?>

					<div class="form-actions">
						<div class="pull-right">
							<input type="button" name="cancel-reply" class="btn btn-medium cancel-reply" value="<?php echo JText::_('COM_EASYDISCUSS_CANCEL'); ?>" />
							<input type="button" name="submit-reply" class="btn btn-primary btn-medium submit-reply" value="<?php echo JText::_('COM_EASYDISCUSS_BUTTON_SAVE'); ?>" />
						</div>
						<div class="pull-right reply-loading"></div>
					</div>

					<input type="hidden" name="title" value="Re: <?php echo DiscussStringHelper::escape($parent->title); ?>" />
					<input type="hidden" name="post_id" value="<?php echo $post->id; ?>" />
					<input type="hidden" name="parent_id" value="<?php echo $parent->id; ?>" />
					<input type="hidden" name="parent_catid" value="<?php echo $parent->category_id; ?>" />
					<input type="hidden" name="user_type" value="<?php echo $system->my->id == 0 ? 'guest' : ''; ?>" />
				</div>
			</form>
		</div>
	</div>
</div>
