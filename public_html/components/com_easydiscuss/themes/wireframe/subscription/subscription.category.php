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
<?php if ($this->config->get('main_sitesubscription') && $view == 'index'){ ?>
	<?php if( $isSubscribed && $this->my->id != 0 ) { ?>
		<a id="unsubscribe-<?php echo $sid; ?>" class="cancel-email has-tip atr <?php echo ($class) ? ' '.$class : ''; ?>" href="javascript:void(0);" onclick="disjax.loadingDialog();disjax.load('index', 'ajaxUnSubscribe', '<?php echo $type; ?>', '<?php echo $isSubscribed; ?>', '<?php echo $cid; ?>');">
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
		<a data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_SUBSCRIBE_VIAEMAIL_'.strtoupper($type) ); ?>" id="subscribe-<?php echo $type.'-'.$cid; ?>" class="via-email has-tip atr <?php echo ($class) ? ' '.$class : ''; ?>" href="javascript:void(0);" onclick="disjax.loadingDialog();disjax.load('index', 'ajaxSubscribe', '<?php echo $type; ?>', '<?php echo $cid; ?>');">
			<i class="icon-ed-email" ></i>
			<div class="tooltip tooltip-ed top in">
				<div class="tooltip-arrow"></div>
				<div class="tooltip-inner"><?php echo JText::_('COM_EASYDISCUSS_SUBSCRIBE_VIA_EMAIL'); ?></div>
			</div>
			<?php if( $type == 'site' ) { ?>
			<?php echo JText::_('COM_EASYDISCUSS_SUBSCRIBE_VIA_EMAIL'); ?>
			<?php } ?>
		</a>
	<?php } ?>
<?php } ?>

<?php if ($this->config->get('main_postsubscription') && $view == 'post'){ ?>
	<?php if( $isSubscribed && $this->my->id != 0 ) { ?>
		<a id="unsubscribe-<?php echo $sid; ?>" class="cancel-email has-tip atr <?php echo ($class) ? ' '.$class : ''; ?>" href="javascript:void(0);" onclick="disjax.loadingDialog();disjax.load('index', 'ajaxUnSubscribe', '<?php echo $type; ?>', '<?php echo $isSubscribed; ?>', '<?php echo $cid; ?>');">
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
		<a data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_SUBSCRIBE_VIAEMAIL_'.strtoupper($type) ); ?>" id="subscribe-<?php echo $type.'-'.$cid; ?>" class="via-email has-tip atr <?php echo ($class) ? ' '.$class : ''; ?>" href="javascript:void(0);" onclick="disjax.loadingDialog();disjax.load('index', 'ajaxSubscribe', '<?php echo $type; ?>', '<?php echo $cid; ?>');">
			<i class="icon-ed-email" ></i>
			<div class="tooltip tooltip-ed top in">
				<div class="tooltip-arrow"></div>
				<div class="tooltip-inner"><?php echo JText::_('COM_EASYDISCUSS_SUBSCRIBE_VIA_EMAIL'); ?></div>
			</div>
			<?php if( $type == 'site' ) { ?>
			<?php echo JText::_('COM_EASYDISCUSS_SUBSCRIBE_VIA_EMAIL'); ?>
			<?php } ?>
		</a>
	<?php } ?>
<?php } ?>

<?php if ($this->config->get('main_ed_categorysubscription') && $view == 'categories') { ?>
	<?php if( $isSubscribed && $this->my->id != 0 ) { ?>
		<a id="unsubscribe-<?php echo $sid; ?>" class="cancel-email has-tip atr <?php echo ($class) ? ' '.$class : ''; ?>" href="javascript:void(0);" onclick="disjax.loadingDialog();disjax.load('index', 'ajaxUnSubscribe', '<?php echo $type; ?>', '<?php echo $isSubscribed; ?>', '<?php echo $cid; ?>');">
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
		<a data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_SUBSCRIBE_VIAEMAIL_'.strtoupper($type) ); ?>" id="subscribe-<?php echo $type.'-'.$cid; ?>" class="via-email has-tip atr <?php echo ($class) ? ' '.$class : ''; ?>" href="javascript:void(0);" onclick="disjax.loadingDialog();disjax.load('index', 'ajaxSubscribe', '<?php echo $type; ?>', '<?php echo $cid; ?>');">
			<i class="icon-ed-email" ></i>
			<div class="tooltip tooltip-ed top in">
				<div class="tooltip-arrow"></div>
				<div class="tooltip-inner"><?php echo JText::_('COM_EASYDISCUSS_SUBSCRIBE_VIA_EMAIL'); ?></div>
			</div>
			<?php if( $type == 'site' ) { ?>
			<?php echo JText::_('COM_EASYDISCUSS_SUBSCRIBE_VIA_EMAIL'); ?>
			<?php } ?>
		</a>
	<?php } ?>
<?php } ?>

