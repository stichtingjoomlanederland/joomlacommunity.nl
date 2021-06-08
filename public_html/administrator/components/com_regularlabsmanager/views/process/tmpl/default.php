<?php
/**
 * @package         Regular Labs Extension Manager
 * @version         7.4.9
 * 
 * @author          Peter van Westen <info@regularlabs.com>
 * @link            http://www.regularlabs.com
 * @copyright       Copyright © 2021 Regular Labs All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory as JFactory;
use Joomla\CMS\HTML\HTMLHelper as JHtml;
use Joomla\CMS\Language\Text as JText;
use Joomla\CMS\Session\Session as JSession;
use RegularLabs\Library\Document as RL_Document;

JHtml::_('jquery.framework');
JHtml::_('bootstrap.framework');

$task = JFactory::getApplication()->input->get('task');

RL_Document::loadMainDependencies();

$update_extensionmanager = count($this->items) > 1 && isset($this->items['extensionmanager']) && $task !== 'reinstall';

$ids = $update_extensionmanager ? ['extensionmanager'] : array_keys($this->items);

$options = [
	'ids'   => $ids,
	'token' => JSession::getFormToken(),
];
RL_Document::scriptOptions($options, 'Extension Manager');
JText::script('RLEM_INSTALLATION_FAILED');

RL_Document::script('regularlabsmanager/process.min.js', '7.4.9');
RL_Document::style('regularlabsmanager/process.min.css', '7.4.9');
?>

<div id="rlem">
	<div class="titles">
		<div class="title pre process">
			<h2>
				<?php echo JText::_('RLEM_TITLE_' . strtoupper($task)); ?>:
				<span class="btn btn-primary" onclick="RegularLabsManagerProcess.process('<?php echo $task; ?>');">
					<?php echo JText::_('RL_START'); ?>
				</span>
			</h2>
			<?php if ($update_extensionmanager): ?>
				<div class="alert alert-warning alert-noclose">
					<?php echo JText::_('RLEM_UPDATE_EXTENSIONMANAGER_FIRST'); ?>
				</div>
			<?php endif; ?>
		</div>

		<div class="title failed process hide">
			<h2>
				<?php echo JText::_('RLEM_TITLE_' . strtoupper($task)); ?>:
				<span class="btn btn-primary" onclick="RegularLabsManagerProcess.process('<?php echo $task; ?>', true);">
					<?php echo JText::_('RLEM_TITLE_RETRY'); ?>
				</span>
			</h2>

			<div class="alert alert-danger alert-noclose errors" style="display:none;">
				<h4 class="alert-heading"><?php echo JText::_('ERROR'); ?></h4>
				<div>
					<p class="alert-message"><?php echo JText::_('RLEM_MEET_REQUIREMENTS'); ?></p>
				</div>
			</div>
		</div>

		<div class="title processing hide">
			<h2><?php echo JText::sprintf('RLEM_PROCESS_' . strtoupper($task), '...'); ?></h2>
		</div>

		<div class="title done process hide">
			<div class="alert alert-success alert-noclose">
				<h2><?php echo JText::_('RLEM_TITLE_FINISHED'); ?></h2>
				<?php if ($update_extensionmanager): ?>
					<?php echo JText::_('RLEM_UPDATE_OTHER_EXTENSIONS'); ?>
				<?php endif; ?>
			</div>
		</div>

		<div id="process-error" class="alert alert-error alert-noclose" style="display:none;">
			<h4 class="alert-heading"><?php echo JText::_('ERROR'); ?></h4>
			<div class="alert-message"></div>
		</div>
		<div id="process-warning" class="alert alert-warning alert-noclose" style="display:none;">
			<div class="alert-message"></div>
		</div>
		<div id="process-info" class="alert alert-info alert-noclose" style="display:none;">
			<div class="alert-message"></div>
		</div>
	</div>

	<table class="table processlist">
		<tbody>
			<?php foreach ($this->items as $item) : ?>
				<?php
				$disabled = $update_extensionmanager && $item->id != 'extensionmanager';
				?>
				<tr id="row_<?php echo $item->id; ?>" class="<?php echo $disabled ? 'ghosted' : ''; ?>">
					<td width="1%" nowrap="nowrap" class="ext_name">
						<span id="ext_name_<?php echo $item->id; ?>"><?php echo JText::_($item->name); ?></span>
					</td>
					<td class="statuses">
						<?php if ( ! $disabled): ?>
							<input type="hidden" id="url_<?php echo $item->id; ?>" value="<?php echo $item->url; ?>">

							<div class="queue_<?php echo $item->id; ?> status process queued">
								<span class="label"><?php echo JText::_('RLEM_QUEUED'); ?></span>
							</div>
							<div class="processing_<?php echo $item->id; ?> status processing hide">
								<div class="progress progress-striped active">
									<div class="bar" style="width: 100%;"></div>
								</div>
							</div>
							<div class="success_<?php echo $item->id; ?> status success process hide">
								<span class="label label-success"><?php echo JText::_(($task == 'uninstall') ? 'RLEM_UNINSTALLED' : 'RLEM_INSTALLED'); ?></span>
							</div>
							<div class="failed_<?php echo $item->id; ?> status failed process hide">
								<span class="label label-important"><?php echo JText::_('RLEM_INSTALLATION_FAILED'); ?></span>
							</div>
						<?php endif; ?>
					</td>
				</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
</div>
