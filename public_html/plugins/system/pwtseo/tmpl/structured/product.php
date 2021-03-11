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
use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;
use Joomla\CMS\Uri\Uri;
use Joomla\Registry\Registry;

$data      = $displayData['data'];
$seo       = $displayData['seo'];
$config    = $displayData['config'];
$component = $displayData['component'];

$images = array();

if ($data->product_images)
{
	foreach ($data->product_images as $image)
	{
		$images[] = Uri::base() . '/' . $image->product_image;
	}
}
?>
{
	"@context": "https://schema.org/",
	"@type": "Product",
	"name": "<?php echo $data->product_name ?>",
	"image": <?php echo json_encode($images) ?>,
	"description": "<?php echo $data->product_description ?>"
	<?php if ($data->currency && $data->price): ?>
, "offers": {
	"@type": "Offer"
	<?php if ($data->url): ?>
		,"url": "<?php echo $data->url ?>"
	<?php endif; ?>
	<?php if ($data->priceValidUntil): ?>
		,"priceValidUntil": "<?php echo $data->priceValidUntil ?>"
	<?php endif; ?>
	<?php if ($data->currency): ?>
		,"priceCurrency": "<?php echo $data->currency ?>"
	<?php endif; ?>
	<?php if ($data->price): ?>
		,"price": "<?php echo $data->price ?>"
	<?php endif; ?>
	<?php if ($data->availability): ?>
		,"availability": "<?php echo $data->availability ?>"
	<?php endif; ?>
 }
	<?php endif; ?>
	<?php if ($data->reviewRating && $data->reviewAuthor): ?>
	,"review": {
		"@type": "Review",
		"reviewRating": {
			"@type": "Rating",
			"ratingValue": "<?php echo $data->reviewRating ?>"
		},
		"author": {
			"@type": "Person",
			"name": "<?php echo $data->reviewAuthor ?>"
		}
	}
	<?php endif; ?>
	<?php if ($data->aggregated_rating && $data->review_count): ?>
,"aggregateRating": {
		"@type": "AggregateRating",
		"ratingValue": "<?php echo $data->aggregated_rating ?>",
		"reviewCount": "<?php echo $data->review_count ?>"
	}
	<?php endif; ?>
	<?php if ($data->product_identification)
	:
		?>
		<?php foreach ($data->product_identification as $item)
		:
			?>
,"<?php echo $item->product_identifier ?>":"<?php echo $item->product_identifier_value ?>"
		<?php endforeach; ?>
	<?php endif; ?>
	<?php
	if ($data->product_brand)
	:
		?>
	,"brand": {
		"@type": "Thing",
		"name": "<?php echo $data->product_brand ?>"
	}
	<?php endif; ?>
}
