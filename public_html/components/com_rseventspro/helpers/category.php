<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

// no direct access
defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.categories');

class RseventsproCategories extends JCategories
{
	public function __construct($options = array()) {
		$options['table'] = '#__rseventspro_events';
		$options['extension'] = 'com_rseventspro';
		parent::__construct($options);
	}
}