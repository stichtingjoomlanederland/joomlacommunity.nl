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
defined('_JEXEC') or die;

/**
 * SPUpgrade component helper.
 */
abstract class SPUpgradeHelper {

    /**
     * Configure the Linkbar.
     */
    public static function addSubmenu($submenu ) {
        JSubMenuHelper::addEntry(JText::_('COM_SPUPGRADE_TABLES_SUBMENU'), 'index.php?option=com_spupgrade', $submenu == 'tables');
        JSubMenuHelper::addEntry(JText::_('COM_SPUPGRADE_EXTENSIONS_SUBMENU'), 'index.php?option=com_spupgrade&view=extensions', $submenu == 'extensions');
        JSubMenuHelper::addEntry(JText::_('COM_SPUPGRADE_DATABASE_SUBMENU'), 'index.php?option=com_spupgrade&view=database', $submenu == 'database');
        //JSubMenuHelper::addEntry(JText::_('COM_SPUPGRADE_LOG_SUBMENU'), 'index.php?option=com_spupgrade&view=monitoring_log', $submenu == 'monitoring_log');
        JSubMenuHelper::addEntry(JText::_('COM_SPUPGRADE_HISTORY_SUBMENU'), 'index.php?option=com_spupgrade&view=log', $submenu == 'log');
        // set some global property
        $document = JFactory::getDocument();
    }

    /**
     * Get the actions
     */
    public static function getActions($itemsId = 0) {
        $user = JFactory::getUser();
        $result = new JObject;

        if (empty($tablesId)) {
            $assetName = 'com_spupgrade';
        } else {
            $assetName = 'com_spupgrade.tables.' . (int) $tablesId;
        }

        $actions = array(
            'core.admin', 'core.manage', 'core.create', 'core.edit', 'core.delete'
        );

        foreach ($actions as $action) {
            $result->set($action, $user->authorise($action, $assetName));
        }

        return $result;
    }

}
