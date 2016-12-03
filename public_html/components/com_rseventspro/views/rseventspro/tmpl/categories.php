<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' );
$count = count($this->categories); ?>

<?php if ($this->params->get('show_page_heading', 1)) { ?>
<?php $title = $this->params->get('page_heading', ''); ?>
<h1><?php echo !empty($title) ? $this->escape($title) : JText::_('COM_RSEVENTSPRO_CATEGORIES_TITLE'); ?></h1>
<?php } ?>

<?php if (!empty($this->categories)) { ?>
<ul class="rs_events_container rsepro-categories-list" id="rs_events_container">
	<?php foreach($this->categories as $category) { ?>
	<?php if ($this->params->get('hierarchy', 0)) { ?><li class="rs_level_<?php echo $category->level; ?>"><?php } else { ?><li><?php } ?>
		<div class="well">
			<div class="rs_heading">
				<a href="<?php echo rseventsproHelper::route('index.php?option=com_rseventspro&category='.rseventsproHelper::sef($category->id,$category->title)); ?>">
					<?php echo $category->title; ?>
					<?php if ($this->params->get('events',0)) { ?>
					<?php $events = (int) $this->getNumberEvents($category->id,'categories'); ?>
					<?php if (!empty($events)) { ?>
					<small>(<?php echo $this->getNumberEvents($category->id,'categories'); ?>)</small>
					<?php } ?>
					<?php } ?>
				</a>
			</div>
			<div class="rs_description">
				<?php echo rseventsproHelper::shortenjs($category->description,$category->id, 255,$this->params->get('type', 1)); ?>
			</div>
		</div>
	</li>
	<?php } ?>
</ul>
<?php } ?>
<span id="total" class="rs_hidden"><?php echo $this->total; ?></span>
<span id="Itemid" class="rs_hidden"><?php echo JFactory::getApplication()->input->getInt('Itemid'); ?></span>

<div class="rs_loader" id="rs_loader" style="display:none;">
	<img src="<?php echo JURI::root(); ?>components/com_rseventspro/assets/images/loader.gif" alt="" />
</div>

<?php if ($this->total > $count) { ?>
	<a class="rs_read_more" id="rsepro_loadmore"><?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_LOAD_MORE'); ?></a>
<?php } ?>

<?php if ($this->total > $count) { ?>
<script type="text/javascript">
	jQuery(document).ready(function() {
		jQuery('#rsepro_loadmore').on('click', function() {
			rspagination('categories',jQuery('#rs_events_container > li').length);
		});
	});
</script>
<?php } ?>