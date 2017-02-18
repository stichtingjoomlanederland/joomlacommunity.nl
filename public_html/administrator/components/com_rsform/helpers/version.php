<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2017 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('_JEXEC') or die('Restricted access');

class RSFormProVersion
{
	public $version = '1.52.9';
	public $key		= '2XKJ3KS7JO';
	// Unused
	public $revision = null;
	
	public function __toString() {
		return $this->version;
	}
	
	// Legacy, keep revision
	public function __construct() {
		list($j, $revision, $bugfix) = explode('.', $this->version);
		$this->revision = $revision;
	}
}