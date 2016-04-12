<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' );

class RSEvent
{
	/**
	 * Array to hold the object instances
	 *
	 * @var    array
	 */
	public static $instances = array();

	/**
	 * Event ID
	 *
	 * @var    int
	 */
	protected $id;
	
	/**
	 * Class constructor
	 *
	 * @param   int  $id  Event ID
	 *
	 */
	public function __construct($id) {
		$this->id = (int) $id;
	}
	
	/**
	 * Returns a reference to a RSEvent object
	 *
	 * @param   int  $id  Event ID
	 *
	 * @return  RSEvent         RSEvent object
	 *
	 */
	public static function getInstance($id = null) {
		if (!isset(self::$instances[$id])) {
			$classname = 'RSEvent';
			self::$instances[$id] = new $classname($id);
		}
		
		return self::$instances[$id];
	}
	
	/**
	 * Method to get null date
	 *
	 * @return   string  Database null date
	 *
	 */
	public function getNullDate() {
		return JFactory::getDbo()->getNullDate();
	}
	
	
	/**
	 * Method to get RSEvents!Pro Groups
	 *
	 * @return   array  List of groups
	 *
	 */
	public function groups() {
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		
		$query->clear()
			->select($db->qn('id','value'))->select($db->qn('name','text'))
			->from($db->qn('#__rseventspro_groups'));
		
		$db->setQuery($query);
		return $db->loadObjectList();
	}
	
	/**
	 * Method to get Event selected groups
	 *
	 * @return   array  List of selected groups
	 *
	 */
	public function getGroups() {
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		
		$query->clear()
			->select($db->qn('id'))
			->from($db->qn('#__rseventspro_taxonomy'))
			->where($db->qn('type').' = '.$db->q('groups'))
			->where($db->qn('ide').' = '.$this->id);
		
		$db->setQuery($query);
		return $db->loadColumn();
	}
	
	/**
	 * Method to get RSEvents!Pro selected categories
	 *
	 * @return   array  List of selected categories
	 *
	 */
	public function getCategories() {
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		
		$query->clear()
			->select($db->qn('id'))
			->from($db->qn('#__rseventspro_taxonomy'))
			->where($db->qn('type').' = '.$db->q('category'))
			->where($db->qn('ide').' = '.$this->id);
		
		$db->setQuery($query);
		$selected = $db->loadColumn();
		
		if (JFactory::getApplication()->isSite()) {
			rseventsproHelper::allowedCategories($selected);
		}
		
		return $selected;
	}
	
	/**
	 * Method to get RSEvents!Pro categories
	 *
	 * @return   array  List of categories
	 *
	 */
	public function getCategoriesOptions() {
		$db			= JFactory::getDbo();
		$query		= $db->getQuery(true);
		$categories = JHtml::_('category.options','com_rseventspro', array('filter.published' => array(1)));
		$groups		= rseventsproHelper::getUserGroups();
		$disabled	= array();
		
		if ($groups) {
			$query->select($db->qn('restricted_categories'))
				->from($db->qn('#__rseventspro_groups'))
				->where($db->qn('id').' IN ('.implode(',',$groups).')');
			$db->setQuery($query);
			if ($restrictions = $db->loadColumn()) {
				foreach ($restrictions as $restriction) {
					$registry = new JRegistry;
					$registry->loadString($restriction);
					if ($restriction = $registry->toArray()) {
						$disabled = array_merge($disabled, $restriction);
					}
				}
			}
			
			if ($disabled) {
				foreach ($categories as $i => $category) {
					if (in_array($category->value, $disabled)) {
						$categories[$i]->disable = true;
					}
				}
			}
		}
		
		return $categories;
	}
	
	/**
	 * Method to get Event selected tags
	 *
	 * @return   array  List of selected tags
	 *
	 */
	public function getTags($array = false) {
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		
		$query->clear()
			->select($db->qn('t.name'))
			->from($db->qn('#__rseventspro_tags','t'))
			->join('left', $db->qn('#__rseventspro_taxonomy','tx').' ON '.$db->qn('tx.id').' = '.$db->qn('t.id'))
			->where($db->qn('tx.type').' = '.$db->q('tag'))
			->where($db->qn('tx.ide').' = '.$this->id);
		
		$db->setQuery($query);
		$tags = $db->loadColumn();
		
		if ($array) {
			$return = array();
			foreach ($tags as $tag) {
				$return[] = JHtml::_('select.option',$tag,$tag);
			}
			
			return $return;
		} else {
			return implode(',',$tags);
		}
	}
	
	/**
	 * Method to get Event meta keywords
	 *
	 * @return   array  List of selected meta keywords
	 *
	 */
	public function getKeywords() {
		$db 	= JFactory::getDbo();
		$query  = $db->getQuery(true);
		$return = array();
		
		$query->clear()
			->select($db->qn('metakeywords'))
			->from($db->qn('#__rseventspro_events'))
			->where($db->qn('id').' = '.$this->id);
		
		$db->setQuery($query);
		if ($keywords = $db->loadResult()) {
			$keywords = explode(',',$keywords);
			$keywords = array_unique($keywords);
			foreach ($keywords as $keyword) {
				$keyword = trim($keyword);
				$return[] = JHtml::_('select.option',$keyword,$keyword);
			}
		}
		
		return $return;
	}
	
	/**
	 * Method to get Event files
	 *
	 * @return   array  List of files
	 *
	 */
	public function getFiles() {
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		
		$query->clear()
			->select($db->qn('id'))->select($db->qn('name'))
			->from($db->qn('#__rseventspro_files'))
			->where($db->qn('ide').' = '.$this->id);
		
		$db->setQuery($query);
		return $db->loadObjectList();
	}
	
	/**
	 * Method to get Event owner
	 *
	 * @return   string  Owner name
	 *
	 */
	public function getOwner() {
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		
		if (!$this->id)
			return JFactory::getUser()->get('name');
		
		$query->clear()
			->select($db->qn('u.name'))
			->from($db->qn('#__users','u'))
			->join('left', $db->qn('#__rseventspro_events','e').' ON '.$db->qn('e.owner').' = '.$db->qn('u.id'))
			->where($db->qn('e.id').' = '.$this->id);
		
		$db->setQuery($query);
		$owner = $db->loadResult();
		return $owner ? $owner : JText::_('COM_RSEVENTSPRO_GLOBAL_GUEST');
	}
	
	/**
	 * Method to get Event frontend options
	 *
	 * @return   array  A list of event options
	 *
	 */
	public function getEventOptions() {
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$defaults = self::getDefaultOptions();
		
		$query->clear()
			->select($db->qn('options'))
			->from($db->qn('#__rseventspro_events'))
			->where($db->qn('id').' = '.$this->id);
		
		$db->setQuery($query);
		$options = $db->loadResult();
		
		if (!empty($options)) {
			$registry = new JRegistry;
			$registry->loadString($options);
			if ($options = $registry->toArray()) {
				foreach ($options as $option => $value) {
					if (isset($defaults[$option])) {
						$defaults[$option] = $value;
					}
				}
			}
		}
		
		return $defaults;
	}
	
	/**
	 * Method to get Event default options
	 *
	 * @return   array  A list of event default options
	 *
	 */
	public function getDefaultOptions() {
		return rseventsproHelper::getOptions();
	}
	
	public function yesno() {
		return array(JHTML::_('select.option', 0, JText::_('JNO')), JHTML::_('select.option', 1, JText::_('JYES')));
	}
	
	/**
	 * Method to get Event repeat types
	 *
	 * @return   array  A list of event repeat types
	 *
	 */
	public function repeatType() {
		return array(JHTML::_('select.option', 1, JText::_('COM_RSEVENTSPRO_REPEAT_EVERY_DAY')), JHTML::_('select.option', 2, JText::_('COM_RSEVENTSPRO_REPEAT_EVERY_WEEK')), 
			JHTML::_('select.option', 3, JText::_('COM_RSEVENTSPRO_REPEAT_EVERY_MONTH')) ,JHTML::_('select.option', 4, JText::_('COM_RSEVENTSPRO_REPEAT_EVERY_YEAR'))
		);
	}
	
	/**
	 * Method to get repeat days
	 *
	 * @return   array  A list of repeat days
	 *
	 */
	public function repeatDays() {
		return array(JHTML::_('select.option', '1', JText::_('COM_RSEVENTSPRO_MONDAY')),JHTML::_('select.option', '2', JText::_('COM_RSEVENTSPRO_TUESDAY')),
			JHTML::_('select.option', '3',JText::_('COM_RSEVENTSPRO_WEDNESDAY')), JHTML::_('select.option', '4', JText::_('COM_RSEVENTSPRO_THURSDAY')), 
			JHTML::_('select.option', '5', JText::_('COM_RSEVENTSPRO_FRIDAY')), JHTML::_('select.option', '6', JText::_('COM_RSEVENTSPRO_SATURDAY')),
			JHTML::_('select.option', '0', JText::_('COM_RSEVENTSPRO_SUNDAY'))
		);
	}
	
	/**
	 * Method to get Event repeat days
	 *
	 * @return   array  A list of event repeat days
	 *
	 */
	public function repeatEventDays() {
		if ($this->id) {
			$db = JFactory::getDbo();
			$query = $db->getQuery(true);
			
			$query->clear()
				->select($db->qn('id'))
				->from($db->qn('#__rseventspro_taxonomy'))
				->where($db->qn('type').' = '.$db->q('days'))
				->where($db->qn('ide').' = '.$this->id);
			
			$db->setQuery($query);
			return $db->loadColumn();
		} else {
			return array(0,1,2,3,4,5,6);
		}
	}
	
	/**
	 * Method to get Event repeat also dates
	 *
	 * @return   array  A list of event repeat also dates
	 *
	 */
	public function repeatAlso() {
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$days = array();
		
		$query->clear()
			->select($db->qn('repeat_also'))
			->from($db->qn('#__rseventspro_events'))
			->where($db->qn('id').' = '.$this->id);
		
		$db->setQuery($query);
		if ($days = $db->loadResult()) {
			$registry = new JRegistry;
			$registry->loadString($days);
			$days = $registry->toArray();
			
			foreach ($days as $i => $day) {
				$days[$i] = JHtml::_('select.option', $day, $day);
			}
		}
		
		return $days ? $days : array();
	}
	
	/**
	 * Method to get Event repeat exclude dates
	 *
	 * @return   array  A list of event exclude dates
	 *
	 */
	public function excludeDates() {
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$days = array();
		
		$query->clear()
			->select($db->qn('exclude_dates'))
			->from($db->qn('#__rseventspro_events'))
			->where($db->qn('id').' = '.$this->id);
		
		$db->setQuery($query);
		if ($days = $db->loadResult()) {
			$registry = new JRegistry;
			$registry->loadString($days);
			$days = $registry->toArray();
			
			foreach ($days as $i => $day) {
				$days[$i] = JHtml::_('select.option', $day, $day);
			}
		}
		
		return $days ? $days : array();
	}
	
	public function repeatOn() {
		return array(JHTML::_('select.option', 0, JText::_('COM_RSEVENTSPRO_REPEAT_ON_SAME_AS_START')), JHTML::_('select.option', 1, JText::_('COM_RSEVENTSPRO_REPEAT_ON_SPECIFIC_DAY')), 
			JHTML::_('select.option', 2, JText::_('COM_RSEVENTSPRO_REPEAT_ON_SPECIFIC_INTERVAL'))
		);
	}
	
	public function repeatOnOrder() {
		return array(JHTML::_('select.option', 1, JText::_('COM_RSEVENTSPRO_REPEAT_ON_FIRST')), JHTML::_('select.option', 2, JText::_('COM_RSEVENTSPRO_REPEAT_ON_SECOND')), 
			JHTML::_('select.option', 3, JText::_('COM_RSEVENTSPRO_REPEAT_ON_THIRD')), JHTML::_('select.option', 4, JText::_('COM_RSEVENTSPRO_REPEAT_ON_FOURTH')),
			JHTML::_('select.option', 5, JText::_('COM_RSEVENTSPRO_REPEAT_ON_LAST'))
		);
	}
	
	
	/**
	 * Method to get Event selected payment methods
	 *
	 * @return   array  A list of event selected payment methods
	 *
	 */
	public function getPayments() {
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		
		$query->clear()
			->select($db->qn('payments'))
			->from($db->qn('#__rseventspro_events'))
			->where($db->qn('id').' = '.$this->id);
		
		$db->setQuery($query);
		if ($payments = $db->loadResult()) {
			$registry = new JRegistry;
			$registry->loadString($payments);
			return $registry->toArray();
		}
		
		return array();
	}
	
	/**
	 * Method to get Event tickets
	 *
	 * @return   array  Tickets list
	 *
	 */
	public function getTickets($id = null) {
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		
		$query->clear()
			->select('*')
			->from($db->qn('#__rseventspro_tickets'))
			->where($db->qn('ide').' = '.$this->id)
			->order($db->qn('order').' ASC');
			
		if (!is_null($id)) {
			$query->where($db->qn('id').' = '.$db->q($id));
		}
		
		$db->setQuery($query);
		if ($tickets = $db->loadObjectList()) {
			foreach ($tickets as $i => $ticket) {
				if (!empty($ticket->groups)) {
					$registry = new JRegistry;
					$registry->loadString($ticket->groups);
					$tickets[$i]->groups = $registry->toArray();
				}
			}
			
			return $tickets;
		}
		
		return array();
	}
	
	/**
	 * Method to get Event discount types
	 *
	 * @return   array  
	 *
	 */
	public function getDiscountTypes() {
		return array(JHTML::_('select.option', 0, rseventsproHelper::getConfig('payment_currency_sign')), 
			JHTML::_('select.option', 1, '%')
		);
	}
	
	/**
	 * Method to get Event discount actions
	 *
	 * @return   array  
	 *
	 */
	public function getDiscountActions() {
		return array(JHTML::_('select.option', 1, JText::_('COM_RSEVENTSPRO_TOTAL_PRICE')), 
			JHTML::_('select.option', 0, JText::_('COM_RSEVENTSPRO_SINGLE_PRICE'))
		);
	}
	
	/**
	 * Method to get Event coupons
	 *
	 * @return   array  Coupons list
	 *
	 */
	public function getCoupons($id = null) {
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		
		$query->clear()
			->select('*')
			->from($db->qn('#__rseventspro_coupons'))
			->where($db->qn('ide').' = '.$this->id);
		
		if (!is_null($id)) {
			$query->where($db->qn('id').' = '.$db->q($id));
		}
		
		$db->setQuery($query);
		if ($coupons = $db->loadObjectList()) {
			foreach ($coupons as $i => $coupon) {
				$query->clear()
					->select($db->qn('code'))
					->from($db->qn('#__rseventspro_coupon_codes'))
					->where($db->qn('idc').' = '.(int) $coupon->id);
				
				$db->setQuery($query);
				$codes = $db->loadColumn();
				if (!empty($codes)) {
					$coupons[$i]->code = implode("\n",$codes);
				}
				
				if (!empty($coupon->groups)) {
					$registry = new JRegistry;
					$registry->loadString($coupon->groups);
					$coupons[$i]->groups = $registry->toArray();
				}
			}
			return $coupons;
		}
		return array();
	}
	
	/**
	 * Method to get Event selected gallery tags
	 *
	 * @return   array  
	 *
	 */
	public function getSelectedGalleryTags() {
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		
		$query->clear()
			->select($db->qn('gallery_tags'))
			->from($db->qn('#__rseventspro_events'))
			->where($db->qn('id').' = '.$this->id);
		
		$db->setQuery($query);
		if ($tags = $db->loadResult()) {
			$registry = new JRegistry;
			$registry->loadString($tags);
			$tags = $registry->toArray();
			
			return $tags;
		}
		
		return array();
	}
	
	/**
	 * Method to get Event Registration form name
	 *
	 * @return   string
	 *
	 */
	public function getForm() {
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		
		if (!file_exists(JPATH_SITE.'/components/com_rsform/rsform.php')) 
			return JText::_('COM_RSEVENTSPRO_DEFAULT_FORM');
		
		$query->clear()
			->select($db->qn('f.FormName'))
			->from($db->qn('#__rsform_forms','f'))
			->join('left', $db->qn('#__rseventspro_events','e').' ON '.$db->qn('f.FormId').' = '.$db->qn('e.form'))
			->where($db->qn('e.id').' = '.$this->id);

		$db->setQuery($query);
		if ($name = $db->loadResult())
			return $name;
		
		return JText::_('COM_RSEVENTSPRO_DEFAULT_FORM');
	}
	
	/**
	 * Method to get the number of times an event is repeated.
	 *
	 * @return   int
	 *
	 */
	public function getChild() {
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		
		if (!$this->id) 
			return 0;
		
		$query->clear()
			->select('COUNT('.$db->qn('id').')')
			->from($db->qn('#__rseventspro_events'))
			->where($db->qn('parent').' = '.$this->id);
		
		$db->setQuery($query);
		return (int) $db->loadResult();
	}
	
	/**
	 * Method to get the event name.
	 *
	 * @return   string
	 *
	 */
	public function getParent() {
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		
		$query->clear()
			->select($db->qn('name'))
			->from($db->qn('#__rseventspro_events'))
			->where($db->qn('id').' = '.$this->id);
		
		$db->setQuery($query);
		return $db->loadResult();
	}
	
	/**
	 * Method to get the event repeats.
	 *
	 * @return   array
	 *
	 */
	public function getRepeats() {
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		
		$query->clear()
			->select($db->qn('id'))->select($db->qn('name'))
			->select($db->qn('start'))->select($db->qn('end'))
			->select($db->qn('allday'))
			->from($db->qn('#__rseventspro_events'))
			->where($db->qn('parent').' = '.$this->id)
			->order($db->qn('start').' ASC');
		
		$db->setQuery($query);
		return $db->loadObjectList();
	}
	
	/**
	 * Method to get the event.
	 *
	 * @return   object
	 *
	 */
	public function getEvent() {
		jimport('joomla.application.component.modeladmin');
		JModelLegacy::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_rseventspro/models');
		JModelLegacy::addTablePath(JPATH_ADMINISTRATOR.'/components/com_rseventspro/tables');
		
		$model = JModelLegacy::getInstance('Event','rseventsproModel');
		return $model->getItem($this->id);
	}
	
	/**
	 * After store process.
	 *
	 * @return   boolean
	 *
	 */
	public function save($table, $new) {
		// Save groups
		self::savegroups($table->id);
		// Save tags
		self::savetags($table->id);
		// Save categories
		self::savecategories($table->id);
		// Save files
		self::savefiles($table->id);
		// Save recurring days
		self::saverecurringdays($table->id,$new);
		// Save tickets
		self::savetickets($table->id);
		// Save coupons
		self::savecoupons($table->id);
		// Complete the event
		self::complete($table->id);
		// Repeat events
		self::repeatevents($table);
		// JomSocial activity
		self::jomsocial($table->id);
		// Smart search index
		self::index($table->id,$new);
	}
	
	/**
	 * Method to save event groups.
	 *
	 * @return   void
	 *
	 */
	protected function savegroups($id) {
		$jinput = JFactory::getApplication()->input;
		$groups = $jinput->get('groups',array(),'array');
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);
		
		$query->clear()
			->delete()
			->from($db->qn('#__rseventspro_taxonomy'))
			->where($db->qn('type').' = '.$db->q('groups'))
			->where($db->qn('ide').' = '.(int) $id);
		
		$db->setQuery($query);
		$db->execute();
		
		if (!empty($groups)) {
			foreach($groups as $group) {
				$query->clear()
					->insert($db->qn('#__rseventspro_taxonomy'))
					->set($db->qn('type').' = '.$db->q('groups'))
					->set($db->qn('ide').' = '.(int) $id)
					->set($db->qn('id').' = '.(int) $group);
				
				$db->setQuery($query);
				$db->execute();
			}
		}
	}
	
	/**
	 * Method to save event tags.
	 *
	 * @return   void
	 *
	 */
	protected function savetags($id, $moderate_tags = false) {
		$db		= JFactory::getDbo();
		$app	= JFactory::getApplication();
		$query	= $db->getQuery(true);
		$lang	= JFactory::getLanguage();
		$tags	= JFactory::getApplication()->input->get('tags', array(), 'array');
		
		$permissions	= rseventsproHelper::permissions();
		$admin			= rseventsproHelper::admin();
		
		if (!$app->isAdmin())
			$moderate_tags = !empty($permissions['tag_moderation']) && !$admin;
		
		$query->clear()
			->delete()
			->from($db->qn('#__rseventspro_taxonomy'))
			->where($db->qn('type').' = '.$db->q('tag'))
			->where($db->qn('ide').' = '.(int) $id);
		
		$db->setQuery($query);
		$db->execute();
		
		if (!empty($tags)) {
			$sendmail 	= false;
			$items 		= array();
			
			foreach ($tags as $tag) {
				$tag = trim($tag);
				
				$query->clear()
					->select($db->qn('id'))
					->from($db->qn('#__rseventspro_tags'))
					->where($db->qn('name').' = '.$db->q($tag));
				
				$db->setQuery($query);
				$tid = $db->loadResult();
				
				if (empty($tid)) {
					$published = $moderate_tags ? 0 : 1;
					
					$query->clear()
						->insert($db->qn('#__rseventspro_tags'))
						->set($db->qn('name').' = '.$db->q($tag))
						->set($db->qn('published').' = '.$db->q($published));
					
					$db->setQuery($query);
					$db->execute();
					$tid = $db->insertid();
					
					if ($moderate_tags) {
						$sendmail = true;
						$item = new stdClass();
						$item->name = $tag;
						$item->id = $tid;
						$items[] = $item;
					}
				}
				
				$query->clear()
					->select($db->qn('id'))
					->from($db->qn('#__rseventspro_taxonomy'))
					->where($db->qn('type').' = '.$db->q('tag'))
					->where($db->qn('ide').' = '.(int) $id)
					->where($db->qn('id').' = '.(int) $tid);
				$db->setQuery($query);
				if (!$db->loadResult()) {
					$query->clear()
						->insert($db->qn('#__rseventspro_taxonomy'))
						->set($db->qn('type').' = '.$db->q('tag'))
						->set($db->qn('ide').' = '.(int) $id)
						->set($db->qn('id').' = '.(int) $tid);
					
					$db->setQuery($query);
					$db->execute();
				}
			}
			
			if ($sendmail) {
				$emails = rseventsproHelper::getConfig('tags_moderation_emails');
				$emails = !empty($emails) ? explode(',',$emails) : '';
				if (!empty($emails)) {
					foreach ($emails as $email) {
						rseventsproEmails::tag_moderation(trim($email), $id, $items, $lang->getTag());
					}
				}
			}
		}
	}
	
	/**
	 * Method to save event categories.
	 *
	 * @return   void
	 *
	 */
	protected function savecategories($id) {
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);
		$categories = JFactory::getApplication()->input->get('categories',array(),'array');
		
		$query->clear()
			->delete()
			->from($db->qn('#__rseventspro_taxonomy'))
			->where($db->qn('type').' = '.$db->q('category'))
			->where($db->qn('ide').' = '.(int) $id);
		
		$db->setQuery($query);
		$db->execute();
		
		if (JFactory::getApplication()->isSite()) {
			rseventsproHelper::allowedCategories($categories);
		}
		
		if (!empty($categories)) {
			foreach($categories as $category) {
				$query->clear()
					->insert($db->qn('#__rseventspro_taxonomy'))
					->set($db->qn('type').' = '.$db->q('category'))
					->set($db->qn('ide').' = '.(int) $id)
					->set($db->qn('id').' = '.(int) $category);
				
				$db->setQuery($query);
				$db->execute();
			}
		}
	}
	
	/**
	 * Method to save event files.
	 *
	 * @return   void
	 *
	 */
	protected function savefiles($id) {
		jimport('joomla.filesystem.file');
		
		$app			= JFactory::getApplication();
		$permissions	= rseventsproHelper::permissions();
		$admin			= rseventsproHelper::admin();
		
		if (empty($permissions['can_upload']) && !$admin && !$app->isAdmin())
			return false;
		
		$extensions		= rseventsproHelper::getConfig('extensions');
		$extensions		= strtolower($extensions);
		$extensions		= explode(',',$extensions);
		
		$path	= JPATH_SITE.'/components/com_rseventspro/assets/images/files/';
		$db	  	= JFactory::getDbo();
		$query	= $db->getQuery(true);
		$files	= $app->input->files->get('files');
		
		if (!empty($files)) {
			foreach ($files as $file) {
				if (empty($file['name'])) 
					continue;
				
				$extension = JFile::getExt($file['name']);
				if (!in_array($extension,$extensions)) {
					$app->enqueueMessage(JText::sprintf('COM_RSEVENTSPRO_WRONG_EXTENSION',$file['name']));
					continue;
				}
				
				if ($file['error'] == 0) {
					$file['name'] = JFile::makeSafe($file['name']);
					$filename = JFile::getName(JFile::stripExt($file['name']));
					
					while(JFile::exists($path.$filename.'.'.$extension))
						$filename .= rand(1,999);
					
					if (JFile::upload($file['tmp_name'],$path.$filename.'.'.$extension)) {
						$query->clear()
							->insert($db->qn('#__rseventspro_files'))
							->set($db->qn('name').' = '.$db->q($filename))
							->set($db->qn('location').' = '.$db->q($filename.'.'.$extension))
							->set($db->qn('permissions').' = '.$db->q('111111'))
							->set($db->qn('ide').' = '.(int) $id);
						
						$db->setQuery($query);
						$db->execute();
					}
				}
			}
		}
	}
	
	/**
	 * Method to save event recurring days.
	 *
	 * @return   void
	 *
	 */
	protected function saverecurringdays($id, $new) {
		$db	  	= JFactory::getDbo();
		$query	= $db->getQuery(true);
		$days	= JFactory::getApplication()->input->get('repeat_days',array(),'array');
		
		if ($new) 
			$days = array(0,1,2,3,4,5,6);
		
		$query->clear()
			->delete()
			->from($db->qn('#__rseventspro_taxonomy'))
			->where($db->qn('type').' = '.$db->q('days'))
			->where($db->qn('ide').' = '.(int) $id);
		
		$db->setQuery($query);
		$db->execute();
		
		if (!empty($days)) {
			foreach($days as $day) {
				$query->clear()
					->insert($db->qn('#__rseventspro_taxonomy'))
					->set($db->qn('type').' = '.$db->q('days'))
					->set($db->qn('ide').' = '.(int) $id)
					->set($db->qn('id').' = '.(int) $day);
				
				$db->setQuery($query);
				$db->execute();
			}
		}
	}
	
	/**
	 * Method to save event tickets.
	 *
	 * @return   void
	 *
	 */
	protected function savetickets($id) {
		$db	  		= JFactory::getDbo();
		$tickets	= JFactory::getApplication()->input->get('tickets',array(),'array');
		
		if (!empty($tickets)) {
			foreach ($tickets as $tid => $ticket) {
				$ticket = (object) $ticket;
				$ticket->id = $tid;
				$ticket->ide = $id;
				if ($ticket->seats == JText::_('COM_RSEVENTSPRO_GLOBAL_UNLIMITED'))
					$ticket->seats = 0;
				if ($ticket->user_seats == JText::_('COM_RSEVENTSPRO_GLOBAL_UNLIMITED'))
					$ticket->user_seats = 0;
				if (isset($ticket->groups) && is_array($ticket->groups)) {
					$registry = new JRegistry;
					$registry->loadArray($ticket->groups);
					$ticket->groups = $registry->toString();
				} else $ticket->groups = '';
				
				$db->updateObject('#__rseventspro_tickets', $ticket, 'id');
			}
		}
	}
	
	/**
	 * Method to save event coupons.
	 *
	 * @return   void
	 *
	 */
	protected function savecoupons($id) {
		$db			= JFactory::getDbo();
		$query		= $db->getQuery(true);
		$config		= JFactory::getConfig();
		$tzoffset	= rseventsproHelper::getTimezone();
		$nulldate	= $db->getNullDate();
		$coupons	= JFactory::getApplication()->input->get('coupons',array(),'array');
		
		if (!empty($coupons)) {
			foreach ($coupons as $cid => $coupon) {
				$codes = $coupon['code'];
				unset($coupon['code']);
				$coupon = (object) $coupon;
				
				if (!empty($coupon->from) && $coupon->from != $nulldate)
					$coupon->from = JFactory::getDate($coupon->from, $tzoffset)->toSql();
				if (!empty($coupon->to) && $coupon->to != $nulldate)
					$coupon->to = JFactory::getDate($coupon->to, $tzoffset)->toSql();
				if ($coupon->usage == JText::_('COM_RSEVENTSPRO_GLOBAL_UNLIMITED'))
					$coupon->usage = 0;
				if (isset($coupon->groups) && is_array($coupon->groups)) {
					$registry = new JRegistry;
					$registry->loadArray($coupon->groups);
					$coupon->groups = $registry->toString();
				} else $coupon->groups = '';
				$coupon->id = $cid;
				$coupon->ide = $id;
				
				$db->updateObject('#__rseventspro_coupons', $coupon, 'id');
				
				if (!empty($codes)) {
					if ($codes = explode("\n", $codes)) {
						$query->clear()
							->select($db->qn('id'))
							->from($db->qn('#__rseventspro_coupon_codes'))
							->where($db->qn('idc').' = '.(int) $cid);
						
						// Get the ids of all codes
						$db->setQuery($query);
						$codeids = $db->loadColumn();
						if ($codeids) JArrayHelper::toInteger($codeids);
						$ids = array();
						
						foreach ($codes as $code) {
							$code = trim($code);
							$query->clear()
								->select($db->qn('id'))
								->from($db->qn('#__rseventspro_coupon_codes'))
								->where($db->qn('idc').' = '.$db->q($code))
								->where($db->qn('idc').' = '.(int) $cid);
							
							$db->setQuery($query);
							$codeid = (int) $db->loadResult();
							
							if (!$codeid) {
								$couponcoderow = new stdClass();
								$couponcoderow->id = null;
								$couponcoderow->idc = $cid;
								$couponcoderow->code = $code;
								$db->insertObject('#__rseventspro_coupon_codes', $couponcoderow, 'id');
							} else $ids[] = $codeid;
						}
						
						// Get codes for removal
						$remove = array_diff($codeids, $ids);
						
						if (!empty($remove)) {
							JArrayHelper::toInteger($remove);
							$query->clear()
								->delete()
								->from($db->qn('#__rseventspro_coupon_codes'))
								->where($db->qn('id').' IN ('.implode(',',$remove).')');
							
							$db->setQuery($query);
							$db->execute();
						}
					}
				}
			}
		}
	}
	
	/**
	 * Method to check if the event would be marked as complete.
	 *
	 * @return   void
	 *
	 */
	protected function complete($id) {
		$db			= JFactory::getDbo();
		$query		= $db->getQuery(true);
		
		$query->clear()
			->select('COUNT(id)')
			->from($db->qn('#__rseventspro_taxonomy'))
			->where($db->qn('type').' = '.$db->q('category'))
			->where($db->qn('ide').' = '.(int) $id);
		
		$db->setQuery($query);
		$counter = $db->loadResult();
		
		$query->clear()
			->select($db->qn('name'))->select($db->qn('owner'))->select($db->qn('location'))
			->select($db->qn('completed'))->select($db->qn('sid'))
			->from($db->qn('#__rseventspro_events'))
			->where($db->qn('id').' = '.(int) $id);
		
		$db->setQuery($query);
		$event = $db->loadObject();
		
		if (!empty($event->name) && !empty($event->location) && (!empty($event->owner) || !empty($event->sid)) && $counter > 0 && $event->completed == 0) {
			$query->clear()
				->update($db->qn('#__rseventspro_events'))
				->set($db->qn('completed').' = 1')
				->where($db->qn('id').' = '.(int) $id);
			
			$db->setQuery($query);
			$db->execute();
		}
	}
	
	/**
	 * Method to repeat events.
	 *
	 * @return   void
	 *
	 */
	protected function repeatevents($row) {
		$apply	= JFactory::getApplication()->input->getInt('apply_changes',0);
		$db		= JFactory::getDbo();
		$app	= JFactory::getApplication();
		$query	= $db->getQuery(true);
		$admin	= rseventsproHelper::admin();
		
		$permissions	= rseventsproHelper::permissions();
		if (empty($permissions['can_repeat_events']) && !$admin && !$app->isAdmin()) {
			return false;
		}
		
		if ($row->recurring == 0 || $apply == 0) {
			return false;
		}
		
		$query->clear()
			->select($db->qn('id'))
			->from($db->qn('#__rseventspro_taxonomy'))
			->where($db->qn('type').' = '.$db->q('days'))
			->where($db->qn('ide').' = '.$db->q($row->id));
		
		$db->setQuery($query);
		$days = $db->loadColumn();
		
		require_once JPATH_SITE . '/components/com_rseventspro/helpers/recurring.php';
		
		$registry = new JRegistry;
		$registry->set('interval', $row->repeat_interval);
		$registry->set('type', $row->repeat_type);
		$registry->set('start', $row->start);
		$registry->set('endd', $row->end);
		$registry->set('end', $row->repeat_end);
		$registry->set('days', $days);
		
		if (!empty($row->repeat_also)) {
			$reg = new JRegistry;
			$reg->loadString($row->repeat_also);
			$registry->set('also', $reg->toArray());
		}
		
		if (!empty($row->exclude_dates)) {
			$reg = new JRegistry;
			$reg->loadString($row->exclude_dates);
			$registry->set('exclude', $reg->toArray());
		}
		
		$registry->set('repeat_on_type', $row->repeat_on_type);
		$registry->set('repeat_on_day', $row->repeat_on_day);
		$registry->set('repeat_on_day_order', $row->repeat_on_day_order);
		$registry->set('repeat_on_day_type', $row->repeat_on_day_type);
		
		$recurring = RSEventsProRecurring::getInstance($registry);
		$recurringDates = $recurring->getDates();
		$dates = $recurringDates['start'];
		$ends  = $recurringDates['end'];
		
		if (!empty($dates)) {
			// Get the old children list
			$query->clear()
				->select($db->qn('id'))->select($db->qn('start'))
				->from($db->qn('#__rseventspro_events'))
				->where($db->qn('parent').' = '.(int) $row->id);
			
			$db->setQuery($query);
			$childs = $db->loadObjectList('start');
			
			// Get children dates
			$children = array_keys($childs);
			
			// Remove these children
			$diff = array_diff($children,$dates);
			
			if (!empty($children)) {
				foreach ($dates as $j => $date) {
					$object = new stdClass();
					if (in_array($date,$children)) {
						$object->date = $date;
						$object->end  = $ends[$date];
						$object->task = 'update';
						$object->id = @$childs[$date]->id;
						$dates[$j] = $object;
					} else {
						$object->date = $date;
						$object->end  = $ends[$date];
						$object->task = 'insert';
						$object->id = '';
						$dates[$j] = $object;
					}
				}
			} else {
				foreach ($dates as $k => $date) {
					$object = new stdClass();
					$object->date = $date;
					$object->end  = $ends[$date];
					$object->task = 'insert';
					$object->id = '';
					$dates[$k] = $object;
				}
			}
			
			if (!empty($diff)) {
				foreach ($diff as $dif) {
					$object = new stdClass();
					$object->date = $dif;
					$object->end  = $ends[$dif];
					$object->id = @$childs[$dif]->id;
					$object->task = 'remove';
					array_push($dates,$object);
				}
			}
			
			rseventsproHelper::copy($row->id,$dates);
		} else {
			// Delete all children
			$query->clear()
				->delete()
				->from($db->qn('#__rseventspro_events'))
				->where($db->qn('parent').' = '.$db->q($row->id));
			$db->setQuery($query);
			$db->execute();
		}
	}
	
	/**
	 * Method for JomSocial integration.
	 *
	 * @return   void
	 *
	 */
	protected function jomsocial($id) {
		if (JFactory::getApplication()->isAdmin())
			return;
		
		if (!file_exists(JPATH_BASE.'/components/com_community/libraries/core.php'))
			return;
			
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);
		
		$uri = JURI::getInstance();
		$root= $uri->toString(array('scheme','host'));
		
		$query->clear()
			->select($db->qn('name'))->select($db->qn('owner'))->select($db->qn('description'))
			->select($db->qn('completed'))->select($db->qn('published'))
			->from($db->qn('#__rseventspro_events'))
			->where($db->qn('id').' = '.(int) $id);
		
		$db->setQuery($query);
		$row = $db->loadObject();
		
		if ($row->completed && $row->published == 1) {
			require_once JPATH_SITE.'/components/com_rseventspro/helpers/route.php';
			require_once JPATH_BASE.'/components/com_community/libraries/core.php';

			$lang = JFactory::getLanguage();
			$lang->load('com_rseventspro');

			$query->clear()
				->select($db->qn('id'))
				->from($db->qn('#__community_activities'))
				->where($db->qn('actor').' = '.$db->q($row->owner))
				->where($db->qn('app').' = '.$db->q('rseventspro'))
				->where($db->qn('cid').' = '.$db->q($id));
			
			$db->setQuery($query);
			$activity = $db->loadResult();
			
			if (empty($activity) && rseventsproHelper::getConfig('jsactivity','int')) {
				$eitemid  = RseventsproHelperRoute::getEventsItemid();
				$jsitemid = rseventsproHelper::itemid($id);
				$jsitemid = empty($jsitemid) ? $eitemid : $jsitemid;
				
				$link = '<a href="'.$root.rseventsproHelper::route('index.php?option=com_rseventspro&layout=show&id='.rseventsproHelper::sef($id,$row->name),true,$jsitemid).'">'.$row->name.'</a>';
				
				$act = new stdClass();
				$act->cmd     = 'rseventspro.create';
				$act->actor   = $row->owner;
				$act->target  = $row->owner;
				$act->title   = JText::sprintf('COM_RSEVENTSPRO_JOMSOCIAL_ACTIVITY_POST',$link);
				$act->content = substr(strip_tags($row->description),0,255);
				$act->app     = 'rseventspro';
				$act->cid     = $id;
				
				CFactory::load('libraries', 'activities');
				$act->comment_type  = 'rseventspro.addcomment';
				$act->comment_id    = CActivities::COMMENT_SELF;

				$act->like_type     = 'rseventspro.like';
				$act->like_id     = CActivities::LIKE_SELF;
				
				CActivities::add($act);
			}
		}
	}
	
	/**
	 * Method to index events for the smart search plugin
	 *
	 * @return   void
	 *
	 */
	protected function index($id, $isNew) {
		$dispatcher	= JDispatcher::getInstance();
		JPluginHelper::importPlugin('finder');
		
		$table = JTable::getInstance('Event','rseventsproTable');
		$table->load($id);
		
		if ($table->completed) {
			// Trigger the onFinderAfterSave event.
			$dispatcher->trigger('onFinderAfterSave', array('com_rseventspro.event', $table, $isNew));
		}
	}
}