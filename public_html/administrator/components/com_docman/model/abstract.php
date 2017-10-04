<?php
/**
 * @package    DOCman
 * @copyright   Copyright (C) 2011 Timble CVBA (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

abstract class ComDocmanModelAbstract extends KModelDatabase
{
    /**
     * @var ComDocmanModelPages
     */
    protected static $_pages_model;

    /**
     * A key/value cache of different page sets
     *
     * @var array
     */
    protected static $_page_cache = array();

    protected static $_level_cache  = array();

    protected $_page_filters = array();

    const PAGE_FILTER_NO_RESULTS = -1;

    const PAGE_FILTER_ALL_DOCUMENTS = 1;
    const PAGE_FILTER_ALL_CATEGORIES = 2;
    const PAGE_FILTER_ALL_ENTITIES = 3;

    /**
     * Constructor
     *
     * @param KObjectConfig $config Configuration options
     */
    public function __construct(KObjectConfig $config)
    {
        parent::__construct($config);

        $this->getState()
            ->insert('page', 'raw')
            ->insert('current_user', 'int')
        ;
    }

    public function getGroupLevels($groups)
    {
        $result = array();
        $groups = (array) $groups;

        if ($groups)
        {
            $table = $this->getObject('com://admin/docman.database.table.levels');
            $query = $this->getObject('database.query.select')
                          ->columns('docman_level_id')
                          ->table('docman_levels');

            foreach ($groups as $i => $group) {
                $query->where("FIND_IN_SET(:group$i, groups)", 'OR')->bind(array('group'.$i => $group));
            }

            $result = array_map(function($i) { return -1*$i; }, $table->select($query, KDatabase::FETCH_FIELD_LIST));
        }

        return $result;
    }

    public function getUserLevels($user_id)
    {
        if (!isset(static::$_level_cache[$user_id]))
        {
            $groups = JUser::getInstance($user_id)->getAuthorisedGroups();
            $result = $this->getGroupLevels($groups);

            static::$_level_cache[$user_id] = $result;
        }

        return static::$_level_cache[$user_id];
    }

    public function getPagesModel()
    {
        if (!self::$_pages_model) {
            self::$_pages_model = $this->getObject('com://admin/docman.model.pages');

            if (JFactory::getApplication()->isAdmin()) {
                self::$_pages_model->access(-1);
            }

            $view = $this->getObject('request')->query->view;
            if (JFactory::getApplication()->isAdmin() || $view === 'doclink' || $view === 'documents') {
                self::$_pages_model->language('all');
            }
        }

        return self::$_pages_model;
    }

    /**
     * Returns all component pages
     *
     * @return array
     */
    protected function _getPages()
    {
        if (!isset(self::$_page_cache['all']))
        {
            self::$_page_cache['all'] = $this->getPagesModel()->reset()
                    ->view(array('list', 'flat', 'document', 'tree'))->fetch();
        }

        return self::$_page_cache['all'];
    }

    /**
     * Returns a list of page IDs in the order they are preferred so that generic menu items come after
     * specific ones.
     *
     * For example, for documents, document menu item wins over list menu item.
     *
     * @param $type string document or category
     * @return array
     */
    protected function _getPreferredPageList($type)
    {
        $state   = $this->getState();
        $model   = $this->getPagesModel();
        $user_id = $this->getObject('user')->getId();

        $model->reset()->view(array('document', 'flat', 'search', 'list', 'tree'));

        if (!empty($state->page)) {
            $model->id((array) $state->page);
        }

        $pages = $model->fetch();

        $results = array(
            'document' => array(),
            'category' => array(),
            'flat' => array(),
            'list' => array(),
            'own_category' => array(),
            'own_flat' => array(),
            'own_list' => array()
        );

        foreach ($pages as $page)
        {
            $view = $page->query['view'];
            $own  = !empty($page->query['own']);

            $where = null;

            switch ($view) {
                case 'document':
                    if ($type === 'document') {
                        $where = 'document';
                    }
                    break;

                case 'list':
                case 'tree':
                    if (!empty($page->query['slug'])) {
                        $where = $own ? ($user_id ? 'own_category' : false) : 'category';
                    }
                    else $where = $own ? ($user_id ? 'own_list' : false) : 'list';
                    break;

                case 'flat':
                    if ($type === 'document') {
                        $where = $own ? ($user_id ? 'own_flat' : false) : 'flat';
                    }
                    break;
            }

            if ($where) {
                $results[$where][] = $page->id;
            }
        }

        return array_merge(
            $results['document'],
            $results['category'],
            $results['flat'],
            $results['list'],
            $results['own_category'],
            $results['own_flat'],
            $results['own_list']
        );
    }

    /**
     * Page filters getter.
     *
     * @param $pages int|array Page IDs
     *
     * @return array An array containing page conditions.
     */
    protected function _getPageFilters($pages)
    {
        $pages      = (array) KObjectConfig::unbox($pages);
        $conditions = array();

        $all_pages  = $this->_getPages();

        if (empty($pages) || !array_intersect($pages, array_keys($all_pages->toArray()))) {
            return static::PAGE_FILTER_NO_RESULTS;
        }

        foreach ($pages as $id)
        {
            $page = $all_pages->find($id);

            if (empty($page)) {
                continue;
            }

            if ($page->query['view'] === 'list' || $page->query['view'] === 'tree')
            {
                $created_by = isset($page->query['created_by']) ? $page->query['created_by'] : array();

                if (!empty($page->query['own'])) {
                    $created_by = $this->getObject('user')->getId();
                }

                // If we have a category view to the root category everything is reachable
                if (empty($created_by) && $created_by !== 0 && $created_by !== '0'
                    && empty($page->query['slug']) && empty($page->query['tag']))
                {
                    $conditions[$page->id] = static::PAGE_FILTER_ALL_DOCUMENTS | static::PAGE_FILTER_ALL_CATEGORIES;
                    continue;
                }

                $conditions[$page->id] = array(
                    'categories' => $page->children,
                    'created_by' => $created_by,
                    'tags'       => (array) (!empty($page->query['tag']) ? $page->query['tag'] : array()),
                );
            }

            if ($page->query['view'] === 'flat')
            {
                $created_by = isset($page->query['created_by']) ? $page->query['created_by'] : array();
                $category   = (array) (isset($page->query['category']) ? $page->query['category'] : array());

                if (!empty($page->query['own'])) {
                    $created_by = $this->getObject('user')->getId();
                }

                if (empty($created_by) && $created_by !== 0 && $created_by !== '0'
                    && empty($page->query['category']) && empty($page->query['tag']))
                {
                    $conditions[$page->id] = static::PAGE_FILTER_ALL_DOCUMENTS | static::PAGE_FILTER_ALL_CATEGORIES;
                    continue;
                }

                $conditions[$page->id] = array(
                    'categories' => array_merge($category, $page->children),
                    'created_by' => $created_by,
                    'tags'       => (array) (!empty($page->query['tag']) ? $page->query['tag'] : array()),
                );
            }

            if ($this->getIdentifier()->getName() === 'documents' && $page->query['view'] === 'document')
            {
                $conditions[$page->id] = array(
                    'slug' => $page->query['slug']
                );
            }

        }

        return $conditions;
    }

    /**
     * Get a list of items which represents a table rowset
     *
     * Overridden to add itemid to the returned results
     *
     * @param KModelContext $context
     * @return KModelEntityInterface
     */
    protected function _actionFetch(KModelContext $context)
    {
        $entities = parent::_actionFetch($context);

        if (count($entities) && $this->getState()->page)
        {
            $page = $this->getState()->page;

            if (is_scalar($page) || (is_array($page) && count($page) === 1))
            {
                $page = is_scalar($page) ? $page : $page[0];

                foreach ($entities as $entity) {
                    $entity->itemid = $page;
                }
            }
            elseif (method_exists($this, 'setPage')) {
                $this->setPage($entities);
            }
        }

        return $entities;
    }

    protected function _afterReset(KModelContextInterface $context)
    {
        $modified = (array) KObjectConfig::unbox($context->modified);

        if (empty($modified)) {
            $this->_page_filters = array();
        }

        if (in_array('page', $modified))
        {
            $state = $this->getState();

            if ($state->page === 'all') {
                $state->page = array_keys($this->_getPages()->toArray());
            }

            $this->_page_filters = $state->page ? $this->_getPageFilters($state->page) : array();
        }
    }
}
