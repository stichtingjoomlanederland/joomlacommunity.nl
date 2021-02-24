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
			<?php echo $this->html('panel.head', 'COM_EASYDISCUSS_LAYOUT_POST'); ?>

			<div class="panel-body">
				<div class="o-form-horizontal">
					<?php echo $this->html('settings.toggle', 'layout_replies_pagination', 'COM_EASYDISCUSS_REPLIES_ENABLE_PAGINATION'); ?>
					<?php echo $this->html('settings.textbox', 'layout_replies_list_limit', 'COM_EASYDISCUSS_REPLIES_LIST_LIMIT', '', array('size' => 8, 'postfix' => 'Replies'), '', '', 'text-center'); ?>
					<?php echo $this->html('settings.toggle', 'main_enable_print', 'COM_EASYDISCUSS_ENABLE_PRINT_BUTTON'); ?>
					<?php echo $this->html('settings.toggle', 'main_viewingpage', 'COM_EASYDISCUSS_ENABLE_WHOS_VIEWING'); ?>

					<?php echo $this->html('settings.dropdown', 'main_post_activity', 'COM_ED_POST_ACTIVITY_LOGS', '',
						array(
							'moderator' => 'Visible to moderators only',
							'everyone' => 'Visible to everyone',
							'disable' => 'Disable post activity logs'
						)
					);?>
					
					<div class="o-form-group">
						<div class="col-md-5 o-form-label">
							<?php echo $this->html('form.label', 'COM_EASYDISCUSS_REPLIES_SORTING_TAB'); ?>
						</div>
						<div class="col-md-7">
							<?php
								$filterFormat = array();
								$filterFormat[] = JHTML::_('select.option', 'oldest', JText::_( 'COM_EASYDISCUSS_REPLIES_SORTING_BY_OLDEST' ) );
								$filterFormat[] = JHTML::_('select.option', 'latest', JText::_( 'COM_EASYDISCUSS_REPLIES_SORTING_BY_LATEST' ) );
								$showdet = JHTML::_('select.genericlist', $filterFormat, 'layout_replies_sorting', 'class="o-form-select"  ', 'value', 'text', $this->config->get('layout_replies_sorting' , 'latest' ) );
								echo $showdet;
							?>
						</div>
					</div>

					<div class="o-form-group">
						<div class="col-md-5 o-form-label">
							<?php echo $this->html('form.label', 'COM_ED_REPLIES_DATE_SOURCE_SETTING'); ?>
						</div>
						<div class="col-md-7">
							<?php
								$filterFormat = array();
								$filterFormat[] = JHTML::_('select.option', 'default', JText::_('COM_ED_REPLIES_DATE_SOURCE_DEFAULT'));
								$filterFormat[] = JHTML::_('select.option', 'created', JText::_('COM_ED_REPLIES_DATE_SOURCE_CREATED'));
								$filterFormat[] = JHTML::_('select.option', 'modified', JText::_('COM_ED_REPLIES_DATE_SOURCE_MODIFIED'));	
								$showdet = JHTML::_('select.genericlist', $filterFormat, 'layout_replies_date_source', 'class="o-form-select"  ', 'value', 'text', $this->config->get('layout_replies_date_source' , 'default'));
								echo $showdet;
							?>
						</div>
					</div>

					<?php echo $this->html('settings.toggle', 'layout_post_liveupdates', 'COM_ED_POST_LAYOUT_LIVE_UPDATES_SETTINGS'); ?>
				</div>
			</div>
		</div>
	</div>

	<div class="col-md-6">

	</div>
</div>
