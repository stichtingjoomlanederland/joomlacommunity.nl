<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2014 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access'); ?>

<table width="100%" class="com-rsform-table-props">
	<tr>
		<td width="25%" nowrap="nowrap" align="right" class="key"><?php echo JText::_('JSTATUS'); ?></td>
		<td>
			<?php if ($this->getStatus($this->directory->formId)) { ?>
				<span class="badge badge-success"><?php echo JText::_('RSFP_SUBM_DIR_ENABLED'); ?></span>
			<?php } else { ?>
				<span class="badge badge-important"><?php echo JText::_('RSFP_SUBM_DIR_DISABLED'); ?></span><p><small><?php echo JText::_('RSFP_SUBM_DIR_DISABLED_INSTRUCTIONS'); ?></small></p>
			<?php } ?>
		</td>
	</tr>
	<tr>
		<td width="25%" nowrap="nowrap" align="right" class="key"><?php echo JText::_('RSFP_SUBM_DIR_CAN_EDIT'); ?></td>
		<td>
			<div class="control-group">
				<div class="controls">
					<label for="own" class="checkbox">
						<input type="checkbox" id="own" value="own" name="jform[groups][]" <?php if (in_array('own',$this->directory->groups)) echo 'checked="checked"'; ?> />
						<?php echo JText::_('RSFP_SUBM_DIR_EDIT_OWN_SUBMISSIONS'); ?>
					</label>
				</div>
			</div>
			<?php echo JHtml::_('access.usergroups', 'jform[groups]', $this->directory->groups, true); ?>
		</td>
	</tr>
	<tr>
		<td width="25%" nowrap="nowrap" align="right" class="key"><?php echo JText::_('RSFP_SUBM_DIR_ENABLE_PDF_SUPPORT'); ?></td>
		<td>
			<?php echo $this->lists['enablepdf']; ?>
		</td>
	</tr>
	<tr>
		<td width="25%" nowrap="nowrap" align="right" class="key"><?php echo JText::_('RSFP_SUBM_DIR_PDF_FILENAME'); ?></td>
		<td>
			<input type="text" class="input-xxlarge" name="jform[filename]" value="<?php echo $this->escape($this->directory->filename); ?>" />
		</td>
	</tr>
	<tr>
		<td width="25%" nowrap="nowrap" align="right" class="key"><?php echo JText::_('RSFP_SUBM_DIR_ENABLE_CSV_SUPPORT'); ?></td>
		<td>
			<?php echo $this->lists['enablecsv']; ?>
		</td>
	</tr>
</table>