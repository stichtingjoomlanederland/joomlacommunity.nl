<?php
/**
 * @package    DOCman
 * @copyright   Copyright (C) 2011 Timble CVBA (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

/**
 * File controller permissions
 */
class ComDocmanControllerPermissionFile extends ComDocmanControllerPermissionAbstract
{
    /**
     * Generic permission checker
     *
     * * Renders the view if the user can see any edit form in the component
     * * Renders the folder tree and allow upload if you can see any edit form in the component
     * * Renders nodes, files and thumbnails, container views only if the user can manage the component
     *
     * @return bool
     */
    public function canRender()
    {
        if (!$this->getMixer()->isDispatched() || $this->canManage()) {
            return true;
        }

        $view   = $this->getView()->getName();
        $format = $this->getRequest()->getFormat();

        if (($format === 'html' && $view === 'files') ||
            ($format === 'json' && in_array($view, array('nodes', 'file', 'files', 'folders', 'proxy')))
        ) {
            return $this->canChangeAnything();
        }

        return false;
    }

    public function canRead()
    {
        return $this->canRender();
    }

    public function canBrowse()
    {
        return $this->canRender();
    }

    public function canAdd()
    {
        return $this->canRender();
    }

    public function canEdit()
    {
        return $this->canRender();
    }

    public function canDelete()
    {
        if (!$this->getRequest()->isGet())
        {
            $name = $this->getMixer()->getIdentifier()->name;
            if ($name === 'file' || $name === 'folder')
            {
                $request = $this->getRequest();
                $path = ($request->query->folder ? $request->query->folder.'/' : '') . $request->query->name;

                $documents = $this->getObject('com://admin/docman.model.documents')
                    ->search_path($name === 'file' ? $path : $path.'/%')
                    ->storage_type('file')
                    ->fetch();
                $count = count($documents);

                if ($count)
                {
                    $translator = $this->getObject('translator');

                    if ($name === 'file')
                    {
                        $messages = array(
                            $translator->translate('The document with the title {title} has this file attached to it. You should either change the attached file or delete the document before deleting this file.'),
                            $translator->translate('This file has {count} documents attached to it. You should either change the attached files or delete these documents before deleting this file.'),
                        );
                    }
                    else
                    {
                        $messages = array(
                            $translator->translate('The document with the title {title} has a file attached from this folder. You should either change the attached file or delete the document before deleting this folder.'),
                            $translator->translate('There are {count} documents that have a file attached from this folder. You should either change the attached files or delete these documents before deleting this folder.')
                        );
                    }

                    $message = $translator->choose($messages, $count, array(
                        'count' => $count,
                        'title' => $count == 1 ? $documents->top()->title : ''
                    ));

                    throw new KControllerExceptionActionFailed($message);
                }
            }
        }

        return $this->canManage();
    }

    public function canMove()
    {
        return $this->canDelete() && $this->canAdd();
    }

    public function canCopy()
    {
        return $this->canAdd();
    }


    public function canManage()
    {
        return $this->getObject('user')->authorise('core.manage', 'com_docman') === true;
    }
}