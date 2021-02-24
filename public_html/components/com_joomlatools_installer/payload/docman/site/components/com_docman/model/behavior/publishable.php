<?php
/**
 * @package    DOCman
 * @copyright   Copyright (C) 2011 Timble CVBA (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

class ComDocmanModelBehaviorPublishable extends KModelBehaviorAbstract
{
    protected function _beforeCount(KModelContextInterface $context)
    {
        $this->_beforeFetch($context);
    }

    protected function _beforeFetch(KModelContextInterface $context)
    {
        $state = $context->state;

        if (is_numeric($state->enabled))
        {
            $user_enabled_clause = '';
            // Logged in users see their documents regardless of the access level
            if ($state->current_user) {
                $user_enabled_clause = 'tbl.created_by = :current_user OR';
            }

            $context->query->where(sprintf('(%s c.enabled IN :enabled)', $user_enabled_clause))
                ->bind(array(
                    'enabled' => (array) $state->enabled,
                    'current_user' => $state->current_user
                ));
        }
    }
}