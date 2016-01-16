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
<?php if( $notifications ){ ?>
	<?php foreach($notifications as $notification){ ?>
	<li class="item type-<?php echo $notification->type;?> new notificationItem">
		<?php if( !$notification->component || $notification->component  == 'com_easydiscuss' || empty( $notification->component ) ){ ?>
			<a href="<?php echo DiscussRouter::_( $notification->permalink , false );?>#<?php echo $notification->type;?>-<?php echo $notification->cid;?>" class="media">
		<?php } else { ?>
			<a href="<?php echo $notification->permalink;?>" class="media">
		<?php } ?>
			<?php if( $notification->favicon){ ?>
			<i style="background: url('<?php echo $notification->favicon;?>') no-repeat;" class="pull-left"></i>
			<?php } else { ?>
			<i class="pull-left"></i>
			<?php } ?>

			<div class="media-body">
				<div><?php echo $notification->title;?></div>
				<small class="muted"><?php echo $notification->touched; ?></small>
			</div>
		</a>
	</li>

	<?php } ?>
<?php } else { ?>
	<li class="item feed-none notificationItem">
		<div class="pa-10 tac"><?php echo JText::_( 'COM_EASYDISCUSS_NO_NEW_NOTIFICATIONS_YET' ); ?></div>
	</li>
<?php } ?>
