<?php
/**
 * @package    DOCman
 * @copyright   Copyright (C) 2011 Timble CVBA (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

class ComDocmanViewHtml extends ComKoowaViewHtml
{
    protected function _fetchData(KViewContext $context)
    {
        $this->getObject('translator')->load('com:files');

        parent::_fetchData($context);
    }
}
