<?php
/**
* @package RSComments!
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('_JEXEC') or die('Restricted access');

class RscommentsModelSubscriptions extends JModelList
{
	/**
	 * Constructor.
	 *
	 * @param	array	An optional associative array of configuration settings.
	 * @see		JController
	 * @since	1.6
	 */
	public function __construct($config = array()) {
		if (empty($config['filter_fields'])) {
			$config['filter_fields'] = array(
				'name', 'option', 'email'
			);
		}
		
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
		$app = JFactory::getApplication();
		$old_component = $app->getUserStateFromRequest($this->context.'.filter.component', '', '');
		$new_component = JFactory::getApplication()->input->get('filter_component', '', 'string');
		
		$this->setState($this->context.'.filter.search',		$app->getUserStateFromRequest($this->context.'.filter.search', 'filter_search'));
		$this->setState($this->context.'.filter.component',		$app->getUserStateFromRequest($this->context.'.filter.component', 'filter_component', ''));
		if($old_component != $new_component || (empty($old_component) && empty($new_component)))
			$this->setState($this->context.'.filter.component_id',	'');
		else
			$this->setState($this->context.'.filter.component_id',	$app->getUserStateFromRequest($this->context.'.filter.component_id', 'filter_component_id', ''));

		// List state information.
		parent::populateState('IdSubscription', 'DESC');
	}
	
	/**
	 * Build an SQL query to load the list data.
	 *
	 * @return	JDatabaseQuery
	 * @since	1.6
	 */
	protected function getListQuery() {
		$db 	= JFactory::getDbo();
		$query 	= $db->getQuery(true);

		// Select fields
		$query->select('*');

		// Select from table
		$query->from($db->qn('#__rscomments_subscriptions'));

		// Filter by search in title
		$search = $this->getState($this->context.'.filter.search');
		
		if (!empty($search)) {
			$search = $db->q('%'.$search.'%');
			$query->where('('.$db->qn('name').' LIKE '.$search.' OR '.$db->qn('email').' LIKE '.$search.')');
		}

		// Filter by components
		$component = $this->getState($this->context.'.filter.component');
		if(!empty($component)) {
			$query->where($db->qn('option').' = '.$db->q($component));
		}

		// Filter by component id
		$component_id = $this->getState($this->context.'.filter.component_id');
		if(is_numeric($component_id)) {
			$query->where($db->qn('id').' = '.$db->q($component_id));
		}

		// Add the list ordering clause
		$listOrdering  = $this->getState('list.ordering', 'IdSubscription');
		$listDirection = $this->getState('list.direction', 'DESC');
		
		$query->order($db->qn($listOrdering).' '.$db->escape($listDirection));

		return $query;
	}

	// get filters
	public function getFilterBar() {
		$options = array();

		$options['search'] = array(
			'label' => JText::_('JSEARCH_FILTER'),
			'value' => $this->getState($this->context.'.filter.search')
		);
		$options['limitBox']   = $this->getPagination()->getLimitBox();
		$options['listDirn']   = $this->getState('list.filter_order_Dir', 'desc');
		$options['listOrder']  = $this->getState('list.ordering', 'Id');
		$options['sortFields'] = array(
			JHtml::_('select.option', 'name', JText::_('COM_RSCOMMENTS_SUBSCRIPTION_NAME')),
			JHtml::_('select.option', 'email', JText::_('COM_RSCOMMENTS_SUBSCRIPTION_EMAIL')),
			JHtml::_('select.option', 'option', JText::_('COM_RSCOMMENTS_COMMENT_COMPONENT'))
		);
	
		// build components list select options
		$joomla_comps = RSCommentsHelper::getComponents();
		$comp = array();
		$comp[] 			= JHtml::_('select.option', '', JText::_('COM_RSCOMMENTS_SELECT_COMPONENT_LIST_ITEM'));
		foreach($joomla_comps as $component)
			$comp[] 			= JHtml::_('select.option', $component, RSCommentsHelper::component($component));

		// set the components list 
		$options['Components']   			= $comp;
		// set the selected filter component
		$options['ComponentFilter']	 		= $this->getState($this->context.'.filter.component', '');

		// build the article filter button
		$component_id_button =  array('input' => '');
		$options['ComponentArticleFilter'] = '';
		if ($this->getState($this->context.'.filter.component') == 'com_content' || 
		    $this->getState($this->context.'.filter.component') == 'com_rsblog' || 
		    $this->getState($this->context.'.filter.component') == 'com_k2' || 
		    $this->getState($this->context.'.filter.component') == 'com_flexicontent') 
		{
			$this->article = RSCommentsHelper::ArticleTitle($this->getState($this->context.'.filter.component'), $this->getState($this->context.'.filter.component_id'));
			$SelectArticleBtn = '<div class="row-fluid hidden-phone"><div class="span12 offset0"><a class="modal btn btn-info btn-xsmall btnarticle" style="" rel="{handler: \'iframe\', size: {x: 600, y: 475}}" href="index.php?option=com_rscomments&view=components&component='.$this->state->get($this->context.'.filter.component').'&tmpl=component">'.(!empty($this->article) ? $this->article : JText::_('COM_RSCOMMENTS_SELECT_ARTICLE')).'</a></div></div>';
			$component_id_button =  array( 'input' => $SelectArticleBtn);
			$options['ComponentArticleFilter']	= $SelectArticleBtn;	
		}

		$options['rightItems'] = array(
			array(
				'input' => '<select name="filter_component" class="inputbox" onchange="this.form.submit()">'."\n"
						   .JHtml::_('select.options', $comp, 'value', 'text', $options['ComponentFilter'], false)."\n"
						   .'</select>'
			),
			$component_id_button
		);
		
		$bar = new RSFilterBar($options);
		return $bar;
	}

	public function getSideBar() {
		require_once JPATH_COMPONENT.'/helpers/toolbar.php';
		return RSCommentsToolbarHelper::render();
	}
}