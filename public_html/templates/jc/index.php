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
?>
<div class="leaderboard-container">
	<div class="banner">
		<img src="http://placehold.it/728x90"/>
	</div>
</div>
<div class="header<?php echo $helper->isHome() ? ' homepage' : ''; ?>">
	<nav class="navbar navbar-default navbar-main" role="navigation" data-spy="affix" data-offset-top="110">
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
					<jdoc:include type="modules" name="search"/>
				</div>
				<jdoc:include type="modules" name="usermenu"/>
			</div>

		</div>
	</nav>

	<div class="subnav" data-spy="affix" data-offset-top="110">
		<div class="container">
			<nav class="navbar navbar-sub" role="navigation">
				<jdoc:include type="modules" name="submenu"/>
				<jdoc:include type="modules" name="search"/>
			</nav>
		</div>
	</div>
</div>

<?php if ($helper->isHome() == true) : ?>
	<div class="jc-welcome">
		<div class="overlay">
			<div class="container">
				<div class="row">
					<div class="col-md-12">
						<div class="joomla-intro">
							<jdoc:include type="modules" name="welcome-intro"/>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-md-6 col-lg-3">
						<jdoc:include type="modules" name="welcome-a"/>
					</div>
					<div class="col-md-6 col-lg-3">
						<jdoc:include type="modules" name="welcome-b"/>
					</div>
					<div class="col-md-6 col-lg-3">
						<jdoc:include type="modules" name="welcome-c"/>
					</div>
					<div class="col-md-6 col-lg-3">
						<jdoc:include type="modules" name="welcome-d"/>
					</div>
				</div>
			</div>
		</div>
	</div>
<?php endif; ?>

<!-- Body -->
<div class="content">
	<div class="container">
		<div class="row">
			<jdoc:include type="modules" name="top-a" style="panel"/>
		</div>
		<div class="row">
			<jdoc:include type="modules" name="breadcrumbs"/>

			<?php if ($helper->isHome() == true) : ?>
				<div class="col-md-7 laatste-nieuws">
					<jdoc:include type="modules" name="home-left" style=""/>
				</div>
				<div class="col-md-5">
					<div class="promo">
						<jdoc:include type="modules" name="home-promo"/>
					</div>
					<jdoc:include type="modules" name="home-right" style="panel"/>
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
					<p class="copyright">Copyright &copy; 2008-<?php echo date('Y'); ?>
						<a href="http://www.stichtingsympathy.nl">Stichting Sympathy</a> - Alle rechten voorbehouden
					</p>
					<p class="followus">
						Volg ons op:
						<a href="https://www.facebook.com/joomlacommunity/" target="_blank" class="facebook"><i class="fa fa-facebook-square fa-2x" aria-hidden="true"></i></a>
						<a href="https://www.linkedin.com/groups/1857791" target="_blank" class="linkedin"><i class="fa fa-linkedin-square fa-2x" aria-hidden="true"></i></a>
						<a href="https://twitter.com/joomlacommunity" target="_blank" class="twitter"><i class="fa fa-twitter-square fa-2x" aria-hidden="true"></i></a>
						<a href="https://www.flickr.com/groups/joomlacommunity/pool/" target="_blank"><i class="fa fa-flickr fa-2x" aria-hidden="true"></i></a>
					</p>
				</div>
			</div>
			<div class="row">
				<div class="col-12">
					<div class="joomla-disclaimer">
						<jdoc:include type="modules" name="copyright"/>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<jdoc:include type="modules" name="debug"/>
</body>
</html>