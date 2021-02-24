if (typeof RSFormPro !== 'object') {
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

// set the moment custom localization
jQuery(document).ready(function(){
	moment.locale('custom', {
		months : RSFormPro.jQueryCalendar.settings.MONTHS_LONG,
		monthsShort : RSFormPro.jQueryCalendar.settings.MONTHS_SHORT,
		weekdays : RSFormPro.jQueryCalendar.settings.WEEKDAYS_LONG,
		weekdaysShort : RSFormPro.jQueryCalendar.settings.WEEKDAYS_MEDIUM,
		weekdaysMin: RSFormPro.jQueryCalendar.settings.WEEKDAYS_SHORT
	});
});

Date.parseDate = function( input, format ){
	return moment(input,format).toDate();
};
Date.prototype.dateFormat = function( format ){
	return moment(this).format(format);
};

RSFormPro.jQueryCalendar = {
	settings: {},
	calendars:  {},
	calendarsData: {},
	setCalendar: function(formId, idCalendar, config) {
		if (typeof RSFormPro.jQueryCalendar.calendarsData[formId] === 'undefined') {
			RSFormPro.jQueryCalendar.calendarsData[formId] = {};
		}
		if (typeof RSFormPro.jQueryCalendar.calendarsData[formId][idCalendar] === 'undefined') {
			RSFormPro.jQueryCalendar.calendarsData[formId][idCalendar] = {};
		}
		RSFormPro.jQueryCalendar.calendarsData[formId][idCalendar].config = config;
	},
	renderCalendars: function() {
		var forms = Object.keys(RSFormPro.jQueryCalendar.calendarsData);
		var countForms = forms.length;

		if (countForms > 0) {
			for (var i = 0; i < countForms; i++) {
				var formId = forms[i];
				var calendarsIds = Object.keys(RSFormPro.jQueryCalendar.calendarsData[formId]);

				for (var j = 0; j < calendarsIds.length; j++) {
					RSFormPro.jQueryCalendar.initCalendar(formId, calendarsIds[j], RSFormPro.jQueryCalendar.calendarsData[formId][calendarsIds[j]].config);
				}
			}
		}
	},
	initCalendar: function(formId, idCalendar, config) {
		if (typeof RSFormPro.jQueryCalendar.calendars[formId] === 'undefined') {
			RSFormPro.jQueryCalendar.calendars[formId] = {};
		}


		var calendarId 	 = 'cal'+idCalendar;
		var txtDate 	 = jQuery('#txtjQ' + calendarId);

		if (!txtDate.length)
		{
			return;
		}

		var hiddenDate 	 = jQuery('#hiddenjQ' + calendarId);
		var calendarName = txtDate.attr('name').substring(5, txtDate.attr('name').length - 1);

		if (typeof RSFormPro.jQueryCalendar.calendars[formId][calendarName] === 'undefined') {
			// initiate the object
			RSFormPro.jQueryCalendar.calendars[formId][calendarName] = {};
			// check if the date format configured by the user has any time data in it
			var hasTimeDateFormat = /H{1,2}|h{1,2}|a|A|m{1,2}|s{1,2}|S{1,3}|Z{1,2}/.test(config.format);
			if (config.timepicker && !hasTimeDateFormat) {
				config.timepicker = false;
			}
			// if the value is set configure the startDate and apply it to the hidden field
			var startDate = RSFormPro.jQueryCalendar.stringToDate(config.value, config.timepicker);
			if (startDate) {
				hiddenDate.val(config.value);
			}

			// set the min and max Date
			var minDate = typeof config.extra.minDate !== 'undefined' ? config.extra.minDate : false;
			var maxDate = typeof config.extra.maxDate !== 'undefined' ? config.extra.maxDate : false;

			// set the min and max Time
			var minTime = (typeof config.extra.minTime !== 'undefined' && config.extra.minTime.indexOf(':') > -1) ? config.extra.minTime : false;
			var maxTime = (typeof config.extra.maxTime !== 'undefined' && config.extra.maxTime.indexOf(':') > -1) ? config.extra.maxTime : false;
			// set the time step
			var step = (typeof config.extra.step !== 'undefined' && !isNaN(config.extra.step)) ? parseInt(config.extra.step) : 60;

			// set the allowDates
			var allowDates = (typeof config.extra.allowDates === 'object' && jQuery.isArray(config.extra.allowDates)) ? config.extra.allowDates : [];
			// set the allowDateRe
			var allowDateRe = typeof config.extra.allowDateRe !== 'undefined' ? config.extra.allowDateRe : null;

			// set the hidden field date format
			var hiddenFormat = 'MM/DD/YYYY';
			if (config.timepicker) {
				hiddenFormat += ' HH:mm';
			}

			// set the rule
			var operation 	= false;
			var offset 		= 1;
			if (config.extra.rule) {
				var rule 				= config.extra.rule.split('|');
				var otherCalendarName   = rule[1];
				operation 				= rule[0];

				if (typeof rule[2] !== 'undefined')
				{
					offset = parseInt(rule[2]);
				}
			}

			// set the timepicker format
			if (typeof config.timepickerformat === 'undefined') {
				config.timepickerformat = 'HH:mm';
			}

			// set the minTime/maxTime for the specific date according to the minDate/MaxDate
			var minSpecificTime = '';
			var maxSpecificTime = '';
			if (config.timepicker) {
				if (minDate) {
					var minDateParts = minDate.split(' ');
					minDate = minDateParts[0];
					minSpecificTime = minDateParts[1];
				}
				if (maxDate) {
					var maxDateParts = maxDate.split(' ');
					maxDate = maxDateParts[0];
					maxSpecificTime = maxDateParts[1];
				}
			}

			// set the minDate and maxDate for the other calendar if the rule is present
			if (operation && ((typeof config.value !== 'undefined' && config.value != '') || minDate || maxDate)) {
				// configure the date and time regarding the rule for the other calendar
				var referenceDate = false;
				if (typeof config.value != 'undefined' && config.value != '') {
					referenceDate = config.value;
				} else if (minDate && operation == 'min') {
					referenceDate = minDate;
				} else if (maxDate && operation == 'max') {
					referenceDate = maxDate;
				}

				if (referenceDate) {
					var newDateParts = referenceDate.split(' ');
					var newDate = newDateParts[0];
					var newTime = false;
					if (newDateParts.length > 1) {
						newTime = newDateParts[1];
					}

					// if the calendar does not use the timepicker we must increment or decrement the day by 1
					if (!config.timepicker) {
						var newDateObject = new Date.parseDate(newDate, 'MM/DD/YYYY');
						if (operation == 'min') {
							var d = newDateObject.getDate() + offset;
						} else {
							var d = newDateObject.getDate() - offset;
						}
						newDateObject.setDate(d);
						newDate = newDateObject.dateFormat('MM/DD/YYYY');
					}

					var otherCalendar = false;
					if (typeof RSFormPro.jQueryCalendar.calendars[formId][otherCalendarName] != 'undefined') {
						otherCalendar = RSFormPro.jQueryCalendar.calendars[formId][otherCalendarName];
					}

					if (otherCalendar) {
						RSFormPro.jQueryCalendar.calendars[formId][otherCalendarName].calendar.datetimepicker((operation == 'min' ? {minDate: newDate} : {maxDate: newDate}));

						var newDateObject = new Date.parseDate(newDate, 'MM/DD/YYYY');
						var otherDate = RSFormPro.jQueryCalendar.calendars[formId][otherCalendarName].currentDate;
						if (otherDate != '') {
							otherDate = Date.parseDate(otherDate, RSFormPro.jQueryCalendar.calendars[formId][otherCalendarName].hiddenFormat);
							if ((operation == 'min' && newDateObject.getTime() > otherDate.getTime()) || (operation == 'max' && newDateObject.getTime() < otherDate.getTime())) {
								RSFormPro.jQueryCalendar.calendars[formId][otherCalendarName].calendar.val('');
								RSFormPro.jQueryCalendar.calendars[formId][otherCalendarName].calendar.datetimepicker({startDate:false});
								RSFormPro.jQueryCalendar.calendars[formId][otherCalendarName].hiddenDate.val('');
							}
						}

						if (newTime) {
							RSFormPro.jQueryCalendar.calendars[formId][otherCalendarName].calendar.datetimepicker((operation == 'min' ? {minTime: newTime} : {maxTime: newTime}));
						}
					} else {
						var otherCalendarInput = document.getElementsByName("form["+otherCalendarName+"]");

						// get the proper field
						for (i = 0 ; i < otherCalendarInput.length; i++) {
							var otherCalendarId = otherCalendarInput[i].id;
							if (otherCalendarId.indexOf('txtjQcal'+formId+'_') === 0)
							{
								otherCalendarId = otherCalendarId.substring(8, otherCalendarId.length);
								break;
							}
						}

						if (operation == 'min') {
							if (typeof RSFormPro.jQueryCalendar.calendarsData[formId][otherCalendarId].config.extra.minDate == 'undefined') {
								RSFormPro.jQueryCalendar.calendarsData[formId][otherCalendarId].config.extra.minDate = '';
							}
							RSFormPro.jQueryCalendar.calendarsData[formId][otherCalendarId].config.extra.minDate = newDate;
						}
						if (operation == 'max') {
							if (typeof RSFormPro.jQueryCalendar.calendarsData[formId][otherCalendarId].config.extra.maxDate == 'undefined') {
								RSFormPro.jQueryCalendar.calendarsData[formId][otherCalendarId].config.extra.maxDate = '';
							}
							RSFormPro.jQueryCalendar.calendarsData[formId][otherCalendarId].config.extra.maxDate = newDate;
						}

						var otherTxtDate = jQuery('#txtjQcal'+otherCalendarId);
						var otherHiddenDate = jQuery('#hiddenjQcal'+otherCalendarId);

						var otherDate = new Date.parseDate(otherHiddenDate.val(), 'MM/DD/YYYY HH:mm');
						var newDateObject = new Date.parseDate(newDate, 'MM/DD/YYYY');

						if ((operation == 'min' && newDateObject.getTime() > otherDate.getTime()) || (operation == 'max' && newDateObject.getTime() < otherDate.getTime())) {
							otherHiddenDate.val('');
							otherTxtDate.val('');
							RSFormPro.jQueryCalendar.calendarsData[formId][otherCalendarId].config.value='';
						}

						if (newTime) {
							RSFormPro.jQueryCalendar.calendarsData[formId][otherCalendarId].config.extra.useTimeLogic = {date: newDate, time: newTime, rule: operation};
						}
					}
				}
			}

			// set the current date based on the config value if is set and the hidden date format, will be needing this for the correct rule implementation
			RSFormPro.jQueryCalendar.calendars[formId][calendarName].currentDate  = config.value;
			RSFormPro.jQueryCalendar.calendars[formId][calendarName].hiddenFormat = hiddenFormat;
			RSFormPro.jQueryCalendar.calendars[formId][calendarName].hiddenDate   = hiddenDate;
			RSFormPro.jQueryCalendar.calendars[formId][calendarName].timepicker   = config.timepicker;
			RSFormPro.jQueryCalendar.calendars[formId][calendarName].useTimeLogic = (typeof config.extra.useTimeLogic != 'undefined' ? config.extra.useTimeLogic : false);
			RSFormPro.jQueryCalendar.calendars[formId][calendarName].callbackSelectedDateTime = false;

			if (minSpecificTime != '' || maxSpecificTime !='') {
				RSFormPro.jQueryCalendar.calendars[formId][calendarName].useSpecificTimeLogic = {minSpecificTime:minSpecificTime, maxSpecificTime: maxSpecificTime, minDate: minDate, maxDate: maxDate, defaultMinTime:minTime, defaultMaxTime: maxTime};
			} else {
				RSFormPro.jQueryCalendar.calendars[formId][calendarName].useSpecificTimeLogic = false;
			}

			var defaultSelectValue = config.inline ? false : true;

			// set yearStart if minDate is available
			var yearStart = 1950;
			var startDateForce = false;
			if (minDate) {
				var minDateObject = new Date.parseDate(minDate, 'MM/DD/YYYY');
				yearStart = minDateObject.getFullYear();

				if (!startDate) {
					var currentTime = new Date();

					if (minDateObject > currentTime) {
						startDateForce = minDateObject;
					}
				}
			}

			// set yearEnd if maxDate is available
			var yearEnd = 2050;
			if (maxDate) {
				var maxDateObject = new Date.parseDate(maxDate, 'MM/DD/YYYY');
				yearEnd = maxDateObject.getFullYear();

				if (!startDate) {
					var currentTime = new Date();

					if (maxDateObject < currentTime) {
						startDateForce = maxDateObject;
					}
				}
			}

			RSFormPro.jQueryCalendar.calendars[formId][calendarName].calendar = jQuery(txtDate).datetimepicker({
				format: config.format, // the format used for the output
				formatDate: 'MM/DD/YYYY', // the format used by the minDate and maxDate
				formatTime: config.timepickerformat, // the time format used in the calendar, works only if the timepicker is available
				inline: config.inline,
				defaultSelect: defaultSelectValue,
				startDate: (startDate ? startDate : startDateForce),
				timepicker: config.timepicker,
				theme: config.theme,
				closeOnDateSelect: config.timepicker == '1' ? false : true,
				closeOnWithoutClick: true,
				minDate: minDate,
				maxDate: maxDate,
				minTime: minTime,
				maxTime: maxTime,
				allowDates: allowDates,
				allowDateRe: allowDateRe,
				yearStart: yearStart,
				yearEnd: yearEnd,
				step: step,
				validateOnBlur: false,
				onChangeMonth: function($ct, $i){
					// if the timepicker exists but a year is not in the min/max range then we must disable the timepicker if its active
					if (config.timepicker) {
						// check if max/min date is set
						var currentMaxDate = this.getOptions('maxDate');
						var currentMinDate = this.getOptions('minDate');

						// handle the conditions
						if (currentMaxDate || currentMinDate) {
							currentMaxDate = new Date.parseDate(currentMaxDate, 'MM/DD/YYYY HH:mm');
							currentMinDate = new Date.parseDate(currentMinDate, 'MM/DD/YYYY HH:mm');

							if ($ct > currentMaxDate || $ct < currentMinDate){
								this.setOptions({timepicker: false});
							}
						}

						//if the allowDateRe or allowDates are used
						var allowDateRegex = this.getOptions('allowDateRe');
						var allowDates = this.getOptions('allowDates');
						if (allowDateRegex || (allowDates && allowDates.length > 0)) {
							var formatDate = this.getOptions('formatDate');

							if (allowDateRegex) {
								if (!allowDateRegex.test($ct.dateFormat(formatDate))) {
									this.setOptions({timepicker: false});
								}
							} else {
								if(allowDates.indexOf($ct.dateFormat(formatDate)) === -1){
									this.setOptions({timepicker: false});
								}
							}
						}
					}
				},
				dayOfWeekStart: RSFormPro.jQueryCalendar.settings.START_WEEKDAY,
				i18n: {
					'custom': {
						months: RSFormPro.jQueryCalendar.settings.MONTHS_LONG,
						dayOfWeek: RSFormPro.jQueryCalendar.settings.WEEKDAYS_SHORT
					}
				},
				lang: 'custom',
				onSelectDate: function(ct, $i) {
					if (RSFormPro.jQueryCalendar.calendars[formId][calendarName].useSpecificTimeLogic) {
						RSFormPro.jQueryCalendar.specificTimeLogic(this, ct, RSFormPro.jQueryCalendar.calendars[formId][calendarName].useSpecificTimeLogic);
					}

					if (RSFormPro.jQueryCalendar.calendars[formId][calendarName].useTimeLogic) {
						var inputParams = {formId: formId, calendarName: calendarName, config: config};
						RSFormPro.jQueryCalendar.timeLogic(this, ct, inputParams, RSFormPro.jQueryCalendar.calendars[formId][calendarName].useTimeLogic);
					}

					var selectedDate = ct.dateFormat(hiddenFormat);
					if (config.timepicker) {
						// check timepicker state
						var checkTimePicker = this.getOptions('timepicker');
						if (!checkTimePicker) {
							this.setOptions({timepicker: true});
						}
						var currentMinTime = this.getOptions('minTime');
						var currentMaxTime = this.getOptions('maxTime');

						// need the time Line
						var timeLine = RSFormPro.jQueryCalendar.generateTimeLine(ct, currentMinTime, currentMaxTime, step);

						var checkDate = RSFormPro.jQueryCalendar.checkSelected(this, selectedDate, currentMinTime, currentMaxTime, step, timeLine);
						if (checkDate != selectedDate) {
							selectedDate = checkDate;
							var calendarInput = $i;
							if (selectedDate != '') {
								var startDateObject = RSFormPro.jQueryCalendar.stringToDate(selectedDate, true);
								var startMiliseconds = startDateObject.getTime();
								ct.setTime(startMiliseconds);
								calendarInput.val(startDateObject.dateFormat(config.format));
							} else {
								calendarInput.val(selectedDate);
							}
						}

						// if the currentMinTime is higher than the currentMaxTime we need to increment a day in the calendar
						if (currentMinTime && currentMaxTime) {
							var curentMinTimeParts = currentMinTime.split(':');
							var currentMaxTimeParts = currentMaxTime.split(':');

							var change = false;
							if (parseInt(curentMinTimeParts[0]) > parseInt(currentMaxTimeParts[0])) {
								change = true;
							} else {
								if (parseInt(curentMinTimeParts[1]) > parseInt(currentMaxTimeParts[1])) {
									change = false;
								}
							}

							if (change) {
								var d = ct.getDate() + 1;
								var minTimeParts = minTime.split(':');
								ct.setHours(minTimeParts[0]);
								ct.setMinutes(minTimeParts[1]);
								ct.setSeconds(0);
								ct.setDate(d);
								this.setOptions({minDate:ct.dateFormat('MM/DD/YYYY'), minTime:minTime, maxTime:maxTime});

								calendarInput.val(ct.dateFormat(config.format));
								selectedDate = ct.dateFormat(hiddenFormat);
							}
						}
					}

					hiddenDate.val(selectedDate);
					RSFormPro.jQueryCalendar.calendars[formId][calendarName].currentDate = selectedDate;
				},
				onSelectTime: function(ct, $i) {
					var selectedDate = ct.dateFormat(hiddenFormat);

					if (operation && config.timepicker) {
						// if the calendar only has the time picker we must let the timepicker option unchanged
						if (RSFormPro.jQueryCalendar.calendars[formId][otherCalendarName].datepicker && RSFormPro.jQueryCalendar.calendars[formId][otherCalendarName].timepicker) {
							RSFormPro.jQueryCalendar.calendars[formId][otherCalendarName].calendar.datetimepicker({timepicker: false});
						}
					}

					hiddenDate.val(selectedDate);
					RSFormPro.jQueryCalendar.calendars[formId][calendarName].currentDate = selectedDate;
				},

				// used to take in consideration the rules
				onChangeDateTime: function(dp,$input) {
					if (RSFormPro.jQueryCalendar.calendars[formId][calendarName].useSpecificTimeLogic) {
						RSFormPro.jQueryCalendar.specificTimeLogic(this, dp, RSFormPro.jQueryCalendar.calendars[formId][calendarName].useSpecificTimeLogic);
					}

					if (RSFormPro.jQueryCalendar.calendars[formId][calendarName].useTimeLogic) {
						var inputParams = {formId: formId, calendarName: calendarName, config: config};
						RSFormPro.jQueryCalendar.timeLogic(this, dp, inputParams, RSFormPro.jQueryCalendar.calendars[formId][calendarName].useTimeLogic);
					}

					if (operation) {
						var selectedDateTime = dp;
						var otherCalendarDate = false;

						if (!RSFormPro.jQueryCalendar.calendars[formId][calendarName].timepicker) {
							otherCalendarDate = Date.parse(selectedDateTime);
							otherCalendarDate = new Date(otherCalendarDate);
							if (operation == 'min') {
								var d = otherCalendarDate.getDate() + offset;
							} else {
								var d = otherCalendarDate.getDate() - offset;
							}

							otherCalendarDate.setDate(d);
						}

						if (!otherCalendarDate) {
							otherCalendarDate = selectedDateTime;
						}

						// force startDate
						var currentTime = new Date();
						var startDateForceOther = false;
						var startForceCalendar = false;
						if ((operation == 'min' && otherCalendarDate > currentTime) || (operation == 'max' && otherCalendarDate < currentTime)) {
							startDateForceOther = otherCalendarDate;
						}

						var otherDate = RSFormPro.jQueryCalendar.calendars[formId][otherCalendarName].currentDate;
						if (otherDate != '') {
							otherDate = Date.parseDate(otherDate, RSFormPro.jQueryCalendar.calendars[formId][otherCalendarName].hiddenFormat);
							if ((operation == 'min' && selectedDateTime.getTime() > otherDate.getTime()) || (operation == 'max' && selectedDateTime.getTime() < otherDate.getTime())) {
								RSFormPro.jQueryCalendar.calendars[formId][otherCalendarName].calendar.val('');
								RSFormPro.jQueryCalendar.calendars[formId][otherCalendarName].calendar.datetimepicker({startDate:startDateForceOther});
								RSFormPro.jQueryCalendar.calendars[formId][otherCalendarName].hiddenDate.val('');
								if (RSFormPro.jQueryCalendar.calendars[formId][otherCalendarName].timepicker) {
									RSFormPro.jQueryCalendar.calendars[formId][otherCalendarName].calendar.datetimepicker({timepicker: false});
								}
								startForceCalendar = false;
							}
						} else {
							// if there is no date previously selected we only need to set the start date if the startDateForceOther is set
							if (startDateForceOther) {
								RSFormPro.jQueryCalendar.calendars[formId][otherCalendarName].calendar.datetimepicker({startDate:startDateForceOther});
								startForceCalendar = false;
							}
						}

						var selectedDate = otherCalendarDate.dateFormat('MM/DD/YYYY');
						var selectedTime = otherCalendarDate.dateFormat('HH:mm');

						RSFormPro.jQueryCalendar.calendars[formId][otherCalendarName].calendar.datetimepicker((operation == 'min' ? {minDate: selectedDate, yearStart: otherCalendarDate.getFullYear()} : {maxDate: selectedDate, yearEnd: otherCalendarDate.getFullYear()}));

						if (startForceCalendar  && startDateForceOther) {
							if (RSFormPro.jQueryCalendar.calendars[formId][otherCalendarName].calendarInstance.getOptions('inline')) {
								RSFormPro.jQueryCalendar.calendars[formId][otherCalendarName].calendarInstance.setOptions({value:startDateForceOther});
								RSFormPro.jQueryCalendar.calendars[formId][otherCalendarName].calendarInstance.setOptions({value:''});
							}
						}

						if (RSFormPro.jQueryCalendar.calendars[formId][calendarName].timepicker) {

							RSFormPro.jQueryCalendar.calendars[formId][otherCalendarName].useTimeLogic = {date: selectedDate, time: selectedTime, rule: operation};
						}
					}
				},

				onAfterChanges : function(ct, $i) {
					if (RSFormPro.jQueryCalendar.calendars[formId][calendarName].useTimeLogic) {
						var inputParams = {formId: formId, calendarName: calendarName, config: config};
						RSFormPro.jQueryCalendar.timeLogic(this, ct, inputParams, RSFormPro.jQueryCalendar.calendars[formId][calendarName].useTimeLogic);
					}

					var selectedDate = ct.dateFormat(hiddenFormat);
					if (config.timepicker) {
						// check timepicker state
						if (!this.getOptions('timepicker')) {
							this.setOptions({timepicker: true});
						}
						var currentMinTime = this.getOptions('minTime');
						var currentMaxTime = this.getOptions('maxTime');

						// need the time Line
						var timeLine = RSFormPro.jQueryCalendar.generateTimeLine(ct, currentMinTime, currentMaxTime, step);

						var checkDate = RSFormPro.jQueryCalendar.checkSelected(this, selectedDate, currentMinTime, currentMaxTime, step, timeLine);
						if (checkDate != selectedDate) {
							selectedDate = checkDate;
							var calendarInput = $i;
							if (selectedDate != '') {
								var startDateObject = RSFormPro.jQueryCalendar.stringToDate(selectedDate, true);
								var startMiliseconds = startDateObject.getTime();
								ct.setTime(startMiliseconds);
								calendarInput.val(startDateObject.dateFormat(config.format));
							} else {
								calendarInput.val(selectedDate);
							}
						}

						// if the currentMinTime is higher than the currentMaxTime we need to increment a day in the calendar
						if (currentMinTime && currentMaxTime) {
							var curentMinTimeParts = currentMinTime.split(':');
							var currentMaxTimeParts = currentMaxTime.split(':');

							var change = false;
							if (parseInt(curentMinTimeParts[0]) > parseInt(currentMaxTimeParts[0])) {
								change = true;
							} else {
								if (parseInt(curentMinTimeParts[1]) > parseInt(currentMaxTimeParts[1])) {
									change = false;
								}
							}

							if (change) {
								var d = ct.getDate() + 1;
								var minTimeParts = minTime.split(':');
								ct.setHours(minTimeParts[0]);
								ct.setMinutes(minTimeParts[1]);
								ct.setSeconds(0);
								ct.setDate(d);
								this.setOptions({minDate:ct.dateFormat('MM/DD/YYYY'), minTime:minTime, maxTime:maxTime});

								calendarInput.val(ct.dateFormat(config.format));
								selectedDate = ct.dateFormat(hiddenFormat);
							}
						}

					}

					if (operation && selectedDate != '') {
						var selectedDateTime = RSFormPro.jQueryCalendar.stringToDate(selectedDate, config.timepicker);

						// force startDate
						var currentTime = new Date();
						var startDateForceOther = false;
						var startForceCalendar = false;
						if ((operation == 'min' && selectedDateTime > currentTime) || (operation == 'max' && selectedDateTime < currentTime)) {
							startDateForceOther = selectedDateTime;
						}

						var otherDate = RSFormPro.jQueryCalendar.calendars[formId][otherCalendarName].currentDate;

						if (otherDate != '') {
							otherDate = Date.parseDate(otherDate, RSFormPro.jQueryCalendar.calendars[formId][otherCalendarName].hiddenFormat);
							if ((operation == 'min' && selectedDateTime.getTime() > otherDate.getTime()) || (operation == 'max' && selectedDateTime.getTime() < otherDate.getTime())) {
								RSFormPro.jQueryCalendar.calendars[formId][otherCalendarName].calendar.val('');
								RSFormPro.jQueryCalendar.calendars[formId][otherCalendarName].calendar.datetimepicker({startDate:startDateForceOther});
								RSFormPro.jQueryCalendar.calendars[formId][otherCalendarName].hiddenDate.val('');
								if (RSFormPro.jQueryCalendar.calendars[formId][otherCalendarName].timepicker) {
									RSFormPro.jQueryCalendar.calendars[formId][otherCalendarName].calendar.datetimepicker({timepicker: false});
								}
								startForceCalendar = true;
							}
						} else {
							// if there is no date previously selected we only need to set the start date if the startDateForceOther is set
							if (startDateForceOther) {
								RSFormPro.jQueryCalendar.calendars[formId][otherCalendarName].calendar.datetimepicker({startDate:startDateForceOther});
								startForceCalendar = true;
							}
						}

						if (operation === 'min') {
							selectedDateTime.setDate(selectedDateTime.getDate() + offset);
						} else {
							selectedDateTime.setDate(selectedDateTime.getDate() - offset);
						}

						var selectedOtherDate = selectedDateTime.dateFormat('MM/DD/YYYY');
						var selectedOtherTime = selectedDateTime.dateFormat('HH:mm');

						RSFormPro.jQueryCalendar.calendars[formId][otherCalendarName].calendar.datetimepicker((operation == 'min' ? {minDate: selectedOtherDate,yearStart: selectedDateTime.getFullYear()} : {maxDate: selectedOtherDate, yearEnd: selectedDateTime.getFullYear()}));
						if (startForceCalendar  && startDateForceOther) {
							if (RSFormPro.jQueryCalendar.calendars[formId][otherCalendarName].calendarInstance.getOptions('inline')) {
								RSFormPro.jQueryCalendar.calendars[formId][otherCalendarName].calendarInstance.setOptions({value:startDateForceOther});
								RSFormPro.jQueryCalendar.calendars[formId][otherCalendarName].calendarInstance.setOptions({value:''});
							}
						}

						if (RSFormPro.jQueryCalendar.calendars[formId][calendarName].timepicker) {

							RSFormPro.jQueryCalendar.calendars[formId][otherCalendarName].useTimeLogic = {date: selectedOtherDate, time: selectedOtherTime, rule: operation};
						}
					}

					var selectedDateObject = {};
					selectedDateObject.selectedDate = selectedDate;
					// trigger function after all chages have been made (if the function si defined)
					if (typeof RSFormPro.jQueryCalendar.calendars[formId][calendarName].callbackSelectedDateTime == 'function') {
						var result = RSFormPro.jQueryCalendar.calendars[formId][calendarName].callbackSelectedDateTime(selectedDateObject, this, ct, $i, config.format);
						// reset the current calendar if the callback functions returns a false result
						if (typeof result != 'undefined' && !result) {
							hiddenDate.val('');
							RSFormPro.jQueryCalendar.calendars[formId][calendarName].currentDate = '';
							RSFormPro.jQueryCalendar.calendars[formId][calendarName].calendar.datetimepicker('reset');
							if (this.getOptions('mask')) {
								this.setOptions({mask:this.getOptions('mask')});
							}

							if (config.timepicker) {
								var checkTimePicker = this.getOptions('timepicker');
								if (checkTimePicker) {
									this.setOptions({timepicker: false});
								}
							}
							return false;
						}
					}

					hiddenDate.val(selectedDateObject.selectedDate);
					RSFormPro.jQueryCalendar.calendars[formId][calendarName].currentDate = selectedDateObject.selectedDate;
					
					// lose focus on selection
					jQuery(':focus').blur();
				},

				onShow: function() {
					var index = RSFormPro.jQueryCalendar.shownCalendars.indexOf(calendarId);
					if (index  < 0) {
						RSFormPro.jQueryCalendar.shownCalendars.push(calendarId);
					}
				},

				onClose: function() {
					var index = RSFormPro.jQueryCalendar.shownCalendars.indexOf(calendarId);
					if (index  > -1) {
						RSFormPro.jQueryCalendar.shownCalendars.splice(index, 1);
					}
				},

				onCreate: function() {
					if (startDate) {
						// if on load the specificTimeLogic is active
						if (RSFormPro.jQueryCalendar.calendars[formId][calendarName].useSpecificTimeLogic) {
							RSFormPro.jQueryCalendar.specificTimeLogic(this, startDate, RSFormPro.jQueryCalendar.calendars[formId][calendarName].useSpecificTimeLogic);
						}
						// if on load the timeLogic is active
						if (RSFormPro.jQueryCalendar.calendars[formId][calendarName].useTimeLogic) {
							var inputParams = {formId: formId, calendarName: calendarName, config: config};
							RSFormPro.jQueryCalendar.timeLogic(this, startDate, inputParams, RSFormPro.jQueryCalendar.calendars[formId][calendarName].useTimeLogic);
						}
					} else {
						this.setOptions({timepicker: false})
					}

					// hold the calendar instance so that users cand work with it
					RSFormPro.jQueryCalendar.calendars[formId][calendarName].calendarInstance =  this;
					
					// try and focus out other focused fields if anything is pressed inside the calendar, only when inline
					if (this.getOptions('inline'))
					{
						var currentCalendar = jQuery(this);

						currentCalendar.click(function (e) {
							if (jQuery(e.target).parents(".xdsoft_datetimepicker").length) {
								jQuery(':focus').blur();
							}
						});
					}
				}
			});
		}
	},

	// recontruct the timeLine form the datetimepicker.js
	generateTimeLine: function(selected, minTime, maxTime, step) {
		var now = new Date(selected.getTime());
		var dateFormat = now.dateFormat('MM/DD/YYYY');
		var i, j;

		var minTimeDate = RSFormPro.jQueryCalendar.stringToDate(dateFormat, false);
		var maxTimeDate = RSFormPro.jQueryCalendar.stringToDate(dateFormat, false);

		if (minTime) {
			var minTimeParts = minTime.split(':');
			minTimeDate.setHours(minTimeParts[0]);
			minTimeDate.setMinutes(minTimeParts[1]);
			minTimeDate.setSeconds(0);
		}

		if (maxTime) {
			var maxTimeParts = maxTime.split(':');
			maxTimeDate.setHours(maxTimeParts[0]);
			maxTimeDate.setMinutes(maxTimeParts[1]);
			maxTimeDate.setSeconds(0);
		}

		var timeLine = [];
		var newNow = now;
		var h, m;

		for (i = 0, j = 0; i < 24; i += 1) {
			for (j = 0; j < 60; j += step) {
				h = (i < 10 ? '0' : '') + i;
				m = (j < 10 ? '0' : '') + j;

				newNow.setHours(h);
				h = parseInt(newNow.getHours(), 10);
				newNow.setMinutes(m);
				m = parseInt(newNow.getMinutes(), 10);

				if (maxTime !== false && maxTimeDate.getTime() < newNow.getTime()) {
					continue;
				} else if (minTime !== false && minTimeDate.getTime() > newNow.getTime()) {
					continue;
				}

				timeLine.push(newNow.dateFormat('HH:mm'));
			}
		}

		return timeLine;
	},

	checkSelected: function(calendar, selectedDate, minTime, maxTime, step, timeLine) {
		var conditionMinDate = new Date.parseDate(selectedDate, 'MM/DD/YYYY HH:mm');
		var conditionMaxDate = new Date.parseDate(selectedDate, 'MM/DD/YYYY HH:mm');
		selectedDate = new Date.parseDate(selectedDate, 'MM/DD/YYYY HH:mm');

		//set the minTime
		if (minTime) {
			var minTimeParts = minTime.split(':');
			conditionMinDate.setHours(minTimeParts[0]);
			conditionMinDate.setMinutes(minTimeParts[1]);
			conditionMinDate.setSeconds(0);
		}

		// check if the selected date and time is less than the minimum set by the user
		var diffMin = selectedDate.getTime() - conditionMinDate.getTime();

		if (diffMin < 0) {
			return RSFormPro.jQueryCalendar.findInTimeLine(conditionMinDate, timeLine, step, minTime, maxTime); //.dateFormat('MM/DD/YYYY HH:mm');
		}

		//set the maxTime
		if (maxTime) {
			var maxTimeParts = maxTime.split(':');
			conditionMaxDate.setHours(maxTimeParts[0]);
			conditionMaxDate.setMinutes(maxTimeParts[1]);
			conditionMaxDate.setSeconds(0);
		}

		// check if the selected date and time is more than the maximum set by the user
		var diffMax = selectedDate.getTime() - conditionMaxDate.getTime();

		if (diffMax > 0) {
			return '';
		}

		return RSFormPro.jQueryCalendar.findInTimeLine(selectedDate, timeLine, step, minTime, maxTime); //.dateFormat('MM/DD/YYYY HH:mm');
	},

	findInTimeLine: function(newDate, timeLine, step, minTime, maxTime) {
		var dateHours = newDate.getHours();
		var dateMin = newDate.getMinutes();

		var arrayType = '';
		if (dateHours > 9) {
			arrayType += dateHours;
		} else {
			arrayType += '0'+dateHours;
		}
		arrayType +=':';
		if (dateMin > 9) {
			arrayType += dateMin;
		} else {
			arrayType += '0'+dateMin;
		}

		if (timeLine.indexOf(arrayType) > -1) {
			return newDate.dateFormat('MM/DD/YYYY HH:mm');
		}
		else {
			var timeFound = '';
			for(i = 0; i < timeLine.length; i++){
				var lineTime = timeLine[i].split(':');
				var lineH = parseInt(lineTime[0]);
				var lineM = parseInt(lineTime[1]);

				if (lineH == dateHours) {
					var diffMinutes = dateMin - lineM;
					if (diffMinutes < 0) diffMinutes = -diffMinutes;


					if (diffMinutes > step) {
						continue;
					} else {
						// check if the next timeLine entry is closer
						var middleStep = step / 2;

						if (diffMinutes > middleStep) {
							timeFound = timeLine[(i+1)];
						} else {
							timeFound = timeLine[i];
						}
						break;
					}
				}
			}

			// if the currentTime is less than the min of the timeline or higher than the max of the timeLine
			if (timeFound == '' && timeLine.length > 0) {
				var referenceMin = timeLine[0].replace(':', '');
				var referenceMax = timeLine[(timeLine.length - 1)].replace(':', '');
				var currentTime = dateHours.toString() + dateMin.toString();
				currentTime = parseInt(currentTime);

				if (currentTime < parseInt(referenceMin)) {
					timeFound = timeLine[0];
				} else if(currentTime > parseInt(referenceMax)) {
					timeFound = timeLine[(timeLine.length - 1)];
				}

			}
			if (timeFound != '') {
				timeFound = timeFound.split(':');
				newDate.setHours(timeFound[0]);
				newDate.setMinutes(timeFound[1]);
				newDate.setSeconds(0);

				return newDate.dateFormat('MM/DD/YYYY HH:mm');
			}
		}

		return newDate.dateFormat('MM/DD/YYYY HH:mm');
	},

	specificTimeLogic : function(element, currentDateTime, params) {
		if (params.minSpecificTime !='') {
			var refferenceDate = RSFormPro.jQueryCalendar.stringToDate(params.minDate, false);
			if (refferenceDate.getFullYear() == currentDateTime.getFullYear() && refferenceDate.getMonth() == currentDateTime.getMonth() && refferenceDate.getDate() == currentDateTime.getDate()) {
				element.setOptions({minTime:params.minSpecificTime});
			} else {
				element.setOptions({minTime:params.defaultMinTime});
			}
		}

		if (params.maxSpecificTime !='') {
			var refferenceDate = RSFormPro.jQueryCalendar.stringToDate(params.maxDate, false);
			if (refferenceDate.getFullYear() == currentDateTime.getFullYear() && refferenceDate.getMonth() == currentDateTime.getMonth() && refferenceDate.getDate() == currentDateTime.getDate()) {
				element.setOptions({maxTime:params.maxSpecificTime})
			} else {
				element.setOptions({maxTime:params.defaultMaxTime});
			}
		}
	},

	timeLogic: function(element, currentDateTime, inputParams, params) {
		var conditionDate = new Date.parseDate((params.date), 'MM/DD/YYYY');
		if (currentDateTime.getFullYear() == conditionDate.getFullYear() && currentDateTime.getMonth() == conditionDate.getMonth() && currentDateTime.getDate() == conditionDate.getDate()) {
			element.setOptions((params.rule == 'min' ? {minTime: params.time} : {maxTime: params.time}));
			// check the current time if is less then the minTime
			if (params.rule == 'min') {
				var timeParts = params.time.split(':');
				var h = parseInt(timeParts[0]);
				var min = parseInt(timeParts[1]);

				if (currentDateTime.getHours() < h || (currentDateTime.getHours() == h && currentDateTime.getMinutes() < min)) {
					currentDateTime.setHours(h);
					currentDateTime.setMinutes(min);
					currentDateTime.setSeconds(0);

					var outputInputDate = currentDateTime.dateFormat(inputParams.config.format);
					var outputHiddenDate = currentDateTime.dateFormat(inputParams.config.format);

					RSFormPro.jQueryCalendar.calendars[inputParams.formId][inputParams.calendarName].calendar.val(outputInputDate);
					RSFormPro.jQueryCalendar.calendars[inputParams.formId][inputParams.calendarName].hiddenDate.val(outputHiddenDate);
				}
			}
			if (params.rule == 'max') {
				var timeParts = params.time.split(':');
				var h = parseInt(timeParts[0]);
				var min = parseInt(timeParts[1]);

				if (currentDateTime.getHours() > h || (currentDateTime.getHours() == h && currentDateTime.getMinutes() > min)) {
					currentDateTime.setHours(h);
					currentDateTime.setMinutes(min);
					currentDateTime.setSeconds(0);

					var outputInputDate = currentDateTime.dateFormat(inputParams.config.format);
					var outputHiddenDate = currentDateTime.dateFormat(inputParams.config.format);

					RSFormPro.jQueryCalendar.calendars[inputParams.formId][inputParams.calendarName].calendar.val(outputInputDate);
					RSFormPro.jQueryCalendar.calendars[inputParams.formId][inputParams.calendarName].hiddenDate.val(outputHiddenDate);
				}
			}
		} else {
			var minTime = typeof inputParams.config.extra.minTime != 'undefined' ? inputParams.config.extra.minTime : false;
			var maxTime = typeof inputParams.config.extra.maxTime != 'undefined' ? inputParams.config.extra.maxTime : false;
			element.setOptions((params.rule == 'min' ? {minTime: minTime} : {maxTime: maxTime}));
		}
	},

	stringToDate: function(date, withTime) {
		var newDate = false;
		if (typeof date != 'undefined' && date != '') {
			if (!withTime) {
				newDate = new Date.parseDate(date, 'MM/DD/YYYY');
			} else {
				var parts = date.split(' ');
				if (parts.length == 2) {
					newDate = new Date.parseDate(date, 'MM/DD/YYYY HH:mm');
				} else {
					newDate = RSFormPro.jQueryCalendar.stringToDate(date, false);
				}
			}
		}
		return newDate;
	},

	showCalendar: function(calendarId) {
		var index = RSFormPro.jQueryCalendar.shownCalendars.indexOf(('cal'+calendarId));
		if (index > -1) {
			jQuery('#txtjQcal' + calendarId).datetimepicker('hide');
			RSFormPro.jQueryCalendar.shownCalendars.splice(index, 1);
		} else {
			jQuery('#txtjQcal' + calendarId).datetimepicker('show');
		}
	},

	hideAllPopupCalendars: function(formId) {
		if (typeof RSFormPro.jQueryCalendar.calendars[formId] != 'undefined') {
			jQuery.each(RSFormPro.jQueryCalendar.calendars[formId], function () {
				if (!this.calendarInstance.getOptions('inline')) {
					this.calendarInstance.trigger('close.xdsoft');
				}
			});
		}
	},

	shownCalendars: []
};