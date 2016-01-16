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
<div class="row-fluid">
	<div class="span12 panel-title">
		<h2><?php echo JText::_( 'COM_EASYDISCUSS_SETTINGS_LAYOUT_FEATURED_TITLE' );?></h2>
		<p style="margin: 0 0 15px;">
			<?php echo JText::_( 'COM_EASYDISCUSS_SETTINGS_LAYOUT_FEATURED_DESC' );?>
		</p>
	</div>
</div>
<div class="row-fluid ">
	<div class="span6">
		<div class="widget accordion-group">
			<div class="whead accordion-heading">
				<a href="javascript:void(0);" data-foundry-toggle="collapse" data-target="#option01">
				<h6><?php echo JText::_( 'COM_EASYDISCUSS_FEATURED_FRONTPAGE_LISTING' ); ?></h6>
				<i class="icon-chevron-down"></i>
				</a>
			</div>

			<div id="option01" class="accordion-body collapse in">
				<div class="wbody">

					<div class="si-form-row">
						<div class="span4 form-row-label">
							<label>
								<?php echo JText::_( 'COM_EASYDISCUSS_FEATURED_POSTS_FRONTPAGE' ); ?>
							</label>
						</div>
						<div class="span8"
							rel="ed-popover"
							data-placement="left"
							data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_FEATURED_POSTS_FRONTPAGE' ); ?>"
							data-content="<?php echo JText::_('COM_EASYDISCUSS_FEATURED_POSTS_FRONTPAGE_DESC'); ?>"
						>
							<?php echo $this->renderCheckbox( 'layout_featuredpost_frontpage' , $this->config->get( 'layout_featuredpost_frontpage' ) );?>
						</div>
					</div>


					<div class="si-form-row">
						<div class="span4 form-row-label">
							<label>
								<?php echo JText::_( 'COM_EASYDISCUSS_FEATURED_POSTS_LIMIT' ); ?>
							</label>
						</div>
						<div class="span8"
							rel="ed-popover"
							data-placement="left"
							data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_FEATURED_POSTS_LIMIT' ); ?>"
							data-content="<?php echo JText::_('COM_EASYDISCUSS_FEATURED_POSTS_LIMIT_DESC'); ?>"
						>
							<input type="text" class="input-mini center" name="layout_featuredpost_limit" value="<?php echo $this->config->get('layout_featuredpost_limit', '5' );?>" />
						</div>
					</div>

					<div class="si-form-row">
						<div class="span4 form-row-label">
							<label>
								<?php echo JText::_( 'COM_EASYDISCUSS_FEATURED_SORTING' ); ?>
							</label>
						</div>
						<div class="span8"
							rel="ed-popover"
							data-placement="left"
							data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_FEATURED_SORTING' ); ?>"
							data-content="<?php echo JText::_('COM_EASYDISCUSS_FEATURED_SORTING_DESC'); ?>"
						>
							<?php
								$featuredOrdering = array();
								$featuredOrdering[] = JHTML::_('select.option', 'date_latest', JText::_( 'COM_EASYDISCUSS_FEATURED_ORDER_DATE_LATEST' ) );
								$featuredOrdering[] = JHTML::_('select.option', 'date_oldest', JText::_( 'COM_EASYDISCUSS_FEATURED_ORDER_DATE_OLDEST' ) );
								$featuredOrdering[] = JHTML::_('select.option', 'order_asc', JText::_( 'COM_EASYDISCUSS_FEATURED_ORDER_ORDER_ASC' ) );
								$featuredOrdering[] = JHTML::_('select.option', 'order_desc', JText::_( 'COM_EASYDISCUSS_FEATURED_ORDER_ORDER_DESC' ) );
								$showdet = JHTML::_('select.genericlist', $featuredOrdering, 'layout_featuredpost_sort', 'class="full-width" size="1" ', 'value', 'text', $this->config->get('layout_featuredpost_sort' , 'date_latest' ) );
								echo $showdet;
							?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="span6">

	</div>
</div>

