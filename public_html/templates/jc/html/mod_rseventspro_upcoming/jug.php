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

<div class="list-group list-group-flush">
	<?php foreach ($events as $eventid) : ?>
		<?php
		$details  = rseventsproHelper::details($eventid->id);
		?>
		<?php if (isset($details['event']) && !empty($details['event'])) $event = $details['event'];
		else continue; ?>
        <a class="list-group-item" href="<?php echo rseventsproHelper::route('index.php?option=com_rseventspro&layout=show&id=' . rseventsproHelper::sef($event->id, $event->name), true, $event->itemid); ?>">
            <h4 class="list-group-item-heading">
				<?php echo strip_tags($event->name); ?>
            </h4>
            <p class="list-group-item-text">
	            <?php echo rseventsproHelper::showdate($event->start, 'l j F Y, H:i', true); ?> -
	            <?php echo rseventsproHelper::showdate($event->end, 'H:i', true); ?> uur
            </p>
        </a>
	<?php endforeach; ?>
</div>