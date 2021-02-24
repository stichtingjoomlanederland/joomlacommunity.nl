<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2020 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' );

echo $this->form->renderFieldset('invoice');
echo '<div class="alert alert-info">'.JText::_('COM_RSEVENTSPRO_INVOICE_PLACEHOLDERS').'</div>';