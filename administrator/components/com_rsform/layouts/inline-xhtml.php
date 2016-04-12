<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2014 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
?>
<fieldset class="formFieldset">
	<?php if ($showFormTitle) { ?>
	<legend>{global:formtitle}</legend>
	<?php } ?>
	{error}
	<?php foreach ($fieldsets as $page_num => $fields) { ?>
	<!-- Do not remove this ID, it is used to identify the page so that the pagination script can work correctly -->
	<ol class="formContainer" id="rsform_{global:formid}_page_<?php echo $page_num; ?>">
<?php 
			foreach ($fields['visible'] as $field) {
				// handle special hidden fields
				if ($this->getProperty($field['data'], 'LAYOUTHIDDEN', false)) {
					continue;
				}
				$fieldName = $this->getProperty($field['data'], 'NAME');
	?>
		<li class="rsform-block rsform-block-<?php echo JFilterOutput::stringURLSafe($fieldName); ?>">
<?php	if (!$field['pagebreak']) { ?>
			<div class="formCaption">{<?php echo $fieldName; ?>:caption}<?php echo ($field['required'] ? '<strong class="formRequired">'.$requiredMarker.'</strong>' : '');?></div>
<?php } ?>
			<div class="formBody">{<?php echo $fieldName; ?>:body}<?php if (!$field['pagebreak']) { ?><span class="formClr"><?php } else { ?></div>
<?php } ?>
<?php
			if (!$field['pagebreak']) { ?>
{<?php echo $fieldName; ?>:validation}</span></div>
			<div class="formDescription">{<?php echo $fieldName; ?>:description}</div>
<?php } ?>
		</li>
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
	</ol>
<?php
	}
	?>
</fieldset>