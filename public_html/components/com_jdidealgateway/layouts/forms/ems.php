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

/** @var array $displayData */
$jdideal = $displayData['jdideal'];
$data    = $displayData['data'];
$url     = $displayData['url'];
$root    = $displayData['root'];

// Load the stylesheet
JHtml::stylesheet('com_jdidealgateway/payment.css', null, true);

if (!$data->silent)
{
	// Show custom HTML
	echo $data->custom_html;
}
?>
<form method="post" action="<?php echo $url; ?>" id="idealform<?php echo $data->logid; ?>" name="idealform<?php echo $data->logid; ?>">
	<input type="hidden" name="txntype" value="sale">
	<input type="hidden" name="timezone" value="<?php echo $data->timezone; ?>"/>
	<input type="hidden" name="txndatetime" value="<?php echo $data->transactionDateTime; ?>"/>
	<input type="hidden" name="hash_algorithm" value="SHA256"/>
	<input type="hidden" name="hash" value="<?php echo $data->hash; ?>"/>
	<input type="hidden" name="storename" value="<?php echo $data->storeName; ?>"/>
	<input type="hidden" name="mode" value="payonly"/>
	<input type="hidden" name="paymentMethod" value="<?php echo $data->paymentMethod; ?>"/>
	<input type="hidden" name="currency" value="<?php echo $data->currency; ?>"/>
	<input type="hidden" name="chargetotal" value="<?php echo $data->amount; ?>"/>

	<?php if ($data->banks) : ?>
		<input type="hidden" name="idealIssuerID" value="<?php echo $data->banks; ?>"/>
	<?php endif; ?>

	<input type="hidden" name="orderId" value="<?php echo $data->orderNumber; ?>"/>
	<input type="hidden" name="invoiceNumber" value="<?php echo $data->invoiceNumber; ?>"/>
	<input type="hidden" name="language" value="<?php echo $jdideal->get('customerLanguage'); ?>"/>
	<input type="hidden" name="logId" value="<?php echo $data->logid; ?>"/>

	<!-- Redirect URLs -->
	<input type="hidden" name="responseFailURL" value="<?php echo $data->notifyurl; ?>">
	<input type="hidden" name="responseSuccessURL" value="<?php echo $data->notifyurl; ?>">
	<input type="hidden" name="transactionNotificationURL" value="<?php echo $data->notifyurl . '?customer=0'; ?>">

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
