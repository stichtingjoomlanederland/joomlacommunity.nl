<?php
/**
 * @package    DOCman
 * @copyright   Copyright (C) 2011 Timble CVBA (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

class ComDocmanModelCategories extends ComDocmanModelAbstract
{
    public function __construct(KObjectConfig $config)
    {
        parent::__construct($config);

        $this->getState()
            ->insert('access',      'int')
            ->insert('folder',      'string')
            ->insert('group',       'int')
            ->insert('created_by',  'int')
            ->insert('enabled',     'int');
    }

    protected function _initialize(KObjectConfig $config)
    {
        $config->append(array(
            'behaviors' => array(
                'nestable',
                'searchable' => array('columns' => array('title', 'description'))
            )
        ));

        parent::_initialize($config);
    }

    /**
     * Sets the page ID in the itemid property for the given categories
     *
     * Prefers the least generic menu item.
     *
     * @param $entities
     */
    public function setPage(KModelEntityInterface $entities)
    {
        if (count($entities))
        {
            $id = array();

            foreach ($entities as $entity) {
                $id[] = $entity->id;
            }

            $table = $this->getTable();
            /** @var $query KDatabaseQuerySelect */
            $query = $this->getObject('database.query.select');
            $query->columns('tbl.docman_category_id');
            $query->table(array('tbl' => $table->getName()));
            $query->group('tbl.docman_category_id');

            $query->where('tbl.docman_category_id IN :id')
                ->bind(array(
                    'id' => $id
                ));

            $callback = function($where, &$query) {
                foreach ($where as $page => $condition) {
                    $query->columns(array('page'.$page => '('.$condition.')'));
                }
            };

            $this->_addPageFiltersToQuery($this->_page_filters, $query, $callback);

            $document_page_map = $table->getAdapter()->select($query, KDatabase::FETCH_ARRAY_LIST, 'docman_category_id');
            $pages   = $this->_getPreferredPageList('category');

            foreach ($entities as $entity)
            {
                if (!isset($document_page_map[$entity->id])) {
                    continue;
                }

                $result = $document_page_map[$entity->id];

                foreach ($pages as $page)
                {
                    if (isset($result['page'.$page]) && $result['page'.$page])
                    {
                        $entity->itemid = $page;
                        break;
                    }
                }
            }
        }
    }

    protected function _buildQueryColumns(KDatabaseQueryInterface $query)
    {
        $query->columns(array('viewlevel_title' => 'viewlevel.title'));
        $query->columns(array('folder' => 'folder.folder'));
        $query->columns(array('automatic_folder' => 'folder.automatic'));

        parent::_buildQueryColumns($query);
    }

    protected function _buildQueryJoins(KDatabaseQueryInterface $query)
    {
        $query->join(array('viewlevel' => 'viewlevels'), 'tbl.access = viewlevel.id');
        $query->join(array('folder' => 'docman_category_folders'), 'tbl.docman_category_id = folder.docman_category_id');

        parent::_buildQueryJoins($query);
    }

    /**
     * Converts page filters to query to limit fetched entities per page
     *
     * @param $page_filters
     * @param $query
     * @param $callback
     */
    protected function _addPageFiltersToQuery($page_filters, $query, $callback)
    {
        if (is_scalar($page_filters)) {
            return;
        }

        $where     = array();
        $i         = 0;

        /** @var  $filter integer|Traversable */
        foreach ($page_filters as $page => $filter)
        {
            $parts = array();

            if (is_scalar($filter) && ($filter & static::PAGE_FILTER_ALL_CATEGORIES)) {
                $parts[] = '1 = 1';
            }
            else {
                foreach ($filter as $type => $value)
                {
                    if ($type === 'categories' && count($value)) {
                        $parts[] = "tbl.docman_category_id IN :category_id$i";
                        $query->bind(array("category_id$i" => (array) $value));
                    }
                }
            }

            if (count($parts)) {
                $where[$page] = '('.implode(' AND ', $parts).')';
            }

            $i++;
        }

        $callback($where, $query);
    }

    protected function _buildQueryPageFilter(&$query)
    {
        if ($this->_page_filters)
        {
            $filters = $this->_page_filters;

            $can_reach_all = is_array($filters) && (array_search(static::PAGE_FILTER_ALL_CATEGORIES, $filters, true)
                || array_search(static::PAGE_FILTER_ALL_ENTITIES, $filters, true));

            if ($filters === static::PAGE_FILTER_NO_RESULTS) {
                $query->where('1 = 2');
            }
            elseif (!$can_reach_all)
            {
                $callback = function($where, &$query) {
                    if (!empty($where)) {
                        $query->where('('.implode(' OR ', $where).')');
                    }
                };

                $this->_addPageFiltersToQuery($this->_page_filters, $query, $callback);
            }
        }
    }

    protected function _buildQueryWhere(KDatabaseQueryInterface $query)
    {
        parent::_buildQueryWhere($query);

        $state = $this->getState();

        $this->_buildQueryPageFilter($query);



        if (is_numeric($state->enabled)) {
            $query->where('tbl.enabled = :enabled')
                ->bind(array('enabled' => $state->enabled));
        }

        if ($state->created_by)
        {
            $query->where('tbl.created_by IN :created_by')
                ->bind(array('created_by' => (array) $state->created_by));
        }

        if ($state->access || $state->group)
        {
            $access = (array) $state->access;
            $user_access_clause = '';

            // Logged in users see their documents regardless of the published status
            if ($state->current_user !== null) {
                $access = array_merge($state->access, $this->getUserLevels($state->current_user));
                $user_access_clause = 'tbl.created_by = :current_user OR';
            }

            if($state->group) {
                $access = array_merge($access, $this->getGroupLevels($state->group));

                // no access level match the groups, should return 0 results
                if (empty($access)) {
                    $access = array(-PHP_INT_MAX);
                }
            }

            $query->where(sprintf('(%s tbl.access IN :access)', $user_access_clause))
                  ->bind(array(
                    //if no access values then use a value that ensures 0 results
                    'access' => (array) $access,
                    'current_user' => $state->current_user
                  ));
        }

        if ($state->folder)
        {
            $query->where('folder.folder IN :folder')->bind(array(
                'folder' => (array) $state->folder
            ));
        }
    }

    protected function _getPageFilters($pages)
    {
        $pages = (array) KObjectConfig::unbox($pages);

        $document_pages_list = $this->getPagesModel()->reset()->view('document')->fetch()->toArray();
        $document_pages = array_intersect($pages, array_keys($document_pages_list));

        // Return an empty list of categories if the model is filtered against single document pages only.
        if (count($document_pages) === count($pages)) {
            $conditions = static::PAGE_FILTER_NO_RESULTS;
        } else {
            $conditions = parent::_getPageFilters($pages);
        }

        return $conditions;
    }
}
