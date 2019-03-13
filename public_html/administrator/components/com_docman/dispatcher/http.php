<?php
/**
 * @package    DOCman
 * @copyright   Copyright (C) 2011 Timble CVBA (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

class ComDocmanDispatcherHttp extends ComKoowaDispatcherHttp
{
    protected function _initialize(KObjectConfig $config)
    {
        $config->append(array(
            'controller' => 'document',
            'behaviors'  => array(
                'com://admin/docman.dispatcher.behavior.routable',
                'com:migrator.dispatcher.behavior.migratable'
            )
        ));

        parent::_initialize($config);
    }

    public function getRequest()
    {
        $request = parent::getRequest();

        if ($request->query->Itemid) {
            $request->query->page = $request->query->Itemid;
        }

        return $request;
    }
}
