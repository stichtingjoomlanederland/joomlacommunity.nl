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

// Needs cleanup
$app         = JFactory::getApplication();
$doc         = JFactory::getDocument();
$kolomrechts = $this->countModules('rechts');
$kolomlinks  = $this->countModules('links');
if ($kolomrechts || $kolomlinks)
{
	$maincols = 8;
}
else
{
	$maincols = 12;
}

// Detecting Active Variables
$option   = $app->input->getCmd('option', '');
$view     = $app->input->getCmd('view', '');
$layout   = $app->input->getCmd('layout', '');
$task     = $app->input->getCmd('task', '');
$itemid   = $app->input->getCmd('Itemid', '');
$sitename = $app->getCfg('sitename');

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
	<nav class="navbar navbar-default navbar-fixed-top navbar-main" role="navigation">
		<div class="container">
			<div class="navbar-logo">
				<a class="logo" href=".">
					<span class="name">JoomlaCommunity.nl
						<span class="subline">het Nederlandstalige Joomla!-portal</span>
					</span>
				</a>
			</div>
			<div class="navbar-main-menu navbar-left">
				<jdoc:include type="modules" name="mainmenu"/>
			</div>
			<jdoc:include type="modules" name="usermenu"/>
			<form class="navbar-form navbar-right" role="search">
				<div class="form-group ">
					<input type="text" class="form-control input-sm" placeholder="Zoeken">
				</div>
			</form>
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
	<div data-spy="affix" data-offset-top="<?php if ($itemid == 248): ?>290<?php else: ?>90<?php endif; ?>" class="subnav">
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
		<?php elseif ($itemid == 468): ?>
			<?php include 'dummy/downloads_core.php'; ?>
		<?php elseif ($itemid == 492): ?>
			<?php include 'dummy/styleguide.php'; ?>
		<?php elseif ($itemid == 241): ?>
			<?php include 'dummy/downloads.php'; ?>
		<?php elseif ($itemid == 250): ?>
			<?php include 'dummy/downloads_extensies.php'; ?>
		<?php elseif ($itemid == 251): ?>
			<?php include 'dummy/downloads_extensie.php'; ?>
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
					<h4>JoomlaCommunity.nl</h4>
					<p>JoomlaCommunity is de plaats voor het laatste Joomla nieuws, de nieuwste vertalingen van Joomla en andere extensies, een uitgebreide documentatie sectie en een behulpzaam forum om je wegwijs te maken in de Joomla wereld.</p>
				</div>
				<div class="col-2">
					<h4>Algemeen</h4>
					<ul class="list-unstyled">
						<li><a href="http://www.joomlacommunity.eu/">Nieuws</a></li>
						<li><a href="http://www.joomlacommunity.eu/over-joomla.html">Over Joomla</a></li>
						<li><a href="http://www.joomlacommunity.eu/over-joomlacommunity.html">JoomlaCommunity.nl</a></li>
						<li><a href="http://www.joomlacommunity.eu/boekenhoek.html">Boekenhoek</a></li>
						<li><a href="http://www.joomlacommunity.eu/agenda.html">Agenda</a></li>
						<li><a href="http://www.joomlacommunity.eu/gebruikersgroepen.html">Gebruikersgroepen</a></li>
						<li><a href="http://www.joomlacommunity.eu/component/mailinglist/?task=archive">Nieuwsbrief</a>
						</li>
					</ul>
				</div>
				<div class="col-2">
					<h4>Downloads</h4>
					<ul class="list-unstyled">
						<li><a href="http://download.joomlacommunity.eu/joomla-25.html">Joomla 2.5</a></li>
						<li><a href="http://download.joomlacommunity.eu/joomla-15.html">Joomla 1.5</a></li>
						<li><a href="http://download.joomlacommunity.eu/extensies-25-compatible.html">Extensies 2.5 native</a></li>
						<li><a href="http://download.joomlacommunity.eu/extensies-15-native.html">Extensies 1.5 native</a></li>
						<li><a href="http://download.joomlacommunity.eu/virtuemart.html">VirtueMart</a></li>
						<li><a href="http://download.joomlacommunity.eu/overige.html">Overige</a></li>
					</ul>
				</div>
				<div class="col-2">
					<h4>Documentatie</h4>
					<ul class="list-unstyled">
						<li><a href="http://help.joomlacommunity.eu/algemeen.html">Joomla algemeen</a></li>
						<li><a href="http://help.joomlacommunity.eu/joomla-15.html">Joomla 1.5</a></li>
						<li><a href="http://help.joomlacommunity.eu/joomla-10.html">Joomla 1.0</a></li>
						<li><a href="http://help.joomlacommunity.eu/helpbestanden-15.html">Helpbestanden 1.5</a></li>
						<li><a href="http://help.joomlacommunity.eu/extensies.html">Extensies</a></li>
						<li><a href="http://help.joomlacommunity.eu/virtuemart.html">VirtueMart</a></li>
						<li><a href="http://help.joomlacommunity.eu/links.html">Links</a></li>
					</ul>
				</div>
				<div class="col-2">
					<h4>Forum</h4>
					<ul class="list-unstyled">
						<li><a href="http://forum.joomlacommunity.eu/forumdisplay.php?f=64">Mededelingen</a></li>
						<li><a href="http://forum.joomlacommunity.eu/forumdisplay.php?f=4">Joomla 1.5</a></li>
						<li><a href="http://forum.joomlacommunity.eu/forumdisplay.php?f=5">Joomla 1.0</a></li>
						<li><a href="http://forum.joomlacommunity.eu/forumdisplay.php?f=92">Joomla 2.5</a></li>
						<li><a href="http://forum.joomlacommunity.eu/forumdisplay.php?f=50">VirtueMart</a></li>
						<li><a href="http://forum.joomlacommunity.eu/forumdisplay.php?f=69">Overig</a></li>
						<li><a href="http://forum.joomlacommunity.eu/forumdisplay.php?f=72">Marktplaats</a></li>
					</ul>
				</div>
			</div>
		</div>
	</div>
	<div class="footer-copyright">
		<div class="container">
			<div class="row">
				<div class="col-12">
					<jdoc:include type="modules" name="footer"/>
					<p class="">Copyright © 2008-<?php echo date('Y'); ?> Joomla!Community - Alle rechten voorbehouden</p>
					<p class="pull-right">De naam Joomla!® en logo worden gebruikt onder een beperkte licentie met toestemming van Open Source Matters. JoomlaCommunity.eu is niet verbonden aan en is geen onderdeel van Open Source Matters, Inc, of het Joomla! project.</p>
				</div>
			</div>
		</div>
	</div>
</div>
<jdoc:include type="modules" name="debug"/>
</body>
</html>
