<?php
/**
 * sh404SEF support for com_easydiscuss
 * Author : StackIdeas Private Limited
 * contact : support@stackideas.com
 */
defined('_JEXEC') or die('Direct Access to this location is not allowed.');

global $sh_LANG;

// Include main file.
require_once JPATH_ROOT . '/administrator/components/com_easydiscuss/includes/easydiscuss.php';

if (class_exists('shRouter')) {
	$sefConfig = shRouter::shGetConfig();
} else {
	$sefConfig = Sh404sefFactory::getConfig();
}

$shLangName = '';
$shLangIso = '';
$title = array();
$shItemidString = '';
$dosef = shInitializePlugin($lang, $shLangName, $shLangIso, $option);

if ($dosef == false) {
	return;
}

// remove common URL from GET vars list, so that they don't show up as query string in the URL
shRemoveFromGETVarsList('option');
shRemoveFromGETVarsList('lang');

// Load language file
$language = JFactory::getLanguage();
$language->load('com_easydiscuss', JPATH_ROOT);

// start by inserting the menu element title (just an idea, this is not required at all)
$task = isset($task) ? @$task : null;
$Itemid = isset($Itemid) ? @$Itemid : null;
$view = isset($view) ? $view : '';
$layout = isset($layout) ? $layout : '';

// prepare the menu item view for later reference.
$menuView = '';
$xMenu = '';

if ($Itemid) {
	$xMenu = JFactory::getApplication()->getMenu()->getItem($Itemid);
	$menuView = (isset($xMenu->query['view']) && $xMenu->query['view']) ? $xMenu->query['view'] : '';
}

if (!empty($id) && !empty($view)) {
	$permalink  = '';


	if ($view == 'categories') {
		$permalink = EDR::getAlias('category', $id);
	}

	if ($view == 'post') {
		$permalink = EDR::getAlias('posts', $id);
	}

	if ($view == 'profile') {
		$permalink = EDR::getUserAlias($id);
	}

	if ($view == 'tags') {
		$permalink = EDR::getAlias('tags', $id);
	}

	if ($view == 'badges') {
		$permalink = EDR::getAlias('badges', $id);
	}

	if ($view == 'points') {
		$permalink = EDR::getAlias('points', $id);
	}
}

if (empty($Itemid)) {
	$Itemid	= EDR::getItemId($view);
	shAddToGETVarsList('Itemid', $Itemid);
}

$name = shGetComponentPrefix($option);
$name = empty($name) ? getMenuTitle($option, $task, $Itemid, null, $shLangName) : $name;
$name = empty($name) || $name == '/' ? 'discuss' : $name;

$title[] = $name;


if (isset($view) && !empty($view)) {

	$addView = true;

	if ($menuView && $view == $menuView) {
		$addView = false;
	}

	if ($addView && $view == 'post') {
		$addView = false;
	}

	if ($addView) {
		// Translate the view
		$title[] = JText::_('COM_EASYDISCUSS_SH404_VIEW_' . JString::strtoupper($view));
	}

	shRemoveFromGETVarsList('view');
}


if ($view == 'post' && $id) {
	$edConfig = ED::config();
	if ($edConfig->get('main_sef') == 'category') {
		$post = ED::post($id);
		$title[] = EDR::getAlias('category', $post->getCategory()->id);
	}
}

if ($view == 'categories' && $layout == 'listings' && !empty($category_id)) {

	$addAlias = true;

	// check if we need to insert the alias or not.
	if ($menuView == $view) {
		$xLayout = (isset($xMenu->query['layout']) && $xMenu->query['layout']) ? $xMenu->query['layout'] : '';
		$xCategoryId = (isset($xMenu->query['category_id']) && $xMenu->query['category_id']) ? $xMenu->query['category_id'] : '';

		if ($xLayout == $layout && $xCategoryId == $category_id) {
			$addAlias = false;
		}
	}

	if ($addAlias) {
		$title[] = EDR::getAlias('category', $category_id);
	}

	shRemoveFromGETVarsList('category_id');

	// Remove the view since we don't want to set the view.
	unset($layout);
	shRemoveFromGETVarsList('layout');
}

if ($view == 'badges' && isset($id)) {
	unset($layout);
	shRemoveFromGETVarsList('layout');
}

if ($view == 'forums' && !empty($category_id)) {
	$title[] = EDR::getAlias('category', $category_id);

	shRemoveFromGETVarsList('category_id');
}

if (!empty($id)) {
	if (!empty($permalink)) {
		$title[] = $permalink;
		shRemoveFromGETVarsList('id');
	}
}

// Category id may be category_id=0 in index view.
if (isset($category_id) && $category_id == 0) {
	shRemoveFromGETVarsList('category_id');
}

if (!empty($layout)) {
	$title[] = $layout;
	shRemoveFromGETVarsList('layout');
}

if (!empty($filter)) {
	$title[] = $filter;
	shRemoveFromGETVarsList('filter');
}

if (!empty($format)) {
	$title[] = $format;
	shRemoveFromGETVarsList('format');
}

if(!empty($Itemid)) {
	shRemoveFromGETVarsList('Itemid');
}

if(!empty($limit)) {
	shRemoveFromGETVarsList('limit');
}

if(isset($limitstart)) {
	shRemoveFromGETVarsList('limitstart'); // limitstart can be zero}
}

// ------------------  standard plugin finalize function - don't change ---------------------------
if ($dosef){
	$string = shFinalizePlugin($string, $title, $shAppendString, $shItemidString,
		(isset($limit) ? @$limit : null), (isset($limitstart) ? @$limitstart : null),
		(isset($shLangName) ? @$shLangName : null));
}
// ------------------  standard plugin finalize function - don't change ---------------------------
