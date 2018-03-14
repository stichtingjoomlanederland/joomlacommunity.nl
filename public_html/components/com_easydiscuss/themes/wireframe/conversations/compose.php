<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 - 2018 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<form name="composeMessage" action="<?php echo EDR::_('index.php?option=com_easydiscuss&controller=conversation&task=save');?>" method="post">
	<div class="ed-messaging composeForm">

		<div class="form-group">
			
			<label for="recipient">
				<?php echo JText::_('COM_EASYDISCUSS_WRITING_TO');?>
			</label>

			<select name="recipient" placeholder="<?php echo JText::_("COM_EASYDISCUSS_START_TYPE_YOUR_FRIENDS_NAME");?>" data-ed-conversation-recipient></select>

			<div class="ed-convo-selectize-dummy"></div>
		</div>

		<div class="ed-convo-markitup">
			<div>
				<textarea name="message" class="form-control" data-ed-conversation-message></textarea>
			</div>
		</div>

		<div class="o-row t-lg-pa--lg t-lg-mt--lg">
			<div class="pull-right">
				<input type="submit" class="btn btn-large btn-primary" value="<?php echo JText::_('COM_EASYDISCUSS_BUTTON_SEND' , true); ?>" />
			</div>
		</div>
	</div>

	<?php echo $this->html('form.hidden', 'conversation', 'conversation', 'save'); ?>
</form>
