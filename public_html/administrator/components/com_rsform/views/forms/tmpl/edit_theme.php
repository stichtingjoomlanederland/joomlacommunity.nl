<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2014 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');
?>
<div class="alert alert-error" id="rsform_themes_disabled" <?php if ($this->form->FormLayoutName != 'responsive') { ?>style="display: none;"<?php } ?>>
<?php echo JText::_('RSFP_THEMES_DISABLED'); ?>
</div>
<table class="adminlist table table-striped">
<thead>
	<tr>
		<th width="5" class="title"><?php echo JText::_('#'); ?></th>
		<th class="title"><?php echo JText::_('RSFP_NAME'); ?></th>
	</tr>
	</thead>
	<tbody>
	<?php
	$k = 0;
	$i = 0;
	foreach ($this->themes as $theme) { ?>
	<tr class="row<?php echo $k; ?>">
		<td width="5">
			<input type="radio" id="theme<?php echo $i; ?>" <?php if ($this->form->FormLayoutName == 'responsive') { ?>disabled="disabled"<?php } ?> name="ThemeName" value="<?php echo $this->escape($theme->directory); ?>" <?php echo $this->form->ThemeParams->get('name') == $theme->directory ? 'checked="checked"' : ''; ?> />
			<?php if (isset($theme->css)) foreach ($theme->css as $css) { ?>
				<input type="hidden" name="ThemeCSS[<?php echo $this->escape($theme->directory); ?>][]" value="<?php echo $this->escape($css); ?>" />
			<?php } ?>
			<?php if (isset($theme->js)) foreach ($theme->js as $js) { ?>
				<input type="hidden" name="ThemeJS[<?php echo $this->escape($theme->directory); ?>][]" value="<?php echo $this->escape($js); ?>" />
			<?php } ?>
		</td>
		<td>
		<label for="theme<?php echo $i; ?>"><?php echo $this->escape($theme->name);?></label>
		<?php if ($theme->img_path) { ?>
		<img src="<?php echo $this->escape($theme->img_path); ?>" />
		<?php } ?>
		</td>
	</tr>
	<?php $i++; ?>
	<?php } ?>
	</tbody>
</table>