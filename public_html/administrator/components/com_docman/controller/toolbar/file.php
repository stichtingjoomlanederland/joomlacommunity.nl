<?php
/**
 * @package     DOCman
 * @copyright   Copyright (C) 2011 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

class ComDocmanControllerToolbarFile extends ComDocmanControllerToolbarActionbar
{
    public function getCommands()
    {
        $controller = $this->getController();
        $layout     = $controller->getView()->getLayout();

        if ($layout == 'default')
        {
            $this->addNewfolder(array(
                'label' => 'New Folder',
                'allowed' => $controller->canAdd(),
                'icon' => 'k-icon-plus',
                'attribs' => array('class' => array('js-open-folder-modal k-button--success'))
            ));

            $this->addCopy(array('allowed' => $controller->canMove()));
            $this->addMove(array('allowed' => $controller->canMove()));

            $this->addDelete(array('allowed' => $controller->canDelete()));
            $this->addSeparator();
            $this->addCommand('createdocuments', array('label' => 'Create Documents', 'icon' => 'icon-save-new', 'allowed' => $controller->canAdd()));
            $this->addRefresh();
        }

        if ($layout == 'form')
        {
            $this->addApply(array('allowed' => $controller->canAdd()));
            $this->addSave(array('allowed' => $controller->canAdd()));

            $this->addCancel();
        }

        return parent::getCommands();
    }

    protected function _commandRefresh(KControllerToolbarCommand $command)
    {
        $command->icon = 'k-icon-loop-circular';
    }

    protected function _commandCreatedocuments(KControllerToolbarCommand $command)
    {
        $command->icon = 'k-icon-plus';
        $command->href = 'component=docman&view=upload&layout=default';
        $command->attribs->append(['class' => ['k-is-hidden']]);
    }

}
