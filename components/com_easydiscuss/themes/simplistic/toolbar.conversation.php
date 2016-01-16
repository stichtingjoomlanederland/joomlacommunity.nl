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
<li class="dropdown_<?php echo $views->conversation;?>">
<?php if( $config->get( 'main_conversations_notification') ){ ?>

	<a data-foundry-toggle="dropdown" class="dropdown-toggle_ messageLink" href="javascript:void(0);" rel="ed-tooltip" data-placement="top" data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_CONVERSATIONS' );?>">
		<i class="icon-envelope-alt"></i>
		<span class="visible-phone"><?php echo JText::_( 'COM_EASYDISCUSS_CONVERSATIONS' );?></span>
		<span class="label label-notification" id="conversation-count" style="display: <?php echo $totalMessages > 0 ? 'inline-block' : 'none';?>"><?php echo $totalMessages; ?></span>
	</a>

	<ul class="dropdown-menu dropdown-menu-large messageDropDown" style="display: none;">
		<li>
			<div class="discuss-notice-menu">
				<ul class="unstyled messaging-result messageResult fs-11">
					<li class="loading-indicator messageLoader"><i><?php echo JText::_( 'COM_EASYDISCUSS_LOADING' );?></i></li>
				</ul>

				<div class="modal-footer pt-0 pb-5">
					<a class="btn btn-link small" href="<?php echo DiscussRouter::_( 'index.php?option=com_easydiscuss&view=conversation' );?>"><?php echo JText::_( 'COM_EASYDISCUSS_SEE_ALL' );?></a>
				</div>
			</div>
		</li>
	</ul>

<?php }else{ ?>

	<a class="messageLink" href="<?php echo DiscussRouter::_( 'index.php?option=com_easydiscuss&view=conversation' ); ?>">
		<i class="icon-envelope-alt"></i>
		<span class="label label-notification" id="conversation-count" style="display: <?php echo $totalMessages > 0 ? 'inline-block' : 'none';?>"><?php echo $totalMessages; ?></span>
	</a>

<?php } ?>
</li>
