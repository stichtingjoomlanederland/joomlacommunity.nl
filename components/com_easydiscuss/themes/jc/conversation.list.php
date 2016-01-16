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
<header>
	<h2><?php echo $heading;?></h2>

	<div data-toggle="buttons-radio">
		<a href="<?php echo DiscussRouter::_( 'index.php?option=com_easydiscuss&view=conversation' );?>" class="butt butt-default<?php echo $active == 'inbox' ? ' active' : '';?>" >
			<i class="i i-inbox muted"></i>
			&nbsp;
			<?php echo JText::_( 'COM_EASYDISCUSS_CONVERSATIONS_CONVERSATIONS' ); ?>
			&nbsp;
			<span class="muted"><?php echo $countInbox;?></span>
		</a>
		<a href="<?php echo DiscussRouter::_( 'index.php?option=com_easydiscuss&view=conversation&layout=archives' );?>" class="butt butt-default<?php echo $active == 'archives' ? ' active' : '';?>">
			<i class="i i-trash-o muted"></i>
			&nbsp;
			<?php echo JText::_( 'COM_EASYDISCUSS_CONVERSATIONS_ARCHIVES' ); ?>
			&nbsp;
			<span class="muted"><?php echo $countArchives;?></span>
		</a>
	</div>
</header>

<article id="dc_conversation">
	<ul class="list-conversations reset-ul">
		<?php if( $conversations ){ ?>
			<?php foreach( $conversations as $conversation ){ ?>
			<li class="conversationItem<?php echo $conversation->isNew( $system->my->id ) ? ' is-unread' : ' is-read';?>">
				<div class="media">
					<?php if( $system->config->get( 'layout_avatar' ) ) { ?>
					<a class="discuss-avatar float-l" href="<?php echo DiscussRouter::getMessageRoute( $conversation->id );?>">
						<img class="avatar" src="<?php echo $conversation->creator->getAvatar();?>" alt="<?php echo $this->escape( $conversation->creator->getName() );?>" width="50" height="50">
					</a>
					<?php } ?>
					<div class="float-r">
						<?php if( $active == 'archives' ){ ?>
						<a href="<?php echo DiscussRouter::_( 'index.php?option=com_easydiscuss&controller=conversation&task=unarchive&' . DiscussHelper::getToken() . '=1&id=' . $conversation->id );?>" class="butt butt-default butt-s">
							<i class="i i-archive muted"></i> 
							&nbsp;
							<?php echo JText::_( 'COM_EASYDISCUSS_UNARCHIVE_CONVERSATION' );?>
						</a>
						<?php } else { ?>
						<a href="<?php echo DiscussRouter::_( 'index.php?option=com_easydiscuss&controller=conversation&task=archive&' . DiscussHelper::getToken() . '=1&id=' . $conversation->id );?>" class="butt butt-default butt-s">
							<i class="i i-archive muted"></i> 
							&nbsp;
							<?php echo JText::_( 'COM_EASYDISCUSS_ARCHIVE_CONVERSATION' );?>
						</a>
						<?php } ?>
						<a href="javascript:void(0);" onclick="disjax.load( 'conversation' , 'confirmDelete' , '<?php echo $conversation->id;?>' )" class="butt butt-default butt-s">
							<i class="i i-trash-o muted"></i>
							&nbsp;
							<?php echo JText::_( 'COM_EASYDISCUSS_DELETE_BUTTON' );?>
						</a>
					</div>
					<div class="media-body">
						<header>
							<a href="<?php echo DiscussRouter::getMessageRoute( $conversation->id );?>"><?php echo $conversation->creator->getName();?></a>
						</header>

						<article>
							<?php echo $conversation->intro;?>
						</article>

						<footer>
							<time datetime="<?php echo $conversation->created;?>" class="muted"><?php echo $conversation->lapsed; ?></time>
						</footer>
					</div>
				</div>
			</li>
			<?php } ?>
		<?php } else { ?>
		<li>
			<div class="discuss-empty"><?php echo JText::_( 'COM_EASYDISCUSS_CONVERSATIONS_NO_CONVERSATION_HERE' );?></div>
		</li>
		<?php } ?>
	</ul>
	<hr>
	<?php echo $pagination->getPagesLinks();?>
</article>
