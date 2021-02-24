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
<div class="row">
	<div class="col-md-6">
		<div class="panel">
			<?php echo $this->html('panel.head', 'COM_EASYDISCUSS_LAYOUT_RECENT_VIEW'); ?>

			<div class="panel-body">
				<div class="o-form-horizontal">
					<?php echo $this->html('settings.toggle', 'filters_resolved', 'COM_EASYDISCUSS_ENABLE_FILTER_RESOLVED'); ?>
					<?php echo $this->html('settings.toggle', 'filters_unresolved', 'COM_EASYDISCUSS_ENABLE_FILTER_UNRESOLVED'); ?>
					<?php echo $this->html('settings.toggle', 'filters_unanswered', 'COM_EASYDISCUSS_ENABLE_FILTER_UNANSWERED'); ?>
					<?php echo $this->html('settings.toggle', 'filters_unread', 'COM_EASYDISCUSS_ENABLE_FILTER_UNREAD'); ?>
					<?php echo $this->html('settings.toggle', 'filters_mine', 'COM_ED_ENABLE_FILTER_MINE'); ?>
					<?php echo $this->html('settings.toggle', 'filters_assign', 'COM_ED_ENABLE_FILTER_ASSIGNED'); ?>
				</div>
			</div>
		</div>
		
	</div>

	<div class="col-md-6">
	</div>
</div>
