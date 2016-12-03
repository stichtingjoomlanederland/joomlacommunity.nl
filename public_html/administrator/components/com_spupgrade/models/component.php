<?php

/**
 * @package		SP Upgrade
 * @subpackage	Components
 * @copyright	KAINOTOMO PH LTD - All rights reserved.
 * @author		KAINOTOMO PH LTD
 * @link		http://www.kainotomo.com
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
// no direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.modellist');

class SPUpgradeModelComponent extends JModelList {

    
    /**
     * Method to auto-populate the model state.
     *
     * Note. Calling getState in this method will result in recursion.
     *
     * @return	void
     * @since	1.6
     */
    protected function populateState($ordering = null, $direction = null) {
        $extension_name = JRequest::getVar('extension_name');
        $this->setState('extension_name', $extension_name);
        $name = JRequest::getVar('name');
        $this->setState('name', $name);
        $pk = JRequest::getVar('pk');
        $this->setState('pk', $pk);
    }

    /**
     * Build an SQL query to load the list data.
     *
     * @return	JDatabaseQuery
     * @since	1.6
     */
    protected function getListQuery() {
        $source = new CYENDSource();
        
        // Create a new query object.
        $db = $source->source_db;
        $query = $db->getQuery(true);
        
        // Select the required fields from the table.
        /*
        $column_name = $this->getColumnName();
        $name = $this->getState('name');
        $query->select('*');
        $query->from('#__'.$name.' AS a');
        $query->order($column_name.' ASC');
         * 
         */
        
        $extension_name = strtolower($this->getState('extension_name'));
        $name = strtolower($this->getState('name'));
        $pk = $this->getState('pk');  
        $query = 'select '.$db->quoteName($pk).' sp_id, p.* from #__'.$name.' p, (SELECT @rownum:=-1) r';

        if ($name == 'categories') {
            switch ($extension_name) {
                case 'com_content':
                    $query .= ' WHERE section NOT LIKE "com_%"';
                    break;
                case 'com_contact':
                    $query .= ' WHERE section LIKE "COM_CONTACT_DETAILS"';
                    break;
                case 'com_weblinks':
                    $query .= ' WHERE section LIKE "COM_WEBLINKS"';
                    break;
                case 'com_newsfeeds':
                    $query .= ' WHERE section LIKE "COM_NEWSFEEDS"';
                    break;                
                case 'com_banners':
                    $query .= ' WHERE section LIKE "COM_BANNER"';
                    break;
                default:
                    break;
            }
        }
        $query .= ' ORDER BY sp_id ASC';

        return $query;
    }
    
    protected function getColumnName() {
        $source = new CYENDSource();
        $db = $source->source_db;
        $name = $this->getState('name');
        $query = 'describe #__'.$name;
        $db->setQuery($query);
        CYENDFactory::execute($db);
        $column_name = $db->loadResult();
        return $column_name;
    }
    
     public function getItems($pk = null) {
        $source = new CYENDSource();
        $db = $source->source_db;
        $query = $this->getListQuery($pk);

        $db->setQuery($query);
        CYENDFactory::execute($db);
        $items = $db->loadObjectList();

        return $items;
    }

}
