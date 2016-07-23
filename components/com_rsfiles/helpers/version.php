<?php
/**
* @package RSFiles!
* @copyright (C) 2010-2014 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' );

class RSFilesVersion
{
	public $version  = '1.15.15';
	public $key 	 = 'RK238DSJ41';
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

if (!defined('RSF_RS_REVISION')) {
	$version = new RSFilesVersion();
	define('RSF_RS_REVISION', $version->revision);
}