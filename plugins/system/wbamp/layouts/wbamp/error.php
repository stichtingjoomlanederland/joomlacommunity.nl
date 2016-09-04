<?php
/**
 * wbAMP - Accelerated Mobile Pages for Joomla!
 *
 * @author      Yannick Gaultier
 * @copyright   (c) Yannick Gaultier - Weeblr llc - 2016
 * @package     wbAmp
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @version     1.5.0.585
 * @date        2016-08-25
 */

// no direct access
defined('_JEXEC') or die;

?>
<!doctype html>
<html lang="<?php echo JFactory::getLanguage()->getTag(); ?>" amp>

<head>
	<meta charset="utf-8">
	<meta name="viewport"
	      content="width=device-width,initial-scale=1,minimum-scale=1">
	<link
		href="https://fonts.googleapis.com/css?family=Roboto:400,400italic,700,700italic|Open+Sans:400,700,400italic,700italic"
		rel="stylesheet" type="text/css">

	<?php echo ShlMvcLayout_Helper::render('wbamp.head', $displayData, WbampHelper_Runtime::$layoutsBasePaths); ?>
</head>

<body>
<?php
if (WbampHelper_Edition::$id == 'full')
{
	if ($displayData['params']->get('menu_location', 'hidden') != 'hidden')
	{
		WbampHelper_Amphtml::getRenderedMenu($displayData, array('slide', 'slide-right'));
	}
}
?>
<div class="wbamp-wrapper">
	<?php

	// analytics
	if (WbampHelper_Edition::$id == 'full')
	{
		echo ShlMvcLayout_Helper::render('wbamp.tags.analytics', $displayData, WbampHelper_Runtime::$layoutsBasePaths);
	}

	// Header: either a custom module or link to home and/or logo
	if (WbampHelper_Edition::$id == 'full' && !empty($displayData['header_module']))
	{
		echo ShlMvcLayout_Helper::render('wbamp.header_module', $displayData, WbampHelper_Runtime::$layoutsBasePaths);
	}
	else
	{
		echo ShlMvcLayout_Helper::render('wbamp.header', $displayData, WbampHelper_Runtime::$layoutsBasePaths);
	}

	// Optional menu
	if (WbampHelper_Edition::$id == 'full')
	{
		if ($displayData['params']->get('menu_location', 'hidden') == 'before')
		{
			echo WbampHelper_Amphtml::getRenderedMenu($displayData, array('default'));
		}
	}

	// Optional Ads
	if (WbampHelper_Edition::$id == 'full')
	{
		if ($displayData['params']->get('ads_location', 'hidden') == 'before')
		{
			$network = strtolower($displayData['params']->get('ads_network', ''));
			if (!empty($network))
			{
				echo ShlMvcLayout_Helper::render('wbamp.ads-networks.' . $network, $displayData, WbampHelper_Runtime::$layoutsBasePaths);
			}
		}
	}

	// Main content
	echo ShlMvcLayout_Helper::render('wbamp.error_content', $displayData, WbampHelper_Runtime::$layoutsBasePaths);

	// Optional ads
	if (WbampHelper_Edition::$id == 'full')
	{
		if ($displayData['params']->get('ads_location', 'hidden') == 'after')
		{
			$network = strtolower($displayData['params']->get('ads_network', ''));
			if (!empty($network))
			{
				echo ShlMvcLayout_Helper::render('wbamp.ads-networks.' . $network, $displayData, WbampHelper_Runtime::$layoutsBasePaths);
			}
		}
	}
	// Optional menu
	if (WbampHelper_Edition::$id == 'full')
	{
		if ($displayData['params']->get('menu_location', 'hidden') == 'after')
		{
			echo WbampHelper_Amphtml::getRenderedMenu($displayData, array('default'));
		}
	}

	// footer
	echo ShlMvcLayout_Helper::render('wbamp.footer', $displayData, WbampHelper_Runtime::$layoutsBasePaths);

	// user notification
	if (!empty($displayData['user-notification']))
	{
		echo ShlMvcLayout_Helper::render('wbamp.tags.user-notification', $displayData, WbampHelper_Runtime::$layoutsBasePaths);
	}

	?>
</div>
</body>

</html>
