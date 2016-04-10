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
<div class="form2LinesLayout">
<?php foreach ($fieldsets as $page_num => $fields) { ?>
<!-- Do not remove this ID, it is used to identify the page so that the pagination script can work correctly -->
<div id="rsform_{global:formid}_page_<?php echo $page_num; ?>">
<?php 
		foreach ($fields['visible'] as $field) {
			// handle special hidden fields
			if ($this->getProperty($field['data'], 'LAYOUTHIDDEN', false)) {
				continue;
			}
			$fieldName = $this->getProperty($field['data'], 'NAME');
?>
	<div class="formField rsform-block rsform-block-<?php echo JFilterOutput::stringURLSafe($fieldName); ?>">
<?php	if (!$field['pagebreak']) { ?>
			{<?php echo $fieldName; ?>:caption}<?php echo ($field['required'] ? '<strong class="formRequired">'.$requiredMarker.'</strong>' : '');?><br/>
			<?php } ?>{<?php echo $fieldName; ?>:body}<?php if (!$field['pagebreak']) { ?><br/>
<?php } ?>
<?php
		if (!$field['pagebreak']) { ?>
			{<?php echo $fieldName; ?>:validation}<br/>
			{<?php echo $fieldName; ?>:description}<br/>
		<?php } ?>
	</div>
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
</div>
<?php
}
?>
</div>