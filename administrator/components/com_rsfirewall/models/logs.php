<?php
/**
 * @package    RSFirewall!
 * @copyright  (c) 2009 - 2016 RSJoomla!
 * @link       https://www.rsjoomla.com
 * @license    GNU General Public License http://www.gnu.org/licenses/gpl-3.0.en.html
 */

defined('_JEXEC') or die('Restricted access');

class RSFirewallModelLogs extends JModelList
{
	public function __construct($config = array()) {
		if (empty($config['filter_fields'])) {
			$config['filter_fields'] = array(
				'logs.level', 'logs.date', 'logs.ip', 'logs.user_id', 'logs.username', 'logs.page', 'logs.referer'
			);
		}

		parent::__construct($config);
	}
	
	protected function getListQuery() {
		$db 	= JFactory::getDBO();
		$query 	= $db->getQuery(true);
		
		// get filtering states
		$search 		= $this->getState('filter.search');
		$level 			= $this->getState('filter.level');
		$blocked_status = $this->getState('filter.blocked_status');
		
		$query	->select($db->qn('logs').'.*')
				->select($db->qn('#__rsfirewall_lists').'.'.$db->qn('type'))
				->select($db->qn('#__rsfirewall_lists').'.'.$db->qn('id', 'listId'))
				->from($db->qn('#__rsfirewall_logs', 'logs'))
				->join('LEFT', $db->qn('#__rsfirewall_lists').' ON ('.$db->qn('logs').'.'.$db->qn('ip').' = '.$db->qn('#__rsfirewall_lists').'.'.$db->qn('ip').')');
		// search
		if ($search != '') {
			$search = $db->q('%'.str_replace(' ', '%', $db->escape($search, true)).'%', false);
			$like 	= array();
			$like[] = $db->qn('logs.ip').' LIKE '.$search;
			$like[] = $db->qn('logs.user_id').' LIKE '.$search;
			$like[] = $db->qn('logs.username').' LIKE '.$search;
			$like[] = $db->qn('logs.page').' LIKE '.$search;
			$like[] = $db->qn('logs.referer').' LIKE '.$search;
			$query->where('('.implode(' OR ', $like).')');
		}
		// level
		if ($level != '') {
			$query->where($db->qn('logs.level').'='.$db->q($level));
		}

		if ($blocked_status) {
			switch ($blocked_status)
			{
				// Blocked
				case 1:
					$query->where($db->qn('#__rsfirewall_lists.id').' IS NOT NULL')
						->where($db->qn('type').' = '.$db->q(0));
					break;

				// Not blocked
				case -1:
					$query->where($db->qn('#__rsfirewall_lists.id').' IS NULL');
					break;
			}
		}
		
		// order by
		$query->order($db->escape($this->getState('list.ordering', 'logs.date')).' '.$db->escape($this->getState('list.direction', 'desc')));
		
		return $query;
	}
	
	protected function populateState($ordering = null, $direction = null) {
		$this->setState('filter.search', $this->getUserStateFromRequest($this->context.'.filter.search', 'filter_search'));
		$this->setState('filter.level',  $this->getUserStateFromRequest($this->context.'.filter.level',  'filter_level'));
		$this->setState('filter.blocked_status', $this->getUserStateFromRequest($this->context.'.filter.blocked_status',  'filter_blocked_status', null, 'int'));

		// List state information.
		parent::populateState('logs.date', 'desc');
	}
	
	public function getLevels()
	{
		return array(
			JHtml::_('select.option', 'low', JText::_('COM_RSFIREWALL_LEVEL_LOW')),
			JHtml::_('select.option', 'medium', JText::_('COM_RSFIREWALL_LEVEL_MEDIUM')),
			JHtml::_('select.option', 'high', JText::_('COM_RSFIREWALL_LEVEL_HIGH')),
			JHtml::_('select.option', 'critical', JText::_('COM_RSFIREWALL_LEVEL_CRITICAL'))
		);
	}

	public function getBlockedStatuses()
	{
		return array(
			JHtml::_('select.option', '-1', JText::_('COM_RSFIREWALL_NOT_BLOCKED')),
			JHtml::_('select.option', '1', JText::_('COM_RSFIREWALL_BLOCKED'))
		);
	}
	
	public function toCSV() {
		// Get Dbo
		$db = JFactory::getDbo();
		
		// Populate state so filters and ordering is available.
		$this->populateState();
		
		// Get results
		$results = $db->setQuery($this->getListQuery())->loadAssocList();
		
		// Error on no results
		if (!$results) {
			throw new Exception(JText::_('COM_RSFIREWALL_NOT_ENOUGH_RESULTS_TO_OUTPUT'));
		}
		
		// Load GeoIP helper class
		require_once JPATH_ADMINISTRATOR.'/components/com_rsfirewall/helpers/geoip/geoip.php';
		$geoip = RSFirewallGeoIP::getInstance();
		
		$out = @fopen('php://output', 'w');
		
		if (!is_resource($out)) {
			throw new Exception(JText::_('COM_RSFIREWALL_COULD_NOT_OPEN_PHP_OUTPUT'));
		}
		
		// Get CSV headers
		$columns = array(
			JText::_('COM_RSFIREWALL_ALERT_LEVEL'),
			JText::_('COM_RSFIREWALL_LOG_DATE_EVENT'),
			JText::_('COM_RSFIREWALL_LOG_IP_ADDRESS'),
			JText::_('COM_RSFIREWALL_LOG_USER_ID'),
			JText::_('COM_RSFIREWALL_LOG_USERNAME'),
			JText::_('COM_RSFIREWALL_LOG_PAGE'),
			JText::_('COM_RSFIREWALL_LOG_REFERER'),
			JText::_('COM_RSFIREWALL_LOG_DESCRIPTION'),
			JText::_('COM_RSFIREWALL_LOG_DEBUG_VARIABLES')
		);
		
		// Write CSV headers
		if (fputcsv($out, $columns, ',', '"') === false) {
			throw new Exception(JText::_('COM_RSFIREWALL_COULD_NOT_WRITE_PHP_OUTPUT'));
		}
		
		foreach ($results as $result) {
			// Prettify results
			$result['level'] = JText::_('COM_RSFIREWALL_LEVEL_'.$result['level']);
			$result['date']  = JHTML::_('date', $result['date'], 'Y-m-d H:i:s');
			$result['code']  = JText::_('COM_RSFIREWALL_EVENT_'.$result['code']);
			
			// Add country code if available
			if ($country = $geoip->getCountryCode($result['ip'])) {
				$result['ip'] = sprintf('(%s) %s', $country, $result['ip']);
			}
			
			// Remove unneeded headers
			unset($result['type']);
			unset($result['listId']);
			unset($result['id']);
			
			// Write CSV row
			if (fputcsv($out, $result, ',', '"') === false) {
				throw new Exception(JText::_('COM_RSFIREWALL_COULD_NOT_WRITE_PHP_OUTPUT'));
			}
		}
		
		fclose($out);
	}
	
	public function getIsJ30() {
		$jversion = new JVersion();
		return $jversion->isCompatible('3.0');
	}
	
	public function getFilterBar() {
		require_once JPATH_COMPONENT.'/helpers/adapters/filterbar.php';
		
		$options = array();
		$options['search'] = array(
			'label' => JText::_('JSEARCH_FILTER'),
			'value' => $this->getState('filter.search')
		);
		$options['limitBox']  = $this->getPagination()->getLimitBox();
		$options['listDirn']  = $this->getState('list.direction', 'desc');
		$options['listOrder'] = $this->getState('list.ordering', 'logs.date');
		$options['sortFields'] = array(
			JHtml::_('select.option', 'logs.level', JText::_('COM_RSFIREWALL_ALERT_LEVEL')),
			JHtml::_('select.option', 'logs.date', JText::_('COM_RSFIREWALL_LOG_DATE_EVENT')),
			JHtml::_('select.option', 'logs.ip', JText::_('COM_RSFIREWALL_LOG_IP_ADDRESS')),
			JHtml::_('select.option', 'logs.user_id', JText::_('COM_RSFIREWALL_LOG_USER_ID')),
			JHtml::_('select.option', 'logs.username', JText::_('COM_RSFIREWALL_LOG_USERNAME')),
			JHtml::_('select.option', 'logs.page', JText::_('COM_RSFIREWALL_LOG_PAGE')),
			JHtml::_('select.option', 'logs.referer', JText::_('COM_RSFIREWALL_LOG_REFERER'))
		);
		$options['rightItems'] = array(
			array(
				'input' => '<select name="filter_level" class="inputbox" onchange="this.form.submit()">'."\n"
						   .'<option value="">'.JText::_('COM_RSFIREWALL_SELECT_LEVEL').'</option>'."\n"
						   .JHtml::_('select.options', $this->getLevels(), 'value', 'text', $this->getState('filter.level'))."\n"
						   .'</select>'
			),
			array(
				'input' => '<select name="filter_blocked_status" class="inputbox" onchange="this.form.submit()">'."\n"
					.'<option value="">'.JText::_('COM_RSFIREWALL_SELECT_BLOCKED_STATUS').'</option>'."\n"
					.JHtml::_('select.options', $this->getBlockedStatuses(), 'value', 'text', $this->getState('filter.blocked_status'))."\n"
					.'</select>'
			)
		);
		
		$bar = new RSFilterBar($options);
		
		return $bar;
	}
	
	public function getSideBar() {
		require_once JPATH_COMPONENT.'/helpers/toolbar.php';
		
		RSFirewallToolbarHelper::addFilter(
			JText::_('COM_RSFIREWALL_SELECT_LEVEL'),
			'filter_level',
			JHtml::_('select.options', $this->getLevels(), 'value', 'text', $this->getState('filter.level'))
		);

		RSFirewallToolbarHelper::addFilter(
			JText::_('COM_RSFIREWALL_SELECT_BLOCKED_STATUS'),
			'filter_blocked_status',
			JHtml::_('select.options', $this->getBlockedStatuses(), 'value', 'text', $this->getState('filter.blocked_status'))
		);
		
		return RSFirewallToolbarHelper::render();
	}
}