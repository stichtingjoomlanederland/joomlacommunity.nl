<?php
/**
 * @package     DOCman
 * @copyright   Copyright (C) 2011 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

class ComDocmanControllerToolbarFlat extends ComKoowaControllerToolbarActionbar
{
    public function getCommands()
    {
        if($this->getController()->canAdd()) {
            $this->addCommand('new');

            $this->addUpload();
        }

        $layout = $this->getObject('request')->query->get('layout', 'cmd');

        // Batch delete button is only available in gallery and table
        if($this->getController()->canDelete() && !empty($layout) && $layout !== 'default') {
            $data = array(
                'csrf_token' => $this->getObject('user')->getSession()->getToken(),
                '_method' => 'delete'
            );

            $this->addCommand('delete', array(
                'attribs' => array(
                    'class' => array('btn-danger'),
                    'data-params' => htmlentities(json_encode($data))
                )
            ));
        }

        return parent::getCommands();
    }

    protected function _commandUpload(KControllerToolbarCommand $command)
    {
        $command->icon = 'k-icon-data-transfer-upload';
        $command->href = 'component=docman&view=upload&layout=default';
        $command->append(array(
            'attribs' => array(
                'data-k-modal'   => array(
                    'items' => array(
                        'src'  => (string)$this->getController()->getView()->getRoute($command->href),
                        'type' => 'iframe'
                    ),
                    'modal' => true,
                    'mainClass' => 'koowa_dialog_modal'
                )
            )
        ));

        parent::_commandDialog($command);
    }

    protected function _commandNew(KControllerToolbarCommand $command)
    {
        $command->href  = 'view=document&layout=form&slug=';
        $command->label = 'Add document';

        parent::_commandNew($command);
    }

    protected function _afterBrowse(KControllerContextInterface $context)
    {
        // Do nothing
    }
}
