<?php
/**
 * @package    RSFirewall!
 * @copyright  (c) 2009 - 2016 RSJoomla!
 * @link       https://www.rsjoomla.com
 * @license    GNU General Public License http://www.gnu.org/licenses/gpl-3.0.en.html
 */

defined('_JEXEC') or die('Restricted access');
?>
<table class="adminlist table table-striped">
<thead>
	<tr>
		<th width="1%" nowrap="nowrap"><?php echo JText::_('#'); ?></th>
		<th width="1%" nowrap="nowrap"><input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" /></th>
		<th width="1%" nowrap="nowrap"><?php echo JText::_('COM_RSFIREWALL_FILES_MODIFIED_DATE'); ?></th>
		<th><?php echo JText::_('COM_RSFIREWALL_FILES_FILE_PATH'); ?></th>
		<th><?php echo JText::_('COM_RSFIREWALL_ORIGINAL_HASH'); ?></th>
		<th><?php echo JText::_('COM_RSFIREWALL_MODIFIED_HASH'); ?></th>
	</tr>
</thead>
<?php foreach ($this->files as $i => $file) { ?>
<tr class="row<?php echo $i % 2; ?>">
	<td width="1%" nowrap="nowrap"><?php echo $i+1; ?></td>
	<td width="1%" nowrap="nowrap"><?php echo JHtml::_('grid.id', $i, $file->id); ?></td>
	<td width="1%" nowrap="nowrap"><?php echo $this->showDate($file->date); ?></td>
	<td><?php echo $this->escape($file->path); ?></td>
	<td width="1%" nowrap="nowrap"><?php echo $this->escape($file->hash); ?></td>
	<td width="1%" nowrap="nowrap" class="com-rsfirewall-level-high"><?php echo $file->modified_hash; ?></td>
</tr>
<?php } ?>
</table>
<button type="button" class="btn btn-primary" onclick="Joomla.submitbutton('acceptModifiedFiles')"><?php echo JText::_('COM_RSFIREWALL_ACCEPT_CHANGES'); ?></button>
