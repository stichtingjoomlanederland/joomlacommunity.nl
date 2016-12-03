<?php
/**
 * @package    RSFirewall!
 * @copyright  (c) 2009 - 2016 RSJoomla!
 * @link       https://www.rsjoomla.com
 * @license    GNU General Public License http://www.gnu.org/licenses/gpl-3.0.en.html
 */

defined('_JEXEC') or die('Restricted access');

JText::script('COM_RSFIREWALL_HASHES_CORRECT');
JText::script('COM_RSFIREWALL_CONFIRM_OVERWRITE_LOCAL_FILE');
JText::script('COM_RSFIREWALL_BUTTON_FAILED');
JText::script('COM_RSFIREWALL_BUTTON_PROCESSING');
JText::script('COM_RSFIREWALL_BUTTON_SUCCESS');
?>

<div class="rsfirewall-replace-original text-center" style="margin:25px">
	<button type="button" id="replace-original" class="btn btn-primary" style="margin-bottom:10px" onclick="RSFirewall.diffs.download('<?php echo $this->escape($this->filename); ?>', '<?php echo $this->escape($this->hashId); ?>', window.opener.document)"><?php echo JText::_('COM_RSFIREWALL_DOWNLOAD_ORIGINAL') ?></button>
</div>

<?php
// Output table
echo Diff::toTable(Diff::compare($this->remote, $this->local), '', '', array(
	JText::sprintf('COM_RSFIREWALL_REMOTE_FILE', $this->remoteFilename),
	JText::sprintf('COM_RSFIREWALL_LOCAL_FILE', realpath($this->localFilename))
));