<?php
/**
 * @package    JDiDEAL
 *
 * @author     Roland Dalmulder <contact@rolandd.com>
 * @copyright  Copyright (C) 2009 - 2021 RolandD Cyber Produksi. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link       https://rolandd.com
 */

defined('_JEXEC') or die;

$jdideal = $displayData['jdideal'];
$data = $displayData['data'];
$root = $displayData['root'];
$output = $displayData['output'];

// Load the stylesheet
JHtml::stylesheet('com_jdidealgateway/payment.css', null, true);

?>
<div id="paybox">
	<?php
		// Show custom HTML
		echo $data->custom_html;
	?>
	<form name="idealform<?php echo $data->logid; ?>" id="idealform<?php echo $data->logid; ?>" action="<?php echo $root; ?>index.php?option=com_jdidealgateway&task=checkideal.send&format=raw" method="post" target="_self">
		<input type="hidden" name="logid" value="<?php echo $data->logid; ?>">
		<div id="paybox_links">
			<div id="paybox_banks">
				<?php
				foreach ($data->output as $name => $options)
				{
					echo $options;
				}
				?>
			</div>
			<div class="clr"></div>
			<div id="paybox_button">
				<?php
				echo JHtml::link(
					$root,
					JText::_('COM_ROPAYMENTS_GO_TO_CASH_REGISTER'),
					'onclick="document.idealform' . $data->logid . '.submit(); return false;"'
				);
				?>
			</div>
		</div>
	</form>
</div>
<?php
// Do we need to redirect
$payment_info = '';

switch ($data->redirect)
{
	case 'direct':
		/* go straight to the bank */
		$payment_info = '<script type="text/javascript">';
		$payment_info .= '	document.idealform' . $data->logid . '.submit();';
		$payment_info .= '</script>';
		break;
	case 'timer':
		/* show timer before going to bank */
		$payment_info = '<div id="showtimer">' . JText::_('COM_ROPAYMENTS_REDIRECT_5_SECS');
		$payment_info .= ' ' . JHtml::_('link', '', JText::_('COM_ROPAYMENTS_DO_NOT_REDIRECT'), array('onclick' => 'clearTimeout(timeout);return false;')) . '</div>';
		$payment_info .= '<script type="text/javascript">';
		$payment_info .= '	var timeout = setTimeout("document.idealform' . $data->logid . '.submit()", 5000);';
		$payment_info .= '</script>';
		break;
	case 'wait':
	default:
		break;
}
echo $payment_info;
?>
<script type="text/javascript">
	// Add some JavaScript to hide the iDEAL banks
	function showBanks(element)
	{
		if (element.value == '') document.getElementById('bank').show();
		else document.getElementById('bank').hide();
	}
</script>
