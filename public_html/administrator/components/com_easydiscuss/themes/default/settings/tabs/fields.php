<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
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
			<?php echo $this->html('panel.head', 'COM_EASYDISCUSS_SETTINGS_CUSTOMFIELDS'); ?>

			<div id="customfields" class="panel-body">
				<div class="form-horizontal">
					<div class="form-group">
                        <div class="col-md-5 control-label">
                            <?php echo $this->html('form.label', 'COM_EASYDISCUSS_SETTINGS_CUSTOMFIELDS_INPUT'); ?>
                        </div>
                        <div class="col-md-7">
							<?php echo $this->html('form.boolean', 'main_customfields_input', $this->config->get('main_customfields_input'));?>
						</div>
					</div>
					<div class="form-group">
                        <div class="col-md-5 control-label">
                            <?php echo $this->html('form.label', 'COM_EASYDISCUSS_SETTINGS_CUSTOMFIELDS'); ?>
                        </div>
                        <div class="col-md-7">
							<?php echo $this->html('form.boolean', 'main_customfields', $this->config->get('main_customfields'));?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="col-md-6">
	</div>
</div>


