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

// import Joomla view library
jimport('joomla.application.component.view');

/**
 * SPUpgrades View
 */
class SPUpgradeViewTables extends JViewLegacy {

    public $dbTestConnection;

    /**
     * SPUpgrades view display method
     * @return void
     */
    function display($tpl = null) {
        // Get data from the model
        $this->dbTestConnection = $this->get('TestConnection');
        if ($this->dbTestConnection) {
            CYENDFactory::enqueueMessage(JText::_('COM_SPUPGRADE_MSG_SUCCESS_CONNECTION'), 'message');
        } else {
            CYENDFactory::enqueueMessage(JText::_('COM_SPUPGRADE_MSG_ERROR_CONNECTION'), 'error');
        }
        //$this->pathConnection = $this->get('PathConnection');
        $this->ftpConnection = $this->get('FtpConnection');
        $items = $this->get('Items');
        $pagination = $this->get('Pagination');

        // Check for errors.
        if (count($errors = $this->get('Errors'))) {
            JError::raiseError(500, implode('<br />', $errors));
            return false;
        }
        // Assign data to the view
        $this->items = $items;
        $this->pagination = $pagination;

        // Set the toolbar
        $this->addToolBar();

        //Set JavaScript
        $this->addJS();

        // Display the template
        parent::display($tpl);

        // Set the document
        $this->setDocument();
    }

    /**
     * Setting the toolbar
     */
    protected function addToolBar() {
        $canDo = SPUpgradeHelper::getActions();

        JToolBarHelper::title(JText::_('COM_SPUPGRADE_TABLES_TITLE'), 'copy');

        $view = JRequest::setVar('view', JRequest::getCmd('view', 'tables'));
        SPUpgradeHelper::addSubmenu('tables');

        if ($canDo->get('core.admin')) {
            $bar = JToolBar::getInstance('toolbar');
            if ($this->dbTestConnection) {
                $bar->appendButton('Confirm', 'COM_SPUPGRADE_CONFIRM_MSG', 'copy', 'COM_SPUPGRADE_TRANSFER', 'tables.transfer', true);
                $bar->appendButton('Confirm', 'COM_SPUPGRADE_CONFIRM_MSG', 'move', 'COM_SPUPGRADE_TRANSFER_ALL', 'tables.transfer_all', false);
                if ($this->ftpConnection) {
                    $bar->appendButton('Confirm', 'COM_SPUPGRADE_CONFIRM_MSG', 'folder', 'COM_SPUPGRADE_TRANSFER_IMAGES', 'tables.transfer_images', false);
                    $bar->appendButton('Confirm', 'COM_SPUPGRADE_CONFIRM_MSG', 'image', 'COM_SPUPGRADE_TRANSFER_TEMPLATE', 'tables.transfer_template', false);
                }
                JToolBarHelper::divider();
            }
            JToolBarHelper::preferences('com_spupgrade');
        }
        $bar = JToolBar::getInstance('toolbar');
        $bar->appendButton('Help', 'help', 'JTOOLBAR_HELP', 'http://www.kainotomo.com/products/sp-upgrade/documentation', 640, 480);
    }

    /**
     * Method to set up the document properties
     *
     * @return void
     */
    protected function setDocument() {
        $document = JFactory::getDocument();
        $document->setTitle(JText::_('COM_SPUPGRADE_ADMINISTRATION'));
    }

    private function addJS() {
        //Handle chosed items
        $rows = "";
        foreach ($this->items as $item) {
            $rows .= "rows[" . $item->id . "]='" . $item->extension_name . "_" . $item->name . "';\n";
        }

        //Choose items
        $js = "
		function jSelectItem(cid, name, id_arr) {
            var chklength = id_arr.length;
             var input_ids = 'input_ids'+cid;
            var input_id;
            for(k=0;k<chklength;k++) {
                input_id = document.getElementById(input_ids);
                if (input_id.value == '') {
                    input_id.value = id_arr[k];
                } else {
                    input_id.value = input_id.value + ',' + id_arr[k];
                }                
            }
            SqueezeBox.close();
    	}";

        $doc = JFactory::getDocument();
        $doc->addScriptDeclaration($js);

        //Clear selected items
        $js2 = "
		function jClearItem(cid) {
            var input_ids = 'input_ids'+cid;
            document.getElementById(input_ids).value = '';            
    	}";

        $doc->addScriptDeclaration($js2);

        $doc->addScript(JURI::root() . 'media/com_spupgrade/js/core.js');
        $doc->addScript(JURI::root() . 'media/com_spupgrade/js/submit.js');
    }

}
