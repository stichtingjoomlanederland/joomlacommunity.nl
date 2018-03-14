<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2014 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');
$count = 0;
?>
<?php foreach ($this->fields as $fieldType => $fields) { ?>
	<?php if ($fields) { ?>
		<div class="alert alert-error" id="rsformerror<?php echo $count++; ?>" style="display:none;"></div>
		<div class="form-horizontal">
		<?php foreach ($fields as $field) { ?>
			<?php if (strpos($field->type, 'hidden') !== false) { ?>
				<?php echo $field->body; ?>
				<?php continue; ?>
			<?php } ?>
			<div class="control-group" id="id<?php echo $field->name; ?>" <?php echo ($field->name == 'VALIDATIONMULTIPLE' ? ' style="display:none"' : ''); ?>>
				<div class="control-label">
					<?php echo $field->label; ?>
				</div>
				<div class="controls">
					<?php echo $field->body; ?>
					<?php if ($field->translatable) { ?>
						<span class="help-inline"><?php echo RSFormProHelper::translateIcon(); ?></span>
					<?php } ?>
				</div>
			</div>
		<?php } ?>
		</div>
	<?php } ?>
	<?php if ($fieldType != 'attributes') { ?>{rsfsep}<?php } ?>
<?php } ?>