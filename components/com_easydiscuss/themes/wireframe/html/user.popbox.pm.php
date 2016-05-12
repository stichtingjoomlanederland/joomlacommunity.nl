<?php
/**
* @package      EasyDiscuss
* @copyright    Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<a class="btn btn-default btn-xs pull-right" data-ed-conversations-api data-userid="<?php echo $user->id;?>" href="javascript:void(0);">
    <i class="fa fa-envelope"></i> <?php echo JText::_('COM_EASYDISCUSS_BUTTON_SEND'); ?>
</a>