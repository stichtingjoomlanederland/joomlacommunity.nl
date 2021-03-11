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

// We might not need this, but to simplify the code
if ($seo['context'] === 'com_content.article')
{
	/** @var ContentModelArticle $model */
	$model = BaseDatabaseModel::getInstance('Article', 'ContentModel', array('ignore_request' => true));

	$model->setState('params', new Registry);
	$item = $model->getItem($seo['context_id']);
}

$author = '';

switch ($data->article_author)
{
	case 'article.author':
		$author = $item ? $item->author : '';
		break;
	case 'custom.user':
		$author = Factory::getUser($data->article_custom_user)->name;
		break;
	case 'custom':
		$author = $data->article_custom_author;
		break;
}

$description = '';

switch ($data->article_description)
{
	case 'article.meta':
		$description = $item ? $item->metadesc : '';
		break;
	case 'article.intro':

		break;
	case 'menuitem.meta':

		break;
	case 'custom':
		$description = $data->article_custom_description;
		break;
}

$images = array();

switch ($data->article_image)
{
	case 'custom':
		if ($data->article_custom_images)
		{
			foreach ($data->article_custom_images as $image)
			{
				$images[] = Uri::base() . $image->custom_image;
			}
		}
		break;
	case 'image_intro':
	case 'image_fulltext':
		if ($item)
		{
			$itemImage = json_decode($item->images);
			$images[]  = Uri::base() . $itemImage->{$data->article_image};
		}
	break;
}
?>
{
	"@context": "https://schema.org",
	"@type": "NewsArticle",
	"mainEntityOfPage": {
		"@type": "WebPage",
		"@id": "<?php echo (isset($seo['override_canonical']) && $seo['override_canonical'] === '3') ? $seo['canonical'] : Uri::getInstance() ?>"
	},
	"headline": "<?php echo $item->title ?>",
	<?php if (count($images) > 0)
	:
		?>
	"image": <?php echo json_encode($images) ?>,
	<?php endif; ?>
	"datePublished": "<?php echo Date::getInstance($data->article_date_published === 'custom' ? $data->article_custom_publish_up : $item->publish_up)->toISO8601(true) ?>",
	"dateModified": "<?php echo Date::getInstance($data->article_date_modified === 'custom' ? $data->article_custom_modified : $item->modified)->toISO8601(true) ?>",
	"author": {
	"@type": "Person",
		"name": "<?php echo $author ?>"
	},
	"publisher": {
		"@type": "Organization",
		"name": "<?php echo $data->article_publisher === 'default' ? $component->get('publisher_name', $config->get('sitename')) : $data->article_custom_publishername ?>",
		"logo": {
			"@type": "ImageObject",
			"url": "<?php echo Uri::base() . ($data->article_publisher === 'default' ? $component->get('publisher_logo', '') : $data->article_custom_publisherlogo) ?>"
		}
	},
	"description": "<?php echo $description ?>"
}
