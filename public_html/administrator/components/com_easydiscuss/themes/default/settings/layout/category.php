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
<div class="row">
	<div class="col-md-6">
		<div class="panel">
			<?php echo $this->html('panel.head', 'COM_ED_CATEGORY_LAYOUT_SETTINGS'); ?>

			<div class="panel-body">
				<div class="o-form-horizontal">
					<?php echo $this->html('settings.toggle', 'layout_category_icons', 'COM_ED_CATEGORY_USE_ICONS'); ?>
	
					<?php echo $this->html('settings.textbox', 'main_categoryavatarpath', 'COM_EASYDISCUSS_CATEGORY_PATH', '', array('defaultValue' => 'images/discuss_cavatar/')); ?>

					<?php echo $this->html('settings.dropdown', 'layout_ordering_category', 'COM_EASYDISCUSS_LAYOUT_CATEGORY_ORDERING', '',
						array(
							'alphabet' => 'COM_EASYDISCUSS_SORT_ALPHABETICAL',
							'latest' => 'COM_EASYDISCUSS_SORT_LATEST',
							'ordering' => 'COM_EASYDISCUSS_SORT_ORDERING'
						)
					); ?>

					<?php echo $this->html('settings.dropdown', 'layout_sort_category', 'COM_EASYDISCUSS_LAYOUT_CATEGORY_SORTING', '',
						array(
							'asc' => 'COM_EASYDISCUSS_SORT_ASC',
							'desc' => 'COM_EASYDISCUSS_SORT_DESC'
						)
					); ?>

					<?php echo $this->html('settings.textbox', 'layout_single_category_post_limit', 'COM_EASYDISCUSS_SINGLE_CATEGORY_POST_LIMIT', '', array('size' => 7, 'postfix' => 'Posts'), '', '', 'text-center'); ?>
				</div>
			</div>
		</div>
	</div>
	<div class="col-md-6">
	</div>
</div>
