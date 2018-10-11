if (typeof RSFormPro != 'object') {
	var RSFormPro = {};
}

// if the browser has not defined the Object.keys function 
if (!Object.keys) Object.keys = function(o) {
  if (o !== Object(o))
    throw new TypeError('Object.keys called on a non-object');
  var k=[],p;
  for (p in o) if (Object.prototype.hasOwnProperty.call(o,p)) k.push(p);
  return k;
};

RSFormPro.ionSlider = {
	sliders:  {},
	slidersData: {},
	setSlider: function(formId, sliderId, config) {
		if (typeof RSFormPro.ionSlider.slidersData[formId] == 'undefined') {
			RSFormPro.ionSlider.slidersData[formId] = {};
		} 
		if (typeof RSFormPro.ionSlider.slidersData[formId][sliderId] == 'undefined') {
			RSFormPro.ionSlider.slidersData[formId][sliderId] = {};
		}
		RSFormPro.ionSlider.slidersData[formId][sliderId].config = config;
	},
	renderSliders: function () {
		var countForms = Object.keys(RSFormPro.ionSlider.slidersData).length;
		var forms = Object.keys(RSFormPro.ionSlider.slidersData);
		
		if (countForms > 0) {
			for (i = 0; i < countForms; i++) {
				var formId = forms[i];
				var slidersIds = Object.keys(RSFormPro.ionSlider.slidersData[formId]);
				
				for (var j = 0; j < slidersIds.length; j++) {
					RSFormPro.ionSlider.initSlider(formId, slidersIds[j], RSFormPro.ionSlider.slidersData[formId][slidersIds[j]].config);
				}
			}
		}
	},
	initSlider: function(formId, sliderId, config) {
		if (typeof RSFormPro.ionSlider.sliders[formId] == 'undefined') {
			RSFormPro.ionSlider.sliders[formId] = {};
		}
		var sliderName   = jQuery('#rs-range-slider'+sliderId).attr('name').substring(5, jQuery('#rs-range-slider'+sliderId).attr('name').length - 1);
		
		if (typeof RSFormPro.ionSlider.sliders[formId][sliderName] == 'undefined') {
			// initiate the object
			RSFormPro.ionSlider.sliders[formId][sliderName] = {};
			RSFormPro.ionSlider.sliders[formId][sliderName].slider = jQuery('#rs-range-slider'+sliderId);
			RSFormPro.ionSlider.sliders[formId][sliderName].slider.ionRangeSlider(config);
		}
	}
};