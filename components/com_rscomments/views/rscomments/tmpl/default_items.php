<?php
/**
* @package RSComments!
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('_JEXEC') or die('Restricted access');

if (!empty($this->comments)) {
	foreach ($this->comments as $comment) {
		$this->comment = $comment;
		echo $this->loadTemplate('item');
	}
}