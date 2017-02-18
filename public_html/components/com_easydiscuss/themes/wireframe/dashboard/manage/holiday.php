<?php
/**
* @package      EasyDiscuss
* @copyright    Copyright (C) 2010 - 2016 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Restricted access');
?>
<h2 class="ed-page-title"><?php echo JText::_('COM_EASYDISCUSS_MANAGE_HOLIDAY'); ?></h2>

<div class="ed-dashboard">
	<div class="ed-dashboard__hd">
		<div class="o-col-sm">
			<i class="fa fa-calendar-o"></i> <?php echo JText::_('COM_EASYDISCUSS_HOLIDAY'); ?>    
		</div>
		<div class="o-col-sm">
			<a href="<?php echo EDR::_('view=dashboard&layout=form'); ?>" class="btn btn-primary pull-right"><?php echo JText::_('COM_EASYDISCUSS_NEW_HOLIDAY'); ?></a>
		</div>    
	</div>

		<div class="ed-dashboard__bd">
		 <?php if ($holidays) { ?>
			<?php foreach ($holidays as $holiday) { ?>
				<div class="ed-dashboard-item" data-holiday-item>
					<div class="o-col ed-dashboard-item__col-name">
						<b><?php echo $holiday->title; ?></b>
						<div><?php echo $holiday->description; ?></div>
					</div>
					<div class="o-col ed-dashboard-item__col-date">
						<div class="o-flag">
							<div class="o-flag__image o-flag--top">
								<i class="fa fa-clock-o"></i>
							</div>
							<div class="o-flag__body">
								<div><?php echo JText::_('COM_EASYDISCUSS_HOLIDAY_STARTS') ?>: <b><?php echo ED::date($holiday->start)->display(JText::_('DATE_FORMAT_LC1')); ?></b></div>
								<div><?php echo JText::_('COM_EASYDISCUSS_HOLIDAY_ENDS') ?>: <b><?php echo ED::date($holiday->end)->display(JText::_('DATE_FORMAT_LC1')); ?></b></div>
							</div>
						</div>
					</div>
					<div class="o-col ed-dashboard-item__col-switch o-col--top">
						<div class="o-switch">
							<input type="checkbox" name="onoffswitch" class="o-switch__checkbox" id="<?php echo $holiday->title;?>" data-holiday-toggle data-id="<?php echo $holiday->id;?>" <?php echo $holiday->published? 'checked':''; ?> >
							<label class="o-switch__label" for="<?php echo $holiday->title;?>">
								<span class="o-switch__inner"></span>
								<span class="o-switch__switch"></span>
							</label>
						</div>
					</div>
					<div class="o-col ed-dashboard-item__col-action o-col--top">
						<div class="dropdown">
							<button data-ed-toggle="dropdown" class="btn btn-default btn-xs" type="button">
								<span class="caret"></span>
							</button>
							<ul class="dropdown-menu dropdown-menu-right">
								<li>
									<a href="<?php echo EDR::_('view=dashboard&layout=form&id='.$holiday->id);?>"><?php echo JText::_('COM_EASYDISCUSS_HOLIDAY_EDIT_DROPDOWN'); ?></a>
								</li>
								<li>
									<a href="javascript:void(0);" data-delete-holiday data-id="<?php echo $holiday->id;?>"><?php echo JText::_('COM_EASYDISCUSS_HOLIDAY_DELETE_DROPDOWN'); ?></a>
								</li>
							</ul>
						</div>
						
					</div>
				</div>
			<?php } ?>
		<?php } else { ?>
			<div class="is-empty">
				<div class="o-empty">
					<div class="o-empty__content">
						<i class="o-empty__icon fa fa-calendar-o t-lg-mb--md"></i>
						<div class="o-empty__text"><?php echo JText::_('COM_EASYDISCUSS_EMPTY_HOLIDAY_LIST');?></div>
					</div>
				</div>    
			</div>  
		<?php } ?>
		</div>
</div>