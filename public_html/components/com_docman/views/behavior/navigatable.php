<?php
/**
 * @package    DOCman
 * @copyright   Copyright (C) 2011 Timble CVBA (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

class ComDocmanViewBehaviorNavigatable extends KViewBehaviorAbstract
{
    protected function _beforeRender(KViewContext $context)
    {
        $query  = $this->getActiveMenu()->query;

        if (isset($query['view']) && $query['view'] === 'tree' && $this->getMixer()->getLayout() !== 'form')
        {
            $params  = $this->getParameters();
            $state   = $this->getModel()->getState();
            $request = $this->getObject('request');
            $data    = $this->_getSidebarData($context);


            if (in_array($this->getLayout(), ['default', 'table', 'gallery']) && !$state->isUnique())
            {
                /*
                 * Automatically render the first category for top level tree view
                 * Otherwise the content pane will be empty and the page will only display the sidebar
                 *
                 * If search is enabled no such redirect is necessary, we just hide the top-level categories
                 */
                if ($params->show_document_search || $request->query->has('filter')) {
                    // Search is visible, so content pane is not empty. We hide categories then
                    $params->set('show_subcategories', false);
                }
                else {
                    $model = $this->getObject('com://site/docman.model.categories');
                    $first = $model->setState($data['state'])->limit(1)->fetch();

                    // Ensure there is a category
                    if (!$first->isNew())
                    {
                        $category_link = $this->getTemplate()->createHelper('com://admin/docman.template.helper.route')
                            ->category(array('entity' => $first, 'view' => 'tree'), true, false);

                        $this->getObject('response')->setRedirect($category_link)->send();
                    }

                }
            }
        }
    }

    protected function _afterRender(KViewContext $context)
    {
        $query  = $this->getActiveMenu()->query;

        if (isset($query['view']) && $query['view'] === 'tree' && $this->getMixer()->getLayout() !== 'form')
        {
            $data = $this->_getSidebarData($context);

            $context->result = $this->getTemplate()
                ->loadFile('com://site/docman.tree.sidebar.html')
                ->render($data);
        }
    }

    protected function _getSidebarData(KViewContext $context)
    {
        $params   = $this->getParameters();
        $state    = $this->getModel()->getState();
        $selected = null;

        if ($this->getMixer()->getName() === 'document') {
            $selected = $this->getModel()->fetch()->docman_category_id;
        }
        else {
            $selected = $this->getModel()->fetch()->id;
        }

        $data = array(
            'state' => array(
                'enabled'       => $state->enabled,
                'access'        => $state->access,
                'current_user'  => $this->getObject('user')->getId(),
                'page'          => $state->page,
                'sort'          => $params->sort_categories
            ),
            'selected' => $selected
        );

        return $data;
    }
}