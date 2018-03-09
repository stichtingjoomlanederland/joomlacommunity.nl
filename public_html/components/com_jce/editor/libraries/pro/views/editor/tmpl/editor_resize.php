<?php

/**
 * @copyright     Copyright (c) 2009-2017 Ryan Demmer. All rights reserved
 * @license       GNU/GPL 2 or later - http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * JCE is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses
 */
defined('_JEXEC') or die('RESTRICTED');
?>
<h3 id="transform-resize" data-action="resize">
	<a href="#">
		<?php echo WFText::_('WF_MANAGER_TRANSFORM_RESIZE', 'Resize'); ?>
	</a>
</h3>
<div class="uk-form">
	<div class="uk-grid uk-grid-small uk-form-constrain">
		<label for="resize_width" class="uk-width-1-5">
        	<?php echo WFText::_('WF_LABEL_WIDTH'); ?>
        </label>

		<div class="uk-width-4-5">
			<div class="uk-form-controls uk-width-1-4">
				<input type="text" id="resize_width" value="" class="uk-text-center" />
			</div>
			
			<label class="uk-float-left uk-text-bold uk-width-1-10 uk-text-center">&times;</label>

			<label for="resize_height" class="uk-float-left uk-width-1-4 uk-margin-small-left">
				<?php echo WFText::_('WF_LABEL_HEIGHT'); ?>
			</label>

			<div class="uk-form-controls uk-width-1-4 uk-margin-small-left">
				<input type="text" id="resize_height" value="" class="uk-text-center" />
			</div>
		</div>

		<label>
            <input id="resize_constrain" class="uk-constrain-checkbox uk-margin-left-remove" type="checkbox" checked />
            <?php echo WFText::_('WF_LABEL_PROPORTIONAL'); ?>
        </label>
	</div>
	<div class="uk-grid uk-grid-small">
		<label for="resize_presets" class="uk-width-1-5">
			<?php echo WFText::_('WF_LABEL_PRESETS'); ?>
		</label>
		<div class="uk-width-4-5">
			<select id="resize_presets" class="uk-width-1-1">
				<option value="1:1"><?php echo WFText::_('WF_MANAGER_TRANSFORM_PRESET_ORIGINAL'); ?></option>
				<?php foreach ($this->lists['resize'] as $option): ?>
					<option value="<?php echo $option; ?>"><?php echo $option; ?></option>
				<?php endforeach; ?>
			</select>
		</div>
	</div>
	<div class="uk-form-row uk-align-right uk-margin-top uk-margin-bottom">
		<a id="resize_apply" role="button" class="uk-button apply" data-function="resize">
			<?php echo WFText::_('WF_LABEL_APPLY'); ?>
		</a>
		<a id="resize_reset" role="button" class="uk-button reset" data-function="resize">
			<?php echo WFText::_('WF_LABEL_RESET'); ?>
		</a>
	</div>
</div>