<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' );

class RseventsproViewEvent extends JViewLegacy
{	
	protected $item;
	protected $config;
	protected $layout;
	protected $tab;
	protected $eventClass;
	
	public function display($tpl = null) {
		$this->document		= JFactory::getDocument();
		$this->config		= rseventsproHelper::getConfig();
		$this->layout		= $this->getLayout();
		$this->item			= $this->get('Item');
		$this->app			= JFactory::getApplication();
		
		if ($this->layout == 'edit') {
			require_once JPATH_SITE.'/components/com_rseventspro/helpers/events.php';
			
			$this->form			= $this->get('Form');
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
		JToolBarHelper::apply('event.apply');
		JToolBarHelper::save('event.save');
		JToolBarHelper::custom('preview','zoom-in','zoom-in',JText::_('COM_RSEVENTSPRO_PREVIEW_EVENT'),false);
		JToolBarHelper::cancel('event.cancel');
		
		JHtml::_('rseventspro.chosen');
		JHtml::_('jquery.ui', array('core', 'sortable'));
		
		// Load scripts
		JHtml::script('com_rseventspro/edit.js', array('relative' => true, 'version' => 'auto'));
		JHtml::stylesheet('com_rseventspro/edit.css', array('relative' => true, 'version' => 'auto'));
		
		// Load RSEvents!Pro plugins
		rseventsproHelper::loadPlugins();
		
		// Load custom scripts
		$this->app->triggerEvent('rsepro_addCustomScripts');
		
		if ($this->config->enable_google_maps) {
			$this->document->addScript('https://maps.google.com/maps/api/js?language='.JFactory::getLanguage()->getTag().($this->config->google_map_api ? '&key='.$this->config->google_map_api : ''));
			JHtml::script('com_rseventspro/jquery.map.js', array('relative' => true, 'version' => 'auto'));
		}
	}
}