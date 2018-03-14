<?php
/**
 * @package   AdminTools
 * @copyright Copyright (c)2010-2018 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

use FOF30\Utils\Ip;

/** @var Akeeba\AdminTools\Admin\View\ConfigureWAF\Html $this */

// Protect from unauthorized access
defined('_JEXEC') or die;

JHtml::_('formbehavior.chosen');

$tabclass = $this->longConfig ? '' : 'akeeba-tabs';

?>
<form name="adminForm" id="adminForm" action="index.php" method="post" class="akeeba-form--horizontal">
    <div class="<?php echo $tabclass?>">
        <?php if ($this->longConfig):?>
            <h4><?php echo JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPTGROUP_BASICSETTINGS'); ?></h4>
        <?php else:?>
        <label for="base" class="active">
            <?php echo JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPTGROUP_BASICSETTINGS'); ?>
        </label>
        <?php endif;?>
        <section id="base">
			<?php echo $this->loadAnyTemplate('admin:com_admintools/ConfigureWAF/base'); ?>
        </section>

		<?php if ($this->longConfig):?>
            <h4><?php echo JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPTGROUP_ACTIVEFILTERING'); ?></h4>
		<?php else:?>
            <label for="activefiltering">
				<?php echo JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPTGROUP_ACTIVEFILTERING'); ?>
            </label>
		<?php endif;?>
        <section id="activefiltering">
			<?php echo $this->loadAnyTemplate('admin:com_admintools/ConfigureWAF/activefiltering'); ?>
        </section>

		<?php if ($this->longConfig):?>
            <h4><?php echo JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPTGROUP_JHARDENING'); ?></h4>
		<?php else:?>
            <label for="jhardening">
				<?php echo JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPTGROUP_JHARDENING'); ?>
            </label>
		<?php endif;?>
        <section id="jhardening">
			<?php echo $this->loadAnyTemplate('admin:com_admintools/ConfigureWAF/jhardening'); ?>
        </section>

		<?php if ($this->longConfig):?>
            <h4><?php echo JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPTGROUP_FINGERPRINTING'); ?></h4>
		<?php else:?>
            <label for="fingerprinting">
				<?php echo JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPTGROUP_FINGERPRINTING'); ?>
            </label>
		<?php endif;?>
        <section id="fingerprinting">
			<?php echo $this->loadAnyTemplate('admin:com_admintools/ConfigureWAF/fingerprinting'); ?>
        </section>

		<?php if ($this->longConfig):?>
            <h4><?php echo JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_LBL_PROJECTHONEYPOT'); ?></h4>
		<?php else:?>
            <label for="projecthoneypot">
				<?php echo JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_LBL_PROJECTHONEYPOT'); ?>
            </label>
		<?php endif;?>
        <section id="projecthoneypot">
			<?php echo $this->loadAnyTemplate('admin:com_admintools/ConfigureWAF/projecthoneypot'); ?>
        </section>

		<?php if ($this->longConfig):?>
            <h4><?php echo JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPTGROUP_EXCEPTIONS'); ?></h4>
		<?php else:?>
            <label for="exceptions">
				<?php echo JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPTGROUP_EXCEPTIONS'); ?>
            </label>
		<?php endif;?>
        <section id="exceptions">
			<?php echo $this->loadAnyTemplate('admin:com_admintools/ConfigureWAF/exceptions'); ?>
        </section>

		<?php if ($this->longConfig):?>
            <h4><?php echo JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_LBL_TSR'); ?></h4>
		<?php else:?>
            <label for="tsr">
				<?php echo JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_LBL_TSR'); ?>
            </label>
		<?php endif;?>
        <section id="tsr">
			<?php echo $this->loadAnyTemplate('admin:com_admintools/ConfigureWAF/tsr'); ?>
        </section>

		<?php if ($this->longConfig):?>
            <h4><?php echo JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPTGROUP_LOGGINGANDREPORTING'); ?></h4>
		<?php else:?>
            <label for="logging">
				<?php echo JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPTGROUP_LOGGINGANDREPORTING'); ?>
            </label>
		<?php endif;?>
        <section id="logging">
			<?php echo $this->loadAnyTemplate('admin:com_admintools/ConfigureWAF/logging'); ?>
        </section>

        <?php if ($this->longConfig):?>
            <h4><?php echo JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_CUSTOMMESSAGE_HEADER'); ?></h4>
		<?php else:?>
            <label for="custom">
				<?php echo JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_CUSTOMMESSAGE_HEADER'); ?>
            </label>
		<?php endif;?>
        <section id="custom">
			<?php echo $this->loadAnyTemplate('admin:com_admintools/ConfigureWAF/custom'); ?>
        </section>
    </div>

    <input type="hidden" name="option" value="com_admintools"/>
    <input type="hidden" name="view" value="ConfigureWAF"/>
    <input type="hidden" name="task" value="save"/>
    <input type="hidden" name="<?php echo $this->container->platform->getToken(true); ?>" value="1"/>
</form>
