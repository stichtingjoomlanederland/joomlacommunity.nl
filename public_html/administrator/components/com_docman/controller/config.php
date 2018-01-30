<?php
/**
 * @package     DOCman
 * @copyright   Copyright (C) 2011 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

class ComDocmanControllerConfig extends ComKoowaControllerModel
{
    public function __construct(KObjectConfig $config)
    {
        parent::__construct($config);

        $this->addCommandCallback('after.save',   '_setRedirect');
        $this->addCommandCallback('after.apply',  '_setRedirect');
        $this->addCommandCallback('after.cancel', '_setRedirect');
        $this->addCommandCallback('before.render', '_checkThumbnailsSupport');
    }

    /**
     * We always need to call edit since config is never new
     */
    protected function _actionAdd(KControllerContextInterface $context)
    {
        return $this->_actionEdit($context);
    }

    /**
     * Avoid getting redirected to the configs, export, or import views
     */
    protected function _setRedirect(KControllerContextInterface $context)
    {
        $response = $context->getResponse();

        if ($response->isRedirect())
        {
            $url = $response->getHeaders()->get('Location');
            if (preg_match('#view=(configs|export|import)#', $url, $matches)) {
                $response->setRedirect(str_replace('view='.$matches[1], 'view=documents', $url));
            }
        }
    }

    protected function _checkThumbnailsSupport(KControllerContextInterface $context)
    {
        $thumbnails_available = $this->getObject('com://admin/docman.model.configs')->fetch()
                                           ->thumbnailsAvailable();

        $this->getView()->thumbnails_available = $thumbnails_available;

        if (!$thumbnails_available)
        {
            $message = $this->getObject('translator')->translate('GD missing');
            $this->getResponse()->addMessage($message, 'warning');
        }
    }
}
