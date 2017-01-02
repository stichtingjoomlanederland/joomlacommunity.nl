<?php
/**
 * @package   AdminTools
 * @copyright 2010-2016 Akeeba Ltd / Nicholas K. Dionysopoulos
 * @license   GNU General Public License version 3, or later
 */

defined('_JEXEC') or die;

/** @var  \Akeeba\AdminTools\Admin\View\WebApplicationFirewall\Html  $this */
?>
<?php if (!$this->pluginExists): ?>
    <p class="alert alert-error">
        <a class="close" data-dismiss="alert" href="#">×</a>
        <?php echo \JText::_('COM_ADMINTOOLS_ERR_CONFIGUREWAF_NOPLUGINEXISTS'); ?>
    </p>
<?php elseif (!$this->pluginActive): ?>
    <p class="alert alert-error">
        <a class="close" data-dismiss="alert" href="#">×</a>
        <?php echo \JText::_('COM_ADMINTOOLS_ERR_CONFIGUREWAF_NOPLUGINACTIVE'); ?>
        <br/>
        <a href="index.php?option=com_plugins&client=site&filter_type=system&search=admin%20tools">
            <?php echo \JText::_('COM_ADMINTOOLS_ERR_CONFIGUREWAF_NOPLUGINACTIVE_DOIT'); ?>
        </a>
    </p>
<?php endif; ?>