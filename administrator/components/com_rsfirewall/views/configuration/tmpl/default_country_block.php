<?php
/**
 * @package    RSFirewall!
 * @copyright  (c) 2009 - 2016 RSJoomla!
 * @link       https://www.rsjoomla.com
 * @license    GNU General Public License http://www.gnu.org/licenses/gpl-3.0.en.html
 */

defined('_JEXEC') or die('Restricted access');

// set description if required
if (isset($this->fieldset->description) && !empty($this->fieldset->description)) { ?>
	<div class="com-rsfirewall-tooltip"><?php echo JText::_($this->fieldset->description); ?><br />
	<a href="https://www.rsjoomla.com/support/documentation/rsfirewall-user-guide/frequently-asked-questions/how-do-i-use-country-blocking-and-where-do-i-get-geoipdat-.html" target="_blank"><?php echo JText::_('COM_RSFIREWALL_GEOIP_DOCUMENTATION_LINK'); ?></a></div>
	<?php if (!$this->geoip->native) { ?>
		<?php if (!$this->geoip->uploaded) { ?>
		<div class="com-rsfirewall-not-ok"><p><?php echo JText::sprintf('COM_RSFIREWALL_GEOIP_DB_NOT_FOUND', $this->geoip->path); ?></p></div>
		<?php } else { ?>
		<div class="com-rsfirewall-ok"><p><?php echo JText::sprintf('COM_RSFIREWALL_GEOIP_DB_FOUND', $this->geoip->path); ?></p></div>
		<?php } ?>
	<?php } else { ?>
		<?php if ($this->geoip->nativedb) { ?>
		<div class="com-rsfirewall-ok"><p><?php echo JText::_('COM_RSFIREWALL_GEOIP_NATIVE_DB_FOUND'); ?></p></div>
		<?php } else { ?>
		<div class="com-rsfirewall-not-ok"><p><?php echo JText::_('COM_RSFIREWALL_GEOIP_NATIVE_DB_NOT_FOUND'); ?></p></div>
		<?php } ?>
	<?php } ?>
<?php } ?>
<?php
$this->field->startFieldset();
foreach ($this->fields as $field) {
	if ($field->fieldname == 'geoip_upload' && $this->geoip->native) {
		continue;
	}
	
	$this->field->showField($field->hidden ? '' : $field->label, $field->input);
}
$this->field->endFieldset();