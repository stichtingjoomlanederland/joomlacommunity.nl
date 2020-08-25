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
use Joomla\CMS\Language\Text;

defined('_JEXEC') or die;

/** @var JdidealgatewayViewProfile $this */

?>
<div class="span10">
	<?php echo $this->pspForm->renderFieldset('abn-internetkassa'); ?>
</div>
<div class="span2">
	<table class="table table-striped">
		<caption><?php echo Text::_('COM_ROPAYMENTS_DASHBOARD_LINKS')?></caption>
		<thead><tr><th><?php echo Text::_('COM_ROPAYMENTS_PRODUCTION_DASHBOARD'); ?></th><th><?php echo Text::_('COM_ROPAYMENTS_TEST_DASHBOARD'); ?></th></tr></thead>
		<tfoot><tr><td></td><td></td></tr></tfoot>
		<tbody>
			<tr>
				<td class="center"><?php echo HTMLHelper::_('link', 'https://internetkassa.abnamro.nl/ncol/prod/admin_ABN.asp', HTMLHelper::_('image', 'com_jdidealgateway/abnamro.jpg', 'ABN AMRO', false, true), 'target="_new"'); ?></td>
				<td class="center"><?php echo HTMLHelper::_('link', 'https://internetkassa.abnamro.nl/ncol/test/admin_ABN.asp', HTMLHelper::_('image', 'com_jdidealgateway/abnamro.jpg', 'ABN AMRO', false, true), 'target="_new"'); ?></td>
			</tr>
		</tbody>
	</table>
</div>
