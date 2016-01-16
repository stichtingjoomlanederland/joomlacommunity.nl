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
<div class="tab-item user-alias pb-15" >
	<div class="control-group">
		<div class="input-label pb-10"><?php echo JText::_('COM_EASYDISCUSS_PROFILE_ALIAS'); ?></div>
		<div class="input-wrap mb-5"><input type="text" value="<?php echo $profile->alias; ?>" id="profile-alias" name="alias" class="input width-250"></div>

		<button type="button" class="btn btn-success" onclick="discuss.user.checkAlias()"><?php echo JText::_( 'COM_EASYDISCUSS_CHECK_AVAILABILITY' );?></button>
		<span id="alias-status" class="ml-5"></span>
	</div>
</div>
