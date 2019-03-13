<?php
/**
 * @package     DOCman
 * @copyright   Copyright (C) 2011 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

class ComDocmanViewScriptHtml extends ComKoowaViewHtml
{
    protected function _initialize(KObjectConfig $config)
    {
        $config->append(array(
            'auto_fetch' => false,
            'decorator'  => 'koowa'
        ));

        parent::_initialize($config);
    }

    protected function _fetchData(KViewContext $context)
    {
        parent::_fetchData($context);

        $context->data->script  = $this->getData()['script'];
        $context->data->jobs    = $this->getData()['jobs'];
        $context->data->title   = $this->getData()['title'];
        $context->data->go_back = $this->getRoute('option=com_docman&view=documents');

        $context->data->token     = $this->getObject('user')->getSession()->getToken();
    }
}
