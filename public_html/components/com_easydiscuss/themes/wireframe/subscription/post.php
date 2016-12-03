<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<?php if ($sid && !$this->my->guest) { ?>
	<a id="unsubscribe-<?php echo $sid; ?>" class="cancel-email has-tip atr" href="javascript:void(0);" onclick="disjax.loadingDialog();disjax.load('index', 'ajaxUnSubscribe', '<?php echo $type; ?>', '<?php echo $isSubscribed; ?>', '<?php echo $cid; ?>');">
		<i class="icon-ed-email-minus" ></i>
		<div class="tooltip tooltip-ed top in">
			<div class="tooltip-arrow"></div>
			<div class="tooltip-inner"><?php echo JText::_( 'COM_EASYDISCUSS_UNSUBSCRIBE_VIAEMAIL_'.strtoupper($type) ); ?></div>
		</div>
		<?php if( $type == 'site' ) { ?>
		<?php echo JText::_('COM_EASYDISCUSS_UNSUBSCRIBE'); ?>
		<?php } ?>

	</a>
<?php } else { ?>
	<a class="via-email has-tip atr <?php echo ($class) ? ' '.$class : ''; ?>" 
		href="javascript:void(0);"
		data-ed-subscribe
		data-type="post"
		data-cid="<?php echo $cid;?>"
		data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_SUBSCRIBE_VIAEMAIL_POST'); ?>" 
	>
		<i class="icon-ed-email" ></i>
		<div class="tooltip tooltip-ed top in">
			<div class="tooltip-arrow"></div>
			<div class="tooltip-inner"><?php echo JText::_('COM_EASYDISCUSS_SUBSCRIBE_VIA_EMAIL'); ?></div>
		</div>
	</a>
<?php } ?>