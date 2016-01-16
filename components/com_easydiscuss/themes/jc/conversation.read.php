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

$config = DiscussHelper::getConfig();
$count = JRequest::getInt( 'count' );
$count = $count + $config->get('main_messages_limit', 5);
?>
<script type="text/javascript">
EasyDiscuss
.require()
.script( 'conversation' )
.done(function($){
	$( '.discussMessage' ).implement( EasyDiscuss.Controller.Conversation.Read );
})
</script>
<div class="discuss-messaging discussMessage" data-id="<?php echo $conversation->id;?>">
	<header class="clearfix">
		<div class="float-r" data-toggle="buttons-radio">
			<a href="<?php echo DiscussRouter::_( 'index.php?option=com_easydiscuss&controller=conversation&task=unread&' . DiscussHelper::getToken() . '=1&id=' . $conversation->id );?>" class="butt butt-default">
				<i class="i i-eye muted"></i> 
				&nbsp;
				<?php echo JText::_( 'COM_EASYDISCUSS_BUTTON_MARK_UNREAD' ); ?>
			</a>
			<a href="<?php echo DiscussRouter::_( 'index.php?option=com_easydiscuss&controller=conversation&task=archive&' . DiscussHelper::getToken() . '=1&id=' . $conversation->id );?>" class="butt butt-default">
				<?php echo JText::_( 'COM_EASYDISCUSS_BUTTON_ARCHIVE' ); ?>
			</a>
		</div>
		<a href="<?php echo DiscussRouter::_( 'index.php?option=com_easydiscuss&view=conversation' );?>" class="butt butt-default">
			<i class="i i-angle-left muted"></i>
			&nbsp;
			<?php echo JText::_( 'COM_EASYDISCUSS_BACK_TO_INBOX' );?>
		</a>
	</header>
	<hr>

	

	<?php
		$day    = '';
		if( $replies ){
	?>

	<div>
		<?php if( JRequest::getVar( 'show' ) != 'all' ){ ?>
			<a href="<?php echo DiscussRouter::_( 'index.php?option=com_easydiscuss&view=conversation&layout=read&id=' . $conversation->id . '&show=all' );?>"><?php echo JText::_( 'COM_EASYDISCUSS_VIEW_ALL_PREVIOUS_MESSAGES' ) ?></a>
		<?php } ?>
		<?php if( JRequest::getVar( 'show' ) != 'all' ){ ?>
			&nbsp;<b>&middot;</b>&nbsp;
			<a href="<?php echo DiscussRouter::_( 'index.php?option=com_easydiscuss&view=conversation&layout=read&id=' . $conversation->id . '&show=previous&count=' . $count );?>"><?php echo JText::_( 'COM_EASYDISCUSS_VIEW_PREVIOUS_MESSAGES' ) ?></a>
		<?php } ?>
	</div>

	<ul class="conversation-stream reset-ul replyList">
	<?php
		foreach( $replies as $reply )
		{
			if( $day == '' )
			{
			?>
			<li class="conversation-date"><b><?php echo DiscussHelper::getHelper( 'Date' )->getLapsedTime( $reply->created );?></b></li>
			<?php
				$day    = $reply->daydiff;
			}
			else if( $day != $reply->daydiff )
			{

				if( $reply->daydiff == 0 )
				{
				?>
					<li class="conversation-date today"><b><?php echo JText::_( 'COM_EASYDISCUSS_CONVERSATION_SEPARATOR_TODAY' );?></b></li>
				<?php
				}
				else
				{
				?>
					<li class="conversation-date"><b><?php echo DiscussHelper::getHelper( 'Date' )->getLapsedTime( $reply->created ); ?></b></li>
				<?php
				}
				$day    = $reply->daydiff;
			}
	?>
	<?php echo $this->loadTemplate( 'conversation.read.item.php' , array( 'reply' => $reply ) ); ?>
	<?php } //END foreach ?>
	</ul>
	<?php } else { ?>
	<div class="discuss-empty">
		<?php echo JText::_( 'COM_EASYDISCUSS_NO_MESSAGES_YET' );?>
	</div>
	<?php } ?>

	<div class="discuss-composer replyForm">
		<hr>
		<a id="reply"></a>
		<h3><?php echo JText::_( 'COM_EASYDISCUSS_WRITE_A_REPLY' );?></h3>
		<div class="conversationError"></div>
		<textarea name="message" class="replyMessage full-width"></textarea>
		<br>
		<input type="button" class="butt butt-primary replyButton" value="<?php echo JText::_('COM_EASYDISCUSS_REPLY_BUTTON' , true ); ?>" />
	</div>
</div>
