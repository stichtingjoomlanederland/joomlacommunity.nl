<?php
/**
 * @package    DOCman
 * @copyright   Copyright (C) 2011 Timble CVBA (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

class ComDocmanModelBehaviorNestable extends KModelBehaviorAbstract
{
    protected function _beforeCount(KModelContextInterface $context)
    {
        $this->_beforeFetch($context);
    }

    public function onMixin(KObjectMixable $mixer)
    {
        parent::onMixin($mixer);

        if ($mixer instanceof KModelDatabase)
        {
            $state = $mixer->getState();

            if (!isset($state->parent_id)) {
                $state->insert('parent_id', 'int');
            }

            if (!isset($state->group_id)) {
                $state->insert('group_id', 'int');
            }

            if (!isset($state->level)) {
                $state->insert('level', 'int');
            }

            if (!isset($state->max_level)) {
                $state->insert('max_level', 'int');
            }

            if (!isset($state->include_self)) {
                $state->insert('include_self', 'boolean', false);
            }

            $state->setProperty('sort', 'default', 'title');

            return true;
        } else {
            return false;
        }
    }

    protected function _beforeFetch(KModelContextInterface $context)
    {
        $state = $context->state;

        if (!$state->isUnique())
        {
            if ($state->sort) {
                $context->query->bind(array('sort' => $state->sort));
            }

            if ($state->direction) {
                $context->query->bind(array('direction' => $state->direction));
            }

            if ($state->include_self) {
                $context->query->bind(array('include_self' => $state->include_self));
            }

            if ($state->parent_id) {
                $context->query->bind(array('parent_id' => $state->parent_id));
            }

            if ($state->group_id) {
                $context->query->bind(array('group_id' => $state->group_id));
            }

            if ($state->level) {
                $context->query->bind(array('level' => $state->level));
            }

            if ($state->max_level) {
                $context->query->bind(array('max_level' => $state->max_level));
            }
        }
    }
}
