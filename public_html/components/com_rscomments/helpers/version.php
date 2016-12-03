<?php
/**
* @package RSComments!
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('_JEXEC') or die('Restricted access');

class RSCommentsVersion
{
	public $version  = '1.13.5';
	public $key 	 = 'CM478HGD21';
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

if (!defined('_RSCOMMENTS_VERSION')) {
	$version = new RSCommentsVersion();
	define('_RSCOMMENTS_VERSION', $version->revision);
}