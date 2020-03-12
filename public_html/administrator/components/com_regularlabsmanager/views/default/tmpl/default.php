<?php
/**
 * @package         Regular Labs Extension Manager
 * @version         7.4.4
 * 
 * @author          Peter van Westen <info@regularlabs.com>
 * @link            http://www.regularlabs.com
 * @copyright       Copyright © 2020 Regular Labs All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper as JHtml;
use Joomla\CMS\Language\Text as JText;
use Joomla\CMS\Router\Route as JRoute;
use RegularLabs\Library\Document as RL_Document;
use RegularLabs\Library\Parameters as RL_Parameters;
use RegularLabs\Library\StringHelper as RL_String;
use RegularLabs\Library\Version as RL_Version;

JHtml::_('jquery.framework');
JHtml::_('bootstrap.framework');
JHtml::_('behavior.modal');
JHtml::_('behavior.tooltip');
JHtml::_('bootstrap.popover');

$ids = [];
foreach ($this->items as $item)
{
	$ids[] = $item->id;
}

$config            = RL_Parameters::getInstance()->getComponentParams('regularlabsmanager');
$check_data        = $config->check_data;
$hide_notinstalled = $config->hide_notinstalled;

RL_Document::loadFormDependencies();

$key    = trim($config->key);
$js_key = $key ? strtolower(substr($key, 0, 8) . md5(substr($key, 8))) : '';

$options = [
	'ids'          => $ids,
	'timeout'      => (int) $config->timeout,
	'key'          => $js_key,
	'updatesource' => $config->updatesource,
];
RL_Document::scriptOptions($options, 'Extension Manager');
JText::script('RLEM_ALERT_NO_ITEMS_TO_UPDATE');
JText::script('RLEM_ALERT_NO_ITEMS_SELECTED');
JText::script('RLEM_ALERT_FAIL');
JText::script('RLEM_ALERT_NO_DATA');

RL_Document::script('regularlabsmanager/script.min.js', '7.4.4');
RL_Document::style('regularlabsmanager/style.min.css', '7.4.4');

$script = "
	jQuery(document).ready(function() {
		RegularLabsManager.refreshData(" . ($check_data ? 1 : 0) . ");
	});
";
RL_Document::scriptDeclaration($script);

$loading = '<div class="progress progress-striped active" style="min-width: 60px;"><div class="bar" style="width: 100%;"></div></div>';
?>
	<div id="rl_key" <?php echo ($key || ! $config->show_key_field) ? ' style="display:none;"' : '' ?>>
		<form action="<?php echo JRoute::_('index.php?option=com_regularlabsmanager'); ?>" method="post" class="form-horizontal">
			<div class="well">
				<h4><?php echo JText::_('RLEM_DOWNLOAD_KEY'); ?></h4>

				<p id="rl_key_text_empty"><?php echo RL_String::html_entity_decoder(JText::sprintf('RLEM_DOWNLOAD_KEY_DESC', '<a href="https://www.regularlabs.com/purchase" target="_blank">', '</a>', '<a href="https://www.regularlabs.com/downloads" target="_blank">', '</a>')); ?></p>

				<p id="rl_key_text_invalid"
				   style="display:none;"><?php echo RL_String::html_entity_decoder(JText::sprintf('RLEM_DOWNLOAD_KEY_INVALID', '<a href="https://www.regularlabs.com/downloads" target="_blank">', '</a>')); ?></p>

				<div>
					<?php
					require_once JPATH_SITE . '/libraries/regularlabs/fields/key.php';
					$field   = new JFormFieldRL_Key;
					$element = new SimpleXMLElement('<field name="key" type="rl_key" filter="raw" action="Joomla.submitbutton(\'storekey\')" />');
					$field->setup($element, $key);
					echo $field->__get('input');
					?>
				</div>
				<input type="hidden" name="task" value="storekey">
				<?php echo JHtml::_('form.token'); ?>
			</div>
		</form>
	</div>

	<a id="rlem_modal" href=""></a>
	<div id="rlem">
		<form action="" name="adminForm" id="adminForm">
			<table class="table table-striped<?php echo $hide_notinstalled ? ' hide_not_installed' : ''; ?>">
				<thead class="hidden-phone">
					<tr>
						<th width="1%">
							<input type="checkbox" name="checkall-toggle" value="" onclick="Joomla.checkAll(this)">
						</th>
						<th width="200" class="left nowrap"><?php echo JText::_('RLEM_EXTENSION'); ?></th>
						<th width="16" class="hidden-tablet"><!-- website --></th>
						<th width="48" class="hidden-tablet"><?php echo JText::_('RLEM_TYPE'); ?></th>

						<th width="5%"><!-- spacer --></th>

						<th width="60" class="left"><?php echo JText::_('RLEM_INSTALLED'); ?></th>

						<th width="5%"><!-- spacer --></th>

						<th width="1%" class="left"><span class="loaded hide"><?php echo JText::_('RLEM_NEW'); ?></span></th>
						<th width="60"><!-- new version --></th>

						<th width="5%"><!-- spacer --></th>

						<th><!-- pro --></th>

						<th width="5%"><!-- spacer --></th>

						<th><!-- reinstall --></th>

						<th width="5%"><!-- spacer --></th>

						<th width="20"><!-- uninstall --></th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ($this->items as $i => $item) : ?>
						<?php
						$row_class = 'not_installed';
						if ($item->installed)
						{
							$row_class = 'installed'
								. ($item->pro ? ' pro_installed' : ' free_installed')
								. (empty($item->missing) ? '' : ' has_missing');
						}
						?>
						<tr id="row_<?php echo $item->id; ?>" class="<?php echo $row_class; ?>">
							<td class="center hidden-phone ext_checkbox">
								<span class="select hide">
									<?php echo JHtml::_('grid.id', $i, $item->id); ?>
								</span>
							</td>
							<td class="nowrap ext_name">
								<input type="hidden" id="url_<?php echo $item->id; ?>" value="">
								<span class="hasPopover" data-trigger="hover"
								      title="<?php echo JText::_($item->name); ?>" data-content="<?php echo JText::_($item->name . '_DESC'); ?>">
									<span class="icon-reglab icon-<?php echo $item->alias; ?> hidden-phone"></span>
									<?php echo JText::_($item->name); ?>
								</span>
							</td>
							<td class="center hidden-phone hidden-tablet ext_website">
								<a href="https://www.regularlabs.com/<?php echo $item->id; ?>" target="_blank">
									<span class="icon-out-2"></span>
								</a>
							</td>
							<td class="nowrap hidden-phone hidden-tablet ext_types">
								<?php echo $loading; ?>
								<div class="loaded">
									<?php foreach ($item->types as $type) : ?>
										<?php
										switch ($type->type)
										{
											case 'com':
												$icon = '<span class="label label-success">C</span>';
												break;
											case 'mod':
												$icon = '<span class="label label-important">M</span>';
												break;
											case 'plg_fields':
												$icon = '<span class="label label-info">P<small>F</small></span>';
												break;
											case 'plg_system':
												$icon = '<span class="label label-info">P<small>S</small></span>';
												break;
											case 'plg_editors-xtd':
												$icon = '<span class="label label-info">P<small>B</small></span>';
												break;
											case 'plg_actionlog':
												$icon = '<span class="label label-info">P<small>A</small></span>';
												break;
											default:
												$icon = '<span class="label label-success">E</span>';
												break;
										}
										?>
										<span class="not_installed data hide disabled" rel="tooltip"
										      title="<?php echo JText::_('RL_' . strtoupper($type->type)); ?>">
											<?php echo $icon; ?>
										</span>
										<span class="installed data hide" rel="tooltip" title="<?php echo JText::_('RL_' . strtoupper($type->type)); ?>">
											<a href="index.php?<?php echo $type->link; ?>" target="_blank"><?php echo $icon; ?></a>
										</span>
									<?php endforeach; ?>
								</div>
							</td>

							<td class="nowrap hidden-phone"><!-- spacer --></td>

							<td class="nowrap ext_installed">
								<?php echo $loading; ?>
								<div class="loaded hide">
									<span class="installed nowrap data hide">
										<span class="uptodate data hide">
											<span class="current_version badge badge-success" rel="tooltip"
											      title="<?php echo makeSafe(JText::_('RLEM_COMMENT_UPTODATE')); ?>">
												<?php echo $item->version; ?>
											</span>
										</span>
										<span class="downgrade data hide">
											<span class="current_version badge badge-success" rel="tooltip"
											      title="<?php echo makeSafe(JText::_('RLEM_COMMENT_DOWNGRADE')); ?>">
												<?php echo $item->version; ?>
											</span>
										</span>
										<span class="update data hide">
											<span class="current_version badge badge-warning" rel="tooltip"
											      title="<?php echo makeSafe(JText::_('RLEM_COMMENT_UPDATE')); ?>">
												<?php echo $item->version; ?>
											</span>
										</span>
										<span class="pro_installed data hide">
											<span class="pro_no_access data hide">
												<span class="current_version badge badge-important hasPopover" data-trigger="hover" data-placement="right"
												      title="<?php echo makeSafe('<span class="icon-warning"></span> ' . JText::_('RLEM_COMMENT')); ?>"
												      data-content="<?php echo makeSafe(JText::_('RLEM_COMMENT_NO_PRO')); ?>">
													<?php echo $item->version; ?>
												</span>
											</span>
										</span>
										<?php
										$missing = '';
										if ($item->installed && ! empty($item->missing))
										{
											$missing = [];
											foreach ($item->missing as $m)
											{
												$missing[] = JText::_('RL_' . strtoupper($m));
											}
											$missing = JText::sprintf('RLEM_MISSING_EXTENSIONS', implode(',', $missing));
										}
										?>
										<span class="missing data <?php echo $missing ? '' : 'hide'; ?>">
											<span class="current_version badge badge-important hasPopover" data-trigger="hover" data-placement="right"
											      title="<?php echo makeSafe('<span class="icon-warning"></span> ' . JText::_('RLEM_COMMENT')); ?>"
											      data-content="<?php echo makeSafe($missing); ?>">
												<?php echo $item->version; ?>
											</span>
										</span>
										<span class="hidden-tablet hidden-desktop">
											<span class="pro_installed label label-info data hide">P</span>
										</span>
										<span class="hidden-phone">
											<span class="pro_installed label label-info data hide">PRO</span>
										</span>
									</span>
								</div>
							</td>

							<td class="nowrap hidden-phone"><!-- spacer --></td>

							<td class="center nowrap ext_install">
								<span class="install btn btn-small btn-success data hide" onclick="rlem_function('install', '<?php echo $item->id; ?>');">
									<span class="icon-box-add"></span> <?php echo JText::_('RLEM_TITLE_INSTALL'); ?>
								</span>
								<span class="update btn btn-small btn-warning data hide" onclick="rlem_function('update', '<?php echo $item->id; ?>');">
									<span class="icon-upload"></span> <?php echo JText::_('RLEM_TITLE_UPDATE'); ?>
								</span>
								<span class="reinstall label label-success disabled data hide">
									<?php echo JText::_('RLEM_TITLE_UPTODATE'); ?>
								</span>

								<div class="downgrade data hide">
									<span class="pro_access data">
										<span class="btn btn-small" onclick="rlem_function('downgrade', '<?php echo $item->id; ?>');"
										      rel="tooltip" title="<?php echo makeSafe(JText::_('RLEM_COMMENT_DOWNGRADE')); ?>">
											<?php echo JText::_('RLEM_TITLE_DOWNGRADE'); ?>
										</span>
									</span>
									<span class="pro_installed data hide">
										<span class="pro_no_access data hide">
											<span class="btn btn-small btn-danger disabled hasPopover" data-trigger="hover" data-placement="right"
											      title="<?php echo makeSafe('<span class="icon-warning"></span> ' . JText::_('RLEM_COMMENT')); ?>"
											      data-content="<?php echo makeSafe(JText::_('RLEM_COMMENT_NO_PRO')); ?>">
												<?php echo JText::_('RLEM_TITLE_DOWNGRADE'); ?>
											</span>
										</span>
									</span>
								</div>

								<div class="pro_installed data hide">
									<span class="pro_no_access data hide">
										<span class="btn btn-small btn-danger disabled hasPopover" data-trigger="hover" data-placement="right"
										      title="<?php echo makeSafe('<span class="icon-warning"></span> ' . JText::_('RLEM_COMMENT')); ?>"
										      data-content="<?php echo makeSafe(JText::_('RLEM_COMMENT_NO_PRO')); ?>">
											<span class="icon-upload"></span> <?php echo JText::_('RLEM_TITLE_UPDATE'); ?>
										</span>
									</span>
								</div>
								<span class="hidden-tablet hidden-desktop nowrap">
									<div class="clearfix"></div>
									<span class="changelog data hide">
										<a href="https://www.regularlabs.com/<?php echo $item->id; ?>/changelog" target="_blank">
											<span class="new_version badge"></span></a>
										<span class="pro_access label label-info data hide">P</span>
									</span>
								</span>
							</td>
							<td class="hidden-phone nowrap ext_new">
								<span class="refresh no_external btn btn-small btn-primary data hide" onclick="rlem_function('refresh');">
									<span class="icon-refresh"></span> <?php echo JText::_('RLEM_CHECK_DATA'); ?>
								</span>
								<span class="changelog data hide">
									<span class="hidden-tablet">
										<a href="https://www.regularlabs.com/<?php echo $item->id; ?>/changelog" target="_blank"
										   class="hasPopover" data-trigger="hover"
										   title="<?php echo JText::_('RL_CHANGELOG'); ?>" data-content="">
											<span class="new_version badge"></span></a>
									</span>
									<span class="hidden-desktop">
										<a href="https://www.regularlabs.com/<?php echo $item->id; ?>/changelog" target="_blank"
										   class="changelog data hide">
											<span class="new_version badge"></span></a>
									</span>
									<span class="pro_access label label-info data hide">PRO</span>
								</span>
							</td>

							<td class="nowrap hidden-phone"><!-- spacer --></td>

							<td class="center nowrap hidden-phone">
								<span class="pro_not_installed data hide">
									<span class="pro_available data hide">
										<span class="pro_no_access data hide">
											<a style="margin-bottom:4px;" class="btn btn-small btn-info hidden-tablet"
											   href="https://www.regularlabs.com/purchase?ext=<?php echo $item->id; ?>" target="_blank">
												<span class="icon-basket"></span> <?php echo JText::_('RLEM_BUY_PRO_VERSION'); ?>
											</a>
											<a style="margin-bottom:4px;" class="btn btn-small btn-info hidden-desktop"
											   rel="tooltip" title="<?php echo JText::_('RLEM_BUY_PRO_VERSION'); ?>"
											   href="https://www.regularlabs.com/purchase?ext=<?php echo $item->id; ?>" target="_blank">
												<span class="icon-basket"></span>
											</a>
										</span>
									</span>
								</span>
								<span class="pro_installed data hide">
									<span class="pro_key_invalid data hide">
										<a style="margin-bottom:4px;" class="btn btn-small btn-warning hidden-tablet"
										   href="https://www.regularlabs.com/purchase?ext=<?php echo $item->id; ?>" target="_blank">
											<span class="icon-basket"></span> <?php echo JText::_('RLEM_RENEW_SUBSCRIPTION'); ?>
										</a>
										<a style="margin-bottom:4px;" class="btn btn-small btn-warning hidden-desktop"
										   rel="tooltip" title="<?php echo JText::_('RLEM_RENEW_SUBSCRIPTION'); ?>"
										   href="https://www.regularlabs.com/purchase?ext=<?php echo $item->id; ?>" target="_blank">
											<span class="icon-basket"></span>
										</a>
									</span>
								</span>
							</td>

							<td class="nowrap hidden-phone"><!-- spacer --></td>

							<td class="center nowrap hidden-phone">
								<span class="reinstall btn btn-small btn-default data hide" onclick="rlem_function('reinstall', '<?php echo $item->id; ?>');">
									<?php echo JText::_('RLEM_TITLE_REINSTALL'); ?>
								</span>
								<span class="install_stable btn btn-small btn-default data hide" onclick="rlem_function('install_stable', '<?php echo $item->id; ?>');">
									<?php echo JText::_('RLEM_TITLE_INSTALL_STABLE'); ?>
								</span>
							</td>

							<td class="nowrap hidden-phone"><!-- spacer --></td>

							<td class="center nowrap hidden-phone ext_uninstall">
								<?php if ($item->id != 'extensionmanager') : ?>
									<span class="installed btn btn-micro btn-danger data hide"
									      rel="tooltip" data-placement="left" title="<?php echo JText::_('RLEM_TITLE_UNINSTALL'); ?>"
									      onclick="rlem_function('uninstall', '<?php echo $item->id; ?>');">
										<span class="icon-cancel-2"></span>
									</span>
								<?php endif; ?>
							</td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
			<input type="hidden" name="task" id="task" value="browse" />
			<input type="hidden" name="boxchecked" id="boxchecked" value="0" />
		</form>
	</div>
<?php
// Copyright

echo RL_Version::getFooter('REGULAR_LABS_EXTENSION_MANAGER', $config->show_copyright);

function makeSafe($str)
{
	return str_replace(['"', '<', '>'], ['&quot;', '&lt;', '&gt;'], $str);
}
