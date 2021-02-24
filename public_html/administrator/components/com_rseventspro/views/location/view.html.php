<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2020 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' );

class RseventsproViewLocation extends JViewLegacy
{
	public function display($tpl = null) {
		$this->form 		= $this->get('Form');
		$this->item 		= $this->get('Item');
		$this->config		= rseventsproHelper::getConfig();
		
		$this->addToolBar();
		parent::display($tpl);
	}
	
	protected function addToolBar() {
		JToolBarHelper::title(JText::_('COM_RSEVENTSPRO_ADD_EDIT_LOCATION'),'rseventspro48');
		JToolBarHelper::apply('location.apply');
		JToolBarHelper::save('location.save');
		JToolBarHelper::save2new('location.save2new');
		JToolBarHelper::cancel('location.cancel');
		
		$params = array(
			'id' => 'map-canvas',
			'address' => 'jform_address',
			'coordinates' => 'jform_coordinates',
			'pinpointBtn' => 'rsepro-pinpoint',
			'zoom' => (int) $this->config->google_map_zoom,
			'center' => $this->config->google_maps_center,
			'markerDraggable' => 'true',
			'resultsWrapperClass' => 'rsepro-locations-results-wrapper',
			'resultsClass' => 'rsepro-locations-results'
		);
		
		rseventsproMapHelper::loadMap($params);
	}
}