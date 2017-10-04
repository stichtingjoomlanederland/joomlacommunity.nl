<?php
/**
* @package    DOCman
* @copyright   Copyright (C) 2011 Timble CVBA (http://www.timble.net)
* @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
* @link        http://www.joomlatools.com
*/

class ComDocmanTemplateHelperRoute extends KTemplateHelperAbstract
{
    /**
     * A function to return routed strings
     *
     * @var callable
     */
    protected $_router;

    public function __construct(KObjectConfig $config)
    {
        parent::__construct($config);

        $this->setRouter(array($this, '_route'));
    }
    /**
     * Create a route for a row
     *
     * @param array|KObjectConfig $config
     * @param bool                $fqr
     * @param bool                $escape
     * @return string Routed URL
     */
    public function entity($config = array(), $fqr = false, $escape = true)
    {
        $config = new KObjectConfigJson($config);

        $entity = $config->entity;

        $query = array(
            'view' => $entity->getIdentifier()->name,
            'slug' => $entity->slug
        );

        $config->append($query);

        return $this->_getRoute($config, $fqr, $escape);
    }

    /**
     * Create a route for a category
     *
     * @param array|KObjectConfig $config
     * @param bool                $fqr
     * @param bool                $escape
     * @return string Routed URL
     */
    public function category($config = array(), $fqr = false, $escape = true)
    {
        $config = new KObjectConfigJson($config);
        $config->append(array(
            'admin_link' => JFactory::getApplication()->isAdmin()
        ));

        // Return a generic link for administrator
        if ($config->admin_link) {
            return $this->entity($config, $fqr, $escape);
        }

        unset($config->admin_link);

        // Append the correct view based on the menu item
        if (!empty($config->Itemid) && empty($config->view)) {
            $menu = JFactory::getApplication()->getMenu()->getItem($config->Itemid);

            if ($menu && !empty($menu->query) && !empty($menu->query['view'])) {
                $config->append(['view' => $menu->query['view']]);
            }
        }


        $query = array(
            'option' => 'com_docman',
            'slug' => $config->entity->slug,
            //'view' => 'list', provided by the menu item
        );

        $config->append($query);

        return $this->_getRoute($config, $fqr, $escape);
    }

    /**
     * Create a route for a document
     *
     * @param array|KObjectConfig $config
     * @param bool                $fqr
     * @param bool                $escape
     * @return string Routed URL
     */
    public function document($config = array(), $fqr = false, $escape = true)
    {
        $config = new KObjectConfigJson($config);
        $config->append(array(
            'admin_link' => JFactory::getApplication()->isAdmin()
        ));

        // Return a generic link for administrator
        if ($config->admin_link) {
            return $this->entity($config);
        }

        unset($config->admin_link);

        $document = $config->entity;

        $query = array(
            'option' => 'com_docman',
            'view' => 'document',
            'alias' => $document->alias
        );

        if ($document->category_slug) {
            $query['category_slug'] = $document->category_slug;
        }

        $config->append($query);

        $route = $this->_getRoute($config, $fqr, $escape);
        $query = $route->getQuery(true);

        if (isset($query['view']) && $query['view'] === 'download') {
            unset($query['format']);
            $route->setQuery($query);
        }

        return $route;
    }

    /**
     * Convert an array to a URL
     *
     * @param KObjectConfig $config
     * @param bool          $fqr
     * @param bool          $escape
     * @return string Routed URL
     */
    protected function _getRoute(KObjectConfig $config, $fqr = false, $escape = true)
    {
        unset($config->entity);

        return call_user_func_array($this->_router, array($config->toArray(), $fqr, $escape));
    }

    /**
     * Default router
     *
     * @param $config
     * @param $fqr
     * @param $escape
     * @return mixed
     */
    protected function _route($config, $fqr, $escape)
    {
        return $this->getTemplate()->route($config, $fqr, $escape);
    }

    /**
     * Sets a router
     *
     * @param $callable
     * @return $this
     * @throws InvalidArgumentException
     */
    public function setRouter($callable)
    {
        if (!is_callable($callable)) {
            throw new InvalidArgumentException('Router should be callable');
        }

        $this->_router = $callable;

        return $this;
    }
}