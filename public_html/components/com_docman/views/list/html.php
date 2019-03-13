<?php
/**
 * @package    DOCman
 * @copyright   Copyright (C) 2011 Timble CVBA (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

class ComDocmanViewListHtml extends ComDocmanViewHtml
{
    protected function _initialize(KObjectConfig $config)
    {
        $config->append(array(
            'auto_fetch' => false
        ));

        parent::_initialize($config);
    }

    public function isCollection()
    {
        return true;
    }

    protected function _fetchData(KViewContext $context)
    {
        $context->data->append(array(
            'event_context' => 'com_docman.list'
        ));

        $state  = $this->getModel()->getState();
        $params = $this->getParameters();
        $user   = $this->getObject('user');

        //Category
        if ($this->getModel()->getState()->isUnique()) {
            $category = $this->getModel()->fetch();
        }
        else
        {
            $category = $this->getModel()->create();
            $category->title = $params->page_heading ? $params->page_heading : $this->getActiveMenu()->title;
        }

        if ($state->isUnique() && $category->isNew()) {
            throw new KControllerExceptionResourceNotFound('Category not found');
        }

        //Subcategories
        if ($params->show_subcategories)
        {
            $subcategories = $this->getObject('com://site/docman.model.categories')
                ->level(1)
                ->parent_id($category->id)
                ->enabled($state->enabled)
                ->access($state->access)
                ->current_user($user->getId())
                ->page($state->page)
                ->sort($params->sort_categories)
                ->direction($params->direction_categories ?: 'asc')
                ->limit(0)
                ->fetch();
        }
        else $subcategories = array();

        $filter = $this->getObject('lib:http.message.parameters', array('parameters' => $this->getObject('request')->query->filter ?: array()));
        $context->data->filter = $filter;

        $has_filter = false;
        foreach ($filter->toArray() as $key => $value) {
            if (!empty($value)) {
                $has_filter = true;
                break;
            }
        }

        //Documents
        if ($category->id || $has_filter)
        {
            $document_category = $has_filter ? $filter->category : $category->id;
            $document_category_children = $filter->category ? true : false;
            $search_contents = $filter->search_contents === null ? true : $filter->search_contents;

            // Needs to come from request as category model does not have a status state
            $status = $this->getObject('request')->query->status;

            $model = $this->getObject('com://site/docman.controller.document')
                ->enabled($state->enabled)
                ->status($status)
                ->access($state->access)
                ->current_user($user->getId())
                ->page($state->page)
                ->limit($state->limit)
                ->offset($state->offset)
                ->sort($state->sort)
                ->direction($state->direction)

                ->created_by($filter->created_by)
                ->created_on_from($filter->created_on_from)
                ->created_on_to($filter->created_on_to)
                ->search($filter->search)
                ->search_by($params->search_by)
                ->search_contents($search_contents)
                ->tag($filter->tag)
                ->category($document_category)
                ->category_children($document_category_children)

                ->getModel();

            $total     = $model->count();
            $documents = $model->fetch();

            foreach ($documents as $document) {
                $this->prepareDocument($document, $params, $context->data->event_context);
            }
        }
        else
        {
            $total     = 0;
            $documents = array();
        }

        $context->data->category        = $category;
        $context->data->documents       = $documents;
        $context->data->total           = $total;
        $context->data->subcategories   = $subcategories;

        parent::_fetchData($context);

        $context->parameters->total   = $total;

        $this->_setSearchFilterData($context);
    }

    protected function _setSearchFilterData(KViewContext $context)
    {
        $menu     = $this->getActiveMenu();
        $category = $this->getModel()->fetch();
        $filter   = $context->data->filter;
        $owner    = !empty($menu->query['created_by']) ? $menu->query['created_by'] : null;
        $tags     = !empty($menu->query['tag']) ? $menu->query['tag'] : array();

        if (!empty($menu->query['own']) || empty($owner) || count($owner) <= 1) {
            $menu->params->set('show_owner_filter', false);
        }

        if (count($tags) === 1) {
            $menu->params->set('show_tag_filter', false);
            $menu->params->set('show_document_tags', false);
        }

        if (!$this->getObject('com://admin/docman.model.entity.config')->connectAvailable()) {
            $menu->params->set('show_content_filter', false);
        }
        elseif ($filter->search_contents === null) {
            $filter->search_contents = true;
        }

        // pre-select the current category if possible
        if (empty($filter->category) && $category->id && (empty($menu->query['slug']) || $menu->query['slug'] != $category->slug)) {
            $filter->category = array($category->id);
        }

        // Toggle the filters at all times in menu item root
        if (!$category->id || (!empty($menu->query['slug']) && $menu->query['slug'] === $category->slug)) {
            $context->data->filter_toggled = true;
        }
        else {
            $context->data->filter_toggled = !empty($filter->search)
                || (!empty($filter->category) && $filter->category != array($category->id))
                || (!empty($filter->tag) && $filter->tag != $tags)
                || (!empty($filter->created_by) && $filter->created_by != $owner);
        }

        $context->data->filter_group = 'filter';

        $status = $this->getObject('request')->query->status;
        $enabled = $this->getModel()->getState()->enabled;

        $context->data->category_filter = array(
            'page'         => $this->getModel()->getState()->page,
            'access'       => $this->getObject('user')->getRoles(),
            'current_user' => $this->getObject('user')->getId(),
            'enabled'      => true
        );

        $context->data->tag_model  = $this->getObject('com://admin/docman.model.pagetags');
        $context->data->tag_filter = [
            'page'         => $this->getModel()->getState()->page,
            'access'       => $this->getObject('user')->getRoles(),
            'current_user' => $this->getObject('user')->getId(),
            'enabled'      => $enabled,
            'status'       => $status
        ];

        if (!$context->data->tag_model->setState($context->data->tag_filter->toArray())->count()) {
            $menu->params->set('show_tag_filter', false);
        }
    }

    protected function _generatePathway($category = null, $document = null)
    {
        $category = $this->getModel()->fetch();

        parent::_generatePathway(($category->id ? $category : null));
    }

    /**
     * If the current page is not the menu category, use the current category title
     */
    protected function _setPageTitle()
    {
        if ($this->getName() === $this->getActiveMenu()->query['view'])
        {
            $category = $this->getModel()->fetch();
            $slug     = isset($this->getActiveMenu()->query['slug']) ? $this->getActiveMenu()->query['slug'] : null;

            if (!$category->isNew() && $category->slug !== $slug) {
                $this->getParameters()->def('page_heading', $category->title);
                $this->getParameters()->def('page_title',   $category->title);
            }

            if ($category->isNew() && $this->getParameters()->show_page_heading) {
                $this->getParameters()->show_category_title = false;
            }
        }

        parent::_setPageTitle();
    }
    /**
     * If the current page is not to a category menu item, set metadata
     */
    protected function _preparePage()
    {
        if ($this->getName() === $this->getActiveMenu()->query['view']) {
            $category = $this->getModel()->fetch();
            $slug     = isset($this->getActiveMenu()->query['slug']) ? $this->getActiveMenu()->query['slug'] : null;

            if ($category->slug !== $slug)
            {
                $helper   = $this->getTemplate()->createHelper('string');
                $this->getParameters()->{'menu-meta_description'} = $helper->truncate(array(
                    'text'   => $category->description,
                    'length' => 140
                ));
            }
        }

        parent::_preparePage();
    }
}
