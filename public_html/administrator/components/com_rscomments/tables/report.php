<?php
/**
* @package RSComments!
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('_JEXEC') or die('Restricted access');

class RscommentsTableReport extends JTable
{
	public function __construct(&$db) {
		parent::__construct('#__rscomments_reports', 'id', $db);
	}
}