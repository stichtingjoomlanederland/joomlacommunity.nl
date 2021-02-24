<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<div class="o-form-check">
	<input id="anonymous" class="o-form-check-input" type="checkbox" name="anonymous" value="1" <?php echo $anonymous ? ' checked="checked"' : '';?> />
	<label for="anonymous" class="o-form-check-label">
		<?php echo $operation == 'replying' ? JText::_('COM_EASYDISCUSS_REPLY_ANONYMOUSLY') : JText::_('COM_EASYDISCUSS_POST_ANONYMOUSLY');?>
	</label>
</div>