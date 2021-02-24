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
<?php if ($moderator) { ?>
<div class="o-media__image t-mr--xs">
	<?php echo $this->html('user.avatar', $moderator, [
		'hyperlink' => false
	]); ?>
</div>
<div class="o-media__body">
	<?php echo $moderator->getName();?> &nbsp;<i class="fa fa-chevron-down t-text--600 t-font-size--01"></i>
</div>
<?php } else { ?>
<div class="o-media__body">
	<?php echo JText::_('COM_ED_SELECT_MODERATOR');?>&nbsp; <i class="fa fa-chevron-down t-text--600 t-font-size--01"></i>
</div>
<?php } ?>