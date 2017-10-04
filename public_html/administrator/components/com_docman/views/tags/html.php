<?php
/**
 * @package    DOCman
 * @copyright   Copyright (C) 2011 Timble CVBA (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

 class ComDocmanViewTagsHtml extends ComDocmanViewHtml
 {
     protected function _fetchData(KViewContext $context)
     {
         $context->data->tag_count = $this->getObject('com://admin/docman.model.tags')->count();
         
         parent::_fetchData($context);
     }
 }
