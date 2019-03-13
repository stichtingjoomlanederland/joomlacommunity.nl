<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

class RSEventsProVersion {
	public $version  = '1.12.2';
	public $key		 = 'EV8PR413H1';
	// Unused
	public $revision = null;
	
	// Get version
	public function __toString() {
		return $this->version;
	}
	
	// Legacy, keep revision
	public function __construct() {
		list($j, $revision, $bugfix) = explode('.', $this->version);
		$this->revision = $revision;
	}
}

$version = new RSEventsProVersion();
define('RSEPRO_RS_REVISION', $version->revision);