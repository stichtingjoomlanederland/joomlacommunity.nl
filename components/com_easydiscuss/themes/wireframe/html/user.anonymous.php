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
<div class="o-avatar-status is-offline">
	<a href="javascript:void(0);"
	    class="o-avatar o-avatar--<?php echo isset($size) ? $size : 'sm'; ?>"
	    data-ed-provide="tooltip"
	    data-placement="top"
	    title="<?php echo JText::_('COM_EASYDISCUSS_ANONYMOUS_USER');?>"
	>
	    <?php if ($this->config->get('layout_avatar')) { ?>
	        <img src="<?php echo ED::getDefaultAvatar();?>" alt="<?php echo JText::_('COM_EASYDISCUSS_ANONYMOUS_USER');?>"/>
	    <?php } else { ?>
	        <span class="o-avatar o-avatar--<?php echo $size; ?>; ?> o-avatar--text o-avatar--bg-1"><?php echo JText::_('COM_EASYDISCUSS_ANONYMOUS_USER');?></span>
	    <?php } ?>
	</a>
</div>
