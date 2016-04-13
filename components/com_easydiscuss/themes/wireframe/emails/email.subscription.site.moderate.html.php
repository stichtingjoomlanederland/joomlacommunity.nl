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
<b><?php echo $postAuthor; ?></b> <?php echo JText::_( 'COM_EASYDISCUSS_EMAILTEMPLATE_CREATED_NEW_DISCUSSION' );?> <b><?php echo $postTitle; ?></b> <?php echo JText::_( 'COM_EASYDISCUSS_EMAILTEMPLATE_IT_IS_PENDING_MODERATION');?>.
<br />
<hr style="clear:both;margin:10px 0 15px;padding:0;border:0;border-top:1px solid #ddd" />
<img src="<?php echo $postAuthorAvatar; ?>" width="80" alt="<?php echo $postAuthor; ?>" style="width:80px;height:80px;border-radius:3px;-moz-border-radius:3px;-webkit-border-radius:3px;float:left;margin:0 15px 0 0" />
<?php echo $postContent; ?>
<?php echo $moderation; ?>
