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
			<?php echo $this->html('panel.head', 'COM_EASYDISCUSS_SETTINGS_SITEDETAILS'); ?>

			<div id="option01" class="panel-body">
				<div class="form-horizontal">
					<div class="form-group">
                        <div class="col-md-5 control-label">
                            <?php echo $this->html('form.label', 'COM_EASYDISCUSS_SITEDETAILS_ENABLE_QUESTION'); ?>
                        </div>
                        <div class="col-md-7">
							<?php echo $this->html('form.boolean', 'tab_site_question', $this->config->get('tab_site_question'));?>
						</div>
					</div>
					<div class="form-group">
                        <div class="col-md-5 control-label">
                            <?php echo $this->html('form.label', 'COM_EASYDISCUSS_SITEDETAILS_ENABLE_REPLIES'); ?>
                        </div>
                        <div class="col-md-7">
							<?php echo $this->html('form.boolean', 'tab_site_reply', $this->config->get('tab_site_reply'));?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="col-md-6">
		<div class="panel">
			<?php echo $this->html('panel.head', 'COM_EASYDISCUSS_SETTINGS_SITEDETAILS_ACCESS'); ?>

			<div id="option02" class="panel-body">
				<div class="form-horizontal">
					<div class="form-group">
                        <div class="col-md-5 control-label">
                            <?php echo $this->html('form.label', 'COM_EASYDISCUSS_SITEDETAILS_VIEW_ACCESS'); ?>
                        </div>
                        <div class="col-md-7">
							<?php
							$access = explode(',', trim($this->config->get('tab_site_access')));
							?>
							<select name="tab_site_access[]" multiple="multiple" style="height:150px;">
							<?php foreach ($joomlaGroups as $group) { ?>
								<option value="<?php echo $group->id;?>"<?php echo in_array($group->id, $access) ? ' selected="selected"' : '';?>><?php echo $group->name; ?></option>
							<?php }?>
							</select>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>


