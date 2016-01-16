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
<div class="tab-item user-post">
	<div class="form-group">
		<label><?php echo JText::_('COM_EASYDISCUSS_PROFILE_USERNAME'); ?></label>
		<div><b><?php echo $user->username; ?></b></div>
	</div>
	<div class="form-group">
		<label><?php echo JText::_('COM_EASYDISCUSS_PROFILE_EMAIL'); ?></label>
		<div><b><?php echo $user->email; ?></b></div>
	</div>
	<div class="form-group">
		<label><?php echo JText::_('COM_EASYDISCUSS_PROFILE_PASSWORD'); ?></label>
		<input type="password" value="" name="password" class="form-control" autocomplete="off">
	</div>
	<div class="form-group">
		<label><?php echo JText::_('COM_EASYDISCUSS_PROFILE_RETYPE_PASSWORD'); ?></label>
		<input type="password" value="" name="password2" class="form-control" autocomplete="off">
	</div>
</div>
