<?php
/**
* @package      EasyDiscuss
* @copyright    Copyright (C) 2010 - 2017 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<?php echo JText::_('COM_EASYDISCUSS_EMAILTEMPLATE_HELLO'); ?>,

<?php echo JText::_('COM_EASYDISCUSS_EMAILTEMPLATE_PLEASE_MODERATE_EDITED_REPLY'); ?>

<?php echo JText::_('COM_EASYDISCUSS_EMAILTEMPLATE_AUTHOR'); ?>:
<?php echo $replyAuthor; ?>


<?php echo JText::_('COM_EASYDISCUSS_EMAILTEMPLATE_MESSAGE'); ?>:
<?php echo $replyContent; ?>

<?php echo $moderation; ?>
