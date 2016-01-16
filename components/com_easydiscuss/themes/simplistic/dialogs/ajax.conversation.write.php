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
<script type="text/javascript">
EasyDiscuss.require()
	.library('markitup')
	.script('bbcode')
	.done(function($){

		$( '#conversationMessage' ).markItUp(
			$.getEasyDiscussBBCodeSettings
		);

	});
</script>

<form id="conversationForm">

<div style="margin-bottom: 20px;">
	<?php echo JText::_( 'COM_EASYDISCUSS_SENDING_MESSAGE_TO' ); ?> <a href="<?php echo $recipient->getLink();?>"><?php echo $recipient->getName();?></a>.
</div>

<div>
	<div id="conversationEmptyMessage" class="alert alert-error" style="display: none;"><?php echo JText::_( 'COM_EASYDISCUSS_PLEASE_ENTER_SOME_MESSAGE' );?></div>

	<textarea id="conversationMessage" class="full-width markItUpEditor"></textarea>
</div>
<input type="hidden" id="recipientId" value="<?php echo $recipient->id;?>" />
</form>
