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
			<?php echo $this->html('panel.head', 'COM_EASYDISCUSS_SETTINGS_NOTIFICATIONS_SYSTEM'); ?>

			<div class="panel-body">
				<div class="o-form-horizontal">
					<?php echo $this->html('settings.toggle', 'main_notifications', 'COM_EASYDISCUSS_ENABLE_NOTIFICATIONS'); ?>
					<?php echo $this->html('settings.textbox', 'main_notifications_limit', 'COM_EASYDISCUSS_NOTIFICATIONS_LIMIT', '', array('size' => 7), '', '', 'text-center'); ?>
					<?php echo $this->html('settings.textbox', 'main_notification_listings_limit', 'COM_EASYDISCUSS_NOTIFICATION_LIMIT_DISPLAY', '', array('size' => 7, 
									'defaultValue' => 20), '', '', 'text-center'); ?>
				</div>
			</div>
		</div>
	</div>

	<div class="col-md-6">
	</div>
</div>