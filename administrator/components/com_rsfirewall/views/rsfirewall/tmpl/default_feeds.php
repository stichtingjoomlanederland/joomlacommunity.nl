<?php
/**
 * @package    RSFirewall!
 * @copyright  (c) 2009 - 2016 RSJoomla!
 * @link       https://www.rsjoomla.com
 * @license    GNU General Public License http://www.gnu.org/licenses/gpl-3.0.en.html
 */

defined('_JEXEC') or die('Restricted access');

foreach ($this->feeds as $feed) { ?>
	<h3><?php echo JText::sprintf('COM_RSFIREWALL_FEED', $this->escape($feed->title)); ?></h3>
	<table class="adminlist table table-striped">
	<thead>
	<tr>
		<th width="10%" nowrap="nowrap"><?php echo JText::_('COM_RSFIREWALL_FEED_DATE'); ?></th>
		<th class="title"><?php echo JText::_('COM_RSFIREWALL_FEED_TITLE'); ?></th>
	</tr>
	</thead>
	<tbody>
	<?php foreach ($feed->items as $i => $item) { ?>
	<tr class="row<?php echo $i % 2; ?>">
		<td width="1%" nowrap="nowrap"><?php echo $this->escape($item->date); ?></td>
		<td><a href="<?php echo $this->escape($item->link); ?>" target="_blank"><?php echo $this->escape($item->title); ?></a></td>
	</tr>
	<?php } ?>
	</tbody>
	</table>
<?php } ?>