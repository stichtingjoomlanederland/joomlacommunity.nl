<?php
/**
 * @package     DOCman
 * @copyright   Copyright (C) 2011 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

class ComDocmanControllerToolbarSubmit extends ComKoowaControllerToolbarActionbar
{
    protected function _afterRead(KControllerContextInterface $context)
    {
        $controller = $this->getController();

        if($controller->canAdd()) {
            $this->addCommand('save', array('allowed' => true, 'label' => 'Submit'));
        }
    }
}
