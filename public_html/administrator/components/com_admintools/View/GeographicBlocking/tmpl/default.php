<?php
/**
 * @package   admintools
 * @copyright Copyright (c)2010-2019 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

// Protect from unauthorized access
defined('_JEXEC') or die;

/** @var  \Akeeba\AdminTools\Admin\View\GeographicBlocking\Html  $this */

?>
<?php if (!$this->hasPlugin): ?>
    <div class="akeeba-block--warning">
        <h3><?php echo JText::_('COM_ADMINTOOLS_LBL_GEOGRAPHICBLOCKING_GEOIPPLUGINSTATUS'); ?></h3>

        <p><?php echo JText::_('COM_ADMINTOOLS_LBL_GEOGRAPHICBLOCKING_GEOIPPLUGINMISSING'); ?></p>

        <a class="akeeba-btn--primary" href="https://www.akeebabackup.com/download/akgeoip.html" target="_blank">
            <span class="akion-code-download"></span>
            <?php echo JText::_('COM_ADMINTOOLS_LBL_GEOGRAPHICBLOCKING_DOWNLOADGEOIPPLUGIN'); ?>
        </a>
    </div>

    <?php return; ?>
<?php endif; ?>
<?php if ($this->pluginNeedsUpdate): ?>
    <div class="akeeba-block--info">
        <h3><?php echo JText::_('COM_ADMINTOOLS_LBL_GEOGRAPHICBLOCKING_GEOIPPLUGINEXISTS'); ?></h3>

        <p><?php echo JText::_('COM_ADMINTOOLS_LBL_GEOGRAPHICBLOCKING_GEOIPPLUGINCANUPDATE'); ?></p>

        <a class="akeeba-btn--dark--small"
           href="index.php?option=com_admintools&view=ControlPanel&task=updategeoip&returnurl=<?php echo base64_encode('index.php?option=com_admintools&view=GeographicBlocking'); ?>&<?php echo $this->container->platform->getToken(true); ?>=1">
            <span class="akion-refresh"></span>
            <?php echo JText::_('COM_ADMINTOOLS_LBL_GEOGRAPHICBLOCKING_UPDATEGEOIPDATABASE'); ?>
        </a>
    </div>
<?php endif; ?>

<div class="akeeba-block--info">
    <h3><?php echo JText::_('COM_ADMINTOOLS_LBL_GEOGRAPHICBLOCKING_INFOHEAD'); ?></h3>
	<p>
        <?php echo JText::_('COM_ADMINTOOLS_LBL_GEOGRAPHICBLOCKING_INFO'); ?><br/>
        <?php echo JText::sprintf('COM_ADMINTOOLS_LBL_GEOGRAPHICBLOCKING_MYIP', $this->myIP, $this->country, $this->continent)?>
    </p>
	<p class="small"><?php echo JText::_('COM_ADMINTOOLS_LBL_GEOGRAPHICBLOCKING_MAXMIND'); ?></p>
</div>

<form action="index.php" method="post" name="adminForm" id="adminForm" class="akeeba-form--inline">
	<div class="akeeba-panel--primary" id="waf-continents">
		<header class="akeeba-block-header">
            <h3><?php echo JText::_('COM_ADMINTOOLS_LBL_GEOGRAPHICBLOCKING_CONTINENTS'); ?></h3>
        </header>

		<?php foreach($this->allContinents as $code => $name): ?>
        <?php $checked = in_array($code, $this->continents) ? 'checked="$checked"' : ''; ?>
        <div class="akeeba-form-group--checkbox">
            <label>
                <input type="checkbox" <?php echo $checked; ?> name="continent[<?php echo $code; ?>]" id="continent<?php echo $code; ?>">
                <?php echo $this->escape($name); ?>
            </label>
        </div>
        <?php endforeach; ?>
	</div>

	<div class="akeeba-panel--primary" id="waf-countries">
		<header class="akeeba-block-header">
            <h3><?php echo JText::_('COM_ADMINTOOLS_LBL_GEOGRAPHICBLOCKING_COUNTRIES'); ?></h3>
        </header>

		<table class="akeeba-table">
			<thead>
			<tr>
				<th colspan="3">
					<button class="akeeba-btn--dark"
							onclick="akeeba.jQuery('.country').attr('checked', 'checked');return false;"><?php echo JText::_('COM_ADMINTOOLS_LBL_GEOGRAPHICBLOCKING_ALL') ?></button>
					<button class="akeeba-btn--dark"
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
                    <td width="33%">
                        <?php $checked = in_array($code, $this->countries) ? 'checked="$checked"' : ''; ?>
                        <div class="akeeba-form-group">
                            <div>
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
	</div>

    <input type="hidden" name="option" value="com_admintools"/>
    <input type="hidden" name="view" value="GeographicBlocking"/>
    <input type="hidden" name="task" value=""/>
    <input type="hidden" name="<?php echo $this->container->platform->getToken(true); ?>" value="1"/>
</form>
