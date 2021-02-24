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
			<?php echo $this->html('panel.head', 'COM_EASYDISCUSS_SETTINGS_COMMENT'); ?>

			<div class="panel-body">
				<div class="o-form-horizontal">
					<?php echo $this->html('settings.toggle', 'main_commentpost', 'COM_EASYDISCUSS_ENABLE_COMMENT_POST'); ?>

					<?php echo $this->html('settings.toggle', 'main_comment', 'COM_EASYDISCUSS_ENABLE_COMMENT'); ?>

					<?php echo $this->html('settings.toggle', 'main_comment_permalink', 'COM_ED_COMMENTS_SHOW_PERMALINK'); ?>

					<?php echo $this->html('settings.dropdown', 'main_comment_ordering', 'COM_ED_COMMENT_LIST_SETTING_ORDER', '',	
						array(
							'desc' => 'COM_ED_COMMENT_LIST_SETTING_DESC',
							'asc' => 'COM_ED_COMMENT_LIST_SETTING_ASC'
						)
					);?>

					<?php echo $this->html('settings.toggle', 'main_comment_pagination', 'COM_EASYDISCUSS_COMMENT_PAGINATION'); ?>
					
					<?php echo $this->html('settings.textbox', 'main_comment_pagination_count', 'COM_ED_COMMENT_FIRST_SIGHT_COUNT', '', array('size' => 8, 'postfix' => 'Comments'), '', '', 'text-center'); ?>				
				</div>
			</div>
		</div>
	</div>

	<div class="col-md-6">
	</div>
</div>