<?php
/**
* @package RSComments!
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('_JEXEC') or die('Restricted access');

class RscommentsViewImport extends JViewLegacy
{
	public function display($tpl = null) {
		JPluginHelper::importPlugin('rscomments');

		$this->tabs		 	= $this->get('RSTabs');
		$this->form  		= $this->get('Form');
		$this->fieldsets 	= $this->form->getFieldsets();
		$this->html			= JFactory::getApplication()->triggerEvent('onrscommentsButton');

		$this->addToolbar();
		parent::display($tpl);
	}

	protected function addToolbar() {
		JToolbarHelper::title('RSComments!','rscomments');
		
		$layout = new JLayoutFile('joomla.toolbar.standard');
		$dhtml = $layout->render(array('text' => JText::_('COM_RSCOMMENTS_IMPORT'), 'btnClass' => 'btn btn-success', 'doTask' => 'rsc_import_table()', 'onclick' => 'rsc_import_table()', 'htmlAttributes' => '', 'name' => 'import', 'class' => 'icon-upload'));
		JToolbar::getInstance('toolbar')->appendButton('Custom', $dhtml, 'import');
		
		JToolbarHelper::preferences('com_rscomments');
	}
}