<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2020 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' ); ?>

<div class="navbar" id="rsepro-navbar">
	<div class="navbar-inner">
		<div class="container">
			<a data-target=".navbar-responsive-collapse" data-toggle="collapse" class="btn btn-navbar">
				<i class="icon-bar"></i>
				<i class="icon-bar"></i>
				<i class="icon-bar"></i>
			</a>
			<div class="nav-collapse collapse navbar-responsive-collapse">
				<ul class="nav">
					<li id="rsepro-filter-from" class="dropdown">
						<a data-toggle="dropdown" class="dropdown-toggle" href="#" rel="events"><span><?php echo JText::_('COM_RSEVENTSPRO_FILTER_NAME'); ?></span> <i class="caret"></i></a>
						<ul class="dropdown-menu">
							<?php foreach ($this->get('filteroptions') as $option) { ?>
							<li><a href="javascript:void(0);" rel="<?php echo $option->value; ?>"><?php echo $option->text; ?></a></li>
							<?php } ?>
						</ul>
					</li>
					<li id="rsepro-filter-condition" class="dropdown">
						<a data-toggle="dropdown" class="dropdown-toggle" href="#" rel="is"><span><?php echo JText::_('COM_RSEVENTSPRO_FILTER_CONDITION_IS'); ?></span> <i class="caret"></i></a>
						<ul class="dropdown-menu">
							<?php foreach ($this->get('filterconditions') as $option) { ?>
							<li><a href="javascript:void(0);" rel="<?php echo $option->value; ?>"><?php echo $option->text; ?></a></li>
							<?php } ?>
						</ul>
					</li>
					<li id="rsepro-search" class="navbar-search center">
						<input type="text" id="rsepro-filter" name="rsepro-filter" value="" size="35" />
					</li>
					<li id="rsepro-filter-featured" class="dropdown" style="display: none;">
						<a data-toggle="dropdown" class="dropdown-toggle" href="#" rel="1"><span><?php echo JText::_('JYES'); ?></span> <i class="caret"></i></a>
						<ul class="dropdown-menu">
							<li><a href="javascript:void(0);" rel="1"><?php echo JText::_('JYES'); ?></a></li>
							<li><a href="javascript:void(0);" rel="0"><?php echo JText::_('JNO'); ?></a></li>
						</ul>
					</li>
					<li id="rsepro-filter-child" class="dropdown" style="display: none;">
						<a data-toggle="dropdown" class="dropdown-toggle" href="#" rel="1"><span><?php echo JText::_('JYES'); ?></span> <i class="caret"></i></a>
						<ul class="dropdown-menu">
							<li><a href="javascript:void(0);" rel="1"><?php echo JText::_('JYES'); ?></a></li>
							<li><a href="javascript:void(0);" rel="0"><?php echo JText::_('JNO'); ?></a></li>
						</ul>
					</li>
					<li id="rsepro-filter-status" class="dropdown" style="display: none;">
						<a data-toggle="dropdown" class="dropdown-toggle" href="#" rel="1"><span><?php echo JText::_('JPUBLISHED'); ?></span> <i class="caret"></i></a>
						<ul class="dropdown-menu">
							<li><a href="javascript:void(0);" rel="1"><?php echo JText::_('JPUBLISHED'); ?></a></li>
							<li><a href="javascript:void(0);" rel="0"><?php echo JText::_('JUNPUBLISHED'); ?></a></li>
							<li><a href="javascript:void(0);" rel="2"><?php echo JText::_('JARCHIVED'); ?></a></li>
							<li><a href="javascript:void(0);" rel="3"><?php echo JText::_('COM_RSEVENTSPRO_CANCELED_STATUS'); ?></a></li>
						</ul>
					</li>
					<li id="rsepro-filter-start" class="navbar-search center" style="display: none;">
						<?php echo JHTML::_('rseventspro.calendar', JFactory::getDate()->format('Y-m-d H:i:s'), 'start_date', 'start_date','%Y-%m-%d %H:%M:%S'); ?>
					</li>
					<li id="rsepro-filter-end" class="navbar-search center" style="display: none;">
						<?php echo JHTML::_('rseventspro.calendar', JFactory::getDate()->format('Y-m-d H:i:s'), 'end_date', 'end_date','%Y-%m-%d %H:%M:%S'); ?>
					</li>
					<li class="divider-vertical"></li>
					<li class="center">
						<div class="btn-group">
							<button id="rsepro-filter-btn" type="button" class="btn"><?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_ADD_FILTER'); ?></button>
							<button id="rsepro-clear-btn" type="button" class="btn"><?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_CLEAR_FILTER'); ?></button>
						</div>
					</li>
				</ul>
				<ul class="nav pull-right">
					<li id="rsepro-filter-order" class="dropdown">
						<a data-toggle="dropdown" class="dropdown-toggle" href="#" rel="<?php echo $this->sortColumn; ?>"><span><?php echo $this->sortColumnText; ?></span> <i class="caret"></i></a>
						<ul class="dropdown-menu">
							<?php foreach ($this->get('ordering') as $option) { ?>
							<li><a href="javascript:void(0);" rel="<?php echo $option->value; ?>"><?php echo $option->text; ?></a></li>
							<?php } ?>
						</ul>
					</li>
					<li id="rsepro-filter-order-dir" class="dropdown">
						<a data-toggle="dropdown" class="dropdown-toggle" href="#" rel="<?php echo $this->sortOrder; ?>"><span><?php echo $this->sortOrderText; ?></span> <i class="caret"></i></a>
						<ul class="dropdown-menu">
							<?php foreach ($this->get('order') as $option) { ?>
							<li><a href="javascript:void(0);" rel="<?php echo $option->value; ?>"><?php echo $option->text; ?></a></li>
							<?php } ?>
						</ul>
					</li>
					
					<?php if ($this->tpl == 'general') { ?>
					<li class="navbar-search">
						<?php echo $this->pagination->getLimitBox(); ?>
					</li>
					<?php } ?>
				</ul>
				
			</div>
		</div>
	</div>
</div>

<br />

<ul class="rsepro-filter-filters inline unstyled">
	<li class="rsepro-filter-operator" <?php echo $this->showCondition > 1 ? '' : 'style="display:none"'; ?>>
		<div class="btn-group">
			<a data-toggle="dropdown" class="btn btn-small dropdown-toggle" href="#"><span><?php echo ucfirst(JText::_('COM_RSEVENTSPRO_GLOBAL_'.$this->operator)); ?></span> <i class="caret"></i></a>
			<ul class="dropdown-menu">
				<li><a href="javascript:void(0)" rel="AND"><?php echo ucfirst(JText::_('COM_RSEVENTSPRO_GLOBAL_AND')); ?></a></li>
				<li><a href="javascript:void(0)" rel="OR"><?php echo ucfirst(JText::_('COM_RSEVENTSPRO_GLOBAL_OR')); ?></a></li>
			</ul>
		</div>
		<input type="hidden" name="filter_operator" value="<?php echo $this->operator; ?>" />
	</li>
	
	<?php if (!is_null($statuses = $this->other['status'])) { ?>
	<?php foreach ($statuses as $status) { ?>
		<li id="<?php echo sha1('status'.$status); ?>">
			<div class="btn-group">
				<span class="btn btn-small"><?php echo JText::_('COM_RSEVENTSPRO_FILTER_STATUS'); ?></span>
				<span class="btn btn-small"><?php if ($status == 0) echo JText::_('JUNPUBLISHED'); elseif ($status == 1) echo JText::_('JPUBLISHED'); elseif ($status == 2) echo JText::_('JARCHIVED'); elseif ($status == 3) echo JText::_('COM_RSEVENTSPRO_CANCELED_STATUS'); ?></span>
				<input type="hidden" name="filter_status[]" value="<?php echo $this->escape($status); ?>">
				<a href="javascript:void(0)" class="btn btn-small rsepro-close">
					<i class="icon-delete"></i>&nbsp;
				</a>
			</div>
		</li>
		
		<li class="rsepro-filter-conditions" <?php echo $this->showCondition > 1 ? '' : 'style="display: none;"'; ?>>
			<a class="btn btn-small"><?php echo ucfirst(JText::_('COM_RSEVENTSPRO_GLOBAL_'.$this->operator));?></a>
		</li>
	<?php } ?>
	<?php } ?>
	
	<?php if (!is_null($featured = $this->other['featured'])) { ?>
		<li id="<?php echo sha1('featured'); ?>">
			<div class="btn-group">
				<span class="btn btn-small"><?php echo JText::_('COM_RSEVENTSPRO_FILTER_FEATURED'); ?></span>
				<span class="btn btn-small"><?php echo $featured == 0 ? JText::_('JNO') : JText::_('JYES'); ?></span>
				<input type="hidden" name="filter_featured[]" value="<?php echo $this->escape($featured); ?>">
				<a href="javascript:void(0)" class="btn btn-small rsepro-close">
					<i class="icon-delete"></i>&nbsp;
				</a>
			</div>
		</li>
		
		<li class="rsepro-filter-conditions" <?php echo $this->showCondition > 1 ? '' : 'style="display: none;"'; ?>>
			<a class="btn btn-small"><?php echo ucfirst(JText::_('COM_RSEVENTSPRO_GLOBAL_'.$this->operator));?></a>
		</li>
	<?php } ?>
	
	<?php if (!is_null($child = $this->other['childs'])) { ?>
		<li id="<?php echo sha1('child'); ?>">
			<div class="btn-group">
				<span class="btn btn-small"><?php echo JText::_('COM_RSEVENTSPRO_FILTER_CHILD'); ?></span>
				<span class="btn btn-small"><?php echo $child == 0 ? JText::_('JNO') : JText::_('JYES'); ?></span>
				<input type="hidden" name="filter_child[]" value="<?php echo $this->escape($child); ?>">
				<a href="javascript:void(0)" class="btn btn-small rsepro-close">
					<i class="icon-delete"></i>&nbsp;
				</a>
			</div>
		</li>
		
		<li class="rsepro-filter-conditions" <?php echo $this->showCondition > 1 ? '' : 'style="display: none;"'; ?>>
			<a class="btn btn-small"><?php echo ucfirst(JText::_('COM_RSEVENTSPRO_GLOBAL_'.$this->operator));?></a>
		</li>
	<?php } ?>
	
	<?php if (!is_null($start = $this->other['start'])) { ?>
		<li id="<?php echo sha1('start_date'); ?>">
			<div class="btn-group">
				<span class="btn btn-small"><?php echo JText::_('COM_RSEVENTSPRO_FILTER_FROM'); ?></span>
				<span class="btn btn-small"><?php echo $start; ?></span>
				<input type="hidden" name="filter_start[]" value="<?php echo $this->escape($start); ?>">
				<a href="javascript:void(0)" class="btn btn-small rsepro-close">
					<i class="icon-delete"></i>&nbsp;
				</a>
			</div>
		</li>
		
		<li class="rsepro-filter-conditions" <?php echo $this->showCondition > 1 ? '' : 'style="display: none;"'; ?>>
			<a class="btn btn-small"><?php echo ucfirst(JText::_('COM_RSEVENTSPRO_GLOBAL_'.$this->operator));?></a>
		</li>
	<?php } ?>
	
	<?php if (!is_null($end = $this->other['end'])) { ?>
		<li id="<?php echo sha1('end_date'); ?>">
			<div class="btn-group">
				<span class="btn btn-small"><?php echo JText::_('COM_RSEVENTSPRO_FILTER_TO'); ?></span>
				<span class="btn btn-small"><?php echo $end; ?></span>
				<input type="hidden" name="filter_end[]" value="<?php echo $this->escape($end); ?>">
				<a href="javascript:void(0)" class="btn btn-small rsepro-close">
					<i class="icon-delete"></i>&nbsp;
				</a>
			</div>
		</li>
		
		<li class="rsepro-filter-conditions" <?php echo $this->showCondition > 1 ? '' : 'style="display: none;"'; ?>>
			<a class="btn btn-small"><?php echo ucfirst(JText::_('COM_RSEVENTSPRO_GLOBAL_'.$this->operator));?></a>
		</li>
	<?php } ?>
	
	<?php if (!empty($this->columns)) { ?>
	<?php for ($i=0; $i < count($this->columns); $i++) { ?>
		<?php $hash = sha1(@$this->columns[$i].@$this->operators[$i].@$this->values[$i]); ?>
		<li id="<?php echo $hash; ?>">
			<div class="btn-group">
				<span class="btn btn-small"><?php echo rseventsproHelper::translate($this->columns[$i]); ?></span>
				<span class="btn btn-small"><?php echo rseventsproHelper::translate($this->operators[$i]); ?></span>
				<span class="btn btn-small"><?php echo $this->escape($this->values[$i]); ?></span>
				<input type="hidden" name="filter_from[]" value="<?php echo $this->escape($this->columns[$i]); ?>">
				<input type="hidden" name="filter_condition[]" value="<?php echo $this->escape($this->operators[$i]); ?>">
				<input type="hidden" name="search[]" value="<?php echo $this->escape($this->values[$i]); ?>">
				<a href="javascript:void(0)" class="btn btn-small rsepro-close">
					<i class="icon-delete"></i>&nbsp;
				</a>
			</div>
		</li>
		
		<li class="rsepro-filter-conditions" <?php echo $i == (count($this->columns) - 1) ? 'style="display: none;"' : ''; ?>>
			<a class="btn btn-small"><?php echo ucfirst(JText::_('COM_RSEVENTSPRO_GLOBAL_'.$this->operator));?></a>
		</li>
		
	<?php } ?>
	<?php } ?>
</ul>