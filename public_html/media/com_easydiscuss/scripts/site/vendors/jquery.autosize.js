ed.define('jquery.autosize', ['edjquery', 'lodash'], function($, _) {

var jQuery = $;


var AutosizeInput = function(input, options) {

	var self = this;

	self.input   = $(input);
	self.options = $.extend(AutosizeInput.defaultOptions, options);
	self.mirror  = $('<span style="position:absolute; top:-999px; left:0; white-space:pre;"/>');

	$.each([
		'fontFamily',
		'fontSize',
		'fontWeight',
		'fontStyle',
		'letterSpacing',
		'textTransform',
		'wordSpacing',
		'textIndent'
	], function (i, val) {
		self.mirror[0].style[val] = self.input.css(val);
	});

	$("body").append(self.mirror);

	self.input.bind("keydown keyup input", function(e){
		self.update();
	});

	self.update();
}

AutosizeInput.defaultOptions = {
	space: 30
}

AutosizeInput.validTypes = [
	"text",
	"password",
	"search",
	"url",
	"tel",
	"email"
];

AutosizeInput.prototype.update = function() {

	var self   = this,
		input  = self.input,
		mirror = self.mirror,
		value  = input.val();

	if (!value) {
		value = input.attr("placeholder");
	}

	if (value === mirror.text()) {
		return;
	}

	mirror.text(value);

	var newWidth = mirror.width() + self.options.space;
	input.width(newWidth);
};

$.fn.autosizeInput = function(options) {

	return this.each(function () {
		if(!(this.tagName == "INPUT" && $.inArray(this.type, AutosizeInput.validTypes) > -1)) {
			return;
		}
		var $this = $(this);
		if (!$this.data("autosizeInputInstance")) {
			$this.data("autosizeInputInstance", new AutosizeInput(this, options));
		}
	});
};

$(function () {
	$("input[data-autosize-input]").autosizeInput();
});


});