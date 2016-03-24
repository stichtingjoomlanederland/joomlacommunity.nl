<?php
/*
 * @package     perfecttemplate
 * @copyright   Copyright (c) Perfect Web Team / perfectwebteam.nl
 * @license     GNU General Public License version 3 or later
 */

// No direct access.
defined('_JEXEC') or die;

// Load Perfect Template Helper
include_once JPATH_THEMES . '/' . $this->template . '/templateDetails.php';

// Determ col-size
if ($this->countModules('rechts'))
{
	$maincols = 8;
}
else
{
	$maincols = 12;
}

// Remove after removing dumy text
$app      = JFactory::getApplication();
$itemid   = $app->input->getCmd('Itemid', '');
$sitename = $app->getCfg('sitename');

// Get title of active menu
$menu_active = $app->getMenu()->getActive();
$menu_route  = $menu_active->route;
$menu_title  = $menu_active->title;
$route_parts = explode('/', $menu_route);
$subsite     = $route_parts[0];
?>
<!DOCTYPE html>
<html class="html no-js" lang="<?php echo $this->language; ?>" dir="<?php echo $this->direction; ?>">
<head>
	<jdoc:include type="head"/>
</head>

<body class="base <?php echo($subsite); ?> <?php echo $helper->getBodySuffix(); ?>">
<?php
if (!empty($analyticsData) && $analyticsData['position'] == 'after_body_start')
{
	echo $analyticsData['script'];
}
?>

<div class="header <?php if ($itemid == 248): ?>homepage<?php endif; ?>">
	<nav class="navbar navbar-default navbar-main" role="navigation">
		<div class="container">

            <?php //Brand and toggle get grouped for better mobile display ?>
			<div class="navbar-header navbar-logo">
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#jc-navbar-collapse-1" aria-expanded="false">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
				<a class="logo" href=".">
					<span class="navbar-logo-name"><?php echo $sitename ?>
						<span class="navbar-logo-subline"><?php echo JText::_('TPL_JC_SUBLINE') ?></span>
					</span>
				</a>
			</div>

			<div class="navbar-main-menu navbar-left">
				<jdoc:include type="modules" name="mainmenu"/>
			</div>

            <?php // Collect the nav links, forms, and other content for toggling ?>
            <div class="collapse navbar-collapse" id="jc-navbar-collapse-1">
                <div class="navbar-mobile">
                    <jdoc:include type="modules" name="mainmenu-mobile"/>
                </div>
                <jdoc:include type="modules" name="usermenu"/>
				<jdoc:include type="modules" name="search"/>
            </div>

		</div>
	</nav>

	<div class="pagetitle">
		<?php if ($this->countModules('slider')) : ?>
			<jdoc:include type="modules" name="slider" />
		<?php else: ?>
			<div class="container">
				<div class="paginatitel pull-left">
					<h3><?php echo($menu_title); ?></h3>
				</div>
				<div class="banner pull-right">
					<img src="images/banner.png"/>
				</div>
			</div>
		<?php endif; ?>
	</div>
	<div class="subnav" data-spy="affix" data-offset-top="<?php if ($itemid == 248): ?>290<?php else: ?>90<?php endif; ?>">
		<div class="container">
			<nav class="navbar navbar-sub" role="navigation">
				<jdoc:include type="modules" name="submenu"/>
			</nav>
		</div>
	</div>
</div>

<!-- Body -->
<div class="content">
	<div class="container">
		<jdoc:include type="modules" name="test" style="xhtml"/>

		<?php if ($itemid == 122): ?>
			<?php include 'dummy/nieuws_alt.php'; ?>
		<?php elseif ($itemid == 124): ?>
			<?php include 'dummy/nieuws_artikel.php'; ?>
		<?php elseif ($itemid == 98): ?>
			<?php include 'dummy/nieuws.php'; ?>
		<?php elseif ($itemid == 248): ?>
			<?php include 'dummy/home.php'; ?>
		<?php elseif ($itemid == 242): ?>
			<?php include 'dummy/documentatie.php'; ?>
		<?php elseif ($itemid == 259): ?>
			<?php include 'dummy/documentatie_artikel.php'; ?>
		<?php elseif ($itemid == 260): ?>
			<?php include 'dummy/documentatie_artikel_2.php'; ?>
		<?php elseif ($itemid == 492): ?>
			<?php include 'dummy/styleguide.php'; ?>
		<?php endif; ?>

		<div class="row">
			<jdoc:include type="modules" name="top-a" style="panel"/>
		</div>
		<div class="row">
			<div class="content-<?php echo($maincols); ?>">
				<jdoc:include type="message"/>
				<jdoc:include type="component"/>
			</div>
			<?php if ($this->countModules('rechts')) : ?>
				<div class="content-4">
					<jdoc:include type="modules" name="rechts" style="panel"/>
				</div>
			<?php endif; ?>
		</div>
	</div>
</div>
<!-- Footer -->
<div class="footer">
	<div class="footer-navs">
		<div class="container">
			<div class="row">
				<div class="col-4">
					<jdoc:include type="modules" name="footer-1" style="xhtml" />
				</div>
				<div class="col-2">
					<jdoc:include type="modules" name="footer-2" style="xhtml" />
				</div>
				<div class="col-2">
					<jdoc:include type="modules" name="footer-3" style="xhtml" />
				</div>
				<div class="col-2">
					<jdoc:include type="modules" name="footer-4" style="xhtml" />
				</div>
				<div class="col-2">
					<jdoc:include type="modules" name="footer-5" style="xhtml" />
				</div>
			</div>
		</div>
	</div>
	<div class="footer-copyright">
		<div class="container">
			<div class="row">
				<div class="col-12">
					<p class="">Copyright © 2008-<?php echo date('Y'); ?> Joomla!Community - Alle rechten voorbehouden</p>
					<jdoc:include type="modules" name="copyright" />
				</div>
			</div>
		</div>
	</div>
</div>
<jdoc:include type="modules" name="debug"/>
</body>
</html>
