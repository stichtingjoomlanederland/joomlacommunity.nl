<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' );
$locations = count($this->events); ?>

<?php if ($this->params->get('show_page_heading', 1)) { ?>
<?php $title = $this->params->get('page_heading', ''); ?>
<h1><?php echo !empty($title) ? $this->escape($title) : JText::_('COM_RSEVENTSPRO_EVENTS_MAP'); ?></h1>
<?php } ?>

<?php if ($this->config->timezone) { ?>
<div class="rs_rss">
	<a href="#timezoneModal" data-toggle="modal" class="<?php echo rseventsproHelper::tooltipClass(); ?> rsepro-timezone" title="<?php echo rseventsproHelper::tooltipText(JText::_('COM_RSEVENTSPRO_CHANGE_TIMEZONE')); ?>">
		<i class="fa fa-clock-o"></i>
	</a>
</div>
<?php } ?>

<?php if (rseventsproHelper::getConfig('enable_google_maps','int')) { ?>
<script type="text/javascript">
var rsepromap;
jQuery(document).ready(function (){
	<?php if ($this->params->get('enable_radius', 0)) { ?>
	rsepromap = jQuery('#map-canvas').rsjoomlamap({
		zoom:				<?php echo (int) $this->config->google_map_zoom ?>,
		center:				'<?php echo $this->config->google_maps_center; ?>',
		radiusSearch:		1,
		radiusLocationId:	'rsepro-location',
		radiusValueId:		'rsepro-radius',
		radiusUnitId:		'rsepro-unit',
		radiusLoaderId: 	'rsepro-loader',
		radiusBtnId:	 	'rsepro-radius-search',
		use_geolocation:	<?php echo (int) $this->params->get('use_geolocation',0); ?>,
		circleColor:		'<?php echo $this->params->get('circle_color','#ff8080'); ?>',
		resultsWrapperClass:'rsepro-locations-results-wrapper',
		resultsClass:		'rsepro-locations-results'
	});
	<?php } else { ?>
	rsepromap = jQuery('#map-canvas').rsjoomlamap({
		zoom: <?php echo (int) $this->config->google_map_zoom ?>,
		center: '<?php echo $this->config->google_maps_center; ?>',
		markerDraggable: false,
		markers: [
			<?php 
				if ($locations) {
					$i = 0;
					foreach ($this->events as $location => $events) {
						if (empty($events)) continue;
						$event = $events[0];
						if (empty($event->coordinates) && empty($event->address)) continue;
						$single = count($events) > 1 ? false : true;
			?>
			{
				title : '<?php echo addslashes($event->name); ?>',
				position: '<?php echo addslashes($event->coordinates); ?>',
				address: '<?php echo addslashes($event->address); ?>',
				content: '<?php echo rseventsproHelper::locationContent($event, $single); ?>'
			}
			
			<?php 
				$i++;
				if ($locations > $i) echo ','; 
			?>
			
			<?php 
					}
				}
			?>
		]
	});
	<?php } ?>
});
</script>
<?php } ?>

<?php if ($this->params->get('search',1)) { ?>
<form method="post" action="<?php echo $this->escape(JRoute::_(JURI::getInstance(),false)); ?>" name="adminForm" id="adminForm">
	<div class="rsepro-filter-container">
		<div class="navbar" id="rsepro-navbar">
			<div class="navbar-inner">
				<a data-target=".rsepro-navbar-responsive-collapse" data-toggle="collapse" class="btn btn-navbar">
					<i class="icon-bar"></i>
					<i class="icon-bar"></i>
					<i class="icon-bar"></i>
				</a>
				<div class="nav-collapse collapse rsepro-navbar-responsive-collapse">
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
			<li class="rsepro-filter-operator" <?php echo count($this->columns) > 1 ? '' : 'style="display:none"'; ?>>
				<div class="btn-group">
					<a data-toggle="dropdown" class="btn btn-small dropdown-toggle" href="#"><span><?php echo ucfirst(JText::_('COM_RSEVENTSPRO_GLOBAL_'.$this->operator)); ?></span> <i class="caret"></i></a>
					<ul class="dropdown-menu">
						<li><a href="javascript:void(0)" rel="AND"><?php echo ucfirst(JText::_('COM_RSEVENTSPRO_GLOBAL_AND')); ?></a></li>
						<li><a href="javascript:void(0)" rel="OR"><?php echo ucfirst(JText::_('COM_RSEVENTSPRO_GLOBAL_OR')); ?></a></li>
					</ul>
				</div>
				<input type="hidden" name="filter_operator" value="<?php echo $this->operator; ?>" />
			</li>
			
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
							<i class="icon-delete"></i>
						</a>
					</div>
				</li>
				
				<li class="rsepro-filter-conditions" <?php echo $i == (count($this->columns) - 1) ? 'style="display: none;"' : ''; ?>>
					<a class="btn btn-small"><?php echo ucfirst(JText::_('COM_RSEVENTSPRO_GLOBAL_'.$this->operator));?></a>
				</li>
				
			<?php } ?>
			<?php } ?>
		</ul>
		
		<input type="hidden" name="filter_from[]" value="">
		<input type="hidden" name="filter_condition[]" value="">
		<input type="hidden" name="search[]" value="">
	</div>
</form>
<?php } else { ?>
<?php if (!empty($this->columns)) { ?>
<a href="<?php echo rseventsproHelper::route('index.php?option=com_rseventspro&task=clear&from=map'); ?>" class="rs_filter_clear"><?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_CLEAR_FILTER'); ?></a>
<div class="rs_clear"></div>
<?php } ?>
<?php } ?>

<?php if ($this->params->get('enable_radius', 0)) { ?>

<div class="control-group">
	<div class="control-label">
		<label for="rsepro-location"><?php echo JText::_('COM_RSEVENTSPRO_MAP_LOCATION'); ?></label>
	</div>
	<div class="controls">
		<input id="rsepro-location" class="input-xxlarge" type="text" name="location" value="<?php echo $this->escape($this->location); ?>" autocomplete="off" placeholder="<?php echo JText::_('COM_RSEVENTSPRO_MAP_LOCATION'); ?>" />
	</div>
</div>

<div class="control-group">
	<div class="control-label">
		<label for="rsepro-radius"><?php echo JText::_('COM_RSEVENTSPRO_MAP_RADIUS'); ?></label>
	</div>
	<div class="controls">
		<input id="rsepro-radius" class="input-mini" type="text" name="radius" value="<?php echo $this->escape($this->radius); ?>" placeholder="<?php echo JText::_('COM_RSEVENTSPRO_MAP_RADIUS'); ?>" />
		<select id="rsepro-unit" class="input-mini" name="unit">
			<option value="km"><?php echo JText::_('COM_RSEVENTSPRO_MAP_KM'); ?></option>
			<option value="miles"><?php echo JText::_('COM_RSEVENTSPRO_MAP_MILES'); ?></option>
		</select>
	</div>
</div>

<div class="control-group">
	<div class="controls">
		<button class="btn btn-primary" type="button" id="rsepro-radius-search">
			<i class="fa fa-search"></i> <?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_SEARCH'); ?>
		</button>
		<img id="rsepro-loader" src="<?php echo JURI::root(); ?>components/com_rseventspro/assets/images/loader.gif" width="16" height="16" alt="" style="display: none;" />
	</div>
</div>
<?php } ?>

<?php if (rseventsproHelper::getConfig('enable_google_maps','int')) { ?>
<div id="map-canvas" style="width: <?php echo $this->escape($this->width); ?>; height: <?php echo $this->escape($this->height); ?>"></div>
<?php if ($this->params->get('enable_radius', 0) && $this->params->get('display_results', 1)) { ?>
<table id="rsepro-map-results-table" class="table table-striped" style="display: none;">
	<tbody id="rsepro-map-results"></tbody>
</table>
<?php } ?>
<?php } else { ?>
<div class="alert alert-danger">
	<a class="close" data-dismiss="alert" href="#">&times;</a>
	<?php echo JText::_('COM_RSEVENTSPRO_EVENTS_MAP_OFF'); ?>
</div>
<?php } ?>

<span id="rsepro-itemid" style="display: none;"><?php echo JFactory::getApplication()->input->get('Itemid'); ?></span>

<?php if ($this->config->timezone) { ?>
<div id="timezoneModal" class="modal hide fade" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
		<h3><?php echo JText::_('COM_RSEVENTSPRO_CHANGE_TIMEZONE'); ?></h3>
	</div>
	<div class="modal-body">
		<form method="post" action="<?php echo htmlentities(JUri::getInstance(), ENT_COMPAT, 'UTF-8'); ?>" id="timezoneForm" name="timezoneForm" class="form-horizontal">
			<div class="control-group">
				<div class="control-label">
					<label><?php echo JText::_('COM_RSEVENTSPRO_DEFAULT_TIMEZONE'); ?></label>
				</div>
				<div class="controls">
					<span class="btn disabled"><?php echo $this->timezone; ?></span>
				</div>
			</div>
			<div class="control-group">
				<div class="control-label">
					<label for="timezone"><?php echo JText::_('COM_RSEVENTSPRO_SELECT_TIMEZONE'); ?></label>
				</div>
				<div class="controls">
					<?php echo JHtml::_('rseventspro.timezones','timezone'); ?>
				</div>
			</div>
			<input type="hidden" name="task" value="timezone" />
			<input type="hidden" name="return" value="<?php echo $this->timezoneReturn; ?>" />
		</form>
	</div>
	<div class="modal-footer">
		<button class="btn" data-dismiss="modal" aria-hidden="true"><?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_CANCEL'); ?></button>
		<button class="btn btn-primary" type="button" onclick="document.timezoneForm.submit();"><?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_SAVE'); ?></button>
	</div>
</div>
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