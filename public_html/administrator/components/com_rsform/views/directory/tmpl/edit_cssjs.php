<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2014 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access'); ?>
<table class="admintable table">
	<tr class="info">
		<td width="75" style="width: 75px;" align="right" class="key"><b>CSS</b></td>
		<td><?php echo JText::_('RSFP_CSS_DESC'); ?></td>
	</tr>
	<tr>
		<td colspan="2"><textarea class="rs_textarea codemirror-css" rows="20" cols="75" name="jform[CSS]" id="CSS" style="width:100%;"><?php echo $this->escape($this->directory->CSS);?></textarea></td>
	</tr>
	<tr class="info">
		<td width="75" style="width: 75px;" align="right" class="key"><b>Javascript</b></td>
		<td><?php echo JText::_('RSFP_JS_DESC'); ?></td>
	</tr>
	<tr>
		<td colspan="2"><textarea class="rs_textarea codemirror-js" rows="20" cols="75" name="jform[JS]" id="JS" style="width:100%;"><?php echo $this->escape($this->directory->JS);?></textarea></td>
	</tr>
</table>