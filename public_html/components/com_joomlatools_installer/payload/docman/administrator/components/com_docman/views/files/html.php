<?php
/**
 * @package    DOCman
 * @copyright   Copyright (C) 2011 Timble CVBA (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

class ComDocmanViewFilesHtml extends ComDocmanViewHtml
{
    protected function _initialize(KObjectConfig $config)
    {
        $config->append([
            'decorator'  => $config->layout === 'select' ? 'koowa' : 'joomla'
        ]);

        parent::_initialize($config);
    }

    protected function _fetchData(KViewContext $context)
    {
        $context->data->tag_count      = $this->getObject('com://admin/docman.model.tags')->count();
        $context->data->can_create_tag = $this->getObject('com://admin/docman.model.configs')->fetch()->can_create_tag;
        $context->data->hide_tag_field = $context->data->tag_count == 0 && !$context->data->can_create_tag;

        parent::_fetchData($context);
    }
}
