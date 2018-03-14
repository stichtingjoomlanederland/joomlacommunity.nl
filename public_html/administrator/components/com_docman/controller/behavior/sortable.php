<?php
/**
 * @package     DOCman
 * @copyright   Copyright (C) 2011 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

/**
 * Adds default sorting
 *
 * It is low priority so that persistable kicks in first
 */
class ComDocmanControllerBehaviorSortable extends KControllerBehaviorAbstract
{
    protected function _initialize(KObjectConfig $config)
    {
        $config->append(array(
            'priority' => KCommandHandlerAbstract::PRIORITY_LOW
        ));

        parent::_initialize($config);
    }

    public function isSupported()
    {
        $mixer   = $this->getMixer();
        $request = $mixer->getRequest();

        if ($mixer instanceof KControllerModellable && $mixer->isDispatched() && $request->isGet() && $request->getFormat() === 'html') {
            return true;
        }

        return false;
    }

    protected function _beforeBrowse(KControllerContextInterface $context)
    {
        $query = $context->getRequest()->getQuery();
        $state = $this->getModel()->getState();
        $name  = $this->getMixer()->getIdentifier()->getName();
        $sort  = $direction = null;

        if ($name === 'document') {
            $sort = 'created_on';
            $direction = 'desc';
        } elseif ($name === 'category') {
            $sort = 'custom';
            $direction = 'asc';
        }

        if (!$query->sort) {
            $query->sort = $sort;
            $state->sort = $sort;
        }

        if (!$query->direction) {
            $query->direction = $direction;
            $state->direction = $direction;
        }

        $state->setProperty('sort', 'default', $sort)
            ->setProperty('direction', 'default', $direction);

    }

}