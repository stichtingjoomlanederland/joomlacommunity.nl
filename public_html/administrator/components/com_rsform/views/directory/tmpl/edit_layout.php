<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2014 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access'); ?>

<fieldset>
	<h3 class="rsfp-legend"><?php echo JText::_('RSFP_XHTML_LAYOUTS'); ?></h3>
	<?php $xhtmlLayouts = array('dir-inline', 'dir-2lines', 'dir-inline-title', 'dir-2lines-title', 'dir-2cols'); ?>
	<?php foreach ($xhtmlLayouts as $layout) { ?>
	<div class="rsform_layout_box">
		<label for="formLayout<?php echo ucfirst($layout); ?>" class="radio">
			<input type="radio" id="formLayout<?php echo ucfirst($layout); ?>" name="jform[ViewLayoutName]" value="<?php echo $layout; ?>" onclick="saveDirectoryLayoutName('<?php echo $this->formId; ?>', this.value);" <?php if ($this->directory->ViewLayoutName == $layout) { ?>checked="checked"<?php } ?> /> <?php echo JText::_('RSFP_LAYOUT_'.str_replace('-', '_', $layout));?><br/>
		</label>
		<?php echo JHtml::image('com_rsform/admin/layouts/' . $layout . '.gif', JText::_('RSFP_LAYOUT_'.str_replace('-', '_', $layout)), 'width="175"', true); ?>
	</div>
	<?php } ?>
	<span class="rsform_clear_both"></span>
</fieldset>

<fieldset>
	<h3 class="rsfp-legend"><?php echo JText::_('RSFP_SUBM_DIR_DETAILS_LAYOUT'); ?></h3>
	<table border="0">
		<tr>
			<td><button class="pull-left btn btn-warning" type="button" onclick="generateDirectoryLayout('<?php echo $this->formId; ?>');"><?php echo JText::_('RSFP_GENERATE_LAYOUT'); ?></button></td>
			<td><?php echo JText::_('RSFP_AUTOGENERATE_LAYOUT');?></td>
			<td><?php echo $this->lists['ViewLayoutAutogenerate']; ?></td>
		</tr>
	</table>
	<table width="100%">
		<tr>
			<td valign="top">
			   <table width="98%" style="clear:both;">
					<tr>
						<td>
							<?php echo RSFormProHelper::showEditor('jform[ViewLayout]', $this->directory->ViewLayout, array('classes' => 'rs_100', 'id' => 'ViewLayout', 'syntax' => 'html', 'readonly' => $this->directory->ViewLayoutAutogenerate)); ?>
						</td>
					</tr>
				</table>
			</td>
			<td valign="top" width="1%" nowrap="nowrap">
				<button class="btn" type="button" onclick="toggleQuickAddDirectory();"><?php echo JText::_('RSFP_TOGGLE_QUICKADD'); ?></button>
				<span class="rsform_clear_both"></span>
				<div id="QuickAdd1">
					<h3><?php echo JText::_('RSFP_QUICK_ADD');?></h3>
					<?php echo JText::_('RSFP_QUICK_ADD_DESC');?><br/><br/>
					<?php foreach($this->quickfields as $field) {
						echo RSFormProHelper::generateQuickAdd($field, 'display');
					}?>
				</div>
			</td>
		</tr>
	</table>
</fieldset>