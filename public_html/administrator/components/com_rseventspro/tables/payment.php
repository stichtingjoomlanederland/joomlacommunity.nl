<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

class RseventsproTablePayment extends JTable
{
	/**
	 * @param	JDatabase	A database connector object
	 */
	public function __construct($db) {
		parent::__construct('#__rseventspro_payments', 'id', $db);
	}
	
	public function check() {
		if (empty($this->tax_value)) {
			$this->tax_value = 0;
		}
		
		return true;
	}
}