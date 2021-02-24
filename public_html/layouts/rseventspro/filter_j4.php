<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2020 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

$view  = $displayData['view']; ?>

<div class="rsepro-filter-container">
	<nav class="navbar navbar-expand-lg navbar-light bg-light" id="rsepro-navbar">
		<div class="container-fluid">
		
			<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target=".rsepro-navbar-responsive-collapse" aria-expanded="false">
				<span class="navbar-toggler-icon"></span>
			</button>
			
			<div class="navbar-collapse collapse rsepro-navbar-responsive-collapse" id="rsepro-navbar-j4">
				<ul class="navbar-nav me-auto">
					<li id="rsepro-filter-from" class="nav-item dropdown">
						<a data-bs-toggle="dropdown" class="nav-link active dropdown-toggle" href="#" role="button" rel="<?php echo $view->config->filter_from; ?>"><span><?php echo rseventsproHelper::getFilterText($view->config->filter_from); ?></span> <i class="caret"></i></a>
						<div class="dropdown-menu">
							<?php foreach ($view->get('filteroptions') as $option) { ?>
							<?php if (!$view->maxPrice && $option->value == 'price') continue; ?>
							<a href="javascript:void(0);" class="dropdown-item" rel="<?php echo $option->value; ?>"><?php echo $option->text; ?></a>
							<?php } ?>
						</div>
					</li>
					<li id="rsepro-filter-condition" class="nav-item dropdown">
						<a data-bs-toggle="dropdown" class="nav-link active dropdown-toggle" href="#" role="button" rel="<?php echo $view->config->filter_condition; ?>"><span><?php echo rseventsproHelper::getFilterText($view->config->filter_condition); ?></span> <i class="caret"></i></a>
						<div class="dropdown-menu">
							<?php foreach ($view->get('filterconditions') as $option) { ?>
							<a href="javascript:void(0);" class="dropdown-item" rel="<?php echo $option->value; ?>"><?php echo $option->text; ?></a>
							<?php } ?>
						</div>
					</li>
					<li id="rsepro-search" class="navbar-search center">
						<input type="text" id="rsepro-filter" name="rsepro-filter" value="" size="35" class="form-control" />
					</li>
					<li id="rsepro-filter-featured" class="nav-item dropdown" style="display: none;">
						<a data-bs-toggle="dropdown" class="nav-link active dropdown-toggle" href="#" role="button" rel="1"><span><?php echo JText::_('JYES'); ?></span> <i class="caret"></i></a>
						<div class="dropdown-menu">
							<a href="javascript:void(0);" class="dropdown-item" rel="1"><?php echo JText::_('JYES'); ?></a>
							<a href="javascript:void(0);" class="dropdown-item" rel="0"><?php echo JText::_('JNO'); ?></a>
						</div>
					</li>
					<?php if ($view->maxPrice) { ?>
					<li id="rsepro-filter-price" class="nav-item dropdown" style="display: none;">
						<span id="price-field-min" class="label rsepro-min-price"><?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_FREE'); ?></span>
						<input id="price-field" type="text" value="" data-slider-min="0" data-slider-max="<?php echo $view->maxPrice; ?>" data-slider-step="1" data-slider-value="[0,<?php echo $view->maxPrice; ?>]" />
						<span id="price-field-max" class="label rsepro-max-price"><?php echo rseventsproHelper::currency($view->maxPrice, false, 0); ?></span> 
					</li>
					<?php } ?>
					<li class="divider-vertical"></li>
					<li class="nav-item ms-2">
						<div class="btn-group">
							<button id="rsepro-filter-btn" type="button" class="btn btn-primary"><?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_ADD_FILTER'); ?></button>
							<button id="rsepro-clear-btn" type="button" class="btn btn-danger"><?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_CLEAR_FILTER'); ?></button>
						</div>
					</li>
				</ul>
			</div>
		</div>
	</nav>
	
	<br />
	
	<ul class="rsepro-filter-filters list-unstyled list-inline">
		<li class="list-inline-item rsepro-filter-operator" <?php echo $view->showCondition > 1 ? '' : 'style="display:none"'; ?>>
			<div class="btn-group">
				<a data-bs-toggle="dropdown" class="btn btn-primary btn-sm dropdown-toggle" href="#" role="button"><span><?php echo ucfirst(JText::_('COM_RSEVENTSPRO_GLOBAL_'.$view->operator)); ?></span> <i class="caret"></i></a>
				<div class="dropdown-menu">
					<a class="dropdown-item" href="javascript:void(0)" rel="AND"><?php echo ucfirst(JText::_('COM_RSEVENTSPRO_GLOBAL_AND')); ?></a>
					<a class="dropdown-item" href="javascript:void(0)" rel="OR"><?php echo ucfirst(JText::_('COM_RSEVENTSPRO_GLOBAL_OR')); ?></a>
				</div>
			</div>
			<input type="hidden" name="filter_operator" value="<?php echo $view->operator; ?>" />
		</li>
		
		<?php if (!is_null($price = $view->extra['price'])) { ?>
			<li class="list-inline-item rsepro-filter-option" id="<?php echo sha1('price'); ?>">
				<?php list($min, $max) = explode(',',$price,2); ?>
				<div class="btn-group">
					<span class="btn btn-primary btn-sm"><?php echo JText::_('COM_RSEVENTSPRO_FILTER_PRICE'); ?></span>
					<span class="btn btn-primary btn-sm"><?php echo ($min == 0 ? JText::_('COM_RSEVENTSPRO_GLOBAL_FREE') : rseventsproHelper::currency($min, false, 0)).' - '.rseventsproHelper::currency($max, false, 0); ?></span>
					<input type="hidden" name="filter_price[]" value="<?php echo $view->escape($price); ?>">
					<a href="javascript:void(0)" class="btn btn-primary btn-sm rsepro-close">
						<i class="icon-delete"></i>
					</a>
				</div>
			</li>
			
			<li class="rsepro-filter-conditions list-inline-item" <?php echo $view->showCondition > 1 ? '' : 'style="display: none;"'; ?>>
				<a href="javascript:void(0)" class="btn btn-primary btn-sm"><?php echo ucfirst(JText::_('COM_RSEVENTSPRO_GLOBAL_'.$view->operator));?></a>
			</li>
		<?php } ?>
		
		<?php if (!is_null($featured = $view->extra['featured'])) { ?>
			<li class="list-inline-item rsepro-filter-option" id="<?php echo sha1('featured'); ?>">
				<div class="btn-group">
					<span class="btn btn-primary btn-sm"><?php echo JText::_('COM_RSEVENTSPRO_FILTER_FEATURED'); ?></span>
					<span class="btn btn-primary btn-sm"><?php echo $featured == 0 ? JText::_('JNO') : JText::_('JYES'); ?></span>
					<input type="hidden" name="filter_featured[]" value="<?php echo $view->escape($featured); ?>">
					<a href="javascript:void(0)" class="btn btn-primary btn-sm rsepro-close">
						<i class="icon-delete"></i>
					</a>
				</div>
			</li>
			
			<li class="rsepro-filter-conditions list-inline-item" <?php echo $view->showCondition > 1 ? '' : 'style="display: none;"'; ?>>
				<a href="javascript:void(0)" class="btn btn-primary btn-sm"><?php echo ucfirst(JText::_('COM_RSEVENTSPRO_GLOBAL_'.$view->operator));?></a>
			</li>
		<?php } ?>
		
		<?php if (!empty($view->columns)) { ?>
		<?php for ($i=0; $i < count($view->columns); $i++) { ?>
			<?php $hash = sha1(@$view->columns[$i].@$view->operators[$i].@$view->values[$i]); ?>
			<li class="list-inline-item rsepro-filter-option" id="<?php echo $hash; ?>">
				<div class="btn-group">
					<span class="btn btn-primary btn-sm"><?php echo rseventsproHelper::translate($view->columns[$i]); ?></span>
					<span class="btn btn-primary btn-sm"><?php echo rseventsproHelper::translate($view->operators[$i]); ?></span>
					<span class="btn btn-primary btn-sm"><?php echo $view->escape($view->values[$i]); ?></span>
					<input type="hidden" name="filter_from[]" value="<?php echo $view->escape($view->columns[$i]); ?>">
					<input type="hidden" name="filter_condition[]" value="<?php echo $view->escape($view->operators[$i]); ?>">
					<input type="hidden" name="search[]" value="<?php echo $view->escape($view->values[$i]); ?>">
					<a href="javascript:void(0)" class="btn btn-primary btn-sm rsepro-close">
						<i class="icon-delete"></i>
					</a>
				</div>
			</li>
			
			<li class="rsepro-filter-conditions list-inline-item" <?php echo $i == (count($view->columns) - 1) ? 'style="display: none;"' : ''; ?>>
				<div class="btn-group">
					<a href="javascript:void(0)" class="btn btn-primary btn-sm"><?php echo ucfirst(JText::_('COM_RSEVENTSPRO_GLOBAL_'.$view->operator));?></a>
				</div>
			</li>
			
		<?php } ?>
		<?php } ?>
	</ul>
	
	<input type="hidden" name="filter_from[]" value="">
	<input type="hidden" name="filter_condition[]" value="">
	<input type="hidden" name="search[]" value="">
	<input type="hidden" name="filter_featured[]" value="">
	<input type="hidden" name="filter_price[]" value="">
</div>