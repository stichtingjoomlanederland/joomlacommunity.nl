jQuery(document).ready(function($) {
   if (typeof $._data($("#rscomments-terms")[0], 'events') == 'undefined') {
	   $('#rscomments-terms').on('show.bs.modal', function() {
		   $('body').addClass('modal-open');
		   var modalBody = $(this).find('.modal-body');
		   modalBody.find('iframe').remove();
		   modalBody.prepend('<iframe class="iframe jviewport-height70" src="' + $('input[name="rscomments_terms"]').val() + '" name="Terms & Conditions"></iframe>');
	   }).on('shown.bs.modal', function() {
		   var modalHeight = $('div.modal:visible').outerHeight(true),
			   modalHeaderHeight = $('div.modal-header:visible').outerHeight(true),
			   modalBodyHeightOuter = $('div.modal-body:visible').outerHeight(true),
			   modalBodyHeight = $('div.modal-body:visible').height(),
			   modalFooterHeight = $('div.modal-footer:visible').outerHeight(true),
			   padding = document.getElementById('rscomments-terms').offsetTop,
			   maxModalHeight = ($(window).height()-(padding*2)),
			   modalBodyPadding = (modalBodyHeightOuter-modalBodyHeight),
			   maxModalBodyHeight = maxModalHeight-(modalHeaderHeight+modalFooterHeight+modalBodyPadding);
		   var iframeHeight = $('.iframe').height();
		   if (iframeHeight > maxModalBodyHeight){;
			   $('.modal-body').css({'max-height': maxModalBodyHeight, 'overflow-y': 'auto'});
			   $('.iframe').css('max-height', maxModalBodyHeight-modalBodyPadding);
		   }
	   }).on('hide.bs.modal', function () {
		   $('body').removeClass('modal-open');
		   $('.modal-body').css({'max-height': 'initial', 'overflow-y': 'initial'});
		   $('.modalTooltip').tooltip('destroy');
	   });
   }
   
   if (typeof $._data($("#rscomments-subscribe")[0], 'events') == 'undefined') {
	   $('#rscomments-subscribe').on('show.bs.modal', function() {
		   $('body').addClass('modal-open');
		   var modalBody = $(this).find('.modal-body');
		   modalBody.find('iframe').remove();
		   modalBody.prepend('<iframe class="iframe jviewport-height70" src="' + $('input[name="rscomments_subscribe"]').val() + '" name="Subscribe"></iframe>');
	   }).on('shown.bs.modal', function() {
		   var modalHeight = $('div.modal:visible').outerHeight(true),
			   modalHeaderHeight = $('div.modal-header:visible').outerHeight(true),
			   modalBodyHeightOuter = $('div.modal-body:visible').outerHeight(true),
			   modalBodyHeight = $('div.modal-body:visible').height(),
			   modalFooterHeight = $('div.modal-footer:visible').outerHeight(true),
			   padding = document.getElementById('rscomments-subscribe').offsetTop,
			   maxModalHeight = ($(window).height()-(padding*2)),
			   modalBodyPadding = (modalBodyHeightOuter-modalBodyHeight),
			   maxModalBodyHeight = maxModalHeight-(modalHeaderHeight+modalFooterHeight+modalBodyPadding);
		   var iframeHeight = $('.iframe').height();
		   if (iframeHeight > maxModalBodyHeight){;
			   $('.modal-body').css({'max-height': maxModalBodyHeight, 'overflow-y': 'auto'});
			   $('.iframe').css('max-height', maxModalBodyHeight-modalBodyPadding);
		   }
	   }).on('hide.bs.modal', function () {
		   $('body').removeClass('modal-open');
		   $('.modal-body').css({'max-height': 'initial', 'overflow-y': 'initial'});
		   $('.modalTooltip').tooltip('destroy');
	   });
   }
   
   if (typeof $._data($("#rscomments-report")[0], 'events') == 'undefined') {
	   $('#rscomments-report').on('show.bs.modal', function() {
		   $('body').addClass('modal-open');
		   var modalBody = $(this).find('.modal-body');
		   modalBody.find('iframe').remove();
		   modalBody.prepend('<iframe class="iframe jviewport-height70" src="' + $('input[name="rscomments_report"]').val() + '" name="Report"></iframe>');
	   }).on('shown.bs.modal', function() {
		   var modalHeight = $('div.modal:visible').outerHeight(true),
			   modalHeaderHeight = $('div.modal-header:visible').outerHeight(true),
			   modalBodyHeightOuter = $('div.modal-body:visible').outerHeight(true),
			   modalBodyHeight = $('div.modal-body:visible').height(),
			   modalFooterHeight = $('div.modal-footer:visible').outerHeight(true),
			   padding = document.getElementById('rscomments-report').offsetTop,
			   maxModalHeight = ($(window).height()-(padding*2)),
			   modalBodyPadding = (modalBodyHeightOuter-modalBodyHeight),
			   maxModalBodyHeight = maxModalHeight-(modalHeaderHeight+modalFooterHeight+modalBodyPadding);
		   var iframeHeight = $('.iframe').height();
		   if (iframeHeight > maxModalBodyHeight){;
			   $('.modal-body').css({'max-height': maxModalBodyHeight, 'overflow-y': 'auto'});
			   $('.iframe').css('max-height', maxModalBodyHeight-modalBodyPadding);
		   }
	   }).on('hide.bs.modal', function () {
		   $('body').removeClass('modal-open');
		   $('.modal-body').css({'max-height': 'initial', 'overflow-y': 'initial'});
		   $('.modalTooltip').tooltip('destroy');
	   });
   }
   
   if (typeof $._data($("#rscomments-mycomments")[0], 'events') == 'undefined') {
	   $('#rscomments-mycomments').on('show.bs.modal', function() {
		   $('body').addClass('modal-open');
		   var modalBody = $(this).find('.modal-body');
		   modalBody.find('iframe').remove();
		   modalBody.prepend('<iframe class="iframe jviewport-height70" src="' + $('input[name="rscomments_comments"]').val() + '" name="My comments"></iframe>');
	   }).on('shown.bs.modal', function() {
		   var modalHeight = $('div.modal:visible').outerHeight(true),
			   modalHeaderHeight = $('div.modal-header:visible').outerHeight(true),
			   modalBodyHeightOuter = $('div.modal-body:visible').outerHeight(true),
			   modalBodyHeight = $('div.modal-body:visible').height(),
			   modalFooterHeight = $('div.modal-footer:visible').outerHeight(true),
			   padding = document.getElementById('rscomments-mycomments').offsetTop,
			   maxModalHeight = ($(window).height()-(padding*2)),
			   modalBodyPadding = (modalBodyHeightOuter-modalBodyHeight),
			   maxModalBodyHeight = maxModalHeight-(modalHeaderHeight+modalFooterHeight+modalBodyPadding);
		   var iframeHeight = $('.iframe').height();
		   if (iframeHeight > maxModalBodyHeight){;
			   $('.modal-body').css({'max-height': maxModalBodyHeight, 'overflow-y': 'auto'});
			   $('.iframe').css('max-height', maxModalBodyHeight-modalBodyPadding);
		   }
	   }).on('hide.bs.modal', function () {
		   $('body').removeClass('modal-open');
		   $('.modal-body').css({'max-height': 'initial', 'overflow-y': 'initial'});
		   $('.modalTooltip').tooltip('destroy');
	   });
   }
});