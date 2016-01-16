<?php
/**
* @version 1.0.0
* @package RSEvents!Pro 1.0.0
* @copyright (C) 2011 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' );
jimport( 'joomla.application.component.model' );

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
		
		$query->clear();
		$query->select('COUNT(u.'.$db->qn('id').') as subscribers, e.'.$db->qn('id').', e.'.$db->qn('name').', e.'.$db->qn('start').', e.'.$db->qn('end'))
				->from($db->qn('#__rseventspro_events').' e')
				->join('left',$db->qn('#__rseventspro_users').' u ON e.'.$db->qn('id').' = u.'.$db->qn('ide'))
				->where('e.'.$db->qn('published').' = 1')
				->where('e.'.$db->qn('start').' > '.$db->q(rseventsproHelper::date('now','Y-m-d H:i:s')))
				->group('e.'.$db->qn('id'))
				->order('e.'.$db->qn('start').' ASC');
		
		$db->setQuery($query, 0, rseventsproHelper::getConfig('dashboard_upcoming_nr','int',5));
		return $db->loadObjectList();
	}
	
	/**
	 * Method to get subscribers.
	 */
	public function getSubscribers() {
		$db		= JFactory::getDBO();
		$query	= $db->getQuery(true);
		
		$query->clear();
		$query->select('e.'.$db->qn('id','eid').', e.'.$db->qn('name','ename').', u.'.$db->qn('id').', u.'.$db->qn('name').', u.'.$db->qn('date'))
				->from($db->qn('#__rseventspro_users','u'))
				->join('left',$db->qn('#__rseventspro_events','e').' ON e.'.$db->qn('id').' = u.'.$db->qn('ide'))
				->order('u.'.$db->qn('date').' DESC');
		
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
}