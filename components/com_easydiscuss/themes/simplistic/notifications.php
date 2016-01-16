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
<h2 class="discuss-component-title pull-left">
	<?php echo JText::_( 'COM_EASYDISCUSS_ALL_NOTIFICATIONS' );?>
</h2>
<a class="btn mt-15 pull-right" href="<?php echo DiscussRouter::_( 'index.php?option=com_easydiscuss&controller=notification&task=markreadall' );?>"><?php echo JText::_( 'COM_EASYDISCUSS_MARK_ALL_AS_READ' ); ?></a>
</div>
<hr>

<?php if( $system->config->get( 'layout_avatar' ) ) { ?>
	<?php foreach( $notifications as $day => $data ){ ?>
	<div class="notification-day">
		<div class="day-seperator discuss-post-title"><?php echo $day; ?></div>
		<ul class="notification-result unstyled">
			<?php foreach( $data as $item ){ ?>
				<li class="item type-<?php echo $item->type;?> is-<?php echo $item->state == DISCUSS_NOTIFICATION_READ ? 'read' : 'unread';?> notificationItem">
					<div class="media">
						<div class="media-object pull-left">
							<div class="discuss-avatar avatar-small">
								<img src="<?php echo $system->profile->getAvatar();?>" alt="<?php echo $this->escape( $system->profile->getName() );?>" />
							</div>
						</div>
						<div class="media-body">
							<i class="icon-"></i>
							<?php echo $item->title;?>
							<a href="javascript:void(0);" class="pull-">
								<small>
									- <a href="<?php echo DiscussRouter::_( $item->permalink );?>#<?php echo $item->type;?>-<?php echo $item->cid;?>"><?php echo $item->touched; ?></a>
									<?php if( $item->state != DISCUSS_NOTIFICATION_READ) { ?>
									<a href="<?php echo DiscussRouter::_( 'index.php?option=com_easydiscuss&controller=notification&task=markread&id=' . $item->id );?>"><?php echo JText::_( 'COM_EASYDISCUSS_MARK_AS_READ' );?></a>
									<?php } ?>
								</small>
							</a>
						</div>
					</div>
				</li>
			<?php } ?>
		</ul>
	</div>
	<?php } ?>
<?php } else { ?>
	<?php foreach( $notifications as $day => $data ){ ?>
	<!-- @php notification dom without avatar -->
	<div class="notification-day">
		<div class="day-seperator discuss-post-title"><?php echo $day; ?></div>
		<ul class="notification-result unstyled">
			<!-- defined type in <li>
			type-mention, type-reply, type-resolved, type-accepted, type-featured, type-comment, type-profile, type-badge, type-locked, type-unlocked, type-likes-discussion, type-likes-replies -->
			<!-- defined staes in <li>
			is-read is-unread -->
			<?php foreach( $data as $item ){ ?>
				<li class="item type-<?php echo $item->type;?> is-<?php echo $item->state == DISCUSS_NOTIFICATION_READ ? 'read' : 'unread';?> notificationItem">
					<i class="icon-"></i>
						<?php echo $item->title;?>
					<a href="javascript:void(0);" class="pull-">
						<small>
							- <a href="<?php echo DiscussRouter::_( $item->permalink );?>#<?php echo $item->type;?>-<?php echo $item->cid;?>"><?php echo $item->touched; ?></a>
							<?php if( $item->state != DISCUSS_NOTIFICATION_READ) { ?>
							<a href="<?php echo DiscussRouter::_( 'index.php?option=com_easydiscuss&controller=notification&task=markread&id=' . $item->id );?>"><?php echo JText::_( 'COM_EASYDISCUSS_MARK_AS_READ' );?></a>
							<?php } ?>
						</small>
					</a>
				</li>
			<?php } ?>
		</ul>
	</div>
	<?php } ?>
<?php } ?>
