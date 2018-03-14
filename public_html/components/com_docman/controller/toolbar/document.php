<?php
/**
 * @package     DOCman
 * @copyright   Copyright (C) 2011 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

class ComDocmanControllerToolbarDocument extends ComKoowaControllerToolbarActionbar
{
    protected function _afterRead(KControllerContextInterface $context)
    {
        $allowed = true;

        if (isset($context->result) && $context->result->isLockable() && $context->result->isLocked()) {
            $allowed = false;
        }

        $this->addCommand('apply', array('allowed' => $allowed));
        $this->addCommand('save', array('allowed' => $allowed));
        $this->addCommand('cancel');

        $controller = $this->getController();
        $translator = $this->getObject('translator');
        $name       = $translator->translate(strtolower($context->subject->getIdentifier()->name));;
        $unique  = $controller->getModel()->getState()->isUnique();
        $title   = $this->getObject('translator')->translate($unique ? 'Edit {item_type}' : 'Create new {item_type}',
            array('item_type' => $name));

        $this->getCommand('title')->title = $title;
    }

    protected function _afterBrowse(KControllerContextInterface $context)
    {
        // Do nothing
    }
}
