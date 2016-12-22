<?php
/**
 * @package     DOCman
 * @copyright   Copyright (C) 2012 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

class ComDocmanControllerBehaviorFindable extends ComKoowaControllerBehaviorFindable
{
    /**
     * Initializes the options for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param   KObjectConfig $config Configuration options
     * @return void
     */
    protected function _initialize(KObjectConfig $config)
    {
        $config->append(array(
            'entity' => 'document',
        ));

        parent::_initialize($config);
    }

    /**
     * Only add new items to the index if they have a frontend link
     *
     * @param KControllerContextInterface $context
     */
    protected function _afterAdd(KControllerContextInterface $context)
    {
        $name = $context->result->getIdentifier()->name;

        // Get a new model since we are going to change the state and it would reset the cached entity
        $model = $this->getObject('com://admin/docman.model.documents');

        $model->page('all')->setPage($context->result);

        if ($name === $this->_entity && $context->result->itemid) {
            parent::_afterAdd($context);
        }
    }
}
