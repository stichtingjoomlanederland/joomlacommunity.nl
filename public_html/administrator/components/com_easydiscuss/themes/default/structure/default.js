ed.require(['edq', 'easydiscuss'], function($, EasyDiscuss) {
	
	window.insertVideoCode = function(videoURL , caretPosition , elementId) {

		if (videoURL.length == 0) {
			return false;
		}

		var textarea = $('textarea[name=' + elementId + ']');
		var tag = '[video]' + videoURL + '[/video]';

		// If this is at the first position, we don't want to do anything here.
		if (caretPosition == 0) {

			$(textarea).val(tag);
			EasyDiscuss.dialog().close();
			return true;
		}

		var contents = $(textarea).val();

		$(textarea).val(contents.substring(0, caretPosition) + tag + contents.substring(caretPosition, contents.length));
	};

	window.insertPhotoCode = function(photoURL , caretPosition , elementId) {

		if (photoURL.length == 0) {
			return false;
		}

		var textarea = $('textarea[name=' + elementId + ']');
		var tag = '[img]' + photoURL + '[/img]';

		// If this is at the first position, we don't want to do anything here.
		if (caretPosition == 0) {

			$(textarea).val(tag);
			EasyDiscuss.dialog().close();
			return true;
		}

		var contents = $(textarea).val();

		$(textarea).val(contents.substring(0, caretPosition) + tag + contents.substring(caretPosition, contents.length));
	};

	window.insertLinkCode = function(linkURL , linkTitle, caretPosition , elementId) {

		if (linkURL.length == 0) {
			return false;
		}

		if (linkTitle.length == 0) {
			linkTitle = 'Title';
		}

		var textarea = $('textarea[name=' + elementId + ']');
		var tag = '[url=' + linkURL + ']'+ linkTitle +'[/url]';

		// If this is at the first position, we don't want to do anything here.
		if (caretPosition == 0) {

			$(textarea).val(tag);
			EasyDiscuss.dialog().close();
			return true;
		}

		var contents = $(textarea).val();

		$(textarea).val(contents.substring(0, caretPosition) + tag + contents.substring(caretPosition, contents.length));
	};
});



ed.require(['edq'], function($){

	// Script to handle expand / hide of sidebar items
	$('[data-sidebar-link]').on('click', function() {

		var link = $(this);

		link
			.parents('[data-sidebar-item]')
			.toggleClass('active');
	});

	// Fix the header for mobile view
	$('.container-nav').appendTo($('.header'));

	$(window).scroll(function () {
		if ($(this).scrollTop() > 50) {
			$('.header').addClass('header-stick');
		} else if ($(this).scrollTop() < 50) {
			$('.header').removeClass('header-stick');
		}
	});

	$('.nav-sidebar-toggle').click(function(){
		$('html').toggleClass('show-easydiscuss-sidebar');
		$('.subhead-collapse').removeClass('in').css('height', 0);
	});

	var wrapper = $('[data-ed-wrapper]');

	$(document).ready(function() {

		$.ajax({
			url: "<?php echo ED_SERVICE_VERSION;?>",
			jsonp: "callback",
			dataType: "jsonp",
			data: {
				"apikey": "<?php echo $this->config->get('main_apikey');?>",
				"current": "<?php echo $version;?>"
			},
			success: function(data) {

				if (data.error) {
					$('#ed.ed-backend').prepend('<div style="margin-bottom: 0;padding: 15px 24px;font-size: 12px;" class="app-alert o-alert o-alert--danger"><div class="row-table"><div class="col-cell cell-tight"><i class="app-alert__icon fa fa-bolt"></i></div><div class="col-cell alert-message">' + data.error + '</div></div></div>');
				}

				var version = {
					"latest": data.version,
					"local": "<?php echo $version;?>"
				};

				var outdated = EasyDiscuss.compareVersion(version.local, version.latest) === -1;

				// Applicable only on dashboard
				$('[data-online-version]').html(version.latest);
				$('[data-local-version]').html(version.local);

				if (outdated) {
					wrapper.addClass('is-outdated');

					// This is only applicable to the dashboard view
					$('[data-version-checks]').toggleClass('require-updates');

					return;
				}

				// This is only applicable to the dashboard view
				$('[data-version-checks]').toggleClass('latest-updates');
			}
		});

	});

});
