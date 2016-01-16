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
	<div class="fd-cf">

		<a href="<?php echo $conversation->creator->getLink();?>">
			<h2 class="discuss-component-title pull-left">
				<div class="discuss-avatar avatar-medium">
					<?php if( $system->config->get( 'layout_avatar' ) ) { ?>
					<img src="<?php echo $conversation->creator->getAvatar();?>" />
					<?php } ?>
					<?php echo $conversation->creator->getName();?>
				</div>
			</h2>
		</a>

		<div class=" mr-5 mt-20 hide-phone pull-right" data-toggle="buttons-radio">
			<a href="<?php echo DiscussRouter::_( 'index.php?option=com_easydiscuss&controller=conversation&task=unread&' . DiscussHelper::getToken() . '=1&id=' . $conversation->id );?>" class="btn btn-mini">
				<i class="icon-eye-open"></i> <?php echo JText::_( 'COM_EASYDISCUSS_BUTTON_MARK_UNREAD' ); ?>
			</a>
			<a href="<?php echo DiscussRouter::_( 'index.php?option=com_easydiscuss&controller=conversation&task=archive&' . DiscussHelper::getToken() . '=1&id=' . $conversation->id );?>" class="btn btn-mini">
				<i class="icon-remove"></i> <?php echo JText::_( 'COM_EASYDISCUSS_BUTTON_ARCHIVE' ); ?>
			</a>
		</div>
	</div>
	<a href="<?php echo DiscussRouter::_( 'index.php?option=com_easydiscuss&view=conversation' );?>">&laquo; <?php echo JText::_( 'COM_EASYDISCUSS_BACK_TO_INBOX' );?></a>

	<div class="fd-cf">
		<?php
			$day    = '';
			if( $replies ){
		?>

		<div style="text-align: center;">
			<?php if( JRequest::getVar( 'show' ) != 'all' ){ ?>
				<a href="<?php echo DiscussRouter::_( 'index.php?option=com_easydiscuss&view=conversation&layout=read&id=' . $conversation->id . '&show=all' );?>"><?php echo JText::_( 'COM_EASYDISCUSS_VIEW_ALL_PREVIOUS_MESSAGES' ) ?></a>
			<?php } ?>
		</div>

		<div style="text-align: center;">
			<?php if( JRequest::getVar( 'show' ) != 'all' ){ ?>
					<a href="<?php echo DiscussRouter::_( 'index.php?option=com_easydiscuss&view=conversation&layout=read&id=' . $conversation->id . '&show=previous&count=' . $count );?>"><?php echo JText::_( 'COM_EASYDISCUSS_VIEW_PREVIOUS_MESSAGES' ) ?></a>
			<?php } ?>
		</div>



			<ul class="unstyled discuss-list discuss-conversation-list mt-0 replyList">
			<?php
				foreach( $replies as $reply )
				{
					if( $day == '' )
					{
					?>
					<li class="divider small"><?php echo DiscussHelper::getHelper( 'Date' )->getLapsedTime( $reply->created );?></li>
					<?php
						$day    = $reply->daydiff;
					}
					else if( $day != $reply->daydiff )
					{

						if( $reply->daydiff == 0 )
						{
						?>
							<li class="divider small today"><?php echo JText::_( 'COM_EASYDISCUSS_CONVERSATION_SEPARATOR_TODAY' );?></li>
						<?php
						}
						else
						{
						?>
							<li class="divider small"><?php echo DiscussHelper::getHelper( 'Date' )->getLapsedTime( $reply->created ); ?></li>
						<?php
						}
					$day    = $reply->daydiff;
				}
			?>

				<?php echo $this->loadTemplate( 'conversation.read.item.php' , array( 'reply' => $reply ) ); ?>
			<?php } ?>
			</ul>
		<?php } else { ?>
			<div class="empty">
				<?php echo JText::_( 'COM_EASYDISCUSS_NO_MESSAGES_YET' );?>
			</div>
		<?php } ?>
	</div>

	<div class="fd-cf mt-20 replyForm">
		<a id="reply"></a>
		<legend><?php echo JText::_( 'COM_EASYDISCUSS_WRITE_A_REPLY' );?></legend>
		<div class="conversationError"></div>
		<textarea name="message" class="replyMessage full-width"></textarea>
		<div class="form-actions">
			<div class="pull-right">
				<input type="button" class="btn btn-medium btn-primary replyButton" value="<?php echo JText::_('COM_EASYDISCUSS_REPLY_BUTTON' , true ); ?>" />
			</div>
		</div>
	</div>
</div>
