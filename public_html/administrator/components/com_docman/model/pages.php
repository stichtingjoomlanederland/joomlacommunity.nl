<?php
/**
 * @package     DOCman
 * @copyright   Copyright (C) 2011 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

class ComDocmanModelPages extends KModelAbstract
{
    /**
     * Pages pointing to this component
     *
     * @var array
     */
    protected static $_pages = array();

    /**
     * An array of categories that are reachable through a page
     *
     * @var array
     */
    protected static $_categories;

    protected static $_filtered_categories;

    /**
     * Constructor
     *
     * @param KObjectConfig $config Configuration options
     */
    public function __construct(KObjectConfig $config)
    {
        parent::__construct($config);

        $this->getState()
            ->insert('id', 'int', null, true)
            ->insert('alias', 'cmd', null, true)
            ->insert('language', 'cmd', null)
            ->insert('access', 'int', null) // -1 for no access filter
            ->insert('view', 'cmd')
            ->insert('sort', 'cmd')
            ->insert('direction', 'word', 'asc');
    }

    protected function _initialize(KObjectConfig $config)
    {
        $config->append(array(
            'identity_key'     => 'id'
        ));

        parent::_initialize($config);
    }

    /**
     * Returns an array of component pages.
     *
     * Each page includes a children property that contains a list of all categories reachable by the page
     *
     * @return array
     */
    protected function _getPages()
    {
        if (!self::$_pages)
        {
            $component = JComponentHelper::getComponent('com_' . $this->getIdentifier()->package);

            $attributes = array('component_id');
            $values     = array($component->id);

            if ($this->getState()->language !== null)
            {
                $attributes[] = 'language';

                if ($this->getState()->language === 'all') {
                    $values[] = JFactory::getDbo()->setQuery('SELECT DISTINCT language FROM #__menu')->loadColumn();
                } else {
                    $values[] = $this->getState()->language;
                }
            }

            if ($this->getState()->access !== null)
            {
                $attributes[] = 'access';
                $values[]     = $this->getState()->access === -1 ? null : $this->getState()->access;
            }


            $items = JApplication::getInstance('site')->getMenu()->getItems($attributes, $values);

            foreach ($items as $item)
            {
                $item           = clone $item;
                $item->children = array();

                if ($item->language === '*') {
                    $item->language = '';
                }

                self::$_pages[$item->id] = $item;
            }

            foreach (self::$_pages as &$item)
            {
                // Assign categories and their children to pages
                if (isset($item->query['view']) && in_array($item->query['view'], array('list', 'tree')) && isset($item->query['slug'])) {
                    $item->children = $this->_getCategoryChildren($item->query['slug']);
                }

                if (isset($item->query['view']) && $item->query['view'] === 'flat')
                {
                    $item->children = array();
                    $item_cats = (array) (!empty($item->query['category']) ? $item->query['category'] : array());

                    if ($item_cats && !empty($item->query['category_children']))
                    {
                        $categories = $this->_getFilteredCategories();
                        $children   = array();

                        foreach ($categories as $category) {
                            if (in_array($category->ancestor_id, $item_cats)) {
                                $children[] = $category->descendant_id;
                            }
                        }

                        $item->children = $children;
                    }
                }
            }
            unset($item);
        }

        return self::$_pages;
    }

    /**
     * Filters pages by view
     *
     * @param array $pages Page list
     * @param array $value Allowed views
     */
    protected function _filterPagesByView(&$pages, array $value)
    {
        foreach ($pages as $i => $page)
        {
            if (!isset($page->query['view']) || !in_array($page->query['view'], $value)) {
                unset($pages[$i]);
            }
        }
    }

    /**
     * Filters pages by a given field
     *
     * @param array  $pages Page list
     * @param string $field Field to filter against
     * @param array  $value Allowed values
     */
    protected function _filterPages(&$pages, $field, array $value)
    {
        foreach ($pages as $i => $page)
        {
            if (!in_array($page->$field, $value)) {
                unset($pages[$i]);
            }
        }
    }

    protected function _getFilteredCategories()
    {
        if (self::$_filtered_categories === null)
        {
            $table = $this->getObject('com://admin/docman.database.table.categories');
            $query = $this->getObject('lib:database.query.select');

            // Gather the category slugs of active pages
            $category_ids = array();
            $pages          = $this->_getPages();
            foreach ($pages as $page)
            {
                if (isset($page->query['view']) && $page->query['view'] === 'flat'
                    && isset($page->query['category_children'])
                ) {
                    if (isset($page->query['category'])) {
                        $category_ids = array_merge($category_ids, (array) $page->query['category']);
                    }
                }
            }

            // Get a list of categories and their children
            if ($category_ids)
            {
                $query->columns('r.ancestor_id, r.descendant_id')
                    ->table(array('r' => $table->getBehavior('nestable')->getRelationTable()))
                    ->join(array('tbl' => $table->getName()), 'tbl.docman_category_id = r.ancestor_id')
                    ->where('tbl.docman_category_id IN :id')
                    ->bind(array(
                        'id' => (array)$category_ids
                    ));

                self::$_filtered_categories = $table->getAdapter()->select($query, KDatabase::FETCH_OBJECT_LIST);

            } else self::$_filtered_categories = array();
        }

        return self::$_filtered_categories;
    }

    /**
     * Get a list of categories that are reachable by a list view page
     *
     * @return array
     */
    protected function _getCategories()
    {
        if (self::$_categories === null)
        {
            $table = $this->getObject('com://admin/docman.database.table.categories');
            $query = $this->getObject('lib:database.query.select');

            // Gather the category slugs of active pages
            $category_slugs = array();
            $pages          = $this->_getPages();
            foreach ($pages as $page)
            {
                if (isset($page->query['view']) && in_array($page->query['view'], array('list', 'tree'))  && isset($page->query['slug'])) {
                    $category_slugs[] = $page->query['slug'];
                }
            }

            // Get a list of categories and their children
            if ($category_slugs)
            {
                $query->columns('tbl.slug, r.ancestor_id, r.descendant_id')
                    ->table(array('r' => $table->getBehavior('nestable')->getRelationTable()))
                    ->join(array('tbl' => $table->getName()), 'tbl.docman_category_id = r.ancestor_id')
                    ->where('tbl.slug IN :slug')
                    ->bind(array('slug' => (array)$category_slugs));

                self::$_categories = $table->getAdapter()->select($query, KDatabase::FETCH_OBJECT_LIST);

            } else self::$_categories = array();
        }

        return self::$_categories;
    }

    /**
     * Takes a category slug and returns the IDs of itself and all its children
     *
     * @param string $slug
     *
     * @return array
     */
    protected function _getCategoryChildren($slug)
    {
        $return     = array();
        $categories = $this->_getCategories();

        if (empty($slug)) {
            return $return;
        }

        foreach ($categories as $category)
        {
            if ($category->slug === $slug) {
                $return[] = (int)$category->descendant_id;
            }
        }

        return $return;
    }

    protected function _actionFetch(KModelContext $context)
    {
        $pages = $this->_getPages();
        $state = $this->getState();

        if ($state->view) {
            $this->_filterPagesByView($pages, (array)$state->view);
        }

        if ($state->id) {
            $this->_filterPages($pages, 'id', (array)$state->id);
        }

        if ($state->alias) {
            $this->_filterPages($pages, 'alias', (array)$state->alias);
        }

        foreach ($pages as &$page) {
            $p = get_object_vars($page);
            $p['params'] = $page->params;

            $page = $p;
        }
        unset($page);

        if ($state->sort && count($pages))
        {
            $page = end($pages);
            if (isset($page[$state->sort]))
            {
                $sort      = $state->sort;
                $direction = $state->direction === 'desc' ? 'desc' : 'asc';
                $numeric   = is_numeric($page[$state->sort]);

                usort($pages, function($a, $b) use($numeric, $sort, $direction) {
                    $result = $numeric ? ($a[$sort] - $b[$sort]) : strcasecmp($a[$sort], $b[$sort]);

                    return $direction === 'desc' ? -1 * $result : $result;
                });
            }
        }

        $options = array(
            'data'         => $pages,
            'identity_key' => $context->getIdentityKey()
        );

        $pages = $this->getObject('com://admin/docman.model.entity.pages', $options);

        return $pages;
    }

    protected function _actionCount(KModelContext $context)
    {
        return count($this->_actionFetch($context));
    }
}
