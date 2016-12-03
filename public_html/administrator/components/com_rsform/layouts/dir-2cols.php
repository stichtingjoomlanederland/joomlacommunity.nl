<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2014 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

$out = '<div class="rsform-table" id="rsform-table3">'."\n";

// Organize fields into titles, images and other.
$titles = array();
$others = array();
$mainframe = JFactory::getApplication();

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

		// Add to titles
		if (count($titles) < 3) {
			$titles[] = $placeholders['value'];
			continue;
		}
		
		// Is it an upload field?		
		if (in_array($field->FieldName, $imagefields)) {
			continue;
		}
		
		// No more titles, add to other.
		$others[] = (object) array(
			'caption' => $placeholders['caption'],
			'value'	  => $placeholders['value']
		);
	}
}

if ($titles) {
	if (isset($titles[0])) {
		$out .= "\t".'<p class="rsform-main-title rsform-title">'.$titles[0].'</p>'."\n";
	}
	if (isset($titles[1])) {
		$out .= "\t".'<p class="rsform-big-subtitle rsform-title">'.$titles[1].'</p>'."\n";
	}
	if (isset($titles[2])) {
		$out .= "\t".'<p class="rsform-small-subtitle rsform-title">'.$titles[2].'</p>'."\n";
	}
}

if (!empty($imagefields)) {
	foreach ($imagefields as $image) {
		$out .= "\t\t".'{if {'.$image.':value}}<div class="rsform-gallery"><a href="javascript:void(0)" class="modal" rel="{handler: \'clone\'}"><img src="{'.$image.':path}" alt="" /></a></div>{/if}'."\n";
	}
}

if (!empty($others)) {
	$out .= "\t".'<div class="rsfp-table">'."\n";
	
	foreach ($others as $other) {
		$out .= "\t\t".'<div class="rsform-table-row">'."\n";
		$out .= "\t\t\t".'<div class="rsform-left-col">'.$other->caption.'</div>'."\n";
		$out .= "\t\t\t".'<div class="rsform-right-col">'.$other->value.'</div>'."\n";
		$out .= "\t\t".'</div>'."\n";
	}
	
	$out .= "\t".'</div>'."\n";
}

$out .= '</div>';
	
return $out;