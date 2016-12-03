<?php
/**
* @package RSForm!Pro
* @copyright (C) 2007-2014 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

// no direct access
defined('_JEXEC') or die('Restricted access');
?>

<style>
#rseproemailsdiv {
	overflow: hidden;
}
</style>
<table class="admintable">
<tr>
	<td valign="top" align="left" width="60%">
		<table class="table table-bordered">
			<tr>
				<td colspan="2"><div class="alert alert-info"><?php echo JText::_('RSFP_RSEPRO_DESC'); ?></div></td>
			</tr>
			<tr>
				<td width="80" align="right" nowrap="nowrap" class="key"><?php echo JText::_('RSFP_RSEPRO_USE_INTEGRATION'); ?></td>
				<td><?php echo $lists['published']; ?></td>
			</tr>
		</table>
	</td>
	<td valign="top">
		&nbsp;
	</td>
</tr>
</table>

<br />

<?php 
	foreach ($emails as $type => $email) { 
		$tabs->addTitle('RSFP_RSEPRO_'.strtoupper($type).'_EMAIL','rsepro-'.$type); 
		
		if ($type == 'notify') {
			$placeholder = 'notify_me';
		} elseif ($type == 'ticketpdf') {
			$placeholder = 'pdf';
		} else $placeholder = $type;
		
		$content = '<table width="100%">';
		$content .= '<tr>';
		$content .= '<td width="60%" valign="top">';
		
		$content .= '<table class="admintable" width="100%" cellpadding="5">';
		foreach ($email as $field) {
			$content .= '<tr>';
			$content .= '<td width="200px;">';
			$content .= '<label>'.$field['label'].'</label>';
			$content .= '</td>';
			$content .= '<td>';
			$content .= $field['input'];
			$content .= '</td>';
			$content .= '</tr>';
		}
		$content .= '</table>';
		
		$content .= '</td>';
		$content .= '<td width="5%"></td>';
		$content .= '<td width="35%" valign="top">';
		
		$content .= '<table class="admintable" width="100%" cellpadding="5">';
		$content .= '<tr>';
		$content .= '<td>';
		
		try {
			if ($placeholders = RSEventsProPlaceholders::get($placeholder)) {
				$content .= '<table class="admintable" width="100%">';
				$content .= '<thead><tr><th colspan="2">'.JText::_('COM_RSEVENTSPRO_EMAIL_PLACEHOLDERS').'</th></tr></thead>';
				foreach ($placeholders as $name => $description) {
					$content .= '<tr>';
					$content .= '<td>';
					$content .= $name;
					$content .= '</td>';
					$content .= '<td>';
					$content .= JText::_($description);
					$content .= '</td>';
					$content .= '</tr>';
				}
				$content .= '</table>';
			}
		} catch (Exception $e) {}
		
		$content .= '</td>';
		$content .= '</tr>';
		$content .= '</table>';
		$content .= '</td>';
		$content .= '</tr>';
		$content .= '</table>';
		
		$tabs->addContent($content);
	}
	
	$tabs->render();
?>