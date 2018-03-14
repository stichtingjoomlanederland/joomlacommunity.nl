<?php
/**
 * @package   AdminTools
 * @copyright Copyright (c)2010-2018 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

use Akeeba\AdminTools\Admin\Helper\Select;

/** @var $this Akeeba\AdminTools\Admin\View\ScanAlerts\Html  */

defined('_JEXEC') or die;

$subtitle = JText::sprintf('COM_ADMINTOOLS_TITLE_SCANALERT_EDIT', $this->item->scan_id);
JToolbarHelper::title(JText::_('COM_ADMINTOOLS') . ' &ndash; <small>' . $subtitle . '</small>', 'admintools');
$tabs_class = '';

if ($this->generateDiff && ($this->fstatus == 'modified'))
{
    $tabs_class = 'akeeba-tabs';
}
?>
<form name="adminForm" id="adminForm" action="index.php" method="post" class="akeeba-form--horizontal">
	<div class="akeeba-panel--information">
		<header class="akeeba-block-header">
            <h3><?php echo JText::_('COM_ADMINTOOLS_LBL_SCANALERT_FILEINFO'); ?></h3>
        </header>

		<table class="akeeba-table--striped">
			<tr>
				<td>
					<?php echo JText::_('COM_ADMINTOOLS_LBL_SCANALERTS_PATH'); ?>
				</td>
				<td>
					<?php echo $this->item->path ?>
				</td>
			</tr>
			<tr>
				<td>
					<?php echo JText::_('COM_ADMINTOOLS_LBL_SCANALERT_SCANDATE'); ?>
				</td>
				<td>
					<?php echo $this->scanDate->format(JText::_('DATE_FORMAT_LC2') . ' T', true) ?>
				</td>
			</tr>
			<tr>
				<td>
					<?php echo JText::_('COM_ADMINTOOLS_LBL_SCANALERTS_STATUS'); ?>
				</td>
				<td>
					<span
						class="admintools-scanfile-<?php echo $this->fstatus ?> <?php if (!$this->item->threat_score): ?>admintools-scanfile-nothreat<?php endif ?>">
						<?php echo JText::_('COM_ADMINTOOLS_LBL_SCANALERTS_STATUS_' . $this->fstatus) ?>
					</span>
				</td>
			</tr>
			<tr>
				<td>
					<?php echo JText::_('COM_ADMINTOOLS_LBL_SCANALERTS_THREAT_SCORE'); ?>
				</td>
				<td>
					<span class="admintools-scanfile-threat-<?php echo $this->threatindex ?>">
						<?php echo $this->item->threat_score ?>
					</span>
				</td>
			</tr>
			<tr>
				<td>
					<?php echo JText::_('COM_ADMINTOOLS_LBL_SCANALERTS_ACKNOWLEDGED'); ?>
				</td>
				<td>
                    <?php echo \JHtml::_('FEFHelper.select.booleanswitch', 'acknowledged', $this->item->acknowledged)?>
				</td>
			</tr>

		</table>

	</div>

    <div class="<?php echo $tabs_class?>">
    <?php if ($this->generateDiff && ($this->fstatus == 'modified')):?>
        <label for="diff" class="active"><?php echo JText::_('COM_ADMINTOOLS_LBL_SCANALERT_DIFF')?></label>
        <section id="diff">
            <pre><code class="<?php echo $this->suspiciousFile ? 'php' : 'diff' ?>"><?php echo htmlentities($this->item->diff); ?></code></pre>
        </section>
    <?php endif; ?>

    <?php if ($this->generateDiff && ($this->fstatus == 'modified')):?>
        <label for="source"><?php echo JText::_('COM_ADMINTOOLS_LBL_SCANALERT_SOURCE')?></label>
    <?php else:?>
        <h4><?php echo JText::_('COM_ADMINTOOLS_LBL_SCANALERT_SOURCE') ?></h4>
    <?php endif; ?>

        <section id="source">
            <div class="akeeba-block--warning--small">
                <?php echo JText::_('COM_ADMINTOOLS_LBL_SCANALERT_SOURCE_NOTE')?>
            </div>

            <div class="akeeba-form-group">
                <label><?php echo JText::_('COM_ADMINTOOLS_LBL_SCANALERTS_MD5'); ?></label>

                <div>
                    <span class="help-block"><?php echo @md5_file(JPATH_SITE . '/' . $this->item->path) ?></span>
                </div>
            </div>
            <div style="clear:left"></div>

            <pre><?php echo $this->item->getFileSourceForDisplay(true); ?></pre>
        </div>
    </div>
    <input type="hidden" name="option" value="com_admintools"/>
    <input type="hidden" name="view" value="ScanAlerts"/>
    <input type="hidden" name="task" value=""/>
    <input type="hidden" name="admintools_scanalert_id" value="<?php echo $this->item->admintools_scanalert_id ?>"/>
    <input type="hidden" name="<?php echo $this->container->platform->getToken(true); ?>" value="1"/>
</form>
