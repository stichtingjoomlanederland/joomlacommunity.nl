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
	<div class="media">
		<?php if( $system->config->get( 'layout_avatar' ) ) { ?>
		<a class="discuss-avatar float-l" href="<?php echo $reply->creator->getLink();?>">
			<img src="<?php echo $reply->creator->getAvatar(); ?>" alt="<?php echo $this->escape( $reply->creator->getName() );?>" class="avatar" width="70" height="70" />
		</a>
		<?php } ?>
		<div class="media-body">
			<header>
				<b><a href="<?php echo $reply->creator->getLink();?>"><?php echo $reply->creator->getName(); ?></a></b>
			</header>
			<article>
				<?php echo $reply->message; ?>
			</article>
			<footer class="muted">
				<?php echo $reply->lapsed;?>
				<time datetime="<?php echo $reply->created;?>"></time>
			</footer>
		</div>
	</div>
</li>