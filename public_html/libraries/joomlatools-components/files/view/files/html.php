<?php
/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright	Copyright (C) 2011 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/joomlatools-framework-files for the canonical source repository
 */

/**
 * Files Html View
 *
 * @author  Ercan Ozkaya <https://github.com/ercanozkaya>
 * @package Koowa\Component\Files
 */
class ComFilesViewFilesHtml extends ComKoowaViewHtml
{
	protected function _initialize(KObjectConfig $config)
	{
		$config->auto_fetch = false;

		parent::_initialize($config);
	}

    protected function _fetchData(KViewContext $context)
	{
	    $state     = $this->getModel()->getState();
        $container = $this->getModel()->getContainer();

        $config = new KObjectConfig($state->config);

        $config->append(array(
            'router'           => array(
                'defaults' => array(
                    'option' => 'com_' . substr($container->slug, 0, strpos($container->slug, '-')),
                    'routed' => '1'
                )
            ),
            'initial_response' => true
        ))->append($this->getConfig()->config);

        if ($config->initial_response === true)
        {
            $count = 0;
            $query = $state->getValues();
            unset($query['config']);
            $query['thumbnails'] = $this->getModel()->getContainer()->getParameters()->thumbnails;

            if (strpos($this->getLayout(), 'compact') !== false)
            {
                $query['limit'] = 0;
                $count = ComFilesIteratorDirectory::countNodes(array('path' => $this->getModel()->getPath()));
            }

            if ($count < 100)
            {
                $controller = $this->getObject('com:files.controller.node');
                $controller->getRequest()->setQuery($query);

                $config->initial_response = $controller->format('json')->render();
            }
            else unset($config->initial_response);
        }

        $state->config = $config->toArray();

		$context->data->sitebase  = trim(JURI::root(), '/');
        $context->data->token     = $this->getObject('user')->getSession()->getToken();
		$context->data->container = $container;
        $context->data->debug     = KClassLoader::getInstance()->isDebug();

		parent::_fetchData($context);

        $context->parameters = $state->getValues();
        $context->parameters->config = $config;
    }
}
