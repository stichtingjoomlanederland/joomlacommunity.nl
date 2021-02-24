<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2020 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' );

class RseventsproViewEvent extends JViewLegacy
{
	public function display($tpl = null) {
		$this->document		= JFactory::getDocument();
		$this->config		= rseventsproHelper::getConfig();
		$this->layout		= $this->getLayout();
		$this->item			= $this->get('Item');
		$this->app			= JFactory::getApplication();
		
		if ($this->layout == 'edit') {
			require_once JPATH_SITE.'/components/com_rseventspro/helpers/events.php';
			
			if (rseventsproHelper::isJ4()) { 
				JHtml::_('bootstrap.framework');
			}
			
			$this->form			= $this->get('Form');
			$this->dependencies	= $this->get('FormDependencies');
			$this->ticketsform	= $this->get('FormTickets');
			$this->couponsform	= $this->get('FormCoupons');
			$this->eventClass	= RSEvent::getInstance($this->item->id);
			$this->tickets		= $this->eventClass->getTickets();
			$this->coupons		= $this->eventClass->getCoupons();
			$this->files		= $this->eventClass->getFiles();
			$this->repeats		= $this->eventClass->getRepeats();
			$this->states		= array('published' => true, 'unpublished' => true, 'archived' => true, 'trash' => false, 'all' => false);
			$this->tab			= $this->app->input->getInt('tab');
			
			$this->addToolBar();
		} elseif ($this->layout == 'upload') {
			
			// Load scripts
			JHtml::script('com_rseventspro/jquery.imgareaselect.pack.js', array('relative' => true, 'version' => 'auto'));
			JHtml::stylesheet('com_rseventspro/imgareaselect-animated.css', array('relative' => true, 'version' => 'auto'));
			
			$image				= @getimagesize(JPATH_SITE.'/components/com_rseventspro/assets/images/events/'.$this->item->icon);
			$this->width		= isset($image[0]) ? $image[0] : 800;
			$this->height		= isset($image[1]) ? $image[1] : 380;
			$this->customheight	= round(($this->height * ($this->width < 380 ? $this->width : 380)) / $this->width) + 100;

			if ($this->height > $this->width) {
				$this->divwidth		= $this->width < 380 ? $this->width : 380;
			} else {
				if ($this->width < 600) {
					$this->divwidth = $this->width;
				} else {
					$ratio = $this->height / $this->width;
					$newHeight = (int) (600 * $ratio);
					$this->divwidth = $newHeight > 400 ? 400 : 600;
				}
			}
			
			$this->left_crop	= isset($this->item->properties['left']) ? $this->item->properties['left'] : 0;
			$this->top_crop		= isset($this->item->properties['top']) ? $this->item->properties['top'] : 0;
			$this->width_crop	= isset($this->item->properties['width']) ? $this->item->properties['width'] : $this->width;
			$this->height_crop	= isset($this->item->properties['height']) ? $this->item->properties['height'] : $this->height;
			
			$this->icon = $this->get('Icon');
			
			if (!empty($this->item->icon) && !file_exists(JPATH_SITE.'/components/com_rseventspro/assets/images/events/'.$this->item->icon)) {
				$this->item->icon = '';
				$this->icon = '';
			}
			
		} elseif ($this->layout == 'tickets') {
			
			JHtml::script('com_rseventspro/jquery-ui.min.js', array('relative' => true, 'version' => 'auto'));
			JHtml::stylesheet('com_rseventspro/tickets.css', array('relative' => true, 'version' => 'auto'));
			$this->tickets = rseventsproHelper::getTickets($this->app->input->getInt('id',0));
		}
		
		parent::display($tpl);
	}
	
	protected function addToolBar() {
		$this->item->name ? JToolBarHelper::title(JText::sprintf('COM_RSEVENTSPRO_EDIT_EVENT',$this->item->name),'rseventspro48') : JToolBarHelper::title(JText::_('COM_RSEVENTSPRO_ADD_EVENT'),'rseventspro48');
		
		$toolbar = JToolBar::getInstance('toolbar');
		
		$layout = new JLayoutFile('joomla.toolbar.standard');
		$dhtml = $layout->render(array('text' => JText::_('JTOOLBAR_APPLY'), 'btnClass' => 'btn btn-success', 'id' => '', 'htmlAttributes' => '', 'onclick' => 'RSEventsPro.Event.save(\'event.apply\');', 'class' => 'icon-save', 'doTask' => 'RSEventsPro.Event.save(\'event.apply\');'));
		$toolbar->appendButton('Custom', $dhtml, 'apply', true);
		
		$layout = new JLayoutFile('joomla.toolbar.standard');
		$dhtml = $layout->render(array('text' => JText::_('JTOOLBAR_SAVE'), 'btnClass' => 'btn btn-success', 'id' => '', 'htmlAttributes' => '', 'onclick' => 'RSEventsPro.Event.save(\'event.save\');', 'class' => 'icon-save', 'doTask' => 'RSEventsPro.Event.save(\'event.save\');'));
		$toolbar->appendButton('Custom', $dhtml, 'apply', true);
		
		JToolBarHelper::save2copy('event.copy');
		
		$layout = new JLayoutFile('joomla.toolbar.standard');
		$dhtml = $layout->render(array('text' => JText::_('COM_RSEVENTSPRO_PREVIEW_EVENT'), 'btnClass' => 'btn', 'id' => '', 'htmlAttributes' => '', 'onclick' => 'rsepro_preview()', 'class' => 'icon-zoom-in', 'doTask' => 'rsepro_preview()'));
		$toolbar->appendButton('Custom', $dhtml, 'preview', true);
		
		JToolBarHelper::cancel('event.cancel');
		
		if (!rseventsproHelper::isJ4()) {
			JHtml::_('formbehavior.chosen', 'select');
			JHtml::_('jquery.ui', array('core', 'sortable'));
			JHtml::_('rseventspro.tags', '#tags');
		} else {
			JHtml::script('com_rseventspro/jquery-ui.min.js', array('relative' => true, 'version' => 'auto'));
		}
		
		// Load scripts
		JHtml::script('com_rseventspro/edit.js', array('relative' => true, 'version' => 'auto'));
		JHtml::stylesheet('com_rseventspro/edit'.(rseventsproHelper::isJ4() ? '.j4' : '').'.css', array('relative' => true, 'version' => 'auto'));
		
		// Load RSEvents!Pro plugins
		rseventsproHelper::loadPlugins();
		
		// Load custom scripts
		$this->app->triggerEvent('onrsepro_addCustomScripts');
		
		$mapParams = array(
			'id' => 'rsepro-location-map',
			'address' => 'location_address',
			'coordinates' => 'location_coordinates',
			'zoom' => (int) $this->config->google_map_zoom,
			'center' => $this->config->google_maps_center,
			'markerDraggable' => 'true'
		);
		
		rseventsproMapHelper::loadMap($mapParams);
	}
}