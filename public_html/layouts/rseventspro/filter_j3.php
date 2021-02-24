<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2020 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

$view  = $displayData['view']; ?>

<div class="rsepro-filter-container">
	<div class="navbar" id="rsepro-navbar">
		<div class="navbar-inner">
			<a data-target=".rsepro-navbar-responsive-collapse" data-toggle="collapse" class="btn btn-navbar collapsed">
				<i class="icon-bar"></i>
				<i class="icon-bar"></i>
				<i class="icon-bar"></i>
			</a>
			<div class="nav-collapse collapse rsepro-navbar-responsive-collapse">
				<ul class="nav">
					<li id="rsepro-filter-from" class="dropdown">
						<a data-toggle="dropdown" class="dropdown-toggle" href="#" rel="<?php echo $view->config->filter_from; ?>"><span><?php echo rseventsproHelper::getFilterText($view->config->filter_from); ?></span> <i class="caret"></i></a>
						<ul class="dropdown-menu">
							<?php foreach ($view->get('filteroptions') as $option) { ?>
							<?php if (!$view->maxPrice && $option->value == 'price') continue; ?>
							<li><a href="javascript:void(0);" rel="<?php echo $option->value; ?>"><?php echo $option->text; ?></a></li>
							<?php } ?>
						</ul>
					</li>
					<li id="rsepro-filter-condition" class="dropdown">
						<a data-toggle="dropdown" class="dropdown-toggle" href="#" rel="<?php echo $view->config->filter_condition; ?>"><span><?php echo rseventsproHelper::getFilterText($view->config->filter_condition); ?></span> <i class="caret"></i></a>
						<ul class="dropdown-menu">
							<?php foreach ($view->get('filterconditions') as $option) { ?>
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
					<?php if ($view->maxPrice) { ?>
					<li id="rsepro-filter-price" class="dropdown" style="display: none;">
						<span id="price-field-min" class="label rsepro-min-price"><?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_FREE'); ?></span>
						<input id="price-field" type="text" value="" data-slider-min="0" data-slider-max="<?php echo $view->maxPrice; ?>" data-slider-step="1" data-slider-value="[0,<?php echo $view->maxPrice; ?>]" />
						<span id="price-field-max" class="label rsepro-max-price"><?php echo rseventsproHelper::currency($view->maxPrice, false, 0); ?></span> 
					</li>
					<?php } ?>
					<li class="divider-vertical"></li>
					<li class="center">
						<div class="btn-group">
							<button id="rsepro-filter-btn" type="button" class="btn btn-primary"><?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_ADD_FILTER'); ?></button>
							<button id="rsepro-clear-btn" type="button" class="btn"><?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_CLEAR_FILTER'); ?></button>
						</div>
					</li>
				</ul>
			</div>
		</div>
	</div>
	
	<ul class="rsepro-filter-filters inline unstyled">
		<li class="rsepro-filter-operator" <?php echo $view->showCondition > 1 ? '' : 'style="display:none"'; ?>>
			<div class="btn-group">
				<a data-toggle="dropdown" class="btn btn-small dropdown-toggle" href="#"><span><?php echo ucfirst(JText::_('COM_RSEVENTSPRO_GLOBAL_'.$view->operator)); ?></span> <i class="caret"></i></a>
				<ul class="dropdown-menu">
					<li><a href="javascript:void(0)" rel="AND"><?php echo ucfirst(JText::_('COM_RSEVENTSPRO_GLOBAL_AND')); ?></a></li>
					<li><a href="javascript:void(0)" rel="OR"><?php echo ucfirst(JText::_('COM_RSEVENTSPRO_GLOBAL_OR')); ?></a></li>
				</ul>
			</div>
			<input type="hidden" name="filter_operator" value="<?php echo $view->operator; ?>" />
		</li>
		
		<?php if (!is_null($price = $view->extra['price'])) { ?>
			<li id="<?php echo sha1('price'); ?>">
				<?php list($min, $max) = explode(',',$price,2); ?>
				<div class="btn-group">
					<span class="btn btn-small"><?php echo JText::_('COM_RSEVENTSPRO_FILTER_PRICE'); ?></span>
					<span class="btn btn-small"><?php echo ($min == 0 ? JText::_('COM_RSEVENTSPRO_GLOBAL_FREE') : rseventsproHelper::currency($min, false, 0)).' - '.rseventsproHelper::currency($max, false, 0); ?></span>
					<input type="hidden" name="filter_price[]" value="<?php echo $view->escape($price); ?>">
					<a href="javascript:void(0)" class="btn btn-small rsepro-close">
						<i class="icon-delete"></i>
					</a>
				</div>
			</li>
			
			<li class="rsepro-filter-conditions" <?php echo $view->showCondition > 1 ? '' : 'style="display: none;"'; ?>>
				<a class="btn btn-small"><?php echo ucfirst(JText::_('COM_RSEVENTSPRO_GLOBAL_'.$view->operator));?></a>
			</li>
		<?php } ?>
		
		<?php if (!is_null($featured = $view->extra['featured'])) { ?>
			<li id="<?php echo sha1('featured'); ?>">
				<div class="btn-group">
					<span class="btn btn-small"><?php echo JText::_('COM_RSEVENTSPRO_FILTER_FEATURED'); ?></span>
					<span class="btn btn-small"><?php echo $featured == 0 ? JText::_('JNO') : JText::_('JYES'); ?></span>
					<input type="hidden" name="filter_featured[]" value="<?php echo $view->escape($featured); ?>">
					<a href="javascript:void(0)" class="btn btn-small rsepro-close">
						<i class="icon-delete"></i>
					</a>
				</div>
			</li>
			
			<li class="rsepro-filter-conditions" <?php echo $view->showCondition > 1 ? '' : 'style="display: none;"'; ?>>
				<a class="btn btn-small"><?php echo ucfirst(JText::_('COM_RSEVENTSPRO_GLOBAL_'.$view->operator));?></a>
			</li>
		<?php } ?>
		
		<?php if (!empty($view->columns)) { ?>
		<?php for ($i=0; $i < count($view->columns); $i++) { ?>
			<?php $hash = sha1(@$view->columns[$i].@$view->operators[$i].@$view->values[$i]); ?>
			<li id="<?php echo $hash; ?>">
				<div class="btn-group">
					<span class="btn btn-small"><?php echo rseventsproHelper::translate($view->columns[$i]); ?></span>
					<span class="btn btn-small"><?php echo rseventsproHelper::translate($view->operators[$i]); ?></span>
					<span class="btn btn-small"><?php echo $view->escape($view->values[$i]); ?></span>
					<input type="hidden" name="filter_from[]" value="<?php echo $view->escape($view->columns[$i]); ?>">
					<input type="hidden" name="filter_condition[]" value="<?php echo $view->escape($view->operators[$i]); ?>">
					<input type="hidden" name="search[]" value="<?php echo $view->escape($view->values[$i]); ?>">
					<a href="javascript:void(0)" class="btn btn-small rsepro-close">
						<i class="icon-delete"></i>
					</a>
				</div>
			</li>
			
			<li class="rsepro-filter-conditions" <?php echo $i == (count($view->columns) - 1) ? 'style="display: none;"' : ''; ?>>
				<a class="btn btn-small"><?php echo ucfirst(JText::_('COM_RSEVENTSPRO_GLOBAL_'.$view->operator));?></a>
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