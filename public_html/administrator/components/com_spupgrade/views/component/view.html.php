<?php

/**
 * @package		SP Trasnfer
 * @subpackage	Components
 * @copyright	KAINOTOMO PH LTD - All rights reserved.
 * @author		KAINOTOMO PH LTD
 * @link		http://www.kainotomo.com
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.view');

class SPUpgradeViewComponent extends JViewLegacy {

    protected $items;

    /**
     * Display the view
     *
     * @return	void
     */
    public function display($tpl = null) {
        $source = new CYENDSource();
        $this->dbTestConnection = $source->testConnection();
        if ($this->dbTestConnection === false ) {
            CYENDFactory::enqueueMessage(JText::_('COM_SPUPGRADE_MSG_ERROR_CONNECTION'), 'error');
            return false;
        } 
        
        $this->items = $this->get('Items');
        if (count($this->items) == 0) {
            echo JText::_('COM_SPUPGRADE_EMPTY_TABLE');
            return;
        }
        $this->name = JRequest::getVar('name');
        $this->pk = JRequest::getVar('pk');
        $this->cid = JRequest::getVar('cid');
        
        // Check for errors.
        if (count($errors = $this->get('Errors'))) {
            JError::raiseError(500, implode("\n", $errors));
            return false;
        }


        $js = '
		function findChecked(checkbox, stub) {
            var chk_arr =  document.getElementsByName("cid[]");
            var chklength = chk_arr.length;             
            var id_arr = [];
            for(k=0;k< chklength;k++)
            {
                if (chk_arr[k].checked == true) {
                    id_arr.push( chk_arr[k].value );                
                }
            }         
            return id_arr;
        }';

        $doc = JFactory::getDocument();
        $doc->addScriptDeclaration($js);

        parent::display($tpl);
    }

}
