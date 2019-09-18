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
<div class="ed-post-replies" data-ed-post-replies>
	<?php if( $replies ){ ?>
		<?php foreach( $replies as $reply ){ ?>
			<?php echo ED::themes()->output('site/post/default.reply.item', array('post' => $reply, 'poll' => $reply->getPoll(), 'composer' => $composer)); ?>
		<?php } ?>
	<?php } ?>
</div>
