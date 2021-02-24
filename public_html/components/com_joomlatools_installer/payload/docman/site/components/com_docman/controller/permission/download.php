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
class ComDocmanControllerPermissionDownload extends ComDocmanControllerPermissionAbstract
{
    public function canRead()
    {
        $item   = $this->getModel()->fetch();
        $result = $this->getObject('user')->authorise('com_docman.download', 'com_docman');

        if ($item->isPermissible()) {
            $result = $item->canPerform('download');
        }

        if (!$result) {
            $result = $item->created_by == $this->getUser()->getId();
        }

        return (bool) $result;
    }

    public function canRender()
    {
        return $this->canRead();
    }
}