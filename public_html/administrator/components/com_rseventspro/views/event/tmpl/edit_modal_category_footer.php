<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' ); ?>

<?php echo JHtml::image('com_rseventspro/loader.gif', '', array('id' => 'rsepro-add-category-loader', 'style' => 'display: none;', 'class' => 'pull-left'), true); ?> 
<button class="btn btn-primary rsepro-event-add-category"><?php echo JText::_('COM_RSEVENTSPRO_EVENT_ADD_CATEGORY_ADD'); ?></button>
<button class="btn" data-dismiss="modal" aria-hidden="true"><?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_CANCEL_BTN'); ?></button>