<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 - 2018 Stack Ideas Sdn Bhd. All rights reserved.
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
			<?php echo $this->html('panel.head', 'COM_EASYDISCUSS_POLLS'); ?>

			<div class="panel-body">
				<div class="form-horizontal">
					<?php echo $this->html('settings.toggle', 'main_polls', 'COM_EASYDISCUSS_ENABLE_POLLS'); ?>
					<?php echo $this->html('settings.toggle', 'main_polls_replies', 'COM_EASYDISCUSS_ENABLE_POLLS_REPLIES'); ?>
					<?php echo $this->html('settings.toggle', 'main_polls_multiple', 'COM_EASYDISCUSS_ENABLE_POLLS_MULTIPLE_VOTES'); ?>
					<?php echo $this->html('settings.toggle', 'main_polls_guests', 'COM_EASYDISCUSS_ENABLE_POLLS_FOR_GUESTS'); ?>
					<?php echo $this->html('settings.toggle', 'main_polls_lock', 'COM_EASYDISCUSS_ENABLE_LOCK_POLLS'); ?>
				</div>
			</div>
		</div>
	</div>

	<div class="col-md-6">
	</div>
</div>