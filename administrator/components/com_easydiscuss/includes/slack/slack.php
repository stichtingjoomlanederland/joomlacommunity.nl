<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

class EasyDiscussSlack extends EasyDiscuss
{
    /**
     * Creates a new gist anonymously
     *
     * @since   4.0
     * @access  public
     * @param   string
     * @return  
     */
    public function share(EasyDiscussPost $post)
    {
        // Load front end's language
        ED::loadLanguages();

        // Ensure that the integrations is enabled
        if (!$this->config->get('integrations_slack')) {
            return false;
        }

        $data = array();
        $data['username'] = $this->config->get('integrations_slack_bot');
        $data['text'] = $this->prepareMessage($post);

        $fields = 'payload=' . urlencode(json_encode($data));

        // Get the url
        $url = $this->config->get('integrations_slack_webhook');

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");                                                                     
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $output = curl_exec($ch);
        curl_close($ch);

        if (!$output) {
            return false;
        }

        return $output == "ok";
    }

    /**
     * Prepares the message
     *
     * @since   4.0
     * @access  public
     * @param   string
     * @return  
     */
    public function prepareMessage(EasyDiscussPost $post)
    {
        $message = $this->config->get('integrations_slack_message');
        $message = str_ireplace('{title}', $post->title, $message);
        $message = str_ireplace('{permalink}', $post->getPermalink(true), $message);

        return $message;
    }
}
