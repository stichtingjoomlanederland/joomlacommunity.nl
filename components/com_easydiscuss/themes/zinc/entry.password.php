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

<h3 class="discuss-post-title"><?php echo JText::_( 'COM_EASYDISCUSS_PASSWORD_FORM_TITLE' );?></h3>
<p><?php echo JText::_( 'COM_EASYDISCUSS_PASSWORD_FORM_TIPS' ); ?></p>
<form action="<?php echo DiscussRouter::_( 'index.php?option=com_easydiscuss&controller=posts&task=setPassword' );?>" method="post">
	<div class="input-group" style="width:80%">
		<span class="input-group-addon"><i class="i i-lock"></i> </span>
		<input type="password" name="discusspassword" id="password-post" class="form-control" autocomplete="off"/>
		<span class="input-group-btn">
			<button type="submit" class="butt butt-primary"> <?php echo JText::_( 'COM_EASYDISCUSS_VIEW_POST_BUTTON' );?> </button>
		</span>
	</div><!-- /input-group -->
</form>

<?php echo (isset($googleAdsense)) ? $googleAdsense->beforereplies : ''; ?>
<?php echo (isset($googleAdsense)) ? $googleAdsense->footer : ''; ?>
