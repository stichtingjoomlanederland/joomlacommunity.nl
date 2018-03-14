<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 - 2017 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<div class="app-filter-bar__cell">
	<input type="text" name="<?php echo $name; ?>" value="<?php echo $this->escape($search); ?>" placeholder="<?php echo JText::_('COM_EASYDISCUSS_SEARCH', true);?>" data-ed-table-search class="form-control app-filter-bar__search-input"
		<?php if ($tooltipMessage) { ?>
		data-ed-provide="tooltip" data-original-title="<?php echo $tooltipMessage;?>"
		<?php } ?>
	/>
	<span class="app-filter-bar__search-btn-group">
		<button class="btn btn-es-default-o app-filter-bar__search-btn" data-ed-table-search-submit>
			<i class="fa fa-search"></i>
		</button>

		<button class="btn btn-ed-danger-o app-filter-bar__search-btn" data-ed-table-search-reset>
			<i class="fa fa-times"></i>
		</button>
	</span>
</div>