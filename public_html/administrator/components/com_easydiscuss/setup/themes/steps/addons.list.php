<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<script type="text/javascript">
$('[data-select-all]').on('change', function() {

	var parent = $(this).parents('[data-tab]');
	var checkbox = parent.find('[data-checkbox]');
	var selected = $(this).is(':checked');

	checkbox.prop('checked', selected);
});
</script>
<ul class="tabs row-table list-reset">
	<li class="col-cell active">
		<a href="#modules" data-toggle="tab">
			<b><?php echo ($data->modules) ? count($data->modules) : '0';?></b>
			<?php echo JText::_('COM_EASYDISCUSS_SETUP_ADDONS_MODULES');?>
		</a>
	</li>
	<li class="col-cell">
		<a href="#plugins" data-toggle="tab">
			<b><?php echo count($data->plugins);?></b>
			<?php echo JText::_('COM_EASYDISCUSS_SETUP_ADDONS_PLUGINS');?>
		</a>
	</li>
</ul>

<div class="tab-content">
	<div class="tab-pane in active" id="modules" data-tab>
		<ul class="modules-list list-reset">
			<li>
				<div class="checkbox check-all">
					<input type="checkbox" id="module-all" data-select-all checked="checked" />
					<label for="module-all">
						<div><?php echo JText::_('COM_EASYDISCUSS_SETUP_ADDONS_SELECT_ALL');?></div>
					</label>
				</div>
			</li>

			<?php if ($data->modules) { ?>
				<?php foreach ($data->modules as $module) { ?>
				<li>
					<div class="checkbox">
						<input type="checkbox" id="module-<?php echo $module->element; ?>" value="<?php echo $module->element;?>" checked="checked" data-checkbox data-checkbox-module />
						<label for="module-<?php echo $module->element; ?>">
							<b><?php echo $module->title;?></b>
							<div>
								<span class="text-muted"><?php echo JText::_('COM_EASYDISCUSS_SETUP_ADDONS_VERSION');?>: <?php echo $module->version;?></span>
							</div>
							<div><?php echo $module->description;?></div>
						</label>
					</div>
				</li>
				<?php } ?>
			<?php } ?>
		</ul>
	</div>

	<div class="tab-pane" id="plugins">
		<ul class="modules-list list-reset" data-tab>
			<li>
				<div class="checkbox check-all">
					<input type="checkbox" id="plugin-all" data-select-all checked="checked" />
					<label for="plugin-all">
						<div><?php echo JText::_('COM_EASYDISCUSS_SETUP_ADDONS_SELECT_ALL');?></div>
					</label>
				</div>
			</li>

			<?php foreach ($data->plugins as $plugin) { ?>
			<li>
				<div class="checkbox">
					<input type="checkbox" id="plugin-<?php echo $plugin->element; ?>" value="<?php echo $plugin->element;?>" data-group="<?php echo $plugin->group;?>" checked="checked" data-checkbox data-checkbox-plugin />
					<label for="plugin-<?php echo $plugin->element; ?>">
						<b><?php echo $plugin->title;?></b>
						<div><?php echo $plugin->description;?></div>
					</label>
				</div>
			</li>
			<?php } ?>
		</ul>
	</div>
</div>
