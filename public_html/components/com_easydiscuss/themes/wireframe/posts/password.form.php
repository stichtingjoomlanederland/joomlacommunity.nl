<?php
/**
* @package      EasyDiscuss
* @copyright    Copyright (C) Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<form action="<?php echo JRoute::_('index.php');?>" method="post">
	<div class="o-card o-card--bg">
		<div class="o-card__body">
			<div class="l-stack l-stack--sm t-text--center">
				<div class="o-title-01">
					<i class="fas fa-lock t-mr--sm"></i> <?php echo JText::_('COM_EASYDISCUSS_PASSWORD_FORM_TITLE'); ?>
				</div>
				<div class="o-description">
					<?php echo JText::_('COM_EASYDISCUSS_PASSWORD_FORM_TIPS'); ?>
				</div>

				<div class="l-center">
					<div class="o-input-group">
						<input type="password" name="discusspassword" class="o-form-control" autocomplete="new-password" placeholder="<?php echo JText::_('COM_EASYDISCUSS_INSERT_PASSWORD'); ?>" />
						<button type="submit" class="o-btn o-btn--default-o" type="button"><?php echo JText::_('COM_EASYDISCUSS_VIEW_POST_BUTTON'); ?></button>
					</div>
				</div>
			</div>

			<?php echo $this->html('form.action', 'posts','index', 'setPassword'); ?>
			<input type="hidden" name="id" value="<?php echo $post->id;?>" />
			<input type="hidden" name="type" value="<?php echo $type;?>" />
			<input type="hidden" name="return" value="<?php echo base64_encode('index.php?option=com_easydiscuss&view=post&id=' . $post->id); ?>" />
		</div>
	</div>
</form>