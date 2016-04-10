<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2014 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

$jversion = new JVersion();
if ($jversion->isCompatible('3.0')) {
	require_once dirname(__FILE__).'/3.0/'.basename(__FILE__);
} elseif ($jversion->isCompatible('2.5')) {
	require_once dirname(__FILE__).'/2.5/'.basename(__FILE__);
}