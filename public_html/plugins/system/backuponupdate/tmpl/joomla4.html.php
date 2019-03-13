<?php
/**
 * @package   akeebabackup
 * @copyright Copyright (c)2006-2019 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

defined('_JEXEC') or die();
/**
 * @package    AkeebaBackup
 * @subpackage backuponupdate
 * @copyright  Copyright (c)2006-2019 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license    GNU General Public License version 3, or later
 *
 * @since      6.4.1
 *
 * This file contains the CSS for rendering the status (footer) icon for the Backup on Update plugin. The icon is only
 * rendered in the administrator backend of the site.
 *
 * You can override this file WITHOUT overwriting it. Copy this file into:
 *
 * administrator/templates/YOUR_TEMPLATE/html/plg_system_backuponupdate/joomla4.html.php
 *
 * where YOUR_TEMPLATE is the folder of the administrator template you are using. Modify that copy. It will be loaded
 * instead of the file in plugins/system/backuponupdate.
 */

$document = JFactory::getDocument();
$document->addScript('../media/com_akeeba/js/System.min.js');

$token = urlencode(JFactory::getSession()->getToken());
$js    = <<< JS
; // Work around broken third party Javascript

function akeeba_backup_on_update_toggle()
{
    window.jQuery.get('index.php?_akeeba_backup_on_update_toggle=$token', function() {
        location.reload(true);
    });
}

akeeba.System.documentReady(function() {
    var newListItemArray = document.getElementById('akeebabackup-bou-container').querySelectorAll('ul > li');
    var headerLinks = document.getElementById('header').querySelectorAll('div.header-items ul.nav');
    var newItem = newListItemArray[0];
    var headerLink = headerLinks[0];
    
    if (headerLink.childNodes.length < 2)
	{
		headerLink.appendChild(newItem);
	}
    else
	{
		headerLink.insertBefore(newItem, headerLink.childNodes[headerLink.childNodes.length - 2]);		
	}
    
    var oldChild = document.getElementById('akeebabackup-bou-container');
    oldChild.parentElement.removeChild(oldChild);
})

JS;

$document = JFactory::getApplication()->getDocument();

if (empty($document))
{
	$document = JFactory::getDocument();
}

if (empty($document))
{
	return;
}

$document->addScriptDeclaration($js);

?>
<div id="akeebabackup-bou-container">
	<ul>
		<li class="nav-item">
			<a class="nav-link dropdown-toggle" href="javascript:akeeba_backup_on_update_toggle()"
			   title="<?php echo JText::_('PLG_SYSTEM_BACKUPONUPDATE_LBL_POPOVER_CONTENT_' . ($params['active'] ? 'ACTIVE' : 'INACTIVE')) ?>">
				<span class="fa fa-akbou <?php echo $params['active'] ? 'fa-akbou-active' : 'fa-akbou-inactive' ?>"
					  aria-hidden="true"></span>
				<span class="sr-only">
					<?php echo JText::_('PLG_SYSTEM_BACKUPONUPDATE_LBL_' . ($params['active'] ? 'ACTIVE' : 'INACTIVE')) ?>
				</span>
			</a>
		</li>
	</ul>
</div>
