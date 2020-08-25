<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2020 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' ); ?>

<?php echo JHtml::image('com_rseventspro/loader.gif', '', array('id' => 'rsepro-add-sponsor-loader', 'style' => 'display: none;', 'class' => 'pull-left'), true); ?> 
<button class="btn btn-primary" onclick="jQuery('#rsepro-add-new-sponsor iframe').contents().find('#rsepro-save-sponsor').click();"><?php echo JText::_('COM_RSEVENTSPRO_EVENT_ADD_SPONSOR_ADD'); ?></button>
<button class="btn" data-dismiss="modal" aria-hidden="true"><?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_CANCEL'); ?></button>