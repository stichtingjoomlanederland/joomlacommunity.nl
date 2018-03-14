<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2014 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');
?>
<h3 class="rsfp-legend"><?php echo JText::_('RSFP_SCRIPTS_DISPLAY'); ?></h3>
<p class="alert alert-info"><?php echo JText::_('RSFP_SCRIPTS_DISPLAY_DESC'); ?></p>
<textarea class="rs_textarea codemirror-php rs_100" rows="20" cols="75" name="ScriptDisplay" id="ScriptDisplay"><?php echo $this->escape($this->form->ScriptDisplay);?></textarea>

<h3 class="rsfp-legend"><?php echo JText::_('RSFP_SCRIPTS_PROCESS'); ?></h3>
<p class="alert alert-info"><?php echo JText::_('RSFP_SCRIPTS_PROCESS_DESC'); ?></p>
<textarea class="rs_textarea codemirror-php rs_100" rows="20" cols="75" name="ScriptProcess" id="ScriptProcess"><?php echo $this->escape($this->form->ScriptProcess);?></textarea>

<h3 class="rsfp-legend"><?php echo JText::_('RSFP_SCRIPTS_PROCESS2'); ?></h3>
<p class="alert alert-info"><?php echo JText::_('RSFP_SCRIPTS_PROCESS2_DESC'); ?></p>
<textarea class="rs_textarea codemirror-php rs_100" rows="20" cols="75" name="ScriptProcess2" id="ScriptProcess2"><?php echo $this->escape($this->form->ScriptProcess2);?></textarea>