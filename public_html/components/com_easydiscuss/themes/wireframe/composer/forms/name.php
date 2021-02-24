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
<div class="ed-editor-widget">
	<?php if ($type == 'question') { ?>
	<div class="ed-editor-widget__title">
		<?php echo JText::_('COM_EASYDISCUSS_YOUR_DETAILS'); ?>
	</div>

	<div class="ed-editor-widget__note">
		<p><?php echo JText::_('COM_EASYDISCUSS_YOUR_DETAILS_NOTE'); ?></p>
	</div>
	<?php } ?>

	<div class="ed-user-form">
		<div class="lg:o-grid o-grid--gutters t-mb--no">
			<div class="o-grid__cell">
				<div class="o-form-group lg:t-mb--no">
					<input type="text" name="poster_name" class="o-form-control" placeholder="<?php echo JText::_('COM_EASYDISCUSS_GUEST_NAME'); ?>" value="<?php echo $this->html('string.escape', $post->poster_name);?>" />
				</div>
			</div>

			<div class="o-grid__cell">
				<div class="o-form-group t-mb--no">
					<input type="text" name="poster_email" class="o-form-control" placeholder="<?php echo JText::_('COM_EASYDISCUSS_GUEST_EMAIL'); ?>" value="<?php echo $this->html('string.escape', $post->poster_email);?>" />
				</div>
			</div>
		</div>
	</div>
</div>


