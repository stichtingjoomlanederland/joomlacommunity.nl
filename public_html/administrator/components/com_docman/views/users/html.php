<?php
/**
 * @package    DOCman
 * @copyright   Copyright (C) 2011 Timble CVBA (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

class ComDocmanViewUsersHtml extends ComDocmanViewHtml
{

    protected function _fetchData(KViewContext $context)
    {
        parent::_fetchData($context);

        $context->data->groups = $this->getObject('com://admin/docman.model.usergroups')->fetch();
    }


}
