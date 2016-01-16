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
	<h2>
		<?php echo JText::_( 'COM_EASYDISCUSS_ALL_NOTIFICATIONS' );?>
	</h2>
	<a class="butt butt-default" href="<?php echo DiscussRouter::_( 'index.php?option=com_easydiscuss&controller=notification&task=markreadall' );?>"><?php echo JText::_( 'COM_EASYDISCUSS_MARK_ALL_AS_READ' ); ?></a>
</header>

<article id="dc_notifications">
	<ul class="list-notifications reset-ul">
	<?php foreach( $notifications as $day => $data ){ ?>
		<li>
			<p class="notification-day discuss-post-title">
				<b><?php echo $day; ?></b>
			</p>	
			<ul class="notification-stream reset-ul">
			<?php foreach( $data as $item ){ ?>
				<li class="media type-<?php echo $item->type;?> is-<?php echo $item->state == DISCUSS_NOTIFICATION_READ ? 'read' : 'unread';?> notificationItem" style="overflow: hidden">
					<?php if( $system->config->get( 'layout_avatar' ) ) { ?>
					<div class="discuss-avatar float-l">
						<img src="<?php echo $system->profile->getAvatar();?>" alt="<?php echo $this->escape( $system->profile->getName() );?>" class="avatar" width="50" height="50" />
					</div>
					<?php } ?>
					<div class="media-body">
						<p><?php echo $item->title;?></p>
						<a class="muted" href="<?php echo DiscussRouter::_( $item->permalink );?>#<?php echo $item->type;?>-<?php echo $item->cid;?>"><?php echo $item->touched; ?></a>
						<?php if( $item->state != DISCUSS_NOTIFICATION_READ) { ?>
						&nbsp;<b>&middot;</b>&nbsp;
						<a class="muted" href="<?php echo DiscussRouter::_( 'index.php?option=com_easydiscuss&controller=notification&task=markread&id=' . $item->id );?>"><?php echo JText::_( 'COM_EASYDISCUSS_MARK_AS_READ' );?></a>
						<?php } ?>
					</div>
				</li>
			<?php } ?>
			</ul>
		</li>
	<?php } ?>
	</ul>
</article>