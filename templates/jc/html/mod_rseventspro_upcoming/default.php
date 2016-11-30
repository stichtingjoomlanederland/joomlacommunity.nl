<?php
/**
 * @package       RSEvents!Pro
 * @copyright (C) 2015 www.rsjoomla.com
 * @license       GPL, http://www.gnu.org/copyleft/gpl.html
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

// get RSEventsPro Template Helper
$template = JFactory::getApplication()->getTemplate();
include_once JPATH_THEMES . '/' . $template . '/helpers/rsevents.php';

// Instantiate the helper class
$rsEventHelper = new ThisRSEventsProHelper();

$open = !$links ? '\'target\' => \'_blank\'' : '';
?>


<div class="panel-body">
	<p>De Joomla gebruikersgroepen zijn er voor beginnende én gevorderden Joomlagebruikers. Kom gerust eens langs bij
		een gebruikersgroep bij jou in de buurt!</p>
</div>

<div class="list-group list-group-flush">
	<?php foreach ($events as $eventid) : ?>
		<?php $details = rseventsproHelper::details($eventid->id); ?>
		<?php if (isset($details['event']) && !empty($details['event'])) $event = $details['event'];
		else continue; ?>
		<div class="list-group-item">
			<div class="date-icon">
				<span class="date-day"><?php echo rseventsproHelper::date($event->start, 'd', true); ?></span><?php echo rseventsproHelper::date($event->start, 'M', true); ?>
			</div>
			<h4 class="list-group-item-heading">
				<?php
				$category = $rsEventHelper->getCategoryName($eventid->id);
				$category = str_replace('Joomla Gebruikersgroep', '',$category);
				?>
				<?php echo JHtml::_('link', rseventsproHelper::route('index.php?option=com_rseventspro&layout=show&id=' . rseventsproHelper::sef($event->id, $event->name), true, $itemid), $category, array('class' => 'list-group-item-anchor', $open)); ?>
			</h4>
			<p class="list-group-item-text"><?php echo strip_tags($event->name); ?></p>
		</div>
	<?php endforeach; ?>
</div>
