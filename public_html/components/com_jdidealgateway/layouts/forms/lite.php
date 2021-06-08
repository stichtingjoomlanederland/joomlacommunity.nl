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
JHtml::stylesheet('com_jdidealgateway/payment.css', array('relative' => true, 'version' => 'auto'));

if (!$data->silent)
{
	// Show custom HTML
	echo $data->custom_html;
}
?>
<form method="post" action="<?php echo $url; ?>" name="idealform<?php echo $data->logid; ?>">
	<input type="hidden" name="merchantID" value="<?php echo $data->merchantID; ?>">
	<input type="hidden" name="subID" value="<?php echo $data->subID; ?>">
	<input type="hidden" name="amount" value="<?php echo $data->amount; ?>">
	<input type="hidden" name="purchaseID" value="<?php echo $data->purchaseID; ?>">
	<input type="hidden" name="language" value="<?php echo $data->language; ?>">
	<input type="hidden" name="currency" value="<?php echo $data->currency; ?>">
	<input type="hidden" name="description" value="<?php echo $data->description; ?>">
	<input type="hidden" name="hash" value="<?php echo $data->shasign; ?>">
	<input type="hidden" name="paymentType" value="<?php echo $data->paymentType; ?>">
	<input type="hidden" name="validUntil" value="<?php echo $data->validUntil; ?>">
	<?php echo $data->products; ?>
	<input type="hidden" name="urlSuccess" value="<?php echo $data->urlSuccess; ?>">
	<input type="hidden" name="urlCancel" value="<?php echo $data->urlCancel; ?>">
	<input type="hidden" name="urlError" value="<?php echo $data->urlError; ?>">
	<?php if (!$data->silent) : ?>
		<?php
		echo JHtml::link(
			$root,
			JText::_('COM_ROPAYMENTS_PAY_WITH_IDEAL'),
			'onclick="document.idealform' . $data->logid . '.submit(); return false;"'
		);
		?>
	<?php endif; ?>
</form>
<?php
// Do we need to redirect
$paymentInfo = '';
$redirect    = $data->silent ? 'direct' : $jdideal->get('redirect', 'wait');

switch ($redirect)
{
	case 'direct':
		// Go straight to the bank
		$paymentInfo = '<script type="text/javascript">';
		$paymentInfo .= '	document.idealform' . $data->logid . '.submit();';
		$paymentInfo .= '</script>';
		break;
	case 'timer':
		// Show timer before going to bank
		$paymentInfo = '<div id="showtimer">' . JText::_('COM_ROPAYMENTS_REDIRECT_5_SECS');
		$paymentInfo .= ' ' . JHtml::_('link', '', JText::_('COM_ROPAYMENTS_DO_NOT_REDIRECT'), array('onclick' => 'clearTimeout(timeout);return false;')) . '</div>';
		$paymentInfo .= '<script type="text/javascript">';
		$paymentInfo .= '	var timeout = setTimeout("document.idealform' . $data->logid . '.submit()", 5000);';
		$paymentInfo .= '</script>';
		break;
	case 'wait':
	default:
		break;
}

echo $paymentInfo;
