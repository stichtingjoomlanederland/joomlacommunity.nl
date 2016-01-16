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
EasyDiscuss
.require()
.script( 'conversation' )
.done(function($){
	$( '.composeForm' ).implement( EasyDiscuss.Controller.Conversation.Form ,
		{
			"{textEditor}" : ".composeMessage"
		});
})
</script>
<form name="composeMessage" action="<?php echo DiscussRouter::_( 'index.php?option=com_easydiscuss&controller=conversation&task=save' );?>" method="post">
<div class="discuss-messaging composeForm">
	<div class="fd-cf">
		<div class="pull-left">
			<h2 class="discuss-component-title"><?php echo JText::_( 'Start a conversation' ); ?></h2>
		</div>
	</div>
	<hr />

	<div class="fd-cf">
		Writing to <a href="#"><?php echo $recipient->getName();?></a>.
	</div>

	<div class="fd-cf">
		<?php
		// @TODO: Add location form.
		?>
	</div>

	<div class="fd-cf mt-20">

		<div>
			<textarea name="message" class="composeMessage full-width"></textarea>
		</div>
	</div>
	<div class="form-actions">
		<div class="pull-right">
			<input type="submit" class="btn btn-large btn-primary" value="<?php echo JText::_('Send' , true ); ?>" />
		</div>
	</div>
</div>
<input type="hidden" name="recipient" value="<?php echo $recipient->id; ?>" />
<?php echo JHTML::_( 'form.token' );?>
</form>
