<?php
/**
 * @package    Pwtsitemap
 *
 * @author     Perfect Web Team <extensions@perfectwebteam.com>
 * @copyright  Copyright (C) 2016 - 2019 Perfect Web Team. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link       https://extensions.perfectwebteam.com
 */

defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;

HTMLHelper::_('formbehavior.chosen');

/** @param   PwtSitemapViewDashboard  $this */

?>
<div id="j-sidebar-container" class="span2">
	<?php echo $this->sidebar; ?>
</div>

<?php // The following form is necessary to handle the [Add to robots.txt] button ?>
<form action="<?php echo Route::_('index.php?option=com_pwtsitemap'); ?>" method="post" name="adminForm" id="adminForm">
	<input type="hidden" name="task" value=""/>
	<input type="hidden" name="boxchecked" value="0"/>
	<?php echo HTMLHelper::_('form.token'); ?>
</form>

<div id="j-main-container" class="span10">
	<div class="row-fluid">
		<div class="span8">

			<!-- Standard Sitemaps -->
			<div class="well">
				<legend>
					<?php echo Text::_('COM_PWTSITEMAP_VIEW_SITEMAP_DEFAULT_HTML_TITLE'); ?>
				</legend>

				<form id="sitemap" name="sitemap" method="post" action="<?php echo Route::_('index.php?option=com_pwtsitemap&view=dashboard'); ?>"
				      class="sitemap-create">
					<?php echo HTMLHelper::_('select.genericlist', $this->menusList, 'menutype', 'class="menu advancedSelect"', 'value', 'text'); ?>
					<input class="btn btn-success" type="submit" value="<?php echo Text::_('COM_PWTSITEMAP_DASHBOARD_VIEW_CREATE_MENU_ITEM'); ?>">
					<input type="hidden" name="sitemap" value="Sitemap"/>
					<input type="hidden" name="alias" value="sitemap"/>
					<input type="hidden" name="type" value="sitemap"/>
					<input type="hidden" name="task" value="dashboard.saveSitemapMenuItem"/>
				</form>

				<?php if (!empty($this->menuItems['sitemap'])): ?>
					<table class="table table-striped table-vcenter" id="sitemap">
						<tbody>
						<?php foreach ($this->menuItems['sitemap'] as $menuItem):; ?>
							<tr>
								<td>
									<a href="<?php echo Uri::root() . $menuItem->path; ?>" target="_blank">
										<?php echo Uri::root() . $menuItem->path; ?>
									</a>

									<div class="btn-group btn-group-justified pull-right">
										<a class="btn btn-primary"
										   href="<?php echo Route::_('index.php?option=com_menus&view=item&layout=edit&id=' . $menuItem->id); ?>">
											<?php echo Text::_('COM_PWTSITEMAP_DASHBOARD_EDIT_MENU_ITEM'); ?>
										</a>
										<a class="btn btn-primary" href="<?php echo Route::_('index.php?option=com_pwtsitemap&view=items'); ?>">
											<?php echo Text::_('COM_PWTSITEMAP_DASHBOARD_VIEW_ITEMS'); ?>
										</a>
									</div>
								</td>
							</tr>
						<?php endforeach; ?>

						</tbody>
					</table>
				<?php endif; ?>
			</div>

			<!-- XML Sitemaps -->
			<div class="well">
				<legend>
					<?php echo Text::_('COM_PWTSITEMAP_VIEW_SITEMAP_DEFAULT_XML_TITLE'); ?>
				</legend>

				<form id="xmlitemap" name="xmlitemap" method="post" action="<?php echo Route::_('index.php?option=com_pwtsitemap&view=dashboard'); ?>"
				      class="sitemap-create">
					<?php echo HTMLHelper::_('select.genericlist', $this->menusList, 'menutype', 'class="menu advancedSelect"', 'value', 'text'); ?>
					<input class="btn btn-success" type="submit" value="<?php echo Text::_('COM_PWTSITEMAP_DASHBOARD_VIEW_CREATE_MENU_ITEM'); ?>">
					<input type="hidden" name="sitemap" value="XML Sitemap"/>
					<input type="hidden" name="alias" value="xml-sitemap"/>
					<input type="hidden" name="type" value="xmlitemap"/>
					<input type="hidden" name="task" value="dashboard.saveSitemapMenuItem"/>
				</form>

				<?php if (!empty($this->menuItems['xmlsitemap'])): ?>
					<table class="table table-striped" id="xmlsitemap">
						<tbody>
						<?php foreach ($this->menuItems['xmlsitemap'] as $menuItem):; ?>
							<tr>
								<td>
									<a href="<?php echo Uri::root() . $menuItem->path; ?>" target="_blank">
										<?php echo Uri::root() . $menuItem->path; ?>
									</a>

									<div class="btn-group btn-group-justified pull-right">
										<a class="btn btn-primary"
										   href="<?php echo Route::_('index.php?option=com_menus&view=item&layout=edit&id=' . $menuItem->id); ?>">
											<?php echo Text::_('COM_PWTSITEMAP_DASHBOARD_EDIT_MENU_ITEM'); ?>
										</a>
										<a class="btn btn-primary" href="<?php echo Route::_('index.php?option=com_pwtsitemap&view=items'); ?>">
											<?php echo Text::_('COM_PWTSITEMAP_DASHBOARD_VIEW_ITEMS'); ?>
										</a>
									</div>
								</td>
							</tr>
						<?php endforeach; ?>
						</tbody>
					</table>
				<?php endif; ?>
			</div>

			<!-- Multilingual Sitemaps -->
			<div class="well">
				<legend>
					<?php echo Text::_('COM_PWTSITEMAP_VIEW_MULTILANGUAGE_HTML_TITLE'); ?>
				</legend>

				<form id="multilingualsitemap" name="multilingualsitemap" method="post"
				      action="<?php echo Route::_('index.php?option=com_pwtsitemap&view=dashboard'); ?>" class="sitemap-create">
					<?php echo HTMLHelper::_('select.genericlist', $this->menusList, 'menutype', 'class="menu advancedSelect"', 'value', 'text'); ?>
					<input class="btn btn-success" type="submit" value="<?php echo Text::_('COM_PWTSITEMAP_DASHBOARD_VIEW_CREATE_MENU_ITEM'); ?>">
					<input type="hidden" name="sitemap" value="Multilingual sitemap"/>
					<input type="hidden" name="alias" value="mulitilingual-sitemap"/>
					<input type="hidden" name="type" value="multilingualsitemap"/>
					<input type="hidden" name="task" value="dashboard.saveSitemapMenuItem"/>
				</form>

				<?php if (!empty($this->menuItems['multilingualsitemap'])): ?>
					<table class="table table-striped" id="multilingualsitemap">
						<tbody>
						<?php foreach ($this->menuItems['multilingualsitemap'] as $menuItem):; ?>
							<tr>
								<td>
									<a href="<?php echo Uri::root() . $menuItem->path; ?>" target="_blank">
										<?php echo Uri::root() . $menuItem->path; ?>
									</a>

									<div class="btn-group btn-group-justified pull-right">
										<a class="btn btn-primary"
										   href="<?php echo Route::_('index.php?option=com_menus&view=item&layout=edit&id=' . $menuItem->id); ?>">
											<?php echo Text::_('COM_PWTSITEMAP_DASHBOARD_EDIT_MENU_ITEM'); ?>
										</a>
										<a class="btn btn-primary" href="<?php echo Route::_('index.php?option=com_pwtsitemap&view=items'); ?>">
											<?php echo Text::_('COM_PWTSITEMAP_DASHBOARD_VIEW_ITEMS'); ?>
										</a>
									</div>
								</td>
							</tr>
						<?php endforeach; ?>

						</tbody>
					</table>
				<?php endif; ?>
			</div>

			<!-- Image Sitemaps -->
			<div class="well">
				<legend>
					<?php echo Text::_('COM_PWTSITEMAP_VIEW_IMAGE_HTML_TITLE'); ?>
				</legend>

				<form id="imagesitemap" name="imagesitemap" method="post"
				      action="<?php echo Route::_('index.php?option=com_pwtsitemap&view=dashboard'); ?>" class="sitemap-create">
					<?php echo HTMLHelper::_('select.genericlist', $this->menusList, 'menutype', 'class="menu advancedSelect"', 'value', 'text'); ?>
					<input class="btn btn-success" type="submit" value="<?php echo Text::_('COM_PWTSITEMAP_DASHBOARD_VIEW_CREATE_MENU_ITEM'); ?>">
					<input type="hidden" name="sitemap" value="Image sitemap"/>
					<input type="hidden" name="alias" value="image-sitemap"/>
					<input type="hidden" name="type" value="imagesitemap"/>
					<input type="hidden" name="task" value="dashboard.saveSitemapMenuItem"/>
				</form>

				<?php if (!empty($this->menuItems['imagesitemap'])): ?>
					<table class="table table-striped" id="imagesitemap">
						<tbody>
						<?php foreach ($this->menuItems['imagesitemap'] as $menuItem):; ?>
							<tr>
								<td>
									<a href="<?php echo Uri::root() . $menuItem->path; ?>" target="_blank">
										<?php echo Uri::root() . $menuItem->path; ?>
									</a>

									<div class="btn-group btn-group-justified pull-right">
										<a class="btn btn-primary"
										   href="<?php echo Route::_('index.php?option=com_menus&view=item&layout=edit&id=' . $menuItem->id); ?>">
											<?php echo Text::_('COM_PWTSITEMAP_DASHBOARD_EDIT_MENU_ITEM'); ?>
										</a>
										<a class="btn btn-primary" href="<?php echo Route::_('index.php?option=com_pwtsitemap&view=items'); ?>">
											<?php echo Text::_('COM_PWTSITEMAP_DASHBOARD_VIEW_ITEMS'); ?>
										</a>
									</div>
								</td>
							</tr>
						<?php endforeach; ?>
						</tbody>
					</table>
				<?php endif; ?>
			</div>
		</div>

		<!-- Start Sidebar -->
		<div class="span4">
			<div class="well well-large pwt-extensions">

				<!-- PWT branding -->
				<div class="pwt-section">
					<?php echo HTMLHelper::_('image', 'com_pwtsitemap/pwt-sitemap.png', 'PWT Sitemap', ['class' => 'pwt-extension-logo'], true); ?>
					<p class="pwt-heading"><?php echo Text::_('COM_PWTSITEMAP_DASHBOARD_ABOUT_HEADER'); ?></p>
					<p><?php echo Text::_('COM_PWTSITEMAP_DASHBOARD_ABOUT_DESC'); ?></p>
					<p>
						<a href="https://extensions.perfectwebteam.com/pwt-sitemap">https://extensions.perfectwebteam.com/pwt-sitemap</a>
					</p>
					<p><?php echo Text::sprintf('COM_PWTSITEMAP_DASHBOARD_ABOUT_REVIEW',
							'https://extensions.joomla.org/extension/pwt-sitemap'); ?></p>
				</div>

				<div class="pwt-section">

					<div class="btn-group btn-group-justified">
						<a class="btn btn-large btn-primary"
						   href="https://extensions.perfectwebteam.com/pwt-sitemap/documentation"><?php echo Text::_('COM_PWTSITEMAP_DASHBOARD_ABOUT_DOCUMENTATION'); ?></a>
						<a class="btn btn-large btn-primary"
						   href="https://extensions.perfectwebteam.com/support"><?php echo Text::_('COM_PWTSITEMAP_DASHBOARD_ABOUT_SUPPORT'); ?></a>
					</div>

				</div>

				<div class="pwt-section pwt-section--border-top">
					<p>
						<strong><?php echo Text::sprintf('COM_PWTSITEMAP_DASHBOARD_ABOUT_VERSION', '</strong>1.4.1'); ?>
					</p>
				</div>
				<!-- End PWT branding -->

			</div>
		</div>
		<!-- End Sidebar -->
	</div>
</div>
