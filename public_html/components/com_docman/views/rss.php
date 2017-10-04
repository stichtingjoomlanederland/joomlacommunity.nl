<?php
/**
 * @package    DOCman
 * @copyright   Copyright (C) 2011 Timble CVBA (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

class ComDocmanViewRss extends KViewRss
{
    protected function _initialize(KObjectConfig $config)
    {
        $config->append(array(
            'behaviors' => array('pageable'),
            'auto_fetch' => false
        ));

        parent::_initialize($config);
    }

    public function getLayout()
    {
        return 'com://site/docman.documents.default';
    }

    protected function _fetchData(KViewContext $context)
    {
        $params = $this->getParameters();

        $context->data->append(array(
            'sitename'  => JFactory::getApplication()->getCfg('sitename'),
            'language'  => JFactory::getLanguage()->getTag(),
            'documents' => $this->getModel()->fetch(),
            'total'     => $this->getModel()->count(),
            'channel_link' => $this->getRoute('format=html&layout=default'),
            'feed_link'    => $this->getRoute('format=rss&layout=default'),
            'description'  => ''
        ));

        foreach ($context->data->documents as $document)
        {
            $this->prepareDocument($document, $params, 'com_docman.rss');
            $document->document_link->query['format'] = 'html';
        }

        parent::_fetchData($context);
    }
}
