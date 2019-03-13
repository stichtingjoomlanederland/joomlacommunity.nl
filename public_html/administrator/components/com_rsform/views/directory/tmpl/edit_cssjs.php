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
		<td colspan="2"><?php echo RSFormProHelper::showEditor('jform[CSS]', $this->directory->CSS, array('classes' => 'rs_100', 'syntax' => 'html', 'id' => 'CSS')); ?></td>
	</tr>
	<tr class="info">
		<td width="75" style="width: 75px;" align="right" class="key"><b>Javascript</b></td>
		<td><?php echo JText::_('RSFP_JS_DESC'); ?></td>
	</tr>
	<tr>
		<td colspan="2"><?php echo RSFormProHelper::showEditor('jform[JS]', $this->directory->JS, array('classes' => 'rs_100', 'syntax' => 'html', 'id' => 'JS')); ?></td>
	</tr>
</table>