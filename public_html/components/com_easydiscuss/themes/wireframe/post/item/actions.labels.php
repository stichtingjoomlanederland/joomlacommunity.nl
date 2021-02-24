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
?>
<div class="o-dropdown ed-entry-actions-group" role="group">
	<a class="o-btn" data-ed-toggle="dropdown">
		<?php echo JText::_('COM_ED_POST_LABEL'); ?> &nbsp;<i class="fa fa-caret-down"></i>
	</a>

	<ul class="o-dropdown-menu o-dropdown-menu-right ed-adminbar__dropdown-menu-tools has-active-markers" data-ed-assign-labels-list>
		<?php foreach ($labels as $label) { ?>
			<li class="t-text--truncate <?php echo $post->getCurrentLabel() && $post->getCurrentLabel()->id == $label->id ? 'active' : '';?>" data-ed-labels>
				<a href="javascript:void(0);" 
					class="o-dropdown__item t-text--truncate" 
					data-ed-assign-label 
					data-label-id="<?php echo $label->id; ?>" 
					data-label-title="<?php echo $label->title; ?>"
				>
					<?php echo $label->getTitle(); ?>
				</a>
			</li>
		<?php } ?>

		<li class="<?php echo $post->hasLabel() && $post->canLabel(true) ? '' : 't-d--none';?>" data-ed-assign-label-divider><hr class="o-dropdown-divider" /></li>
		<li>
			<a href="javascript:void(0);" class="o-dropdown__item <?php echo $post->hasLabel() && $post->canLabel(true) ? '' : 't-d--none';?>" data-ed-assign-label data-label-id="0" data-label-title="">
				<?php echo JText::_('COM_ED_POST_REMOVE_CURRENT_LABEL'); ?>
			</a>
		</li>
	</ul>
</div>