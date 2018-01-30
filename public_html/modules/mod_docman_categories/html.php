<?php
/**
 * @package    DOCman
 * @copyright   Copyright (C) 2011 Timble CVBA (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

class ModDocman_categoriesHtml extends ModKoowaHtml
{
    protected function _initialize(KObjectConfig $config)
    {
        $config->append(array(
            'auto_fetch'       => false,
            'model'            => 'com://site/docman.model.categories',
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

        $parent_id = $params->parent;

        // Active DOCman page
        if ($params->page == -1)
        {
            $menu = JFactory::getApplication()->getMenu()->getActive();
            $params->page = $menu ? $menu->id : 0;

            // Get the current category if available
            $slug = $this->getObject('request')->query->slug;

            if ($slug)
            {
                $parent_id = $model->slug($slug)->fetch()->id;
                $model->getState()->reset();
            }
        }

        // Set all parameters in the state to allow easy extension of the module
        $state = $params->toArray();
        $state['parent_id'] = $parent_id;
        $state['page'] = !empty($params->page) ? $params->page : 'all';

        if (substr($params->sort, 0, 8) === 'reverse_')
        {
            $state['sort'] = substr($params->sort, 8);
            $state['direction'] = 'desc';
        }

        if ($params->level) {
            $state['level'] = $params->level == 1 ? 1 : range(0, $params->level);
        }

        // Force created_by for user module
        if ($params->own) {
            $state['created_by'] = $user->getId();
        }

        $model->setState($state);

        // Force certain states
        $model->access($user->getRoles())
            ->enabled(1)
            ->current_user($user->getId());

        return $this;
    }

    protected function _fetchData(KViewContext $context)
    {
        parent::_fetchData($context);

        $model = $this->getModel();

        $this->_setModelState($model);

        $categories = $model->fetch();

        $this->_prepareCategories($categories, $this->getParameters());

        $context->data->previous_level = 0;
        $context->data->categories  = $categories;
        $context->parameters->total = $categories->count();

        if (count($context->data->categories)) {
            $context->data->categories = new CachingIterator($categories->getIterator(), CachingIterator::TOSTRING_USE_KEY);
        }
    }

    /**
     * Return the views output
     *
     * @param KViewContext	$context A view context object
     * @return string  The output of the view
     */
    protected function _actionRender(KViewContext $context)
    {
        $pages = $this->getObject('com://site/docman.model.pages')->fetch();

        // Only render if there is a menu item to DOCman AND we have categories to display
        if (count($pages) && $context->parameters->total) {
            return parent::_actionRender($context);
        }

        return '';
    }

    /**
     * Sets the layout from the parameters
     *
     * Stops rendering if the page is set to active and the active menu is not a list view
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

        if ($this->getParameters()->page == -1)
        {
            $menu = JFactory::getApplication()->getMenu()->getActive();

            if ($menu && is_array($menu->query) && isset($menu->query['view']) && !in_array($menu->query['view'], ['list', 'tree'])) {
                return false;
            }
        }
    }

    /**
     * Set properties such as category links
     *
     * @param $categories
     * @param $params
     */
    protected function _prepareCategories($categories, $params)
    {
        $helper  = $this->getTemplate()->createHelper('com://admin/docman.template.helper.route');
        $parents = array();

        // Pre-include the selected parent category to relax the parentship check to include owned sub-nodes.
        if ($parent_id = $params->parent) {
            $parents[] = $parent_id;
        }

        foreach ($categories as $category)
        {
            // Only show the category if its parent is visible in owner view
            if ($params->own)
            {
                $parent_id  = $category->getParentId();

                if ($parent_id && !in_array($parent_id, $parents)) {
                    $categories->remove($category);

                    continue;
                }

                $parents[] = $category->id;

            }

            $category->link = $helper->category(array(
                'entity' => $category,
                'Itemid' => $category->itemid
            ));
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