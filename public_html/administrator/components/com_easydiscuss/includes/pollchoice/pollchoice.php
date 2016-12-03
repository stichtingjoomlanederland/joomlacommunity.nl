<?php
/**
* @package      EasyDiscuss
* @copyright    Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

class EasyDiscussPollChoice extends EasyDiscuss
{
    private $table = null;

    public function __construct($poll, $options = array())
    {
        parent::__construct();

        // The $post must always be a table.
        $this->table = ED::table('Poll');

        // If passed in argument is an integer, we load it
        if (is_numeric($poll)) {
            $this->table->load($poll);
        }

        // If passed in argument is already a post, table just assign it.
        if ($poll instanceof DiscussPoll) {
            $this->table = $poll;
        }

        if (is_object($poll)) {
            $this->table->bind($poll);
        }
    }

    /**
     * Magic method to get properties which don't exist on this object but on the table
     *
     * @since   4.0
     * @access  public
     * @param   string
     * @return
     */
    public function __get($key)
    {
        if (isset($this->table->$key)) {
            return $this->table->$key;
        }

        if (isset($this->$key)) {
            return $this->$key;
        }

        return $this->table->$key;
    }

    /**
     * Creates a new vote
     *
     * @since   4.0
     * @access  public
     * @param   string
     * @return  
     */
    public function createVote($userId)
    {
        // Create a new vote
        $vote = ED::table('PollUser');
        $vote->poll_id = $this->table->id;
        $vote->user_id = $userId;
        $vote->session_id = JFactory::getSession()->getId();
        $vote->store();

        return $vote;
    }

    /**
     * Get the poll item
     *
     * @since   4.0
     * @access  public
     * @param   string
     * @return  
     */
    public function getPoll()
    {
        static $items = array();

        if (!isset($items[$this->table->id])) {
            $poll = ED::table('PollQuestion');
            $poll->load(array('post_id' => $this->table->post_id));

            $items[$this->table->id] = ED::poll($poll);
        }

        return $items[$this->table->id];
    }

    /**
     * Gets the poll question
     *
     * @since   4.0
     * @access  public
     * @param   string
     * @return  
     */
    public function getTitle()
    {
        return $this->table->value;
    }

    /**
     * Retrieves a list of voters
     *
     * @since   4.0
     * @access  public
     * @param   string
     * @return  
     */
    public function getVoters($limit = null)
    {
        $model = ED::model('Polls');
        $users = $model->getVoters($this, $limit);

        return $users;
    }

    /**
     * Gets the percentage of a choice
     *
     * @since   4.0
     * @access  public
     * @param   string
     * @return  
     */
    public function getVoteCount()
    {
        return $this->count;
    }

    /**
     * Gets the percentage of a choice
     *
     * @since   4.0
     * @access  public
     * @param   string
     * @return  
     */
    public function getPercentage()
    {
        static $items = array();

        if (!isset($items[$this->table->id])) {

            // If the vote count for this choice is 0, there is no point querying the db
            if (!$this->table->count) {
                $items[$this->table->id] = 0;
            } else {
                $model = ED::model('Polls');
                $items[$this->table->id] = $model->getPercentage($this);                
            }
        }

        return $items[$this->table->id];
    }

    /**
     * Updates the count for the choice
     *
     * @since   4.0
     * @access  public
     * @param   string
     * @return  
     */
    public function updateCount()
    {
        $model = ED::model('Polls');

        return $model->updateChoiceCount($this->table->id);
    }

    /**
     * Determines if a user has voted on this choice before
     *
     * @since   4.0
     * @access  public
     * @param   string
     * @return  
     */
    public function hasVoted($userId = null)
    {
        $userId = JFactory::getUser($userId)->id;

        $model = ED::model('Polls');
        $voted = $model->hasVotedChoice($this->table->id, $userId);

        return $voted;
    }
    

    /**
     * Tests if the user has already voted for this discussion's poll before.
     *
     * @access  public
     * @param   int $userId     The user id to check for.
     * @return  boolean         True if voted, false otherwise.
     */
    public function hasVotedPoll( $userId, $sessionId )
    {
        $db     = DiscussHelper::getDBO();

        if( $userId == 0 )
        {
            $where = 'WHERE ' . $db->nameQuote( 'session_id' ) . '=' . $db->Quote( $sessionId ) . ' ';
        }
        else
        {
            $where = 'WHERE ' . $db->nameQuote( 'user_id' ) . '=' . $db->Quote( $userId ) . ' ';
        }

        $query  = 'SELECT COUNT(1) FROM ' . $db->nameQuote( '#__discuss_polls_users' ) . ' '
            . $where
            . 'AND ' . $db->nameQuote( 'poll_id' ) . ' IN('
            . 'SELECT `id` FROM ' . $db->nameQuote( '#__discuss_polls' ) . ' WHERE ' . $db->nameQuote( 'post_id' ) . '=' . $db->Quote( $this->post_id )
            . ')';
        $db->setQuery( $query );
        $voted  = $db->loadResult();

        return $voted > 0;
    }

    /**
     * Allows caller to vote for this choice
     *
     * @since   4.0
     * @access  public
     * @param   string
     * @return  
     */
    public function vote($userId = null)
    {
        $userId = JFactory::getUser($userId)->id;

        // Get user's session id.
        $session = JFactory::getSession();
        $sessionId = $session->getId();

        // Get the poll
        $poll = $this->getPoll();

        // If this is not a multiple polls, we need to remove the user's vote first
        if (!$poll->isMultiple()) {

            // Delete any existing votes from this user first
            $poll->deleteExistingVotes($userId);

            // Create a new vote
            $this->createVote($userId);
        }

        // If this is a multiple choice option, we need to check if the user is voting on the same
        if ($poll->isMultiple()) {

            // If user has voted on this item before, we need to unvote the particular poll item.
            if ($this->hasVoted($userId)) {

                // Remove the user's vote
                $model = ED::model('Polls');
                $model->unvoteChoice($this->table->id, $userId);

            } else {

                // Create a new vote
                $this->createVote($userId);
            }
        }

        // Once the vote is casted, we need to tell the post table to recalculate the polls
        $poll->update();

        return true;
    }

    /**
     * Converts the poll choice in a json safe object
     *
     * @since   4.0
     * @access  public
     * @param   string
     * @return  
     */
    public function toData()
    {
        $obj = new stdClass();
        $obj->id = $this->table->id;
        $obj->percentage = $this->getPercentage();
        $obj->count = $this->count;
        $obj->votes = $this->getVoteCount();

        return $obj;
    }
}
