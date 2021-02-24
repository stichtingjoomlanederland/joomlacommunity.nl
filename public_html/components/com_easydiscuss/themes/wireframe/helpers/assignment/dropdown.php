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
<div class="t-d--flex" data-ed-assignments data-id="<?php echo $post->id;?>" data-category-id="<?php echo $post->category_id;?>">
	<div class="t-mr--sm">
		<?php echo JText::_('COM_ED_MODERATOR');?>: 
	</div>

	<div class="o-dropdown">
		<a href="javascript:void(0);" class="si-link sm:t-d--block" data-ed-toggle="dropdown" data-moderator-selection>
			 <div class="o-media" data-assignee>
			 	<?php echo $this->output('site/helpers/assignment/assignee', ['moderator' => $moderator]); ?>
			</div>
		</a>

		<ul class="o-dropdown-menu t-mt--2xs sm:t-w--100" data-moderators>
			<li class="is-loading" data-loader>
				<?php echo $this->html('loading.block'); ?>
			</li>
		</ul>
	</div>
</div>