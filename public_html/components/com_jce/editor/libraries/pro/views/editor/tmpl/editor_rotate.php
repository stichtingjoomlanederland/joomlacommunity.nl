<?php

/**
 * @copyright 	Copyright (c) 2009-2017 Ryan Demmer. All rights reserved
 * @license   	GNU/GPL 2 or later - http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * JCE is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses
 */
defined('_JEXEC') or die('RESTRICTED');
?>
<h3 id="transform-rotate" data-action="rotate">
<a href="#">
<?php echo WFText::_('WF_MANAGER_TRANSFORM_ROTATE', 'Rotate'); ?>
</a>
</h3>
<div class="uk-form">
		<div class="uk-grid uk-grid-mini">
			<label for="rotate-angle" class="uk-width-2-10"><?php echo WFText::_('WF_MANAGER_TRANSFORM_ROTATE', 'Rotate'); ?></label>
			<div class="uk-width-4-10">
				<a role="button" class="uk-button uk-width-1-1" id="rotate-angle-clockwise"><i class="uk-icon uk-icon-repeat"></i>&nbsp;<?php echo WFText::_('WF_MANAGER_TRANSFORM_ROTATE_RIGHT', 'Right'); ?></a>
			</div>
			<div class="uk-width-4-10">
				<a role="button" class="uk-button uk-width-1-1" id="rotate-angle-anticlockwise"><i class="uk-icon uk-icon-undo"></i>&nbsp;<?php echo WFText::_('WF_MANAGER_TRANSFORM_ROTATE_LEFT', 'Left'); ?></a>
			</div>
		</div>
		<div class="uk-grid uk-grid-mini">
			<label for="rotate-flip" class="uk-width-2-10"><?php echo WFText::_('WF_MANAGER_TRANSFORM_FLIP', 'Flip'); ?></label>
			<div class="uk-width-4-10">
				<a role="button" class="uk-button uk-width-1-1" id="rotate-flip-vertical"><i class="uk-icon uk-icon-arrows-v"></i>&nbsp;<?php echo WFText::_('WF_MANAGER_TRANSFORM_FLIP_VERTICAL', 'Vertical'); ?></a>
			</div>
			<div class="uk-width-4-10">
				<a role="button" class="uk-button uk-width-1-1" id="rotate-flip-horizontal"><i class="uk-icon uk-icon-arrows-h"></i>&nbsp;<?php echo WFText::_('WF_MANAGER_TRANSFORM_FLIP_HORIZONTAL', 'Horizontal'); ?></a>
			</div>
		</div>
</div>
