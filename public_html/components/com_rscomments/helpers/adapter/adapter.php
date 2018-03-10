<?php
/**
* @package RSJoomla! Adapter
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('_JEXEC') or die('Restricted access');

if (version_compare(JVERSION, '3.0', '>='))  {
	// Joomla! 3.0
	require_once JPATH_SITE.'/components/com_rscomments/helpers/adapter/3.0/tabs.php';
	require_once JPATH_SITE.'/components/com_rscomments/helpers/adapter/3.0/fieldsets.php';
}