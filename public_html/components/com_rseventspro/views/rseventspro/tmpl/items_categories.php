<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');?>
<?php if (!empty($this->categories)) { ?>
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
			<?php echo rseventsproHelper::shortenjs($category->description,$category->id, 255, $this->params->get('type', 1)); ?>
		</div>
	</div>
</li>
<?php } ?>
<?php } ?>