<?php
/**
 * @package    JDiDEAL
 *
 * @author     Roland Dalmulder <contact@rolandd.com>
 * @copyright  Copyright (C) 2009 - 2020 RolandD Cyber Produksi. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link       https://rolandd.com
 */

defined('_JEXEC') or die;

/** @var array $displayData */
$jdideal = $displayData['jdideal'];
$data = $displayData['data'];
$url = $displayData['url'];
$root = $displayData['root'];

// Load the stylesheet
JHtml::stylesheet('com_jdidealgateway/payment.css', null, true);

if (!$data->silent)
{
	// Show custom HTML
	echo $data->custom_html;
}
?>
<form method="post" action="<?php echo $url; ?>" id="idealform<?php echo $data->logid; ?>" name="idealform<?php echo $data->logid; ?>">
	<input type="hidden" name="PSPID" value="<?php echo $data->merchantID; ?>">
	<input type="hidden" name="ORDERID" value="<?php echo $data->orderNumber; ?>">
	<input type="hidden" name="AMOUNT" value="<?php echo $data->amount; ?>">
	<input type="hidden" name="CURRENCY" value="<?php echo $data->currency; ?>" />
	<input type="hidden" name="LANGUAGE" value="<?php echo $data->language; ?>" />
	<input type="hidden" name="EMAIL" value="<?php echo $data->email; ?>" />
	<?php

	if (!empty($data->hashinkey))
	{
		?>
		<input type="hidden" name="SHASign" value="<?php echo $data->shasign; ?>" />
		<?php
	}
	?>
	<input type="hidden" name="PM" value="<?php echo $data->pm; ?>" />
	<input type="hidden" name="BRAND" value="<?php echo $data->brand; ?>" />

	<input type="hidden" name="PMLIST" value="<?php echo $data->pmlist; ?>" />
	<input type="hidden" name="COM" value="<?php echo $data->com;?>" />

	<!-- Redirect URLs -->
	<input type="hidden" name="BACKURL" value="<?php echo $data->backurl; ?>">
	<input type="hidden" name="ACCEPTURL" value="<?php echo $data->notifyurl; ?>">
	<input type="hidden" name="DECLINEURL" value="<?php echo $data->notifyurl; ?>">
	<input type="hidden" name="EXCEPTIONURL" value="<?php echo $data->notifyurl; ?>">
	<input type="hidden" name="CANCELURL" value="<?php echo $data->notifyurl; ?>">

	<!-- Post payment processing -->
	<?php
	if ($data->hashoutkey)
	{
		?>
		<input type="hidden" name="COMPLUS" value="<?php echo $data->logid; ?>" />
		<?php
	}
	?>

	<!-- Look and feel  -->
	<input type="hidden" name="TITLE" value="<?php echo $data->look_title; ?>" />
	<input type="hidden" name="BGCOLOR" value="<?php echo $data->look_bgcolor; ?>" />
	<input type="hidden" name="TXTCOLOR" value="<?php echo $data->look_txtcolor; ?>" />
	<input type="hidden" name="TBLBGCOLOR" value="<?php echo $data->look_tblbgcolor; ?>" />
	<input type="hidden" name="TBLTXTCOLOR" value="<?php echo $data->look_tbltxtcolor; ?>" />
	<input type="hidden" name="BUTTONBGCOLOR" value="<?php echo $data->look_buttonbgcolor; ?>" />
	<input type="hidden" name="BUTTONTXTCOLOR" value="<?php echo $data->look_buttontxtcolor; ?>" />
	<input type="hidden" name="LOGO" value="<?php echo $data->look_logo; ?>" />
	<input type="hidden" name="FONTTYPE" value="<?php echo $data->look_fonttype; ?>" />

	<?php if (!$data->silent) : ?>
		<div id="paybox_button">
		<?php
		echo JHtml::link(
			$root,
			JText::_('COM_ROPAYMENTS_GO_TO_CASH_REGISTER'),
			'onclick="document.idealform' . $data->logid . '.submit(); return false;"'
		);
		?>
		</div>
	<?php endif; ?>

</form>
<?php
// Do we need to redirect
$payment_info = '';
$redirect = $data->silent ? 'direct' : $jdideal->get('redirect', 'wait');

switch ($redirect)
{
	case 'direct':
		// Go straight to the bank
		$payment_info = '<script type="text/javascript">';
		$payment_info .= '	document.idealform' . $data->logid . '.submit();';
		$payment_info .= '</script>';
		break;
	case 'timer':
		// Show timer before going to bank
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
