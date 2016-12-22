<?php
/**
 * @package    DOCman
 * @copyright   Copyright (C) 2011 - 2014 Timble CVBA (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

class ComDocmanViewBehaviorNavigatable extends KViewBehaviorAbstract
{
    protected function _afterRender(KViewContext $context)
    {
        $params = $this->getParameters();
        $query  = $this->getActiveMenu()->query;

        if (isset($query['view']) && $query['view'] === 'tree' && $this->getMixer()->getLayout() !== 'form')
        {
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

            /*
             * Automatically render the first category for top level tree view
             *
             * Otherwise the content pane will be empty and the page will only display the sidebar
             * We are putting a canonical URL for the actual category link for SEO purposes
             */
            if (in_array($this->getLayout(), array('default', 'table', 'gallery')) && !$this->getModel()->getState()->isUnique())
            {
                $model         = $this->getObject('com://site/docman.model.categories');
                $first         = $model->setState($data['state'])->limit(1)->fetch();

                $category_link = $this->getTemplate()->createHelper('com://admin/docman.template.helper.route')
                    ->category(array('entity' => $first, 'view' => 'tree'), true, false);

                $this->getObject('response')->setRedirect($category_link)->send();
            }

            $context->result = $this->getTemplate()
                ->loadFile('com://site/docman.tree.sidebar.html')
                ->render($data);
        }
    }
}