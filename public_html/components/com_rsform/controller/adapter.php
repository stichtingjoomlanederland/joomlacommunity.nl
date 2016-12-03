<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2014 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' );

require_once JPATH_ADMINISTRATOR.'/components/com_rsform/helpers/rsform.php';

global $RSadapter;
$RSadapter = RSFormProHelper::getLegacyAdapter();
$GLOBALS['RSadapter'] = $RSadapter;