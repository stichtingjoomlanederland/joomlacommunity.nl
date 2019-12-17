<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2019 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

class RsformViewMenus extends JViewLegacy
{
	public function display($tpl = null) {
		JToolbarHelper::title('RSForm! Pro','rsform');
		
		$this->formId 		= JFactory::getApplication()->input->getInt('formId');
		$this->formTitle 	= $this->get('formtitle');
		$this->menus 		= $this->get('menus');
		$this->pagination 	= $this->get('pagination');

		JToolbarHelper::custom('menus.cancelform', 'previous', 'previous', JText::_('RSFP_BACK_TO_FORM'), false);
		JToolbarHelper::spacer();
		JToolbarHelper::cancel('submissions.cancel', JText::_('JTOOLBAR_CLOSE'));
		
		parent::display($tpl);
	}
}