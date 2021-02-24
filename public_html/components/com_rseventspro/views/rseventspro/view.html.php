<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2020 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined( '_JEXEC' ) or die( 'Restricted access' );

class RseventsproViewRseventspro extends JViewLegacy
{
	public function display($tpl = null) {
		$lists = array();
		$db			= JFactory::getDbo();
		$query		= $db->getQuery(true);
		$app		= JFactory::getApplication();
		$user		= JFactory::getUser();
		$layout		= $this->getLayout();
		$pathway	= $app->getPathWay();
		$menus		= $app->getMenu();
		$menu		= $menus->getActive();
		$jconfig	= JFactory::getConfig();
		$tpl		= $app->input->get('tpl', null);
		$skipMeta	= false;
		
		// Get menu parameters , user permission etc.
		$this->user			= $this->get('User');
		$this->admin		= rseventsproHelper::admin();
		$this->params		= rseventsproHelper::getParams();
		$this->permissions	= rseventsproHelper::permissions();
		$this->pdf			= rseventsproHelper::pdf();
		$this->config		= rseventsproHelper::getConfig();
		$this->operator		= $this->get('Operator');
		$this->document		= JFactory::getDocument();
		$this->root			= JUri::getInstance()->toString(array('scheme','host','port'));
		$this->modal_width 	= !empty($this->config->modal_width) ? (int) $this->config->modal_width : 800;
		$this->modal_height = !empty($this->config->modal_height) ? (int) $this->config->modal_height : 600;
		$this->captcha_use	= $this->config->captcha_use ? explode(',',$this->config->captcha_use) : array();
		$this->mask			= empty($this->config->payment_mask) ? '%p %c' : $this->config->payment_mask;
		$this->currency		= empty($this->config->payment_currency_sign) ? $this->config->payment_currency : $this->config->payment_currency_sign;
		$this->decimals		= $this->config->payment_decimals;
		$this->decimal		= $this->config->payment_decimal;
		$this->thousands	= $this->config->payment_thousands;
		
		$this->timezoneReturn	= base64_encode(JUri::getInstance());
		$this->timezone			= JFactory::getConfig()->get('offset');
		
		// Load RSEvents!Pro plugins
		rseventsproHelper::loadPlugins();
		
		if ($menu && isset($menu->title)) {
			$title = $menu->title;
			
			if ($jconfig->get('sitename_pagetitles', 0) == 1) {
				$title = JText::sprintf('JPAGETITLE', $jconfig->get('sitename'), $title);
			} elseif ($jconfig->get('sitename_pagetitles', 0) == 2) {
				$title = JText::sprintf('JPAGETITLE', $title, $jconfig->get('sitename'));
			}
			
			$this->document->setTitle($title);
		}
		
		// Add search bar
		if ($this->params->get('search',1)) {
			if ($this->document->getType() == 'html') {
				$this->document->addCustomTag('<script src="'.JHtml::script('com_rseventspro/jquery.filter.'.(rseventsproHelper::isJ4() ? 'j4' : 'j3').'.js', array('relative' => true, 'pathOnly' => true, 'version' => 'auto')).'" type="text/javascript"></script>');
			}
		}
		
		if ($layout == 'default') {
			// Get events
			$this->events	= $this->get('events');
			$this->total	= $this->get('total');
			$this->fid		= $this->get('FilterId');
			
			// Add feed
			if ($this->params->get('rss',1)) {
				$link	= '&format=feed';
				$attribs = array('type' => 'application/rss+xml', 'title' => 'RSS 2.0');
				$this->document->addHeadLink(JRoute::_($link.'&type=rss'), 'alternate', 'rel', $attribs);
				$attribs = array('type' => 'application/atom+xml', 'title' => 'Atom 1.0');
				$this->document->addHeadLink(JRoute::_($link.'&type=atom'), 'alternate', 'rel', $attribs);
			}
			
			$filters			= $this->get('filters');
			$this->columns		= $filters[0];
			$this->operators	= $filters[1];
			$this->values		= $filters[2];
			$this->extra		= $this->get('ExtraFilters');
			$this->showCondition= $this->get('Conditions');
			$this->category		= $this->get('EventCategory');
			$this->tag			= $this->get('EventTag');
			$this->location		= $this->get('EventLocation');
			
			// detect if current page is the homepage
			$currentItemId = ($menu ? $menu->id : $app->input->getInt('Itemid', 0));
			$lang = '*';
			
			if ($app->getLanguageFilter()) {
				$lang = JFactory::getLanguage()->getTag();
			}
			$home = $menus->getDefault($lang);
			$isHomePage = ($currentItemId && $home && $currentItemId == $home->id);
			
			$menuTitle = $this->document->getTitle();
			$siteName = JFactory::getConfig()->get('sitename');
			
			$menuTitle = $menuTitle == $siteName ? '' : $menuTitle;
			$modifyTitle = (!$isHomePage || ($isHomePage && empty($menuTitle)));
			// end detection
			
			if ($modifyTitle) {
				$title = null;
				
				if ($this->config->seo_title) {
					if ($this->category) {
						$skipMeta = true;
						$title = JText::sprintf('COM_RSEVENTSPRO_EVENTS_CATEGORY_TITLE_SEO',$this->category->title);
					} elseif ($this->location) {
						$skipMeta = true;
						$title = JText::sprintf('COM_RSEVENTSPRO_EVENTS_LOCATION_TITLE_SEO',$this->location);
					} elseif ($this->tag) {
						$skipMeta = true;
						$title = JText::sprintf('COM_RSEVENTSPRO_EVENTS_TAG_TITLE_SEO',$this->tag);
					}
				}
				
				if (!is_null($title)) {
					if ($jconfig->get('sitename_pagetitles', 0) == 1) {
						$title = JText::sprintf('JPAGETITLE', $jconfig->get('sitename'), $title);
					} elseif ($jconfig->get('sitename_pagetitles', 0) == 2) {
						$title = JText::sprintf('JPAGETITLE', $title, $jconfig->get('sitename'));
					}
					
					$this->document->setTitle($title);
				}
			}
			
			// Price slider assets
			JHtml::stylesheet('com_rseventspro/bootstrap-slider.css', array('relative' => true, 'version' => 'auto'));
			JHtml::script('com_rseventspro/bootstrap-slider.js', array('relative' => true, 'version' => 'auto'));
			$this->maxPrice = $this->get('MaxPrice');
			
			//set the pathway
			if (!$menu) 
				$pathway->addItem(JText::_('COM_RSEVENTSPRO_BC_EVENTS'));
			
		} elseif ($layout == 'edit') {
			// Check for permission
			$id				= $app->input->getInt('id');
			$this->owner	= $this->get('owner');
			$this->states	= array('published' => true, 'unpublished' => true, 'archived' => false, 'trash' => false, 'all' => false);
			
			$this->tab	= $app->input->getInt('tab');
			$this->item	= $this->get('Event');
			$this->form	= $this->get('Form');
			$this->dependencies	= $this->get('FormDependencies');
			$this->ticketsform	= $this->get('FormTickets');
			$this->couponsform	= $this->get('FormCoupons');
			
			$permission_denied = false;
			if (!$this->admin) {
				if (!$id) {
					if (empty($this->permissions['can_post_events']))
						$permission_denied = true;
				} else {
					if (($this->user != $this->owner && empty($this->permissions['can_edit_events'])) || empty($this->user))
						$permission_denied = true;
				}
			}
			
			if ($permission_denied) {
				if ($user->get('guest')) {
					$app->enqueueMessage(JText::_('COM_RSEVENTSPRO_PLEASE_LOGIN_TO_CREATE_EVENT'));
					$app->redirect(JRoute::_('index.php?option=com_users&view=login&return='.base64_encode(JURI::getInstance()),false));
				} else {
					if (!$id) {
						rseventsproHelper::error(JText::_('COM_RSEVENTSPRO_ERROR_NO_CREATION_PERMISSION'), rseventsproHelper::route('index.php?option=com_rseventspro&layout=default',false, RseventsproHelperRoute::getEventsItemid('999999')));
					} else {
						rseventsproHelper::error(JText::_('COM_RSEVENTSPRO_ERROR_NO_EDIT_PERMISSION'), rseventsproHelper::route('index.php?option=com_rseventspro&layout=show&id='.rseventsproHelper::sef($this->item->id,$this->item->name),false,rseventsproHelper::itemid($this->item->id)));
					}
				}
			}
			
			if ($id == 0) {
				
				if (!empty($this->permissions['limit_events'])) {
					if (rseventsproHelper::getUserEventCount() >= $this->permissions['limit_events']) {
						rseventsproHelper::error(JText::_('COM_RSEVENTSPRO_ERROR_CREATE_LIMIT'), rseventsproHelper::route('index.php?option=com_rseventspro&layout=default',false, RseventsproHelperRoute::getEventsItemid('999999')));
					}
				}
				
				if ($date = $app->input->get('date')) {
					$time = JFactory::getDate()->format('H:i:s');
					$start = JFactory::getDate($date.' '.$time);
					$end = clone($start);
					$end->modify('+2 hours');
					
					$array = array('start' => $start->toSql(), 'end' => $end->toSql());
					$app->input->set('jform', $array);
				}
				
				// Set new state
				$app->input->set('new',1);
				// Get the model
				$model = $this->getModel();
				// Save event
				$model->save();
				// Redirect
				$app->redirect(rseventsproHelper::route('index.php?option=com_rseventspro&layout=edit&id='.rseventsproHelper::sef($model->getState('eventid'),$model->getState('eventname')),false));
			}
			
			// Load scripts
			if (!$tpl) {
				if ($this->document->getType() == 'html') {
					$this->document->addCustomTag('<script src="'.JHtml::script('com_rseventspro/edit.js', array('relative' => true, 'pathOnly' => true, 'version' => 'auto')).'" type="text/javascript"></script>');
				}
				JHtml::stylesheet('com_rseventspro/edit'.(rseventsproHelper::isJ4() ? '.j4' : '').'.css', array('relative' => true, 'version' => 'auto'));
				
				if ($this->config->modaltype == 2) {
					$modalInit = array();
					
					$modalInit[] = 'jQuery("a[rel=\'rs_rsform\']").colorbox({iframe:true, maxWidth:\'95%\', maxHeight:\'95%\', innerWidth:'.$this->modal_width.', innerHeight:'.$this->modal_height.', title:\''.JText::_('COM_RSEVENTSPRO_SELECT_FORM',true).'\'});';
					$modalInit[] = 'jQuery("a[rel=\'rs_etickets\']").colorbox({iframe:true, maxWidth:\'95%\', maxHeight:\'95%\', innerWidth:'.$this->modal_width.', innerHeight:'.$this->modal_height.', title:\'\'});';
					
					if ($modalInit) {
						$modalJS = array();
						$modalJS[] = '<script type="text/javascript">';
						$modalJS[] = 'jQuery(document).ready(function(){';
						$modalJS[] = implode("\n", $modalInit);
						$modalJS[] = '});';
						$modalJS[] = '</script>';
						$this->document->addCustomTag(implode("\n", $modalJS));
					}	
				}
			}
			
			if (rseventsproHelper::isJ4()) {
				JHtml::script('com_rseventspro/jquery-ui.min.js', array('relative' => true, 'version' => 'auto'));
			} else {
				JHtml::_('formbehavior.chosen', '#speakers, #sponsors, #groups, #categories, #jform_payments, #ticket_groups, #coupon_groups, #jform_repeat_also, #jform_exclude_dates, #repeat_days, .rsepro-chosen');
				JHtml::_('jquery.ui', array('core', 'sortable'));
				JHtml::_('rseventspro.tags', '#tags');
			}
			
			// Load custom scripts
			$app->triggerEvent('onrsepro_addCustomScripts');
			
			if (!$tpl) {
				if ($this->config->map && (!empty($this->permissions['can_add_locations']) || $this->admin)) {
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
			
			require_once JPATH_SITE.'/components/com_rseventspro/helpers/events.php';
			$this->eventClass	= RSEvent::getInstance($this->item->id);
			$this->tickets		= $this->eventClass->getTickets();
			$this->coupons		= $this->eventClass->getCoupons();
			$this->files		= $this->eventClass->getFiles();
			$this->repeats		= $this->eventClass->getRepeats();
			$this->categories	= $this->eventClass->getCategoriesOptions();
			
			$this->setTitle(JText::sprintf('COM_RSEVENTSPRO_TITLE_EDIT_EVENT',$this->item->name));
			
			//set the pathway
			$theview = isset($menu->query['layout']) ? $menu->query['layout'] : 'rseventspro';
			if (($menu && $theview != 'show') || !$menu)
				$pathway->addItem($this->item->name,rseventsproHelper::route('index.php?option=com_rseventspro&layout=show&id='.rseventsproHelper::sef($this->item->id,$this->item->name),false,rseventsproHelper::itemid($this->item->id)));
			
			$pathway->addItem(JText::_('COM_RSEVENTSPRO_BC_EDIT_EVENT'));
			
		} elseif ($layout == 'file') {
		
			$app->input->set('from','file');
			$this->owner = $this->get('owner');
			$permission_denied = false;
			
			if (!$this->admin) {
				if (($this->user != $this->owner && empty($this->permissions['can_edit_events'])) || empty($this->user))
					$permission_denied = true;
			}
			
			if ($permission_denied) {
				rseventsproHelper::error(JText::_('COM_RSEVENTSPRO_ERROR_EDIT_FILE'), rseventsproHelper::route('index.php?option=com_rseventspro&layout=default',false));
			}
			
			$this->row = $this->get('file');
			
		} elseif ($layout == 'upload') {
			
			$this->owner = $this->get('owner');
			$permission_denied = false;
			
			if (!$this->admin) {
				if (($this->user != $this->owner && empty($this->permissions['can_edit_events'])) || empty($this->user))
					$permission_denied = true;
			}
			
			if ($permission_denied) {
				rseventsproHelper::error(JText::_('COM_RSEVENTSPRO_ERROR_UPLOAD_ICON'), rseventsproHelper::route('index.php?option=com_rseventspro&layout=default',false));
			}
			
			// Load scripts
			if ($this->document->getType() == 'html') {
				$this->document->addCustomTag('<script src="'.JHtml::script('com_rseventspro/jquery.imgareaselect.pack.js', array('relative' => true, 'pathOnly' => true, 'version' => 'auto')).'" type="text/javascript"></script>');
			}
			JHtml::stylesheet('com_rseventspro/imgareaselect-animated.css', array('relative' => true, 'version' => 'auto'));
			
			$this->item			= $this->get('Event');
			$this->icon			= $this->get('icon');
			
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
			
			if (!empty($this->item->icon) && !file_exists(JPATH_SITE.'/components/com_rseventspro/assets/images/events/'.$this->item->icon)) {
				$this->item->icon = '';
				$this->icon = '';
			}
			
		} elseif ($layout == 'forms') {
			
			$this->owner = $this->get('owner');
			$permission_denied = false;
			
			if (!$this->admin) {
				if (($this->user != $this->owner && empty($this->permissions['can_edit_events'])) || empty($this->user))
					$permission_denied = true;
			}
			
			if ($permission_denied) {
				rseventsproHelper::error(JText::_('COM_RSEVENTSPRO_GLOBAL_PERMISSION_DENIED'), rseventsproHelper::route('index.php?option=com_rseventspro&layout=default',false));
			}
			
			$this->forms		= $this->get('forms');
			$this->pagination	= $this->get('formspagination');
			
		} elseif ($layout == 'location') {
			$this->row	= $this->get('location');
			
			$this->setTitle($this->row->name);
			
			$marker = array(
				'title' => $this->row->name,
				'position' => $this->row->coordinates,
				'content' => '<div id="content"><h3>'.$this->row->name.'</h3> <br /> '.JText::_('COM_RSEVENTSPRO_LOCATION_ADDRESS',true).': '.$this->row->address.(!empty($this->row->url) ? '<br /><a target="_blank" href="'.$this->row->url.'">'.$this->row->url.'</a>' : '').'</div>'
			);
			
			if ($this->row->marker) $marker['icon'] = rseventsproHelper::showMarker($this->row->marker);
			
			$mapParams = array(
				'id' => 'map-canvas',
				'locationCoordonates' => $this->row->coordinates,
				'directionsBtn' => 'rsepro-get-directions',
				'directionsPanel' => 'rsepro-directions-panel',
				'directionsFrom' => 'rsepro-directions-from',
				'directionNoResults' => JText::_('COM_RSEVENTSPRO_DIRECTIONS_NO_RESULT',true),
				'zoom' => (int) $this->config->google_map_zoom,
				'center' => $this->config->google_maps_center,
				'markerDraggable' => 'false',
				'markers' => array($marker)
			);
			
			rseventsproMapHelper::loadMap($mapParams);
			
			//set the pathway
			$pathway->addItem(JText::_('COM_RSEVENTSPRO_BC_LOCATION'));
			
		} elseif ($layout == 'locations') {
		
			$this->locations	= $this->get('locations');
			$this->total		= $this->get('totallocations');
			
		} elseif ($layout == 'editlocation') {
			
			if (empty($this->permissions['can_edit_locations']) && !$this->admin) {
				rseventsproHelper::error(JText::_('COM_RSEVENTSPRO_ERROR_EDIT_LOCATION'), rseventsproHelper::route('index.php?option=com_rseventspro&layout=locations',false));
			}
			
			$mapParams = array(
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
			
			rseventsproMapHelper::loadMap($mapParams);
			
			// Load custom scripts
			$app->triggerEvent('onrsepro_addCustomScripts');
			
			$this->row  = $this->get('location');
			$this->form = $this->get('LocationForm');
			
			$this->setTitle(JText::sprintf('COM_RSEVENTSPRO_TITLE_EDIT_LOCATION', $this->row->name));
			
			//set the pathway
			$pathway->addItem(JText::_('COM_RSEVENTSPRO_BC_EDIT_LOCATION'));
			
		} elseif ($layout == 'categories') {
		
			$this->categories	= $this->get('categories');
			$this->total		= $this->get('totalcategories');
		
		} elseif ($layout == 'map') {
			
			$filters			= $this->get('filters');
			$this->columns		= $filters[0];
			$this->operators	= $filters[1];
			$this->values		= $filters[2];
			$this->extra		= $this->get('ExtraFilters');
			$this->showCondition= $this->get('Conditions');
			
			// Price slider assets
			JHtml::stylesheet('com_rseventspro/bootstrap-slider.css', array('relative' => true, 'version' => 'auto'));
			JHtml::script('com_rseventspro/bootstrap-slider.js', array('relative' => true, 'version' => 'auto'));
			$this->maxPrice = $this->get('MaxPrice');
			
			$this->location	= $this->params->get('default_location', 'Statue of Liberty National Monument, New York, NY 10004, United States');
			$this->radius	= (int) $this->params->get('default_radius', '100');
			$width 			= $this->params->get('map_width', '100%');
			$height 		= $this->params->get('map_height', '400px');
			
			if (strpos($width, '%') !== false) {
				$this->width = $width;
			} elseif (strpos($width, 'px') !== false) {
				$this->width = $width;
			} else {
				$this->width = (int) $width.'px';
			}
			
			if (strpos($height, '%') !== false) {
				$this->height = $height;
			} elseif (strpos($height, 'px') !== false) {
				$this->height = $height;
			} else {
				$this->height = (int) $height.'px';
			}
			
			if ($this->params->get('enable_radius', 0)) {
				$mapParams = array(
					'id' => 'map-canvas',
					'zoom' => (int) $this->config->google_map_zoom,
					'center' => $this->config->google_maps_center,
					'radiusSearch' => '1',
					'radiusLocationId' => 'rsepro-location',
					'radiusValueId' => 'rsepro-radius',
					'radiusUnitId' => 'rsepro-unit',
					'radiusLoaderId' => 'rsepro-loader',
					'radiusBtnId' => 'rsepro-radius-search',
					'use_geolocation' => (int) $this->params->get('use_geolocation',0),
					'circleColor' => $this->params->get('circle_color','#ff8080'),
					'resultsWrapperClass' => 'rsepro-locations-results-wrapper',
					'resultsClass' => 'rsepro-locations-results'
				);
			} else {
				$mapParams = array(
					'id' => 'map-canvas',
					'zoom' => (int) $this->config->google_map_zoom,
					'center' => $this->config->google_maps_center,
					'markerDraggable' => 'false',
					'markers' => rseventsproMapHelper::markers($this->get('eventsmap'))
				);
			}
			
			rseventsproMapHelper::loadMap($mapParams);
			
		} elseif ($layout == 'show' || $layout == 'print') {
			if (!rseventsproHelper::check(JFactory::getApplication()->input->getInt('id')))	{			
				rseventsproHelper::error(JText::_('COM_RSEVENTSPRO_ERROR_INVALID_EVENT'), rseventsproHelper::route('index.php?option=com_rseventspro&layout=default',false));
			}
			
			if ($layout == 'print') {
				$this->document->setMetaData('robots', 'noindex,nofollow');
			}
			
			// Get the event
			$skipMeta				= true;
			$this->event			= $this->get('event');
			$this->cansubscribe		= $this->get('cansubscribe');
			$this->issubscribed		= $this->get('issubscribed');
			$this->canunsubscribe	= $this->get('canunsubscribe');
			$this->report			= $this->get('canreport');
			
			if (!rseventsproHelper::canview($this->event->id) && $this->event->owner != $this->user && !$this->admin) {
				rseventsproHelper::error(JText::_('COM_RSEVENTSPRO_ERROR_VIEW_EVENT'), rseventsproHelper::route('index.php?option=com_rseventspro&layout=default',false));
			}
			
			// Load event metadata
			rseventsproHelper::metas($this->event);
			
			if ($layout == 'show') {
				// Set hits
				rseventsproHelper::hits($this->event->id);
				
				// Set canonical URL if itemid is set
				if ($this->event->itemid) {
					$canonical = rseventsproHelper::route('index.php?option=com_rseventspro&layout=show&id='.rseventsproHelper::sef($this->event->id,$this->event->name),false,rseventsproHelper::itemid($this->event->id));
					$this->document->addHeadLink($canonical, 'canonical', 'rel');
				}
			}
			
			$this->options		= rseventsproHelper::options($this->event->id);
			$this->guests		= $this->get('guests');
			$this->RSVPguests	= $this->get('RSVPGuests');
			
			// Load jQuery Colorbox modal
			$modalInit = array();
			if ($layout == 'show') {
				if ($this->config->modaltype == 2) {
					if ($this->config->modal == 1) {
						$modalInit[] = 'jQuery("a[rel=\'rs_subscribe\']").colorbox({iframe:true, maxWidth:\'95%\', maxHeight:\'95%\', innerWidth:'.$this->modal_width.', innerHeight:'.$this->modal_height.' , title:\''.JText::_('COM_RSEVENTSPRO_EVENT_JOIN',true).'\'});';
						$modalInit[] = 'jQuery("a[rel=\'rs_invite\']").colorbox({iframe:true, maxWidth:\'95%\', maxHeight:\'95%\', innerWidth:'.$this->modal_width.', innerHeight:'.$this->modal_height.', title:\''.JText::_('COM_RSEVENTSPRO_EVENT_INVITE',true).'\'});';
						$modalInit[] = 'jQuery("a[rel=\'rs_message\']").colorbox({iframe:true, maxWidth:\'95%\', maxHeight:\'95%\', innerWidth:'.$this->modal_width.', innerHeight:'.$this->modal_height.', title:\''.JText::_('COM_RSEVENTSPRO_EVENT_MESSAGE_TO_GUESTS',true).'\'});';
					}
					
					if ($this->report) {
						$modalInit[] = 'jQuery("a[rel=\'rs_report\']").colorbox({iframe:true, maxWidth:\'95%\', maxHeight:\'95%\', innerWidth:'.$this->modal_width.', innerHeight:'.$this->modal_height.', title:\'\'});';
					}
					
					$modalInit[] = 'jQuery("a[rel=\'rs_unsubscribe\']").colorbox({iframe:true, maxWidth:\'95%\', maxHeight:\'95%\', innerWidth:'.$this->modal_width.', innerHeight:'.$this->modal_height.', title:\''.JText::_('COM_RSEVENTSPRO_UNSUBSCRIBE_UNSUBSCRIBE',true).'\'});';
					$modalInit[] = 'jQuery("a[rel=\'rs_image\']").colorbox({photo:true, maxWidth:\'95%\', maxHeight:\'95%\', innerWidth:'.$this->modal_width.', innerHeight:'.$this->modal_height.', title:\'\'});';
				}
				
				if ($modalInit) {
					$modalJS = array();
					$modalJS[] = '<script type="text/javascript">';
					$modalJS[] = 'jQuery(document).ready(function(){';
					$modalJS[] = implode("\n", $modalInit);
					$modalJS[] = '});';
					$modalJS[] = '</script>';
					$this->document->addCustomTag(implode("\n", $modalJS));
				}
			}
			
			//set the pathway
			$theview = isset($menu->query['layout']) ? $menu->query['layout'] : 'rseventspro';
			if (($menu && $theview != 'show') || !$menu)
				$pathway->addItem($this->event->name);
			
			// date helping functions
			$nowunix = JFactory::getDate()->toUnix();
			
			if ($this->event->allday) {
				$date = JFactory::getDate($this->event->start);
				$date->modify('+1 days');
				$endunix = $date->toUnix();
			} else {
				$endunix = JFactory::getDate($this->event->end)->toUnix();
			}
			$this->eventended = $endunix < $nowunix;
		} elseif ($layout == 'subscribe') {
			$app->input->set('cid',$app->input->getInt('id'));
			
			$this->event		= $this->get('event');
			$this->cansubscribe	= $this->get('cansubscribe');
			
			$this->js = $this->config->multi_tickets ? 'rs_get_ticket(this);' : 'rsepro_add_single_ticket(this);';
			
			if (rseventsproHelper::validWaitingList($this->event->id) && $this->event->ticketsconfig) {
				$this->event->ticketsconfig = false;
			}
			
			if ($this->event->ticketsconfig) {
				$this->updatefunction = 'rsepro_multi_seats_total();';
			} else {
				if ($this->config->multi_tickets) {
					$this->updatefunction = 'rsepro_multi_total();';
				} else {
					$this->updatefunction = 'rsepro_single_total();';
				}
			}
			
			$thankyou = false;
			if ($this->event->form) {
				$formparams = JFactory::getSession()->get('com_rsform.formparams.formId'.$this->event->form);
				if (isset($formparams->formProcessed)) 
					$thankyou = true;
			}
			
			$this->thankyou = $thankyou;
			
			// If the Force login option is enabled and the current user is not logged in redirect the user
			if ($this->config->must_login && $user->get('id') == 0) {
				if ($this->config->modal == 0) {
					$app->enqueueMessage(JText::_('COM_RSEVENTSPRO_PLEASE_LOGIN'));
					$app->redirect(rseventsproHelper::route('index.php?option=com_rseventspro&layout=show&id='.rseventsproHelper::sef($this->event->id,$this->event->name),false,rseventsproHelper::itemid($this->event->id)));
				} else {
					echo rseventsproHelper::redirect(true,JText::_('COM_RSEVENTSPRO_PLEASE_LOGIN'),rseventsproHelper::route('index.php?option=com_rseventspro&layout=show&id='.rseventsproHelper::sef($this->event->id,$this->event->name),false,rseventsproHelper::itemid($this->event->id)),false,true);
					return;
				}
			}
			
			// If the user cannot view this event or there is no registration to the event -> redirect
			if (!rseventsproHelper::canview($this->event->id) && $this->event->owner != $this->user && !$this->admin) {
				rseventsproHelper::error(JText::_('COM_RSEVENTSPRO_ERROR_VIEW_EVENT'), rseventsproHelper::route('index.php?option=com_rseventspro&layout=show&id='.rseventsproHelper::sef($this->event->id,$this->event->name),false,rseventsproHelper::itemid($this->event->id)));
			}
			
			// Can the current user view the subscription form
			if (!rseventsproHelper::validWaitingList($this->event->id)) {
				if (!$this->cansubscribe['status'] && !$this->thankyou) {
					rseventsproHelper::error($this->cansubscribe['err'], rseventsproHelper::route('index.php?option=com_rseventspro&layout=show&id='.rseventsproHelper::sef($this->event->id,$this->event->name),false,rseventsproHelper::itemid($this->event->id)));
				}
			}
			
			if ($this->config->modal == 1)
				$app->input->set('tmpl','component');
			
			$this->form = rseventsproHelper::rsform();
			
			// There are no tickets left
			$tickets = $this->get('eventtickets');
			
			$query->clear()
				->select('COUNT('.$db->qn('id').')')
				->from($db->qn('#__rseventspro_tickets'))
				->where($db->qn('ide').' = '.(int) $this->event->id);
			
			$db->setQuery($query);
			$eventtickets = $db->loadResult();
			
			if (!empty($eventtickets) && empty($tickets) && $this->event->form == 0) {
				rseventsproHelper::error(JText::_('COM_RSEVENTSPRO_ERROR_NO_TICKETS_LEFT'), rseventsproHelper::route('index.php?option=com_rseventspro&layout=show&id='.rseventsproHelper::sef($this->event->id,$this->event->name),false,rseventsproHelper::itemid($this->event->id)));
			}
			
			$payments = rseventsproHelper::getPayments(false,$this->event->payments);
			if (!empty($payments)) {
				$default = $this->config->default_payment;
				
				if ($this->config->payment_type) {
					$lists['payments']      = JHTML::_('select.genericlist', $payments, 'payment', 'class="input-large custom-select" onchange="'.$this->updatefunction.'"', 'value' ,'text',$default);
				} else {
					$default				= $default == 'none' ? @$payments[0]->value : $default;
					$lists['payments']      = JHTML::_('select.radiolist', $payments, 'payment', 'class="inputbox" onchange="'.$this->updatefunction.'"', 'value' ,'text',$default);
				}
			}
			
			$this->user			= $user;
			$this->tickets		= $tickets;
			$this->payment		= $this->get('ticketpayment');
			$this->payments		= !empty($payments);
			
			if (in_array(2,$this->captcha_use) && empty($this->event->form) && !rseventsproHelper::isCart()) {
				if ($this->config->captcha == 2) {
					rseventsproHelper::gRecaptcha('rse-g-recaptcha');
				} elseif ($this->config->captcha == 3) {
					rseventsproHelper::hCaptcha('subscribe');
				}
			}
			
			$this->setTitle(JText::sprintf('COM_RSEVENTSPRO_SUBSCRIBE_TO',$this->event->name));
			
			//set the pathway
			$theview = isset($menu->query['layout']) ? $menu->query['layout'] : 'rseventspro';
			if (($menu && $theview != 'show') || !$menu)
				$pathway->addItem($this->event->name,rseventsproHelper::route('index.php?option=com_rseventspro&layout=show&id='.rseventsproHelper::sef($this->event->id,$this->event->name),false,rseventsproHelper::itemid($this->event->id)));
			
			$pathway->addItem(JText::_('COM_RSEVENTSPRO_BC_SUBSCRIBE'));
			
		} elseif ($layout == 'wire') {
			
			$this->data		= $this->get('subscriber');
			$this->payment  = $this->get('payment');
			
			if (empty($this->data['data'])) {
				$app->enqueueMessage(JText::_('COM_RSEVENTSPRO_INVALID_SUBSCRIPTION'),'error');
				$app->redirect(rseventsproHelper::route('index.php?option=com_rseventspro',false));
			}
			
			if ($this->data['data']->state == 1 || $this->data['data']->state == 2) {
				if ($this->data['data']->state == 1) {
					$app->enqueueMessage(JText::_('COM_RSEVENTSPRO_SUBSCRIPTION_STATE_COMPLETE'),'error');
				} else {
					$app->enqueueMessage(JText::_('COM_RSEVENTSPRO_SUBSCRIPTION_STATE_DENIED'),'error');
				}
				
				$app->redirect(rseventsproHelper::route('index.php?option=com_rseventspro&layout=show&id='.rseventsproHelper::sef($this->data['event']->id, $this->data['event']->name),false,rseventsproHelper::itemid($this->data['event']->id)));
			}
			
			//set the pathway
			$theview = isset($menu->query['layout']) ? $menu->query['layout'] : 'rseventspro';
			if (($menu && $theview != 'show') || !$menu) {
				if (isset($this->data['event']->id) && isset($this->data['event']->name)) {
					$pathway->addItem($this->data['event']->name,rseventsproHelper::route('index.php?option=com_rseventspro&layout=show&id='.rseventsproHelper::sef($this->data['event']->id,$this->data['event']->name),false,rseventsproHelper::itemid($this->data['event']->id)));
				}
			}
			
			$this->setTitle($this->payment->name);
			
			$pathway->addItem(JText::_('COM_RSEVENTSPRO_BC_WIRE'));
			
		} elseif ($layout == 'subscriptions') {
			$this->showform = $this->get('ShowForm');
			$this->code		= $app->input->getString('code') ? '&code='.$app->input->getString('code') : '';
			$this->return	= base64_encode(JUri::getInstance());
			
			if (!$this->showform) {
				$this->subscriptions = $this->get('subscriptions');
				$this->rsvpsubscriptions = $this->get('rsvpsubscriptions');
			}
			
			$this->setTitle(JText::_('COM_RSEVENTSPRO_TITLE_MY_SUBSCRIPTIONS'));
			
		} elseif ($layout == 'subscribers') {
			
			$this->row = $this->get('event');
			
			if ($this->admin || $this->row->owner == $user->get('id')) {
				$states = array_merge(array(JHTML::_('select.option', '-', JText::_('COM_RSEVENTSPRO_GLOBAL_SELECT_STATE'))), $this->get('statuses'));
				$lists['state'] = JHTML::_('select.genericlist', $states, 'state', 'size="1" onchange="document.adminForm.submit();" class="custom-select"','value','text',$app->getUserState('com_rseventspro.subscriptions.state.frontend'));
				
				$lists['tickets'] = JHTML::_('select.genericlist', $this->get('ticketsfromevent'), 'ticket', 'size="1" onchange="document.adminForm.submit();" class="custom-select"','value','text',$app->getUserState('com_rseventspro.subscriptions.ticket.frontend'));
				
				$this->data			= $this->get('subscribers');
				$this->total		= $this->get('totalsubscribers');
				$this->tickets		= rseventsproHelper::getTickets($this->row->id, false);
				$this->filter_word	= $app->getUserState('com_rseventspro.subscriptions.search_frontend');
				
				$this->setTitle(JText::sprintf('COM_RSEVENTSPRO_TITLE_SUBSCRIBERS',$this->row->name));
				
				//set the pathway
				$theview = isset($menu->query['layout']) ? $menu->query['layout'] : 'rseventspro';
				if (($menu && $theview != 'show') || !$menu)
					$pathway->addItem($this->row->name,rseventsproHelper::route('index.php?option=com_rseventspro&layout=show&id='.rseventsproHelper::sef($this->row->id,$this->row->name),false,rseventsproHelper::itemid($this->row->id)));
				
				$pathway->addItem(JText::_('COM_RSEVENTSPRO_BC_SUBSCRIBERS'));
				
			} else {
				rseventsproHelper::error(JText::_('COM_RSEVENTSPRO_ERROR_SUBSCRIBERS_VIEW'), rseventsproHelper::route('index.php?option=com_rseventspro&layout=show&id='.rseventsproHelper::sef($this->row->id,$this->row->name),false,rseventsproHelper::itemid($this->row->id)));
			}
		
		} elseif ($layout == 'editsubscriber') {
			$this->data  = $this->get('subscriber');
			$this->email = $this->get('EmailFromCode');
			$this->code	 = $app->input->getString('code') ? '&code='.$app->input->getString('code') : '';
			$this->rlink = $app->input->getString('return') ? base64_decode($app->input->getString('return')) : false;
			
			$userid		 = $user->get('id');
			
			if ($this->admin || $this->data['event']->owner == $user->get('id') || ($userid > 0 && $this->data['data']->idu == $userid) || $this->data['data']->email == $this->email) {
				$this->form  = $this->get('SubscriberForm');
				$this->fields	= $this->get('fields');
				
				$tparams = $this->data['data']->gateway == 'offline' ? $this->get('card') : $this->data['data']->params;
				$this->tparams = $tparams;
				
				$this->user = ($this->data['data']->idu == $user->get('id') || $this->data['data']->email == $this->email) && $this->data['event']->owner != $user->get('id');
				
				if ($this->config->modaltype == 2) {
					$modalJS = array();
					$modalJS[] = '<script type="text/javascript">';
					$modalJS[] = 'jQuery(document).ready(function(){';
					$modalJS[] = 'jQuery("a[rel=\'rs_seats\']").colorbox({iframe:true, maxWidth:\'95%\', maxHeight:\'95%\', innerWidth:'.$this->modal_width.', innerHeight:'.$this->modal_height.', title:\'\'});';
					$modalJS[] = '});';
					$modalJS[] = '</script>';
					$this->document->addCustomTag(implode("\n", $modalJS));
				}
				
				$this->setTitle(JText::sprintf('COM_RSEVENTSPRO_TITLE_EDIT_SUBSCRIBER',$this->data['data']->name));
				
				//set the pathway
				$theview = isset($menu->query['layout']) ? $menu->query['layout'] : 'rseventspro';
				if (($menu && $theview != 'show') || !$menu)
					$pathway->addItem($this->data['event']->name,rseventsproHelper::route('index.php?option=com_rseventspro&layout=show&id='.rseventsproHelper::sef($this->data['event']->id,$this->data['event']->name),false,rseventsproHelper::itemid($this->data['event']->id)));
				
				$pathway->addItem(JText::_('COM_RSEVENTSPRO_BC_EDIT_SUBSCRIBER'));
				
			} else {
				rseventsproHelper::error(JText::_('COM_RSEVENTSPRO_ERROR_EDIT_SUBSCRIBER'), rseventsproHelper::route('index.php?option=com_rseventspro&layout=show&id='.rseventsproHelper::sef($this->data['event']->id,$this->data['event']->name),false,rseventsproHelper::itemid($this->data['event']->id)));
			}
		
		} elseif ($layout == 'message') {
			
			$this->event = $this->get('event');
			
			if ($this->admin || $this->event->owner == $user->get('id')) {
				$this->subscribers = $this->get('people');
				
				$this->setTitle(JText::sprintf('COM_RSEVENTSPRO_TITLE_MESSAGE_GUESTS', $this->event->name));
				
				//set the pathway
				$theview = isset($menu->query['layout']) ? $menu->query['layout'] : 'rseventspro';
				if (($menu && $theview != 'show') || !$menu)
					$pathway->addItem($this->event->name,rseventsproHelper::route('index.php?option=com_rseventspro&layout=show&id='.rseventsproHelper::sef($this->event->id,$this->event->name),false,rseventsproHelper::itemid($this->event->id)));
					
				$pathway->addItem(JText::_('COM_RSEVENTSPRO_BC_SEND_MESSAGE'));
			} else {
				rseventsproHelper::error(JText::_('COM_RSEVENTSPRO_ERROR_SEND_MESSAGE_GUESTS'), rseventsproHelper::route('index.php?option=com_rseventspro&layout=show&id='.rseventsproHelper::sef($this->event->id,$this->event->name),false,rseventsproHelper::itemid($this->event->id)));
			}
			
		} elseif ($layout == 'invite') {
		
			$this->form		= $this->get('InviteForm');
			$this->event	= $this->get('event');
			$options = rseventsproHelper::options($this->event->id);
			
			if (!rseventsproHelper::check($this->event->id)) {
				rseventsproHelper::error(JText::_('COM_RSEVENTSPRO_GLOBAL_PERMISSION_DENIED'), rseventsproHelper::route('index.php?option=com_rseventspro&layout=show&id='.rseventsproHelper::sef($this->event->id,$this->event->name),false,rseventsproHelper::itemid($this->event->id)));
			}
			
			$nowunix 	= JFactory::getDate()->toUnix();
			
			if ($this->event->allday) {
				$date = JFactory::getDate($this->event->start);
				$date->modify('+1 days');
				$endunix = $date->toUnix();
			} else {
				$endunix 	= JFactory::getDate($this->event->end)->toUnix();
			}
			$eventended = $endunix < $nowunix;
			
			if ($eventended) {
				rseventsproHelper::error(JText::_('COM_RSEVENTSPRO_ERROR_INVITE_1'), rseventsproHelper::route('index.php?option=com_rseventspro&layout=show&id='.rseventsproHelper::sef($this->event->id,$this->event->name),false,rseventsproHelper::itemid($this->event->id)));
			}
			
			if (empty($options['show_invite'])) {
				rseventsproHelper::error(JText::_('COM_RSEVENTSPRO_ERROR_INVITE_2'), rseventsproHelper::route('index.php?option=com_rseventspro&layout=show&id='.rseventsproHelper::sef($this->event->id,$this->event->name),false,rseventsproHelper::itemid($this->event->id)));
			}
			
			require_once JPATH_SITE.'/components/com_rseventspro/helpers/invite.php';
			$callback		= $this->root.rseventsproHelper::route('index.php?option=com_rseventspro&layout=invite&id='.rseventsproHelper::sef($this->event->id,$this->event->name));
			$this->auth		= RSYahoo::auth($callback);
			$this->contacts	= RSYahoo::getContacts();
			
			if (!empty($this->config->google_client_id)) {
				$this->document->addScript('https://apis.google.com/js/client.js');
			}
			
			if (in_array(1,$this->captcha_use)) {
				if ($this->config->captcha == 2) {
					rseventsproHelper::gRecaptcha('rse-g-recaptcha');
				} elseif ($this->config->captcha == 3) {
					rseventsproHelper::hCaptcha('rseInvite');
				}
			}
			
			$this->setTitle(JText::sprintf('COM_RSEVENTSPRO_INVITE_FRIENDS',$this->event->name));
			
			//set the pathway
			$theview = isset($menu->query['layout']) ? $menu->query['layout'] : 'rseventspro';
			if (($menu && $theview != 'show') || !$menu)
				$pathway->addItem($this->event->name,rseventsproHelper::route('index.php?option=com_rseventspro&layout=show&id='.rseventsproHelper::sef($this->event->id,$this->event->name),false,rseventsproHelper::itemid($this->event->id)));
				
			$pathway->addItem(JText::_('COM_RSEVENTSPRO_BC_INVITE'));
		
		} elseif ($layout == 'unsubscribe') {
			
			$this->event		= $this->get('event');
			$this->issubscribed	= $this->get('issubscribed');
			
			if ($this->issubscribed)
				$this->subscriptions = $this->get('usersubscriptions');
			else {
				rseventsproHelper::error(JText::_('COM_RSEVENTSPRO_GLOBAL_PERMISSION_DENIED'), rseventsproHelper::route('index.php?option=com_rseventspro&layout=show&id='.rseventsproHelper::sef($this->event->id,$this->event->name),false,rseventsproHelper::itemid($this->event->id)));
			}
				
		} elseif ($layout == 'search') {
		
			$this->events	= $this->get('results');
			$this->total	= $this->get('totalresults');
			$this->search	= $app->getUserStateFromRequest('rsepro.search.search', 'rskeyword');
			
		} elseif ($layout == 'tickets') {
			
			$this->event		= $this->get('event');
			$this->cansubscribe	= $this->get('cansubscribe');
			
			// If the user cannot view this event or there is no registration to the event -> redirect
			if (!rseventsproHelper::canview($this->event->id) && $this->event->owner != $this->user && !$this->admin) {
				rseventsproHelper::error(JText::_('COM_RSEVENTSPRO_ERROR_VIEW_EVENT'), rseventsproHelper::route('index.php?option=com_rseventspro&layout=show&id='.rseventsproHelper::sef($this->event->id,$this->event->name),false,rseventsproHelper::itemid($this->event->id)));
			}
			
			// Can the current user view the subscription form
			if (!$this->cansubscribe['status']) {
				rseventsproHelper::error($this->cansubscribe['err'], rseventsproHelper::route('index.php?option=com_rseventspro&layout=show&id='.rseventsproHelper::sef($this->event->id,$this->event->name),false,rseventsproHelper::itemid($this->event->id)));
			}
			
			// There are no tickets left
			$tickets = $this->get('eventtickets');
			
			$query->clear()
				->select('COUNT('.$db->qn('id').')')
				->from($db->qn('#__rseventspro_tickets'))
				->where($db->qn('ide').' = '.(int) $this->event->id);
			
			$db->setQuery($query);
			$eventtickets = $db->loadResult();
			
			if (!empty($eventtickets) && empty($tickets) && $this->event->form == 0) {
				rseventsproHelper::error(JText::_('COM_RSEVENTSPRO_ERROR_NO_TICKETS_LEFT'), rseventsproHelper::route('index.php?option=com_rseventspro&layout=show&id='.rseventsproHelper::sef($this->event->id,$this->event->name),false,rseventsproHelper::itemid($this->event->id)));
			}
			
			JHtml::stylesheet('com_rseventspro/tickets.css', array('relative' => true, 'version' => 'auto'));
			
			$this->tickets = rseventsproHelper::getTickets(JFactory::getApplication()->input->getInt('id',0));
		} elseif ($layout == 'seats') {
			$this->owner = $this->get('owner');
			
			$id = JFactory::getApplication()->input->getInt('id',0);
			$permission_denied = false;
			if (!$this->admin) {
				if (!$id) {
					if (empty($this->permissions['can_post_events']))
						$permission_denied = true;
				} else {
					if (($this->user != $this->owner && empty($this->permissions['can_edit_events'])) || empty($this->user))
						$permission_denied = true;
				}
			}
			
			if ($permission_denied) {
				rseventsproHelper::error(JText::_('COM_RSEVENTSPRO_GLOBAL_PERMISSION_DENIED'), rseventsproHelper::route('index.php?option=com_rseventspro&layout=show&id='.rseventsproHelper::sef($this->row->id,$this->row->name),false,rseventsproHelper::itemid($this->row->id)));
			}
			
			// Load scripts
			JHtml::script('com_rseventspro/jquery-ui.min.js', array('relative' => true, 'version' => 'auto'));
			JHtml::stylesheet('com_rseventspro/tickets.css', array('relative' => true, 'version' => 'auto'));
			$this->tickets = rseventsproHelper::getTickets($app->input->getInt('id',0));
		} elseif ($layout == 'report') {
			$this->report			= $this->get('canreport');
			
			if (!$this->report) {
				rseventsproHelper::error(JText::_('COM_RSEVENTSPRO_ERROR_REPORT'), rseventsproHelper::route('index.php?option=com_rseventspro&layout=default',false));
			}
		} elseif ($layout == 'reports') {
			$this->report		= $this->get('canreport');
			$this->event		= $this->get('event');
			
			if ($this->admin || $this->event->owner == $user->get('id')) {
				$this->reports	= rseventsproHelper::getReports($this->event->id);
				
				$this->setTitle(JText::sprintf('COM_RSEVENTSPRO_TITLE_REPORTS', $this->event->name));
				
			} else {
				rseventsproHelper::error(JText::_('COM_RSEVENTSPRO_ERROR_REPORTS'), rseventsproHelper::route('index.php?option=com_rseventspro&layout=show&id='.rseventsproHelper::sef($this->event->id,$this->event->name),false,rseventsproHelper::itemid($this->event->id)));
			}
			
			if (!rseventsproHelper::check($this->event->id) || !$this->report) {
				rseventsproHelper::error(JText::_('COM_RSEVENTSPRO_GLOBAL_PERMISSION_DENIED'), rseventsproHelper::route('index.php?option=com_rseventspro&layout=show&id='.rseventsproHelper::sef($this->event->id,$this->event->name),false,rseventsproHelper::itemid($this->event->id)));
			}
		} elseif ($layout == 'scan') {
			$this->event		= $this->get('event');
			
			if ($this->event->id && ($this->admin || $this->event->owner == $user->get('id'))) {
				$this->scan			= rseventsproHelper::getScan();
				
				$this->setTitle(JText::_('COM_RSEVENTSPRO_TITLE_SCAN'));
				
				//set the pathway
				$theview = isset($menu->query['layout']) ? $menu->query['layout'] : 'rseventspro';
				if (($menu && $theview != 'show') || !$menu)
					$pathway->addItem($this->event->name,rseventsproHelper::route('index.php?option=com_rseventspro&layout=show&id='.rseventsproHelper::sef($this->event->id,$this->event->name),false,rseventsproHelper::itemid($this->event->id)));
				
				$pathway->addItem(JText::_('COM_RSEVENTSPRO_BC_SCAN'));
				
			} else {
				rseventsproHelper::error(JText::_('COM_RSEVENTSPRO_GLOBAL_PERMISSION_DENIED'), rseventsproHelper::route('index.php?option=com_rseventspro&layout=show&id='.rseventsproHelper::sef($this->event->id,$this->event->name),false,rseventsproHelper::itemid($this->event->id)));
			}
		} elseif ($layout == 'userseats') {
			$this->data = $this->get('subscriber');
			if ($this->admin || $this->data['event']->owner == $user->get('id')) {
				JHtml::stylesheet('com_rseventspro/tickets.css', array('relative' => true, 'version' => 'auto'));
				
				$eventId		= $this->data['event']->id;
				$this->tickets	= rseventsproHelper::getTickets($eventId);
				$this->id		= JFactory::getApplication()->input->getInt('id',0);
			} else {
				rseventsproHelper::error(JText::_('COM_RSEVENTSPRO_GLOBAL_PERMISSION_DENIED'), rseventsproHelper::route('index.php?option=com_rseventspro&layout=show&id='.rseventsproHelper::sef($this->data['event']->id,$this->data['event']->name),false,rseventsproHelper::itemid($this->data['event']->id)));
			}
		} elseif ($layout == 'placeholders') {
			require_once JPATH_SITE.'/components/com_rseventspro/helpers/placeholders.php';
			
			$type 				= JFactory::getApplication()->input->getCmd('type','');
			$this->placeholders = RSEventsProPlaceholders::get($type);
		} elseif ($layout == 'user') {
			$this->id		= $app->input->getInt('id', 0);
			
			if ($this->id == 0) {
				rseventsproHelper::error(JText::_('COM_RSEVENTSPRO_GLOBAL_PERMISSION_DENIED'), rseventsproHelper::route('index.php?option=com_rseventspro',false));
			}
			
			$this->canEdit	= $user->get('id') == $this->id;
			$this->data		= rseventsproHelper::getUserProfile($app->input->getInt('id', 0));
			$this->created	= rseventsproHelper::getUserEvents($app->input->getInt('id', 0));
			$this->joined	= rseventsproHelper::getUserEvents($app->input->getInt('id', 0), 'join');
			
			$this->setTitle(JText::sprintf('COM_RSEVENTSPRO_TITLE_USER_PROFILE', $this->data->name));
			
		} elseif ($layout == 'edituser') {
			
			if ($app->input->getInt('id', 0) == 0) {
				rseventsproHelper::error(JText::_('COM_RSEVENTSPRO_GLOBAL_PERMISSION_DENIED'), rseventsproHelper::route('index.php?option=com_rseventspro',false));
			}
			
			if ($user->get('id') == $app->input->getInt('id', 0)) {
				$this->data = rseventsproHelper::getUserProfile($app->input->getInt('id', 0));
				$form		= JForm::getInstance('user', JPATH_ADMINISTRATOR.'/components/com_rseventspro/models/forms/user.xml', array('control' => 'jform'));
				$form->bind($this->data);
				
				$this->setTitle(JText::sprintf('COM_RSEVENTSPRO_TITLE_EDIT_USER_PROFILE', $this->data->name));
				
				$this->form = $form;
			} else {
				rseventsproHelper::error(JText::_('COM_RSEVENTSPRO_GLOBAL_PERMISSION_DENIED'), rseventsproHelper::route('index.php?option=com_rseventspro',false));
			}
		} elseif ($layout == 'rsvp') {
			$this->row = $this->get('event');
			
			if ($this->admin || $this->row->owner == $user->get('id')) {
				$states = array_merge(array(JHTML::_('select.option', '-', JText::_('COM_RSEVENTSPRO_GLOBAL_SELECT_STATE'))), $this->get('RSVPstatuses'));
				$lists['state'] = JHTML::_('select.genericlist', $states, 'state', 'size="1" onchange="document.adminForm.submit();" class="custom-select"','value','text',$app->getUserState('com_rseventspro.rsvp.state'));
				
				$this->filter_word	= $app->getUserState('com_rseventspro.rsvp.search');
				$this->data			= $this->get('RSVPData');
				$this->total		= $this->get('RSVPTotal');
				
				$this->setTitle(JText::sprintf('COM_RSEVENTSPRO_TITLE_RSVP', $this->row->name));
				
				$theview = isset($menu->query['layout']) ? $menu->query['layout'] : 'rseventspro';
				if (($menu && $theview != 'show') || !$menu)
					$pathway->addItem($this->row->name,rseventsproHelper::route('index.php?option=com_rseventspro&layout=show&id='.rseventsproHelper::sef($this->row->id,$this->row->name),false,rseventsproHelper::itemid($this->row->id)));
				
				$pathway->addItem(JText::_('COM_RSEVENTSPRO_BC_RSVP_GUESTS'));
				
			} else {
				rseventsproHelper::error(JText::_('COM_RSEVENTSPRO_ERROR_RSVP_VIEW'), rseventsproHelper::route('index.php?option=com_rseventspro&layout=show&id='.rseventsproHelper::sef($this->row->id,$this->row->name),false,rseventsproHelper::itemid($this->row->id)));
			}
		} elseif ($layout == 'waiting') {
			$this->event = $this->get('event');
			
			if (!rseventsproHelper::hasWaitingList($this->event->id)) {
				rseventsproHelper::error(JText::_('COM_RSEVENTSPRO_WAITING_ERROR'), rseventsproHelper::route('index.php?option=com_rseventspro&layout=show&id='.rseventsproHelper::sef($this->row->id,$this->row->name),false,rseventsproHelper::itemid($this->row->id)));
			}
			
			if (rseventsproHelper::isInWaitingList()) {
				$app->enqueueMessage(JText::sprintf('COM_RSEVENTSPRO_USER_IN_WAITINGLIST', $user->get('email')));
			}
			
			$this->setTitle(JText::sprintf('COM_RSEVENTSPRO_TITLE_WAITINGLIST', $this->event->name));
			
			$theview = isset($menu->query['layout']) ? $menu->query['layout'] : 'rseventspro';
			if (($menu && $theview != 'show') || !$menu)
				$pathway->addItem($this->event->name,rseventsproHelper::route('index.php?option=com_rseventspro&layout=show&id='.rseventsproHelper::sef($this->event->id,$this->event->name),false,rseventsproHelper::itemid($this->event->id)));
			
			$pathway->addItem(JText::_('COM_RSEVENTSPRO_BC_WAITING_LIST'));
		} elseif ($layout == 'waitinglist') {
			$this->event = $this->get('event');
			
			if ($this->admin || $this->event->owner == $user->get('id')) {
				$this->data			= $this->get('WaitingData');
				$this->total		= $this->get('WaitingTotal');
				
				$this->setTitle(JText::sprintf('COM_RSEVENTSPRO_TITLE_WAITINGLIST', $this->event->name));
				
				$theview = isset($menu->query['layout']) ? $menu->query['layout'] : 'rseventspro';
				if (($menu && $theview != 'show') || !$menu)
					$pathway->addItem($this->event->name,rseventsproHelper::route('index.php?option=com_rseventspro&layout=show&id='.rseventsproHelper::sef($this->event->id,$this->event->name),false,rseventsproHelper::itemid($this->event->id)));
				
				$pathway->addItem(JText::_('COM_RSEVENTSPRO_BC_WAITINGLIST'));
			} else {
				rseventsproHelper::error(JText::_('COM_RSEVENTSPRO_ERROR_WAITINGLIST_VIEW'), rseventsproHelper::route('index.php?option=com_rseventspro&layout=show&id='.rseventsproHelper::sef($this->event->id,$this->event->name),false,rseventsproHelper::itemid($this->event->id)));
			}
		} elseif ($layout == 'editwaitinglist') {
			$this->event = $this->get('event');
			
			if ($this->admin || $this->event->owner == $user->get('id')) {
				$this->row			= $this->get('WaitingUser');
				
				$this->setTitle(JText::_('COM_RSEVENTSPRO_TITLE_EDIT_WAITINGLIST'));
				
				$theview = isset($menu->query['layout']) ? $menu->query['layout'] : 'rseventspro';
				if (($menu && $theview != 'show') || !$menu) {
					$pathway->addItem($this->event->name,rseventsproHelper::route('index.php?option=com_rseventspro&layout=show&id='.rseventsproHelper::sef($this->event->id,$this->event->name),false,rseventsproHelper::itemid($this->event->id)));
					$pathway->addItem(JText::_('COM_RSEVENTSPRO_BC_WAITINGLIST'),rseventsproHelper::route('index.php?option=com_rseventspro&layout=waitinglist&id='.rseventsproHelper::sef($this->event->id,$this->event->name),false,rseventsproHelper::itemid($this->event->id)));
				}
				
				$pathway->addItem(JText::_('COM_RSEVENTSPRO_BC_WAITINGLIST_EDIT'));
			} else {
				rseventsproHelper::error(JText::_('COM_RSEVENTSPRO_ERROR_WAITINGLIST_VIEW'), rseventsproHelper::route('index.php?option=com_rseventspro&layout=show&id='.rseventsproHelper::sef($this->event->id,$this->event->name),false,rseventsproHelper::itemid($this->event->id)));
			}
		} elseif ($layout == 'unsubscribers') {
			$this->row = $this->get('event');
			
			if ($this->admin || $this->row->owner == $user->get('id')) {
				$this->filter_word	= $app->getUserState('com_rseventspro.unsubscribers.search');
				$this->data			= $this->get('UnsubscribersData');
				$this->total		= $this->get('UnsubscribersTotal');
				
				$theview = isset($menu->query['layout']) ? $menu->query['layout'] : 'rseventspro';
				if (($menu && $theview != 'show') || !$menu)
					$pathway->addItem($this->row->name,rseventsproHelper::route('index.php?option=com_rseventspro&layout=show&id='.rseventsproHelper::sef($this->row->id,$this->row->name),false,rseventsproHelper::itemid($this->row->id)));
				
				$pathway->addItem(JText::_('COM_RSEVENTSPRO_BC_UNSUBSCRIBERS'));
				
			} else {
				rseventsproHelper::error(JText::_('COM_RSEVENTSPRO_ERROR_UNSUBSCRIBERS_VIEW'), rseventsproHelper::route('index.php?option=com_rseventspro&layout=show&id='.rseventsproHelper::sef($this->row->id,$this->row->name),false,rseventsproHelper::itemid($this->row->id)));
			}
		}
		
		$this->lists = $lists;
		
		$app->triggerEvent('onrsepro_siteDisplayLayout', array(array('view' => &$this)));
		
		// Add menu metadata
		if (!$skipMeta) {
			if ($this->params->get('page_title')) {
				$title = $this->params->get('page_title');
				
				if ($jconfig->get('sitename_pagetitles', 0) == 1) {
					$title = JText::sprintf('JPAGETITLE', $jconfig->get('sitename'), $title);
				} elseif ($jconfig->get('sitename_pagetitles', 0) == 2) {
					$title = JText::sprintf('JPAGETITLE', $title, $jconfig->get('sitename'));
				}
				
				$this->document->setTitle($title);
			}
			
			if ($this->params->get('menu-meta_description'))
				$this->document->setDescription($this->params->get('menu-meta_description'));

			if ($this->params->get('menu-meta_keywords'))
				$this->document->setMetadata('keywords', $this->params->get('menu-meta_keywords'));

			if ($this->params->get('robots'))
				$this->document->setMetadata('robots', $this->params->get('robots'));
		}
		
		parent::display($tpl);
	}
	
	public function getStatus($state) {
		if ($state == 0) {
			return '<font color="blue">'.JText::_('COM_RSEVENTSPRO_GLOBAL_STATUS_INCOMPLETE').'</font>';
		} elseif ($state == 1) {
			return '<font color="green">'.JText::_('COM_RSEVENTSPRO_GLOBAL_STATUS_COMPLETED').'</font>';
		} elseif ($state == 2) {
			return '<font color="red">'.JText::_('COM_RSEVENTSPRO_GLOBAL_STATUS_DENIED').'</font>';
		}
	}
	
	public function getUser($id) {
		if ($id > 0) {
			return JFactory::getUser($id)->get('username');
		} else return JText::_('COM_RSEVENTSPRO_GLOBAL_GUEST');
	}
	
	public function getNumberEvents($id, $type) {
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);
		$events	= 0;
		
		if ($type == 'categories') {
			static $eventCategoriesIds = array();
			
			if (!isset($eventCategoriesIds[$id])) {
				$query->clear()
					->select($db->qn('e.id'))
					->from($db->qn('#__rseventspro_events','e'))
					->join('left', $db->qn('#__rseventspro_taxonomy','t').' ON '.$db->qn('e.id').' = '.$db->qn('t.ide'))
					->where($db->qn('t.type').' = '.$db->q('category'))
					->where($db->qn('t.id').' = '.(int) $id)
					->where($db->qn('e.completed').' = 1')
					->where($db->qn('e.published').' = 1');
				
				$db->setQuery($query);
				$eventCategoriesIds[$id] = $db->loadColumn();
			}
			
			if (!empty($eventCategoriesIds[$id])) {
				foreach ($eventCategoriesIds[$id] as $eid) {
					if (!rseventsproHelper::canview($eid)) 
						continue;
					$events++;
				}
			}
		} else if ($type == 'locations') {
			static $eventLocationsIds = array();
			
			if (!isset($eventLocationsIds[$id])) {
				$query->clear()
					->select($db->qn('id'))
					->from($db->qn('#__rseventspro_events'))
					->where($db->qn('location').' = '.(int) $id)
					->where($db->qn('completed').' = 1')
					->where($db->qn('published').' = 1');
				
				
				$db->setQuery($query);
				$eventLocationsIds[$id] = $db->loadColumn();
			}
			
			if (!empty($eventLocationsIds[$id])) {
				foreach ($eventLocationsIds[$id] as $eid) {
					if (!rseventsproHelper::canview($eid)) 
						continue;
					$events++;
				}
			}
		}
		
		if (!$events) return;
		return $events.' '.JText::plural('COM_RSEVENTSPRO_CALENDAR_EVENTS',$events);
	}
	
	protected function setTitle($title) {
		$jconfig = JFactory::getConfig();
		
		if ($jconfig->get('sitename_pagetitles', 0) == 1) {
			$title = JText::sprintf('JPAGETITLE', $jconfig->get('sitename'), $title);
		} elseif ($jconfig->get('sitename_pagetitles', 0) == 2) {
			$title = JText::sprintf('JPAGETITLE', $title, $jconfig->get('sitename'));
		}
		
		$this->document->setTitle($title);
	}
}