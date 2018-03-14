<?php
/**
 * @package     DOCman
 * @copyright   Copyright (C) 2011 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

class ComDocmanControllerBehaviorOwnable extends KControllerBehaviorAbstract
{
    /**
     * Make sure user is logged in if the menu item is for owner's documents
     *
     * @param KControllerContextInterface $context
     * @throws Exception
     */
    protected function _beforeRender(KControllerContextInterface $context)
    {
        $request = $context->getRequest();
        $own     = $request->query->own;

        if ($own && !$context->user->isAuthentic())
        {
            $message  = $this->getObject('translator')->translate('You need to be logged in to access your document list');
            $url      = $context->getRequest()->getUrl();
            $redirect = JRoute::_('index.php?option=com_users&view=login&return='.base64_encode($url), false);

            JFactory::getApplication()->redirect($redirect, $message, 'error');
        }
    }
}