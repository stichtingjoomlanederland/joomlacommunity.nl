<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2014 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
?>
<?php if ($showFormTitle) { ?>
<div class="componentheading">{global:formtitle}</div>
<?php } ?>
{error}
<table class="formTableLayout" border="0">
<?php foreach ($fieldsets as $page_num => $fields) { ?>
<!-- Do not remove this ID, it is used to identify the page so that the pagination script can work correctly -->
	<tbody id="rsform_{global:formid}_page_<?php echo $page_num; ?>">
<?php
	if (!empty($fields['visible'])) {
		foreach ($fields['visible'] as $field) {
			// handle special hidden fields
			if ($this->getProperty($field['data'], 'LAYOUTHIDDEN', false)) {
				continue;
			}
			$fieldName = $this->getProperty($field['data'], 'NAME');
			// retrieve the componentTypeId
			$componentTypeId = $this->getComponentType($field['data']['componentId'], $formId);
			
			// detect if the field is a Captcha Field of any sort
			$captchaField = false;
			if (in_array($componentTypeId, RSFormProHelper::$captchaFields) && !empty($formOptions->RemoveCaptchaLogged)) {
				$captchaField = true;
			}
?>
<?php if ($captchaField) { ?>
			{if {global:userid} == "0"}
<?php } ?>
			<tr class="rsform-block rsform-block-<?php echo JFilterOutput::stringURLSafe($fieldName); ?>">
				<td><?php if ($field['pagebreak']) { ?>&nbsp;<?php } else { ?>{<?php echo $fieldName; ?>:caption}<?php echo ($field['required'] ? '<strong class="formRequired">'.$requiredMarker.'</strong>' : '');?><?php } ?></td>
				<td>{<?php echo $fieldName; ?>:body}<?php if ($field['pagebreak']) { ?></td>
<?php } else { ?><div class="formClr"></div>{<?php echo $fieldName; ?>:validation}</td>
<?php } ?>
				<td><?php if ($field['pagebreak']) { ?>&nbsp;<?php } else { ?>{<?php echo $fieldName; ?>:description}<?php } ?></td>
			</tr>
<?php if ($captchaField) { ?>
			{/if}
<?php } ?>
<?php
		} ?>
<?php
	}
	
		if (!empty($fields['hidden'])) {
			foreach ($fields['hidden'] as $field) {
				$fieldName = $this->getProperty($field['data'], 'NAME'); ?>
	{<?php echo $fieldName; ?>:body} 
<?php
			}
		}
?>
	</tbody>
<?php
}
?>
</table>