<?php
/**
 * @package     DOCman
 * @copyright   Copyright (C) 2011 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

class ComDocmanControllerToolbarList extends ComKoowaControllerToolbarActionbar
{
    public function getCommands()
    {
        if($this->getController()->canAdd()) {
            $this->addCommand('new');

            $page = JFactory::getApplication()->getMenu()->getItem($this->getObject('request')->query->Itemid);

            if ($page && ($page->params->get('allow_category_add', 1) || $this->getController()->canAdmin())) {
                $this->addCommand('newCategory');
            }

            $this->addUpload();
        }

        $layout = $this->getObject('request')->query->get('layout', 'cmd');

        // Batch delete button is only available in gallery and table
        if($this->getController()->canDelete() && in_array($layout, array('table', 'gallery'))) {
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
        $category       = $this->getController()->getModel()->fetch();

        $command->icon = 'k-icon-data-transfer-upload';
        $command->href = 'component=docman&view=upload&layout=default&category_id='.($category->id ? $category->id : '');
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
        $category       = $this->getController()->getModel()->fetch();
        $command->href  = 'view=document&layout=form&slug=&category_slug=' . ($category->slug ? $category->slug : '');
        $command->label = 'Add document';

        parent::_commandNew($command);
    }

    protected function _commandNewCategory(KControllerToolbarCommand $command)
    {
        $category       = $this->getController()->getModel()->fetch();
        $command->href  = 'view=category&layout=form&slug=&category_slug=' . ($category->slug ? $category->slug : '');
        $command->label = $this->getObject('translator')->translate('Add category');

        $command->icon = 'k-icon-plus';
    }

    protected function _afterBrowse(KControllerContextInterface $context)
    {
        // Do nothing
    }
}
