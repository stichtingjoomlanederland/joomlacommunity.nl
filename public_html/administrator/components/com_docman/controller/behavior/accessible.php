<?php
/**
 * @package     DOCman
 * @copyright   Copyright (C) 2011 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

class ComDocmanControllerBehaviorAccessible extends KControllerBehaviorAbstract
{
    /**
     * Sets the default access level to inherit for documents and Joomla default for categories
     *
     * @param KControllerContextInterface $context
     */
    protected function _afterRead(KControllerContextInterface $context)
    {
        if ($context->result->isNew())
        {
            if ($this->getMixer()->getIdentifier()->name === 'document') {
                $context->result->access = 0;
            }
        }
    }

    /**
     * Unsets certain options from the request if the user does not have access to save them.
     *
     * @param KControllerContextInterface $context
     */
    protected function _beforeEdit(KControllerContextInterface $context)
    {
        if (!$this->canAdmin()) {
            unset($context->request->data->rules);
        }
    }

    protected function _beforeAdd(KControllerContextInterface $context)
    {
        $this->_beforeEdit($context);
    }
}