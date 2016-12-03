<?php
/**
* @package      EasyDiscuss
* @copyright	Copyright (C) 2010 Stack Ideas Private Limited. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Restricted access');
?>
<div class="row">
	<div class="col-md-6">
		<div class="panel">
			<?php echo $this->html('panel.head', 'COM_EASYDISCUSS_SETTINGS_SOCIALSHARE_PINGOMATIC_TITLE'); ?>


			<div class="panel-body">
				<div class="form-horizontal">
					<div class="form-group">
						<div class="col-md-6 control-label">
							<?php echo $this->html('form.label', 'COM_EASYDISCUSS_SETTINGS_SOCIALSHARE_PINGOMATIC_ENABLE'); ?>
						</div>
						<div class="col-md-6">
							<?php echo $this->html('form.boolean', 'integration_pingomatic', $this->config->get('integration_pingomatic')); ?>
						</div>
					</div>
				</div>
			</div>

		</div>

	</div>
</div>
