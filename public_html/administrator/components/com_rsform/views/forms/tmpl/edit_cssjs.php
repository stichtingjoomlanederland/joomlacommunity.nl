<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2014 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');
?>
<legend><?php echo JText::_('RSFP_CSS'); ?></legend>
<p class="alert alert-info"><?php echo JText::_('RSFP_CSS_DESC'); ?></p>
<textarea class="rs_textarea codemirror-css rs_100" rows="20" cols="75" name="CSS" id="CSS"><?php echo $this->escape($this->form->CSS);?></textarea>

<legend><?php echo JText::_('RSFP_JS'); ?></legend>
<p class="alert alert-info"><?php echo JText::_('RSFP_JS_DESC'); ?></p>
<textarea class="rs_textarea codemirror-js rs_100" rows="20" cols="75" name="JS" id="JS"><?php echo $this->escape($this->form->JS);?></textarea>