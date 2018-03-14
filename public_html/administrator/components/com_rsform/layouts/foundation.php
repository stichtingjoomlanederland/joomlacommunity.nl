<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2014 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

if ($this->_form->GridLayout)
{
	require_once __DIR__ . '/grid/foundation.php';
	
	$grid = new RSFormProGridFoundation($this->_form->GridLayout, $formId, $formOptions, $requiredMarker, $showFormTitle);
	echo $grid->generate();
	return;
}
?>
<?php if ($showFormTitle) { ?>
<h2>{global:formtitle}</h2>
<?php } ?>
{error}
<?php foreach ($fieldsets as $page_num => $fields) { ?>
<!-- Do not remove this ID, it is used to identify the page so that the pagination script can work correctly -->
<fieldset class="formContainer" id="rsform_{global:formid}_page_<?php echo $page_num; ?>">
<?php
	if (!empty($fields['visible'])) {
		foreach ($fields['visible'] as $field) {
			// handle special hidden fields
			if ($this->getProperty($field['data'], 'LAYOUTHIDDEN', false)) {
				continue; 
			}
			// retrieve the componentTypeId to check if is free text
			$componentTypeId = $this->getComponentType($field['data']['componentId'], $formId);
			
			$fieldName = $this->getProperty($field['data'], 'NAME');
			
			// detect if the field is a Captcha Field of any sort
			$captchaField = false;
			if (in_array($componentTypeId, RSFormProHelper::$captchaFields) && !empty($formOptions->RemoveCaptchaLogged)) {
				$captchaField = true;
			}
?>
<?php if ($captchaField) { ?>
	{if {global:userid} == "0"}
<?php } ?>
	<div class="row rsform-block rsform-block-<?php echo JFilterOutput::stringURLSafe($fieldName)?>{<?php echo $fieldName; ?>:errorClass}">
<?php if ($componentTypeId != RSFORM_FIELD_FREETEXT) {?>
		<div class="medium-3 columns"><label class="<?php echo (!$field['pagebreak'] ? 'has-tip ' : ''); ?> control-label formControlLabel"<?php echo (!$field['pagebreak'] ? ' data-tooltip aria-haspopup="true" data-disable-hover="false" tabindex="1"' : ''); ?><?php if (!$field['pagebreak']) {?> title="{<?php echo $fieldName; ?>:description}" for="<?php echo $fieldName; ?>"<?php } ?>><?php if ($field['pagebreak']) { ?> &nbsp;<?php } else { ?>{<?php echo $fieldName; ?>:caption}<?php echo ($field['required'] ? '<strong class="formRequired">'.$requiredMarker.'</strong>' : ''); } ?></label></div>
		<div class="medium-9 columns formControls">
<?php } ?>
			{<?php echo $fieldName; ?>:body}<?php if (!$field['pagebreak'] && $componentTypeId != RSFORM_FIELD_FREETEXT) { ?> <span class="formValidation">{<?php echo $fieldName; ?>:validation}</span><?php } else { echo "\n"; } ?>
<?php if ($componentTypeId != RSFORM_FIELD_FREETEXT) {?>				
		</div>
<?php } ?>
	</div>
<?php if ($captchaField) { ?>
	{/if}
<?php } ?>
<?php
		}
	}
		
		if (!empty($fields['hidden'])) {
			foreach ($fields['hidden'] as $field) {
				$fieldName = $this->getProperty($field['data'], 'NAME'); ?>
	{<?php echo $fieldName; ?>:body}
<?php
			}
		}
?>
</fieldset>
<?php 
}