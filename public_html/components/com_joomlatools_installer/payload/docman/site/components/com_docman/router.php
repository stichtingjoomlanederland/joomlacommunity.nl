<?php
/**
 * @package    DOCman
 * @copyright   Copyright (C) 2011 Timble CVBA (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

defined('_JEXEC') or die;

/**
 * Docman Router
 */
class ComDocmanRouter
{
    /**
     * @var array A slug to path map for routed categories
     */
    protected static $_category_map = array();

    /**
     * Private constructor to avoid direct instantiation
     */
    private function __construct() {}

    /**
     * Returns an instance of the class
     *
     * @return ComDocmanRouter
     */
    public static function getInstance()
    {
        static $instance;

        if (!$instance) {
            $instance = new ComDocmanRouter();
        }

        return $instance;
    }

    /**
     * Return the category slug of a document
     *
     * @param string $document_slug document slug
     *
     * @return mixed
     */
    public function getCategorySlug($document_slug)
    {
        $db = JFactory::getDBO();
        $db->setQuery(sprintf("
                SELECT c.slug
                FROM `#__docman_documents` AS d
                LEFT JOIN `#__docman_categories` AS c ON c.docman_category_id = d.docman_category_id
                WHERE d.slug = %s
                LIMIT 0, 1",
                $db->quote($document_slug)));
        $path = $db->loadResult();

        return $path;
    }

    /**
     * Returns the full path to a category
     *
     * @param string $slug category slug
     *
     * @return mixed
     */
    public function getCategoryPath($slug)
    {
        if (!array_key_exists($slug, self::$_category_map)) {
            $db = JFactory::getDBO();
            $db->setQuery(sprintf("
                SELECT GROUP_CONCAT(c.slug ORDER BY r.level DESC SEPARATOR '/')
                FROM `#__docman_category_relations` as r
                INNER JOIN `#__docman_categories` AS c2 ON c2.slug = %s
                LEFT JOIN `#__docman_categories` AS c ON c.docman_category_id = r.ancestor_id
                WHERE r.descendant_id = c2.docman_category_id
                ORDER BY r.level DESC",
                $db->quote($slug)));
            $path = $db->loadResult();
            self::$_category_map[$slug] = $path;
        }

        return self::$_category_map[$slug];
    }

    /**
     * Returns the alias (id-slug) for a document
     *
     * @param string $slug document slug
     *
     * @return mixed
     */
    public function getDocumentAlias($slug)
    {
        $db = JFactory::getDBO();
        $db->setQuery(sprintf("
            SELECT CONCAT_WS('-', tbl.docman_document_id, tbl.slug) AS alias
            FROM `#__docman_documents` as tbl
            WHERE tbl.slug = %s", $db->quote($slug)));

        $alias = $db->loadResult();

        return $alias;
    }

    /**
     * Builds a URL from a query object
     *
     * @param array $query query object
     *
     * @return array
     */
    public function build(&$query)
    {
        $segments	= array();
        $itemid		= null;

        if (empty($query['Itemid'])) {
            return $segments;
        }

        $menu = JApplication::getInstance('site')->getMenu()->getItem($query['Itemid']);
        if (!$menu) {
            return $segments;
        }

        $menu_query = $menu->query;

        $menu_view = isset($menu_query['view']) ? $menu_query['view'] : null;
        $menu_layout = isset($menu_query['layout']) ? $menu_query['layout'] : null;

        $query_view = isset($query['view']) ? $query['view'] : null;
        $query_layout = isset($query['layout']) ? $query['layout'] : null;

        // com_files stuff
        if (isset($query['routed']) || in_array($query_view, array('upload', 'files', 'users', 'tags'))) {
            return $segments;
        }

        if ($menu_view === 'document')
        {
            unset($query['slug']);
            unset($query['category_slug']);

            if ($query_layout === 'form' && isset($query['alias']))
            {
                $pieces = explode('-', $query['alias'], 2);
                $query['slug'] = array_pop($pieces);
            }
            unset($query['alias']);
        }
        elseif ($menu_view === 'flat') {
            unset($query['category_slug']);
        }

        $slug = '';

        if (in_array($query_view, array('list', 'tree')))
        {
            // We will calculate the path below so don't need slug anymore
            if (!empty($query['slug'])) {
                $slug = $query['slug'];
            }

            unset($query['slug']);
        }
        elseif ($query_view === 'document' || $query_view === 'download')
        {
            // If slug is set this is an old style link without the alias. Need to convert it.
            if (!empty($query['slug'])) {
                $query['alias'] = $this->getDocumentAlias($query['slug']);
            }

            // Find the category path and use it to build the url
            if (isset($query['category_slug']))
            {
                $slug = $query['category_slug'];
                unset($query['category_slug']);
            }
            elseif (isset($query['slug']) && in_array($menu_view, array('list', 'tree'))) {
                $slug = $this->getCategorySlug($query['slug']);
            }

            if (!empty($query['slug'])) unset($query['slug']);
        }

        if ($slug && $menu_view !== 'document') {
            $path = $this->getCategoryPath($slug);
            self::$_category_map[$slug] = $path;
        }

        // If the menu item also has a category path, we will make our path relative to it
        if (!empty($path))
        {
            $menu_path = null;

            // Calculate the path for the category of the menu item
            if (in_array($menu_view, array('list', 'tree'))) {
                $menu_path = $this->getCategoryPath(isset($menu_query['slug']) ? $menu_query['slug'] : '');
            }

            if (empty($menu_path)) {
                $segments[] = $path;
            } elseif ($path === $menu_path) {
                // do nothing
            }
            elseif (strpos($path, $menu_path) === 0)
            {
                $relative = substr($path, strlen($menu_path)+1, strlen($path));

                $segments[] = $relative;
            }
        }

        if (in_array($query_view, array('document', 'download')))
        {
            if ($menu_view !== 'document' && isset($query['alias'])) {
                $segments[] = $query['alias'];
                unset($query['alias']);
            }

            if ($query_layout === 'form') {
                $query['view'] = 'document';
                $query['layout'] = 'form';
                //unset($query['layout']);
            }
        }

        if ($query_view === 'download') {
            $segments[] = 'file';
        }

        if ($menu_view === $query_view || !isset($query['layout']) || $query['layout'] !== 'form') {
            unset($query['view']);
        }

        if ($query_layout === 'default' || $query_layout === $menu_layout) {
            unset($query['layout']);
        }

        if (isset($query['slug'])) {
            if ($menu_view !== 'document') {
                $segments[] = $query['slug'];
            }

            // Special case:
            // menu.html?view=document&layout=form goes to slug=whatevermenuispointingat instead of new document form
            if ($query_layout !== 'form') {
                unset($query['slug']);
            }
        }

        return $segments;
    }

    /**
     * Checks the last two segments and tries to find a document
     *
     * @param  array       $segments
     * @return null|string
     */
    protected function _findDocument($segments)
    {
        $result = null;

        foreach ($segments as $segment)
        {
            if (preg_match('#[0-9]+\-(.*?)#i', $segment) && $this->_isDocumentAlias($segment)) {
                $result = $segment;
            }
        }

        return $result;
    }

    /**
     * Returns true if the given string is a document alias
     *
     * @param string $alias document alias
     *
     * @return bool
     */
    protected function _isDocumentAlias($alias)
    {
        list($id, $slug) = explode('-', $alias, 2);
        $query = 'SELECT COUNT(*) FROM #__docman_documents'
            . ' WHERE docman_document_id = %d AND slug = %s';

        $db = JFactory::getDBO();
        $db->setQuery(sprintf($query, $id, $db->quote($slug)));

        return (bool) $db->loadResult();
    }

    /**
     * Parse the segments into query string
     *
     * @param array $segments
     *
     * @return array
     */
    public function parse($segments)
    {
        // Circumvent Joomla's auto encoding
        foreach ($segments as &$segment)
        {
            $segment = urldecode($segment);
            $pos = strpos($segment, ':');
            if ($pos !== false) {
                $segment[$pos] = '-';
            }
        }

        $vars      = array();
        $menu      = JFactory::getApplication()->getMenu()->getActive();
        $view      = JFactory::getApplication()->input->getCmd('view', null);
        $document  = $this->_findDocument(array_slice(array_reverse($segments), 0, 2));

        if ($document)
        {
            $position = array_search($document, $segments);

            if (count($segments) > $position+1 && $segments[$position+1] === 'file') {
                $view = 'download';
                array_pop($segments);
            } else {
                $view = 'document';
            }

            unset($segments[$position]);
        }

        // menu view is document so there is only download in the segments
        if ($segments === array('file')) {
            $view = 'download';
        }

        if ($view === 'document' || $view === 'download')
        {
            $vars['view'] = $view;

            if (isset($menu->query['view']) && $menu->query['view'] === 'document') {
                $vars['slug'] = $menu->query['slug'];
            }
            elseif ($document)
            {
                $pieces = explode('-', $document, 2);
                $vars['slug'] = array_pop($pieces);

            } else {
                $vars['slug'] = '';
            }

            $vars['path'] = '';
            if (isset($menu->query['path'])) {
                $vars['path'] .= $menu->query['path'].'/';
            }

            $vars['path'] .= implode('/', $segments);
        }
        elseif ($view === 'flat' && count($segments)) {
            // If we are here, the segment was checked if it's document slug and it wasn't, so this is gonna generate a 404
            $vars['view'] = 'document';
            $vars['slug'] = array_pop($segments);
        }
        else // list view
        {
            $vars['view'] = $menu->query['view'];
            $vars['slug'] = array_pop($segments);
            if (strpos('/', $vars['slug']) !== false)
            {
                $pieces = explode('/', $vars['slug']);
                $vars['slug'] = array_pop($pieces);
            }

            if (isset($menu->query['layout'])) {
                $vars['layout'] = $menu->query['layout'];
            }
        }

        return $vars;
    }
}

/**
 * Hooks up Docman router to Joomla URL build event
 *
 * @param array $query
 *
 * @return array
 */
function DocmanBuildRoute(&$query)
{
    return ComDocmanRouter::getInstance()->build($query);
}

/**
 * Hooks up Docman router to Joomla URL parse event
 *
 * @param array $segments
 *
 * @return array
 */
function DocmanParseRoute($segments)
{
    return ComDocmanRouter::getInstance()->parse($segments);
}
