<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

$unchecked = false;
?>
<div id="modules" class="addons-list" data-tab>
	<div class="mb-3">
		<div class="custom-control custom-checkbox">
			<input type="checkbox" class="custom-control-input" id="maintenance" checked="checked" disabled />
			<label class="custom-control-label" for="maintenance">Run Maintenance Scripts (Required)</label>
		</div>

		<div class="custom-control custom-checkbox mt-3">
			<input type="checkbox" class="custom-control-input" id="modulesnplugins" checked="checked" data-select-all />
			<label class="custom-control-label" for="modulesnplugins">Install Modules &amp; Plugins (Optional)</label>
		</div>
	</div>


	<?php if ($data->modules) { ?>				
	<div class="si-card si-container-overflow mb-4 p-3" style="max-height:30vh">
		<div>
			<h3 class="mt-1 mb-2">Modules</h3>
			<?php foreach ($data->modules as $module) { ?>
				<div class="custom-control custom-checkbox mb-1">
					<input type="checkbox" id="module-<?php echo $module->element; ?>" 
						class="custom-control-input" 
						value="<?php echo $module->element;?>" 
						<?php echo $module->checked ? 'checked="checked"' : '' ?> 
						data-checkbox 
						data-checkbox-module 
						<?php echo $module->disabled ? 'disabled':''; ?> 
					/>
					<label class="custom-control-label" for="module-<?php echo $module->element; ?>">
						<?php echo $module->title;?>
					</label>
				</div>

				<?php if (!$module->checked) { ?>
					<?php $unchecked = true; ?>
				<?php } ?>
			<?php } ?>

			<h3 class="mt-4 mb-2">Plugins</h3>
			<?php foreach ($data->plugins as $plugin) { ?>
				<div class="custom-control custom-checkbox mb-1">
					<input type="checkbox" id="plugin-<?php echo $plugin->group . '-' . $plugin->element; ?>" 
						class="custom-control-input" 
						value="<?php echo $plugin->element;?>" 
						data-group="<?php echo $plugin->group;?>"
						checked="checked"
						data-checkbox 
						data-checkbox-plugin 
						<?php echo $plugin->disabled ? 'disabled':''; ?> 
					/>
					<label class="custom-control-label" for="plugin-<?php echo $plugin->group . '-' . $plugin->element; ?>">
						<?php echo $plugin->title;?> <?php echo $plugin->disabled ? '(Required)' : '';?>
					</label>
				</div>
			<?php } ?>
		</div>
	</div>
	<?php } ?>
</div>

<script>
<?php if ($unchecked) { ?>
$('[data-select-all]').prop('checked', false);
<?php } ?>

$('[data-select-all]').on('change', function() {

	var parent = $(this).parents('[data-tab]');
	var checkbox = parent.find('[data-checkbox]').not(":disabled");
	var selected = $(this).is(':checked');

	checkbox.prop('checked', selected);
});
</script>