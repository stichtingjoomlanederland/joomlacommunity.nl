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
<?php if( $conversations ){ ?>
	<?php foreach($conversations as $conversation){ ?>
	<li class="item new messageItem pa-10<?php echo $conversation->isNew( $system->my->id ) ? ' is-unread' :' is-read';?>">

		<div class="media notice-message">
			<a href="<?php echo DiscussRouter::getMessageRoute( $conversation->id );?>">
				<div class="media-object pull-left">
					<div class="discuss-avatar avatar-small">
						<?php if( $system->config->get( 'layout_avatar' ) ) { ?>
						<img alt="<?php echo $this->escape( $conversation->creator->getName() );?>" src="<?php echo $conversation->creator->getAvatar();?>" />
						<?php } else { ?>
						<?php echo $this->escape( $conversation->creator->getName() );?>
						<?php } ?>
					</div>
				</div>
				<div class="media-body">

					<i class="icon-ok-sign icon-unread-message pull-right"></i>

					<strong><?php echo $conversation->creator->getName();?></strong>
					<p><?php echo $conversation->intro;?></p>
					<small><?php echo $conversation->lapsed;?></small>

				</div>
			</a>
		</div>
	</li>
	<?php } ?>
<?php } else { ?>
	<li class="item dis-none messageItem">
		<div class="pa-10 tac"><?php echo JText::_( 'COM_EASYDISCUSS_CONVERSATIONS_NO_CONVERSATIONS_YET' ); ?></div>
	</li>
<?php } ?>
