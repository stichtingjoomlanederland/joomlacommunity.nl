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

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;

/** @var JdidealgatewayViewPay $this */

HTMLHelper::_('behavior.formvalidator');

$input  = Factory::getApplication()->input;
$silent = $input->getBool('silent', false);
?>
<div class="clear"></div>
<?php if ($silent) : ?>
	<div style="display: none">
<?php endif; ?>
<form name="adminForm" id="adminForm" method="post" class="form-validate" action="<?php echo Route::_('index.php?option=com_jdidealgateway&task=pay.sendmoney'); ?>">
	<fieldset>
		<legend><?php echo Text::_('COM_ROPAYMENTS_PAYMENT_FORM'); ?></legend>
			<div>
				<?php echo $this->form->renderField('user_email'); ?>
			</div>
			<div>
				<?php echo $this->form->renderField('amount'); ?>
			</div>
			<div>
				<?php echo $this->form->renderField('remark'); ?>
			</div>

			<?php if ($input->getString('order_number', false)): ?>
				<div>
					<?php echo $this->form->getLabel('order_number'); ?>
					<?php
						$this->form->setValue('order_number', '', $input->getString('order_number', ''));
						echo $this->form->getInput('order_number');
					?>
				</div>
			<?php endif; ?>
		<?php if (!$silent) : ?>
			<div id="paybox_button" class="submit">
				<div>
					<input type="submit" class="validate" id="submit" name="submit" value="<?php echo Text::_('COM_ROPAYMENTS_SEND_MONEY'); ?>" />
				</div>
			</div>
		<?php else : ?>
			<script type="text/javascript">
				document.adminForm.submit();
			</script>
		<?php endif; ?>
	</fieldset>
	<input type="hidden" name="task" value="pay.sendmoney" />
</form>
<?php if ($silent) : ?>
	</div>
	<?php echo Text::_('COM_ROPAYMENTS_REDIRECT_5_SECS'); ?>
<?php endif;
