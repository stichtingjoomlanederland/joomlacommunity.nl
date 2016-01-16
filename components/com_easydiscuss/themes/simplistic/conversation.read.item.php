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
<li class="<?php echo $reply->created_by == $system->my->id ? 'by-me' : 'by-user';?>">
	<div class="discuss-item discuss-item-message">
		<div class="discuss-item-right">
			<div class="discuss-item discuss-item-media">
				<div class="discuss-media-left">
					<div class="media">
						<div class="media-object">
							<a class="discuss-user-name" href="<?php echo $reply->creator->getLink();?>">
								<div class="discuss-avatar avatar-medium">
									<?php if( $system->config->get( 'layout_avatar' ) ) { ?>
									<img src="<?php echo $reply->creator->getAvatar(); ?>" alt="<?php echo $this->escape( $reply->creator->getName() );?>" />
									<?php } else { ?>
									<?php echo $this->escape( $reply->creator->getName() );?>
									<?php } ?>
								</div>
							</a>
						</div>
						<div class="media-body">
							<div class="discuss-message-box">
								<div class="discuss-user-name">
									<a href="<?php echo $reply->creator->getLink();?>"><?php echo $reply->creator->getName(); ?></a>
								</div>

								<div class="discuss-message-content">
									<?php echo $reply->message; ?>
								</div>
							</div>
							<div class="discuss-date">
								<?php echo $reply->lapsed;?>
								<time datetime="<?php echo $reply->created;?>"></time>
							</div>
						</div>

					</div>
				</div>
			</div>
		</div>
	</div>
</li>