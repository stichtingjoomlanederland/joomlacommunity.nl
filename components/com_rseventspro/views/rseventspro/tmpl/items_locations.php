<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');?>
<?php if (!empty($this->locations)) { ?>
<?php foreach($this->locations as $location) { ?>
<li>
	<div class="well">
		<div class="rs_options" style="display:none;">
			<?php if ((!empty($this->permissions['can_edit_locations']) || $this->admin) && !empty($this->user)) { ?>
				<a href="<?php echo rseventsproHelper::route('index.php?option=com_rseventspro&layout=editlocation&id='.rseventsproHelper::sef($location->id,$location->name)); ?>">
					<i class="fa fa-pencil"></i>
				</a>
			<?php } ?>
			<a href="<?php echo rseventsproHelper::route('index.php?option=com_rseventspro&layout=location&id='.rseventsproHelper::sef($location->id,$location->name)); ?>">
				<i class="fa fa-eye"></i>
			</a>
		</div>
		
		<div class="rs_heading">
			<a href="<?php echo rseventsproHelper::route('index.php?option=com_rseventspro&location='.rseventsproHelper::sef($location->id,$location->name)); ?>">
				<?php echo $location->name; ?>
				<?php if ($this->params->get('events',0)) { ?>
				<?php $events = (int) $this->getNumberEvents($location->id,'locations'); ?>
				<?php if (!empty($events)) { ?>
				<small>(<?php echo $this->getNumberEvents($location->id,'locations'); ?>)</small>
				<?php } ?>
				<?php } ?>
			</a>
		</div>
		<div class="rs_description">
			<?php echo rseventsproHelper::shortenjs($location->description,$location->id,255,$this->params->get('type', 1)); ?>
		</div>
	</div>
</li>
<?php } ?>
<?php } ?>