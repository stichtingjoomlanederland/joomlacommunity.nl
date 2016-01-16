<?php
/**
* @package		EasyDiscuss
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
<script type="text/javascript">
EasyDiscuss
	.require()
	.script( 'customfields' )
	.done(function($){

	$( '.customFields' ).implement( EasyDiscuss.Controller.Administrator.CustomFields,{
		'defaultType': "<?php echo ($this->field->type == null ) ? 'text' : $this->field->type ?>",
		'customId': "<?php echo ($this->field->id == null ) ? '0' : $this->field->id ?>"
	});

	$('#type').on('change', function() {

	});
});

</script>
<div class="row-fluid customFields">

	<div class="span6">
		<div class="widget accordion-group">
			<div class="whead accordion-heading">
				<a href="javascript:void(0);" data-foundry-toggle="collapse" data-target="#option01">
				<h6><?php echo JText::_('COM_EASYDISCUSS_CUSTOMFIELDS_MAIN_TITLE'); ?></h6>
				<i class="icon-chevron-down"></i>
				</a>
			</div>

			<div id="option01" class="accordion-body collapse in">
				<div class="wbody">
					<div class="si-form-row">
						<div class="span5 form-row-label">
							<label><?php echo JText::_( 'COM_EASYDISCUSS_CUSTOMFIELDS_TITLE' ); ?></label>
						</div>
						<div class="span7"
							rel="ed-popover"
							data-placement="left"
							data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_CUSTOMFIELDS_TITLE' ); ?>"
							data-content="<?php echo JText::_('COM_EASYDISCUSS_CUSTOMFIELDS_TITLE_DESC'); ?>"
							>
							<input type="text" data-customid="<?php echo $this->field->id; ?>" class="input-full" name="title" maxlength="255" value="<?php echo $this->escape($this->field->title);?>" />
						</div>
					</div>
					<div class="si-form-row customFieldType">
						<div class="span5 form-row-label">
							<label><?php echo JText::_( 'COM_EASYDISCUSS_CUSTOMFIELDS_TYPE' ); ?></label>
						</div>
						<div class="span7"
							rel="ed-popover"
							data-placement="left"
							data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_CUSTOMFIELDS_TYPE' ); ?>"
							data-content="<?php echo JText::_('COM_EASYDISCUSS_CUSTOMFIELDS_TYPE_DESC'); ?>"
							>
							<div class="span7">
								<select name="type" id="type" size="1" class="full-width">
									<option<?php echo (!empty($this->field->type) && $this->field->type == 'text')? ' selected="selected"' : '' ?> value="text"><?php echo JText::_( 'COM_EASYDISCUSS_CUSTOMFIELDS_TYPE_TEXT' ); ?></option>
									<option<?php echo (!empty($this->field->type) && $this->field->type == 'area')? ' selected="selected"' : '' ?> value="area"><?php echo JText::_( 'COM_EASYDISCUSS_CUSTOMFIELDS_TYPE_AREA' ); ?></option>
									<option<?php echo (!empty($this->field->type) && $this->field->type == 'radio')? ' selected="selected"' : '' ?> value="radio"><?php echo JText::_( 'COM_EASYDISCUSS_CUSTOMFIELDS_TYPE_RADIO' ); ?></option>
									<option<?php echo (!empty($this->field->type) && $this->field->type == 'check')? ' selected="selected"' : '' ?> value="check"><?php echo JText::_( 'COM_EASYDISCUSS_CUSTOMFIELDS_TYPE_CHECK' ); ?></option>
									<option<?php echo (!empty($this->field->type) && $this->field->type == 'select')? ' selected="selected"' : '' ?> value="select"><?php echo JText::_( 'COM_EASYDISCUSS_CUSTOMFIELDS_TYPE_SELECT' ); ?></option>
									<option<?php echo (!empty($this->field->type) && $this->field->type == 'multiple')? ' selected="selected"' : '' ?> value="multiple"><?php echo JText::_( 'COM_EASYDISCUSS_CUSTOMFIELDS_TYPE_MULTI' ); ?></option>
								</select>
							</div>
						</div>
					</div>

					<div class="si-form-row">
						<div class="span5 advanceOptionsTitle form-row-label">
							<label><?php echo JText::_( 'COM_EASYDISCUSS_CUSTOMFIELDS_ADVANCE' ); ?></label>
						</div>
						<div class="span7">
							<?php $result = $this->field->getAdvanceOption( $this->field->type ); ?>

							<div class="addContainer">
								<?php echo $result['addButton'] ?>
							</div>

							<ul class="unstyled customFieldAdvanceOption mt-10">
								<?php echo $result['html']; ?>
							</ul>
							<div class="optionCount" totalcount="<?php echo $result['count'] ?>" style="display: none;"></div>
							<div class="fieldLoader discuss-loader" style="display: none;"></div>
						</div>
					</div>

					<div class="si-form-row">
						<div class="span5 form-row-label">
							<label><?php echo JText::_( 'COM_EASYDISCUSS_CUSTOMFIELDS_PUBLISHED' ); ?></label>
						</div>
						<div class="span7"
							rel="ed-popover"
							data-placement="left"
							data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_CUSTOMFIELDS_PUBLISHED' ); ?>"
							data-content="<?php echo JText::_('COM_EASYDISCUSS_CUSTOMFIELDS_PUBLISHED_DESC'); ?>"
							>
						<div class="span7">
							<?php echo $this->renderCheckbox( 'published' , $this->id->published ); ?>
						</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
