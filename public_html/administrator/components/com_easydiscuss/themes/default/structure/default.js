var loaded = false;

// If jquery already exists on the page, use jquery to attach as it seems to be much faster than ed.require
if (jQuery.length) {

jQuery(document).ready(function($) {
	var joomlaClass = "<?php echo ED::isJoomla4() ? 'is-joomla-4' : 'is-joomla-3'; ?>";

	$('body').addClass('si-theme--light ' + joomlaClass);
});

loaded = true;
}

ed.require(['edq', 'easydiscuss'], function($, EasyDiscuss) {

	if (!loaded) {
		var bodyClassName = 'is-joomla-<?php echo ED::isJoomla4() ? "4" : "3";?>';
		bodyClassName += ' si-theme--light';
		document.body.className += ' ' + bodyClassName; 
	}

	// Append help button
	var helpButton = $('#help-button-template');

	if (helpButton.length > 0) {
		helpButton.children().appendTo('#toolbar');
	}
});

ed.require(['edq'], function($){

	// Fix the header for mobile view
	$('.container-nav').appendTo($('.header'));

	// If the page has tabs, we need to add into the app-head
	var hasTabs = $('#ed .nav.nav-tabs').length > 0;

	if (hasTabs) {
		$('#ed .app-head').addClass('has-tab-bar');
	}

	$(window).scroll(function () {
		if ($(this).scrollTop() > 50) {
			$('.header').addClass('header-stick');
		} else if ($(this).scrollTop() < 50) {
			$('.header').removeClass('header-stick');
		}
	});

	$('.nav-sidebar-toggle').click(function(){
		console.log('click');
		$('html').toggleClass('show-easydiscuss-sidebar');
		$('.subhead-collapse').removeClass('in').css('height', 0);
	});

	$('.nav-subhead-toggle').click(function(){
		$('html').removeClass('show-easydiscuss-sidebar');
		$('.subhead-collapse').toggleClass('in').css('height', 'auto');
	});

	// Hide joomla's sidebar wrapper
	var sidebar = $('#ed [data-sidebar]');
	var sidebarHtml = sidebar.html();

	var joomlaSidebar = $('#sidebarmenu');
	var joomlaSidebarNav = joomlaSidebar.find('> nav');


	var joomlaMenu = joomlaSidebarNav.find('ul.main-nav');

	joomlaMenu.hide();

	var joomlaSidebarTemplate = $('[data-j4-sidebar]').html();

	joomlaMenu.prepend(joomlaSidebarTemplate);

	// Append our own sidebar
	joomlaSidebarNav.append(sidebarHtml);

	var edMenu = joomlaSidebarNav.find('ul.app-sidebar-nav');

	$(document).on('click.back.joomla', '[data-back-joomla]', function() {
		joomlaMenu.show();
		edMenu.hide();
	});

	$(document).on('click.back.easydiscuss', '[data-back-easydiscuss]', function() {
		joomlaMenu.hide();
		edMenu.show();
	});

});
