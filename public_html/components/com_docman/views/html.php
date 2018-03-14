<?php
/**
 * @package    DOCman
 * @copyright   Copyright (C) 2011 Timble CVBA (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

class ComDocmanViewHtml extends ComKoowaViewHtml
{
    protected function _initialize(KObjectConfig $config)
    {
        $config->append(array(
            'behaviors'          => array('pageable', 'navigatable'),
            'template_filters'   => array(
                'com://admin/docman.template.filter.asset'
            )
        ));

        parent::_initialize($config);
    }

    protected function _actionRender(KViewContext $context)
    {
        $this->_preparePage();
        $this->_generatePathway();

        return parent::_actionRender($context);
    }

    /**
     * Create a route based on a query string.
     *
     * Automatically adds the menu item ID to links
     *
     * {@inheritdoc}
     */
    public function getRoute($route = '', $fqr = false, $escape = true)
    {
        if (is_string($route)) {
            parse_str(trim($route), $parts);
        } else {
            $parts = $route;
        }

        if (!isset($parts['Itemid'])) {
            $parts['Itemid'] = $this->getActiveMenu()->id;
        }

        // We don't add the category path to the documents view URLs
        $menu_view = $this->getActiveMenu()->query['view'];
        if ($menu_view === 'flat') {
            unset($parts['category_slug']);
        }

        return parent::getRoute($parts, $fqr, $escape);
    }

    /**
     * Returns pathway object
     *
     * @return JPathway
     */
    public function getPathway()
    {
        return JFactory::getApplication()->getPathway();
    }

    protected function _generatePathway($category = null, $document = null)
    {
        $view    = $this->getName();
        $query   = $this->getActiveMenu()->query;
        $pathway = $this->getPathway();
        $link    = null;
        $query_slug = isset($query['slug']) ? $query['slug'] : '';

        // Joomla handles it all when the view is document or submit
        // or when we are on a menu item set to the current category or document
        if (!in_array($view, array('document', 'tree', 'list', 'submit'))
            || ($query['view'] === 'document' && $view === 'document' && $query['slug'] === $document->slug)
            || (in_array($query['view'], array('tree', 'list')) && $view === $query['view']
                    && !empty($category) && $query_slug === (string) $category->slug)
        ) {
            return;
        }

        if (!in_array($query['view'], array('flat')) && $category && $query_slug !== $category->slug)
        {
            $vars = array(
                'option' => 'com_docman',
                'view'   => $query['view'],
            );

            if (isset($query['layout'])) {
                $vars['layout'] = $query['layout'];
            }

            $pass = !empty($query_slug);
            foreach ($category->getAncestors() as $ancestor)
            {
                if (($query['view'] === 'list' || $query['view'] === 'tree') && $query_slug === $ancestor->slug)
                {
                    /*
                     * We reached to the point of menu category
                     * From this point on all categories should be displayed
                     */
                    $pass = false;
                    continue;
                }

                if ($pass) {
                    continue;
                }

                $vars['slug'] = $ancestor->slug;
                $link = 'index.php?'.http_build_query($vars, null, '&');

                $pathway->addItem($ancestor->title, JRoute::_($link));
            }

            // Link the category if the view is category instead of document
            $vars['slug'] = $category->slug;
            $link = in_array($view, array('list', 'tree')) ? '' : JRoute::_('index.php?'.http_build_query($vars, null, '&'));
            $pathway->addItem($category->title, $link);
        }

        if ($document) {
            $pathway->addItem($document->title, $link);
        }
    }

    /**
     * Set page title, add metadata
     */
    protected function _preparePage()
    {
        $document = JFactory::getDocument();
        $params   = $this->getParameters();

        // Set robots
        if ($this->getObject('request')->query->print) {
            $params->robots = 'noindex, nofollow';
        }

        if ($params->robots) {
            $document->setMetadata('robots', $params->robots);
        }

        // Set keywords
        if ($params->{'menu-meta_keywords'}) {
            $document->setMetadata('keywords', $params->{'menu-meta_keywords'});
        }

        // Set description
        if ($params->{'menu-meta_description'}) {
            $document->setDescription($params->{'menu-meta_description'});
        }

        // Set page title
        $this->_setPageTitle();
    }

    /*
     * Sets the page title
     */
    protected function _setPageTitle()
    {
        $app      = JFactory::getApplication();
        $document = JFactory::getDocument();
        $menu     = $this->getActiveMenu();
        $params   = $this->getParameters();

        // Because the application sets a default page title,
        // we need to get it from the menu item itself

        $title = $params->get('page_title', $params->get('page_heading', $menu->title));

        $params->def('page_heading', $params->get('page_heading', $menu->title));

        if (empty($title)) {
            $title = $app->getCfg('sitename');
        }
        elseif ($app->getCfg('sitename_pagetitles', 0) == 1) {
            $title = JText::sprintf('JPAGETITLE', $app->getCfg('sitename'), $title);
        }
        elseif ($app->getCfg('sitename_pagetitles', 0) == 2) {
            $title = JText::sprintf('JPAGETITLE', $title, $app->getCfg('sitename'));
        }

        $document->setTitle($title);
    }
}
