<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2019 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('_JEXEC') or die('Restricted access');

class RSFormProVersion
{
	public $version = '2.2.3';
	public $key		= '2XKJ3KS7JO';
	// Unused
	public $revision = null;
	
	public function __toString() {
		return $this->version;
	}
}