<?php
/**
 * @package     JoomlatoolsUpdater
 * @copyright   Copyright (C) 2021 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

class PlgSystemJoomlatoolsupdaterEventSubscriberLicense extends KEventSubscriberAbstract
{
    protected $_error_added = false;

    protected function _initialize(KObjectConfig $config)
    {
        $config->append(array(
            'priority' => KEvent::PRIORITY_HIGH
        ));

        parent::_initialize($config);
    }

    public function onBeforeDispatcherDispatch(KEventInterface $event)
    {
        try {
            if (substr($event->getTarget()->getIdentifier()->getPackage(), -3) === 'man') {
                /** @var PlgSystemJoomlatoolsupdaterLicense $license */
                $license = $this->getObject('license');
                $license->load();
            }
        } catch (Exception $e) {
            if (JDEBUG) throw $e;
        }
    }

    public function onBeforeDispatcherSend(KEventInterface $event)
    {
        // TODO: turn this on for a future release
        return;

        try {
            if (!$this->_error_added && JFactory::getDocument()->getType() === 'html'
                && JFactory::getApplication()->isClient('administrator')
                && substr($event->getTarget()->getIdentifier()->getPackage(), -3) === 'man'
            ) {
                $view = $event->getTarget()->getRequest()->getQuery()->get('view', 'cmd');

                // Only show the messages for these views so we don't accumulate them in the session without showing them in views like editor
                if (in_array($view, ['documents', 'categories', 'tags', 'usergroups', 'config'])) {
                    /** @var PlgSystemJoomlatoolsupdaterLicense $license */
                    $license = $this->getObject('license');

                    if (!$license->load()) {
                        if ($license->hasError()) {
                            $error = $license->getError();
                        }
                        else if (!$this->isValid()) {
                            $error = 'Your license has expired';
                        } else {
                            $error = 'Your license could not be validated';
                        }

                        $error = sprintf('License error: %1$s. If you think this is a mistake you can download the extension from <a href="%2$s" target="_blank">our dashboard</a> 
and install it again to renew your license. You can also contact us on <a href="%2$s" target="_blank">our dashboard</a> if the problem is not resolved.', $error, 'https://dashboard.joomlatools.com');

                        $this->getObject('response')->addMessage($error, KControllerResponse::FLASH_ERROR);
                        $this->_error_added = true;
                    }
                }

            }
        } catch (Exception $e) {
            if (JDEBUG) throw $e;
        }


    }
}