<?php
/**
 * @package    DOCman
 * @copyright   Copyright (C) 2011 Timble CVBA (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

class ComDocmanControllerFlat extends ComKoowaControllerModel
{
    public function __construct(KObjectConfig $config)
    {
        parent::__construct($config);

        if ($this->isDispatched())
        {
            $this->addBehavior('com://site/docman.controller.behavior.filterable', array(
                'vars' => ['sort', 'search_by']
            ));
        }
    }

    protected function _initialize(KObjectConfig $config)
    {
        $config->append(array(
            'toolbars' => ['flat'],
            'formats' => array('json', 'rss'),
            'model'   => 'com://site/docman.model.documents',
            'behaviors' => array(
                'ownable'
            )
        ));

        parent::_initialize($config);
    }

    /**
     * Add the toolbar for non-authentic users too
     *
     * @param KControllerContextInterface $context
     */
    protected function _addToolbars(KControllerContextInterface $context)
    {
        if($this->getView() instanceof KViewHtml)
        {
            if($this->isDispatched())
            {
                foreach($context->toolbars as $toolbar) {
                    $this->addToolbar($toolbar);
                }

                if($toolbars = $this->getToolbars())
                {
                    $this->getView()
                        ->getTemplate()
                        ->addFilter('toolbar', array('toolbars' => $toolbars));
                };
            }
        }
    }

    public function getView()
    {
        $view = parent::getView();
        $view->can_delete = $this->canDelete();
        $view->can_add    = $this->canAdd();

        return $view;
    }
}
