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
			<?php echo $this->html('panel.head', 'COM_EASYDISCUSS_CUSTOMFIELDS_MAIN_TITLE'); ?>

			<div class="panel-body">
				<div class="o-form-horizontal">
					<?php echo $this->html('forms.dropdown', 'type', 'COM_EASYDISCUSS_CUSTOMFIELDS_TYPE', $field->type,
						array(
							'' => 'COM_EASYDISCUSS_FIELDS_SELECT_A_FIELD_TYPE',
							'text' => 'COM_EASYDISCUSS_CUSTOMFIELDS_TYPE_TEXT',
							'area' => 'COM_EASYDISCUSS_CUSTOMFIELDS_TYPE_AREA',
							'radio' => 'COM_EASYDISCUSS_CUSTOMFIELDS_TYPE_RADIO',
							'check' => 'COM_EASYDISCUSS_CUSTOMFIELDS_TYPE_CHECK',
							'select' => 'COM_EASYDISCUSS_CUSTOMFIELDS_TYPE_SELECT',
							'multiple' => 'COM_EASYDISCUSS_CUSTOMFIELDS_TYPE_MULTI'
						), 'data-ed-field-type'
					); ?>

					<?php echo $this->html('forms.textbox', 'title', 'COM_EASYDISCUSS_CUSTOMFIELDS_TITLE', $this->escape($field->title), array('attributes' => 'data-customid="' . $field->id . '"')); ?>

					<?php echo $this->html('forms.dropdown', 'section', 'COM_EASYDISCUSS_CUSTOMFIELDS_SECTION', $field->section,
						array(
							'1' => 'COM_EASYDISCUSS_CUSTOMFIELDS_SECTION_QUESTION',
							'2' => 'COM_EASYDISCUSS_CUSTOMFIELDS_SECTION_REPLY'
						)
					); ?>

					<?php echo $this->html('forms.toggle', 'published', 'COM_EASYDISCUSS_CUSTOMFIELDS_PUBLISHED', $field->published); ?>
					<?php echo $this->html('forms.toggle', 'required', 'COM_EASYDISCUSS_CUSTOMFIELDS_REQUIRED', $field->required); ?>
					<?php echo $this->html('forms.toggle', 'global', 'COM_EASYDISCUSS_CUSTOMFIELDS_GLOBAL', $field->global); ?>
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
