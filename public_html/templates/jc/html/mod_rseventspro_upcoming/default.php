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
	<p>Vergroot je Joomla-kennis door Joomla evenementen en curssusen te bezoeken. Zo zijn er onder andere Joomla gebruikersgroepen voor beginnende én gevorderden Joomlagebruikers. Of draag bij aan Joomla tijdens een van de Pizza Bugs & Fun events.</p>
</div>

<div class="list-group list-group-flush">
	<?php foreach ($events as $eventid) : ?>
		<?php
		$details  = rseventsproHelper::details($eventid->id);
		$category = $rsEventHelper->getCategoryName($eventid->id);
		$category = str_replace('Joomla Gebruikersgroep', 'Gebruikersgroep', $category);
		?>
		<?php if (isset($details['event']) && !empty($details['event'])) $event = $details['event'];
		else continue; ?>
		<a class="list-group-item" href="<?php echo rseventsproHelper::route('index.php?option=com_rseventspro&layout=show&id='.rseventsproHelper::sef($event->id,$event->name),true,$itemid); ?>">
			<div class="date-icon">
				<span class="date-day"><?php echo rseventsproHelper::date($event->start, 'd', true); ?></span><?php echo rseventsproHelper::date($event->start, 'M', true); ?>
			</div>
			<h4 class="list-group-item-heading">
				<?php echo strip_tags($event->name); ?>
			</h4>
			<p class="list-group-item-text"><?php echo $category; ?></p>
		</a>
	<?php endforeach; ?>
</div>
