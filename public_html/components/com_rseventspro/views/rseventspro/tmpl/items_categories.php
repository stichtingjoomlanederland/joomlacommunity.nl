<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access'); 

$limitstart = JFactory::getApplication()->input->getInt('limitstart');

$columns = (int) $this->params->get('columns', 1);
$modulo = $columns == 2 ? 1 : ($columns == 3 ? 2 : ($columns == 4 ? 3 : 0)); ?>
<?php if (!empty($this->categories)) { ?>
<?php foreach($this->categories as $i => $category) { ?>
<?php $i = $limitstart + $i; ?>
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
			<?php echo rseventsproHelper::shortenjs($category->description,$category->id, 255, $this->params->get('type', 1)); ?>
		</div>
	</div>
</li>
<?php if ($i%$columns == $modulo && $modulo) { ?><li class="clearfix" style="width:100%;"></li><?php } ?>
<?php } ?>
<?php } ?>