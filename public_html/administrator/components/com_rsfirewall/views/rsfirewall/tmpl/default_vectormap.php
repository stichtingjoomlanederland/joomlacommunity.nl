<?php
/**
 * @package    RSFirewall!
 * @copyright  (c) 2009 - 2016 RSJoomla!
 * @link       https://www.rsjoomla.com
 * @license    GNU General Public License http://www.gnu.org/licenses/gpl-3.0.en.html
 */

defined('_JEXEC') or die('Restricted access');
$document = JFactory::getDocument();
$document->addStyleSheet(JURI::root(true).'/administrator/components/com_rsfirewall/assets/js/vmaps/jqvmap.css');
$document->addScript(JURI::root(true).'/administrator/components/com_rsfirewall/assets/js/vmaps/jquery.vmap.min.js');
$document->addScript(JURI::root(true).'/administrator/components/com_rsfirewall/assets/js/vmaps/jquery.vmap.world.js');

$document->addScriptDeclaration(
	'jQuery(document).ready(function(){
		RSFirewall.vmap.init("#com-rsfirewall-virtual-map");
	});');
?>
<h2><?php echo JText::_('COM_RSFIREWALL_ATTACKS_BLOCKED_REGION_BASED'); ?></h2>
<div id="com-rsfirewall-virtual-map" style="height: 400px;"></div>


