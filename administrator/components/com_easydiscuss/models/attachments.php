<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 Stack Ideas Private Limited. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Restricted access');

require_once dirname( __FILE__ ) . '/model.php';

class EasyDiscussModelAttachments extends EasyDiscussAdminModel
{
    /**
     * Gets a list of attachments on the site
     *
     * @since   4.0
     * @access  public
     * @param   string
     * @return  
     */
    public function getAttachments($excludeStorage = '', $limit = 20)
    {
        $db = $this->db;

        $query = 'SELECT * FROM ' . $db->qn('#__discuss_attachments');
        $query .= ' WHERE ' . $db->qn('storage') . ' != ' . $db->Quote($excludeStorage);
        $query .= ' LIMIT ' . $limit;
        $db->setQuery($query);

        $items = $db->loadObjectList();

        if (!$items) {
            return array();
        }

        $attachments = array();

        foreach ($items as $item) {
            $attachment = ED::attachment($item);

            $attachments[] = $attachment;
        }
        
        return $attachments;
    }

    /**
     * Get a list of attachments for a post
     *
     * @since	4.0
     * @access	public
     * @param	string
     * @return	
     */
    public function getPostAttachments($postId)
    {
    	$db = $this->db;

        $query  = 'SELECT * FROM ' . $db->qn( '#__discuss_attachments' ) . ' '
                . 'WHERE ' . $db->qn('uid') . '=' . $db->Quote($postId) . ' '
                . 'AND ' . $db->nameQuote('published') . '=' . $db->Quote(1);

    	$db->setQuery($query);

        $result = $db->loadObjectList();

        if (!$result) {
        	return $result;
        }

        $attachments = array();

        foreach ($result as $row) {
            $attachment = ED::attachment($row);
        	$attachments[] = $attachment;
        }

        return $attachments;
    }

    public function getAttachmentOwner($attachmentId)
    {
        $data = $this->getAttachementPostDetails($attachmentId);
        return $data['user_id'];
    }

    public function getAttachmentPostId($attachmentId)
    {
        $data = $this->getAttachementPostDetails($attachmentId);
        return $data['post_id'];
    }

    public function getAttachmentPostParentId($attachmentId)
    {
        $data = $this->getAttachementPostDetails($attachmentId);
        return $data['parent_id'];
    }

    public function getAttachementPostDetails($attachmentId)
    {
        static $load = array();

        if (isset($load[$attachmentId])) {
            return $load[$attachmentId];
        }

        $db = $this->db;
        $query  = 'SELECT p.user_id, p.`id` as `post_id`, p.`parent_id` as `parent_id` '
                . ' FROM `#__discuss_attachments` AS a'
                . ' LEFT JOIN `#__discuss_posts` AS p ON p.id = a.uid'
                . ' WHERE a.id = ' . $db->quote((int)$attachmentId)
                . ' LIMIT 1';

        $db->setQuery($query);
        $result = $db->loadAssoc();

        if (empty($result)) {
            $result['user_id'] = null;
            $result['post_id'] = null;
            $result['parent_id'] = null;
        }

        return $result;
    }
}
