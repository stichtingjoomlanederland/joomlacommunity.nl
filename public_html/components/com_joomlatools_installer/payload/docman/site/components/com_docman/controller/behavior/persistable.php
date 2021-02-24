<?php
/**
 * @package    DOCman
 * @copyright   Copyright (C) 2011 Timble CVBA (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

class ComDocmanControllerBehaviorPersistable extends ComKoowaControllerBehaviorPersistable
{
    protected function _beforeRender(KControllerContextInterface $context)
    {
        if ($context->getRequest()->getFormat() !== 'json') {
            parent::_beforeBrowse($context);
        }
    }

    protected function _afterRender(KControllerContextInterface $context)
    {
        if ($context->getRequest()->getFormat() !== 'json') {
            parent::_afterBrowse($context);
        }
    }

    /**
     * Returns a key based on the context to persist state values
     *
     * @param 	KControllerContextInterface $context The active controller context
     * @return  string
     */
    protected function _getStateKey(KControllerContextInterface $context)
    {
        $key = parent::_getStateKey($context);

        $page = $context->getRequest()->getQuery()->get('page', 'int');

        if (is_scalar($page)) {
            $key .= '.'.$page;
        }

        return $key;
    }
}
