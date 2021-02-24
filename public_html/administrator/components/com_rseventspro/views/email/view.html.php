<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2020 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' );

class RseventsproViewEmail extends JViewLegacy
{
	public function display($tpl = null) {
		$this->form 		= $this->get('Form');
		$this->item 		= $this->get('Item');
		
		parent::display($tpl);
	}
}