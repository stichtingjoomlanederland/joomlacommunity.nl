<?php
/**
 * @package     DOCman
 * @copyright   Copyright (C) 2011 - 2016 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

/**
 * Notify plugin for DOCman
 */
class PlgDocmanNotify extends PlgKoowaSubscriber
{
    /**
     * Maximum number of users to notify.
     *
     * If the number of users is more than this, only the owner will be notified and a warning displayed.
     *
     * @var int
     */
    const USERS_LIMIT = 25;

    /**
    * notification title key
    *
    * @var string
    */
    protected static $_title_key = 'PLG_DOCMAN_NOTIFY_DOCUMENT_%s_TITLE';

    /**
    * A list of previously enabled documents
    *
    * @var array
    */
    protected $_previous_enabled = array();

    /**
    * A list of previously disabled documents
    *
    * @var array
    */
    protected $_previous_disabled = array();

    /**
     * A list of previous document owners
     *
     * @var array
     */
    protected $_previous_owners = array();

    public function onAfterDocmanDocumentControllerAdd(KEventInterface $event)
    {
        if ($this->params->get('notify_add', 1)) {
            $this->_sendMail($event);
        }
    }

    public function onBeforeDocmanDocumentControllerEdit(KEventInterface $event)
    {
        if ($this->params->get('notify_publish', 1) || $this->params->get('notify_assign', 1))
        {
            $documents = $event->getTarget()->getModel()->fetch();

            // Keep track of document owners prior editing.
            foreach ($documents->getIterator() as $document)
            {
                if($this->params->get('notify_assign', 1))
                {
                    if (!$document->isNew())
                    {
                        if (!isset($this->_previous_owners[$document->id])) {
                            $this->_previous_owners[$document->id] = array();
                        }

                        $this->_previous_owners[$document->id][] = $document->created_by;
                    }
                }

                if($this->params->get('notify_publish', 1))
                {
                    if ($document->enabled) {
                        $this->_previous_enabled[] = $document->id;
                    } else {
                        $this->_previous_disabled[] = $document->id;
                    }
                }
            }
        }
    }

    public function onAfterDocmanDocumentControllerEdit(KEventInterface $event)
    {
        $edit    = $this->params->get('notify_edit', 1);
        $assign  = $this->params->get('notify_assign', 1);
        $publish = $this->params->get('notify_publish', 1);

        if ($edit || $assign || $publish) {
            $this->_sendMail($event);
        }
    }

    public function onAfterDocmanDocumentControllerDelete(KEventInterface $event)
    {
        if ($this->params->get('notify_delete', 1)) {
            $this->_sendMail($event);
        }
    }

    public function onAfterDocmanSubmitControllerAdd(KEventInterface $event)
    {
        if ($this->params->get('notify_submit', 1)) {
            $this->_sendMail($event);
        }
    }

    public function onAfterDocmanDownloadControllerRender(KEventInterface $event)
    {
        if ($this->params->get('notify_download', 1)) {
            $this->_sendMail($event);
        }
    }

    protected function _sendMail(KEventInterface $event)
    {
        $limit_exceeded = false;
        $messages = array();

        if (!$event->result instanceof KModelEntityInterface) {
          $result = $event->target->getModel()->fetch();
        } else {
          $result = $event->result;
        }

        $this->loadLanguage();

        foreach ($result->getIterator() as $document)
        {
            $users  = $this->_getDocumentUsers($document);
            $owners = $this->_getDocumentOwners($document);

            //Create the users
            if (count($users) > self::USERS_LIMIT) {
                $limit_exceeded = count($users);
                $users = $owners;
            } else {
                $users = array_merge($owners, $users);
            }

            $users = array_unique($users);

            //Prepare the messages
            foreach ($users as $user)
            {
                $user = $this->getObject('user.provider')->load($user);

                if ($this->_canMail($user))
                {
                    $list = array();

                    if ($event->action == 'edit')
                    {
                        //Notify publish
                        if($this->params->get('notify_publish', 1))
                        {
                            if($message = $this->_getMessagePublish($document, $user)) {
                                $list[] = $message;
                            }
                        }

                        //Notify assign
                        if($user->getId() == $document->created_by && $this->params->get('notify_assign', 1))
                        {
                            if($message = $this->_getMessageAssign($document, $user)) {
                                $list[] = $message;
                            }
                        }

                        // Check to avoid edit notifications when assign/publish is enabled and edit is disabled
                        if ($this->params->get('notify_edit', 1))
                        {
                            if($message = $this->_getMessage($document, $user, $event->action)) {
                                $list[] = $message;
                            }
                        }
                    }
                    else
                    {
                        //Notify action
                        if($message = $this->_getMessage($document, $user, $event->action)) {
                            $list[] = $message;
                        }
                    }

                    //Store the messages per user
                    $messages[$user->getEmail()] = $list;
                }
            }
        }

        if (count($messages))
        {
            //Send the messages
            $from_name  = JFactory::getConfig()->get('fromname');
            $from_mail  = JFactory::getConfig()->get('mailfrom');

            $debug = JFactory::getApplication()->getCfg('debug');

            $notifications = array('success' => array(), 'error' => array());

            foreach($messages as $to_mail => $list)
            {
                foreach($list as $message)
                {
                    $result = JFactory::getMailer()->sendMail($from_mail, $from_name, $to_mail, $message['title'], $message['body'], true);

                    if ($debug)
                    {
                        $title = $message['title'];
                        $status = $result ? 'success' : 'error';

                        if (!in_array($title, array_keys($notifications[$status]))) {
                            $notifications[$status][$title] = array();
                        }

                        $notifications[$status][$title][] = $to_mail;
                    }
                }
            }

            if ($debug)
            {
                $this->_postDebugNotifications($notifications);
                $this->_mailDebugNotifications($notifications);

                //Send the user limit warning
                if($limit_exceeded !== false)
                {
                    $message = $this->getObject('translator')->translate('PLG_DOCMAN_NOTIFY_WARNING_USER_LIMIT');
                    $message = sprintf($message, $limit_exceeded, self::USERS_LIMIT);

                    $this->getObject('response')->addMessage($message, 'warning');
                }
            }
        }
    }

    protected function _postDebugNotifications($notifications)
    {
        $response = $this->getObject('response');

        foreach ($notifications as $status => $status_notifications)
        {
            foreach ($status_notifications as $title => $recipients)
            {
                $message = $this->getDebugNotification($title, $status, $recipients);

                if ($status == 'success') {
                    $type = KControllerResponseInterface::FLASH_SUCCESS;
                } else {
                    $type = KControllerResponseInterface::FLASH_ERROR;
                }

                $response->addMessage($message, $type);
            }
        }
    }

    protected function _mailDebugNotifications($notifications)
    {
        $query = $this->getObject('lib:database.query.select')
                      ->table('users')
                      ->columns('*')
                      ->where('sendEmail = :sendEmail')
                      ->bind(array('sendEmail' => 1));

        $users = $this->getObject('lib:database.adapter.mysqli')->select($query, KDatabase::FETCH_OBJECT_LIST);

        $translator = $this->getObject('translator');

        $from_name  = JFactory::getConfig()->get('fromname');
        $from_mail  = JFactory::getConfig()->get('mailfrom');

        $subject = $translator->translate('PLG_DOCMAN_NOTIFY_DEBUG_EMAIL_SUBJECT');

        $template = $this->getObject('com:koowa.view.html')->getTemplate();

        $template->registerFunction('renderNotification', array($this, 'getDebugNotification'));

        $template->loadString($this->_getTemplateContent('debug.html.php'), 'php');

        foreach ($users as $user)
        {
            $message = $template->render(array('notifications' => $notifications));
            JFactory::getMailer()->sendMail($from_mail, $from_name, $user->email, $subject, $message, true);
        }
    }

    protected function _getTemplateContent($name)
    {
        static $template;

        if (!isset($template))
        {
            $query = $this->getObject('lib:database.query.select')->table('template_styles')
                          ->columns('template')
                          ->where('client_id = :client')
                          ->where('home = :home')
                          ->bind(array('client' => 0, 'home' => 1));

            $template = $this->getObject('lib:database.adapter.mysqli')->select($query, KDatabase::FETCH_FIELD);
        }

        $files = array(
            sprintf(JPATH_ROOT . '/templates/%s/html/plg_docman_notify/%s', $template, $name),
            sprintf(JPATH_PLUGINS . '/docman/notify/view/%s', $name)
        );

        foreach ($files as $file)
        {
            if (file_exists($file)) {
                $content = file_get_contents($file);
                break;
            }
        }

        return $content;
    }

    public function getDebugNotification($title, $status, $recipients)
    {
        $choices = array(
            sprintf('PLG_DOCMAN_NOTIFY_DEBUG_%s_SINGLE', strtoupper($status)),
            sprintf('PLG_DOCMAN_NOTIFY_DEBUG_%s_MULTIPLE', strtoupper($status))
        );

        $count = count($recipients);

        return $this->getObject('translator')->choose($choices, $count, array(
            'recipients' => $count,
            'subject'    => $title
        ));
    }


    protected function _canMail(KUserInterface $user)
    {
        if(!empty($user) && $user->isEnabled() && $user->getEmail())
        {
            // No need to notify the user for their own actions
            if ($user->getId() !== $this->getObject('user')->getId()) {
                return true;
            }
        }

        return false;
    }

    protected function _getMessage(ComDocmanModelEntityDocument $document, KUserInterface $user, $action)
    {
        $translator = $this->getObject('translator');
        $sitename    = JFactory::getConfig()->get('sitename');
        $url         = null;

        if($action !== 'delete') {
            $url = $this->_getDocumentURL($document, $user);
        }

        $message = array();

        $message['title'] = $translator->translate(sprintf(self::$_title_key, $action), array(
            'name'     => $user->getName(),
            'title'    => $document->title,
            'sitename' => $sitename
        ));

        $template = $this->getObject('com:koowa.view.html')->getTemplate();

        $template->loadString($this->_getTemplateContent('default.html.php'), 'php');

        $message['body'] = $template->render(array(
            'action'   => $action,
            'name'     => $user->getName(),
            'title'    => $document->title,
            'sitename' => $sitename,
            'url'      => $url
        ));

        return $message;
    }

    protected function _getMessageAssign(ComDocmanModelEntityDocument $document, KUserInterface $user)
    {
        $result = false;

        $previous_owner = array_pop($this->_previous_owners[$document->id]);

        if($document->created_by !== $previous_owner) {
            $result = $this->_getMessage($document, $user, 'assign');
        }

        return $result;
    }

    protected function _getMessagePublish(ComDocmanModelEntityDocument $document, KUserInterface $user)
    {
        $result = false;

        $disabled = in_array($document->id, $this->_previous_disabled);
        $enabled  = in_array($document->id, $this->_previous_enabled);

        if($document->enabled == 1 && $disabled) {
            $result = $this->_getMessage($document, $user, 'publish');
        }

        if($document->enabled == 0 && $enabled) {
            $result = $this->_getMessage($document, $user, 'unpublish');
        }

        return $result;
    }

    protected function _getDocumentURL(ComDocmanModelEntityDocument $document, KUserInterface $user)
    {
        $result = null;

        $itemid = $document->itemid;

        if (!$itemid) {
            $this->getObject('com://admin/docman.model.documents')->page('all')->setPage($document);

            $itemid = $document->itemid;
        }

        $levels = JAccess::getAuthorisedViewLevels($user->getId());
        $menu = JApplication::getInstance('site')->getMenu()->getItem($itemid);

        if (in_array($menu->access, $levels))
        {
            $template = 'index.php?option=com_docman&view=document&alias=%s&category_slug=%s&Itemid=%d';
            $result = sprintf($template, $document->alias, $document->category_slug, $itemid);
        }

        if ($result)
        {
            $result = JApplication::getInstance('site')->getRouter()->build($result);

            // workaround to remove base /subdirectories incuding /administrator from te link sent to the user
            $count = 1; // replace only once
            $result = str_replace(JURI::base(true), '', $result, $count);

            // make it a full URL
            $result = rtrim(JURI::root(), '/').$result;
        }
        else
        {
            // send an admin link if possible
            $jUser = JFactory::getUser($user->getId());
            if ($jUser->authorise('core.login.admin') && $jUser->authorise('core.manage', 'com_docman')) {
                $result = JURI::root().sprintf('administrator/index.php?option=com_docman&view=document&id=%d', $document->id);
            }
        }

        return $result;
    }

    protected function _getDocumentUsers(ComDocmanModelEntityDocument $document)
    {
        $users = array();

        //Notify the document users
        if ($this->params->get('notify_document_group', 0))
        {
            $access   = $document->access;
            $resource = $document;

            if ($access == 0)
            {
                $resource = $document->category;
                $access   = $resource->access;
            }

            if ($access < 0) {
                $groups = array_keys($resource->getGroups()); // Grab from permissible.
            } else {
                $query = $this->getObject('lib:database.query.select')
                              ->table('viewlevels')
                              ->columns('rules')
                              ->where('id = :id')
                              ->bind(array('id' => $access));

                $groups = json_decode($this->getObject('lib:database.adapter.mysqli')
                                           ->select($query, KDatabase::FETCH_FIELD));
            }

            foreach ($groups as $group) {
                $users = array_merge($users, JAccess::getUsersByGroup($group));
            }
        }

        return $users;
    }

    protected function _getDocumentOwners(ComDocmanModelEntityDocument $document)
    {
        $users = array();

        //Notify the document owner
        if ($this->params->get('notify_document', 1)) {
            $users[] = $document->created_by;
        }

        //Notify the category owner
        if ($this->params->get('notify_category', 1)) {
            $users[] = $document->category->created_by;
        }

        return $users;
    }
}
