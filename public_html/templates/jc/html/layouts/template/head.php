<?php
/**
 * @package     Joomla.Site
 * @subpackage  Templates.perfecttemplate
 *
 * @copyright   Copyright (C) 2019 Perfect Web Team / perfectwebteam.nl
 * @license     GNU General Public License version 3 or later
 */

defined('JPATH_BASE') or die;
?>

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
<noscript>
	<link href="/templates/perfecttemplate/css/font.css" rel="stylesheet" type="text/css"/>
</noscript>
