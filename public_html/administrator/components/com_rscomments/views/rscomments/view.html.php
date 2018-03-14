<?php
/**
* @package RSComments!
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('_JEXEC') or die('Restricted access');

class RscommentsViewRscomments extends JViewLegacy
{	
	public function display($tpl = null) {
		$this->code			= $this->get('Code');
		$this->latest_com	= $this->get('LatestComments');
		$this->stats		= $this->get('Stats');
		$this->types		= $this->get('Types');
		$this->version		= (string) new RSCommentsVersion();

		$this->addToolbar();
		parent::display($tpl);
	}
	
	protected function addToolbar() {
		JToolbarHelper::title('RSComments!','rscomments');
		JToolbarHelper::preferences('com_rscomments');
		
		if ($this->stats) {
			JFactory::getDocument()->addScript('https://www.google.com/jsapi');
		}
	}
}