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
<div class="fd-cf">
	<h2 class="discuss-component-title pull-left"><?php echo $heading;?></h2>

	<div class=" mr-5 mt-20 hide-phone pull-right" data-toggle="buttons-radio">
		<a href="<?php echo DiscussRouter::_( 'index.php?option=com_easydiscuss&view=conversation' );?>" class="btn btn-small<?php echo $active == 'inbox' ? ' active' : '';?>" >
			<i class="icon-inbox"></i>
			<?php echo JText::_( 'COM_EASYDISCUSS_CONVERSATIONS_CONVERSATIONS' ); ?> (<?php echo $countInbox;?>)
		</a>
		<a href="<?php echo DiscussRouter::_( 'index.php?option=com_easydiscuss&view=conversation&layout=archives' );?>" class="btn btn-small<?php echo $active == 'archives' ? ' active' : '';?>">
			<i class="icon-trash"></i>
			<?php echo JText::_( 'COM_EASYDISCUSS_CONVERSATIONS_ARCHIVES' ); ?> (<?php echo $countArchives;?>)
		</a>
	</div>
</div>
<hr />

<div class="discuss-messages">
	<ul class="unstyled discuss-list discuss-messages-list mt-0">
		<?php if( $conversations ){ ?>
			<?php foreach( $conversations as $conversation ){ ?>
			<li class="conversationItem<?php echo $conversation->isNew( $system->my->id ) ? ' is-unread' : ' is-read';?>">

				<div class="discuss-item discuss-item-message">
					<div class="discuss-item-right">
						<div class="discuss-item discuss-item-media">
							<div class="discuss-item-left">
								<div class="media">
									<div class="media-object">
										<div class="discuss-avatar avatar-medium">
											<a href="<?php echo DiscussRouter::getMessageRoute( $conversation->id );?>">
												<?php if( $system->config->get( 'layout_avatar' ) ) { ?>
												<img src="<?php echo $conversation->creator->getAvatar();?>" alt="<?php echo $this->escape( $conversation->creator->getName() );?>">
												<?php } else { ?>
												<?php echo $this->escape( $conversation->creator->getName() );?>
												<?php } ?>
											</a>
										</div>
									</div>
									<div class="media-body">
										<div class="discuss-user-name">
											<i class="icon-ok-sign icon-unread-message"></i>
											<a href="<?php echo DiscussRouter::getMessageRoute( $conversation->id );?>"><?php echo $conversation->creator->getName();?></a>
										</div>

										<div class="discuss-message-content">
											<?php echo $conversation->intro;?>
										</div>

										<div class="discuss-date">
											<time datetime="<?php echo $conversation->created;?>"><small><?php echo $conversation->lapsed; ?></small></time>
										</div>
									</div>
								</div>
							</div>

							<div class="discuss-item-right">
								<div class="discuss-action pull-right">

									<div class="pull-right">
										<?php if( $active == 'archives' ){ ?>
										<a href="<?php echo DiscussRouter::_( 'index.php?option=com_easydiscuss&controller=conversation&task=unarchive&' . DiscussHelper::getToken() . '=1&id=' . $conversation->id );?>" class="btn btn-small">
											<i class="icon-eye-open"></i> <?php echo JText::_( 'COM_EASYDISCUSS_UNARCHIVE_CONVERSATION' );?>
										</a>
										<?php } else { ?>
										<a href="<?php echo DiscussRouter::_( 'index.php?option=com_easydiscuss&controller=conversation&task=archive&' . DiscussHelper::getToken() . '=1&id=' . $conversation->id );?>" class="btn btn-small">
											<i class="icon-eye-close"></i> <?php echo JText::_( 'COM_EASYDISCUSS_ARCHIVE_CONVERSATION' );?>
										</a>
										<?php } ?>
										<a href="javascript:void(0);" onclick="disjax.load( 'conversation' , 'confirmDelete' , '<?php echo $conversation->id;?>' )" class="btn btn-small btn-danger">
											<i class="icon-trash"></i> <?php echo JText::_( 'COM_EASYDISCUSS_DELETE_BUTTON' );?>
										</a>

									</div>
								</div>
							</div>

						</div>
					</div>
				</div>
			</li>
			<?php } ?>
		<?php } else { ?>
		<li>
			<div class="empty"><?php echo JText::_( 'COM_EASYDISCUSS_CONVERSATIONS_NO_CONVERSATION_HERE' );?></div>
		</li>
		<?php } ?>
	</ul>


	<?php echo $pagination->getPagesLinks();?>

</div>
