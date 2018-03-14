<?php
/**
 * @package    DOCman
 * @copyright   Copyright (C) 2011 Timble CVBA (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

class ComDocmanViewSubmitHtml extends ComDocmanViewHtml
{
    protected function _initialize(KObjectConfig $config)
    {
        $config->auto_fetch = false;

        parent::_initialize($config);
    }

    protected function _fetchData(KViewContext $context)
    {
        if ($this->getLayout() != 'success')
        {
            $this->getObject('translator')->load('com:files');
            
            $page   = JFactory::getApplication()->getMenu()->getActive();
            $params = $page->params;

            $context->data->document = $this->getModel()->fetch();

            //Preset defaults. Will be overriden if $all_categories == 1
            $context->data->level = null;
            $context->data->categories = 0;
            $context->data->show_categories = true;

            //Get selected categories
            $context->data->categories = $params->get('category_id');

            //Get the level so we know whether to count the children or not
            $level = (count($context->data->categories) == 0) || ($params->get('category_children')) ? null : array(0);

            //count the total categories to display.
            $count = $this->getObject('com://site/docman.model.categories')->setState(array(
                'parent_id'    => KObjectConfig::unbox($context->data->categories),
                'include_self' => true,
                'level'        => $level,
                'access'       => $this->getObject('user')->getRoles(),
                'current_user' => $this->getObject('user')->getId(),
                'enabled'      => true
            ))->count();

            //If there is only a single category for this user then we do not
            //display the category
            $context->data->show_categories = $count != 1;
            $context->data->level = $level;

            //set categories to 0 if nothing is selected. I am using this to show all categories
            if($count == 0)
            {
                $context->data->categories = 0;
            }


        }
        else $context->data->page = JFactory::getApplication()->getMenu()->getDefault();

        parent::_fetchData($context);
    }
}
