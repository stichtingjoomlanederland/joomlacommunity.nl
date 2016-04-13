<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
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
			<?php echo $this->html('panel.head', 'COM_EASYDISCUSS_CUSTOMFIELDS_MAIN_TITLE'); ?>
			
			<div class="panel-body">
				<div class="form-horizontal">
					<div class="form-group customFieldType">
						<div class="col-md-5 control-label">
							<?php echo $this->html('form.label', 'COM_EASYDISCUSS_CUSTOMFIELDS_TYPE'); ?>
						</div>
						<div class="col-md-7">
							<select name="type" class="form-control" data-ed-field-type>
								<option value="" <?php echo !$field->type ? ' selected="selected"' : '';?>><?php echo JText::_('COM_EASYDISCUSS_FIELDS_SELECT_A_FIELD_TYPE');?></option>
								<option<?php echo $field->type == 'text' ? ' selected="selected"' : '' ?> value="text"><?php echo JText::_( 'COM_EASYDISCUSS_CUSTOMFIELDS_TYPE_TEXT' ); ?></option>
								<option<?php echo $field->type == 'area' ? ' selected="selected"' : '' ?> value="area"><?php echo JText::_( 'COM_EASYDISCUSS_CUSTOMFIELDS_TYPE_AREA' ); ?></option>
								<option<?php echo $field->type == 'radio' ? ' selected="selected"' : '' ?> value="radio"><?php echo JText::_( 'COM_EASYDISCUSS_CUSTOMFIELDS_TYPE_RADIO' ); ?></option>
								<option<?php echo $field->type == 'check' ? ' selected="selected"' : '' ?> value="check"><?php echo JText::_( 'COM_EASYDISCUSS_CUSTOMFIELDS_TYPE_CHECK' ); ?></option>
								<option<?php echo $field->type == 'select' ? ' selected="selected"' : '' ?> value="select"><?php echo JText::_( 'COM_EASYDISCUSS_CUSTOMFIELDS_TYPE_SELECT' ); ?></option>
								<option<?php echo $field->type == 'multiple' ? ' selected="selected"' : '' ?> value="multiple"><?php echo JText::_( 'COM_EASYDISCUSS_CUSTOMFIELDS_TYPE_MULTI' ); ?></option>
							</select>
						</div>
					</div>

					<div class="form-group">
						<div class="col-md-5 control-label">
							<?php echo $this->html('form.label', 'COM_EASYDISCUSS_CUSTOMFIELDS_TITLE'); ?>
						</div>
						<div class="col-md-7">
							<input type="text" data-customid="<?php echo $field->id; ?>" class="form-control" name="title" maxlength="255" value="<?php echo $this->escape($field->title);?>" />
						</div>
					</div>

					<div class="form-group">
						<div class="col-md-5 control-label">
							<?php echo $this->html('form.label', 'COM_EASYDISCUSS_CUSTOMFIELDS_SECTION'); ?>
						</div>
						<div class="col-md-7">
							<select name="section" class="form-control">
								<option<?php echo $field->section == '1' ? ' selected="selected"' : ''; ?> value="1"><?php echo JText::_('COM_EASYDISCUSS_CUSTOMFIELDS_SECTION_QUESTION'); ?></option>
								<option<?php echo $field->section == '2' ? ' selected="selected"' : ''; ?> value="2"><?php echo JText::_('COM_EASYDISCUSS_CUSTOMFIELDS_SECTION_REPLY'); ?></option>
							</select>
						</div>
					</div>

					<div class="form-group">
						<div class="col-md-5 control-label">
							<?php echo $this->html('form.label', 'COM_EASYDISCUSS_CUSTOMFIELDS_PUBLISHED'); ?>
						</div>
						<div class="col-md-7">
							<?php echo $this->html('form.boolean', 'published', $field->published); ?>
						</div>
					</div>

					
					<div class="form-group">
						<div class="col-md-5 control-label">
							<?php echo $this->html('form.label', 'COM_EASYDISCUSS_CUSTOMFIELDS_REQUIRED'); ?>
						</div>
						<div class="col-md-7">

							<?php echo $this->html('form.boolean', 'required', $field->required); ?>
						</div>
					</div>

				</div>
			</div>
		</div>
	</div>

	<div class="col-md-6" data-ed-fields-options>
		<?php if ($field->id) { ?>
			<?php echo $this->output('admin/fields/options', array('field' => $field)); ?>
		<?php } ?>
	</div>
</div>