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
<div class="panel-head">
	<div class="t-d--flex">
		<div class="t-flex-grow--1">
			<b class="panel-head-title"><?php echo JText::_($header);?></b>
			<div class="panel-info"><?php echo JText::_($desc);?></div>
		</div>
		<?php if ($helpLink) { ?>
		<div class="">
			<a href="<?php echo $helpLink;?>" class="o-btn o-btn--default-o o-btn--sm" target="_blank">
				<?php echo JText::_('COM_ED_HELP'); ?>
			</a>
		</div>
		<?php } ?>
	</div>
</div>