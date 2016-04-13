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
defined('_JEXEC') or die('Restricted access');

require_once dirname( __FILE__ ) . '/model.php';

class EasyDiscussModelPolls extends EasyDiscussAdminModel
{
    /**
     * Gets the total number of votes for a single poll
     *
     * @since   4.0
     * @access  public
     * @param   string
     * @return  
     */
    public function getTotalVotes($postId)
    {
        $db = $this->db;

        $query  = 'select count( distinct a1.`user_id` ) ';
        $query  .= ' from `#__discuss_polls_users` as a1 ';
        $query  .= '   inner join `#__discuss_polls` as b1 on a1.`poll_id` = b1.`id`';
        $query  .= ' where b1.`post_id` = ' . $db->Quote($postId);

        $db->setQuery($query);

        $total = $db->loadResult();

        return $total;
    }

    /**
     * Unvote a poll choice
     *
     * @since   4.0
     * @access  public
     * @param   string
     * @return  
     */
    public function unvoteChoice($choiceId, $userId)
    {
        $db = $this->db;
        $sessionId = JFactory::getSession()->getId();

        if ($userId == 0) {
            $where = ' WHERE ' . $db->qn('session_id') . '=' . $db->Quote($sessionId);
        } else {
            $where = ' WHERE ' . $db->qn('user_id') . '=' . $db->Quote($userId);
        }

        $query  = 'DELETE FROM ' . $db->qn('#__discuss_polls_users')
                . $where
                . ' AND ' . $db->qn('poll_id') . ' = ' . $db->Quote($choiceId);
        $db->setQuery( $query );
        
        return $db->Query();
    }

    /**
     * Retrieves a list of voters for a choice
     *
     * @since   4.0
     * @access  public
     * @param   string
     * @return  
     */
    public function getVoters(EasyDiscussPollChoice $choice, $limit = null)
    {
        $db = $this->db;

        $query  = 'SELECT `user_id` FROM ' . $db->qn('#__discuss_polls_users') . ' '
                . 'WHERE ' . $db->qn('poll_id') . '=' . $db->Quote($choice->id);

        if (!is_null($limit)) {
            $query  .= 'LIMIT 0,' . $limit;
        }

        $db->setQuery($query);
        $result = $db->loadColumn();

        if (!$result) {
            return $result;
        }

        // preload users
        ED::user($result);

        $users = array();

        foreach ($result as $row) {
            $user = ED::user($row);
            $users[] = $user;
        }

        return $users;
    }

    public function getAllVoters(EasyDiscussPollChoice $choice, $limit = null)
    {
        $db = $this->db;

        $query  = 'select distinct a.`user_id`,';
        $query  .= '  (select count( distinct a1.`user_id` ) ';
        $query  .= '          from `#__discuss_polls_users` as a1 ';
        $query  .= '               inner join `#__discuss_polls` as b1 on a1.`poll_id` = b1.`id`';
        $query  .= '           where b1.`post_id` = ' . $db->Quote($choice->post_id) . ' ) as `total`';
        $query  .= '  from `#__discuss_polls_users` as a';
        $query  .= '  inner join `#__discuss_polls` as b on a.`poll_id` = b.`id`';
        $query  .= ' where b.`post_id` = ' . $db->Quote($choice->post_id);

        if (!is_null($limit)) {
            $query .= ' LIMIT ' . $limit;
        }

        $db->setQuery($query);
        $result = $db->loadObjectList();

        if (!$result) {
            return $result;
        }

        $voters = array();

        foreach ($result as $item) {
            $voters[] = $item->user_id;
        }

        $items = array_unique($voters);

        // preload users
        ED::user($items);

        $users = array();
        
        foreach ($items as $item) {
            $user = ED::user($item);

            $users[] = $user;
        }

        return $users;
    }

    /**
     * Create a poll for a post
     *
     * @since   4.0
     * @access  public
     * @param   string
     * @return  
     */
    public function create($postId, $question, $choices = array(), $removePolls = false, $multiple = false, $original = array())
    {
        // Create a new poll question for this post.
        $table = ED::table('PollQuestion');
        $table->load(array('post_id' => $postId));

        // Set the poll items
        $table->post_id = $postId;
        $table->title = $question;
        $table->multiple = $this->config->get('main_polls_multiple') ? $multiple : false;

        // Store the main poll question.
        $table->store();

        // Get the list of poll choices to be removed
        if ($removePolls) {
            $ids = explode(',', $removePolls);

            foreach ($ids as $id) {
                $id = (int) $id;

                $poll = ED::table('Poll');
                $poll->load($id);
                $poll->delete();
            }
        }

        $i = 0;
        foreach ($choices as $choice) {

            // Ensure that the choice is a string
            $choice = (string) $choice;
            $choice = trim($choice);

            if (!$choice) {
                continue;
            }
    
            // Get the original choice
            $originalChoice = isset($original[$i]) ? $original[$i] : '';

            $poll = ED::table('Poll');
            $poll->post_id = $postId;

            // Update existing poll
            if ($originalChoice && $choice) {
                $poll->loadByValue($originalChoice, $postId);
            }

            $poll->value = $choice;
            $poll->store();

            $i++;
        }

        return $table;
    }

    /**
     * Updates the choice count
     *
     * @since   4.0
     * @access  public
     * @param   string
     * @return  
     */
    public function updateChoiceCount($choiceId)
    {
        $db = $this->db;

        $query = 'SELECT COUNT(1) FROM ' . $db->qn('#__discuss_polls_users' ) . ' '
                . 'WHERE ' . $db->qn('poll_id') . '=' . $db->Quote($choiceId);
        $db->setQuery($query);

        $count = $db->loadResult();

        $query = 'UPDATE ' . $db->qn('#__discuss_polls') . ' SET `count`=' . $db->Quote($count);
        $query .= ' WHERE ' . $db->qn('id') . '=' . $db->Quote($choiceId);

        $db->setQuery($query);
        return $db->Query();
    }

    /**
     * Determines if the user voted on a choice before
     *
     * @since   4.0
     * @access  public
     * @param   string
     * @return  
     */
    public function hasVotedChoice($choiceId, $userId)
    {
        $session = JFactory::getSession();
        $sessionId = $session->getId();
        $db = $this->db;

        if (!$userId) {
            $where = ' WHERE ' . $db->qn('session_id' ) . '=' . $db->Quote( $sessionId );
        } else {
            $where = ' WHERE ' . $db->qn('user_id' ) . '=' . $db->Quote( $userId );
        }

        $query  = 'SELECT ' . $db->qn('id') . ' FROM ' . $db->qn('#__discuss_polls_users')
                . $where
                . ' AND ' . $db->qn('poll_id' ) . ' = ' . $db->Quote($choiceId);
        
        $db->setQuery($query);
        $voted = $db->loadResult() > 0;

        return $voted;
    }

    /**
     * Retrieves a list of polls for a post
     *
     * @since   4.0
     * @access  public
     * @param   string
     * @return  
     */
    public function getChoices($postId, $userId = null)
    {
        $userId = JFactory::getUser($userId)->id;
        $db = $this->db;

        $query = 'SELECT a.*, count(b.`user_id`) as `meVoted`,';
        $query .= ' (select sum(`count`) from `#__discuss_polls` where `post_id`='. $db->Quote($postId) . ') as `totalVoted`';
        $query .= ' FROM ' . $db->qn('#__discuss_polls') . ' AS a';
        $query .= ' left join `#__discuss_polls_users` as b on a.`id` = b.`poll_id` and b.`user_id` = ' . $db->Quote($userId);

        if (!$userId) {
            $session = JFactory::getSession();
            $query .= ' AND b.session_id =' . $db->Quote($session->getId());
        }

        $query .= ' WHERE a.' . $db->qn('post_id') . '=' . $db->Quote($postId);
        $query .= ' GROUP BY a.' . $db->qn('id');
        $query .= ' ORDER BY a.' . $db->qn('id') . ' ASC';

        $db->setQuery($query);

        $items = $db->loadObjectList();

        if (!$items) {
            return false;
        }

        $choices = array();

        foreach ($items as $item) {

            $choice = ED::pollchoice($item);
            $choices[] = $choice;
        }

        return $choices;
    }

    /**
     * Gets the percentage of a choice
     *
     * @since   4.0
     * @access  public
     * @param   string
     * @return  
     */
    public function getPercentage(EasyDiscussPollChoice $choice)
    {
        $db = $this->db;

        $query  = 'SELECT COUNT(b.' . $db->qn('id') . ') FROM ' . $db->qn('#__discuss_polls') . ' AS a '
                . 'INNER JOIN ' . $db->qn('#__discuss_polls_users') . ' AS b '
                . 'ON a.' . $db->qn('id') . ' = b.' . $db->qn('poll_id') . ' '
                . 'WHERE a.' . $db->qn('post_id') . '=' . $db->Quote($choice->post_id);
        $db->setQuery($query);
        
        $total = $db->loadResult();

        if (!$total) {
            return 0;
        }

        $percentage = ($choice->count / $total) * 100;
        $percentage = round($percentage);

        return $percentage;
    }

    /**
     * Delete existing poll votes for a specific user
     *
     * @since   4.0
     * @access  public
     * @param   string
     * @return  
     */
    public function deleteExistingVotes($userId, $postId)
    {
        $session = JFactory::getSession();
        $sessionId = $session->getId();

        $db = $this->db;

        if ($userId == 0) {
            $where = 'WHERE ' . $db->qn('session_id') . '=' . $db->Quote($sessionId) . ' ';
        } else {
            $where = 'WHERE ' . $db->qn('user_id') . '=' . $db->Quote($userId) . ' ';
        }

        $query  = 'DELETE FROM ' . $db->qn('#__discuss_polls_users') . ' '
                . $where
                . 'AND ' . $db->qn('poll_id') . 'IN('
                . 'SELECT ' . $db->qn( 'id' ) . ' FROM ' . $db->qn( '#__discuss_polls' ) . ' '
                . 'WHERE ' . $db->qn('post_id') . '=' . $db->Quote($postId) . ' '
                . ')';
        $db->setQuery($query);
        
        return $db->Query();
    }

	/**
	 * Deletes polls related to a post
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function deletePolls($postId)
	{
        $db = $this->db;

        $query  = 'SELECT * FROM ' . $db->qn('#__discuss_polls') . ' '
                . 'WHERE ' . $db->qn('post_id') . '=' . $db->Quote($postId);

        $db->setQuery($query);
        $rows = $db->loadObjectList();

        if (!$rows) {
            return false;
        }

        foreach ($rows as $row) {
            $poll = ED::table('Poll');
            $poll->bind($row);
            $poll->delete();
        }
    
        // Remove any poll question if necessary.
        $pollQuestion = ED::table('PollQuestion');
        $exists = $pollQuestion->load(array('post_id' => $postId));

        if (!$exists) {
            return true;
        }

        return $pollQuestion->delete();
	}
}
