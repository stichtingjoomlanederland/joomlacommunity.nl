<?php
/**
 * @package    DOCman
 * @copyright   Copyright (C) 2011 Timble CVBA (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

defined('_JEXEC') or die;

if (class_exists('Koowa'))
{
    echo KObjectManager::getInstance()->getObject('mod://site/docman_documents.html')
        ->module($module)
        ->attribs($attribs)
        ->render();
}


