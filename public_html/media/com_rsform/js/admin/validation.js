if (typeof RSFormPro != 'object') {
	var RSFormPro = {};
}

RSFormPro.Validations = {
	Numeric: function () {
		jQuery('.rsform_hide').on("keyup", '[data-properties="numeric"]', function () {
			/**
			 * jQuery.isNumeric
			 * https://api.jquery.com/jQuery.isNumeric/#jQuery-isNumeric-value
			 */
			if (!jQuery.isNumeric(jQuery(this).val()) && jQuery(this).val() != '') {
				jQuery(this).val(jQuery(this).val().replace(/[^0-9]/g, ''));
			}
		});
	},
	
	Float: function () {
		jQuery('.rsform_hide').on("keyup", '[data-properties="float"]', function () {
			/**
			 * jQuery.isNumeric
			 * https://api.jquery.com/jQuery.isNumeric/#jQuery-isNumeric-value
			 */
			if (!jQuery.isNumeric(jQuery(this).val()) && jQuery(this).val() != '') {
				jQuery(this).val(jQuery(this).val().replace(/\.{1,}/g, '.').replace(/[^0-9\.]/g, ''));
			}
		});
	},

	Tags: function ($field) {
		jQuery.each($field, function () {
			var $selector = jQuery('#' + this.selector);
			if ($selector.attr('data-properties') == 'oneperline') {
				$selector.tagEditor({
					delimiter: '\n ,'
				})
			}
		});
	},

	Tooltips: function($field) {
		if (typeof jQuery.fn.popover == 'function') {
			jQuery('.fieldHasTooltip').popover({"html": true, "container": "body", "trigger": 'hover'});
		} else {
			new Tips($$('.fieldHasTooltip'), { maxTitleChars: 50, fixed: false});
		}
	},

	Toggle: function ($field) {
		jQuery.each($field, function () {
            var $selector = jQuery('#' + this.selector);
			if ($selector.attr('data-properties') == 'toggler') {
				/**
				 * Get the JSON sent through the data attributes
				 */
				var $data = this.data;
				var $el	= jQuery('#' + this.selector);
				var $initialVal = $el.val();

				/**
				 * If there are 2 - 3 scenarios for conditionals, the
				 * JSON object that is sent through the DATA ATTRIBUTES
				 * should be like this:
				 * case -> type -> { show : fields , hide : fields}
				 */
				jQuery.each($data.case[$initialVal].hide, function(){
					jQuery('#id' + this).hide();
				});

				$el.change(function () {

					var $value = this.value;

					jQuery.each($data.case[$value].hide, function(){
						jQuery('#id' + this).hide();
					});

					jQuery.each($data.case[$value].show, function(){
						jQuery('#id' + this).show();
					});
				});
			}
		});
	}


};

/**
 * Initiate Validations
 */
jQuery(document).ready(function () {
    RSFormPro.Validations.Numeric();
    RSFormPro.Validations.Float();
	/**
	 * Bind the functions to the event created
	 * in administrator\components\com_rsform\assets\js\script.js
	 */
	jQuery('.rsform_hide').on('renderedLayout',
		function (objectEvent, $field) {

			RSFormPro.Validations.Tags($field);
			RSFormPro.Validations.Toggle($field);
			RSFormPro.Validations.Tooltips($field);
		})


});

