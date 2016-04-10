<?php
/**
 * @package    RSFirewall!
 * @copyright  (c) 2009 - 2016 RSJoomla!
 * @link       https://www.rsjoomla.com
 * @license    GNU General Public License http://www.gnu.org/licenses/gpl-3.0.en.html
 */

defined('_JEXEC') or die('Restricted access');
JText::script('COM_RSFIREWALL_BUTTON_FAILED');
JText::script('COM_RSFIREWALL_BUTTON_PROCESSING');
JText::script('COM_RSFIREWALL_BUTTON_SUCCESS');
JText::script('COM_RSFIREWALL_CONFIRM_UNIGNORE');
?>

<div class="com-rsfirewall-page-wrapper" style="padding:50px;">
	<div class="alert alert-warning">
		<p><small><?php echo JText::_('COM_RSFIREWALL_IGNORED_FILES_ALERT_WARNING'); ?></small></p>
	</div>
	<table id="com-rsfirewall-joomla-configuration-table" class="table table-striped">
		<caption><h3><?php echo JText::_('COM_RSFIREWALL_IGNORED_FILE_TITLE') ?></h3></caption>
		<thead>
		<tr>
			<th><?php echo JText::_('COM_RSFIREWALL_IGNORED_FILE_DATE'); ?></th>
			<th><?php echo JText::_('COM_RSFIREWALL_IGNORED_FILE_FILE'); ?></th>
			<th><?php echo JText::_('COM_RSFIREWALL_IGNORED_FILE_REASON'); ?></th>
			<th>&shy;</th>
		</tr>
		</thead>
		<tbody>
		<?php foreach ($this->files as $file)
		{ ?>
			<tr>
				<td>
					<?php echo JHtml::_('date', $file->date); ?>
				</td>
				<td style="width:50%">
					<?php echo $this->escape($file->file); ?>
				</td>
				<td>
					<?php echo JText::_('COM_RSFIREWALL_IGNORED_FILE_FLAG'.$file->flag); ?>
				</td>
				<td>
					<button class="btn btn-danger" id="removeIgnored<?php echo $file->id ?>" onclick="RSFirewall.ignore.remove('<?php echo $file->id ?>')"><?php echo JText::_('COM_RSFIREWALL_IGNORED_FILE_DELETE_FROM_DB') ?></button>
				</td>
			</tr>
		<?php } ?>
		</tbody>
	</table>
</div>


