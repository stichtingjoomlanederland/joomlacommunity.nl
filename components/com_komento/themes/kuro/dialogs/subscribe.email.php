<?php
/**
 * @package		Komento
 * @copyright	Copyright (C) 2012 Stack Ideas Private Limited. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 *
 * Komento is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */
defined( '_JEXEC' ) or die( 'Restricted access' ); ?>

<div class="subscribeEmailForm" id="kmt-subscribeemail">
	<?php if( $my->guest ) { ?>
		<h3><?php echo JText::_( 'COM_KOMENTO_FORM_SUBSCRIBE_ENTER_DETAILS' ); ?></h3>
		<label><?php echo JText::_( 'COM_KOMENTO_FORM_NAME' ); ?></label>
		<input type="text" name="subscribeName" class="subscribeName" id="subscribeName" />
		<label><?php echo JText::_( 'COM_KOMENTO_FORM_EMAIL' ); ?></label>
		<input type="text" name="subscribeEmail" class="subscribeEmail" id="subscribeEmail" />
		<div class="">
			<button type="button" class="subscribe"><?php echo JText::_( 'COM_KOMENTO_FORM_SUBSCRIBE' ); ?></button>
		</div>
		<span style="display: none;" class="subscribeError"><?php echo JText::_( 'COM_KOMENTO_FORM_SUBSCRIBE_FILL_IN_ALL_THE_FIELDS' ); ?></span>
	<?php } else { ?>
		<?php if( !is_null( $subscribed ) ) { ?>
			<?php if( $subscribed == 0 ) { ?>
				<h3 class="center"><?php echo JText::_( 'COM_KOMENTO_FORM_SUBSCRIBE_PENDING' ); ?></h3>
			<?php } else { ?>
				<h3 class="center"><?php echo JText::_( 'COM_KOMENTO_FORM_ALREADY_SUBSCRIBE' ); ?></h3>
			<?php } ?>
			<button type="button" class="unsubscribe"><?php echo JText::_( 'COM_KOMENTO_FORM_UNSUBSCRIBE' ); ?></button>
		<?php } else { ?>
			<h3><?php echo JText::_( 'COM_KOMENTO_FORM_SUBSCRIBE_WITH_CURRENT_DETAILS' ); ?></h3>
			<div class="kmt-subscribeemail-info">
				<span><?php echo JText::_( 'COM_KOMENTO_FORM_NAME' ); ?></span>: <strong><?php echo $my->getName(); ?></strong>
			</div>
			<div class="kmt-subscribeemail-info">
				<span><?php echo JText::_( 'COM_KOMENTO_FORM_EMAIL' ); ?></span>: <strong><?php echo $my->email; ?></strong>
			</div>
			<button type="button" class="subscribe"><?php echo JText::_( 'COM_KOMENTO_FORM_SUBSCRIBE' ); ?></button>
		<?php } ?>

		<input type="hidden" name="subscribeUser" class="subscribeUser" id="subscribeUser" value="<?php echo $my->id; ?>" />
	<?php } ?>
</div>
