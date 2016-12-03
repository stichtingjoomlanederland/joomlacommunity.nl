<?php
/**
 * @package	AcyMailing for Joomla!
 * @version	5.5.0
 * @author	acyba.com
 * @copyright	(C) 2009-2016 ACYBA S.A.R.L. All rights reserved.
 * @license	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('_JEXEC') or die('Restricted access');
?><?php

JPluginHelper::importPlugin('acymailing');
$dispatcher = JDispatcher::getInstance();
$typesFilters = array();
$outputFilters = implode('', $dispatcher->trigger('onAcyDisplayFilters', array(&$typesFilters, 'mail')));

if(empty($typesFilters)) return;

$filterClass = acymailing_get('class.filter');
$filterClass->addJSFilterFunctions();

$doc = JFactory::getDocument();

$js = '';
$datatype = "filter";
$jsFunction = "addAcyFilter";
if(!empty($this->mail->$datatype)){
	foreach($this->mail->{$datatype}['type'] as $num => $oneType){
		if(empty($oneType)) continue;
		$js .= "while(!document.getElementById('".$datatype."type$num')){".$jsFunction."();}
				document.getElementById('".$datatype."type$num').value= '$oneType';
				update".ucfirst($datatype)."($num);";
		if(empty($this->mail->{$datatype}[$num][$oneType])) continue;
		foreach($this->mail->{$datatype}[$num][$oneType] as $key => $value){
			$js .= "document.adminForm.elements['".$datatype."[$num][$oneType][$key]'].value = '".addslashes(str_replace(array("\n", "\r"), ' ', $value))."';";
			$js .= "if(document.adminForm.elements['".$datatype."[$num][$oneType][$key]'].type && document.adminForm.elements['".$datatype."[$num][$oneType][$key]'].type == 'checkbox'){ document.adminForm.elements['".$datatype."[$num][$oneType][$key]'].checked = 'checked'; }";
		}
		if($datatype == 'filter') $js .= " countresults($num);";
	}
}
$doc->addScriptDeclaration("window.addEvent('domready', function(){ $js });");

$typevaluesFilters = array();
$typevaluesFilters[] = JHTML::_('select.option', '', JText::_('FILTER_SELECT'));
foreach($typesFilters as $oneType => $oneName){
	$typevaluesFilters[] = JHTML::_('select.option', $oneType, $oneName);
}

?>
<br/>
<div class="acy_filter_mail">
	<input type="hidden" name="data[mail][filter]" value=""/>

	<div id="acybase_filters" style="display:none">
		<div id="filters_original">
			<?php echo JHTML::_('select.genericlist', $typevaluesFilters, "filter[type][__num__]", 'class="inputbox" size="1" onchange="updateFilter(__num__);countresults(__num__);"', 'value', 'text', 'filtertype__num__'); ?>
			<span id="countresult___num__"></span>

			<div class="acyfilterarea" id="filterarea___num__"></div>
		</div>
		<?php echo $outputFilters; ?>
	</div>
	<?php echo JText::_('RECEIVER_LISTS').' '.JText::_('RECEIVER_FILTER'); ?>
	<div class="onelineblockoptions" id="filtersblock">
		<span class="acyblocktitle"><?php echo JText::_('ACY_FILTERS'); ?></span>

		<div id="allfilters"></div>
		<button class="acymailing_button" onclick="addAcyFilter();return false;"><?php echo JText::_('ADD_FILTER'); ?></button>
	</div>
</div>
