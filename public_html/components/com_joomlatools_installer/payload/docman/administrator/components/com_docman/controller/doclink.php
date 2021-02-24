<?php
/**
 * @package     DOCman
 * @copyright   Copyright (C) 2011 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

class ComDocmanControllerDoclink extends ComKoowaControllerView
{
    /**
     * Passes the e_view parameter that Joomla sends in the request for the editor name.
     *
     * @see KControllerResource::getView()
     */
    public function getView()
    {
        $view = parent::getView();

        if ($view) {
            $view->editor = $this->getRequest()->query->e_name;
        }

        return $view;
    }
}
