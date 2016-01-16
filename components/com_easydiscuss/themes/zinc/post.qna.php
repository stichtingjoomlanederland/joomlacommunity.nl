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
<?php if( ( $reply->access->canMarkAnswered() && !$question->islock ) || DiscussHelper::isSiteAdmin() ) { ?>
	<?php if( !$question->isresolve && $reply->access->canMarkAnswered() ) { ?>
	<span id="accept-button-<?php echo $reply->id;?>" class="discuss-accept-answer">
		<a href="javascript:void(0);" onclick="discuss.reply.accept('<?php echo $reply->id; ?>');" class="butt butt-default butt-s discuss-accept">
			<?php echo JText::_('COM_EASYDISCUSS_REPLY_ACCEPT');?>
		</a>
	</span>
	<?php } elseif( $reply->access->canUnmarkAnswered() ) { ?>
	<span id="reject-button-<?php echo $reply->id;?>" class="discuss-accept-answer">
		<a href="javascript:void(0);" onclick="discuss.reply.reject('<?php echo $reply->id; ?>');" class="butt butt-default butt-s discuss-reject">
			<?php echo JText::_('COM_EASYDISCUSS_REPLY_REJECT');?>
		</a>
	</span>
	<?php } ?>
<?php } ?>
