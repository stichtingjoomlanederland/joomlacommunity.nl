<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2014 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');
?>

<?php if ($this->params->get('show_page_heading', 1)) { ?>
	<h1><?php echo $this->escape($this->params->get('page_heading')); ?></h1>
<?php } ?>

<?php if ($this->params->get('show_search',0)) { ?>
<form method="post" action="<?php echo $this->escape((string) JUri::getInstance()); ?>" name="adminForm" id="adminForm" class="form-inline">
<div class="well well-small">
	<?php echo JText::_('RSFP_SEARCH'); ?> <input type="text" id="rsfilter" name="filter" value="<?php echo $this->escape($this->filter); ?>" onchange="document.adminForm.submit();" /> 
	<button type="button" class="btn btn-primary button" onclick="document.adminForm.submit();"><?php echo JText::_('RSFP_GO'); ?></button> 
	<button type="button" class="btn button" onclick="document.getElementById('rsfilter').value='';document.adminForm.submit();"><?php echo JText::_('RSFP_RESET'); ?></button>
</div>
<?php } ?>

<table width="100%" border="0" cellspacing="0" cellpadding="0">
	<tr>
		<td><?php echo $this->template; ?></td>
	</tr>
	<tr>
		<td align="center" class="sectiontablefooter<?php echo $this->escape($this->params->get('pageclass_sfx')); ?>">
			<div class="pagination"><?php echo $this->pagination->getPagesLinks(); ?></div>
		</td>
	</tr>
	<tr>
		<td align="center"><?php echo $this->pagination->getPagesCounter(); ?></td>
	</tr>
</table>

<?php if ($this->params->get('show_search',0)) { ?>
</form>
<?php } ?>