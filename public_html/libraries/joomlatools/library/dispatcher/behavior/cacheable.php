<?php
/**
 * Joomlatools Framework - https://www.joomlatools.com/developer/framework/
 *
 * @copyright   Copyright (C) 2007 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/joomlatools/joomlatools-framework for the canonical source repository
 */

/**
 * Cacheable Dispatcher Behavior
 *
 * Handle HTTP caching and validaiton. The caching logic, based on RFC 7234, uses HTTP headers to control caching
 * behavior, cache lifetime and ETag based revalidation.
 *
 * @link https://tools.ietf.org/html/rfc7234
 *
 * @author  Johan Janssens <http://github.com/johanjanssens>
 * @package Koowa\Library\Dispatcher\Behavior
 */
class KDispatcherBehaviorCacheable extends KControllerBehaviorAbstract
{
    /**
     * Initializes the options for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param  KObjectConfig $config A ObjectConfig object with configuration options
     * @return void
     */
    protected function _initialize(KObjectConfig  $config)
    {
        $config->append(array(
            'priority' => self::PRIORITY_LOW,
            'cache'         => true,
            'cache_private' => false,
            'cache_time'    => 0, //must revalidate
        ));

        parent::_initialize($config);
    }

    /**
     * Mixin Notifier
     *
     * This function is called when the mixin is being mixed. It will get the mixer passed in.
     *
     * @param KObjectMixable $mixer The mixer object
     * @return void
     */
    public function onMixin(KObjectMixable $mixer)
    {
        parent::onMixin($mixer);

        //Set max age default
        $this->getMixer()->getResponse()->setMaxAge($this->getConfig()->cache_time);
    }

    /**
     * Check if the behavior is supported
     *
     * @return  boolean  True on success, false otherwise
     */
    public function isSupported()
    {
        return $this->isCacheable() ? parent::isSupported() : false;
    }

    /**
     * Check if the response can be cached
     *
     * @return  boolean  True on success, false otherwise
     */
    public function isCacheable()
    {
        $mixer   = $this->getMixer();
        $request = $mixer->getRequest();

        $cacheable = false;
        if($request->isCacheable() && $this->getConfig()->cache)
        {
            $cacheable = true;

            if(!$this->getConfig()->cache_private && $mixer->getUser()->isAuthentic()) {
                $cacheable = false;
            }
        }

        return $cacheable;
    }

    /**
     * Send HTTP response
     *
     * Prepares the Response before it is sent to the client. This method set the cache control headers to ensure that
     * it is compliant with RFC 2616 and calculates an etag for the response
     *
     * @link http://tools.ietf.org/html/rfc2616
     *
     * @param 	KDispatcherContextInterface $context The active command context
     */
    protected function _beforeSend(KDispatcherContextInterface $context)
    {
        $response = $context->getResponse();
        $request  = $context->getRequest();

        if($this->isCacheable())
        {
            $response->headers->set('Cache-Control', $this->_getCacheControl());

            //Set Validator
            $response->setEtag($this->_getEtag(), true);

            //Determines if the response etag match a conditional value specified in the request.
            if ($etags = $request->getEtags())
            {
                if(in_array($response->getEtag(), $etags) || in_array('*', $etags)) {
                    $response->setStatus(KHttpResponse::NOT_MODIFIED);
                }
            }
        }
    }

    /**
     * Get the cache control directives
     *
     * @link https://tools.ietf.org/html/rfc7234#page-21
     *
     * @return array
     */
    protected function _getCacheControl()
    {
        $cache = array();
        $response = $this->getResponse();

        if($response->getUser()->isAuthentic()) {
            $cache = array('private');
        } else {
            $cache = array('public');
        }

        if(0 == $cache['max-age'] = $response->getMaxAge()) {
            $cache[] = 'no-cache';
        };

        return $cache;
    }

    /**
     * Generate a response etag
     *
     * For files returns a md5 hash of same format as Apache does. Eg "%ino-%size-%0mtime" using the file
     * info, otherwise return a crc32 digest of the response content and the user identifier
     *
     * @link http://stackoverflow.com/questions/44937/how-do-you-make-an-etag-that-matches-apache
     *
     * @return string
     */
    protected function _getEtag()
    {
        $response = $this->getResponse();

        if($response->isDownloadable())
        {
            $info = $response->getStream()->getInfo();
            $etag = sprintf('"%x-%x-%s"', $info['ino'], $info['size'],base_convert(str_pad($info['mtime'],16,"0"),10,16));
        }
        else $etag = crc32($this->getUser()->getId().'/###'.$this->getResponse()->getContent());

        return $etag;
    }
}