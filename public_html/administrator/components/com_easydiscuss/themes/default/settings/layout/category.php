<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 Stack Ideas Private Limited. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Restricted access');
?>

<div class="row">
	<div class="col-md-6">
		<div class="panel">
			<?php echo $this->html('panel.head', 'COM_EASYDISCUSS_CATEGORY'); ?>

			<div id="option01" class="panel-body">
				<div class="form-horizontal">
					<div class="form-group">
                        <div class="col-md-5 control-label">
                            <?php echo $this->html('form.label', 'COM_EASYDISCUSS_ALWAYS_HIDE_CATEGORY_DESCRIPTION'); ?>
                        </div>
                        <div class="col-md-7">
							<?php echo $this->html('form.boolean', 'layout_category_description_hidden', $this->config->get('layout_category_description_hidden'));?>
						</div>
					</div>
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
					<div class="form-group">
                        <div class="col-md-5 control-label">
                            <?php echo $this->html('form.label', 'COM_EASYDISCUSS_CATEGORY_PATH'); ?>
                        </div>
                        <div class="col-md-7">
							<input type="text" name="main_categoryavatarpath" class="form-control" value="<?php echo $this->config->get('main_categoryavatarpath', 'images/eblog_cavatar/' );?>" />
						</div>
					</div>
					<div class="form-group">
                        <div class="col-md-5 control-label">
                            <?php echo $this->html('form.label', 'COM_EASYDISCUSS_CATEGORY_SHOWMODERATORS'); ?>
                        </div>
                        <div class="col-md-7">
							<?php echo $this->html('form.boolean', 'layout_show_moderators', $this->config->get('layout_show_moderators'));?>
						</div>
					</div>
					<div class="form-group">
                        <div class="col-md-5 control-label">
                            <?php echo $this->html('form.label', 'COM_EASYDISCUSS_CATEGORY_SHOW_STATS'); ?>
                        </div>
                        <div class="col-md-7">
							<?php echo $this->html('form.boolean', 'layout_category_stats', $this->config->get('layout_category_stats'));?>
						</div>
					</div>
					<div class="form-group">
                        <div class="col-md-5 control-label">
                            <?php echo $this->html('form.label', 'COM_EASYDISCUSS_CATEGORY_SHOW_ONE_LEVEL_SUBCATEGORY'); ?>
                        </div>
                        <div class="col-md-7">
							<?php echo $this->html('form.boolean', 'layout_category_one_level', $this->config->get('layout_category_one_level'));?>
						</div>
					</div>
					<div class="form-group">
                        <div class="col-md-5 control-label">
                            <?php echo $this->html('form.label', 'COM_EASYDISCUSS_CATEGORY_TOGGLE_CATEGORY'); ?>
                        </div>
                        <div class="col-md-7">
							<?php echo $this->html('form.boolean', 'layout_category_toggle', $this->config->get('layout_category_toggle'));?>
						</div>
					</div>
					<div class="form-group">
                        <div class="col-md-5 control-label">
                            <?php echo $this->html('form.label', 'COM_EASYDISCUSS_CATEGORY_SHOW_CLASSIC_CATEGORY'); ?>
                        </div>
                        <div class="col-md-7">
							<?php echo $this->html('form.boolean', 'layout_show_classic', $this->config->get('layout_show_classic'));?>
						</div>
					</div>
					<div class="form-group">
                        <div class="col-md-5 control-label">
                            <?php echo $this->html('form.label', 'COM_EASYDISCUSS_CATEGORY_SHOW_ALL_SUBCATEGORIES'); ?>
                        </div>
                        <div class="col-md-7">
							<?php echo $this->html('form.boolean', 'layout_show_all_subcategories', $this->config->get('layout_show_all_subcategories'));?>
						</div>
					</div>

                    <div class="form-group">
                        <div class="col-md-5 control-label">
                            <?php echo $this->html('form.label', 'COM_EASYDISCUSS_SINGLE_CATEGORY_POST_LIMIT'); ?>
                        </div>
                        <div class="col-md-7">
                            <input type="text" name="layout_single_category_post_limit" value="<?php echo $this->config->get('layout_single_category_post_limit');?>" size="5" style="text-align:center;" class="form-control form-control-sm text-center" />
                        </div>
                    </div>
				</div>
			</div>
		</div>
	</div>
	<div class="col-md-6">
	</div>
</div>
