<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' );
JHtml::_('behavior.keepalive'); ?>

<?php if ($this->placeholders) { ?>
<fieldset>
	<legend><?php echo JText::_('COM_RSEVENTSPRO_EMAIL_PLACEHOLDERS'); ?></legend>
	<table class="table table-striped table-condensed" id="placeholdersTable">
	<?php foreach ($this->placeholders as $placeholder => $description) { ?>
	<tr>
		<td class="rsepro-placeholder"><?php echo $placeholder; ?></td>
		<td><?php echo JText::_($description); ?></td>
	</tr>
	<?php } ?>
	</table>
</fieldset>
<?php } ?>