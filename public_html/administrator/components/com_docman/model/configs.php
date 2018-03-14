<?php
/**
 * @package    DOCman
 * @copyright   Copyright (C) 2011 Timble CVBA (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

class ComDocmanModelConfigs extends KModelAbstract
{
    public function __construct(KObjectConfig $config)
    {
        parent::__construct($config);

        $this->getState()->insert('page', 'int');
    }

    protected function _initialize(KObjectConfig $config)
    {
        $config->append(array(
            'state' => 'com://admin/docman.model.config.state',
        ));

        parent::_initialize($config);
    }

    protected function _actionFetch(KModelContext $context)
    {
        return $this->getObject('com://admin/docman.model.entity.config');
    }
}
