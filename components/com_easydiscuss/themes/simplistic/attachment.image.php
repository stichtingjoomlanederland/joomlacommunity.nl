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

$app 	= JFactory::getApplication();
?>

<?php if( $isEmail ){ ?>

	<div class="caption">
		<a style="color:#477fda" class="attachment-image-link" <?php echo $system->config->get( 'attachment_image_title' ) ? 'title="' . $this->escape( $attachment->title ) . '"' : '' ;?> href="<?php echo DiscussRouter::getRoutedURL( 'index.php?option=com_easydiscuss&controller=attachment&task=displayFile&tmpl=component&id=' . $attachment->id, false, true ); ?>"><?php echo $attachment->title;?></a>
	</div>

<?php }else{ ?>

	<a class="attachment-image-link" <?php echo $system->config->get( 'attachment_image_title' ) ? 'title="' . $this->escape( $attachment->title ) . '"' : '' ;?> href="<?php echo DiscussRouter::getRoutedURL( 'index.php?option=com_easydiscuss&controller=attachment&task=displayFile&tmpl=component&id=' . $attachment->id, false, true ); ?>"><img src="<?php echo JRoute::_( 'index.php?option=com_easydiscuss&controller=attachment&task=displayFile&tmpl=component&size=thumb&id=' . $attachment->id ); ?>" title="<?php echo $this->escape( $attachment->title );?>" /></a>
	<div class="caption" style="text-align:center;">
		<a class="attachment-image-link" title="<?php echo $this->escape( $attachment->title );?>" href="<?php echo DiscussRouter::getRoutedURL( 'index.php?option=com_easydiscuss&controller=attachment&task=displayFile&tmpl=component&id=' . $attachment->id, false, true ); ?>"><?php echo $attachment->title;?></a>
	</div>

<?php } ?>
