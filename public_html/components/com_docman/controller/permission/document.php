<?php
/**
 * @package    DOCman
 * @copyright   Copyright (C) 2011 Timble CVBA (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

/**
 * Download controller permissions
 */
class ComDocmanControllerPermissionDocument extends ComDocmanControllerPermissionAbstract
{
    public function canRead()
    {
        $result = true;

        // FIXME: layout=form is not used anymore
        if ($this->getRequest()->query->layout === 'form') {
            // Only display the edit form if user can add/edit stuff
            $result = $this->getModel()->getState()->isUnique() ? $this->canEdit() : $this->canAdd();
        }

        return $result;
    }

    public function canAdd()
    {
        if ($this->getRequest()->getMethod() == 'GET')
        {
            $result = parent::canAdd();
        }
        else
        {
            $result = false;
            $data   = $this->getRequest()->data;

            if ($category_id = $data->docman_category_id)
            {
                $category = $this->getObject('com://admin/docman.model.categories')->id((int) $category_id)->fetch();

                if ($category && $category->isPermissible())
                {
                    $result = $category->canPerform('add');
                }
            }
        }

        return (bool) $result;
    }
}