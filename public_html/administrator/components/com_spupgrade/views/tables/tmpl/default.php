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
defined('_JEXEC') or die('Restricted Access');
// load tooltip behavior
JHtml::_('behavior.tooltip');
JHtml::_('behavior.multiselect');
?>
<script type="text/javascript">
    Joomla.submitbutton = function(task) { 
        
        form = document.getElementById('adminForm');
        if (task === 'tables.transfer') {
            SPCYEND_core.transfer(task, form);
            return;
        }
        if (task === 'tables.transfer_all') {
            SPCYEND_core.transfer_all(task, form);
            return;
        }
        if (task === 'tables.transfer_template') {
            SPCYEND_core.transfer_template(task, form);
            return;
        }
        if (task === 'tables.transfer_images') {
            SPCYEND_core.transfer_images(task, form);
            return;
        }
        
        var myDomain = location.protocol + '//' +
            location.hostname +
            location.pathname.substring(0, location.pathname.lastIndexOf('/')) +
            //'/index.php?option=com_spupgrade&view=monitoring_log';                    
        '/components/com_spupgrade/log.htm';                    
        window.open(myDomain,'SP Upgrade','width=640,height=480, scrollbars=1');
        Joomla.submitform(task);              
    }
</script>
<form action="<?php echo JRoute::_('index.php?option=com_spupgrade&view=tables'); ?>" method="post" name="adminForm" id="adminForm">        
    <div class="clr"></div>
    <div class="alert alert-info" hidden="true" id="cyend_log"></div>
    <div class="alert" hidden="true" id="get_last_id"></div>
    <table class="table table-striped" id="spupgrade_table">
        <thead><?php echo $this->loadTemplate('head'); ?></thead>
        <tfoot><?php echo $this->loadTemplate('foot'); ?></tfoot>
        <tbody><?php echo $this->loadTemplate('body'); ?></tbody>
    </table>
    <div>
        <input type="hidden" name="task" value="" />
        <input type="hidden" name="boxchecked" value="0" />
        <?php echo JHtml::_('form.token'); ?>
    </div>
</form>