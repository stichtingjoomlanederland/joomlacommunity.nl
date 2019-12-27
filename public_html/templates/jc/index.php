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
    <script>
        jQuery(document).ready(function ($) {
            // Expand button for touch devices
            var toggleSub = $('.toggle-sub');

            if (toggleSub.length) {
                toggleSub.on('click', function () {
                    $(this).toggleClass('active');
                    expandSubmenu($(this).closest('li'));
                    collapseSiblings($(this).closest('li').siblings());
                });
            }
            function expandSubmenu(el) {
                el.toggleClass('expand');
            }

            function collapseSiblings(siblings) {
                siblings.removeClass('expand');
                siblings.find('.toggle-sub').removeClass('active');
            }

            // Class for expand menu
            var toggleNav = $('.navbar-toggle ');

            if (toggleNav.length) {
                toggleNav.on('click', function () {
                    $('.navbar-main').toggleClass('navbar-expanded');
                });
            }
        });

        // Override RSComments to make it compatible with Bootstrap 3
        function rscomments_show_report(id) {
            var modal = jQuery('#rscomments-report');
            var root = typeof rsc_root != 'undefined' ? rsc_root : '';

            modal.find('.modal-body').load(root + 'index.php?option=com_rscomments&task=report&id=' + id);
            modal.modal();
        }
    </script>

    <!-- Temp Google Ads -->
    <script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
    <script>
        (adsbygoogle = window.adsbygoogle || []).push({
            google_ad_client: "ca-pub-9569324843968575",
            enable_page_level_ads: true
        });
    </script>
    <!-- End Temp Google Ads -->

    <script async='async' src='https://www.googletagservices.com/tag/js/gpt.js'></script>
    <script>
        var googletag = googletag || {};
        googletag.cmd = googletag.cmd || [];
    </script>
    <script>
        googletag.cmd.push(function () {
            var mapLeader = googletag.sizeMapping()
                .addSize([320, 400], [320, 50])
                .addSize([768, 200], [728, 90])
                .build();
            window.LeaderSlot = googletag.defineSlot('/81355425/jc_leader', [[320, 50], [728, 90]], 'div-gpt-ad-1483558516537-0')
                .defineSizeMapping(mapLeader)
                .addService(googletag.pubads());
            window.LeaderSlot = googletag.defineSlot('/81355425/jc_bottom', [[320, 50], [728, 90]], 'div-gpt-ad-1487456152006-0')
                .defineSizeMapping(mapLeader)
                .addService(googletag.pubads());
            googletag.enableServices();
        });
    </script>
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
        <div id='div-gpt-ad-1483558516537-0'>
            <script>
                googletag.cmd.push(function () {
                    googletag.display('div-gpt-ad-1483558516537-0');
                });
            </script>
        </div>
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
                    <a href="<?php echo JRoute::_('index.php?Itemid=244'); ?>" class="btn btn-agenda btn-block">
                        Bekijk de volledige agenda
                    </a>

                </div>
			<?php else: ?>
                <div class="content-<?php echo($this->countModules('rechts') ? 8 : 12); ?>">
					<?php if (count(JFactory::getApplication()->getMessageQueue())) : ?>
                        <jdoc:include type="message"/>
					<?php endif; ?>
                    <jdoc:include type="modules" name="above-component"/>
                    <jdoc:include type="component"/>
                    <jdoc:include type="modules" name="below-component"/>
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

<div class="leaderboard-container">
    <div class="banner">
        <div id='div-gpt-ad-1487456152006-0'>
            <script>
                googletag.cmd.push(function () {
                    googletag.display('div-gpt-ad-1487456152006-0');
                });
            </script>
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
                    <p class="copyright">&copy; Copyright 2008-<?php echo date('Y'); ?></p>
                    <jdoc:include type="modules" name="copyright"/>

                    <p class="followusicons">
                        <a href="https://www.facebook.com/joomlacommunity/" target="_blank" class="facebook"><i class="fa fa-facebook-square fa-2x" aria-hidden="true"></i></a>
                        <a href="https://www.linkedin.com/groups/1857791" target="_blank" class="linkedin"><i class="fa fa-linkedin-square fa-2x" aria-hidden="true"></i></a>
                        <a href="https://twitter.com/joomlacommunity" target="_blank" class="twitter"><i class="fa fa-twitter-square fa-2x" aria-hidden="true"></i></a>
                        <a href="https://www.flickr.com/groups/joomlacommunity/pool/" target="_blank"><i class="fa fa-flickr fa-2x" aria-hidden="true"></i></a>
                    </p>
                    <p class="followus">
                        Volg ons op:
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
<jdoc:include type="modules" name="debug"/>
</body>
</html>
