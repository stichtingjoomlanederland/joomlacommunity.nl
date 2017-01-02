<?php
/**
 * @package   AdminTools
 * @copyright 2010-2016 Akeeba Ltd / Nicholas K. Dionysopoulos
 * @license   GNU General Public License version 3, or later
 */

// Protect from unauthorized access
defined('_JEXEC') or die;

/** @var  \Akeeba\AdminTools\Admin\View\GeographicBlocking\Html  $this */

?>
<?php if (!$this->hasPlugin): ?>
    <div class="well">
        <h3><?php echo \JText::_('COM_ADMINTOOLS_LBL_GEOGRAPHICBLOCKING_GEOIPPLUGINSTATUS'); ?></h3>

        <p><?php echo \JText::_('COM_ADMINTOOLS_LBL_GEOGRAPHICBLOCKING_GEOIPPLUGINMISSING'); ?></p>

        <a class="btn btn-primary" href="https://www.akeebabackup.com/download/akgeoip.html" target="_blank">
            <span class="icon icon-white icon-download"></span>
            <?php echo \JText::_('COM_ADMINTOOLS_LBL_GEOGRAPHICBLOCKING_DOWNLOADGEOIPPLUGIN'); ?>
        </a>
    </div>

    <?php return; ?>
<?php endif; ?>
<?php if ($this->pluginNeedsUpdate): ?>
    <div class="well well-small">
        <h3><?php echo \JText::_('COM_ADMINTOOLS_LBL_GEOGRAPHICBLOCKING_GEOIPPLUGINEXISTS'); ?></h3>

        <p><?php echo \JText::_('COM_ADMINTOOLS_LBL_GEOGRAPHICBLOCKING_GEOIPPLUGINCANUPDATE'); ?></p>

        <a class="btn btn-small"
           href="index.php?option=com_admintools&view=ControlPanel&task=updategeoip&returnurl=<?php echo base64_encode('index.php?option=com_admintools&view=GeographicBlocking'); ?>&<?php echo \JFactory::getSession()->getFormToken(); ?>=1">
            <span class="icon icon-refresh"></span>
            <?php echo \JText::_('COM_ADMINTOOLS_LBL_GEOGRAPHICBLOCKING_UPDATEGEOIPDATABASE'); ?>
        </a>
    </div>
<?php endif; ?>

<div class="alert alert-info">
	<a class="close" data-dismiss="alert" href="#">×</a>
    <h3><?php echo \JText::_('COM_ADMINTOOLS_LBL_GEOGRAPHICBLOCKING_INFOHEAD'); ?></h3>
	<p><?php echo \JText::_('COM_ADMINTOOLS_LBL_GEOGRAPHICBLOCKING_INFO'); ?></p>
	<p class="small"><?php echo \JText::_('COM_ADMINTOOLS_LBL_GEOGRAPHICBLOCKING_MAXMIND'); ?></p>
</div>

<form action="index.php" method="post" name="adminForm" id="adminForm" class="form form-inline">
	<input type="hidden" name="option" value="com_admintools"/>
	<input type="hidden" name="view" value="GeographicBlocking"/>
	<input type="hidden" name="task" value=""/>
	<input type="hidden" name="<?php echo \JFactory::getSession()->getFormToken(); ?>" value="1"/>

	<fieldset id="waf-continents">
		<legend><?php echo \JText::_('COM_ADMINTOOLS_LBL_GEOGRAPHICBLOCKING_CONTINENTS'); ?></legend>

		<?php foreach($this->allContinents as $code => $name): ?>
        <?php $checked = in_array($code, $this->continents) ? 'checked="$checked"' : ''; ?>
        <div class="control-group">
            <div class="controls">
                <label class="checkbox">
                    <input type="checkbox" <?php echo $checked; ?> name="continent[<?php echo $code; ?>]" id="continent<?php echo $code; ?>">
                    <?php echo $this->escape($name); ?>

                </label>
            </div>
        </div>
        <?php endforeach; ?>
	</fieldset>

	<fieldset id="waf-countries">
		<legend><?php echo \JText::_('COM_ADMINTOOLS_LBL_GEOGRAPHICBLOCKING_COUNTRIES'); ?></legend>

		<table class="table table-striped">
			<thead>
			<tr>
				<th colspan="3">
					<button class="btn"
							onclick="akeeba.jQuery('.country').attr('checked', 'checked');return false;"><?php echo JText::_('COM_ADMINTOOLS_LBL_GEOGRAPHICBLOCKING_ALL') ?></button>
					<button class="btn"
							onclick="akeeba.jQuery('.country').removeAttr('checked');return false;"><?php echo JText::_('COM_ADMINTOOLS_LBL_GEOGRAPHICBLOCKING_NONE') ?></button>
				</th>
			</tr>
			</thead>
			<tbody>
            <?php $i = 0 ?>
            <?php foreach($this->allCountries as $code => $name): ?>
                <?php if ($i % 3 == 0): ?>
                <tr>
                <?php endif; ?>
                    <?php $i++; ?>
                    <td>
                        <?php $checked = in_array($code, $this->countries) ? 'checked="$checked"' : ''; ?>
                        <div class="control-group">
                            <div class="controls">
                                <label class="checkbox">
                                    <input type="checkbox" <?php echo $checked; ?> name="country[<?php echo $code; ?>]" id="country<?php echo $code; ?>" class="country">
                                    <?php echo $this->escape($name); ?>

                                </label>
                            </div>
                        </div>
                    </td>
                <?php if ($i % 3 == 0): ?>
                </tr>
                <?php endif; ?>

            <?php endforeach; ?>

			</tbody>
		</table>
	</fieldset>
</form>