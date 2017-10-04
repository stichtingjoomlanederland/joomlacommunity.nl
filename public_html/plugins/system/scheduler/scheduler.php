<?php
/**
 * @package    DOCman
 * @copyright   Copyright (C) 2011 Timble CVBA (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

class PlgSystemScheduler extends JPlugin
{
    protected static $_exclude = array(
        'com_postinstall', 'com_joomlaupdate', 'com_joomlatools_installer',
        'com_config', 'com_installer', 'com_plugins',
        'com_users', 'com_user', 'com_login'
    );

    /**
     * Runs the job dispatcher and ends the request if the request has &run&the&scheduler in the query string
     *
     */
    public function onAfterRoute()
    {
        $app   = JFactory::getApplication();
        $input = $app->input;

        if ($app->isSite() && $input->get('option') === 'com_docman' && $input->get('controller') === 'scheduler')
        {
            $dispatcher = KObjectManager::getInstance()->getObject('com:scheduler.dispatcher.http');

            $dispatcher->getController()->addCommandCallback('after.dispatch', function($context) {
                $this->_afterDispatch($context);
            });

            $dispatcher->dispatch();
        }
    }

    /**
     * Logs the execution results
     *
     * @param $context
     * @throws Exception
     */
    protected function _afterDispatch($context)
    {
        try {
            $file = 'joomlatools-scheduler.php';
            $path = rtrim(JFactory::getConfig()->get('log_path'), '/').'/'.$file;

            if (file_exists($path) && filesize($path) > 10485760) {
                @unlink($path);
            }

            JLog::addLogger([
                'logger'            => 'formattedtext',
                'text_file'         => $file,
                'text_entry_format' => '{DATETIME} {PRIORITY} {MESSAGE}'
            ], JLog::ALL, ['joomlatools-scheduler']);

            foreach ($context->getLogs() as $log) {
                JLog::add($log[0], JLog::INFO, 'joomlatools-scheduler', $log[1]);
            }
        } catch (Exception $e) {
            if (JDEBUG) throw $e;
        }
    }

    /**
     * Only injects the tracker code when:
     * * Request method is GET
     * * Site is not offline
     * * Document type is HTML
     * * We are not on a "delicate" component
     * @return bool
     */
    public function canRun()
    {
        return (@$_SERVER['REQUEST_METHOD'] === 'GET'
            && !JFactory::getConfig()->get('offline')
            && JFactory::getDocument()->getType() === 'html'
            && !in_array(JFactory::getApplication()->input->get('option', 'cmd'), static::$_exclude));
    }

    /**
     * Adds JavaScript trigger to the page right above </body> for HTML pages that are NOT the homepage.
     *
     * The code is not added to home page on frontend so we do not swamp the server.
     * We also only add when there is a </body> tag present to make sure we don't mess with custom content
     */
    public function onAfterRender()
    {
        try {
            if ($this->canRun())
            {
                $now         = gmdate('Y-m-d H:i:s');
                $query       = /** @lang text */"SELECT sleep_until < '$now' FROM #__scheduler_metadata WHERE type = 'metadata' LIMIT 1";
                $sleep_until = JFactory::getDbo()->setQuery($query)->loadResult();

                // null = no rows or actual boolean value
                if ($sleep_until === null || $sleep_until)
                {
                    $url = JUri::root().'index.php?option=com_docman&controller=scheduler';

                    /*
                     * To recreate this block:
                     * * Compress request.js
                     * * Remove the first block for data-scheduler property and replace with a direct call
                     */
                    $html = '<script type="text/javascript">/*docman job scheduler*/
!function(){function e(e,t,n,o){try{o=new(this.XMLHttpRequest||ActiveXObject)("MSXML2.XMLHTTP.3.0"),o.open("POST",e,1),o.setRequestHeader("X-Requested-With","XMLHttpRequest"),o.setRequestHeader("Content-type","application/x-www-form-urlencoded"),o.onreadystatechange=function(){o.readyState>3&&t&&t(o.responseText,o)},o.send(n)}catch(c){}}function t(n){e(n,function(e,o){try{if(200==o.status){var c=JSON.parse(e)
"object"==typeof c&&c["continue"]&&setTimeout(function(){t(n)},1e3)}}catch(u){}})}t("'.$url.'")}()</script>';

                    $body = JFactory::getApplication()->getBody();
                    $body = str_replace('</body>', $html.'</body>', $body);

                    JFactory::getApplication()->setBody($body);
                }
            }
        } catch (Exception $e) {
            if (JDEBUG) {
                JFactory::getApplication()->enqueueMessage($e->getMessage(), 'error');
            }
        }
    }

    /**
     * Overridden to only run if we have Nooku framework installed
     */
    public function update(&$args)
    {
        $return = null;

        if (class_exists('Koowa') && class_exists('KObjectManager'))
        {
            try {
                $return = parent::update($args);
            }
            catch (Exception $e) {
                if (JDEBUG) {
                    JFactory::getApplication()->enqueueMessage($e->getMessage(), 'error');
                }
            }
        }

        return $return;
    }
}