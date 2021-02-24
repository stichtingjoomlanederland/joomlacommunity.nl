<?php
/**
 * @package    DOCman
 * @copyright   Copyright (C) 2011 - 2014 Timble CVBA (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

class plgSystemDocman_redirect extends JPlugin
{
    public function onAfterRoute()
    {
        $app    = JFactory::getApplication();
        $input  = $app->input;

        if (!class_exists('Koowa')
            || !class_exists('KObjectManager')
            || !$app->isSite()
            || $input->getCmd('option', '') !== 'com_docman'
        ) {
            return;
        }

        if ($this->_isLegacy($input)) {
            $url = $this->_routeLegacy($input);
        } else {
            $url = $this->_route($input);
        }

        if ($url) {
            $app->redirect(JRoute::_($url, false), true);
        }
    }

    /**
     * Tells if a legacy request is being handled.
     *
     * @param  JInput $input The input.
     * @return bool True if legacy, false otherwise.
     */
    protected function _isLegacy($input)
    {
        $result = false;

        $regexp = '#(doc_details|doc_view|doc_download|cat_view)\/([0-9]+)#i';

        if ($input->getCmd('task', null) || preg_match($regexp, (string) JFactory::getURI())) {
            $result = true;
        }

        return $result;
    }

    /**
     * Routes DOCman 2.x requests.
     *
     * @param  JInput $input The input.
     * @return string|null The routed URL, null if no routing is required.
     */
    protected function _route($input)
    {
        $url = null;

        if (!$item_id = $this->_getItemId($input))
        {
            $manager = KObjectManager::getInstance();

            $view = $input->getCmd('view', '');
            $id = $input->getInt('id', 0);


            switch ($view)
            {
                case 'document':
                    $model    = $manager->getObject('com://site/docman.model.documents');
                    $document = $model->enabled(1)->status('published')->id($id)->page('all')->fetch();

                    if (!$document->isNew())
                    {
                        $url = sprintf('index.php?option=com_docman&view=document&category_slug=%s&alias=%s&Itemid=%d',
                            $document->category_slug, $document->alias, $document->itemid);
                    }
                    break;
                case 'category':
                    $model    = $manager->getObject('com://site/docman.model.categories');
                    $category = $model->enabled(1)->page('all')->id($id)->fetch();

                    if (!$category->isNew())
                    {
                        $url = sprintf('index.php?option=com_docman&view=%s&slug=%s&Itemid=%d',
                            $view ? $view : 'list', $category->slug, $category->itemid);
                    }
                    break;
                default:
                    break;
            }
        }

        return $url;
    }

    /**
     * Routes DOCman legacy requests (from DOCman 1.5 and 1.6).
     *
     * @param  JInput $input The input.
     * @return string|null The routed URL, null if no routing is required.
     */
    protected function _routeLegacy($input)
    {
        $url  = null;
        $task = $input->getCmd('task', '');
        $id   = $input->getInt('gid', 0);

        if (empty($task) && preg_match('#(doc_details|doc_view|doc_download|cat_view)\/([0-9]+)#i', (string)JFactory::getURI(), $matches))
        {
            $task = $matches[1];
            $id   = $matches[2];
        }

        $item_id = $this->_getItemId($input);

        if ($task === 'doc_download' || $task === 'doc_details' || $task === 'doc_view')
        {
            $document = KObjectManager::getInstance()->getObject('com://site/docman.model.documents')
                                      ->enabled(1)
                                      ->status('published')
                // Also need to redirect links for registered users
                //->access(KObjectManager::getInstance()->getObject('user')->getRoles())
                                      ->page('all')
                                      ->id($id)
                                      ->fetch();

            if (!$document->isNew())
            {
                $view = $task === 'doc_download' ? 'download' : 'document';
                $url = sprintf('index.php?option=com_docman&view=%s&category_slug=%s&alias=%s&Itemid=%d',
                    $view, $document->category_slug, $document->alias, $item_id ? $item_id : $document->itemid);
            }
        }
        elseif ($task === 'cat_view')
        {
            $category = KObjectManager::getInstance()->getObject('com://site/docman.model.categories')
                                      ->enabled(1)
                // Also need to redirect links for registered users
                //->access(KObjectManager::getInstance()->getObject('user')->getRoles())
                                      ->page('all')
                                      ->id($id)
                                      ->fetch();

            if (!$category->isNew())
            {
                $url = sprintf('index.php?option=com_docman&view=list&slug=%s&Itemid=%d',
                    $category->slug, $item_id ? $item_id : $category->itemid);
            }
        }

        return $url;
    }

    /**
     * Menu Item ID getter.
     *
     * @param  JInput $input The input to get the menu item ID from.
     * @return int The menu item ID, null if the menu item is not found.
     */
    protected function _getItemId($input)
    {
        $item_id = $input->getInt('Itemid', 0);

        $menu = JFactory::getApplication()->getMenu()->getItem($item_id);

        if (!$menu || strpos($menu->link, 'option=com_docman') === false) {
            $item_id = null;
        }

        return $item_id;
    }
}