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

$mainframe  = JFactory::getApplication();
$isBackend  = ( $mainframe->isAdmin() ) ? true : false;

?>

<?php if( $isEmail ){ ?>

	<div class="caption">
		<a style="color:#477fda" title="<?php echo $this->escape( $attachment->title );?>" href="<?php echo DiscussRouter::getRoutedURL('index.php?option=com_easydiscuss&controller=attachment&task=getFile&tmpl=component&id=' . $attachment->id, false, true); ?>"><?php echo $attachment->title;?></a>
	</div>

<?php }else{ ?>

	<a title="<?php echo $this->escape( $attachment->title );?>" href="<?php echo DiscussRouter::getRoutedURL('index.php?option=com_easydiscuss&controller=attachment&task=getFile&tmpl=component&id=' . $attachment->id, false, true); ?>" style="font-size:24px;height:50px;line-height:50px;text-align:center">
		<i class="icon-download-alt"></i>
	</a>
	<div class="caption" style="text-align:center;">
		<a title="<?php echo $this->escape( $attachment->title );?>" href="<?php echo DiscussRouter::getRoutedURL('index.php?option=com_easydiscuss&controller=attachment&task=getFile&tmpl=component&id=' . $attachment->id, false, true); ?>"><?php echo $attachment->title;?></a>
	</div>

<?php } ?>
