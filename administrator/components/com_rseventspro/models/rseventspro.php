<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' );

class rseventsproModelRseventspro extends JModelLegacy
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
		
		$query->clear()
			->select('COUNT('.$db->qn('u.id').') as subscribers')->select($db->qn('e.id'))->select($db->qn('e.name'))
			->select($db->qn('e.start'))->select($db->qn('e.end'))->select($db->qn('e.allday'))
			->from($db->qn('#__rseventspro_events','e'))
			->join('LEFT',$db->qn('#__rseventspro_users','u').' ON '.$db->qn('e.id').' = '.$db->qn('u.ide'))
			->where($db->qn('e.published').' = 1')
			->where($db->qn('e.completed').' = 1')
			->where($db->qn('e.start').' > '.$db->q(JFactory::getDate()->toSql()))
			->group($db->qn('e.id'))
			->order($db->qn('e.start').' ASC');
		
		$db->setQuery($query, 0, rseventsproHelper::getConfig('dashboard_upcoming_nr','int',5));
		return $db->loadObjectList();
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
		return $db->loadObjectList();
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
		
		$buttons[] = array('icon' => 'fa fa-calendar fa-4x', 'name' => JText::_('COM_RSEVENTSPRO_DASHBOARD_EVENTS'), 'link' => JRoute::_('index.php?option=com_rseventspro&view=events'));
		$buttons[] = array('icon' => 'fa fa-map-marker fa-4x', 'name' => JText::_('COM_RSEVENTSPRO_DASHBOARD_LOCATIONS'), 'link' => JRoute::_('index.php?option=com_rseventspro&view=locations'));
		$buttons[] = array('icon' => 'fa fa-book fa-4x', 'name' => JText::_('COM_RSEVENTSPRO_DASHBOARD_CATEGORIES'), 'link' => JRoute::_('index.php?option=com_rseventspro&view=categories'));
		$buttons[] = array('icon' => 'fa fa-user fa-4x', 'name' => JText::_('COM_RSEVENTSPRO_DASHBOARD_SUBSCRIPTIONS'), 'link' => JRoute::_('index.php?option=com_rseventspro&view=subscriptions'));
		$buttons[] = array('icon' => 'fa fa-scissors fa-4x', 'name' => JText::_('COM_RSEVENTSPRO_DASHBOARD_DISCOUNTS'), 'link' => JRoute::_('index.php?option=com_rseventspro&view=discounts'));
		$buttons[] = array('icon' => 'fa fa-credit-card fa-4x', 'name' => JText::_('COM_RSEVENTSPRO_DASHBOARD_PAYMENTS'), 'link' => JRoute::_('index.php?option=com_rseventspro&view=payments'));
		$buttons[] = array('icon' => 'fa fa-users fa-4x', 'name' => JText::_('COM_RSEVENTSPRO_DASHBOARD_GROUPS'), 'link' => JRoute::_('index.php?option=com_rseventspro&view=groups'));
		$buttons[] = array('icon' => 'fa fa-upload fa-4x', 'name' => JText::_('COM_RSEVENTSPRO_DASHBOARD_IMPORTS'), 'link' => JRoute::_('index.php?option=com_rseventspro&view=imports'));
		$buttons[] = array('icon' => 'fa fa-archive fa-4x', 'name' => JText::_('COM_RSEVENTSPRO_DASHBOARD_BACKUP'), 'link' => JRoute::_('index.php?option=com_rseventspro&view=backup'));
		$buttons[] = array('icon' => 'fa fa-envelope-o fa-4x', 'name' => JText::_('COM_RSEVENTSPRO_DASHBOARD_EMAILS'), 'link' => JRoute::_('index.php?option=com_rseventspro&view=messages'));
		$buttons[] = array('icon' => 'fa fa-bars fa-4x', 'name' => JText::_('COM_RSEVENTSPRO_DASHBOARD_SETTINGS'), 'link' => JRoute::_('index.php?option=com_rseventspro&view=settings'));
		$buttons[] = array('icon' => 'fa fa-bell-o fa-4x', 'name' => JText::_('COM_RSEVENTSPRO_SUBMENU_UPDATE'), 'link' => JRoute::_('index.php?option=com_rseventspro&layout=update'));
		
		if (rseventsproHelper::getConfig('dashboard_sync')) {
			$buttons[] = array('icon' => 'fa fa-facebook-official fa-4x', 'name' => JText::_('COM_RSEVENTSPRO_DASHBOARD_SYNC_FACEBOOK'), 'link' => JRoute::_('index.php?option=com_rseventspro&task=settings.facebook'));
		}
		
		$app->triggerEvent('rsepro_adminDashboard',array(array('buttons' => &$buttons)));
		
		return $buttons;
	}
}