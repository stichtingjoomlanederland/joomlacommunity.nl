<?php
/**
 * @package    DOCman
 * @copyright   Copyright (C) 2011 Timble CVBA (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

class ComDocmanModelDocuments extends ComDocmanModelAbstract
{
    public function __construct(KObjectConfig $config)
    {
        parent::__construct($config);

        $this->getState()
            ->insert('access', 'int')
            ->insert('access_raw', 'int')
            ->insert('group', 'int')
            ->insert('category', 'int')
            ->insert('category_children', 'boolean')
            ->insert('created_by', 'int')
            ->insert('created_on', 'string')
            ->insert('created_on_from', 'string')
            ->insert('created_on_to', 'string')
            ->insert('enabled', 'int')
            ->insert('status', 'cmd')
            ->insert('search', 'string')
            ->insert('storage_type', 'identifier')
            ->insert('storage_path', 'com:files.filter.path')
            ->insert('search_path', 'com:files.filter.path')
            ->insert('search_by', 'string', 'exact')
            ->insert('search_date', 'date')
            ->insert('search_contents', 'boolean', true)
            ->insert('image', 'com:files.filter.path')
            ->insert('day_range', 'int');
    }

    protected function _initialize(KObjectConfig $config)
    {
        $config->append(array(
            'behaviors' => array(
                'com://admin/docman.model.behavior.taggable'
            )
        ));

        parent::_initialize($config);
    }

    /**
     * Sets the page ID in the itemid property for the given documents
     *
     * Prefers the least generic menu item. For example if a document can be reached through a document menu item
     * and a list menu item, the document menu item is going to win.
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
            $query = $this->getObject('database.query.select')
                ->columns('tbl.docman_document_id')
                ->table(array('tbl' => $table->getName()))
                ->group('tbl.docman_document_id')
                ->where('tbl.docman_document_id IN :id')
                ->bind(array(
                    'id' => $id
                ));

            $callback = function($where, &$query) {
                foreach ($where as $page => $condition) {
                    /*
                     * Query results contain each (document,tag) pair so we use group by docman_document_id
                     * This means MAX(condition) will return 1 if the page matches any of the documents tag
                     */
                    $query->columns(array('page'.$page => 'MAX('.$condition.')'));
                }
            };

            $this->_addPageFiltersToQuery($this->_page_filters, $query, $callback);

            $document_page_map = $table->getAdapter()->select($query, KDatabase::FETCH_ARRAY_LIST, 'docman_document_id');
            $pages   = $this->_getPreferredPageList('document');

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
        parent::_buildQueryColumns($query);

        $query->columns('c.title AS category_title')
            ->columns('c.slug AS category_slug')
            ->columns('c.access AS category_access')
            ->columns('c.enabled AS category_enabled')
            ->columns('c.created_by AS category_owner')
            ->columns('CONCAT_WS(\'-\', tbl.docman_document_id, tbl.slug) AS alias')
            ->columns('tbl.access AS access_raw')
            ->columns('(CASE tbl.access WHEN 0 THEN COALESCE(c.access, 1) ELSE tbl.access END) AS access')
            ->columns('
                IF(tbl.enabled = 1,
                    IF(tbl.publish_on <> \'0000-00-00 00:00:00\' AND tbl.publish_on > :now,
                        :pending_value,
                        IF(tbl.unpublish_on <> \'0000-00-00 00:00:00\' AND :now > tbl.unpublish_on, 
                            :expired_value,
                            :published_value
                        )
                    ),
                    :unpublished_value
                ) AS status')
            ->bind(array(
                'now' => gmdate('Y-m-d H:i:s'),
                'published_value' => 'published',
                'unpublished_value' => 'unpublished',
                'expired_value' => 'expired',
                'pending_value' => 'pending',
            ))
            ->columns('viewlevel.title AS viewlevel_title')
            ->columns('IF(tbl.publish_on = 0, tbl.created_on, tbl.publish_on) AS publish_date')
            ->columns('GREATEST(tbl.created_on, tbl.modified_on) AS touched_on')
            ;
    }

    protected function _buildQueryJoins(KDatabaseQueryInterface $query)
    {
    	$query->join(array('c' => 'docman_categories'), 'tbl.docman_category_id = c.docman_category_id')
              ->join(array('viewlevel' => 'viewlevels'), '(CASE tbl.access WHEN 0 THEN COALESCE(c.access, 1) ELSE tbl.access END) = viewlevel.id');

        parent::_buildQueryJoins($query);
    }

    /**
     * Converts page filters to query to limit fetched entities per page
     *
     * @param $page_filters
     * @param $query
     * @param callable $callback
     */
    protected function _addPageFiltersToQuery($page_filters, $query, $callback)
    {
        if (is_scalar($page_filters)) {
            return;
        }

        $where     = array();
        $join_tags = false;
        $i         = 0;

        /** @var  $filter integer|Traversable */
        foreach ($page_filters as $page => $filter)
        {
            $parts = array();

            if (is_scalar($filter) && ($filter & static::PAGE_FILTER_ALL_DOCUMENTS)) {
                $parts[] = '1 = 1';
            }
            else {
                foreach ($filter as $type => $value)
                {
                    if ($type === 'categories' && count($value)) {
                        $parts[] = "tbl.docman_category_id IN :category_id$i";
                        $query->bind(array("category_id$i" => (array) $value));
                    }

                    if ($type === 'tags' && count($value)) {
                        $join_tags = true;
                        $parts[] = "menu_tag.slug IN :tag_slug$i";
                        $query->bind(array("tag_slug$i" => (array) $value));
                    }

                    if ($type === 'created_by' && (!empty($value) || $value === 0 || $value === '0'))
                    {
                        $parts[] = "tbl.created_by IN :created_by$i";
                        $query->bind(array("created_by$i" => (array) $value));
                    }

                    if ($type === 'document_slug')
                    {
                        $parts[] = "(tbl.slug = :slug$i)";
                        $query->bind(array("slug$i" => $value));
                    }
                }
            }

            if (count($parts)) {
                $where[$page] = '('.implode(' AND ', $parts).')';
            }

            $i++;
        }

        if ($join_tags) {
            $query->join('docman_tags_relations AS menu_tag_relation', 'menu_tag_relation.row = tbl.uuid');
            $query->join('docman_tags AS menu_tag', 'menu_tag.tag_id = menu_tag_relation.tag_id');

            if (!$query->isCountQuery()) {
                $query->group('tbl.docman_document_id');
            }

        }

        $callback($where, $query);
    }

    protected function _buildQueryPageFilter(&$query)
    {
        if ($this->_page_filters)
        {
            $filters = $this->_page_filters;

            $can_reach_all = is_array($filters) && (array_search(static::PAGE_FILTER_ALL_DOCUMENTS, $filters, true)
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
        $state = $this->getState();

        $this->_buildQueryPageFilter($query);

        $this->_buildQuerySearchKeyword($query);

        parent::_buildQueryWhere($query);

        $categories = (array) $state->category;
        if ($categories)
        {
            $include_children = $state->category_children;

            if ($include_children)
            {
                $query->join(array('r' => 'docman_category_relations'), 'r.descendant_id = tbl.docman_category_id')
                    ->where('r.ancestor_id IN :include_children_categories')
                    ->bind(array('include_children_categories' => $categories));
            }
            else {
                $query->where('tbl.docman_category_id IN :include_children_categories')
                    ->bind(array('include_children_categories' => $categories));
            }
        }

        if (is_numeric($state->enabled))
        {
            $user_enabled_clause = '';
            // Logged in users see their documents regardless of the access level
            if ($state->current_user) {
                $user_enabled_clause = 'tbl.created_by = :current_user OR c.created_by = :current_user  OR';
            }

            $query->where(sprintf('(%s tbl.enabled IN :enabled)', $user_enabled_clause))->bind(array(
                'enabled' => (array) $state->enabled,
                'current_user' => $state->current_user
            ));
        }

        if ($created_on = $state->created_on)
        {
            static $format_map = array(
                4  => '%Y', // 2014
                7  => '%Y-%m', // 2014-10
                10 => '%Y-%m-%d', // 2014-10-10
                13 => '%Y-%m-%d %H', // 2014-10-10 10
                16 => '%Y-%m-%d %H:%i', // 2014-10-10 10:10
                0  => '%Y-%m-%d %H:%i:%s' // 2014-10-10 10:10:10
            );

            $format = isset($format_map[strlen($created_on)]) ? $format_map[strlen($created_on)] : $format_map[0];

            $query->where("DATE_FORMAT(tbl.created_on, '$format') = :created_on")
                  ->bind(array('created_on' => $created_on));
        }

        if ($state->created_on_from) {
            $query->where("tbl.created_on >= :created_on_from")
                ->bind(array('created_on_from' => $state->created_on_from));
        }

        if ($state->created_on_to)
        {
            $end_date = $state->created_on_to;
            // Add the hour if it's missing to make the date inclusive
            if (preg_match('#^[0-9]{4}\-[0-9]{2}-[0-9]{2}$#', $end_date)) {
                $end_date .= ' 23:59:59';
            }

            $query->where("tbl.created_on <= :created_on_to")
                ->bind(array('created_on_to' => $end_date));
        }

        if ($state->search_date || $state->day_range)
        {
            $date      = $state->search_date ? ':date' : ':now';
            $date_bind = $state->search_date ?: null;

            if ($state->day_range) {
              $query->where("(tbl.created_on BETWEEN DATE_SUB($date, INTERVAL :days DAY) AND DATE_ADD($date, INTERVAL :days DAY))")
            		    ->bind(array('date' => $date_bind, 'days' => $state->day_range));
            }
        }

        if ($state->status === 'published')
        {
            $user_status_clause = '';
            // Logged in users see their documents regardless of the published status
            if ($state->current_user) {
                $user_status_clause = 'tbl.created_by = :current_user OR c.created_by = :current_user  OR';
            }

            $now = JFactory::getDate()->toSql();

            $query->where(sprintf('(%s (tbl.publish_on = 0 OR tbl.publish_on <= :publish_date))', $user_status_clause))
              	  ->where(sprintf('(%s (tbl.unpublish_on = 0 OR tbl.unpublish_on >= :publish_date))', $user_status_clause))
            	  ->bind(array(
                      'publish_date' => $now,
                      'current_user' => $state->current_user
                  ));
        }
        elseif ($state->status === 'pending')
        {
            $now = JFactory::getDate()->toSql();

            $query->where('(tbl.publish_on <> 0 AND tbl.publish_on >= :publish_date)')
	            ->bind(array('publish_date' => $now));
        }
        elseif ($state->status === 'expired')
        {
            $now = JFactory::getDate()->toSql();

            $query->where('(tbl.unpublish_on <> 0 AND tbl.unpublish_on <= :publish_date)')
	            ->bind(array('publish_date' => $now));
        }

        if ($state->access || $state->group)
        {
            $access = (array) $state->access;
            $user_access_clause = '';

            // Logged in users see their documents regardless of the published status
            if ($state->current_user !== null) {
                $access = array_merge($access, $this->getUserLevels($state->current_user));
                $user_access_clause = 'tbl.created_by = :current_user OR c.created_by = :current_user  OR';
            }

            if($access) {
                $query->where(sprintf('(%s c.access IN :access)', $user_access_clause));
            }

            if($state->group) {
                $access = array_merge($access, $this->getGroupLevels($state->group));

                // no access level match the groups, should return 0 results
                if (empty($access)) {
                    $access = array(-PHP_INT_MAX);
                }
            }

            $query
            ->where(sprintf('(%s (CASE tbl.access WHEN 0 THEN COALESCE(c.access, 1) ELSE tbl.access END) IN :access)', $user_access_clause))
            ->bind(array(
                //if no access values then use a value that ensures 0 results
                'access' => (array) $access,
                'current_user' => $state->current_user
            ));
        }

        if (is_numeric($state->created_by) || !empty($state->created_by)) {
            $query->where('tbl.created_by IN :created_by')->bind(array('created_by' => (array) $state->created_by));
        }

        if ($state->storage_type) {
            $query->where('tbl.storage_type IN :storage_type')->bind(array('storage_type' => (array) $state->storage_type));
        }

        if ($image = $state->image) {
            $query->where('tbl.image IN :image')->bind(array('image' => (array) $image));
        }

        if ($state->storage_path) {
            $query->where('tbl.storage_path IN :storage_path')->bind(array('storage_path' => (array) $state->storage_path));
        }

        if ($state->search_path !== null)
        {
            if ($state->search_path === '')
            {
                $operation = 'NOT LIKE';
                $path = "%/%";
            }
            else
            {
                $operation = 'LIKE';
                $path = $state->search_path;
            }

            $query->where('tbl.storage_path '.$operation. ' :path')->bind(array('path' => $path));
        }
    }

    protected function _buildQuerySearchKeyword(KDatabaseQueryInterface $query)
    {
        $state  = $this->getState();
        $search = $state->search;

        if (!empty($search))
        {
            $search_column = null;

            // Parse $state->search for possible column prefix
            if (preg_match('#^(title|id|contents|description)\s*:\s*(.+)\s*$#i', $search, $matches)) {
                $search_column = $matches[1];
                $search       = $matches[2];
            }

            // Search in the form of id:NUM
            if ($search_column === 'id') {
                $query->where('(tbl.' . $this->getTable()->getIdentityColumn() . ' = :search)')
                    ->bind(array('search' => $search));
            }
            else
            {
                // Convert search field into proper column name
                if ($search_column === 'contents') {
                    $search_column = ['contents.contents'];
                }
                else if (in_array($search_column, ['title', 'description'])) {
                    $search_column = ['tbl.'.$search_column];
                }
                else {
                    $search_column = ['tbl.title', 'tbl.description'];

                    if ($state->search_contents) {
                        $search_column[] = 'contents.contents';
                    }
                }

                if (in_array('contents.contents', $search_column)) {
                    $query->join(array('contents' => 'docman_document_contents'), 'contents.docman_document_id = tbl.docman_document_id');
                }

                switch ($state->search_by)
                {
                    case 'any':
                        $conditions = array();

                        foreach ($search_column as $column) {
                            $conditions[] = $column . ' RLIKE :search';
                        }

                        if ($conditions) {
                            $query->where('(' . implode(' OR ', $conditions) . ')');
                            $query->bind(array('search' => implode('|', explode(' ', $search))));
                        }

                        break;
                    case 'all':
                        $i = 0;
                        foreach (explode(' ', $search) as $keyword)
                        {
                            $conditions = array();

                            foreach ($search_column as $column) {
                                $conditions[] = $column . " LIKE :search$i";
                            }

                            if ($conditions) {
                                $query->where('(' . implode(' OR ', $conditions) . ')');
                                $query->bind(array("search$i" => '%'.$keyword.'%'));
                            }

                            $i++;
                        }

                        break;
                    case 'exact':
                    default:
                        $conditions = array();

                        foreach ($search_column as $column) {
                            $conditions[] = $column . " LIKE :search";
                        }

                        if ($conditions) {
                            $query->where('(' . implode(' OR ', $conditions) . ')');
                            $query->bind(array('search' => '%'.$search.'%'));
                        }

                        break;
                }
            }


        }
    }
}
