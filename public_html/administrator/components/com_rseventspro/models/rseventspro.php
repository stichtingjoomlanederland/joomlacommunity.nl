<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' );

class RseventsproModelRseventspro extends JModelLegacy
{	
	/**
	 * Constructor.
	 *
	 * @since	1.6
	 */
	public function __construct() {
		parent::__construct();
	}
	
	/**
	 * Method to get events.
	 */
	public function getEvents() {		
		$db		= JFactory::getDBO();
		$query	= $db->getQuery(true);
		$events = array();
		
		$query->clear()
			->select($db->qn('id'))->select($db->qn('name'))->select($db->qn('start'))
			->select($db->qn('end'))->select($db->qn('allday'))
			->from($db->qn('#__rseventspro_events'))
			->where($db->qn('published').' = 1')
			->where($db->qn('completed').' = 1')
			->where($db->qn('start').' > '.$db->q(JFactory::getDate()->toSql()))
			->group($db->qn('id'))->group($db->qn('name'))->group($db->qn('start'))->group($db->qn('end'))->group($db->qn('allday'))
			->order($db->qn('start').' ASC');
		
		$db->setQuery($query, 0, rseventsproHelper::getConfig('dashboard_upcoming_nr','int',5));
		if ($events = $db->loadObjectList()) {
			foreach ($events as $event) {
				$query->clear()
					->select('COUNT('.$db->qn('u.id').')')
					->from($db->qn('#__rseventspro_users','u'))
					->where($db->qn('u.ide').' = '.(int) $event->id);
				
				JFactory::getApplication()->triggerEvent('rsepro_subscriptionsQuery', array(array('query' => &$query, 'rule' => 'u.ide')));
				
				$db->setQuery($query);
				$event->subscribers = (int) $db->loadResult();
			}
		}
		
		return $events;
	}
	
	/**
	 * Method to get subscribers.
	 */
	public function getSubscribers() {
		$db		= JFactory::getDBO();
		$query	= $db->getQuery(true);
		
		$query->clear()
			->select($db->qn('e.id','eid'))->select($db->qn('e.name','ename'))
			->select($db->qn('u.id'))->select($db->qn('u.name'))->select($db->qn('u.date'))
			->from($db->qn('#__rseventspro_users','u'))
			->join('left',$db->qn('#__rseventspro_events','e').' ON '.$db->qn('e.id').' = '.$db->qn('u.ide'))
			->order($db->qn('u.date').' DESC');
		$db->setQuery($query, 0, rseventsproHelper::getConfig('dashboard_subscribers_nr','int',5));
		$subscribers = $db->loadObjectList();
		
		if ($subscribers) {
			JFactory::getApplication()->triggerEvent('rsepro_adminSubscribersDashboard', array(array('subscribers' => &$subscribers)));
			
			foreach ($subscribers as $subscriber) {
				if (!isset($subscriber->events)) {
					$subscriber->events = array();
				}
				
				if ($subscriber->eid) {
					$subscriber->events[] = (object) array('id' => $subscriber->eid, 'name' => $subscriber->ename);
				}
			}
		}
		
		return $subscribers;
	}
	
	/**
	 * Method to get comments.
	 */
	public function getComments() {
		$db		= JFactory::getDBO();
		$query	= $db->getQuery(true);
		$limit = rseventsproHelper::getConfig('dashboard_comments_nr','int',5);
		
		switch(rseventsproHelper::getConfig('event_comment','int')) {
			//no comments or Facebook
			default:
			case 0:
			case 1:
				return array();
			break;
			
			//RSComments!
			case 2:
				$query->clear();
				$query->select($db->qn('e.id').', '.$db->qn('e.name').', '.$db->qn('c.IdComment','cid').', '.$db->qn('c.name','cname').', '.$db->qn('c.comment').', '.$db->qn('c.date').', '.$db->qn('c.published'))
						->from($db->qn('#__rscomments_comments','c'))
						->join('left',$db->qn('#__rseventspro_events','e').' ON '.$db->qn('e.id').' = '.$db->qn('c.id'))
						->where($db->qn('c.option').' = '.$db->q('com_rseventspro'))
						->order($db->qn('c.date').' DESC');
						
				$db->setQuery($query, 0, $limit);
				$comments = $db->loadObjectList();
			break;
			
			//JComments
			case 3:
				$query->clear();
				$query->select($db->qn('e.id').', '.$db->qn('e.name').', '.$db->qn('c.id','cid').', '.$db->qn('c.name','cname').', '.$db->qn('c.comment').', UNIX_TIMESTAMP('.$db->qn('c.date').') as date, '.$db->qn('c.published'))
						->from($db->qn('#__jcomments','c'))
						->join('left',$db->qn('#__rseventspro_events','e').' ON '.$db->qn('e.id').' = '.$db->qn('c.object_id'))
						->where($db->qn('c.object_group').' = '.$db->q('com_rseventspro'))
						->order($db->qn('c.date').' DESC');
				
				$db->setQuery($query, 0, $limit);
				$comments = $db->loadObjectList();
			break;
			
			//Jom Comments
			case 4:
				$query->clear();
				$query->select($db->qn('e.id').', '.$db->qn('e.name').', '.$db->qn('c.id','cid').', '.$db->qn('c.name','cname').', '.$db->qn('c.comment').', UNIX_TIMESTAMP('.$db->qn('c.date').') as date, '.$db->qn('c.published'))
						->from($db->qn('#__jomcomment','c'))
						->join('left',$db->qn('#__rseventspro_events','e').' ON '.$db->qn('e.id').' = '.$db->qn('c.contentid'))
						->where($db->qn('c.option').' = '.$db->q('com_rseventspro'))
						->order($db->qn('c.date').' DESC');
				
				$db->setQuery($query, 0, $limit);
				$comments = $db->loadObjectList();
			break;
		}
		return $comments;
	}
	
	public function getButtons() {
		$app	 = JFactory::getApplication();
		$buttons = array();
		
		$buttons[] = array('icon' => 'fa fa-calendar', 'name' => JText::_('COM_RSEVENTSPRO_DASHBOARD_EVENTS'), 'link' => JRoute::_('index.php?option=com_rseventspro&view=events'));
		$buttons[] = array('icon' => 'fa fa-map-marker', 'name' => JText::_('COM_RSEVENTSPRO_DASHBOARD_LOCATIONS'), 'link' => JRoute::_('index.php?option=com_rseventspro&view=locations'));
		$buttons[] = array('icon' => 'fa fa-book', 'name' => JText::_('COM_RSEVENTSPRO_DASHBOARD_CATEGORIES'), 'link' => JRoute::_('index.php?option=com_rseventspro&view=categories'));
		$buttons[] = array('icon' => 'fa fa-tag', 'name' => JText::_('COM_RSEVENTSPRO_DASHBOARD_TAGS'), 'link' => JRoute::_('index.php?option=com_rseventspro&view=tags'));
		$buttons[] = array('icon' => 'fa fa-user-circle-o', 'name' => JText::_('COM_RSEVENTSPRO_DASHBOARD_SPEAKERS'), 'link' => JRoute::_('index.php?option=com_rseventspro&view=speakers'));
		$buttons[] = array('icon' => 'fa fa-user', 'name' => JText::_('COM_RSEVENTSPRO_DASHBOARD_SUBSCRIPTIONS'), 'link' => JRoute::_('index.php?option=com_rseventspro&view=subscriptions'));
		$buttons[] = array('icon' => 'fa fa-scissors', 'name' => JText::_('COM_RSEVENTSPRO_DASHBOARD_DISCOUNTS'), 'link' => JRoute::_('index.php?option=com_rseventspro&view=discounts'));
		$buttons[] = array('icon' => 'fa fa-credit-card', 'name' => JText::_('COM_RSEVENTSPRO_DASHBOARD_PAYMENTS'), 'link' => JRoute::_('index.php?option=com_rseventspro&view=payments'));
		$buttons[] = array('icon' => 'fa fa-users', 'name' => JText::_('COM_RSEVENTSPRO_DASHBOARD_GROUPS'), 'link' => JRoute::_('index.php?option=com_rseventspro&view=groups'));
		$buttons[] = array('icon' => 'fa fa-user-circle', 'name' => JText::_('COM_RSEVENTSPRO_DASHBOARD_USERS'), 'link' => JRoute::_('index.php?option=com_rseventspro&view=users'));
		$buttons[] = array('icon' => 'fa fa-upload', 'name' => JText::_('COM_RSEVENTSPRO_DASHBOARD_IMPORTS'), 'link' => JRoute::_('index.php?option=com_rseventspro&view=imports'));
		$buttons[] = array('icon' => 'fa fa-archive', 'name' => JText::_('COM_RSEVENTSPRO_DASHBOARD_BACKUP'), 'link' => JRoute::_('index.php?option=com_rseventspro&view=backup'));
		$buttons[] = array('icon' => 'fa fa-envelope-o', 'name' => JText::_('COM_RSEVENTSPRO_DASHBOARD_EMAILS'), 'link' => JRoute::_('index.php?option=com_rseventspro&view=messages'));
		$buttons[] = array('icon' => 'fa fa-bars', 'name' => JText::_('COM_RSEVENTSPRO_DASHBOARD_SETTINGS'), 'link' => JRoute::_('index.php?option=com_rseventspro&view=settings'));
		
		if (rseventsproHelper::getConfig('dashboard_sync')) {
			$buttons[] = array('icon' => 'fa fa-facebook-official', 'name' => JText::_('COM_RSEVENTSPRO_DASHBOARD_SYNC_FACEBOOK'), 'link' => JRoute::_('index.php?option=com_rseventspro&task=settings.facebook'));
		}
		
		$app->triggerEvent('rsepro_adminDashboard',array(array('buttons' => &$buttons)));
		
		return $buttons;
	}
	
	public function getStatistics() {
		require_once JPATH_SITE.'/components/com_rseventspro/helpers/statistics.php';
		
		$statistics = new RSEventsProStatistics();
		
		$types = array(
			JText::_('COM_RSEVENTSPRO_DASHBOARD_STATISTICS_TODAY') 		=> $statistics->get('today'),
			JText::_('COM_RSEVENTSPRO_DASHBOARD_STATISTICS_THISWEEK') 	=> $statistics->get('thisweek'),
			JText::_('COM_RSEVENTSPRO_DASHBOARD_STATISTICS_LASTWEEK') 	=> $statistics->get('lastweek'),
			JText::_('COM_RSEVENTSPRO_DASHBOARD_STATISTICS_THISMONTH') 	=> $statistics->get('thismonth'),
			JText::_('COM_RSEVENTSPRO_DASHBOARD_STATISTICS_LASTMONTH') 	=> $statistics->get('lastmonth'),
			JText::_('COM_RSEVENTSPRO_DASHBOARD_STATISTICS_THISYEAR') 	=> $statistics->get('thisyear'),
			JText::_('COM_RSEVENTSPRO_DASHBOARD_STATISTICS_LASTYEAR') 	=> $statistics->get('lastyear'),
			JText::_('COM_RSEVENTSPRO_DASHBOARD_STATISTICS_TOTAL') 		=> $statistics->get('total')
		);
		
		return $types;
	}
	
	public function getTotals() {
		$db		= JFactory::getDBO();
		$query	= $db->getQuery(true);
		$object	= new stdClass();
		
		// Get the total number of events
		$query->clear()->select('COUNT('.$db->qn('id').')')->from($db->qn('#__rseventspro_events'))->where($db->qn('completed').' = '.$db->q(1))->where($db->qn('published').' = '.$db->q(1));
		$db->setQuery($query);
		$object->events = (int) $db->loadResult();
		
		$query->clear()->select('COUNT('.$db->qn('id').')')->from($db->qn('#__categories'))->where($db->qn('extension').' = '.$db->q('com_rseventspro'))->where($db->qn('published').' = '.$db->q(1));
		$db->setQuery($query);
		$object->categories = (int) $db->loadResult();
		
		$query->clear()->select('COUNT('.$db->qn('id').')')->from($db->qn('#__rseventspro_locations'))->where($db->qn('published').' = '.$db->q(1));
		$db->setQuery($query);
		$object->locations = (int) $db->loadResult();
		
		$query->clear()->select('COUNT('.$db->qn('id').')')->from($db->qn('#__rseventspro_users'));
		$db->setQuery($query);
		$object->subscriptions = (int) $db->loadResult();
		
		return $object;
	}
}