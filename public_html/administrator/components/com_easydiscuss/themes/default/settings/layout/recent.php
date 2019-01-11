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
			<?php echo $this->html('panel.head', 'COM_EASYDISCUSS_LAYOUT_RECENT_VIEW'); ?>

			<div class="panel-body">
				<div class="form-horizontal">
					<?php echo $this->html('settings.toggle', 'layout_enableintrotext', 'COM_EASYDISCUSS_ENABLE_INTROTEXT'); ?>
					<?php echo $this->html('settings.textbox', 'layout_introtextlength', 'COM_EASYDISCUSS_INTROTEXT_LENGTH', '', array('size' => 8, 'postfix' => 'Characters'), '', 'text-center form-control-sm'); ?>

					<?php echo $this->html('settings.toggle', 'layout_showtags', 'COM_EASYDISCUSS_LAYOUT_SHOWS_TAGS'); ?>
					<?php echo $this->html('settings.toggle', 'layout_enablefilter_resolved', 'COM_EASYDISCUSS_ENABLE_FILTER_RESOLVED'); ?>
					<?php echo $this->html('settings.toggle', 'layout_enablefilter_unresolved', 'COM_EASYDISCUSS_ENABLE_FILTER_UNRESOLVED'); ?>
					<?php echo $this->html('settings.toggle', 'layout_enablefilter_unanswered', 'COM_EASYDISCUSS_ENABLE_FILTER_UNANSWERED'); ?>
					<?php echo $this->html('settings.toggle', 'layout_enablefilter_unread', 'COM_EASYDISCUSS_ENABLE_FILTER_UNREAD'); ?>
				</div>
			</div>
		</div>
		
	</div>

	<div class="col-md-6">
		<div class="panel">
			<?php echo $this->html('panel.head', 'COM_EASYDISCUSS_FEATURED_FRONTPAGE_LISTING'); ?>
			<div class="panel-body">
				<div class="form-horizontal">
					<?php echo $this->html('settings.toggle', 'layout_featuredpost_frontpage', 'COM_EASYDISCUSS_FEATURED_POSTS_FRONTPAGE'); ?>
					<?php echo $this->html('settings.textbox', 'layout_featuredpost_limit', 'COM_EASYDISCUSS_FEATURED_POSTS_LIMIT', '', array('size' => 8, 'postfix' => 'Posts'), '', 'text-center form-control-sm'); ?>
					
					<div class="form-group">
						<div class="col-md-5 control-label">
							<?php echo $this->html('form.label', 'COM_EASYDISCUSS_FEATURED_SORTING'); ?>
						</div>
						<div class="col-md-7">
							<?php
								$featuredOrdering = array();
								$featuredOrdering[] = JHTML::_('select.option', 'date_latest', JText::_( 'COM_EASYDISCUSS_FEATURED_ORDER_DATE_LATEST' ) );
								$featuredOrdering[] = JHTML::_('select.option', 'date_oldest', JText::_( 'COM_EASYDISCUSS_FEATURED_ORDER_DATE_OLDEST' ) );
								$featuredOrdering[] = JHTML::_('select.option', 'order_asc', JText::_( 'COM_EASYDISCUSS_FEATURED_ORDER_ORDER_ASC' ) );
								$featuredOrdering[] = JHTML::_('select.option', 'order_desc', JText::_( 'COM_EASYDISCUSS_FEATURED_ORDER_ORDER_DESC' ) );
								$showdet = JHTML::_('select.genericlist', $featuredOrdering, 'layout_featuredpost_sort', 'class="form-control"  ', 'value', 'text', $this->config->get('layout_featuredpost_sort' , 'date_latest' ) );
								echo $showdet;
							?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
