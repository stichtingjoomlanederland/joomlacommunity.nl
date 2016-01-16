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
<div class="tab-item user-alias">
	<div class="form-group">
		<label><?php echo JText::_('COM_EASYDISCUSS_PROFILE_ALIAS'); ?></label>
		<p><input type="text" value="<?php echo $profile->alias; ?>" id="profile-alias" name="alias" class="form-control"></p>
		<button type="button" class="butt butt-default" onclick="discuss.user.checkAlias()"><?php echo JText::_( 'COM_EASYDISCUSS_CHECK_AVAILABILITY' );?></button>
	</div>
</div>
