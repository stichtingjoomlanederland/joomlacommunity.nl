<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2020 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' ); ?>

<nav class="navbar navbar-expand-lg navbar-light bg-light" id="rsepro-navbar">
	 <div class="container-fluid">	
		<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target=".navbar-responsive-collapse" aria-expanded="false">
			<span class="navbar-toggler-icon"></span>
		</button>
		
		<div class="collapse navbar-collapse navbar-responsive-collapse">
			<ul class="navbar-nav me-auto">
				<li id="rsepro-filter-from" class="nav-item dropdown">
					<a data-bs-toggle="dropdown" class="nav-link active dropdown-toggle" href="#" rel="events"><span><?php echo JText::_('COM_RSEVENTSPRO_FILTER_NAME'); ?></span> <i class="caret"></i></a>
					<div class="dropdown-menu">
						<?php foreach ($this->get('filteroptions') as $option) { ?>
						<a href="javascript:void(0);" class="dropdown-item" rel="<?php echo $option->value; ?>"><?php echo $option->text; ?></a>
						<?php } ?>
					</div>
				</li>
				<li id="rsepro-filter-condition" class="nav-item dropdown">
					<a data-bs-toggle="dropdown" class="nav-link active dropdown-toggle" href="#" rel="is"><span><?php echo JText::_('COM_RSEVENTSPRO_FILTER_CONDITION_IS'); ?></span> <i class="caret"></i></a>
					<div class="dropdown-menu">
						<?php foreach ($this->get('filterconditions') as $option) { ?>
						<a href="javascript:void(0);" class="dropdown-item" rel="<?php echo $option->value; ?>"><?php echo $option->text; ?></a>
						<?php } ?>
					</div>
				</li>
				<li id="rsepro-search" class="nav-item">
					<input type="text" id="rsepro-filter" name="rsepro-filter" value="" size="35" class="form-control" />
				</li>
				<li id="rsepro-filter-featured" class="nav-item dropdown" style="display: none;">
					<a data-bs-toggle="dropdown" class="nav-link active dropdown-toggle" href="#" rel="1"><span><?php echo JText::_('JYES'); ?></span> <i class="caret"></i></a>
					<div class="dropdown-menu">
						<a href="javascript:void(0);" class="dropdown-item" rel="1"><?php echo JText::_('JYES'); ?></a>
						<a href="javascript:void(0);" class="dropdown-item" rel="0"><?php echo JText::_('JNO'); ?></a>
					</div>
				</li>
				<li id="rsepro-filter-child" class="nav-item dropdown" style="display: none;">
					<a data-bs-toggle="dropdown" class="nav-link active dropdown-toggle" href="#" rel="1"><span><?php echo JText::_('JYES'); ?></span> <i class="caret"></i></a>
					<div class="dropdown-menu">
						<a href="javascript:void(0);" class="dropdown-item" rel="1"><?php echo JText::_('JYES'); ?></a>
						<a href="javascript:void(0);" class="dropdown-item" rel="0"><?php echo JText::_('JNO'); ?></a>
					</div>
				</li>
				<li id="rsepro-filter-status" class="nav-item dropdown" style="display: none;">
					<a data-bs-toggle="dropdown" class="nav-link active dropdown-toggle" href="#" rel="1"><span><?php echo JText::_('JPUBLISHED'); ?></span> <i class="caret"></i></a>
					<div class="dropdown-menu">
						<a href="javascript:void(0);" class="dropdown-item" rel="1"><?php echo JText::_('JPUBLISHED'); ?></a>
						<a href="javascript:void(0);" class="dropdown-item" rel="0"><?php echo JText::_('JUNPUBLISHED'); ?></a>
						<a href="javascript:void(0);" class="dropdown-item" rel="2"><?php echo JText::_('JARCHIVED'); ?></a>
						<a href="javascript:void(0);" class="dropdown-item" rel="3"><?php echo JText::_('COM_RSEVENTSPRO_CANCELED_STATUS'); ?></a>
					</div>
				</li>
				<li id="rsepro-filter-start" class="nav-item" style="display: none;">
					<?php echo JHTML::_('rseventspro.calendar', JFactory::getDate()->format('Y-m-d H:i:s'), 'start_date', 'start_date','%Y-%m-%d %H:%M:%S'); ?>
				</li>
				<li id="rsepro-filter-end" class="nav-item" style="display: none;">
					<?php echo JHTML::_('rseventspro.calendar', JFactory::getDate()->format('Y-m-d H:i:s'), 'end_date', 'end_date','%Y-%m-%d %H:%M:%S'); ?>
				</li>
				<li class="divider-vertical"></li>
				<li class="nav-item ms-2">
					<div class="btn-group">
						<button id="rsepro-filter-btn" type="button" class="btn btn-primary"><?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_ADD_FILTER'); ?></button>
						<button id="rsepro-clear-btn" type="button" class="btn btn-danger"><?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_CLEAR_FILTER'); ?></button>
					</div>
				</li>
			</ul>
			<ul class="navbar-nav float-end">
				<li id="rsepro-filter-order" class="nav-item dropdown">
					<a data-bs-toggle="dropdown" class="nav-link active dropdown-toggle" href="#" rel="<?php echo $this->sortColumn; ?>"><span><?php echo $this->sortColumnText; ?></span> <i class="caret"></i></a>
					<div class="dropdown-menu">
						<?php foreach ($this->get('ordering') as $option) { ?>
						<a href="javascript:void(0);" class="dropdown-item" rel="<?php echo $option->value; ?>"><?php echo $option->text; ?></a>
						<?php } ?>
					</div>
				</li>
				<li id="rsepro-filter-order-dir" class="nav-item dropdown">
					<a data-bs-toggle="dropdown" class="nav-link active dropdown-toggle" href="#" rel="<?php echo $this->sortOrder; ?>"><span><?php echo $this->sortOrderText; ?></span> <i class="caret"></i></a>
					<div class="dropdown-menu">
						<?php foreach ($this->get('order') as $option) { ?>
						<a href="javascript:void(0);" class="dropdown-item" rel="<?php echo $option->value; ?>"><?php echo $option->text; ?></a>
						<?php } ?>
					</div>
				</li>
				
				<?php if ($this->tpl == 'general') { ?>
				<li class="nav-item">
					<?php echo $this->pagination->getLimitBox(); ?>
				</li>
				<?php } ?>
			</ul>
		</div>
	</div>
</nav>

<br />

<ul class="rsepro-filter-filters list-unstyled list-inline">
	<li class="list-inline-item rsepro-filter-operator" <?php echo $this->showCondition > 1 ? '' : 'style="display:none"'; ?>>
		<div class="btn-group">
			<a data-bs-toggle="dropdown" class="btn btn-primary btn-sm dropdown-toggle" href="#"><span><?php echo ucfirst(JText::_('COM_RSEVENTSPRO_GLOBAL_'.$this->operator)); ?></span> <i class="caret"></i></a>
			<div class="dropdown-menu">
				<a class="dropdown-item" href="javascript:void(0)" rel="AND"><?php echo ucfirst(JText::_('COM_RSEVENTSPRO_GLOBAL_AND')); ?></a>
				<a class="dropdown-item" href="javascript:void(0)" rel="OR"><?php echo ucfirst(JText::_('COM_RSEVENTSPRO_GLOBAL_OR')); ?></a>
			</div>
		</div>
		<input type="hidden" name="filter_operator" value="<?php echo $this->operator; ?>" />
	</li>
	
	<?php if (!is_null($statuses = $this->other['status'])) { ?>
	<?php foreach ($statuses as $status) { ?>
		<li class="list-inline-item rsepro-filter-option" id="<?php echo sha1('status'.$status); ?>">
			<div class="btn-group">
				<span class="btn btn-primary btn-sm"><?php echo JText::_('COM_RSEVENTSPRO_FILTER_STATUS'); ?></span>
				<span class="btn btn-primary btn-sm"><?php if ($status == 0) echo JText::_('JUNPUBLISHED'); elseif ($status == 1) echo JText::_('JPUBLISHED'); elseif ($status == 2) echo JText::_('JARCHIVED'); elseif ($status == 3) echo JText::_('COM_RSEVENTSPRO_CANCELED_STATUS'); ?></span>
				<input type="hidden" name="filter_status[]" value="<?php echo $this->escape($status); ?>">
				<a href="javascript:void(0)" class="btn btn-primary btn-sm rsepro-close">
					<i class="icon-delete"></i>&nbsp;
				</a>
			</div>
		</li>
		
		<li class="rsepro-filter-conditions list-inline-item" <?php echo $this->showCondition > 1 ? '' : 'style="display: none;"'; ?>>
			<a href="javascript:void(0)" class="btn btn-primary btn-sm"><?php echo ucfirst(JText::_('COM_RSEVENTSPRO_GLOBAL_'.$this->operator));?></a>
		</li>
	<?php } ?>
	<?php } ?>
	
	<?php if (!is_null($featured = $this->other['featured'])) { ?>
		<li id="<?php echo sha1('featured'); ?>" class="list-inline-item rsepro-filter-option">
			<div class="btn-group">
				<span class="btn btn-primary btn-sm"><?php echo JText::_('COM_RSEVENTSPRO_FILTER_FEATURED'); ?></span>
				<span class="btn btn-primary btn-sm"><?php echo $featured == 0 ? JText::_('JNO') : JText::_('JYES'); ?></span>
				<input type="hidden" name="filter_featured[]" value="<?php echo $this->escape($featured); ?>">
				<a href="javascript:void(0)" class="btn btn-primary btn-sm rsepro-close">
					<i class="icon-delete"></i>&nbsp;
				</a>
			</div>
		</li>
		
		<li class="rsepro-filter-conditions list-inline-item" <?php echo $this->showCondition > 1 ? '' : 'style="display: none;"'; ?>>
			<a href="javascript:void(0)" class="btn btn-primary btn-sm"><?php echo ucfirst(JText::_('COM_RSEVENTSPRO_GLOBAL_'.$this->operator));?></a>
		</li>
	<?php } ?>
	
	<?php if (!is_null($child = $this->other['childs'])) { ?>
		<li id="<?php echo sha1('child'); ?>" class="list-inline-item rsepro-filter-option">
			<div class="btn-group">
				<span class="btn btn-primary btn-sm"><?php echo JText::_('COM_RSEVENTSPRO_FILTER_CHILD'); ?></span>
				<span class="btn btn-primary btn-sm"><?php echo $child == 0 ? JText::_('JNO') : JText::_('JYES'); ?></span>
				<input type="hidden" name="filter_child[]" value="<?php echo $this->escape($child); ?>">
				<a href="javascript:void(0)" class="btn btn-primary btn-sm rsepro-close">
					<i class="icon-delete"></i>&nbsp;
				</a>
			</div>
		</li>
		
		<li class="rsepro-filter-conditions list-inline-item" <?php echo $this->showCondition > 1 ? '' : 'style="display: none;"'; ?>>
			<a href="javascript:void(0)" class="btn btn-primary btn-sm"><?php echo ucfirst(JText::_('COM_RSEVENTSPRO_GLOBAL_'.$this->operator));?></a>
		</li>
	<?php } ?>
	
	<?php if (!is_null($start = $this->other['start'])) { ?>
		<li id="<?php echo sha1('start_date'); ?>" class="list-inline-item rsepro-filter-option">
			<div class="btn-group">
				<span class="btn btn-primary btn-sm"><?php echo JText::_('COM_RSEVENTSPRO_FILTER_FROM'); ?></span>
				<span class="btn btn-primary btn-sm"><?php echo $start; ?></span>
				<input type="hidden" name="filter_start[]" value="<?php echo $this->escape($start); ?>">
				<a href="javascript:void(0)" class="btn btn-primary btn-sm rsepro-close">
					<i class="icon-delete"></i>&nbsp;
				</a>
			</div>
		</li>
		
		<li class="rsepro-filter-conditions list-inline-item" <?php echo $this->showCondition > 1 ? '' : 'style="display: none;"'; ?>>
			<a href="javascript:void(0)" class="btn btn-primary btn-sm"><?php echo ucfirst(JText::_('COM_RSEVENTSPRO_GLOBAL_'.$this->operator));?></a>
		</li>
	<?php } ?>
	
	<?php if (!is_null($end = $this->other['end'])) { ?>
		<li id="<?php echo sha1('end_date'); ?>" class="list-inline-item rsepro-filter-option">
			<div class="btn-group">
				<span class="btn btn-primary btn-sm"><?php echo JText::_('COM_RSEVENTSPRO_FILTER_TO'); ?></span>
				<span class="btn btn-primary btn-sm"><?php echo $end; ?></span>
				<input type="hidden" name="filter_end[]" value="<?php echo $this->escape($end); ?>">
				<a href="javascript:void(0)" class="btn btn-primary btn-sm rsepro-close">
					<i class="icon-delete"></i>&nbsp;
				</a>
			</div>
		</li>
		
		<li class="rsepro-filter-conditions list-inline-item" <?php echo $this->showCondition > 1 ? '' : 'style="display: none;"'; ?>>
			<a href="javascript:void(0)" class="btn btn-primary btn-sm"><?php echo ucfirst(JText::_('COM_RSEVENTSPRO_GLOBAL_'.$this->operator));?></a>
		</li>
	<?php } ?>
	
	<?php if (!empty($this->columns)) { ?>
	<?php for ($i=0; $i < count($this->columns); $i++) { ?>
		<?php $hash = sha1(@$this->columns[$i].@$this->operators[$i].@$this->values[$i]); ?>
		<li id="<?php echo $hash; ?>" class="list-inline-item rsepro-filter-option">
			<div class="btn-group">
				<span class="btn btn-primary btn-sm"><?php echo rseventsproHelper::translate($this->columns[$i]); ?></span>
				<span class="btn btn-primary btn-sm"><?php echo rseventsproHelper::translate($this->operators[$i]); ?></span>
				<span class="btn btn-primary btn-sm"><?php echo $this->escape($this->values[$i]); ?></span>
				<input type="hidden" name="filter_from[]" value="<?php echo $this->escape($this->columns[$i]); ?>">
				<input type="hidden" name="filter_condition[]" value="<?php echo $this->escape($this->operators[$i]); ?>">
				<input type="hidden" name="search[]" value="<?php echo $this->escape($this->values[$i]); ?>">
				<a href="javascript:void(0)" class="btn btn-primary btn-sm rsepro-close">
					<i class="icon-delete"></i>&nbsp;
				</a>
			</div>
		</li>
		
		<li class="rsepro-filter-conditions list-inline-item" <?php echo $i == (count($this->columns) - 1) ? 'style="display: none;"' : ''; ?>>
			<a href="javascript:void(0)" class="btn btn-primary btn-sm"><?php echo ucfirst(JText::_('COM_RSEVENTSPRO_GLOBAL_'.$this->operator));?></a>
		</li>
		
	<?php } ?>
	<?php } ?>
</ul>