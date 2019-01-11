<?php
/**
 * @package    DOCman
 * @copyright   Copyright (C) 2011 Timble CVBA (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

class ComDocmanViewDoclinkHtml extends ComDocmanViewHtml
{
    protected function _initialize(KObjectConfig $config)
    {
        $config->append(array(
            'layout'     => 'default',
            'auto_fetch' => false,
            'decorator'  => 'koowa'
        ));

        parent::_initialize($config);
    }

    protected function _fetchData(KViewContext $context)
    {
        // Load administrator language file for messages
        $this->getObject('translator')->load('com://admin/docman');

        //Pages
        $pages = $this->getObject('com://admin/docman.model.pages')
            ->view(array('tree', 'list', 'document', 'flat', 'submit'))
            ->language('all')
            ->sort('title')
            ->fetch();

        $context->data->pages = $pages;
        $context->data->admin = JFactory::getApplication()->isAdmin();

        parent::_fetchData($context);
    }
}
