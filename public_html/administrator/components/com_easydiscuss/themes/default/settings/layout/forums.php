<?php
/**
* @package      EasyDiscuss
* @copyright    Copyright (C) 2010 - 2018 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
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
			<?php echo $this->html('panel.head', 'COM_EASYDISCUSS_LAYOUT_FORUMS'); ?>

			<div class="panel-body">
				<div class="form-horizontal">
					<?php echo $this->html('settings.toggle', 'layout_categories_with_nopost', 'COM_EASYDISCUSS_FORUMS_SHOW_EMPTY_POST_CATEGORIES'); ?>
					<?php echo $this->html('settings.toggle', 'layout_categories_tags', 'COM_EASYDISCUSS_FORUMS_SHOW_TAGS'); ?>
					<?php echo $this->html('settings.textbox', 'layout_categories_limit', 'COM_EASYDISCUSS_CATEGORIES_LIMIT', '', array('size' => 8, 'postfix' => 'Categories'), '', 'text-center form-control-sm'); ?>
					<?php echo $this->html('settings.textbox', 'layout_post_category_limit', 'COM_EASYDISCUSS_POST_CATEGORY_LIMIT', '', array('size' => 7, 'postfix' => 'Posts'), '', 'text-center form-control-sm'); ?>
				</div>
			</div>
		</div>
	</div>

	<div class="col-md-6">

	</div>
</div>
