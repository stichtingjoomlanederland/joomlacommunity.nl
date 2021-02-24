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
			<?php echo $this->html('panel.head', 'COM_EASYDISCUSS_SETTINGS_TNC'); ?>

			<div class="panel-body">
				<div class="o-form-horizontal">
					<?php echo $this->html('settings.toggle', 'main_tnc_question', 'COM_EASYDISCUSS_TNC_QUESTION'); ?>
					<?php echo $this->html('settings.toggle', 'main_tnc_reply', 'COM_EASYDISCUSS_TNC_REPLY'); ?>
					<?php echo $this->html('settings.toggle', 'main_tnc_comment', 'COM_EASYDISCUSS_TNC_COMMENT'); ?>
					<?php echo $this->html('settings.toggle', 'main_tnc_remember', 'COM_EASYDISCUSS_TNC_REMEMBER_SELECTION'); ?>

					<?php echo $this->html('settings.dropdown', 'main_tnc_remember_type', 'COM_EASYDISCUSS_TNC_REMEMBER_SELECTION_TYPE', '',
						array(
							'global' => 'COM_EASYDISCUSS_TNC_REMEMBER_SELECTION_TYPE_GLOBAL',
							'follow_type' => 'COM_EASYDISCUSS_TNC_REMEMBER_SELECTION_TYPE_FOLLOW_TYPE'
						)
					);?>

					<?php echo $this->html('settings.textarea', 'main_tnctext', 'COM_EASYDISCUSS_TNC_TITLE', '', array('value' => str_replace('<br />', "\n", $this->config->get('main_tnctext')))); ?>
				</div>
			</div>
		</div>
	</div>

	<div class="col-md-6">
	</div>
</div>
