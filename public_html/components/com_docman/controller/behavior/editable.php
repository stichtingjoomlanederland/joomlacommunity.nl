<?php
/**
 * @package    DOCman
 * @copyright   Copyright (C) 2011 Timble CVBA (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

class ComDocmanControllerBehaviorEditable extends ComKoowaControllerBehaviorEditable
{
    protected $_old_slug;

    /**
     * Append slug instead of the identity column to the re-direct URL when dealing with sluggable entities.
     *
     * @param KControllerContextInterface $context
     */
    protected function _afterAdd(KControllerContextInterface $context)
    {
        $entity = $context->result;

        if ($entity instanceof KModelEntityInterface && !$this->getModel()->getState()->isUnique())
        {
            $url = $this->getObject('lib:http.url', array('url' => $context->response->headers->get('Location')));

            unset($url->query[$entity->getIdentityColumn()]);

            $url->query['slug'] = $entity->slug;

            $context->response->headers->set('Location', (string) $url);
        }
    }

    /**
     * Saves the old slug to compare to the new one after apply event
     *
     * This is used to make sure the redirect URL will use the new slug
     *
     * @param KControllerContextInterface $context
     */
    protected function _beforeApply(KControllerContextInterface $context)
    {
        $action = $this->getModel()->getState()->isUnique() ? 'edit' : 'add';

        if ($action === 'edit') {
            $this->_old_slug = $this->getModel()->fetch()->slug;
        }
    }

    protected function _beforeSave(KControllerContextInterface $context)
    {
        $this->_beforeApply($context);
    }

    /**
     * Makes sure the redirect URL won't be unauthorized or a nonexistent one
     *
     * @param KControllerContextInterface $context
     */
    protected function _afterApply(KControllerContextInterface $context)
    {
        $entity = $context->result;

        // User cannot edit the document she just added
        if ($entity instanceof KModelEntityInterface && $entity->isPermissible() && !$entity->canPerform('edit'))
        {
            $translator = $this->getObject('translator');

            $message    = $translator->translate('Document saved');
            $context->getResponse()->addMessage($message, KControllerResponse::FLASH_SUCCESS);

            $message    = $translator->translate('You have been redirected to this page since you do not have permissions to edit the document that you have just created.');
            $context->getResponse()->addMessage($message, KControllerResponse::FLASH_SUCCESS);

            $context->response->setRedirect($this->getReferrer($context));
        }

        $this->_updateReferrer($context);
    }

    protected function _afterSave(KControllerContextInterface $context)
    {
        $this->_updateReferrer($context);
    }

    /**
     * Entity slug has been changed which means the old URL will lead to 404. Rewrite it.
     * @todo this needs to be cleaned up
     * @param KControllerContextInterface $context
     */
    protected function _updateReferrer(KControllerContextInterface $context)
    {
        $entity = $context->result;

        if ($entity instanceof KModelEntityInterface && $this->_old_slug && $this->_old_slug !== $entity->slug)
        {
            $referrer  = $context->getRequest()->getReferrer();
            $old_alias = $entity->id.'-'.$this->_old_slug;
            $new_alias = $entity->id.'-'.$entity->slug;

            if (strpos($referrer, $old_alias) !== false)
            {
                $referrer = strtr($referrer, array(
                    $old_alias => $new_alias,
                    $this->_old_slug => $entity->slug
                ));

            } elseif (strpos($referrer, $this->_old_slug) !== false) {
                $referrer->query['slug'] = $entity->slug;
            }

            // Set the referrer in request and as the redirect
            $context->request->setReferrer($referrer);

            $referrer_cookie = $this->getReferrer($context);
            if (strpos($referrer_cookie, $old_alias) !== false)
            {
                // Also update referrer stored in the cookie
                $referrer_cookie = strtr($referrer_cookie, array(
                    $old_alias => $new_alias,
                    $this->_old_slug => $entity->slug
                ));

                // Unset the old referrer since it's a 404 now
                $this->_unsetReferrer($context);

                // Temporarily change the referrer so that setReferrer can grab it
                $context->request->setReferrer($referrer_cookie);
                $this->setReferrer($context);
                $context->request->setReferrer($referrer);
            }

            if ($context->action === 'apply' || $entity->getStatus() === KModelEntityInterface::STATUS_FAILED)
            {
                $context->response->setRedirect($referrer);
            } else {
                $context->response->setRedirect($referrer_cookie);
            }
        }
    }
}