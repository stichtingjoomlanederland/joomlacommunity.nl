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
// Get current logged in user id
$my = JFactory::getUser();

// Get current question post owner user id
$questionOwner = $question->getOwner()->id;
?>
<?php if( ( $reply->access->canMarkAnswered() && !$question->islock && $my->id == $questionOwner) ||  DiscussHelper::isSiteAdmin() ) { ?>
	<?php if( !$question->isresolve && $reply->access->canMarkAnswered() ) { ?>
	<div class="discuss-accept-answer">
		<span id="accept-button-<?php echo $reply->id;?>">
			<a href="javascript:void(0);" onclick="discuss.reply.accept('<?php echo $reply->id; ?>');" class=" discuss-accept btn btn-small">
				<?php echo JText::_('COM_EASYDISCUSS_REPLY_ACCEPT');?></a>
		</span>
	</div>
	<?php } elseif( $reply->access->canUnmarkAnswered() ) { ?>
	<div class="discuss-accept-answer">
		<span id="reject-button-<?php echo $reply->id;?>">
			<a href="javascript:void(0);" onclick="discuss.reply.reject('<?php echo $reply->id; ?>');" class=" discuss-reject btn btn-small">
				<?php echo JText::_('COM_EASYDISCUSS_REPLY_REJECT');?></a>
		</span>
	</div>
	<?php } ?>
<?php } ?>
