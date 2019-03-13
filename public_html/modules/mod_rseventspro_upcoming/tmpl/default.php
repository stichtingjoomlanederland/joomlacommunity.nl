<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

// no direct access
defined('_JEXEC') or die('Restricted access');
$open = !$links ? 'target="_blank"' : ''; ?>

<?php if ($items) { ?>
<div id="rsepro-upcoming-module">
	<?php foreach ($items as $block => $events) { ?>
	<ul class="rsepro_upcoming<?php echo $suffix; ?> row-fluid">
		<?php foreach ($events as $id) { ?>
		<?php $details = rseventsproHelper::details($id); ?>
		<?php if (isset($details['event']) && !empty($details['event'])) $event = $details['event']; else continue; ?>
		<li class="span<?php echo 12 / $columns; ?>">
			<a <?php echo $open; ?> href="<?php echo rseventsproHelper::route('index.php?option=com_rseventspro&layout=show&id='.rseventsproHelper::sef($event->id,$event->name),true,$itemid); ?>"><?php echo $event->name; ?></a> <small>(<?php echo $event->allday ? rseventsproHelper::date($event->start,rseventsproHelper::getConfig('global_date'),true) : rseventsproHelper::date($event->start,null,true); ?>)</small>
		</li>
		<?php } ?>
	</ul>
	<?php } ?>
</div>
<?php } ?>