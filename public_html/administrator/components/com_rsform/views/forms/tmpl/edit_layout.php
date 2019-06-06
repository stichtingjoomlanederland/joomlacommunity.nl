<?php
/**
 * @package RSForm! Pro
 * @copyright (C) 2007-2019 www.rsjoomla.com
 * @license GPL, http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die('Restricted access');
?>
<?php foreach ($this->layouts as $layoutGroup => $layouts) { ?>
<fieldset>
	<h3 class="rsfp-legend"><?php echo JText::_('RSFP_' . $layoutGroup); ?></h3>
	<?php foreach ($layouts as $layout) { ?>
		<div class="rsform_layout_box">
			<label for="formLayout<?php echo ucfirst($layout); ?>" class="radio">
				<input type="radio" id="formLayout<?php echo ucfirst($layout); ?>" name="FormLayoutName" value="<?php echo $layout; ?>" onclick="saveLayoutName('<?php echo $this->form->FormId; ?>', this.value);" <?php if ($this->form->FormLayoutName == $layout) { ?>checked="checked"<?php } ?> /><?php echo JText::_('RSFP_LAYOUT_'.str_replace('-', '_', $layout));?><br/>
			</label>
			<?php echo JHtml::image('com_rsform/admin/layouts/' . $layout . '.gif', JText::_('RSFP_LAYOUT_'.str_replace('-', '_', $layout)), 'width="175"', true); ?><br/>
		</div>
	<?php } ?>
	<span class="rsform_clear_both"></span>
</fieldset>
<?php } ?>

<fieldset>
	<h3 class="rsfp-legend"><?php echo JText::_('RSFP_FORM_HTML_LAYOUT_OPTIONS'); ?></h3>
	<span class="rsform_clear_both"></span>
	<table border="0">
		<tr>
			<td><label><?php echo JText::_('RSFP_LOAD_LAYOUT_FRAMEWORK'); ?></label></td>
			<td><?php echo $this->renderHTML('select.booleanlist', 'LoadFormLayoutFramework', '', $this->form->LoadFormLayoutFramework); ?></td>
		</tr>
        <tr>
            <td><label><?php echo JText::_('RSFP_FORM_LAYOUT_FLOW');?></label></td>
            <td><?php echo $this->lists['FormLayoutFlow']; ?></td>
        </tr>
		<tr>
			<td><label><?php echo JText::_('RSFP_AUTOGENERATE_LAYOUT');?></label></td>
			<td><?php echo $this->lists['FormLayoutAutogenerate']; ?></td>
		</tr>
	</table>
</fieldset>

<fieldset>
	<h3 class="rsfp-legend"><?php echo JText::_('RSFP_FORM_HTML_LAYOUT'); ?></h3>
	<button class="btn btn-warning" type="button" onclick="generateLayout('<?php echo $this->form->FormId; ?>', true);"><?php echo JText::_('RSFP_GENERATE_LAYOUT'); ?></button>
	<table width="100%">
		<tr>
			<td valign="top">
				<table width="98%" style="clear:both;">
					<tr>
						<td>
							<?php echo RSFormProHelper::showEditor('FormLayout', $this->form->FormLayout, array('classes' => 'rs_100', 'id' => 'formLayout', 'syntax' => 'html', 'readonly' => $this->form->FormLayoutAutogenerate)); ?>
						</td>
					</tr>
				</table>
			</td>
			<td valign="top" width="1%" nowrap="nowrap">
				<button class="btn" type="button" onclick="toggleQuickAdd();"><?php echo JText::_('RSFP_TOGGLE_QUICKADD'); ?></button>
				<span class="rsform_clear_both"></span>
				<div id="QuickAdd1">
					<h3><?php echo JText::_('RSFP_QUICK_ADD');?></h3>
					<?php echo JText::_('RSFP_QUICK_ADD_DESC');?><br/><br/>
					<?php foreach($this->quickfields as $field) {
						echo RSFormProHelper::generateQuickAdd($field, 'generate');
					}?>
				</div>
			</td>
		</tr>
	</table>
</fieldset>