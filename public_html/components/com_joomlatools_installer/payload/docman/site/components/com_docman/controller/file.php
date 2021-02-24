<?php
/**
 * @package     DOCman
 * @copyright   Copyright (C) 2011 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

class ComDocmanControllerFile extends ComKoowaControllerView
{
    protected function _initialize(KObjectConfig $config)
    {
        $config->append(array(
            'view' => 'files'
        ));

        parent::_initialize($config);
    }

    public function getRequest()
    {
        $request = parent::getRequest();

        // This is used to circumvent the URL size exceeding 2k bytes problem for "create documents" screen
        if ($request->data->has('paths')) {
            $request->query->paths = $request->data->paths;
        }

        return $request;
    }

    public function getView()
    {
        $view    = parent::getView();
        $request = $this->getRequest();

        if ($request->query->callback && $request->query->layout === 'select') {
            $view->callback = $request->query->callback;
        }

        if ($request->query->paths && $request->query->layout === 'form') {
            $view->paths = $request->query->paths;
        }

        return $view;
    }
}
