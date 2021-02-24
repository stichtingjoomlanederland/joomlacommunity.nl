<?php
/**
 * @package    DOCman
 * @copyright   Copyright (C) 2011 Timble CVBA (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

class ComDocmanViewUploadHtml extends ComDocmanViewHtml
{
    protected function _initialize(KObjectConfig $config)
    {
        $config->append(array(
            'layout'     => 'default',
            'auto_fetch' => false,
            'decorator'  => 'koowa'
        ));

        if (!JFactory::getApplication()->isAdmin()) {
            $config->append(array(
                'behaviors'  => array('com://site/docman.view.behavior.pageable'),
            ));
        }

        parent::_initialize($config);
    }

    protected function _fetchData(KViewContext $context)
    {
        // Load administrator language file for messages
        $this->getObject('translator')->load('com://admin/docman');
        $this->getObject('translator')->load('com:files');

        $context->data->admin             = JFactory::getApplication()->isAdmin();
        $context->data->show_uploader     = ! KObjectConfig::unbox($context->data->paths);

        if (!$context->data->selected_category) {
            $context->data->selected_category = null;
        }

        if ($context->data->folder)
        {
            $category = $this->getObject('com://admin/docman.model.categories')->folder($context->data->folder)->fetch();

            if (count($category) === 1 && !$category->isNew()) {
                $context->data->selected_category = $category->id;
            }
        }

        $category_filter = array();

        if (!$context->data->admin)
        {
            $menu = $this->getActiveMenu();

            $view = $menu->query['view'];

            if (!empty($menu->query['own']) && !$context->data->can_manage) {
                $context->data->hide_owner_field = true;
            }

            if (in_array($view, array('list', 'tree', 'flat'))) {
                $category_filter['page'] = $menu->id;
            }

            if (!$context->data->can_manage) {
                $category_filter = array(
                    'access'       => $this->getObject('user')->getRoles(),
                    'current_user' => $this->getObject('user')->getId(),
                    'enabled'      => 1
                );
            }
        }

        $context->data->category_filter = $category_filter;

        $context->data->tag_count      = $this->getObject('com://admin/docman.model.tags')->count();
        $context->data->can_create_tag = $this->getObject('com://admin/docman.model.configs')->fetch()->can_create_tag;
        $context->data->hide_tag_field = $context->data->tag_count == 0 && !$context->data->can_create_tag;

        parent::_fetchData($context);
    }
}
