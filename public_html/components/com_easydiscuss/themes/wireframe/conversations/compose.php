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
	<div class="ed-messaging composeForm l-stack">

		<div class="t-d--flex sm:t-flex-direction--c t-align-items--c sm:t-align-items--fs">
			<div class="lg:t-pr--lg sm:t-pb--md">
				<label for="recipient">
					<?php echo JText::_('COM_EASYDISCUSS_WRITING_TO');?>
				</label>
			</div>
			<div class="t-flex-grow--1 sm:t-w--100">
				<select name="recipient" placeholder="<?php echo JText::_("COM_EASYDISCUSS_START_TYPE_YOUR_FRIENDS_NAME");?>" data-ed-conversation-recipient></select>

				<div class="ed-convo-selectize-dummy"></div>
			</div>
		</div>

		<div class="ed-convo-markitup">
			<div>
				<textarea name="message" class="form-control" data-ed-conversation-message></textarea>
			</div>
		</div>

		<div class="t-d--flex t-justify-content--fe">
			<div class="">
				<input type="submit" class="o-btn o-btn--primary " value="<?php echo JText::_('COM_EASYDISCUSS_BUTTON_SEND' , true); ?>" />
			</div>
		</div>
	</div>

	<?php echo $this->html('form.action', 'conversation', 'conversation', 'save'); ?>
</form>
