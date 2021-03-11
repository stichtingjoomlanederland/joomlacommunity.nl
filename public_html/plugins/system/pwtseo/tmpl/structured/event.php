<?php
/**
 * @package    Pwtseo
 *
 * @author     Perfect Web Team <extensions@perfectwebteam.com>
 * @copyright  Copyright (C) 2016 - 2021 Perfect Web Team. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link       https://extensions.perfectwebteam.com
 */

use Joomla\CMS\Date\Date;
use Joomla\CMS\Uri\Uri;

$data      = $displayData['data'];
$seo       = $displayData['seo'];
$config    = $displayData['config'];
$component = $displayData['component'];

$images = array();

if ($data->event_images)
{
	foreach ($data->event_images as $image)
	{
		$images[] = Uri::base() . $image->event_image;
	}
}

$performers = array();

if ($data->event_performers)
{
	foreach ($data->event_performers as $item)
	{
		$performers[] = (object) array(
			'@type' => 'PerformingGroup',
			'name'  => $item->event_performer_name
		);
	}
}

?>
{
	"@context": "https://schema.org",
	"@type": "Event",
	"name": "<?php echo $data->event_name ?>",
	"startDate": "<?php echo Date::getInstance($data->event_start)->toISO8601() ?>",
	"location": {
		"@type": "Place",
		"name": "<?php echo $data->event_location ?>",
		"address": {
			"@type": "PostalAddress",
			"streetAddress": "<?php echo $data->event_streetAddress ?>",
			"addressLocality": "<?php echo $data->event_addressLocality ?>",
			"postalCode": "<?php echo $data->event_postalCode ?>",
			"addressRegion": "<?php echo $data->event_addressRegion ?>",
			"addressCountry": "<?php echo $data->event_addressCountry ?>"
		}
	}
<?php if (count($images))
:
	?>
	,"image": <?php echo json_encode($images) ?>
<?php endif; ?>
,"description": "<?php echo $data->event_description ?>"
<?php if ($data->event_end && $data->event_end !== '0000-00-00 00:00:00')
:
	?>
	,"endDate": "<?php echo Date::getInstance($data->event_end)->toISO8601() ?>"
<?php endif; ?>
<?php
if (count($performers))
:
	?>
	,"performer": <?php echo json_encode($performers) ?>
<?php endif; ?>
}
