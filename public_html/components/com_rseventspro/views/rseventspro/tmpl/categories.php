<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' );
$count = count($this->categories);
$columns = (int) $this->params->get('columns', 1);
$modulo = $columns == 2 ? 1 : ($columns == 3 ? 2 : ($columns == 4 ? 3 : 0)); ?>

<?php if ($this->params->get('show_page_heading', 1)) { ?>
<?php $title = $this->params->get('page_heading', ''); ?>
<h1><?php echo !empty($title) ? $this->escape($title) : JText::_('COM_RSEVENTSPRO_CATEGORIES_TITLE'); ?></h1>
<?php } ?>

<?php if (!empty($this->categories)) { ?>
<ul class="rs_events_container rsepro-categories-list" id="rs_events_container">
	<?php foreach($this->categories as $i => $category) { ?>
	<?php $class = $this->params->get('hierarchy', 0) ? 'rs_level_'.$category->level : 'rsepro-category-row'.$columns; ?>
	<li class="rsepro-category <?php echo $class; ?>">
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
	<?php if ($i%$columns == $modulo && $modulo) { ?><li class="clearfix" style="width:100%;"></li><?php } ?>
	<?php } ?>
</ul>
<?php } ?>
<div class="clearfix"></div>
<span id="total" class="rs_hidden"><?php echo $this->total; ?></span>
<span id="Itemid" class="rs_hidden"><?php echo JFactory::getApplication()->input->getInt('Itemid'); ?></span>

<div class="rs_loader" id="rs_loader" style="display:none;">
	<?php echo JHtml::image('com_rseventspro/loader.gif', '', array(), true); ?>
</div>

<?php if ($this->total > $count) { ?>
	<a class="rs_read_more" id="rsepro_loadmore"><?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_LOAD_MORE'); ?></a>
<?php } ?>

<?php if ($this->total > $count) { ?>
<script type="text/javascript">
	jQuery(document).ready(function() {
		jQuery('#rsepro_loadmore').on('click', function() {
			rspagination('categories',jQuery('#rs_events_container > li.rsepro-category').length);
		});
	});
</script>
<?php } ?>