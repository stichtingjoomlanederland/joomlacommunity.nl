<?php
/**
* @package RSComments!
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('_JEXEC') or die('Restricted access');

class RSCommentsModelComponents extends JModelList
{
	/**
	 * Constructor.
	 *
	 * @param	array	An optional associative array of configuration settings.
	 * @see		JController
	 * @since	1.6
	 */
	public function __construct($config = array()) {
		parent::__construct($config);
	}
	
	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @return	void
	 * @since	1.6
	 */
	protected function populateState($ordering = null, $direction = null) {
		$this->setState('components.filter.search',		$this->getUserStateFromRequest($this->context.'.components.filter.search', 'filter_search'));

		// List state information.
		parent::populateState('ordering', 'asc');
	}

	/**
	 * Build an SQL query to load the list data.
	 *
	 * @return	JDatabaseQuery
	 * @since	1.6
	 */
	protected function getListQuery() {
		$db 	= JFactory::getDBO();
		$query 	= $db->getQuery(true);

		$search 	= $this->getState('components.filter.search');
		$component 	= JFactory::getApplication()->input->get('component', '', 'string');

		$results = array();
		$query->select($db->qn('id').', '.$db->qn('title'));

		switch($component) {
			case 'com_content':
				$query->from($db->qn('#__content'));
				$query->where($db->qn('state').' IN (0,1,2)');
			break;
			case 'com_rsblog':
				$query->from($db->qn('#__rsblog_posts'));
				$query->where($db->qn('published').' IN (0,1)');
			break;
			case 'com_k2':
				$query->from($db->qn('#__k2item'));
				$query->where($db->qn('published').' IN (0,1)');
			break;
			case 'com_flexicontent':
				$query->from($db->qn('#__flexicontent_items'));
				$query->where($db->qn('state').' IN (0,1)');
			break;
		}

		if(!empty($search))
			$query->where($db->qn('title').' LIKE '.$db->q('%'.$search.'%'));

		$query->order( $db->qn('ordering').' ASC');
		return $query;
	}
	
	public function getFilterBar() {
		$options = array();
		$options['search'] = array(
			'label' => JText::_('JSEARCH_FILTER'),
			'value' => $this->getState('components.filter.search')
		);

		$bar = new RSFilterBar($options);
		return $bar;
	}
}