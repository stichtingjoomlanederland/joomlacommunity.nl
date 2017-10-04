<?php
/**
 * @package    DOCman
 * @copyright   Copyright (C) 2011 Timble CVBA (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

/**
 * Document controller permissions
 */
class ComDocmanControllerPermissionDocument extends ComDocmanControllerPermissionAbstract
{
    public function canAdd()
    {
        $result = parent::canAdd();

        // adding a document
        if (!$this->getRequest()->isGet())
        {
            $data        = $this->getRequest()->data;
            $category_id = !empty($data->docman_category_id) ? $data->docman_category_id : $this->getRequest()->query->get('docman_category_id', 'int');

            if ($category_id) {
                $result = $this->canAddToCategory($category_id);
            }
        }
        // form or grid view, enable the button if the user can create something in at least one category
        else
        {
            if (count(self::getAuthorisedCategories(array('core.create')))) {
                $result = true;
            }
        }

        return (bool) $result;
    }

    public function canAddToCategory($category_id = null)
    {
        $result   = false;
        $category = $this->getObject('com://admin/docman.model.categories')->id((int)$category_id)->fetch();

        if ($category && $category->isPermissible()) {
            $result = $category->canPerform('add');
        }

        return $result;
    }
}
