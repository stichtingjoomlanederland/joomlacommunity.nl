<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' );

class rseventsproModelCategories extends JModelList
{
	/**
	 * Constructor.
	 *
	 * @param   array  $config  An optional associative array of configuration settings.
	 *
	 * @see     JController
	 * @since   1.6
	 */
	public function __construct($config = array())
	{
		if (empty($config['filter_fields']))
		{
			$config['filter_fields'] = array(
				'id', 'a.id',
				'title', 'a.title',
				'alias', 'a.alias',
				'published', 'a.published',
				'access', 'a.access', 'access_level',
				'language', 'a.language',
				'checked_out', 'a.checked_out',
				'checked_out_time', 'a.checked_out_time',
				'created_time', 'a.created_time',
				'created_user_id', 'a.created_user_id',
				'lft', 'a.lft',
				'rgt', 'a.rgt',
				'level', 'a.level',
				'path', 'a.path'
			);
		}

		parent::__construct($config);
	}

	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @param   string  $ordering   An optional ordering field.
	 * @param   string  $direction  An optional direction (asc|desc).
	 *
	 * @return  void
	 *
	 * @since   1.6
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		$app = JFactory::getApplication();
		$context = $this->context;
		
		$limitstart = $app->input->getInt('lstart',0);

		$search = $this->getUserStateFromRequest($context . '.search', 'filter_search');
		$this->setState('filter.search', $search);

		$level = $this->getUserStateFromRequest($context . '.filter.level', 'filter_level');
		$this->setState('filter.level', $level);

		$access = $this->getUserStateFromRequest($context . '.filter.access', 'filter_access');
		$this->setState('filter.access', $access);

		$published = $this->getUserStateFromRequest($context . '.filter.published', 'filter_published', '');
		$this->setState('filter.published', $published);

		$language = $this->getUserStateFromRequest($context . '.filter.language', 'filter_language', '');
		$this->setState('filter.language', $language);
		
		// List state information.
		parent::populateState('a.lft', 'asc');
		
		$this->setState('list.start', $limitstart);

		// Force a language
		$forcedLanguage = $app->input->get('forcedLanguage');

		if (!empty($forcedLanguage))
		{
			$this->setState('filter.language', $forcedLanguage);
			$this->setState('filter.forcedLanguage', $forcedLanguage);
		}
	}

	/**
	 * @return  string
	 *
	 * @since   1.6
	 */
	protected function getListQuery()
	{
		// Create a new query object.
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		$user = JFactory::getUser();

		// Select the required fields from the table.
		$query->select(
			$this->getState(
				'list.select',
				'a.id, a.title, a.alias, a.note, a.published, a.access' .
				', a.checked_out, a.checked_out_time, a.created_user_id' .
				', a.path, a.parent_id, a.level, a.lft, a.rgt' .
				', a.language'
			)
		);
		$query->from('#__categories AS a');

		// Join over the language
		$query->select('l.title AS language_title')
			->join('LEFT', $db->quoteName('#__languages') . ' AS l ON l.lang_code = a.language');

		// Join over the users for the checked out user.
		$query->select('uc.name AS editor')
			->join('LEFT', '#__users AS uc ON uc.id=a.checked_out');

		// Join over the asset groups.
		$query->select('ag.title AS access_level')
			->join('LEFT', '#__viewlevels AS ag ON ag.id = a.access');

		// Join over the users for the author.
		$query->select('ua.name AS author_name')
			->join('LEFT', '#__users AS ua ON ua.id = a.created_user_id');

		$query->where('a.extension = ' . $db->quote('com_rseventspro'));

		// Filter on the level.
		if ($level = $this->getState('filter.level'))
		{
			$query->where('a.level <= ' . (int) $level);
		}

		// Filter by access level.
		if ($access = $this->getState('filter.access'))
		{
			$query->where('a.access = ' . (int) $access);
		}

		// Implement View Level Access
		if (!$user->authorise('core.admin'))
		{
			$groups = implode(',', $user->getAuthorisedViewLevels());
			$query->where('a.access IN (' . $groups . ')');
		}

		// Filter by published state
		$published = $this->getState('filter.published');
		if (is_numeric($published))
		{
			$query->where('a.published = ' . (int) $published);
		}
		elseif ($published === '')
		{
			$query->where('(a.published IN (0, 1))');
		}

		// Filter by search in title
		$search = $this->getState('filter.search');
		if (!empty($search))
		{
			if (stripos($search, 'id:') === 0)
			{
				$query->where('a.id = ' . (int) substr($search, 3));
			}
			elseif (stripos($search, 'author:') === 0)
			{
				$search = $db->quote('%' . $db->escape(substr($search, 7), true) . '%');
				$query->where('(ua.name LIKE ' . $search . ' OR ua.username LIKE ' . $search . ')');
			}
			else
			{
				$search = $db->quote('%' . str_replace(' ', '%', $db->escape(trim($search), true) . '%'));
				$query->where('(a.title LIKE ' . $search . ' OR a.alias LIKE ' . $search . ' OR a.note LIKE ' . $search . ')');
			}
		}

		// Filter on the language.
		if ($language = $this->getState('filter.language'))
		{
			$query->where('a.language = ' . $db->quote($language));
		}

		// Add the list ordering clause
		$listOrdering = $this->getState('list.ordering', 'a.lft');
		$listDirn = $db->escape($this->getState('list.direction', 'ASC'));
		if ($listOrdering == 'a.access')
		{
			$query->order('a.access ' . $listDirn . ', a.lft ' . $listDirn);
		}
		else
		{
			$query->order($db->escape($listOrdering) . ' ' . $listDirn);
		}

		//echo nl2br(str_replace('#__','jos_',$query));
		return $query;
	}
	
	/**
	 *	Method to set the side bar
	 */
	public function getSidebar() {
		if (rseventsproHelper::isJ3()) {
			JHtmlSidebar::addFilter(
				JText::_('JOPTION_SELECT_MAX_LEVELS'),
				'filter_level',
				JHtml::_('select.options', $this->levels(), 'value', 'text', $this->getState('filter.level'), true)
			);
			JHtmlSidebar::addFilter(
				JText::_('JOPTION_SELECT_PUBLISHED'),
				'filter_published',
				JHtml::_('select.options', $this->states(), 'value', 'text', $this->getState('filter.published'), true)
			);
			JHtmlSidebar::addFilter(
				JText::_('JOPTION_SELECT_ACCESS'),
				'filter_access',
				JHtml::_('select.options', $this->access(), 'value', 'text', $this->getState('filter.access'), true)
			);
			JHtmlSidebar::addFilter(
				JText::_('JOPTION_SELECT_LANGUAGE'),
				'filter_language',
				JHtml::_('select.options', $this->language(), 'value', 'text', $this->getState('filter.language'), true)
			);
			
			return JHtmlSidebar::render();
		}
		
		return;
	}
	
	/**
	 *	Method to set the filter bar
	 */
	public function getFilterBar() {
		$options = array();
		$options['search'] = array(
			'label' => JText::_('JSEARCH_FILTER'),
			'value' => $this->getState('filter.search')
		);
		$options['listDirn']  = $this->getState('list.direction', 'desc');
		$options['listOrder'] = $this->getState('list.ordering', 'u.date');
		$options['sortFields'] = array(
			JHtml::_('select.option', 'a.lft', JText::_('JGRID_HEADING_ORDERING')),
			JHtml::_('select.option', 'a.published', JText::_('JSTATUS')),
			JHtml::_('select.option', 'a.title', JText::_('JGLOBAL_TITLE')),
			JHtml::_('select.option', 'a.access', JText::_('JGRID_HEADING_ACCESS')),
			JHtml::_('select.option', 'language', JText::_('JGRID_HEADING_LANGUAGE')),
			JHtml::_('select.option', 'a.id', JText::_('JGRID_HEADING_ID'))
		);
		$options['rightItems'] = array(
			array(
				'input' => '<select id="filter_level" name="filter_level" class="inputbox" onchange="this.form.submit()">'."\n"
						   .'<option value="">'.JText::_('JOPTION_SELECT_MAX_LEVELS').'</option>'."\n"
						   .JHtml::_('select.options', $this->levels(), 'value', 'text', $this->getState('filter.level'), true)."\n"
						   .'</select>'
				),
			
			array(
				'input' => '<select id="filter_published" name="filter_published" class="inputbox" onchange="this.form.submit()">'."\n"
						   .'<option value="">'.JText::_('JOPTION_SELECT_PUBLISHED').'</option>'."\n"
						   .JHtml::_('select.options', $this->states(), 'value', 'text', $this->getState('filter.published'), true)."\n"
						   .'</select>'
				),
			
			array(
				'input' => '<select id="filter_access" name="filter_access" class="inputbox" onchange="this.form.submit()">'."\n"
						   .'<option value="">'.JText::_('JOPTION_SELECT_ACCESS').'</option>'."\n"
						   .JHtml::_('select.options', $this->access(), 'value', 'text', $this->getState('filter.access'), true)."\n"
						   .'</select>'
				),
			
			array(
				'input' => '<select id="filter_language" name="filter_language" class="inputbox" onchange="this.form.submit()">'."\n"
						   .'<option value="">'.JText::_('JOPTION_SELECT_LANGUAGE').'</option>'."\n"
						   .JHtml::_('select.options', $this->language(), 'value', 'text', $this->getState('filter.language'), true)."\n"
						   .'</select>'
				)
		);
		
		$bar = new RSFilterBar($options);
		return $bar;
	}
	
	protected function levels() {
		// Levels filter.
		$options	= array();
		$options[]	= JHtml::_('select.option', '1', JText::_('J1'));
		$options[]	= JHtml::_('select.option', '2', JText::_('J2'));
		$options[]	= JHtml::_('select.option', '3', JText::_('J3'));
		$options[]	= JHtml::_('select.option', '4', JText::_('J4'));
		$options[]	= JHtml::_('select.option', '5', JText::_('J5'));
		$options[]	= JHtml::_('select.option', '6', JText::_('J6'));
		$options[]	= JHtml::_('select.option', '7', JText::_('J7'));
		$options[]	= JHtml::_('select.option', '8', JText::_('J8'));
		$options[]	= JHtml::_('select.option', '9', JText::_('J9'));
		$options[]	= JHtml::_('select.option', '10', JText::_('J10'));

		return $options;
	}
	
	protected function states() {
		return JHtml::_('jgrid.publishedOptions', array('published' => true, 'unpublished' => true, 'archived' => false, 'trash' => false, 'all' => false), true);
	}
	
	protected function access () {
		$db = JFactory::getDbo();
		$query = $db->getQuery(true)
			->select($db->qn('a.id','value'))->select($db->qn('a.title','text'))
			->from($db->qn('#__viewlevels','a'))
			->group($db->qn('a.id').', '.$db->qn('a.title').', '.$db->qn('a.ordering'))
			->order($db->qn('a.ordering').' ASC')
			->order($db->qn('title').' ASC');

		$db->setQuery($query);
		return $db->loadObjectList();
	}
	
	protected function language() {
		return array_merge(array(JHtml::_('select.option', '*', JText::_('JALL'))),JHtml::_('contentlanguage.existing'));
	}
}