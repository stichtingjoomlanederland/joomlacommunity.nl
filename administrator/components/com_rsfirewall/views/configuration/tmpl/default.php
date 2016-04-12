<?php
/**
 * @package    RSFirewall!
 * @copyright  (c) 2009 - 2016 RSJoomla!
 * @link       https://www.rsjoomla.com
 * @license    GNU General Public License http://www.gnu.org/licenses/gpl-3.0.en.html
 */

defined('_JEXEC') or die('Restricted access');

//keep session alive while editing
JHtml::_('behavior.keepalive');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
?>
<script type="text/javascript">
Joomla.submitbutton = function(task)
{
	if (task == 'configuration.cancel') {
		return Joomla.submitform(task, document.getElementById('component-form'));
	}
	
	// validation is done manually, here:
	
	// backend password validation
	if (isChecked('backend_password_enabled') && getEl('backend_password').value.length > 0) {
		if (getEl('backend_password').value.length < 6) {
			return alert('<?php echo JText::_('COM_RSFIREWALL_BACKEND_PASSWORD_LENGTH_ERROR', true); ?>');
		} else if (getEl('backend_password').value != getEl('backend_password2').value) {
			return alert('<?php echo JText::_('COM_RSFIREWALL_BACKEND_PASSWORDS_DO_NOT_MATCH', true); ?>');
		}
	}
	
	Joomla.submitform(task, document.getElementById('component-form'));
}

var ctrl  = '<?php echo $this->form->getFormControl(); ?>';

function getEl(name, index) {
	var index = parseInt(index) > 0 ? index : 0;
	return document.getElementsByName(ctrl + '[' + name + ']')[index];
}

function isChecked(name, value) {
	if (typeof value == 'undefined') {
		// by default we search for 1
		value = 1;
	}
	
	var items = document.getElementsByName(ctrl + '[' + name + ']');
	
	for (var i=0; i<items.length; i++) {
		var el = items[i];
		if (el.value == value && el.checked == true) {
			return true;
		}
	}
	
	return false;
}

function checkAllCountries(value) {
	var items = document.getElementsByName('jform[blocked_countries][]');
	for (var i=0; i<items.length; i++) {
		items[i].checked = value;
	}
}

function fixCheckAllCountries() {
	var items 	= document.getElementsByName('jform[blocked_countries][]');
	var checked = 0;
	for (var i=0; i<items.length; i++) {
		if (items[i].checked == true) {
			checked++;
		}
	}
	var checkAll = document.getElementsByName('jform[blocked_countries_checkall][]')[0];
	if (checked == 0 || checked < items.length) {
		checkAll.checked = false;
	} else if (checked == items.length) {
		checkAll.checked = true;
	}
}

RSFirewall.Continents = {
	'--': ['A1', 'A2', 'O1'],
	'EU': ['AD', 'AL', 'AT', 'AX', 'BA', 'BE', 'BG', 'BY', 'CH', 'CZ', 'DE', 'DK', 'EE', 'ES', 'EU', 'FI', 'FO', 'FR', 'GB', 'GG', 'GI', 'GR', 'HR', 'HU', 'IE', 'IM', 'IS', 'IT', 'JE', 'LI', 'LT', 'LU', 'LV', 'MC', 'MD', 'ME', 'MK', 'MT', 'NL', 'NO', 'PL', 'PT', 'RO', 'RS', 'RU', 'SE', 'SI', 'SJ', 'SK', 'SM', 'TR', 'UA', 'VA'],
	'AS': ['AE', 'AF', 'AM', 'AP', 'AZ', 'BD', 'BH', 'BN', 'BT', 'CC', 'CN', 'CX', 'CY', 'GE', 'HK', 'ID', 'IL', 'IN', 'IO', 'IQ', 'IR', 'JO', 'JP', 'KG', 'KH', 'KP', 'KR', 'KW', 'KZ', 'LA', 'LB', 'LK', 'MM', 'MN', 'MO', 'MV', 'MY', 'NP', 'OM', 'PH', 'PK', 'PS', 'QA', 'SA', 'SG', 'SY', 'TH', 'TJ', 'TL', 'TM', 'TW', 'UZ', 'VN', 'YE'],
	'NA': ['AG', 'AI', 'AN', 'AW', 'BB', 'BM', 'BS', 'BZ', 'CA', 'CR', 'CU', 'DM', 'DO', 'GD', 'GL', 'GP', 'GT', 'HN', 'HT', 'JM', 'KN', 'KY', 'LC', 'MQ', 'MS', 'MX', 'NI', 'PA', 'PM', 'PR', 'SV', 'TC', 'TT', 'US', 'VC', 'VG', 'VI'],
	'AF': ['AO', 'BF', 'BI', 'BJ', 'BW', 'CD', 'CF', 'CG', 'CI', 'CM', 'CV', 'DJ', 'DZ', 'EG', 'EH', 'ER', 'ET', 'GA', 'GH', 'GM', 'GN', 'GQ', 'GW', 'KE', 'KM', 'LR', 'LS', 'LY', 'MA', 'MG', 'ML', 'MR', 'MU', 'MW', 'MZ', 'NA', 'NE', 'NG', 'RE', 'RW', 'SC', 'SD', 'SH', 'SL', 'SN', 'SO', 'ST', 'SZ', 'TD', 'TG', 'TN', 'TZ', 'UG', 'YT', 'ZA', 'ZM', 'ZW'],
	'AN': ['AQ', 'BV', 'GS', 'HM', 'TF'],
	'SA': ['AR', 'BO', 'BR', 'CL', 'CO', 'EC', 'FK', 'GF', 'GY', 'PE', 'PY', 'SR', 'UY', 'VE'],
	'OC': ['AS', 'AU', 'CK', 'FJ', 'FM', 'GU', 'KI', 'MH', 'MP', 'NC', 'NF', 'NR', 'NU', 'NZ', 'PF', 'PG', 'PN', 'PW', 'SB', 'TK', 'TO', 'TV', 'UM', 'VU', 'WF', 'WS']
};

function checkCountries(el) {
	for (var continent in RSFirewall.Continents) {
		if (el.value == continent) {
			var countries = RSFirewall.Continents[continent];
			for (var i = 0; i < countries.length; i++) {
				var country = countries[i];
				jQuery('#jform_blocked_countries').find('[value="' + country + '"]').prop('checked', el.checked);
			}
		}
	}
}

function fixCheckContinents() {
	var continents = document.getElementsByName('jform[blocked_continents][]');
	for (var i = 0; i < continents.length; i++) {
		var countries = RSFirewall.Continents[continents[i].value];
		var checked	  = 0;
		for (var j = 0; j < countries.length; j++) {
			if (jQuery('#jform_blocked_countries').find('[value="' + countries[j] + '"]').prop('checked')) {
				checked++;
			}
		}
		continents[i].checked = checked == countries.length;
	}
}
</script>
<form action="<?php echo JRoute::_('index.php?option=com_rsfirewall&view=configuration'); ?>" method="post" name="adminForm" id="component-form" class="form-validate" enctype="multipart/form-data" autocomplete="off">
	<div id="j-sidebar-container" class="span2">
		<?php echo $this->sidebar; ?>
	</div>
	<div id="j-main-container" class="span10">
	<?php
	foreach ($this->fieldsets as $name => $fieldset) {
		// add the tab title
		$this->tabs->addTitle($fieldset->label, $fieldset->name);
		
		// prepare the content
		$this->fieldset =& $fieldset;
		$this->fields 	= $this->form->getFieldset($fieldset->name);
		$content = $this->loadTemplate($fieldset->name);
		
		// add the tab content
		$this->tabs->addContent($content);
	}
	
	// render tabs
	$this->tabs->render();
	?>
		<div>
		<?php echo JHtml::_('form.token'); ?>
		<input type="hidden" name="option" value="com_rsfirewall" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="controller" value="configuration" />
		</div>
	</div>
</form>
<script type="text/javascript">
jQuery(document).ready(function($){
	fixCheckAllCountries();
	fixCheckContinents();
	
	jQuery(document.getElementsByName('jform[blocked_countries][]')).change(function(){
		fixCheckAllCountries();
		fixCheckContinents();
	});
	
	jQuery(document.getElementsByName('jform[blocked_continents][]')).change(function(){
		fixCheckAllCountries();
		fixCheckContinents();
	});
});
</script>