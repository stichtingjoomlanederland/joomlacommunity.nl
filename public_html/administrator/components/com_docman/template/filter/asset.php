<?php
/**
 * @package    DOCman
 * @copyright   Copyright (C) 2011 Timble CVBA (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

class ComDocmanTemplateFilterAsset extends ComKoowaTemplateFilterAsset
{
    protected function _initialize(KObjectConfig $config)
    {
        $path = rtrim($this->getObject('request')->getSiteUrl()->getPath(), '/');

        $config->append(array(
            'schemes' => array(
                'icon://' => $path.'/joomlatools-files/docman-icons/'
            ),
        ));

        parent::_initialize($config);
    }
}