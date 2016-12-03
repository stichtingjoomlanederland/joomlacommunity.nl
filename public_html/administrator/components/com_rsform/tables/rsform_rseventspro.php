<?php
/**
* @package RSForm!Pro
* @copyright (C) 2007-2014 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

class TableRSForm_Rseventspro extends JTable
{
	public function __construct(& $db) {
		parent::__construct('#__rsform_rseventspro', 'form_id', $db);
	}
}