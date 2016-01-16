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
<li class="dropdown_ drop-conversations<?php echo $views->conversation;?>">
<?php if( $config->get( 'main_conversations_notification') ){ ?>
	<a data-foundry-toggle="dropdown" class="dropdown-toggle_ messageLink" href="javascript:void(0);" rel="ed-tooltip" data-placement="top" data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_CONVERSATIONS' );?>">
		<i class="i i-envelope"></i>
		<span class="visible-phone"><?php echo JText::_( 'COM_EASYDISCUSS_CONVERSATIONS' );?></span>
		<b id="conversation-count" style="display: <?php // echo $totalMessages > 0 ? 'inline-block' : 'none';?>"><?php echo $totalMessages; ?></b>
	</a>
	<div class="nav-message nav-drop reset-ul messageDropDown" style="display: none;">
		<ul class="nav-feeds reset-ul messageResult">
			<li class="loading-indicator messageLoader"><i><?php echo JText::_( 'COM_EASYDISCUSS_LOADING' );?></i></li>
		</ul>
		<div class="nav-jumper">
			<a class="butt butt-default butt-block" href="<?php echo DiscussRouter::_( 'index.php?option=com_easydiscuss&view=conversation' );?>"><?php echo JText::_( 'COM_EASYDISCUSS_SEE_ALL' );?></a>
		</div>
	</div>
<?php }else{ ?>
	<a class="messageLink" href="<?php echo DiscussRouter::_( 'index.php?option=com_easydiscuss&view=conversation' ); ?>">
		<i class="i i-envelope"></i>
		<span class="label label-notification" id="conversation-count" style="display: <?php echo $totalMessages > 0 ? 'inline-block' : 'none';?>"><?php echo $totalMessages; ?></span>
	</a>
<?php } ?>
</li>
