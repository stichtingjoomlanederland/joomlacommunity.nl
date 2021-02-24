<?php
/**
 * @package     DOCman
 * @copyright   Copyright (C) 2011 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

/**
 * Serves a file to the user
 */
class ComDocmanControllerBehaviorPreviewable extends ComKoowaControllerBehaviorEditable
{
    protected static $_gdocs_extensions = array(
        'ogg', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'pages', 'ai',
        'psd', 'tiff', 'dxf', 'svg', 'eps', 'ps', 'ttf', 'xps'
    );

    public function getGooglePreviewExtensions()
    {
        return static::$_gdocs_extensions;
    }

    protected function _initialize(KObjectConfig $config)
    {
        $config->append(array(
            'priority' => KCommandHandlerInterface::PRIORITY_HIGH
        ));

        parent::_initialize($config);
    }

    protected function _beforeRender(KControllerContextInterface $context)
    {
        if ($this->canPreview() && $this->canRead())
        {
            $secret = JFactory::getConfig()->get('secret');
            $token  = $this->getObject('lib:http.token')->setSubject($context->user->getUsername());

            $url = clone $this->getObject('request')->getUrl();
            $url->query['force_download'] = 1;

            //If the user is logged in add the authentication token
            if($context->user->isAuthentic()) {
                $url->query['auth_token'] = $token->sign($secret);
            }

            $redirect = sprintf('https://docs.google.com/viewer?url=%s', urlencode($url));

            $context->response->setRedirect($redirect);
            return false;
        }

        //Need to reset the 'current_user' state in the model.
        $this->current_user($context->user->getId());
    }

    /**
     * Returns true if Google viewer is enabled and works for the current document type
     *
     * @return bool
     */
    public function canPreview()
    {
        $result   = false;
        $document = $this->getModel()->fetch();

        if ($document instanceof KModelEntityInterface && !$document->isNew())
        {
            $params = JFactory::getApplication()->getMenu()->getActive()->params;

            if (!$params->get('force_download') && $params->get('preview_with_gdocs'))
            {
                if (!$this->getRequest()->query->has('force_download') && $document->storage_type === 'file')
                {
                    if (in_array($document->storage->extension, self::$_gdocs_extensions)) {
                        $result = true;
                    }
                }
            }
        }

        return $result;
    }
}