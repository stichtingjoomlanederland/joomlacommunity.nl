<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');
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
<h1><?php echo !empty($title) ? $this->escape($title) : JText::_('COM_RSEVENTSPRO_EVENTS'); ?></h1>
<?php } ?>

<?php if ($this->params->get('show_category_title', 0) && $this->category) { ?>
<h2>
	<span class="subheading-category"><?php echo $this->category->title; ?></span>
</h2>
<?php } ?>

<?php if (($this->params->get('show_category_description', 0) || $this->params->def('show_category_image', 0)) && $this->category) { ?>
	<div class="category-desc">
	<?php if ($this->params->get('show_category_image') && $this->category->getParams()->get('image')) { ?>
		<img src="<?php echo $this->category->getParams()->get('image'); ?>" alt="" />
	<?php } ?>
	<?php if ($this->params->get('show_category_description') && $this->category->description) { ?>
		<?php echo JHtml::_('content.prepare', $this->category->description, '', 'com_content.category'); ?>
	<?php } ?>
	<div class="clr"></div>
	</div>
<?php } ?>

<div class="rs_rss">
	<?php JFactory::getApplication()->triggerEvent('rsepro_showCartIcon'); ?>
	<?php $rss = $this->params->get('rss',1); ?>
	<?php $ical = $this->params->get('ical',1); ?>
	
	<a href="<?php echo rseventsproHelper::route('index.php?option=com_rseventspro&layout=subscriptions'); ?>" class="<?php echo rseventsproHelper::tooltipClass(); ?>" title="<?php echo rseventsproHelper::tooltipText(JText::_('COM_RSEVENTSPRO_USER_SUBSCRIPTIONS')); ?>">
		<i class="fa fa-user"></i>
	</a>
	
	<?php if ($rss || $ical || $this->config->timezone) { ?>
	<?php if ($this->config->timezone) { ?>
	<a href="#timezoneModal" data-toggle="modal" class="<?php echo rseventsproHelper::tooltipClass(); ?> rsepro-timezone" title="<?php echo rseventsproHelper::tooltipText(JText::_('COM_RSEVENTSPRO_CHANGE_TIMEZONE')); ?>">
		<i class="fa fa-clock-o"></i>
	</a>
	<?php } ?>
	
	<?php if ($rss) { ?>
	<a href="<?php echo rseventsproHelper::route('index.php?option=com_rseventspro&format=feed&type=rss'); ?>" class="<?php echo rseventsproHelper::tooltipClass(); ?> rsepro-rss" title="<?php echo rseventsproHelper::tooltipText(JText::_('COM_RSEVENTSPRO_RSS')); ?>">
		<i class="fa fa-rss-square"></i>
	</a>
	<?php } ?>
	<?php if ($ical) { ?>
	<a href="<?php echo rseventsproHelper::route('index.php?option=com_rseventspro&format=raw&type=ical'); ?>" class="<?php echo rseventsproHelper::tooltipClass(); ?> rsepro-ical" title="<?php echo rseventsproHelper::tooltipText(JText::_('COM_RSEVENTSPRO_ICS')); ?>">
		<i class="fa fa-calendar"></i>
	</a>
	<?php } ?>
	<?php } ?>
</div>

<?php if ($this->params->get('search',1)) { ?>
<form method="post" action="<?php echo $this->escape(JRoute::_(JURI::getInstance(),false)); ?>" name="adminForm" id="adminForm">
	
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
							<a data-toggle="dropdown" class="dropdown-toggle" href="#" rel="<?php echo $this->config->filter_from; ?>"><span><?php echo rseventsproHelper::getFilterText($this->config->filter_from); ?></span> <i class="caret"></i></a>
							<ul class="dropdown-menu">
								<?php foreach ($this->get('filteroptions') as $option) { ?>
								<?php if (!$this->maxPrice && $option->value == 'price') continue; ?>
								<li><a href="javascript:void(0);" rel="<?php echo $option->value; ?>"><?php echo $option->text; ?></a></li>
								<?php } ?>
							</ul>
						</li>
						<li id="rsepro-filter-condition" class="dropdown">
							<a data-toggle="dropdown" class="dropdown-toggle" href="#" rel="<?php echo $this->config->filter_condition; ?>"><span><?php echo rseventsproHelper::getFilterText($this->config->filter_condition); ?></span> <i class="caret"></i></a>
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
						<?php if ($this->maxPrice) { ?>
						<li id="rsepro-filter-price" class="dropdown" style="display: none;">
							<span id="price-field-min" class="label rsepro-min-price"><?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_FREE'); ?></span>
							<input id="price-field" type="text" value="" data-slider-min="0" data-slider-max="<?php echo $this->maxPrice; ?>" data-slider-step="1" data-slider-value="[0,<?php echo $this->maxPrice; ?>]" />
							<span id="price-field-max" class="label rsepro-max-price"><?php echo rseventsproHelper::currency($this->maxPrice, false, 0); ?></span> 
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
			
			<?php if (!is_null($price = $this->extra['price'])) { ?>
				<li id="<?php echo sha1('price'); ?>">
					<?php list($min, $max) = explode(',',$price,2); ?>
					<div class="btn-group">
						<span class="btn btn-small"><?php echo JText::_('COM_RSEVENTSPRO_FILTER_PRICE'); ?></span>
						<span class="btn btn-small"><?php echo ($min == 0 ? JText::_('COM_RSEVENTSPRO_GLOBAL_FREE') : rseventsproHelper::currency($min, false, 0)).' - '.rseventsproHelper::currency($max, false, 0); ?></span>
						<input type="hidden" name="filter_price[]" value="<?php echo $this->escape($price); ?>">
						<a href="javascript:void(0)" class="btn btn-small rsepro-close">
							<i class="icon-delete"></i>
						</a>
					</div>
				</li>
				
				<li class="rsepro-filter-conditions" <?php echo $this->showCondition > 1 ? '' : 'style="display: none;"'; ?>>
					<a class="btn btn-small"><?php echo ucfirst(JText::_('COM_RSEVENTSPRO_GLOBAL_'.$this->operator));?></a>
				</li>
			<?php } ?>
			
			<?php if (!is_null($featured = $this->extra['featured'])) { ?>
				<li id="<?php echo sha1('featured'); ?>">
					<div class="btn-group">
						<span class="btn btn-small"><?php echo JText::_('COM_RSEVENTSPRO_FILTER_FEATURED'); ?></span>
						<span class="btn btn-small"><?php echo $featured == 0 ? JText::_('JNO') : JText::_('JYES'); ?></span>
						<input type="hidden" name="filter_featured[]" value="<?php echo $this->escape($featured); ?>">
						<a href="javascript:void(0)" class="btn btn-small rsepro-close">
							<i class="icon-delete"></i>
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
		<input type="hidden" name="filter_featured[]" value="">
		<input type="hidden" name="filter_price[]" value="">
	</div>
</form>
<?php } else { ?>
<?php if (!empty($this->columns)) { ?>
<a href="<?php echo rseventsproHelper::route('index.php?option=com_rseventspro&task=clear'); ?>" class="rs_filter_clear"><?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_CLEAR_FILTER'); ?></a>
<div class="rs_clear"></div>
<?php } ?>
<?php } ?>

<?php $count = count($this->events); ?>
<?php if (!empty($this->events)) { ?>
<ul class="rs_events_container" id="rs_events_container">
	
	<?php $eventIds = rseventsproHelper::getEventIds($this->events, 'id'); ?>
	<?php $this->events = rseventsproHelper::details($eventIds); ?>
	<?php foreach($this->events as $details) { ?>
	<?php if (isset($details['event']) && !empty($details['event'])) $event = $details['event']; else continue; ?>
	<?php if (!rseventsproHelper::canview($event->id) && $event->owner != $this->user) continue; ?>
	<?php $full = rseventsproHelper::eventisfull($event->id); ?>
	<?php $ongoing = rseventsproHelper::ongoing($event->id); ?>
	<?php $categories = (isset($details['categories']) && !empty($details['categories'])) ? JText::_('COM_RSEVENTSPRO_GLOBAL_CATEGORIES').': '.$details['categories'] : '';  ?>
	<?php $tags = (isset($details['tags']) && !empty($details['tags'])) ? JText::_('COM_RSEVENTSPRO_GLOBAL_TAGS').': '.$details['tags'] : '';  ?>
	<?php $incomplete = !$event->completed ? ' rs_incomplete' : ''; ?>
	<?php $featured = $event->featured ? ' rs_featured' : ''; ?>
	<?php $repeats = rseventsproHelper::getRepeats($event->id); ?>
	<?php $lastMY = rseventsproHelper::showdate($event->start,'mY'); ?>
	
	<?php if ($monthYear = rseventsproHelper::showMonthYear($event->start, 'events'.$this->fid)) { ?>
		<li class="rsepro-month-year"><span><?php echo $monthYear; ?></span></li>
	<?php } ?>
	
	<li class="rs_event_detail<?php echo $incomplete.$featured; ?>" id="rs_event<?php echo $event->id; ?>" itemscope itemtype="http://schema.org/Event">
		
		<div class="rs_options" style="display:none;">
			<?php if ((!empty($this->permissions['can_edit_events']) || $event->owner == $this->user || $event->sid == $this->user || $this->admin) && !empty($this->user)) { ?>
				<a href="<?php echo rseventsproHelper::route('index.php?option=com_rseventspro&layout=edit&id='.rseventsproHelper::sef($event->id,$event->name)); ?>">
					<i class="fa fa-pencil"></i>
				</a>
			<?php } ?>
			<?php if ((!empty($this->permissions['can_delete_events']) || $event->owner == $this->user || $event->sid == $this->user || $this->admin) && !empty($this->user)) { ?>
				<a href="<?php echo rseventsproHelper::route('index.php?option=com_rseventspro&task=rseventspro.remove&id='.rseventsproHelper::sef($event->id,$event->name)); ?>" onclick="return confirm('<?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_DELETE_CONFIRMATION'); ?>');">
					<i class="fa fa-trash"></i>
				</a>
			<?php } ?>
		</div>
		
		<?php if (!empty($event->options['show_icon_list'])) { ?>
		<div class="rs_event_image" itemprop="image">
			<a href="<?php echo rseventsproHelper::route('index.php?option=com_rseventspro&layout=show&id='.rseventsproHelper::sef($event->id,$event->name),false,rseventsproHelper::itemid($event->id)); ?>" class="rs_event_link thumbnail">
				<img src="<?php echo rseventsproHelper::thumb($event->id, $this->config->icon_small_width); ?>" alt="" width="<?php echo $this->config->icon_small_width; ?>" />
			</a>
		</div>
		<?php } ?>
		
		<div class="rs_event_details">
			<div itemprop="name" class="rsepro-title-block">
				<a itemprop="url" href="<?php echo rseventsproHelper::route('index.php?option=com_rseventspro&layout=show&id='.rseventsproHelper::sef($event->id,$event->name),false,rseventsproHelper::itemid($event->id)); ?>" class="rs_event_link<?php echo $full ? ' rs_event_full' : ''; ?><?php echo $ongoing ? ' rs_event_ongoing' : ''; ?>"><?php echo $event->name; ?></a> <?php if (!$event->completed) echo JText::_('COM_RSEVENTSPRO_GLOBAL_INCOMPLETE_EVENT'); ?> <?php if (!$event->published) echo JText::_('COM_RSEVENTSPRO_GLOBAL_UNPUBLISHED_EVENT'); ?>
			</div>
			<div class="rsepro-date-block">
				<?php if ($event->allday) { ?>
				<?php if (!empty($event->options['start_date_list'])) { ?>
				<span class="rsepro-event-on-block">
				<?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_ON'); ?> <b><?php echo rseventsproHelper::showdate($event->start,$this->config->global_date,true); ?></b>
				</span>
				<?php } ?>
				<?php } else { ?>
				
				<?php if (!empty($event->options['start_date_list']) || !empty($event->options['start_time_list']) || !empty($event->options['end_date_list']) || !empty($event->options['end_time_list'])) { ?>
				<?php if (!empty($event->options['start_date_list']) || !empty($event->options['start_time_list'])) { ?>
				<?php if ((!empty($event->options['start_date_list']) || !empty($event->options['start_time_list'])) && empty($event->options['end_date_list']) && empty($event->options['end_time_list'])) { ?>
				<span class="rsepro-event-starting-block">
				<?php echo JText::_('COM_RSEVENTSPRO_EVENT_STARTING_ON'); ?>
				<?php } else { ?>
				<span class="rsepro-event-from-block">
				<?php echo JText::_('COM_RSEVENTSPRO_EVENT_FROM'); ?> 
				<?php } ?>
				<b><?php echo rseventsproHelper::showdate($event->start,rseventsproHelper::showMask('list_start',$event->options),true); ?></b>
				</span>
				<?php } ?>
				<?php if (!empty($event->options['end_date_list']) || !empty($event->options['end_time_list'])) { ?>
				<?php if ((!empty($event->options['end_date_list']) || !empty($event->options['end_time_list'])) && empty($event->options['start_date_list']) && empty($event->options['start_time_list'])) { ?>
				<span class="rsepro-event-ending-block">
				<?php echo JText::_('COM_RSEVENTSPRO_EVENT_ENDING_ON'); ?>
				<?php } else { ?>
				<span class="rsepro-event-until-block">
				<?php echo JText::_('COM_RSEVENTSPRO_EVENT_UNTIL'); ?>
				<?php } ?>
				<b><?php echo rseventsproHelper::showdate($event->end,rseventsproHelper::showMask('list_end',$event->options),true); ?></b>
				</span>
				<?php } ?>
				<?php } ?>
				
				<?php } ?>
			</div>
			
			<?php if (!empty($event->options['show_location_list']) || !empty($event->options['show_categories_list']) || !empty($event->options['show_tags_list'])) { ?>
			<div class="rsepro-event-taxonomies-block">
				<?php if ($event->locationid && $event->lpublished && !empty($event->options['show_location_list'])) { ?>
				<span class="rsepro-event-location-block" itemprop="location" itemscope itemtype="http://schema.org/Place"><?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_AT'); ?> <a itemprop="url" href="<?php echo rseventsproHelper::route('index.php?option=com_rseventspro&layout=location&id='.rseventsproHelper::sef($event->locationid,$event->location)); ?>"><span itemprop="name"><?php echo $event->location; ?></span></a>
				<span itemprop="address" style="display:none;"><?php echo $event->address; ?></span>
				</span> 
				<?php } ?>
				<?php if (!empty($event->options['show_categories_list'])) { ?>
				<span class="rsepro-event-categories-block"><?php echo $categories; ?></span> 
				<?php } ?>
				<?php if (!empty($event->options['show_tags_list'])) { ?>
				<span class="rsepro-event-tags-block"><?php echo $tags; ?></span> 
				<?php } ?>
			</div>
			<?php } ?>
			
			<?php if (!empty($event->small_description)) { ?>
			<div class="rsepro-small-description-block">
				<?php echo $event->small_description; ?>
			</div>
			<?php } ?>
			
			<?php if ($this->params->get('repeatcounter',1) && $repeats) { ?>
			<div class="rs_event_repeats">
				<?php if ($repeats) { ?> 
				(<a href="<?php echo rseventsproHelper::route('index.php?option=com_rseventspro&layout=default&parent='.rseventsproHelper::sef($event->id,$event->name)); ?>"><?php echo JText::sprintf('COM_RSEVENTSPRO_GLOBAL_REPEATS',$repeats); ?></a>) 
				<?php } ?>
			</div>
			<?php } ?>
		</div>
		
		<meta content="<?php echo rseventsproHelper::showdate($event->start,'Y-m-d H:i:s'); ?>" itemprop="startDate" />
		<?php if (!$event->allday) { ?><meta content="<?php echo rseventsproHelper::showdate($event->end,'Y-m-d H:i:s'); ?>" itemprop="endDate" /><?php } ?>
	</li>
	<?php } ?>
</ul>

<?php rseventsproHelper::clearMonthYear('events'.$this->fid, @$lastMY); ?>
<div class="rs_loader" id="rs_loader" style="display:none;">
	<?php echo JHtml::image('com_rseventspro/loader.gif', '', array(), true); ?>
</div>
<?php if ($this->total > $count) { ?>
	<a class="rs_read_more" id="rsepro_loadmore"><?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_LOAD_MORE'); ?></a>
<?php } ?>
<span id="total" class="rs_hidden"><?php echo $this->total; ?></span>
<span id="Itemid" class="rs_hidden"><?php echo JFactory::getApplication()->input->getInt('Itemid'); ?></span>
<span id="langcode" class="rs_hidden"><?php echo rseventsproHelper::getLanguageCode(); ?></span>
<span id="parent" class="rs_hidden"><?php echo JFactory::getApplication()->input->getInt('parent'); ?></span>
<span id="rsepro-prefix" class="rs_hidden"><?php echo 'events'.$this->fid; ?></span>
<?php } else { ?>
<div class="alert alert-warning"><?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_NO_EVENTS'); ?></div>
<?php } ?>

<?php if ($this->config->timezone) { ?>
<?php echo rseventsproHelper::timezoneModal(); ?>
<?php } ?>

<script type="text/javascript">	
	jQuery(document).ready(function(){
		<?php if ($this->total > $count) { ?>
		jQuery('#rsepro_loadmore').on('click', function() {
			rspagination('events',jQuery('#rs_events_container > li[class!="rsepro-month-year"]').length);
		});
		<?php } ?>
		
		<?php if (!empty($count)) { ?>
		jQuery('#rs_events_container li[class!="rsepro-month-year"]').on({
			mouseenter: function() {
				jQuery(this).find('div.rs_options').css('display','');
			},
			mouseleave: function() {
				jQuery(this).find('div.rs_options').css('display','none');
			}
		});
		<?php } ?>
		
		<?php if ($this->params->get('search',1)) { ?>
		var options = {};
		options.condition = '.rsepro-filter-operator';
		options.events = [{'#rsepro-filter-from' : 'rsepro_select'}];
		jQuery().rsjoomlafilter(options);	
		<?php } ?>
	});
</script>