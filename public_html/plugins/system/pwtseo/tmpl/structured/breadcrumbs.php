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
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;
use Joomla\Registry\Registry;

$component = $displayData['component'];

$pathway = Factory::getApplication()->getPathway();

$list = array();
$position = 1;

if ($component->get('show_home', 0))
{
	$home = '';

	if ($component->get('show_home') === '2' && $component->get('breadcrumbs_home', ''))
	{
		$home = $component->get('breadcrumbs_home', '');
	}
	else
	{
		try
		{
			$home = Factory::getApplication()->getMenu()->getDefault()->title;
		}
		catch (Exception $e)
		{
			$home = 'Home';
		}
	}

	$list[] = (object) array(
		'@type' => 'ListItem',
		'position' => $position,
		'name' => $home,
		'item' => Uri::base()
	);

	$position++;
}

foreach ($pathway->getPathway() as $i => $item)
{
	$list[] = (object) array(
		'@type' => 'ListItem',
		'position' => $position,
		'item' => rtrim(Uri::base(), '\/') . Route::_($item->link),
		'name' => $item->name
	);

	$position++;
}

?>
{
	"@context": "https://schema.org",
	"@type": "BreadcrumbList",
	"itemListElement": <?php echo json_encode($list) ?>
}
