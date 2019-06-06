<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2019 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
$mainframe = JFactory::getApplication();
$i	 = 0;
$out = '<div class="rsform-table" id="rsform-table2">'."\n";

foreach ($fields as $field) {
	if ($field->indetails) {
		$placeholders = array();

		if ($field->componentId < 0 && isset($headers[$field->componentId])) {
			$placeholders['caption'] = JText::_('RSFP_'.$headers[$field->componentId]);
			$placeholders['value']	 = $this->getStaticPlaceholder($headers[$field->componentId]);
		} else {
			$placeholders['caption'] = '{'.$field->FieldName.':caption}';
			$placeholders['value'] 	 = '{'.$field->FieldName.':value}';
		}

		$mainframe->triggerEvent('rsfp_b_onManageDirectoriesAfterCreatedPlaceholders', array($field, & $placeholders));
		
		if ($i == 0) {
			if ($hideEmptyValues)
			{
				$out .= "\t" . '{if ' . $placeholders['value'] . '}' . "\n";
			}

			$out .= "\t".'<p class="rsform-main-title rsform-title">'.$placeholders['value'].'</p>'."\n";

			if ($hideEmptyValues)
			{
				$out .= "\t" . '{/if}' . "\n";
			}
		} elseif ($i == 1) {
			if ($hideEmptyValues)
			{
				$out .= "\t" . '{if ' . $placeholders['value'] . '}' . "\n";
			}

			$out .= "\t".'<p class="rsform-big-subtitle rsform-title">'.$placeholders['value'].'</p>'."\n";

			if ($hideEmptyValues)
			{
				$out .= "\t" . '{/if}' . "\n";
			}
		} elseif ($i == 2) {
			if ($hideEmptyValues)
			{
				$out .= "\t" . '{if ' . $placeholders['value'] . '}' . "\n";
			}

			$out .= "\t".'<p class="rsform-small-subtitle rsform-title">'.$placeholders['value'].'</p>'."\n";

			if ($hideEmptyValues)
			{
				$out .= "\t" . '{/if}' . "\n";
			}
		} else {
			if ($hideEmptyValues)
			{
				$out .= "\t" . '{if ' . $placeholders['value'] . '}' . "\n";
			}

			$out .= "\t".'<div class="rsform-table-item">'."\n";
			$out .= "\t\t".'<div class="rsform-field-title">'.$placeholders['caption'].'</div>'."\n";
			$out .= "\t\t".'<div class="rsform-field-value">'.$placeholders['value'].'</div>'."\n";
			$out .= "\t".'</div>'."\n";

			if ($hideEmptyValues)
			{
				$out .= "\t" . '{/if}' . "\n";
			}
		}
		$i++;
	}
}

$out .= '</div>'."\n";
	
return $out;