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
<form action="index.php" method="post" name="adminForm" id="adminForm">
	<div class="row-fluid">
		<div class="span6">
			<div class="panel">
				<?php echo $this->html('panel.head', 'COM_EASYDISCUSS_ROLE_FORM_GENERAL'); ?>

				<div class="panel-body">
					<div class="form-horizontal">
						<div class="form-group">
							<div class="col-md-5 control-label">
								<?php echo $this->html('form.label', 'COM_EASYDISCUSS_ROLE_TITLE'); ?>
							</div>
							<div class="col-md-7">
								<?php echo $this->html('form.textbox', 'title', $role->title); ?>
							</div>
						</div>
						<div class="form-group">
							<div class="col-md-5 control-label">
								<?php echo $this->html('form.label', 'COM_EASYDISCUSS_ROLE_USERGROUP'); ?>
							</div>
							<div class="col-md-7">
								<?php echo $this->html('form.dropdown', 'usergroup_id', $groups, $role->usergroup_id); ?>
							</div>
						</div>
						<div class="form-group">
							<div class="col-md-5 control-label">
								<?php echo $this->html('form.label', 'COM_EASYDISCUSS_ROLE_LABEL_COLOUR'); ?>
							</div>
							<div class="col-md-7">
								<?php echo $this->html('form.dropdown', 'colorcode', $colors, $role->colorcode); ?>
							</div>
						</div>
						<div class="form-group">
							<div class="col-md-5 control-label">
								<?php echo $this->html('form.label', 'COM_EASYDISCUSS_ROLE_PUBLISHED'); ?>
							</div>
							<div class="col-md-7">
								<?php echo $this->html('form.boolean', 'published', $role->published); ?>
							</div>
						</div>
						<div class="form-group">
							<div class="col-md-5 control-label">
								<?php echo $this->html('form.label', 'COM_EASYDISCUSS_ROLE_CREATION_DATE'); ?>
							</div>
							<div class="col-md-7">
								<input type="text" id="datepicker" class="form-control" name="created_time" value="<?php echo $role->created_time;?>" />
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<?php echo $this->html('form.hidden', 'roles', 'save'); ?>
	
	<input type="hidden" name="role_id" value="<?php echo $role->id;?>" />
	<input type="hidden" name="savenew" id="savenew" value="0" />
</form>