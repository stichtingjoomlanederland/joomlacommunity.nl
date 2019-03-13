<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 - 2018 Stack Ideas Sdn Bhd. All rights reserved.
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
<script type="text/javascript">
$('[data-select-all]').on('change', function() {

	var parent = $(this).parents('[data-tab]');
	var checkbox = parent.find('[data-checkbox]').not(":disabled");
	var selected = $(this).is(':checked');

	checkbox.prop('checked', selected);
});

$('[data-checkbox-module]').on('click', function() {
	var selected = $(this).is(':checked');
	if (! selected) {
		$('#module-all').prop('checked', false);
	} else {
		// find if there is any unchecked item or not.
		var parent = $(this).parents('[data-tab]');
		var unchecked = parent.find('[data-checkbox-module]').not(":checked");

		if (unchecked.length == 0) {
			$('#module-all').prop('checked', true);
		}
	}
});

$('[data-checkbox-plugin]').on('click', function() {
	var selected = $(this).is(':checked');
	if (! selected) {
		$('#plugin-all').prop('checked', false);
	} else {
		// find if there is any unchecked item or not.
		var parent = $(this).parents('[data-tab]');
		var unchecked = parent.find('[data-checkbox-plugin]').not(":checked");

		if (unchecked.length == 0) {
			$('#plugin-all').prop('checked', true);
		}
	}
});

</script>

<div id="modules" class="addons-list" data-tab>
	<ul class="list-reset">
		<li>
			<div class="checkbox">
				<input type="checkbox" id="maintenance" disabled />
				<label for="maintenance">
					<div>Run maintenance script</div>
				</label>
			</div>
		</li>
	</ul>
	<ul class="list-reset">

		<li>
			<div class="checkbox check-all">
				<input type="checkbox" id="module-all" data-select-all checked="checked" />
				<label for="module-all">
					<div>Install modules and plugins</div>
				</label>
			</div>
		</li>

		<li>
			<ul class="list-reset">

				<?php foreach ($data->modules as $module) { ?>
				<li>
					<div class="checkbox">
						<input type="checkbox" id="module-<?php echo $module->element; ?>" value="<?php echo $module->element;?>" <?php echo $module->checked ? 'checked="checked"' : '' ?> data-checkbox data-checkbox-module <?php echo $module->disabled ? 'disabled':''; ?> />
						<label for="module-<?php echo $module->element; ?>">
							<?php echo $module->title;?>
						</label>
					</div>
				</li>
					<?php if (!$module->checked) { ?>
						<?php $unchecked = true; ?>
					<?php } ?>
				<?php } ?>

				<?php foreach ($data->plugins as $plugin) { ?>
				<li>
					<div class="checkbox">
						<input type="checkbox" id="plugin-<?php echo $plugin->group . '-' . $plugin->element; ?>" value="<?php echo $plugin->element;?>" data-group="<?php echo $plugin->group;?>" checked="checked" data-checkbox data-checkbox-plugin <?php echo $plugin->disabled ? 'disabled':''; ?>/>
						<label for="plugin-<?php echo $plugin->group . '-' . $plugin->element; ?>">
							<?php echo $plugin->title;?>
						</label>
					</div>
				</li>
				<?php } ?>

			</ul>
		</li>
	</ul>
</div>

<?php if ($unchecked) { ?>
<script type="text/javascript">
$('[data-select-all]').prop('checked', false);
</script>
<?php } ?>