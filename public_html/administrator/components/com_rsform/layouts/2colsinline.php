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
<?php foreach ($fieldsets as $page_num => $fields) { ?>
<!-- Do not remove this ID, it is used to identify the page so that the pagination script can work correctly -->
<table class="formTableLayout" border="0" id="rsform_{global:formid}_page_<?php echo $page_num; ?>">
<?php 
		if (!empty($fields['visible'])) {
			$chunks = array_chunk($fields['visible'], 2);
			foreach ($chunks as $chunkFields) {
?>
	<tr>
<?php		foreach ($chunkFields as $i => $field) {
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
		<td class="<?php echo ($i == 0 ? 'formTableLeft' : 'formTableRight');?>" valign="top">
		<?php if ($captchaField) { ?>
			{if {global:userid} == "0"}
		<?php } ?>
			<table class="formTableLayout" border="0">
				<tr class="rsform-block rsform-block-<?php echo JFilterOutput::stringURLSafe($fieldName); ?>">
					<td><?php if ($field['pagebreak']) { ?>&nbsp;<?php } else { ?>{<?php echo $fieldName; ?>:caption}<?php echo ($field['required'] ? '<strong class="formRequired">'.$requiredMarker.'</strong>' : '');?><?php } ?></td>
					<td>{<?php echo $fieldName; ?>:body}<?php if ($field['pagebreak']) { ?></td>
<?php } else { ?><br/>
					{<?php echo $fieldName; ?>:validation}</td>
<?php } ?>
					<td><?php if ($field['pagebreak']) { ?>&nbsp;<?php } else { ?>{<?php echo $fieldName; ?>:description}<?php } ?></td>
				</tr>
			</table>
		<?php if ($captchaField) { ?>
			{/if}
		<?php } ?>
		</td>
<?php
			} ?>
	</tr>
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
</table>
<?php
}