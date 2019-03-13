<?php
/**
 * @package    Pwtsitemap
 *
 * @author     Perfect Web Team <extensions@perfectwebteam.com>
 * @copyright  Copyright (C) 2016 - 2018 Perfect Web Team. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link       https://extensions.perfectwebteam.com
 */

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Router\Route;
use Joomla\Uri\Uri;

defined('_JEXEC') or die;

HTMLHelper::_('bootstrap.tooltip');
HTMLHelper::_('behavior.multiselect');
HTMLHelper::_('formbehavior.chosen', 'select');

// List order and direction
$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn  = $this->escape($this->state->get('list.direction'));
$ordering  = ($listOrder == 'a.lft');
$saveOrder = ($listOrder == 'a.lft' && strtolower($listDirn) == 'asc');
$user      = Factory::getUser();
$menuType  = (string) isset($this->activeFilters['menutype']) ? $this->activeFilters['menutype'] : '';

if ($saveOrder && $menuType)
{
	$saveOrderingUrl = 'index.php?option=com_menus&task=items.saveOrderAjax&tmpl=component';
	HTMLHelper::_('sortablelist.sortable', 'itemList', 'adminForm', strtolower($listDirn), $saveOrderingUrl, false, true);
}

$colSpan = 9;

if ($menuType === '')
{
	$colSpan--;
}
?>

<form action="<?php echo Route::_('index.php?option=com_pwtsitemap&view=items'); ?>" method="post" name="adminForm" id="adminForm">
	<?php if (!empty($this->sidebar)) : ?>
	<div id="j-sidebar-container" class="span2">
		<?php echo $this->sidebar; ?>
	</div>
	<div id="j-main-container" class="span10">
		<?php else : ?>
		<div id="j-main-container">
			<?php endif; ?>
			<?php
			// Search tools bar
			echo LayoutHelper::render('joomla.searchtools.default', array('view' => $this));
			?>
			<?php if (empty($this->items)) : ?>
				<div class="alert alert-no-items">
					<?php echo Text::_('JGLOBAL_NO_MATCHING_RESULTS'); ?>
				</div>
			<?php else : ?>
				<table class="table table-striped" id="itemList">
					<thead>
					<tr>
						<?php if ($menuType) : ?>
							<th width="1%" class="nowrap center hidden-phone">
								<?php echo HTMLHelper::_(
									'searchtools.sort', '', 'a.lft', $listDirn, $listOrder, null, 'asc', 'JGRID_HEADING_ORDERING', 'icon-menu-2'
								); ?>
							</th>
						<?php endif; ?>
						<th width="1%" class="center">
							<?php echo HTMLHelper::_('grid.checkall'); ?>
						</th>
						<th>
							<?php echo HTMLHelper::_('searchtools.sort', 'JGLOBAL_TITLE', 'a.title', $listDirn, $listOrder); ?>
						</th>
						<th width="10%">
							<?php echo HTMLHelper::_('searchtools.sort', 'COM_PWTSITEMAP_MENUTYPE', 'menutype_title', $listDirn, $listOrder); ?>
						</th>
						<th width="10%">
							<?php echo Text::_("COM_PWTSITEMAP_FIELD_SHOW_IN_HTML"); ?>
						</th>
						<th width="10%">
							<?php echo Text::_("COM_PWTSITEMAP_FIELD_SHOW_IN_XML"); ?>
						</th>
                        <th width="10%">
							<?php echo Text::_("COM_PWTSITEMAP_ADD_ARTICLES_IN_HTML"); ?>
                        </th>
                        <th width="10%">
							<?php echo Text::_("COM_PWTSITEMAP_ADD_ARTICLES_IN_XML"); ?>
                        </th>
						<th width="15%">
							<?php echo Text::_("COM_PWTSITEMAP_FIELD_ROBOTSTXT"); ?>
						</th>
					</tr>
					</thead>
                    <tfoot>
                    <tr>
                        <td colspan="<?php echo $colSpan; ?>">
							<?php echo $this->pagination->getListFooter(); ?>
                        </td>
                    </tr>
                    </tfoot>
					<tbody>
					<?php foreach ($this->items as $i => $item):
						$orderkey = array_search($item->id, $this->ordering[$item->parent_id]);
						$canCreate = $user->authorise('core.create', 'com_menus.menu.' . $item->menutype_id);
						$canEdit = $user->authorise('core.edit', 'com_menus.menu.' . $item->menutype_id);
						$canCheckin = $user->authorise('core.manage', 'com_checkin') || $item->checked_out == $user->get('id') || $item->checked_out == 0;
						$canChange = $user->authorise('core.edit.state', 'com_menus.menu.' . $item->menutype_id) && $canCheckin;
						$uri = new Uri($item->link);

						// Get the parents of item for sorting
						if ($item->level > 1)
						{
							$parentsStr       = '';
							$_currentParentId = $item->parent_id;
							$parentsStr       = ' ' . $_currentParentId;

							for ($j = 0; $j < $item->level; $j++)
							{
								foreach ($this->ordering as $k => $v)
								{
									$v = implode('-', $v);
									$v = '-' . $v . '-';

									if (strpos($v, '-' . $_currentParentId . '-') !== false)
									{
										$parentsStr       .= ' ' . $k;
										$_currentParentId = $k;
										break;
									}
								}
							}
						}
						else
						{
							$parentsStr = '';
						}
						?>
						<tr sortable-group-id="<?php echo $item->parent_id; ?>" item-id="<?php echo $item->id; ?>"
							parents="<?php echo $parentsStr; ?>" level="<?php echo $item->level; ?>">
							<?php if ($menuType) : ?>
								<td class="order nowrap center hidden-phone">
									<?php
									$iconClass = '';

									if (!$canChange)
									{
										$iconClass = ' inactive';
									}
									elseif (!$saveOrder)
									{
										$iconClass = ' inactive tip-top hasTooltip" title="' . HTMLHelper::_('tooltipText', 'JORDERINGDISABLED');
									}
									?>
									<span class="sortable-handler<?php echo $iconClass ?>">
										<span class="icon-menu"></span>
									</span>
									<?php if ($canChange && $saveOrder) : ?>
										<input type="text" style="display:none" name="order[]" size="5" value="<?php echo $orderkey + 1; ?>"/>
									<?php endif; ?>
								</td>
							<?php endif; ?>
							<td class="center">
								<?php echo HTMLHelper::_('grid.id', $i, $item->id); ?>
							</td>
							<td>
								<?php $prefix = LayoutHelper::render('joomla.html.treeprefix', array('level' => $item->level)); ?>
								<?php echo $prefix; ?>

								<?php if ($user->authorise('core.edit', 'com_menu')) : ?>
									<a href="index.php?option=com_menus&view=item&layout=edit&id=<?php echo $item->id; ?>">
										<?php echo $item->title; ?>
									</a>
								<?php else : ?>
									<?php echo $item->title; ?>
								<?php endif; ?>
								<span class="small break-word">
									<?php echo Text::sprintf('JGLOBAL_LIST_ALIAS', $this->escape($item->alias)); ?>
								</span>
								<div class="small">
									<?php echo Text::sprintf('COM_PWTSITEMAP_LANGUAGE', $this->escape($item->language)) ?>

									<?php echo PwtHtmlPwtSitemap::languageFlag($item->language); ?>
								</div>
							</td>
							<td>
								<?php echo $item->menutype_title; ?>
							</td>
							<td>
								<?php if ($item->params->get('addtohtmlsitemap', 0) !== 'disabled') : ?>
									<?php echo PwtHtmlPwtSitemap::radio(
										'addtohtmlsitemap', $item->id, 'pwtsitemapradio', $item->params->get('addtohtmlsitemap', 1),
										Text::_('COM_PWTSITEMAP_FIELD_SHOW_IN_HTML')
									); ?>
								<?php endif; ?>
							</td>
							<td>
								<?php if ($item->params->get('addtoxmlsitemap', 0) !== 'disabled') : ?>
									<?php echo PwtHtmlPwtSitemap::radio(
										'addtoxmlsitemap', $item->id, 'pwtsitemapradio', $item->params->get('addtoxmlsitemap', 1),
										Text::_('COM_PWTSITEMAP_FIELD_SHOW_IN_XML')
									); ?>
								<?php endif; ?>
							</td>
                            <td>
                                <?php if ($item->componentname === 'com_content' && $uri->getVar('view') === 'category') : ?>
                                    <?php if ($item->params->get('addcontenttohtmlsitemap', 0) !== 'disabled') : ?>
                                        <?php echo PwtHtmlPwtSitemap::radio(
                                            'addcontenttohtmlsitemap', $item->id, 'pwtsitemapradio', $item->params->get('addcontenttohtmlsitemap', 1),
                                            Text::_('COM_PWTSITEMAP_ADD_ARTICLES_IN_HTML')
                                        ); ?>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </td>
                            <td>
	                            <?php if ($item->componentname === 'com_content' && $uri->getVar('view') === 'category') : ?>
                                    <?php if ($item->params->get('addcontenttoxmlsitemap', 0) !== 'disabled') : ?>
                                        <?php echo PwtHtmlPwtSitemap::radio(
                                            'addcontenttoxmlsitemap', $item->id, 'pwtsitemapradio', $item->params->get('addcontenttoxmlsitemap', 1),
                                            Text::_('COM_PWTSITEMAP_ADD_ARTICLES_IN_XML')
                                        ); ?>
                                    <?php endif; ?>
	                            <?php endif; ?>
                            </td>
							<td>
								<?php $robots = '';

								if ($item->params['robots'])
								{
									$robots = $item->params['robots'];
								}
								?>

								<?php $elements = array(); ?>
								<?php $elements[] = HTMLHelper::_(
									'select.option', '',
									Text::sprintf('JGLOBAL_USE_GLOBAL_VALUE', Factory::getConfig()->get('robots', 'index, follow'))
								); ?>
								<?php $elements[] = HTMLHelper::_('select.option', 'index, follow', Text::_('JGLOBAL_INDEX_FOLLOW')); ?>
								<?php $elements[] = HTMLHelper::_('select.option', 'noindex, follow', Text::_('JGLOBAL_NOINDEX_FOLLOW')); ?>
								<?php $elements[] = HTMLHelper::_('select.option', 'index, nofollow', Text::_('JGLOBAL_INDEX_NOFOLLOW')); ?>
								<?php $elements[] = HTMLHelper::_('select.option', 'noindex, nofollow', Text::_('JGLOBAL_NOINDEX_NOFOLLOW')); ?>
								<?php echo HTMLHelper::_('select.genericlist', $elements, 'robots_' . $item->id, 'class="pwtrobots"', 'value', 'text', $robots); ?>
                                <span class="save-indication"></span>
							</td>
						</tr>
					<?php endforeach; ?>
					</tbody>
					<?php echo HTMLHelper::_(
						'bootstrap.renderModal',
						'collapseModal',
						array(
							'title'  => Text::_('COM_PWTSITEMAP_BATCH_OPTIONS'),
							'footer' => $this->loadTemplate('batch_footer')
						),
						$this->loadTemplate('batch_body')
					); ?>
				</table>
			<?php endif; ?>

			<input type="hidden" name="task" value=""/>
			<input type="hidden" name="boxchecked" value="0"/>
			<?php echo HTMLHelper::_('form.token'); ?>
		</div>
</form>

<script type="text/javascript">
	(function ($) {
		jQuery('.pwtsitemapradio, .pwtrobots').on('change', function () {
			var parameter = $(this).attr('name').split('_')[0];
			var itemId = $(this).attr('name').split('_')[1];
			var value = $(this).val();
			var saveIndicator = $(this).closest('td').find('.save-indication').addClass('icon-ok');

			var request = {
				'option': 'com_ajax',
				'plugin': 'pwtsitemap',
				'group': 'system',
				'itemId': itemId,
				'parameter': parameter,
				'value': value,
				'format': 'json'
			};

			$(saveIndicator).removeClass('icon-ok').css({
				'background': 'url(../media/system/images/modal/spinner.gif)',
				'display': 'inline-block',
				'width': '16px',
				'height': '16px'
			});

			$.ajax({
				type: 'POST',
				data: request,
				dataType: 'json',
				success: function (response) {
					$(saveIndicator).removeAttr('style').addClass('icon-ok');
				},
				error: function (xhr, status, err) {
					console.log(err)
				}
			});
		});
	}(jQuery));
</script>

