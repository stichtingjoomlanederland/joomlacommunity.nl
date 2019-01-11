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
			<?php echo $this->html('panel.head', 'COM_EASYDISCUSS_SETTINGS_CONVERSATIONS'); ?>
			
			<div class="panel-body">
				<div class="form-horizontal">
					<?php echo $this->html('settings.toggle', 'main_conversations', 'COM_EASYDISCUSS_ENABLE_CONVERSATIONS'); ?>

					<?php echo $this->html('settings.toggle', 'main_conversations_notification', 'COM_EASYDISCUSS_CONVERSATIONS_NOTIFICATIONS_ENABLE'); ?>

					<?php echo $this->html('settings.textbox', 'main_conversations_notification_interval', 'COM_EASYDISCUSS_CONVERSATIONS_NOTIFICATIONS_POLLING_INTERVAL', '', 
						array('size' => 8, 'postfix' => 'COM_EASYDISCUSS_SECONDS'), '', 'form-control-sm text-center'); ?>

					<?php echo $this->html('settings.textbox', 'main_conversations_notification_items', 'COM_EASYDISCUSS_CONVERSATIONS_TOTAL_ITEMS', '', array('size' => 9, 'postfix' => 'Conversations'), '', 'form-control-sm text-center'); ?>
				</div>
			</div>
		</div>
	</div>

	<div class="col-md-6">
	</div>
</div>