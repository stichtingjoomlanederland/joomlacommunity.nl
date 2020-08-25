<?php
/**
 * @package    JDiDEAL
 *
 * @author     Roland Dalmulder <contact@rolandd.com>
 * @copyright  Copyright (C) 2009 - 2020 RolandD Cyber Produksi. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link       https://rolandd.com
 */

use Joomla\CMS\HTML\HTMLHelper;

defined('_JEXEC') or die;

/** @var JdidealgatewayViewMessage $this */

HTMLHelper::_('behavior.formvalidator');
HTMLHelper::_('behavior.keepalive');
HTMLHelper::_('formbehavior.chosen');

?>
<form action="<?php echo 'index.php?option=com_jdidealgateway&layout=edit&id=' . (int) $this->item->id; ?>" method="post" name="adminForm" id="adminForm" class="form-validate form-horizontal">
	<?php echo $this->form->renderFieldset('message'); ?>
	<input type="hidden" name="task" value="" />
	<?php echo HTMLHelper::_('form.token'); ?>
</form>
