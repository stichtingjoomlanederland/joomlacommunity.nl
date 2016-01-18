<?php
/**
* @package RSFiles!
* @copyright (C) 2010-2014 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access'); ?>

<table class="adminform table table-stiped table-bordered">
	<tr>
		<td><?php echo JText::_('COM_RSFILES_REPORTS_USER'); ?></td>
		<td>
			<?php echo empty($this->data->name) ? JText::_('COM_RSFILES_GUEST') : $this->data->name; ?>
			(<?php echo $this->data->ip; ?>)
		</td>
	</tr>
	<tr>
		<td><?php echo JText::_('COM_RSFILES_REPORTS_DATE'); ?></td>
		<td><?php echo rsfilesHelper::showDate($this->data->date); ?></td>
	</tr>
	<tr>
		<td><?php echo JText::_('COM_RSFILES_REPORTS_TEXT'); ?></td>
		<td><?php echo nl2br($this->data->ReportMessage); ?></td>
	</tr>
</table>