<?php
/**
 * @package    DOCman
 * @copyright   Copyright (C) 2011 Timble CVBA (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

/**
 * Submit controller permissions
 */
abstract class ComDocmanControllerPermissionAbstract extends ComKoowaControllerPermissionAbstract
{
    protected static $_authorised_documents = array();

    protected static $_authorised_categories = array();

    public function canEdit()
    {
        $result = parent::canEdit();

        if (count($this->_getEntities())) {
            $result = $this->canEditList();
        }

        return $result;
    }

    public function canAdd()
    {
        $page = $this->getObject('com://admin/docman.model.pages')->id($this->getRequest()->query->Itemid)->fetch();

        $authorised = self::getAuthorisedCategories(array('core.create'));

        if ($page->children)
        {
            // make sure user can add something to at least one category
            $result = (bool) array_intersect($authorised, $page->children);
        }
        else
        {
            // top level category link, return true if user can add something to any category
            $result = (bool) count($authorised);
        }

        return $result;
    }

    public function canDelete()
    {
        $result = parent::canEdit();

        if (count($this->_getEntities())) {
            $result = $this->canDeleteList();
        }

        return $result;
    }

    public function canEditList()
    {
        $result = false;

        foreach ($this->_getEntities() as $item)
        {
            if ($item->isPermissible())
            {
                $result = $item->canPerform('edit');

                if (!$result) {
                    break;
                }
            }

        }

        return $result;
    }

    public function canDeleteList()
    {
        $result = false;

        foreach ($this->_getEntities() as $item)
        {
            if ($item->isPermissible())
            {
                $result = $item->canPerform('delete');

                if (!$result) {
                    break;
                }
            }
        }

        return $result;
    }

    public function canChangeAnything()
    {
        $categories = $this->getAuthorisedCategories(array('core.create', 'core.edit'));
        $documents  = $this->getAuthorisedDocuments(array('core.edit'));

        return count($categories) || count($documents);
    }

    /**
     * Returns all categories that the user can perform passed actions on
     *
     * @param array $actions An array of actions
     * @param bool  $strict  If set to yes, a strict action check will be performed, i.e. the current
     *                       user must be able to perform all the passed actions over the returned
     *                       documents
     *
     * @return array Authorised category IDs
     */
    public function getAuthorisedDocuments(array $actions, $strict = false)
    {
        $page_id   = $this->getObject('request')->query->Itemid;
        $config    = $this->getObject('com://admin/docman.model.configs')->page($page_id)->fetch();
        $signature = md5(serialize($actions).$page_id);

        if (!isset(self::$_authorised_documents[$signature]))
        {
            $db    = JFactory::getDbo();
            $user  = $this->getObject('user');
            $query = $db->getQuery(true)
                ->select('d.docman_document_id AS id, a.name AS asset_name, d.created_by AS owner')
                ->from('#__docman_documents AS d')
                ->innerJoin('#__assets AS a ON d.asset_id = a.id');

            $all = $db->setQuery($query)->loadObjectList('id');

            $documents = array();

            foreach ($all as $entity)
            {
                foreach ($actions as $action)
                {
                    $can_do_own = false;

                    if (in_array($action, array('core.edit', 'core.delete')) && $entity->owner == $user->getId())
                    {
                        $parameter  = $action === 'delete' ? 'can_delete_own' : 'can_edit_own';
                        $can_do_own = (bool) $config->$parameter;
                    }

                    if (!$user->authorise($action, $entity->asset_name) && !$can_do_own)
                    {
                        if ($strict) {
                            continue 2;
                        } else {
                            continue 1;
                        }
                    }

                    $documents[] = (int) $entity->id;
                }
            }

            $documents = array_unique($documents);

            self::$_authorised_documents[$signature] = $documents;
        }

        return self::$_authorised_documents[$signature];
    }

    /**
     * Returns all categories that the user can perform passed actions on
     *
     * @param array $actions An array of actions
     * @param bool  $strict  If set to yes, a strict action check will be performed, i.e. the current
     *                       user must be able to perform all the passed actions over the returned
     *                       categories
     *
     * @return array Authorised category IDs
     */
    public function getAuthorisedCategories(array $actions, $strict = false)
    {
        $page_id   = $this->getObject('request')->query->Itemid;
        $config    = $this->getObject('com://admin/docman.model.configs')->page($page_id)->fetch();
        $signature = md5(serialize($actions).$page_id);

        if (!isset(self::$_authorised_categories[$signature]))
        {
            $db    = JFactory::getDbo();
            $user  = $this->getObject('user');
            $query = $db->getQuery(true)
                ->select('c.docman_category_id AS id, a.name AS asset_name, c.created_by AS owner')
                ->from('#__docman_categories AS c')
                ->innerJoin('#__assets AS a ON c.asset_id = a.id');

            $all = $db->setQuery($query)->loadObjectList('id');

            $categories = array();

            foreach ($all as $entity)
            {
                foreach ($actions as $action)
                {
                    $can_do_own = false;

                    if (in_array($action, array('core.create', 'core.edit', 'core.delete')) && $entity->owner == $user->getId())
                    {
                        $parameter  = $action === 'core.delete' ? 'can_delete_own' : 'can_edit_own';
                        $can_do_own = (bool) $config->$parameter;
                    }

                    if (!$user->authorise($action, $entity->asset_name) && !$can_do_own)
                    {
                        if ($strict) {
                            continue 2;
                        } else {
                            continue 1;
                        }
                    }

                    $categories[] = (int) $entity->id;
                }
            }

            $categories = array_unique($categories);

            self::$_authorised_categories[$signature] = $categories;
        }

        return self::$_authorised_categories[$signature];
    }

    /**
     * This will return a list of resources that the controller will act on.
     * An empty array is returned if no resource is specified. This happens
     * when methods like canEditState are called to determine if the buttons
     * in toolbar should be shown.
     *
     * @return KModelEntityInterface
     */
    protected function _getEntities()
    {
        $model = clone $this->getModel();
        $state = $this->getModel()->getState()->getValues(true);
        if (empty($state)) {
            return array();
        }

        $model->setState($state);

        return $model->fetch();
    }
}