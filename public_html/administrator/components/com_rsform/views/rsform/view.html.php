<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2018 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

class RsformViewRsform extends JViewLegacy
{
	protected $buttons;
	// version info
	protected $code;
	protected $version;
	
	public function display($tpl = null)
	{
		$this->addToolbar();

		$this->buttons  = $this->get('Buttons');
		$this->code		= $this->get('code');
		$this->version	= (string) new RSFormProVersion();
		
		$this->sidebar	= $this->get('SideBar');

        JHtml::stylesheet('com_rsform/admin/dashboard.css', array('relative' => true, 'version' => 'auto'));
		
		parent::display($tpl);
	}
	
	protected function addToolbar() {
		if (JFactory::getUser()->authorise('core.admin', 'com_rsform'))
		{
			JToolbarHelper::preferences('com_rsform');
		}
		
		// set title
		JToolbarHelper::title('RSForm! Pro', 'rsform');
		
		require_once JPATH_COMPONENT.'/helpers/toolbar.php';
		RSFormProToolbarHelper::addToolbar('rsform');
	}
}