<?php
/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright	Copyright (C) 2011 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/joomlatools-framework-files for the canonical source repository
 */

/**
 * Dispatcher
 *
 * @author  Ercan Ozkaya <https://github.com/ercanozkaya>
 * @package Koowa\Component\Files
 */
class ComFilesDispatcherHttp extends ComKoowaDispatcherHttp
{
    protected function _initialize(KObjectConfig $config)
    {
        $config->append(array(
            'limit' => array(
                'max' => 2000 // Used in tree view
            )
        ));

        parent::_initialize($config);
    }

    /**
     * Overloaded execute function to handle exceptions in JSON requests
     */
    public function execute($action, KControllerContextInterface $context)
    {
        try {
            return parent::execute($action, $context);
        } catch (Exception $e) {
            return $this->_handleException($e);
        }
    }

    /**
     * Plupload do not pass the error to our application if the status code is not 200
     *
     * @param Exception $e
     * @return bool
     * @throws Exception
     */
    protected function _handleException(Exception $e) 
    {
    	if ($this->getRequest()->getFormat() == 'json')
        {
    		$response = new stdClass;
    		$response->status = false;
    		$response->error  = $e->getMessage();
    		$response->code   = $e->getCode();

    		$status_code = $this->getRequest()->query->plupload ? 200 : ($e->getCode() && $e->getCode() <= 505 ? $e->getCode() : 500);

            $this->getResponse()
                ->setStatus($status_code)
                ->setContent(json_encode($response), 'application/json')
                ->send();
    	}
    	else throw $e;

        return false;
    }

    // FIXME: this is here because forwarded dispatchers still render results
    protected function _actionSend(KDispatcherContextInterface $context)
    {
        if (!$context->getRequest()->isGet() || $context->getResponse()->getContentType() !== 'text/html') {
            return parent::_actionSend($context);
        }
    }
}
