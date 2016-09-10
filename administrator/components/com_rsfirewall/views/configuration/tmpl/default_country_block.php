<?php
/**
 * @package    RSFirewall!
 * @copyright  (c) 2009 - 2016 RSJoomla!
 * @link       https://www.rsjoomla.com
 * @license    GNU General Public License http://www.gnu.org/licenses/gpl-3.0.en.html
 */

defined('_JEXEC') or die('Restricted access');

JText::script('COM_RSFIREWALL_DOWNLOAD_GEOIP_SERVER_ERROR');
JText::script('COM_RSFIREWALL_GEOIP_DB_CANNOT_DOWNLOAD');
JText::script('COM_RSFIREWALL_GEOIP_DB_CANNOT_DOWNLOAD_CONTINUED');

$blocked_countries = $this->config->get('blocked_countries');
$class = in_array('US', $blocked_countries) ? '' : 'com-rsfirewall-hidden';

// set description if required
if (isset($this->fieldset->description) && !empty($this->fieldset->description)) { ?>
	<div class="com-rsfirewall-tooltip"><?php echo JText::_($this->fieldset->description); ?><br />
	<a href="https://www.rsjoomla.com/support/documentation/rsfirewall-user-guide/frequently-asked-questions/how-do-i-use-country-blocking-and-where-do-i-get-geoipdat-.html" target="_blank"><?php echo JText::_('COM_RSFIREWALL_GEOIP_DOCUMENTATION_LINK'); ?></a></div>

	<?php if ($this->geoip->works) { ?>
		<div class="alert alert-success rsfirewall-geoip-works">
			<?php echo JText::_('COM_RSFIREWALL_GEOIP_SETUP_CORRECTLY'); ?>
		</div>
	<?php } ?>
	
	<?php if ($this->geoip->native) { ?>
		<div class="alert alert-success">
			<p><?php echo JText::_('COM_RSFIREWALL_GEOIP_NATIVE'); ?></p>
		</div>
		<?php if (!$this->geoip->dbv4) { ?>
			<div class="alert alert-error">
				<h4><?php echo JText::_('COM_RSFIREWALL_GEOIP_V4_DB'); ?></h4>
				<p><?php echo JText::_('COM_RSFIREWALL_GEOIP_NATIVE_DB_NOT_FOUND'); ?></p>
			</div>
		<?php } ?>
		<?php if (!$this->geoip->dbv6) { ?>
			<div class="alert alert-warning">
				<h4><?php echo JText::_('COM_RSFIREWALL_GEOIP_V6_DB'); ?></h4>
				<p><?php echo JText::_('COM_RSFIREWALL_GEOIP_NATIVE_DB_V6_NOT_FOUND'); ?></p>
			</div>
		<?php } ?>
	<?php } else { ?>
		<?php if (!$this->geoip->dbv4) { ?>
			<div class="alert alert-info">
				<h4><?php echo JText::_('COM_RSFIREWALL_GEOIP_V4_DB'); ?></h4>
				<p><?php echo JText::_('COM_RSFIREWALL_GEOIP_DB_V4_DOWNLOAD_INSTRUCTIONS'); ?></p>
				<p><a class="btn btn-primary" onclick="RSFirewall.GeoIPDownload(this, 'v4')"><i class="icon-refresh"></i> <?php echo JText::_('COM_RSFIREWALL_DOWNLOAD_GEOIP_DB_V4'); ?></a></p>
			</div>
		<?php } elseif (!empty($this->geoip->dbv4_old)) { ?>
			<div class="alert alert-info">
				<h4><?php echo JText::_('COM_RSFIREWALL_GEOIP_V4_DB'); ?></h4>
				<p><?php echo JText::sprintf('COM_RSFIREWALL_GEOIP_DB_UPDATE_INSTRUCTIONS', $this->geoip->dbv4_modified); ?></p>
				<p><a class="btn btn-primary" onclick="RSFirewall.GeoIPDownload(this, 'v4')"><i class="icon-refresh"></i> <?php echo JText::_('COM_RSFIREWALL_UPDATE_GEOIP_DB_V4'); ?></a></p>
			</div>
		<?php } ?>
		<?php if (!$this->geoip->dbv6) { ?>
			<div class="alert alert-info">
				<h4><?php echo JText::_('COM_RSFIREWALL_GEOIP_V6_DB'); ?></h4>
				<p><?php echo JText::_('COM_RSFIREWALL_GEOIP_DB_V6_DOWNLOAD_INSTRUCTIONS'); ?></p>
				<p><a class="btn btn-primary" onclick="RSFirewall.GeoIPDownload(this, 'v6')"><i class="icon-refresh"></i> <?php echo JText::_('COM_RSFIREWALL_DOWNLOAD_GEOIP_DB_V6'); ?></a></p>
			</div>
		<?php } elseif (!empty($this->geoip->dbv6_old)) { ?>
			<div class="alert alert-info">
				<h4><?php echo JText::_('COM_RSFIREWALL_GEOIP_V6_DB'); ?></h4>
				<p><?php echo JText::sprintf('COM_RSFIREWALL_GEOIP_DB_UPDATE_INSTRUCTIONS', $this->geoip->dbv6_modified); ?></p>
				<p><a class="btn btn-primary" onclick="RSFirewall.GeoIPDownload(this, 'v6')"><i class="icon-refresh"></i> <?php echo JText::_('COM_RSFIREWALL_UPDATE_GEOIP_DB_V6'); ?></a></p>
			</div>
		<?php } ?>
	<?php } ?>
<?php } ?>
	<div class="alert alert-danger <?php echo $class ?>" id="us-country-blocked">
        <?php echo JText::_('COM_RSFIREWALL_YOU_BANNED_US'); ?>
	</div>
<?php
$this->field->startFieldset();
foreach ($this->fields as $field) {
	if ($field->fieldname == 'geoip_upload' && $this->geoip->native) {
		continue;
	}
	
	$input = $field->input;
	
	// Let's disable the checkboxes if GeoIP is not available
	if (!$this->geoip->works) {
		$input = str_replace('type="checkbox"', 'type="checkbox" disabled', $field->input);		
	}

	$this->field->showField($field->hidden ? '' : $field->label, $input);
}
$this->field->endFieldset();