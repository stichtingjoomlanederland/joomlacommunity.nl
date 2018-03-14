<?php
/**
 * @package    DOCman
 * @copyright   Copyright (C) 2011 Timble CVBA (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

class ComDocmanViewBehaviorPageable extends KViewBehaviorAbstract
{
    /**
     * A reference to the menu or module parameters
     */
    protected $_parameters;

    /**
     * @var ComDocmanTemplateHelperRoute
     */
    protected $_route_helper;

    protected function _beforeRender(KViewContext $context)
    {
        if ($this->getMixer() instanceof KViewTemplate)
        {
            $this->getMixer()->getTemplate()->registerFunction('isRecent', array($this, 'isRecent'));
            $this->getMixer()->getTemplate()->registerFunction('prepareText', array($this, 'prepareText'));
        }

        $params = $this->getParameters();
        $menu   = $this->getActiveMenu();

        if (isset($menu->query['layout']) && $menu->query['layout'] === 'table') {
            $params->show_document_title = true;
        }

        $context->data->menu   = $menu;
        $context->data->params = $params;
        $context->data->config = $this->getObject('com://admin/docman.model.configs')->fetch();
    }

    /**
     * Get menu parameters
     */
    public function getParameters()
    {
        if (!isset($this->_parameters)) {
            $this->setParameters($this->getActiveMenu()->params);
        }

        return $this->_parameters;
    }

    public function setParameters($parameters)
    {
        if (!($parameters instanceof ComKoowaDecoratorParameter) && !($parameters instanceof KObjectConfigInterface)) {
            $parameters = new ComKoowaDecoratorParameter(new KObjectConfig(array('delegate' => $parameters)));
        }

        $this->_parameters = $parameters;

        return $this;
    }

    /**
     * Returns currently active menu item
     *
     * Default menu item for the site will be returned if there is no active menu items
     *
     * @return object
     */
    public function getActiveMenu()
    {
        $menu = JFactory::getApplication()->getMenu()->getActive();
        if (is_null($menu)) {
            $menu = JFactory::getApplication()->getMenu()->getDefault();
        }

        return $menu;
    }

    /**
     * Runs a text through content plugins
     *
     * @param $text
     *
     * @return string
     */
    public function prepareText($text)
    {
        $result = JHtml::_('content.prepare', $text);

        // Make sure our script filter does not screw up email cloaking
        if (strpos($result, '<script') !== false) {
            $result = str_replace('<script', '<script data-inline', $result);
        }

        return $result;
    }

    /**
     * Returns true if the document should have a badge marking it as new
     *
     * @param KModelEntityInterface $document
     *
     * @return bool
     */
    public function isRecent(KModelEntityInterface $document)
    {
        $result = false;

        $days_for_new = $this->getParameters()->get('days_for_new');

        if (!empty($days_for_new))
        {
            $post = strtotime($document->created_on);
            $new = time() - ($days_for_new*24*3600);
            if ($post >= $new) {
                $result = true;
            }
        }

        return $result;
    }

    /**
     * Adds some information to the document row like download links and thumbnails
     *
     * @param $document KModelEntityInterface      Document row
     * @param $params   ComKoowaDecoratorParameter Page parameters
     * @param $event_context string                Event context
     */
    public function prepareDocument(&$document, $params, $event_context = 'com_docman.document')
    {
        if (empty($this->_route_helper)) {
            $this->_route_helper = $this->getObject('com://site/docman.template.helper.route');
        }

        if ($this->getMixer() instanceof KViewTemplate) {
            $this->_route_helper->setRouter(array($this->getMixer(), 'getRoute'));
        } else {
            $this->_route_helper->setRouter(array($this, 'getRoute'));
        }

        $fqr = $this->getMixer() instanceof KViewHtml ? false : true;

        $document->document_link = $this->_route_helper->document(array('entity'=> $document, 'layout' => 'default'), $fqr);
        $document->download_link = $this->_route_helper->document(array('entity'=> $document, 'view'   => 'download'), $fqr);

        $link_to = $params->document_title_link;

        if ($link_to === 'download') {
            $document->title_link = $document->download_link;
        }
        elseif ($link_to === 'details') {
            $document->title_link = $document->document_link;
        }

        if ($document->image) {
            $document->image_download_path = $document->image_path;
        }

        if ($document->isImage() && $document->canPerform('download')) {
            $document->image_download_path = $document->download_link;
        }

        $this->getObject('com://site/docman.template.helper.event')->trigger(array(
            'name'       => 'onDocmanContentPrepare',
            'attributes' => array($event_context, &$document, &$params)
        ));
    }
}