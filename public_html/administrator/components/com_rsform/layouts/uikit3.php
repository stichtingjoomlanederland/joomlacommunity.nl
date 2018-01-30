<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2014 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

require_once __DIR__ . '/grid/uikit3.php';

$grid = new RSFormProGridUikit3($this->_form->GridLayout, $formId, $formOptions, $requiredMarker, $showFormTitle);
echo $grid->generate();