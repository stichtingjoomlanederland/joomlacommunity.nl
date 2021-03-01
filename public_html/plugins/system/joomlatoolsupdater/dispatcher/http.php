<?php
/**
 * @package     JoomlatoolsUpdater
 * @copyright   Copyright (C) 2021 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

class PlgSystemJoomlatoolsupdaterDispatcherHttp extends KDispatcherHttp
{

    protected function _initialize(KObjectConfig $config)
    {
        $config->append([
            'controller' => 'plg:system.joomlatoolsupdater.controller.license'
        ]);

        parent::_initialize($config);

        $config->behaviors = [];
        $config->authenticators = [];
    }

    public function setController($controller, $config = array())
    {
        if(!($controller instanceof KControllerInterface))
        {
            $controller = 'plg:system.joomlatoolsupdater.controller.license';

            return parent::setController($controller, $config);
        }

        return $this;
    }

    protected function _beforeDispatch(KDispatcherContextInterface $context)
    {
    }
}