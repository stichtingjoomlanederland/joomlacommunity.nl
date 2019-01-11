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
?>
<div class="row">
	<div class="col-md-6">
		<div class="panel">
			<?php echo $this->html('panel.head', 'COM_EASYDISCUSS_CATEGORY'); ?>

			<div class="panel-body">
				<div class="form-horizontal">
					<?php echo $this->html('settings.toggle', 'layout_category_description_hidden', 'COM_EASYDISCUSS_ALWAYS_HIDE_CATEGORY_DESCRIPTION'); ?>
					<div class="form-group">
						<div class="col-md-5 control-label">
							<?php echo $this->html('form.label', 'COM_EASYDISCUSS_LAYOUT_CATEGORY_ORDERING'); ?>
						</div>
						<div class="col-md-7">
							<?php
								$orderingType = array();
								$orderingType[] = JHTML::_('select.option', 'alphabet', JText::_( 'COM_EASYDISCUSS_SORT_ALPHABETICAL' ) );
								$orderingType[] = JHTML::_('select.option', 'latest', JText::_( 'COM_EASYDISCUSS_SORT_LATEST' ) );
								$orderingType[] = JHTML::_('select.option', 'ordering', JText::_( 'COM_EASYDISCUSS_SORT_ORDERING' ) );
								$orderingTypeHTML = JHTML::_('select.genericlist', $orderingType, 'layout_ordering_category', 'class="form-control"  ', 'value', 'text', $this->config->get('layout_ordering_category' , 'ordering' ) );
								echo $orderingTypeHTML;
							?>
						</div>
					</div>
					<div class="form-group">
						<div class="col-md-5 control-label">
							<?php echo $this->html('form.label', 'COM_EASYDISCUSS_LAYOUT_CATEGORY_SORTING'); ?>
						</div>
						<div class="col-md-7">
							<?php
								$sortingType = array();
								$sortingType[] = JHTML::_('select.option', 'asc', JText::_( 'COM_EASYDISCUSS_SORT_ASC' ) );
								$sortingType[] = JHTML::_('select.option', 'desc', JText::_( 'COM_EASYDISCUSS_SORT_DESC' ) );
								$sortingTypeHTML = JHTML::_('select.genericlist', $sortingType, 'layout_sort_category', 'class="form-control"  ', 'value', 'text', $this->config->get('layout_sort_category' , 'asc' ) );
								echo $sortingTypeHTML;
							?>
						</div>
					</div>

					<?php echo $this->html('settings.toggle', 'layout_category_show_avatar', 'COM_EASYDISCUSS_CATEGORY_AVATAR'); ?>
					<?php echo $this->html('settings.textbox', 'main_categoryavatarpath', 'COM_EASYDISCUSS_CATEGORY_PATH', '', array('defaultValue' => 'images/discuss_cavatar/')); ?>
					<?php echo $this->html('settings.toggle', 'layout_show_moderators', 'COM_EASYDISCUSS_CATEGORY_SHOWMODERATORS'); ?>
					<?php echo $this->html('settings.toggle', 'layout_category_stats', 'COM_EASYDISCUSS_CATEGORY_SHOW_STATS'); ?>
					<?php echo $this->html('settings.toggle', 'layout_category_one_level', 'COM_EASYDISCUSS_CATEGORY_SHOW_ONE_LEVEL_SUBCATEGORY'); ?>
					<?php echo $this->html('settings.toggle', 'layout_category_toggle', 'COM_EASYDISCUSS_CATEGORY_TOGGLE_CATEGORY'); ?>
					<?php echo $this->html('settings.toggle', 'layout_show_classic', 'COM_EASYDISCUSS_CATEGORY_SHOW_CLASSIC_CATEGORY'); ?>
					<?php echo $this->html('settings.toggle', 'layout_show_all_subcategories', 'COM_EASYDISCUSS_CATEGORY_SHOW_ALL_SUBCATEGORIES'); ?>
					<?php echo $this->html('settings.textbox', 'layout_single_category_post_limit', 'COM_EASYDISCUSS_SINGLE_CATEGORY_POST_LIMIT', '', array('size' => 7, 'postfix' => 'Posts'), '', 'text-center form-control-sm'); ?>
				</div>
			</div>
		</div>
	</div>
	<div class="col-md-6">
	</div>
</div>
