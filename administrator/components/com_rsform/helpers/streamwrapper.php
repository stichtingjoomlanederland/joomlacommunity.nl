<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2014 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
 
defined('_JEXEC') or die('Restricted access');

class RSFormProConfigStream
{
	private $path;
	
	public function url_stat($path, $flags) {
		// Just to trick the server into thinking the file exists
		return stat(__FILE__);
	}
	
	public function stream_stat() {
		// Just to trick the server into thinking the file exists
		return stat(__FILE__);
	}
	
	public function stream_open($path, $mode, $options, &$opened_path) {
		$this->path = substr($path, strlen('rsformconfig://'));
		return true;
	}
	
	public function stream_read($count) {
		static $called = array();
		if (!isset($called[$this->path])) {
			$called[$this->path] = true;
			return RSFormProHelper::getConfig($this->path);
		}
		
		return false;
	}
	
	public function stream_eof() {
		// We're reading the whole data
		return true;
	}
}