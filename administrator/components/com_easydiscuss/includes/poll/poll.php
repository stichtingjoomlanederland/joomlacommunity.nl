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

class EasyDiscussPoll extends EasyDiscuss
{
    private $table = null;

    public function __construct($poll, $options = array())
    {
        parent::__construct();

        // The $post must always be a table.
        $this->table = ED::table('PollQuestion');

        // If passed in argument is an integer, we load it
        if (is_numeric($poll)) {
            $this->table->load($poll);
        }

        // If passed in argument is already a post, table just assign it.
        if ($poll instanceof DiscussPollQuestion) {
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
     * Determines if this poll is locked
     *
     * @since   4.0
     * @access  public
     * @param   string
     * @return  
     */
    public function isLocked()
    {
        return $this->locked;
    }

    /**
     * Determines if this is a multiple answer poll
     *
     * @since   4.0
     * @access  public
     * @param   string
     * @return  
     */
    public function isMultiple()
    {
        return $this->table->multiple == 1;
    }

    /**
     * Deletes existing votes for a particular user
     *
     * @since   4.0
     * @access  public
     * @param   string
     * @return  
     */
    public function deleteExistingVotes($userId)
    {
        $model = ED::model('Polls');
        $state = $model->deleteExistingVotes($userId, $this->table->post_id);

        return $state;
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
        return $this->table->title;
    }

    /**
     * Gets the number of votes for this poll
     *
     * @since   4.0
     * @access  public
     * @param   string
     * @return  
     */
    public function getTotalVotes()
    {
        static $items = array();

        if (!isset($items[$this->table->id])) {
            $model = ED::model('Polls');
            $items[$this->table->id] = $model->getTotalVotes($this->table->post_id);
        }

        return $items[$this->table->id];
    }

    /**
     * Gets the post object
     *
     * @since   4.0
     * @access  public
     * @param   string
     * @return  
     */
    public function getPost()
    {
        $post = ED::post($this->table->post_id);

        return $post;
    }

    /**
     * Determines if the user can vote on choices for this poll
     *
     * @since   4.0
     * @access  public
     * @param   string
     * @return  
     */
    public function canVote($userId = null)
    {
        $userId = JFactory::getUser($userId)->id;
        $post = $this->getPost();

        // Polls for question was disabled
        if ($post->isQuestion() && !$this->config->get('main_polls')) {
            return false;
        }

        // Polls for reply was disabled
        if ($post->isReply() && !$this->config->get('main_polls_replies')) {
            return false;
        }

        // If poll is locked, they shouldn't be able to vote
        if ($this->isLocked()) {
            return false;
        }

        // If the user is not logged in and guests voting is disabled
        if (!$this->config->get('main_polls_guests') && !$userId) {
            return false;
        }

        return true;
    }

    /**
     * Return a list of choices for this poll
     *
     * @since   4.0
     * @access  public
     * @param   string
     * @return
     */
    public function getChoices($force = false)
    {
        static $items = array();

        if (isset($items[$this->table->id]) && !$force) {
            return $items[$this->table->id];
        }

        $model = ED::model('Polls');
        $choices = $model->getChoices($this->table->post_id);

        $items[$this->table->id] = $choices;

        return $items[$this->table->id];
    }

    /**
     * Triggered when a choice is voted
     *
     * @since   4.0
     * @access  public
     * @param   string
     * @return  
     */
    public function update()
    {
        // We need to recalculate the polls counter
        $choices = $this->getChoices();

        if (!$choices) {
            return false;
        }

        foreach ($choices as $choice) {
            $choice->updateCount();
        }
    }
}
