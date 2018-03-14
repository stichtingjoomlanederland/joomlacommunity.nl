<?php
/**
 * @package    DOCman
 * @copyright   Copyright (C) 2011 Timble CVBA (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

class ModDocman_documentsHtml extends ModKoowaHtml
{
    protected function _initialize(KObjectConfig $config)
    {
        $config->append(array(
            'auto_fetch'       => false,
            'model'            => 'com://site/docman.model.documents',
            'behaviors'        => array(
                'com://site/docman.view.behavior.pageable',
            ),
            'template_filters' => array(
                'com://admin/docman.template.filter.asset'
            )
        ));

        parent::_initialize($config);
    }

    /**
     * Load the controller translations
     *
     * @param KViewContext $context
     * @return void
     */
    protected function _loadTranslations(KViewContext $context)
    {
        parent::_loadTranslations($context);

        $this->getObject('translator')->load('com://site/docman');
    }

    /**
     * Sets the model state using module parameters
     *
     * @param KModelInterface $model
     * @return $this
     */
    protected function _setModelState(KModelInterface $model)
    {
        $params = $this->getParameters();
        $user   = $this->getObject('user');

        // Set all parameters in the state to allow easy extension of the module
        $state = $params->toArray();
        $state['category_children'] = $params->include_child_categories;
        $state['page'] = !empty($params->page) ? $params->page : 'all';

        if (substr($params->sort, 0, 8) === 'reverse_')
        {
            $state['sort'] = substr($params->sort, 8);
            $state['direction'] = 'desc';
        }

        // Force created_by for user module
        if ($params->own) {
            $state['created_by'] = $user->getId();
        }

        $model->setState($state);

        // Force certain states
        $model->access($user->getRoles())
            ->enabled(1)
            ->status('published')
            ->current_user($user->getId());

        return $this;
    }

    protected function _fetchData(KViewContext $context)
    {
        parent::_fetchData($context);

        $model = $this->getModel();

        $this->_setModelState($model);

        $documents = $model->fetch();

        $this->_prepareDocuments($documents, $this->getParameters());

        $context->data->documents = $documents;
        $context->parameters->total = $model->count();
    }

    /**
     * Return the views output
     *
     * @param KViewContext	$context A view context object
     * @return string  The output of the view
     */
    protected function _actionRender(KViewContext $context)
    {
        $params    = $this->getParameters();
        $pages     = $this->getObject('com://site/docman.model.pages')->fetch();

        // Only render if there is a menu item to DOCman AND we have documents or displaying a user's own documents
        if (count($pages) && (count($context->data->documents) || ($params->own && $this->getObject('user')->getId()))) {
            return parent::_actionRender($context);
        }

        return '';
    }

    /**
     * Sets the layout from the parameters
     *
     * @param KViewContext $context
     */
    protected function _beforeRender(KViewContext $context)
    {
        $params = $this->getParameters();

        if ($params->layout)
        {
            $this->setLayout($params->layout);
            $context->layout = $this->getLayout();
        }
    }

    /**
     * Set properties such as download and category links
     * @param $documents
     * @param $params
     */
    protected function _prepareDocuments($documents, $params)
    {
        $pages  = $this->getObject('com://site/docman.model.pages')->fetch();
        $helper = $this->getTemplate()->createHelper('com://admin/docman.template.helper.route');

        foreach ($documents as $document)
        {
            $document->document_link = $helper->document(array('entity'=> $document, 'Itemid' => $document->itemid, 'layout' => 'default'));
            $document->download_link = $helper->document(array('entity'=> $document, 'Itemid' => $document->itemid, 'view'   => 'download'));

            $document->title_link = $params->link_to_download ? $document->download_link : $document->document_link;

            if ($params->show_category)
            {
                $current = $pages->find($document->itemid);

                if (!empty($current)
                    && (isset($current->query['view']) && in_array($current->query['view'], array('tree', 'list'))))
                {
                    $document->category_link = $helper->category(array(
                        'entity' => array('slug' => $document->category_slug),
                        'Itemid' => $document->itemid
                    ));
                }
            }
        }
    }

    /**
     * Sets the parameters in the pageable behavior too
     *
     * {@inheritdoc}
     */
    public function set($property, $value)
    {
        parent::set($property, $value);

        if ($property == 'module' && isset($this->module->params)) {
            $this->setParameters($this->module->params);
        }
    }
}
