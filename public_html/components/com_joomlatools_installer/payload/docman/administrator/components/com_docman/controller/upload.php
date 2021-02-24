<?php
/**
 * @package     DOCman
 * @copyright   Copyright (C) 2011 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

class ComDocmanControllerUpload extends ComKoowaControllerView
{
    public function getRequest()
    {
        $request = parent::getRequest();

        // This is used to circumvent the URL size exceeding 2k bytes problem for "create documents" screen
        if ($request->data->has('paths')) {
            $request->query->paths = $request->data->paths;
        }

        if ($request->data->has('folder')) {
            $request->query->folder = $request->data->folder;
        }

        if ($request->data->has('category')) {
            $request->query->category = $request->data->category;
        }

        return $request;
    }

    public function getView()
    {
        $view    = parent::getView();
        $request = $this->getRequest();

        $view->can_manage = $this->canManage();

        if ($request->query->onBeforeInitialize) {
            $view->onBeforeInitialize = $request->query->onBeforeInitialize;
        }

        if ($request->query->paths) {
            $view->paths = $request->query->paths;
        }

        if ($request->query->folder) {
            $view->folder = $request->query->folder;
        }

        if ($request->query->category_id) {
            $view->selected_category = $request->query->category_id;
        }

        return $view;
    }
}
