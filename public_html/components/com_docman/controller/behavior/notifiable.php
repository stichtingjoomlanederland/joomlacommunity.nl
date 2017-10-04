<?php
/**
 * @package     FILEman
 * @copyright   Copyright (C) 2011 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

class ComDocmanControllerBehaviorNotifiable extends KControllerBehaviorAbstract
{
    protected function _afterAdd(KControllerContextInterface $context)
    {
        if ($context->getResponse()->getStatusCode() == KHttpResponse::CREATED)
        {
            $page = JFactory::getApplication()->getMenu()->getItem($this->getRequest()->query->Itemid);
            $translator = $this->getObject('translator');

            $emails = $page->params->get('notification_emails');
            if (!empty($emails))
            {
                $emails = explode("\n", $emails);

                $config	= JFactory::getConfig();
                $from_name = $config->get('fromname');
                $mail_from = $config->get('mailfrom');
                $sitename = $config->get('sitename');
                $subject   = $translator->translate('A new document was submitted for you to review on {sitename}', array(
                    'sitename' => $sitename
                ));

                $admin_link  = JURI::root().'administrator/index.php?option=com_docman&view=documents';
                $title       = $context->result->title;
                $admin_title = $translator->translate('Document Manager');

                $template = $this->getObject('com:koowa.view.html')->getTemplate();

                foreach ($emails as $email)
                {
                    $template->loadFile('com://site/docman.email.upload.html', 'php');

                    $body = $template->render(array(
                        'email'    => $email,
                        'title'    => $title,
                        'sitename' => $sitename,
                        'url'      => $admin_link,
                        'url_text' => $admin_title
                    ));

                    JFactory::getMailer()->sendMail($mail_from, $from_name, $email, $subject, $body, true);
                }
            }
        }
    }
}
