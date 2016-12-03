<?php
/**
 * @package    RSFirewall!
 * @copyright  (c) 2009 - 2016 RSJoomla!
 * @link       https://www.rsjoomla.com
 * @license    GNU General Public License http://www.gnu.org/licenses/gpl-3.0.en.html
 */

defined('_JEXEC') or die('Restricted access');

class RSFirewallModelLists extends JModelList
{
	public function __construct($config = array()) {
		if (empty($config['filter_fields'])) {
			$config['filter_fields'] = array(
				'date', 'ip', 'reason', 'type', 'published'
			);
		}

		parent::__construct($config);
	}
	
	protected function getListQuery() {
		$db 	= JFactory::getDBO();
		$query 	= $db->getQuery(true);
		
		// get filtering states
		$search = $this->getState('filter.search');
		$type 	= $this->getState('filter.type');
		$state 	= $this->getState('filter.state');
		
		$query->select('*')->from('#__rsfirewall_lists');
		// search
		if ($search != '') {
			$search = $db->q('%'.str_replace(' ', '%', $db->escape($search, true)).'%', false);
			$query->where('('.$db->qn('ip').' LIKE '.$search.' OR '.$db->qn('reason').' LIKE '.$search.')');
		}
		// type
		if ($type != '') {
			$query->where($db->qn('type').'='.$db->q($type));
		}
		// published/unpublished
		if ($state != '') {
			$query->where($db->qn('published').'='.$db->q($state));
		}
		// order by
		$query->order($db->escape($this->getState('list.ordering', 'date')).' '.$db->escape($this->getState('list.direction', 'desc')));
		
		return $query;
	}
	
	protected function populateState($ordering = null, $direction = null) {
		
		$this->setState('filter.search', 	$this->getUserStateFromRequest($this->context.'.filter.search', 'filter_search'));
		$this->setState('filter.type', 		$this->getUserStateFromRequest($this->context.'.filter.type', 	'filter_type'));
		$this->setState('filter.state', 	$this->getUserStateFromRequest($this->context.'.filter.state', 	'filter_state'));
		
		// List state information.
		parent::populateState('date', 'desc');
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
		$options['listOrder'] = $this->getState('list.ordering', 'date');
		$options['sortFields'] = array(
			JHtml::_('select.option', 'date', JText::_('COM_RSFIREWALL_LIST_DATE')),
			JHtml::_('select.option', 'ip', JText::_('COM_RSFIREWALL_IP_ADDRESS')),
			JHtml::_('select.option', 'reason', JText::_('COM_RSFIREWALL_LIST_REASON')),
			JHtml::_('select.option', 'type', JText::_('COM_RSFIREWALL_LIST_TYPE')),
			JHtml::_('select.option', 'published', JText::_('JPUBLISHED'))
		);
		$options['rightItems'] = array(
			array(
				'input' => '<select name="filter_state" class="inputbox" onchange="this.form.submit()">'."\n"
						   .'<option value="">'.JText::_('JOPTION_SELECT_PUBLISHED').'</option>'."\n"
						   .JHtml::_('select.options', JHtml::_('jgrid.publishedOptions', array('published' => 1, 'unpublished' => 1, 'archived' => 0, 'trash' => 0, 'all' => 0)), 'value', 'text', $this->getState('filter.state'), true)."\n"
						   .'</select>'
			),
			array(
				'input' => '<select name="filter_type" class="inputbox" onchange="this.form.submit()">'."\n"
						   .'<option value="">'.JText::_('COM_RSFIREWALL_SELECT_TYPE').'</option>'."\n"
						   .JHtml::_('select.options', array(JHtml::_('select.option', 0, JText::_('COM_RSFIREWALL_LIST_TYPE_0')), JHtml::_('select.option', 1, JText::_('COM_RSFIREWALL_LIST_TYPE_1'))), 'value', 'text', $this->getState('filter.type'), false)."\n"
						   .'</select>'
			),
		);
		
		$bar = new RSFilterBar($options);
		
		return $bar;
	}
	
	public function getSideBar() {
		require_once JPATH_COMPONENT.'/helpers/toolbar.php';
		
		RSFirewallToolbarHelper::addFilter(
			JText::_('JOPTION_SELECT_PUBLISHED'),
			'filter_state',
			JHtml::_('select.options', JHtml::_('jgrid.publishedOptions', array('published' => 1, 'unpublished' => 1, 'archived' => 0, 'trash' => 0, 'all' => 0)), 'value', 'text', $this->getState('filter.state'), true)
		);
		RSFirewallToolbarHelper::addFilter(
			JText::_('COM_RSFIREWALL_SELECT_TYPE'),
			'filter_type',
			JHtml::_('select.options', array(JHtml::_('select.option', 0, JText::_('COM_RSFIREWALL_LIST_TYPE_0')), JHtml::_('select.option', 1, JText::_('COM_RSFIREWALL_LIST_TYPE_1'))), 'value', 'text', $this->getState('filter.type'), false)
		);
		
		return RSFirewallToolbarHelper::render();
	}
	
	public function getDropdown() {
		require_once JPATH_COMPONENT.'/helpers/adapters/dropdown.php';
		
		$options  = array(
			'context' => 'list'
		);
		$dropdown = new RSDropdown($options);
		return $dropdown;
	}
}