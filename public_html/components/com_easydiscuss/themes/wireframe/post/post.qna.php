<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<?php if (($access->canMarkAnswered() && !$post->islock && $this->my->id == $post->getOwner()->id) || ED::isSiteAdmin()) { ?>
	<?php if (!$post->isresolve && $access->canMarkAnswered()) { ?>
	<div class="discuss-accept-answer">
		<span id="accept-button-<?php echo $post->id;?>">
			<a href="javascript:void(0);" data-ed-accept-answer class=" discuss-accept btn btn-small">
				<?php echo JText::_('COM_EASYDISCUSS_REPLY_ACCEPT');?></a>
		</span>
	</div>
	<?php } elseif ($access->canUnmarkAnswered()) { ?>
	<div class="discuss-accept-answer">
		<span id="reject-button-<?php echo $post->id;?>">
			<a href="javascript:void(0);" data-ed-reject-answer class=" discuss-reject btn btn-small">
				<?php echo JText::_('COM_EASYDISCUSS_REPLY_REJECT');?></a>
		</span>
	</div>
	<?php } ?>
<?php } ?>
