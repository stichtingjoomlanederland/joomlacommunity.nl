<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' );

class RSEventsProStatistics
{
	/**
	 * Array to hold the object instances
	 *
	 * @var    array
	 */
	public static $instance;
	
	/**
	 * Set statistics type
	 *
	 * @var    array
	 */
	protected static $types = array();
	
	/**
	 * Class constructor
	 *
	 */
	public function __construct() {
		$this->setTypes();
	}
	
	/**
	 * Method to get details
	 *
	 */
	public function get($type) {
		if (isset($this->types[$type])) {
			list($start, $end) = $this->types[$type];
			return $this->data($start, $end);
		}
		
		return false;
	}
	
	/**
	 * Set statistic types
	 *
	 */
	protected function setTypes() {
		$unixGMT	= JFactory::getDate();
		
		// Today statistics
		$clone = clone $unixGMT;
		$clone->setTime(0,0,0);
		$start = $clone->format('Y-m-d H:i:s');
		$clone->setTime(23,59,59);
		$end = $clone->format('Y-m-d H:i:s');
		
		$this->types['today'] = array($start, $end);
		
		// This week statistics
		$clone = clone $unixGMT;
		$operator = $clone->format('N') == 7 ? 'last' : 'this';
		$clone->modify('Monday '.$operator.' week');
		$clone->setTime(0,0,0);
		$start = $clone->format('Y-m-d H:i:s');
		$end = $unixGMT->format('Y-m-d H:i:s');
		
		$this->types['thisweek'] = array($start, $end);
		
		// Last week statistics
		$clone = clone $unixGMT;
		if ($clone->format('N') == 7) $clone->modify('-1 days');
		$clone->modify('Monday last week');
		$clone->setTime(0,0,0);
		$start = $clone->format('Y-m-d H:i:s');
		$clone->modify('this Sunday');
		$clone->setTime(23,59,59);
		$end = $clone->format('Y-m-d H:i:s');
		
		$this->types['lastweek'] = array($start, $end);
		
		// This month statistics
		$clone = clone $unixGMT;
		$clone->modify('first day of this month');
		$clone->setTime(0,0,0);
		$start = $clone->format('Y-m-d H:i:s');
		$end = $unixGMT->format('Y-m-d H:i:s');
		
		$this->types['thismonth'] = array($start, $end);
		
		// Last month statistics
		$clone = clone $unixGMT;
		$clone->modify('first day of last month');
		$clone->setTime(0,0,0);
		$start = $clone->format('Y-m-d H:i:s');
		$clone->modify('last day of this month');
		$clone->setTime(23,59,59);
		$end = $clone->format('Y-m-d H:i:s');
		
		$this->types['lastmonth'] = array($start, $end);
		
		// This year statistics
		$clone = clone $unixGMT;
		$clone->modify('first day of January');
		$clone->setTime(0,0,0);
		$start = $clone->format('Y-m-d H:i:s');
		$end = $unixGMT->format('Y-m-d H:i:s');
		
		$this->types['thisyear'] = array($start, $end);
		
		// Last year statistics
		$clone = clone $unixGMT;
		$start = ($clone->format('Y') - 1).'-01-01 00:00:00';
		$end = ($clone->format('Y') - 1).'-12-31 23:59:59';
		
		$this->types['lastyear'] = array($start, $end);
		
		$this->types['total'] = array(false, false);
	}
	
	/**
	 * Get data
	 *
	 */
	protected function data($start, $end) {
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);
		$total	= 0;
		$data	= (object) array('count' => 0, 'total' => 0);
		$cart	= false;
		
		JFactory::getApplication()->triggerEvent('rsepro_isCart', array(array('cart' => &$cart)));
		
		$query->select($db->qn('u.id'))
			->from($db->qn('#__rseventspro_users','u'))
			->where($db->qn('u.state').' = '.$db->q(1));
			
		if ($start) {
			$query->where($db->qn('u.date').' >= '.$db->q($start));
		}
		
		if ($end) {
			$query->where($db->qn('u.date').' <= '.$db->q($end));
		}
		
		if ($cart) {
			$query->select('c.total');
			$query->join('LEFT',$db->qn('#__rseventspro_cart','c').' ON '.$db->qn('u.id').' = '.$db->qn('c.ids'));
		}
		
		$db->setQuery($query);
		if ($subscriptions = $db->loadObjectList()) {
			foreach ($subscriptions as $subscription) {
				if (isset($subscription->total)) {
					$total += $subscription->total;
				} else {
					$total += rseventsproHelper::total($subscription->id);
				}
			}
			
			$data->count = count($subscriptions);
			$data->total = $total;
		}
		
		return $data;
	}
}