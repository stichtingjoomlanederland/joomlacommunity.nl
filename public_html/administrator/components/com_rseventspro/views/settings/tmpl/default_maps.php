<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2020 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' );

echo JHtml::_('rsfieldset.start', 'adminform');
echo $this->form->renderFieldset('maps');
echo JHtml::_('rsfieldset.end');