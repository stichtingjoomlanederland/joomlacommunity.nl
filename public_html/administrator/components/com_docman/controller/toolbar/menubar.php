<?php
/**
 * @package     DOCman
 * @copyright   Copyright (C) 2011 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

class ComDocmanControllerToolbarMenubar extends ComKoowaControllerToolbarMenubar
{
    public function getCommands()
    {
        // Parent call adds commands from the component manifest
        parent::getCommands();

        if ($this->getController()->canAdmin()) {
            $this->addCommand('Settings', array(
                'href'   => 'option=com_docman&view=config',
                'active' => false
            ));
        }

        return $this->_commands;
    }
}