<?php
/**
* @package RSComments!
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('_JEXEC') or die('Restricted access');

class RSCommentsViewRscomments extends JViewLegacy
{
	protected $sidebar;
	protected $code;
	protected $latest_com;
	
	public function display($tpl = null) {
		$this->sidebar 		= RSCommentsHelper::isJ3() ? JHtmlSidebar::render() : '';
		$this->code			= $this->get('Code');
		$this->latest_com	= $this->get('LatestComments');
		$this->stats		= $this->get('Stats');
		$this->types		= $this->get('Types');
		$this->version		= (string) new RSCommentsVersion();

		$this->addToolbar();
		parent::display($tpl);
	}
	
	protected function addToolbar() {
		JToolBarHelper::title('RSComments!','rscomments');
		JToolBarHelper::preferences('com_rscomments');
		
		$doc = JFactory::getDocument();
		$doc->addScript(JUri::root().'administrator/components/com_rscomments/assets/js/scripts.js');
		
		if ($this->stats) {
			$doc->addScript('https://www.google.com/jsapi');
		}
		
		require_once JPATH_COMPONENT.'/helpers/toolbar.php';
		RSCommentsToolbarHelper::addToolbar('overview');
	}
}