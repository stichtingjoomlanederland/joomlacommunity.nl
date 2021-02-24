<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2020 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' ); 
JText::script('COM_RSEVENTSPRO_GLOBAL_FREE'); ?>

<script type="text/javascript">
	var rseproMask 		= '<?php echo $this->escape($this->mask); ?>';
	var rseproCurrency  = '<?php echo $this->escape($this->currency); ?>';
	var rseproDecimals	= '<?php echo $this->escape($this->decimals); ?>';
	var rseproDecimal 	= '<?php echo $this->escape($this->decimal); ?>';
	var rseproThousands	= '<?php echo $this->escape($this->thousands); ?>';
</script>

<?php if ($this->params->get('show_page_heading', 1)) { ?>
<?php $title = $this->params->get('page_heading', ''); ?>
<h1><?php echo !empty($title) ? $this->escape($title) : JText::_('COM_RSEVENTSPRO_EVENTS_MAP'); ?></h1>
<?php } ?>

<?php if ($this->config->timezone) { ?>
<div class="rs_rss">
	<a rel="rs_timezone" <?php if (rseventsproHelper::getConfig('modaltype','int') == 1) echo ' href="#timezoneModal" data-toggle="modal" data-bs-toggle="modal"'; else echo ' href="javascript:void(0)"'; ?> class="<?php echo rseventsproHelper::tooltipClass(); ?> rsepro-timezone" title="<?php echo rseventsproHelper::tooltipText(JText::_('COM_RSEVENTSPRO_CHANGE_TIMEZONE')); ?>">
		<i class="fa fa-clock-o"></i>
	</a>
</div>
<?php } ?>

<?php if ($this->params->get('search',1)) { ?>
<form method="post" action="<?php echo $this->escape(JRoute::_(JURI::getInstance(),false)); ?>" name="adminForm" id="adminForm">
	<?php echo JLayoutHelper::render('rseventspro.filter_'.(rseventsproHelper::isJ4() ? 'j4' : 'j3'), array('view' => $this)); ?>
</form>
<?php } else { ?>
<?php if (!empty($this->columns)) { ?>
<a href="<?php echo rseventsproHelper::route('index.php?option=com_rseventspro&task=clear&from=map'); ?>" class="rs_filter_clear"><?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_CLEAR_FILTER'); ?></a>
<div class="rs_clear"></div>
<?php } ?>
<?php } ?>

<?php if ($this->params->get('enable_radius', 0)) { ?>
<div class="form-horizontal rsepro-horizontal">
	<?php echo RSEventsproAdapterGrid::renderField('<label for="rsepro-location">'.JText::_('COM_RSEVENTSPRO_MAP_LOCATION').'</label>', '<input id="rsepro-location" class="form-control" type="text" name="location" value="'.$this->escape($this->location).'" autocomplete="off" placeholder="'.JText::_('COM_RSEVENTSPRO_MAP_LOCATION').'" />'); ?>
	<?php $input = RSEventsproAdapterGrid::inputGroup('<input id="rsepro-radius" class="form-control" type="text" name="radius" value="'.$this->escape($this->radius).'" placeholder="'.JText::_('COM_RSEVENTSPRO_MAP_RADIUS').'" />', null, '<select id="rsepro-unit" class="input-mini custom-select" name="unit"><option value="km">'.JText::_('COM_RSEVENTSPRO_MAP_KM').'</option><option value="miles">'.JText::_('COM_RSEVENTSPRO_MAP_MILES').'</option></select>'); ?>
	<?php echo RSEventsproAdapterGrid::renderField('<label for="rsepro-radius">'.JText::_('COM_RSEVENTSPRO_MAP_RADIUS').'</label>', $input); ?>
	<?php echo RSEventsproAdapterGrid::renderField('', '<button class="btn btn-primary" type="button" id="rsepro-radius-search"> <i class="fa fa-search"></i> '.JText::_('COM_RSEVENTSPRO_GLOBAL_SEARCH').'</button> '.JHtml::image('com_rseventspro/loader.gif', '', array('id' => 'rsepro-loader', 'style' => 'display: none;'), true)); ?>
</div>
<?php } ?>

<?php if (!empty($this->config->map)) { ?>
<div id="map-canvas" style="width: <?php echo $this->escape($this->width); ?>; height: <?php echo $this->escape($this->height); ?>"></div>
<?php if ($this->params->get('enable_radius', 0) && $this->params->get('display_results', 1)) { ?>
<table id="rsepro-map-results-table" class="table table-striped table-bordered" style="display: none;">
	<tbody id="rsepro-map-results"></tbody>
</table>
<?php } ?>
<?php } else { ?>
<div class="alert alert-danger">
	<a class="close" data-dismiss="alert" data-bs-dismiss="alert" href="#">&times;</a>
	<?php echo JText::_('COM_RSEVENTSPRO_EVENTS_MAP_OFF'); ?>
</div>
<?php } ?>

<span id="rsepro-itemid" style="display: none;"><?php echo JFactory::getApplication()->input->get('Itemid'); ?></span>

<?php if ($this->config->timezone) { ?>
<?php echo rseventsproHelper::timezoneModal(); ?>
<?php } ?>

<?php if ($this->params->get('search',1)) { ?>
<script type="text/javascript">
	jQuery(document).ready(function(){
		var options = {};
		options.condition = '.rsepro-filter-operator';
		options.events = [{'#rsepro-filter-from' : 'rsepro_select'}];
		
		jQuery().rsjoomlafilter(options);	
	});
</script>
<?php } ?>