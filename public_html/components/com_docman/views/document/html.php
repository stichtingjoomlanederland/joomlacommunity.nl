<?php
/**
 * @package    DOCman
 * @copyright   Copyright (C) 2011 Timble CVBA (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

class ComDocmanViewDocumentHtml extends ComDocmanViewHtml
{
    protected function _initialize(KObjectConfig $config)
    {
        $config->append([
            'decorator'  => $config->layout === 'form' ? 'koowa' : 'joomla'
        ]);

        parent::_initialize($config);
    }

    protected function _fetchData(KViewContext $context)
    {
        $document = $this->getModel()->fetch();

        if ($this->getLayout() !== 'form')
        {
            $params                       = $this->getParameters();
            $context->data->event_context = 'com_docman.document';

            $this->prepareDocument($document, $params, $context->data->event_context);

            //Settings
            $query = $this->getActiveMenu()->query;
            if ($query['view'] === 'document' && $query['slug'] === $document->slug) {
                $context->data->show_delete = false;
            }
        }
        else
        {
            $this->getObject('translator')->load('com:files');

            $menu = $this->getActiveMenu();

            $view = $menu->query['view'];

            if (!empty($menu->query['own']) && $document->isPermissible() && !$document->canPerform('manage')) {
                $context->data->hide_owner_field = true;
                $context->data->hide_publishing_field = true;
            }

            if (in_array($view, array('list', 'tree')) && isset($menu->query['slug']))
            {
                $category = $this->getObject('com://admin/docman.model.categories')->slug($menu->query['slug'])->fetch();

                if (!$category->isNew()) {
                    $context->data->root_category = $category->id;
                }
            }

            $context->data->tag_count      = $this->getObject('com://admin/docman.model.tags')->count();
            $context->data->can_create_tag = $this->getObject('com://admin/docman.model.configs')->fetch()->can_create_tag;
            $context->data->hide_tag_field = $context->data->tag_count == 0 && !$context->data->can_create_tag;
        }

        parent::_fetchData($context);
    }

    protected function _generatePathway($category = null, $document = null)
    {
        $document = $this->getModel()->fetch();

        if ($this->getLayout() === 'form')
        {
            $translator = $this->getObject('translator');

            if($document->isNew()) {
                $text = $translator->translate('Add document');
            } else {
                $text = $translator->translate('Edit document {title}', array('title' => $document->title));
            }

            $this->getPathway()->addItem($text, '');
        }
        else
        {
            $category = $this->getObject('com://site/docman.model.categories')
                              ->id($document->docman_category_id)
                              ->fetch();

            parent::_generatePathway($category, $document);
        }
    }

    /**
     * If the current page is not to a document menu item, use the current document title
     */
    protected function _setPageTitle()
    {
        if ($this->getName() !== $this->getActiveMenu()->query['view'])
        {
            $document = $this->getModel()->fetch();

            $this->getParameters()->set('page_heading', $document->title);
            $this->getParameters()->set('page_title',   $document->title);
        }

        parent::_setPageTitle();
    }

    /**
     * If the current page is not to a document menu item, set metadata
     */
    protected function _preparePage()
    {
        if ($this->getName() !== $this->getActiveMenu()->query['view'])
        {
            $helper   = $this->getTemplate()->createHelper('string');
            $document = $this->getModel()->fetch();
            $this->getParameters()->{'menu-meta_description'} = $helper->truncate(array(
                'text'   => $document->description,
                'length' => 140
            ));
        }

        parent::_preparePage();
    }
}
