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
<div class="tab-item user-site">
	<div class="ed-form-panel__hd">
	    <div class="ed-form-panel__title"><?php echo JText::_('COM_EASYDISCUSS_PROFILE_OTHERS'); ?></div>
	    <div class="ed-form-panel__"><?php echo JText::_('COM_EASYDISCUSS_PROFILE_OTHERS_DESC'); ?></div>
	</div>
	<div class="ed-form-panel__bd">
		<div class="text-center">
		    <label for="forMarkAllRead"><?php echo JText::_('COM_EASYDISCUSS_PROFILE_MARK_ALL_READ_DESC'); ?></label>
		</div>
		<div class="center">
			<a href="javascript:void(0)" class="btn btn-success btn-sm t-lg-mt--md" data-ed-mark-allread><?php echo JText::_('COM_EASYDISCUSS_PROFILE_MARK_ALL_READ');?></a>
		</div>
		<div>
	        <div class="o-alert t-mb--no t-lg-mt--lg" data-ed-allread-status></div>
	    </div>
	</div>
	
</div>
