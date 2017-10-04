<?php
/**
 * @package     DOCman
 * @copyright   Copyright (C) 2011 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

class ComDocmanControllerToolbarUser extends ComDocmanControllerToolbarActionbar
{

    protected function _afterBrowse(KControllerContextInterface $context)
    {
        $controller = $this->getController();
        $translator = $this->getObject('translator');

        $this->addAssign(array(
          'allowed' => $controller->canEdit(),
          'icon' => 'k-icon-plus',
          'label' => $translator->translate('Assign to group')
        ));

        $this->addRemove(array(
            'allowed' => $controller->canEdit(),
            'icon' => 'k-icon-minus',
            'label' => $translator->translate('Remove from group')
        ));
    }

    protected function _commandAssign(KControllerToolbarCommand $command)
    {
        $command->attribs['href'] = '#';
    }

    protected function _commandRemove(KControllerToolbarCommand $command)
    {
        $command->attribs['href'] = '#';
    }
}
