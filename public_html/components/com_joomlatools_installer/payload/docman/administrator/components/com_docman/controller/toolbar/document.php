<?php
/**
 * @package     DOCman
 * @copyright   Copyright (C) 2011 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

class ComDocmanControllerToolbarDocument extends ComDocmanControllerToolbarActionbar
{
    protected function _commandUpload(KControllerToolbarCommand $command)
    {
        $category = $this->getObject('request')->query->category;

        $command->icon = 'k-icon-data-transfer-upload';
        $command->href = 'component=docman&view=upload&layout=default&category_id='.$category;
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
}