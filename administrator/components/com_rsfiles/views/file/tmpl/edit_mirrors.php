<?php
/**
* @package RSFiles!
* @copyright (C) 2010-2014 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined('_JEXEC') or die('Restricted access'); 
JText::script('COM_RSFILES_SAVE');
JText::script('COM_RSFILES_CANCEL'); ?>

<?php if ($this->item->IdFile) { ?>
<div class="well">
	<input type="text" id="mname" name="mname" value="" placeholder="<?php echo JText::_('COM_RSFILES_MIRROR_NAME'); ?>" class="input-large <?php echo rsfilesHelper::tooltipClass(); ?>" size="20" title="<?php echo rsfilesHelper::tooltipText(JText::_('COM_RSFILES_MIRROR_NAME')); ?>" /> 
	<input type="text" id="murl" name="murl" value="" placeholder="<?php echo JText::_('COM_RSFILES_MIRROR_URL'); ?>" class="input-xxlarge <?php echo rsfilesHelper::tooltipClass(); ?>" size="50" title="<?php echo rsfilesHelper::tooltipText(JText::_('COM_RSFILES_MIRROR_URL')); ?>" /> 
	<button type="button" class="btn btn-primary button" onclick="rsf_mirror('<?php echo JURI::root(); ?>');"><?php echo JText::_('COM_RSFILES_ADD'); ?></button>
</div>

<table class="adminlist table table-striped">
	<thead>
		<tr>
			<th width="30%"><?php echo JText::_('COM_RSFILES_MIRROR_NAME'); ?></th>
			<th><?php echo JText::_('COM_RSFILES_MIRROR_URL'); ?></th>
			<th width="10%" class="center" align="center"><?php echo JText::_('COM_RSFILES_MIRROR_ACTIONS'); ?></th>
		</tr>
	</thead>
	<tbody id="file_mirror">
	<?php if (!empty($this->mirrors)) { ?>
	<?php foreach ($this->mirrors as $i => $mirror) { ?>
		<tr class="row<?php echo $i % 2; ?>" id="mirror<?php echo $mirror->IdMirror; ?>">
			<td><span id="sname<?php echo $mirror->IdMirror; ?>"><?php echo $mirror->MirrorName; ?></span></td>
			<td><span id="surl<?php echo $mirror->IdMirror; ?>"><?php echo $mirror->MirrorURL; ?></span></td>
			<td class="center" align="center">
				<span id="actions<?php echo $mirror->IdMirror; ?>">
					<a href="javascript:void(0)" onclick="rsf_edit_mirror('<?php echo JURI::root(); ?>',<?php echo $mirror->IdMirror; ?>)">
						<img src="<?php echo JURI::root(); ?>/administrator/components/com_rsfiles/assets/images/icons/edit.png" alt="" />
					</a> 
					<a href="javascript:void(0)" onclick="rsf_delete_mirror(<?php echo $mirror->IdMirror; ?>)">
						<img src="<?php echo JURI::root(); ?>/administrator/components/com_rsfiles/assets/images/icons/delete.png" alt="" />
					</a>
				</span>
			</td>
		</tr>
	<?php } ?>
	<?php } ?>
	</tbody>
</table>
<?php } else { ?>
<div class="well"><div style="text-align: center; font-weight: bold;"><?php echo JText::_('COM_RSFILES_PLEASE_SAVE_THE_FILE_FIRST'); ?></div></div>
<?php } ?>