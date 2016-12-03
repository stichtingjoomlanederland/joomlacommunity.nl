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
<form id="deletePostForm" class="si_form" action="<?php echo JRoute::_( 'index.php' );?>" method="post">
<p>
<?php if( $type == 'reply' ){ ?>
	<?php echo JText::_('COM_EASYDISCUSS_REPLY_DELETE_DESC'); ?>
<?php } else { ?>
	<?php echo JText::_('COM_EASYDISCUSS_ENTRY_DELETE_DESC'); ?>
<?php } ?>
</p>
<input type="hidden" name="id" value="<?php echo $id;?>" />
<input type="hidden" name="type" value="<?php echo $type; ?>" />
<input type="hidden" name="url" value="<?php echo $url; ?>" />
<input type="hidden" name="option" value="com_easydiscuss" />
<input type="hidden" name="controller" value="posts" />
<input type="hidden" name="task" value="delete" />
<?php echo JHTML::_( 'form.token' );?>
</form>
