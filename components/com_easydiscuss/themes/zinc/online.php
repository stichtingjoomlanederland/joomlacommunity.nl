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
<?php if( $system->config->get( 'layout_user_online') ){ ?>
	<?php if( $user->id ){ ?>
		<?php
		$user->load( $user->id );
		?>
		<?php if( $user->isOnline() ){ ?>
			<b class="discuss-line on" title="<?php echo JText::_( 'COM_EASYDISCUSS_ONLINE' );?>"></b>
		<?php }else{ ?>
			<b class="discuss-line off" title="<?php echo JText::_( 'COM_EASYDISCUSS_OFFLINE' );?>"></b>
		<?php } ?>
	<?php } ?>
<?php } ?>