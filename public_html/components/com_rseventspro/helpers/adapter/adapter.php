<?php
/**
* @package RSJoomla! Adapter
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('_JEXEC') or die('Restricted access');

// Joomla! 3.0
if (version_compare(JVERSION, '3.0', '>=')) {
	require_once JPATH_SITE.'/components/com_rseventspro/helpers/adapter/3.0/tabs.php';
	require_once JPATH_SITE.'/components/com_rseventspro/helpers/adapter/3.0/fieldsets.php';
}