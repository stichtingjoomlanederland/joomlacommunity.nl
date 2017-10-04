<?php
/**
 * Joomlatools Framework - https://www.joomlatools.com/developer/framework/
 *
 * @copyright   Copyright (C) 2016 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://github.com/joomlatools/joomlatools-framework-scheduler for the canonical source repository
 */

/**
 * Schedulable behavior
 *
 * @author Ercan Ozkaya <https://github.com/ercanozkaya>
 * @package Koowa\Component\Scheduler
 */
class ComSchedulerDispatcherHttp extends KDispatcherAbstract
{
    /**
     * Initializes the options for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param   KObjectConfig $config Configuration options.
     * @return  void
     */
    protected function _initialize(KObjectConfig $config)
    {
        $config->append(array(
            'controller' => 'com:scheduler.controller.dispatcher',
            'response'   => 'com:koowa.dispatcher.response',
            'request'    => 'com:koowa.dispatcher.request',
            'user'       => 'com:koowa.user'
        ));

        parent::_initialize($config);
    }

    protected function _actionDispatch(KDispatcherContextInterface $context)
    {
        $job_dispatcher = $this->getController();

        $context = $job_dispatcher->getContext();

        $job_dispatcher->synchronize($context);
        $job_dispatcher->dispatch($context);

        $result = array(
            'continue' => (bool) $job_dispatcher->getNextJob(),
            'logs'     => KClassLoader::getInstance()->isDebug() ? $context->getLogs() : array()
        );

        $context->request->setFormat('json');
        $context->response->setContent(json_encode($result), 'application/json');
        $context->response->headers->set('Cache-Control', 'no-cache');

        $this->send();
    }
}