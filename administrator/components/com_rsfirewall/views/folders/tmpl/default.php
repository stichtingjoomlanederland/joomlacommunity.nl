<?php
/**
 * @package    RSFirewall!
 * @copyright  (c) 2009 - 2016 RSJoomla!
 * @link       https://www.rsjoomla.com
 * @license    GNU General Public License http://www.gnu.org/licenses/gpl-3.0.en.html
 */

defined('_JEXEC') or die('Restricted access');

// current name we're looking for
$name = $this->name ? '&name='.$this->name : '';
// settings
$allowfolders 	= $this->allowFolders ? '&allowfolders=1' : '';
$allowfiles 	= $this->allowFiles ? '&allowfiles=1' : '';
?>

<script type="text/javascript">
function addFile() {
	<?php if ($this->name) { ?>
	if (window.opener) {
		var textbox = window.opener.document.getElementsByName('jform[<?php echo addslashes($this->escape($this->name)); ?>]')[0];
		
		for (var i=0 ;i<document.getElementsByName('cid[]').length; i++) {
			if (document.getElementsByName('cid[]')[i].checked) {
				var file = document.getElementsByName('cid[]')[i].value;
				
				if (textbox.value.length > 0) {
					textbox.value += '\n' + file;
				} else  {
					textbox.value = file;
				}
			}
		}
	}
	<?php } ?>
}
</script>

<div id="com-rsfirewall-explorer">
	<p>
		<button onclick="addFile();" class="btn btn-primary"><?php echo JText::_('COM_RSFIREWALL_ADD_SELECTED_ITEMS'); ?></button>
		<button onclick="window.close();" class="btn btn-secondary"><?php echo JText::_('COM_RSFIREWALL_CLOSE_FILE_MANAGER'); ?></button>
	</p>
	<div id="com-rsfirewall-explorer-header">
		<strong><?php echo JText::_('COM_RSFIREWALL_CURRENT_LOCATION'); ?></strong>
		<?php foreach ($this->elements as $element) { ?>
			<a href="<?php echo JRoute::_('index.php?option=com_rsfirewall&view=folders&tmpl=component'.$name.$allowfolders.$allowfiles.'&folder='.urlencode($element->fullpath)); ?>"><?php echo $this->escape($element->name); ?></a> <?php echo $this->DS; ?>
		<?php } ?>
	</div>
	<br/>
	<table class="com-rsfirewall-striped table-striped">
		<tr>
			<?php if ($this->allowFolders) { ?>
				<th><?php echo JText::_('COM_RSFIREWALL_ADD_SELECT'); ?></th>
			<?php } ?>
			<th><?php echo JText::_('COM_RSFIREWALL_FOLDERS_OR_FILES'); ?></th>
			<th><?php echo JText::_('COM_RSFIREWALL_PERMISSIONS'); ?></th>
			<th><?php echo JText::_('COM_RSFIREWALL_SIZE'); ?></th>
		</tr>
		<?php if ($this->previous) { ?>
		<tr>
			<?php if ($this->allowFolders) { ?>
				<td><input type="checkbox" disabled="disabled" /></td>
			<?php } ?>
			<td>
				<span class="com-rsfirewall-icon-16-folder"></span>
				<a href="<?php echo JRoute::_('index.php?option=com_rsfirewall&view=folders&tmpl=component'.$name.$allowfolders.$allowfiles.'&folder='.urlencode($this->previous)); ?>">..</a>
			</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
		</tr>
		<?php } ?>
		<?php foreach ($this->folders as $folder => $data) { ?>
			<?php $fullpath = $this->path.$this->DS.$folder; ?>
			<tr>
				<?php if ($this->allowFolders) { ?>
					<Td><input type="checkbox" name="cid[]" value="<?php echo $this->escape($fullpath); ?>" /></td>
				<?php } ?>
				<td>
					<span class="com-rsfirewall-icon-16-folder"></span>
					<a href="<?php echo JRoute::_('index.php?option=com_rsfirewall&view=folders&tmpl=component'.$allowfolders.$allowfiles.$name.'&folder='.urlencode($fullpath)); ?>"><?php echo $this->escape($folder); ?></a>
				</td>
				<td><?php echo $data['octal']?> (<?php echo $data['full']?>)</td>
				<td>&nbsp;</td>
			</tr>
		<?php } ?>
		
		<?php
		$i = 0;
		foreach ($this->files as $file => $data) { ?>
			<?php $fullpath = $this->path.$this->DS.$file; ?>
			<tr>
				<?php if ($this->allowFiles) { ?>
					<Td><input type="checkbox" id="file<?php echo $i; ?>" name="cid[]" value="<?php echo $this->escape($fullpath); ?>" /></td>
				<?php } ?>
				<td>
					<span class="com-rsfirewall-icon-16-file"></span>
					<label for="file<?php echo $i; ?>"><?php echo $this->escape($file); ?></label>
				</td>
				<td><?php echo $data['octal']?> (<?php echo $data['full']?>)</td>
				<td><?php echo $data['filesize']?></td>
			</tr>
		<?php 
			$i++;
		} 
		?>
	</table>
	<p><button onclick="addFile();" class="btn btn-primary"><?php echo JText::_('COM_RSFIREWALL_ADD_SELECTED_ITEMS'); ?></button></p>
</div>