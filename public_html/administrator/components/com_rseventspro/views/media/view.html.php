<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' );

class rseventsproViewMedia extends JViewLegacy
{	
	public function display($tpl = null) {
		
		rseventsproHelper::loadBootstrap(true);
		
		$this->baseURL	= $this->get('url');
		$this->images	= $this->get('images');
		$this->folders	= $this->get('folders');
		$this->previous	= $this->get('previous');
		$this->state	= $this->get('state');
		
		parent::display($tpl);
	}
}