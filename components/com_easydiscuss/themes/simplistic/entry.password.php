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
<?php echo (isset($googleAdsense)) ? $googleAdsense->header : ''; ?>

<div class="discuss-protected">
	<h3 class="discuss-post-title"><?php echo JText::_( 'COM_EASYDISCUSS_PASSWORD_FORM_TITLE' );?></h3>
	<p class="small"><?php echo JText::_( 'COM_EASYDISCUSS_PASSWORD_FORM_TIPS' ); ?></p>
	<form action="<?php echo DiscussRouter::_( 'index.php?option=com_easydiscuss&controller=posts&task=setPassword' );?>" method="post">
		<div class="discuss-password">
			<div class="input-prepend input-append">
				<span class="add-on"><i class="icon-lock"></i> </span>
				<input type="password" name="discusspassword" id="password-post" class="span3" autocomplete="off"/>
				<button type="submit" class="btn"> <?php echo JText::_( 'COM_EASYDISCUSS_VIEW_POST_BUTTON' );?> </button>

			</div>
			<input type="hidden" name="id" value="<?php echo $post->id;?>" />
			<input type="hidden" name="return" value="<?php echo base64_encode( 'index.php?option=com_easydiscuss&view=post&id=' . $post->id ); ?>" />
		</div>
	</form>
</div>

<?php echo (isset($googleAdsense)) ? $googleAdsense->beforereplies : ''; ?>
<?php echo (isset($googleAdsense)) ? $googleAdsense->footer : ''; ?>
