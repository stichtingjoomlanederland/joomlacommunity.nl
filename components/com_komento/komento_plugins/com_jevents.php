<?php
/**
 * @package     Komento
 * @copyright   Copyright (C) 2012 Stack Ideas Private Limited. All rights reserved.
 * @license     GNU/GPL, see LICENSE.php
 * Komento is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

// No direct access
defined('_JEXEC') or die('Restricted access');

require_once( dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'abstract.php' );

require_once( JPATH_ROOT . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_jevents' . DIRECTORY_SEPARATOR . 'libraries' . DIRECTORY_SEPARATOR . 'helper.php' );

class KomentoComJEvents extends KomentoExtension
{
    public $component = 'com_jevents';
    public $_item;
    public $_map = array(
        'id'            => 'ev_id',
        'title'         => 'summary',
        'hits'          => 'hits',
        'created_by'    => 'created_by',
        'catid'         => 'catid'
        );

    /**
     * Method to load a plugin object by content id number
     *
     * @access  public
     *
     * @return  object  Instance of this class
     */
    public function load( $cid )
    {
        static $instances = array();

        if( !isset( $instances[$cid] ) )
        {
            $db     = Komento::getDBO();
            $query  = 'SELECT a.`ev_id`, a.`catid`, a.`uid` , a.`created_by`, b.* FROM ' . $db->nameQuote( '#__jevents_vevent' ) . ' AS a '
                    . 'INNER JOIN ' . $db->nameQuote( '#__jevents_vevdetail' ) . ' AS b '
                    . 'ON a.`detail_id`=b.`evdet_id` '
                    . 'WHERE a.' . $db->nameQuote( 'ev_id' ) . '=' . $db->Quote( $cid );

            $db->setQuery( $query );
            $this->_item = $db->loadObject();

            if( empty( $this->_item ) )
            {
                return false;
            }

            $instances[$cid] = $this->_item;
        }

        $this->_item = $instances[$cid];

        return $this;
    }

    public function getContentIds( $categories = '' )
    {
        $db     = Komento::getDBO();
        $query = '';

        if( empty( $categories ) )
        {
            $query = 'SELECT `ev_id` FROM ' . $db->nameQuote( '#__jevents_vevent' ) . ' ORDER BY `ev_id`';
        }
        else
        {
            if( is_array( $categories ) )
            {
                $categories = implode( ',', $categories );
            }

            $query = 'SELECT `ev_id` FROM ' . $db->nameQuote( '#__jevents_vevent' ) . ' WHERE `catid` IN (' . $categories . ') ORDER BY `ev_id`';
        }

        $db->setQuery( $query );
        return $db->loadResultArray();
    }

    public function getCategories()
    {
        $db     = Komento::getDBO();
        $query  = 'SELECT a.id, a.title, a.level, a.parent_id, a.title AS name, a.parent_id AS parent'
                . ' FROM `#__categories` AS a'
                . ' WHERE a.extension = ' . $db->quote( 'com_jevents' )
                . ' AND a.parent_id > 0'
                . ' ORDER BY a.lft';

        if( Komento::joomlaVersion() == '1.5' )
        {
            $query  = 'SELECT a.id, a.title'
                . ' FROM `#__categories` AS a'
                . ' ORDER BY a.ordering';
        }

        $db->setQuery( $query );
        $categories = $db->loadObjectList();

        if( Komento::joomlaVersion() >= '1.6' )
        {
            foreach( $categories as &$row )
            {
                $repeat = ( $row->level - 1 >= 0 ) ? $row->level - 1 : 0;
                $row->treename = str_repeat( '.&#160;&#160;&#160;', $repeat ) . ( $row->level - 1 > 0 ? '|_&#160;' : '' ) . $row->title;
            }
        }

        return $categories;
    }

    public function isListingView()
    {
        // We don't want to load anything on the listing view.
        return false;
    }

    public function isEntryView()
    {
        $task   = JRequest::getCmd( 'task' );

        return stristr( $task , '.detail' ) !== false;
    }

    public function onExecute( &$article, $html, $view, $options = array() )
    {
        $task       = JRequest::getCmd( 'task' );
        $listing    = array();

        // @task: JEvents does not output the appended text, but it only outputs the response.

        if( stristr( $task , '.detail' ) !== false )
        {
            return $html;
        }
    }

    public function getEventTrigger()
    {
        return 'onAfterDisplayContent';
    }

    public function getContentPermalink()
    {
        require_once( JPATH_ROOT . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_jevents' . DIRECTORY_SEPARATOR . 'jevents.defines.php' );
        require_once( JPATH_ROOT . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_jevents' . DIRECTORY_SEPARATOR . 'libraries' . DIRECTORY_SEPARATOR . 'helper.php' );

        $Itemid     = JEVHelper::getItemid();
        $task       = JRequest::getCmd( 'task' );
        $title      = JFilterOutput::stringURLSafe( $this->_item->summary );
        $date       = Komento::getDate( $this->_item->dtstart );

        $year = $date->toFormat('%Y');
        $month = $date->toFormat('%m');
        $day = $date->toFormat('%d');

        //Get the rp_id from the #__jevents_repetition table and assign to the permalink url
        $newEventId = $this->getEventId($this->_item->ev_id);

        $link = 'index.php?option=com_jevents&task=icalrepeat.detail&evid=' . $newEventId . '&Itemid=' . $Itemid . '&year=' . $year . '&month=' . $month . '&day=' . $day . '&title=' . $title . '&uid=' . $this->_item->uid;
        $link = $this->prepareLink($link);

        return $link;
    }

    public function onBeforeLoad( $eventTrigger, $context, &$article, $params, $page, $options )
    {
        $article->id = $article->ev_id;
        return true;
    }

    public function getEventId($idFromQuery)
    {
        $db = JFactory::getDBO();
        $query = 'SELECT `rp_id` FROM ' . $db->qn('#__jevents_repetition') . ' WHERE ' . $db->qn('eventid') . '=' . $db->Quote($idFromQuery);
        $db->setQuery($query);

        $result = $db->loadResult();

        return $result;
    }    
}
