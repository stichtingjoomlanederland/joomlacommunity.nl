<?php
/*
 * @package     perfecttemplate
 * @copyright   Copyright (c) Perfect Web Team / perfectwebteam.nl
 * @license     GNU General Public License version 3 or later
 */

// No direct access.
defined('_JEXEC') or die;

// Include the helper-class
include_once dirname(__FILE__) . '/helper.php';

// changes to HEAD
$helper->setMetadata($this);
$helper->setFavicon($this);
$helper->unloadCss($this);
$helper->unloadJs($this);
$helper->loadCss($this);
$helper->loadJs($this);

// Font
//$helper->localstorageFont('PerfectFont');

// Analytics
$analyticsData = $helper->getAnalytics($this);

// changes to Body
$pageclass  = $helper->getPageClass($this);
$pagelayout = $helper->getPagelayout($this);
$itemid     = $helper->getItemId();
