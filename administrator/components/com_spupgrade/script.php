<?php

/**
 * @package		SP Upgrade
 * @subpackage	Components
 * @copyright	KAINOTOMO PH LTD - All rights reserved.
 * @author		KAINOTOMO PH LTD
 * @link		http://www.kainotomo.com
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html 
 */
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

/**
 * Script file of SPUpgrade component
 */
class com_spupgradeInstallerScript {

    /**
     * method to install the component
     *
     * @return void
     */
    function install($parent) {
        // $parent is the class calling this method
        //$parent->getParent()->setRedirectURL('index.php?option=com_spupgrade');
    }

    /**
     * method to uninstall the component
     *
     * @return void
     */
    function uninstall($parent) {
        // $parent is the class calling this method
        echo '<p>' . JText::_('COM_SPUPGRADE_UNINSTALL_TEXT') . '</p>';
    }

    /**
     * method to update the component
     *
     * @return void
     */
    function update($parent) {
        // $parent is the class calling this method
        echo '<p>' . JText::_('COM_SPUPGRADE_UPDATE_TEXT') . '</p>';
    }

    /**
     * method to run before an install/update/uninstall method
     *
     * @return void
     */
    function preflight($type, $parent) {
        // $parent is the class calling this method
        // $type is the type of change (install, update or discover_install)
        //echo '<p>' . JText::_('COM_SPUPGRADE_PREFLIGHT_' . $type . '_TEXT') . '</p>';

        $jversion = new JVersion();

        // Installing component manifest file version
        $component_version = $parent->get("manifest")->version;
        $joomla_version = $jversion->getShortVersion();
        $minimun_version = $parent->get("manifest")->attributes()->minimun_version;
        $maximun_version = $parent->get("manifest")->attributes()->maximun_version;

        //abort if version less than minimun
        if (version_compare($joomla_version, $minimun_version, 'lt')) {
            Jerror::raiseWarning(null, 'Cannot install in a Joomla release prior to ' . $minimun_version);
            return false;
        }
        /*
        // abort if the current Joomla greater than maximun
        if (version_compare($joomla_version, $maximun_version . '.9999', 'gt')) {
            Jerror::raiseWarning(null, 'Cannot install in a Joomla release greater than ' . $maximun_version);
            return false;
        }        
         * 
         */
    }

    /**
     * method to run after an install/update/uninstall method
     *
     * @return void
     */
    function postflight($type, $parent) {
        // $parent is the class calling this method
        // $type is the type of change (install, update or discover_install)
        //Update client_id in extensions table to enable online upgrade

        $jAp = & JFactory::getApplication();
        $db = & JFactory::getDBO();
        $query = "UPDATE `#__extensions` SET `client_id` = '0'  WHERE `name` ='com_spupgrade';";
        $db->setQuery($query);
        if (!$db->execute()) {
            $jAp->enqueueMessage(nl2br($db->getErrorMsg()), 'error');
            return;
        }

        //echo '<p>' . JText::_('COM_SPUPGRADE_POSTFLIGHT_' . $type . '_TEXT') . '</p>';
    }

}
