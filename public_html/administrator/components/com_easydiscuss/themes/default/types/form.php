<?php
/**
 * @package		EasyDiscuss
 * @copyright	Copyright (C) 2010 Stack Ideas Private Limited. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 *
 * EasyDiscuss is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

defined('_JEXEC') or die('Restricted access');
?>
<form action="index.php" method="post" name="adminForm" id="adminForm">
	<div class="app-tabs">
		<ul class="app-tabs-list g-list-unstyled">
			<li class="tabItem active">
				<a href="#general" data-ed-toggle="tab">
					<?php echo JText::_('COM_EASYDISCUSS_POST_TYPES_TAB_GENERAL');?>
				</a>
			</li>
		</ul>
	</div>

	<div class="tab-content">
		<div id="general" class="tab-pane active in">
			<div class="row">
				<div class="col-md-6">
					<div class="panel">
						<?php echo $this->html('panel.head', 'COM_EASYDISCUSS_POST_TYPES_TAB_GENERAL'); ?>

						<div class="panel-body">
							<div class="form-horizontal">
								<div class="form-group">
									<div class="col-md-5 control-label">
										<?php echo $this->html('form.label', 'COM_EASYDISCUSS_POST_TYPES_TITLE'); ?>
									</div>
									<div class="col-md-7">
										<input type="text" class="form-control" id="title" name="title" size="55" maxlength="255" value="<?php echo $postTypes->title;?>" />
									</div>
								</div>
								<?php if ($postTypes->id) { ?>
								<div class="form-group">
									<div class="col-md-5 control-label">
										<?php echo $this->html('form.label', 'COM_EASYDISCUSS_POST_TYPES_ALIAS'); ?>
									</div>
									<div class="col-md-7">
										<input type="text" class="form-control" id="alias" name="alias" size="55" maxlength="255" value="<?php echo $postTypes->alias;?>" <?php echo $postTypes->id ? 'readonly="readonly"' : ''; ?> />
									</div>
								</div>
								<?php } ?>

								<div class="form-group">
									<div class="col-md-5 control-label">
										<?php echo $this->html('form.label', 'COM_EASYDISCUSS_POST_TYPES_SUFFIX'); ?>
									</div>
									<div class="col-md-7">
										<input type="text" class="form-control" id="suffix" name="suffix" size="55" maxlength="255" value="<?php echo $postTypes->suffix;?>" />
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="col-md-6">
					<div class="panel">
						<?php echo $this->html('panel.head', 'COM_EASYDISCUSS_POST_TYPES_TAB_ASSOCIATION'); ?>

						<div class="panel-body">
							<div class="form-horizontal">
								<div class="form-group">
									<div class="col-md-5 control-label">
										<?php echo $this->html('form.label', 'COM_EASYDISCUSS_POST_TYPES_ASSOCIATION_TYPE'); ?>
									</div>

									<div class="col-md-7">
										<select name="type" class="form-control" data-type>
											<option value="global" <?php echo !$postTypes->type || $postTypes->type == 'global' ? 'selected="selected"' : '';?>><?php echo JText::_('COM_EASYDISCUSS_POST_TYPES_ASSOCIATION_GLOBAL'); ?></option>
											<option value="category" <?php echo $postTypes->type == 'category' ? 'selected="selected"' : '';?>><?php echo JText::_('COM_EASYDISCUSS_POST_TYPES_ASSOCIATION_CATEGORY');?></option>
										</select>
									</div>
								</div>

								<div class="form-group <?php echo !$postTypes->type || $postTypes->type == 'global' ? ' hide' : '';?>" data-type-category>
									<div class="col-md-5 control-label">
										<?php echo $this->html('form.label', 'COM_EASYDISCUSS_POST_TYPES_SELECT_CATEGORIES'); ?>
									</div>

									<div class="col-md-7">
										<?php echo $categories; ?>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<?php echo $this->html('form.hidden', 'post_types', '', ''); ?>
	<input type="hidden" name="id" value="<?php echo $postTypes->id ?>" />

</form>
