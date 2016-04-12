<?php
/**
 * @package    RSFirewall!
 * @copyright  (c) 2009 - 2016 RSJoomla!
 * @link       https://www.rsjoomla.com
 * @license    GNU General Public License http://www.gnu.org/licenses/gpl-3.0.en.html
 */

defined('_JEXEC') or die('Restricted access');

class RSFirewallViewList extends JViewLegacy
{
	protected $form;
	protected $item;
	protected $ip;
	protected $field;
	
	public function display($tpl = null) {
		$user = JFactory::getUser();
		if (!$user->authorise('lists.manage', 'com_rsfirewall')) {
			$app = JFactory::getApplication();
			$app->enqueueMessage(JText::_('JERROR_ALERTNOAUTHOR'), 'error');
			$app->redirect(JRoute::_('index.php?option=com_rsfirewall', false));
		}
		
		$this->addToolBar();
		
		$layout = $this->getLayout();
		switch ($layout) {
			case 'edit':				
				$this->form	 = $this->get('Form');
				$this->item	 = $this->get('Item');
				$this->ip	 = $this->get('Ip'); 
			break;
			
			case 'bulk':
				$this->form	= $this->get('Form');
				$this->ip	= $this->get('Ip'); 
			break;
		}
		
		$this->field = $this->get('RSFieldset');
		
		parent::display($tpl);
	}
	
	protected function addToolBar() {
		// set title
		JToolBarHelper::title('RSFirewall!', 'rsfirewall');
		
		require_once JPATH_COMPONENT.'/helpers/toolbar.php';
		RSFirewallToolbarHelper::addToolbar('lists');
		
		$layout = $this->getLayout();
		switch ($layout) {
			case 'edit':
				JToolBarHelper::apply('list.apply');
				JToolBarHelper::save('list.save');
				JToolBarHelper::save2new('list.save2new');
				JToolBarHelper::save2copy('list.save2copy');
				JToolBarHelper::cancel('list.cancel');
			break;
			
			case 'bulk':
				JToolBarHelper::save('list.bulksave');
				JToolBarHelper::cancel('list.cancel');
			break;
		}
	}
}