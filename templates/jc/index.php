<?php
/*
 * @package     perfecttemplate
 * @copyright   Copyright (c) Perfect Web Team / perfectwebteam.nl
 * @license     GNU General Public License version 3 or later
 */

// No direct access.
defined('_JEXEC') or die;

// Load Perfect Template Helper
include_once JPATH_THEMES . '/' . $this->template . '/helpers/helper.php';

?>
<!DOCTYPE html>
<html class="html no-js" lang="<?php echo $this->language; ?>" dir="<?php echo $this->direction; ?>">
<head>
	<jdoc:include type="head"/>
</head>

<body class="base <?php echo $helper->getBodySuffix(); ?>">
<?php
if (!empty($analyticsData) && $analyticsData['position'] == 'after_body_start')
{
	echo $analyticsData['script'];
}

//require_once JPATH_THEMES . "/" . $this->template . "/dummy/downloads_core.php";
?>
<div class="leaderboard-container">
	<div class="banner">
		<img src="http://placehold.it/728x90"/>
	</div>
</div>
<div class="header<?php echo $helper->isHome() ? ' homepage' : ''; ?>">
	<nav class="navbar navbar-default navbar-main" role="navigation" data-spy="affix" data-offset-top="110" >
		<div class="container">

			<?php //Brand and toggle get grouped for better mobile display ?>
			<div class="navbar-header navbar-logo">
				<button type="button" class="navbar-toggle collapsed" data-toggle="collapse"
				        data-target="#jc-navbar-collapse-1" aria-expanded="false">
					<span class="sr-only">Toggle navigation</span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				</button>
				<a class="logo" href=".">
					<span class="navbar-logo-name"><?php echo $helper->getSitename(); ?>
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

	<div class="subnav" data-spy="affix" data-offset-top="110">
		<div class="container">
			<nav class="navbar navbar-sub" role="navigation">
				<jdoc:include type="modules" name="submenu"/>
			</nav>
		</div>
	</div>

	<div class="pagetitle">
		<?php if ($this->countModules('slider')) : ?>
			<jdoc:include type="modules" name="slider"/>
		<?php endif; ?>
	</div>
</div>

<!-- Body -->
<div class="content">
	<div class="container">
		<jdoc:include type="modules" name="test" style="xhtml"/>

		<?php if ($helper->getItemId() == 122): ?>
			<?php include 'dummy/nieuws_alt.php'; ?>
		<?php elseif ($helper->getItemId() == 124): ?>
			<?php include 'dummy/nieuws_artikel.php'; ?>
		<?php elseif ($helper->getItemId() == 98): ?>
			<?php include 'dummy/nieuws.php'; ?>
		<?php elseif ($helper->getItemId() == 492): ?>
			<?php include 'dummy/styleguide.php'; ?>
		<?php elseif ($helper->getItemId() == 468): ?>
		<?php endif; ?>

		<div class="row">
			<jdoc:include type="modules" name="top-a" style="panel"/>
		</div>
		<div class="row">
			<?php if ($helper->isHome() == true) : ?>
				<div class="content-6 col-sm-12">
					<jdoc:include type="modules" name="home-sidebar-a__top" style="well"/>
					<jdoc:include type="modules" name="home-sidebar-a__bottom" style="panel"/>
				</div>
				<div class="content-3 col-sm-6">
					<jdoc:include type="modules" name="home-sidebar-b" style="panel"/>
				</div>
				<div class="content-3 col-sm-6">
					<jdoc:include type="modules" name="home-sidebar-c" style="panel"/>
				</div>
			<?php else: ?>
				<div class="content-<?php echo($this->countModules('rechts') ? 8 : 12); ?>">
					<?php if (count(JFactory::getApplication()->getMessageQueue())) : ?>
						<jdoc:include type="message"/>
					<?php endif; ?>
					<jdoc:include type="component"/>
				</div>
				<?php if ($this->countModules('rechts')) : ?>
					<div class="content-4">
						<jdoc:include type="modules" name="rechts" style="panel"/>
					</div>
				<?php endif; ?>
			<?php endif; ?>
		</div>
	</div>
</div>
<!-- Footer -->
<div class="footer">
	<div class="footer-navs">
		<div class="container">
			<div class="row">
				<div class="col-sm-12 col-md-12 col-lg-4">
					<jdoc:include type="modules" name="footer-1" style="xhtml"/>
				</div>
				<div class="col-sm-12 col-md-3 col-lg-2">
					<jdoc:include type="modules" name="footer-2" style="xhtml"/>
				</div>
				<div class="col-sm-12  col-md-3  col-lg-2 ">
					<jdoc:include type="modules" name="footer-3" style="xhtml"/>
				</div>
				<div class="col-sm-12  col-md-3  col-lg-2 ">
					<jdoc:include type="modules" name="footer-4" style="xhtml"/>
				</div>
				<div class="col-sm-12  col-md-3  col-lg-2 ">
					<jdoc:include type="modules" name="footer-5" style="xhtml"/>
				</div>
			</div>
		</div>
	</div>
	<div class="footer-copyright">
		<div class="container">
			<div class="row">
				<div class="col-12">
					<p class="copyright">Copyright &copy; 2008-<?php echo date('Y'); ?> Joomla!Community - Alle rechten
						voorbehouden</p>
					<p class="followus">Volg ons op:
						<a href="https://www.facebook.com/joomlacommunity/" target="_blank">Facebook</a> -
						<a href="https://www.linkedin.com/groups/1857791" target="_blank">LinkedIn</a> -
						<a href="https://twitter.com/joomlacommunity" target="_blank">Twitter</a> -
						<a href="https://www.flickr.com/groups/joomlacommunity/pool/" target="_blank">Flickr</a></p>
					<jdoc:include type="modules" name="copyright"/>
				</div>
			</div>
		</div>
	</div>
</div>
<jdoc:include type="modules" name="debug"/>
</body>
</html>
