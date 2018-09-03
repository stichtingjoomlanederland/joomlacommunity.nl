<?php
/**
 * @package    DOCman
 * @copyright   Copyright (C) 2011 Timble CVBA (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

class ComDocmanViewFlatHtml extends ComDocmanViewHtml
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
        $params = $this->getParameters();

        $context->data->event_context = 'com_docman.documents';
        $context->data->documents = $this->getModel()->fetch();
        $context->data->total     = $this->getModel()->count();

        foreach ($context->data->documents as $document) {
            $this->prepareDocument($document, $params, $context->data->event_context);
        }

        parent::_fetchData($context);

        $context->parameters->total = $this->getModel()->count();
        
        $this->_setSearchFilterData($context);
    }

    protected function _setSearchFilterData(KViewContext $context)
    {
        $context->data->filter = $this->getModel()->getState();

        $menu       = $this->getActiveMenu();
        $filter     = $context->data->filter;
        $owner      = !empty($menu->query['created_by']) ? $menu->query['created_by'] : null;
        $tags       = !empty($menu->query['tag']) ? $menu->query['tag'] : array();
        $categories = !empty($menu->query['category']) ? $menu->query['category'] : array();
        $children   = isset($menu->query['category_children']) ? $menu->query['category_children'] : true;

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

        $category_filter = array(
            'page'         => $this->getModel()->getState()->page,
            'access'       => $this->getObject('user')->getRoles(),
            'current_user' => $this->getObject('user')->getId(),
            'enabled'      => true
        );

        if ($categories)
        {
            if ($children) {
                $category_filter['parent_id'] = $categories;
                $category_filter['include_self'] = true;
            } else {
                $category_filter['id'] = $categories;
            }
        }

        $context->data->filter_toggled = ($context->parameters->total > $this->getModel()->getState()->limit)
        || (!empty($filter->search)
            || (!empty($filter->category) && $filter->category != $categories)
            || (!empty($filter->tag) && $filter->tag != $tags)
            || (!empty($filter->created_by) && $filter->created_by != $owner));

        $context->data->category_filter = $category_filter;

        $context->data->tag_model  = $this->getObject('com://admin/docman.model.pagetags');
        $context->data->tag_filter = [
            'page'         => $this->getModel()->getState()->page,
            'access'       => $this->getObject('user')->getRoles(),
            'current_user' => $this->getObject('user')->getId(),
            'enabled'      => $this->getModel()->getState()->enabled,
            'status'       => $this->getModel()->getState()->status
        ];

        if (!$context->data->tag_model->setState($context->data->tag_filter->toArray())->count()) {
            $menu->params->set('show_tag_filter', false);
        }
    }
}
