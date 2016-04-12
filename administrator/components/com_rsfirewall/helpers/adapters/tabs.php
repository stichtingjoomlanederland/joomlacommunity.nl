<?php
/**
 * @package    RSFirewall!
 * @copyright  (c) 2009 - 2016 RSJoomla!
 * @link       https://www.rsjoomla.com
 * @license    GNU General Public License http://www.gnu.org/licenses/gpl-3.0.en.html
 */

$jversion = new JVersion();
if ($jversion->isCompatible('3.0')) {
	require_once dirname(__FILE__).'/3.0/'.basename(__FILE__);
} elseif ($jversion->isCompatible('2.5')) {
	require_once dirname(__FILE__).'/2.5/'.basename(__FILE__);
}