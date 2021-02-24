/* rsform pro */
// For convenience...
Date.prototype.rsfp_format = function (mask) {
	return RSFormPro.YUICalendar.dateFormat(this, mask);
}

// if the browser has not defined the Object.keys function 
if (!Object.keys) Object.keys = function(o) {
  if (o !== Object(o))
    throw new TypeError('Object.keys called on a non-object');
  var k=[],p;
  for (p in o) if (Object.prototype.hasOwnProperty.call(o,p)) k.push(p);
  return k;
}

if (typeof RSFormPro != 'object') {
	var RSFormPro = {};
}

RSFormPro.YUICalendar = {
	settings: {},
	calendars:  {},
	calendarsData: {},
	setCalendar: function(formId, idCalendar, config) {
		if (typeof RSFormPro.YUICalendar.calendarsData[formId] == 'undefined') {
			RSFormPro.YUICalendar.calendarsData[formId] = {};
		} 
		if (typeof RSFormPro.YUICalendar.calendarsData[formId][idCalendar] == 'undefined') {
			RSFormPro.YUICalendar.calendarsData[formId][idCalendar] = {};
		}
			
		RSFormPro.YUICalendar.calendarsData[formId][idCalendar].config = config;
	},
	
	renderCalendars: function() {
		var countForms = Object.keys(RSFormPro.YUICalendar.calendarsData).length;
		var forms = Object.keys(RSFormPro.YUICalendar.calendarsData);
		
		if (countForms > 0) {
			for (var i = 0; i < countForms; i++) {
				var formId = forms[i];
				var calendarsIds = Object.keys(RSFormPro.YUICalendar.calendarsData[formId]);
				
				if (typeof RSFormPro.YUICalendar.calendars[formId] == 'undefined') {
					RSFormPro.YUICalendar.calendars[formId] = {};
				}
				for (var j = 0; j < calendarsIds.length; j++) {
					RSFormPro.YUICalendar.initCalendar(formId, calendarsIds[j], RSFormPro.YUICalendar.calendarsData[formId][calendarsIds[j]].config);
				}
			}
		}
	},
	// need it for the rules to individual load the calendar
	initCalendar: function(formId, idCalendar, config) {
		if (typeof RSFormPro.YUICalendar.calendars[formId] == 'undefined') {
			RSFormPro.YUICalendar.calendars[formId] = {};
		}
		
		var calendarId 	 = 'cal'+idCalendar;
		var txtDate 	 = document.getElementById('txt' + calendarId);
		var hiddenDate 	 = document.getElementById('hidden' + calendarId);
		
		if (!txtDate)
		{
			return;
		}
		
		var calendarName = txtDate.name.substring(5, txtDate.name.length - 1);
		
		if (typeof RSFormPro.YUICalendar.calendars[formId][calendarName] == 'undefined') {
			RSFormPro.YUICalendar.calendars[formId][calendarName] = new rsf_CALENDAR.widget.Calendar(calendarId, calendarId + 'Container');
			// set the specific configuration
			RSFormPro.YUICalendar.setConfig(formId, RSFormPro.YUICalendar.calendars[formId][calendarName], calendarId, config);
			
			// render the calendar
			RSFormPro.YUICalendar.render(RSFormPro.YUICalendar.calendars[formId][calendarName], config.value);
		}
	},
	
	render: function(calendar, value) {
		if (value != '') {
			calendar.select(value);
			var parts = value.split('/');
			if (parts.length == 3) {
				var m = parseInt(parts[0]);
				var y = parseInt(parts[2]);
				calendar.cfg.setProperty('pagedate', m + '/' + y);
			}
		}
		calendar.render();
	},
	
	setConfig: function(formId, calendar, idCalendar, config) {		
		// set the id
		calendar.myid = idCalendar;
		
		// set the date format
		calendar.myFormat 	  = config.format;
		
		// set navigator
		calendar.cfg.setProperty('navigator', typeof RSFormPro.YUICalendar.settings.navConfig == 'undefined' ? true : RSFormPro.YUICalendar.settings.navConfig);
		
		// set language strings
		calendar.cfg.setProperty("MONTHS_SHORT", RSFormPro.YUICalendar.settings.MONTHS_SHORT);
		calendar.cfg.setProperty("MONTHS_LONG", RSFormPro.YUICalendar.settings.MONTHS_LONG);
		calendar.cfg.setProperty("WEEKDAYS_1CHAR", RSFormPro.YUICalendar.settings.WEEKDAYS_1CHAR);
		calendar.cfg.setProperty("WEEKDAYS_SHORT", RSFormPro.YUICalendar.settings.WEEKDAYS_SHORT);
		calendar.cfg.setProperty("WEEKDAYS_MEDIUM", RSFormPro.YUICalendar.settings.WEEKDAYS_MEDIUM);
		calendar.cfg.setProperty("WEEKDAYS_LONG", RSFormPro.YUICalendar.settings.WEEKDAYS_LONG);
		calendar.cfg.setProperty("START_WEEKDAY", RSFormPro.YUICalendar.settings.START_WEEKDAY);
		
		calendar.selectEvent.subscribe(RSFormPro.YUICalendar.handleText, calendar, true);
		if (config.layout == 'POPUP') {
			calendar.selectEvent.subscribe(RSFormPro.YUICalendar.handleClose, calendar, true);
		}
		calendar.resetEvent.subscribe(RSFormPro.YUICalendar.handleReset, calendar, true);
		
		// set the extras
		RSFormPro.YUICalendar.setExtras(formId, calendar, config.extra);
	},
	
	setExtras: function (formId, calendar, extra) {
		for (var extraType in extra) {
			if (!extra.hasOwnProperty(extraType))
			{
				continue;
			}
			if (extraType === 'rule') {
				var rule 				= extra.rule.split('|');
				var operation 			= rule[0];
				var otherCalendarName   = rule[1];
				var offset				= 1;
				var i;

				if (typeof rule[2] !== 'undefined')
				{
					offset = parseInt(rule[2]);
				}
				
				if (typeof RSFormPro.YUICalendar.calendars[formId][otherCalendarName] === 'undefined') {
					var otherCalendarInput = document.getElementsByName("form["+otherCalendarName+"]");
					
					var otherCalendarFound = false;
					// get the proper field
					for (i = 0 ; i < otherCalendarInput.length; i++) {
						var otherCalendarId = otherCalendarInput[i].id;
						if (otherCalendarId.indexOf('txtcal'+formId+'_') === 0)
						{
							otherCalendarId = otherCalendarId.substring(6, otherCalendarId.length);
							otherCalendarFound = true;
							break;
						}
					}
					
					if (otherCalendarFound)
					{
						RSFormPro.YUICalendar.initCalendar(formId,otherCalendarId, RSFormPro.YUICalendar.calendarsData[formId][otherCalendarId].config);
					}
				}
				
				// the other calendar object initated
				var otherCalendar = RSFormPro.YUICalendar.calendars[formId][otherCalendarName];
				
				if (['min', 'max'].indexOf(operation) > -1) {
					calendar.rule = function(theDate) {
						var offsetDate;
						if (operation === 'min')
						{
							offsetDate = theDate.getDate() + offset;
						}
						else
						{
							offsetDate = theDate.getDate() - offset;
						}

						var newDate = new Date(theDate.getFullYear(), theDate.getMonth(), offsetDate);
						if (otherCalendar) {
							if (operation === 'min') {
								otherCalendar.cfg.setProperty('mindate', newDate.rsfp_format('mm/dd/yyyy'));
								otherCalendar.cfg.setProperty('pagedate', newDate.rsfp_format('mm/yyyy'));
							} else {
								otherCalendar.cfg.setProperty('maxdate', newDate.rsfp_format('mm/dd/yyyy'));
							}
							// make sure current selection is still valid, otherwise empty it
							var hiddenDate = document.getElementById('hidden' + otherCalendar.myid);
							var txtDate    = document.getElementById('txt' + otherCalendar.myid);
							
							if (hiddenDate.value.length > 0) {
								var parts = hiddenDate.value.split('/');
								if (parts.length == 3) {
									var d = parseInt(parts[1]);
									var m = parseInt(parts[0]);
									var y = parseInt(parts[2]);
									
									var currentDate = new Date(y, m-1, d);
								
									if ((operation === 'min' && currentDate.getTime() < newDate.getTime()) || (operation === 'max' && currentDate.getTime() > newDate.getTime())) {
										hiddenDate.value = '';
										txtDate.value 	 = '';
									}
								}
							}
							// render the other calendar;
							otherCalendar.render();
						}
					}
				}
				continue;
			}
			
			if (extraType == 'mindate') {
				var currentDate = calendar.today;
				var minDate = new Date(extra[extraType]);
				
				if (currentDate.getTime() < minDate.getTime()) {
					calendar.cfg.setProperty('today', minDate);
				}
			}
			
			if (extraType == 'maxdate') {
				var currentDate = calendar.today;
				var maxDate = new Date(extra[extraType]);
				
				if (currentDate.getTime() > maxDate.getTime()) {
					calendar.cfg.setProperty('today', maxDate);
				}
			}
			
			calendar.cfg.setProperty(extraType, extra[extraType]);
		}
	},
	
	handleText : function(type, args, calendar) {
		var dates = args[0];
		var date = dates[0];
		var year = date[0], month = date[1], day = date[2];

		if(day <= 9) day = '0' + day;
		if(month <= 9) month = '0' + month;

		var myDate = new Date();
		// Bugfix for Joomla! Calendar
		if (typeof myDate.__msh_oldSetFullYear === 'function') {
			myDate.__msh_oldSetFullYear(year, month-1, day);
		} else {
			myDate.setFullYear(year, month-1, day);
		}
		
		if (typeof rsfp_onSelectDate === 'function') {
			var result = rsfp_onSelectDate(myDate.rsfp_format(calendar.myFormat), type, args, calendar);
			if (!result)
				return false;
		}
		
		var txtDate = document.getElementById("txt" + calendar.myid);
		txtDate.value = myDate.rsfp_format(calendar.myFormat);
		
		var hiddenDate = document.getElementById("hidden" + calendar.myid);
		hiddenDate.value = myDate.rsfp_format('mm/dd/yyyy');
		
		if (typeof calendar.rule === 'function') {
			calendar.rule(myDate);
		}
	},

	handleReset: function(type, args, calendar) {
		var hiddenDate = document.getElementById("hidden" + calendar.myid);
		var originalDate = hiddenDate.getAttribute('data-rsfp-original-date')

		if (originalDate) {
			var myDate = new Date(originalDate);

			calendar.select(myDate);

			if (typeof calendar.rule === 'function') {
				calendar.rule(myDate);
			}
		} else {
			calendar.deselectAll();
		}
	},

	handleClose: function (type, args, calendar) {
		calendar.hide();
	},
	
	showHideCalendar: function(calContainerId){
		var cal = document.getElementById(calContainerId);
		if (cal.style.display == 'none') {
			cal.style.display = '';
		} else  {
			cal.style.display = 'none';
		}
	},
	
	hideAllPopupCalendars: function(formId, calendarsIds) {
		if (typeof RSFormPro.YUICalendar.calendars[formId] !== 'undefined') {
			for (var i = 0; i < calendarsIds.length; i++){
				var calId = 'cal'+calendarsIds[i]+'Container';
				var cal = document.getElementById(calId);
				if (cal.style.display != 'none' && cal.style.position == 'absolute') {
					cal.style.display = 'none';
				}
			}
		}
	},

	hideOnClick: function(formId, calendarsIds) {
		RSFormProUtils.addEvent(window, 'DOMContentLoaded', function() {
			if ('body' in document) {
				RSFormProUtils.addEvent(document.body, 'click', function(event) {
					if (!event || !event.target) {
						return true;
					}

					var tagName = event.target.tagName || event.target.nodeName;

					if (tagName === 'INPUT' || tagName === 'BUTTON') {
						if (event.target.id) {
							if (event.target.id.indexOf('txtcal' + formId + '_') === 0 || event.target.id.indexOf('btn' + formId + '_') === 0) {
								return true;
							}
						}
					}

					var parents = RSFormProUtils.getParents(event.target);

					if (parents.length) {
						for (var i = 0; i < parents.length; i++) {
							if (RSFormProUtils.hasClass(parents[i], 'yui-calcontainer')) {
								return true;
							}
						}
					}

					RSFormPro.YUICalendar.hideAllPopupCalendars(formId, calendarsIds);
				});
			}
		});
	},
	
	dateFormat: function () {
		var	token        = /d{1,4}|m{1,4}|yy(?:yy)?|([HhMsTt])\1?|[LloZ]|"[^"]*"|'[^']*'/g,
			timezone     = /\b(?:[PMCEA][SDP]T|(?:Pacific|Mountain|Central|Eastern|Atlantic) (?:Standard|Daylight|Prevailing) Time|(?:GMT|UTC)(?:[-+]\d{4})?)\b/g,
			timezoneClip = /[^-+\dA-Z]/g,
			pad = function (value, length) {
				value = String(value);
				length = parseInt(length) || 2;
				while (value.length < length)
					value = "0" + value;
				return value;
			};

		// Regexes and supporting functions are cached through closure
		return function (date, mask) {
			// Treat the first argument as a mask if it doesn't contain any numbers
			if (
				arguments.length == 1 &&
				(typeof date == "string" || date instanceof String) &&
				!/\d/.test(date)
			) {
				mask = date;
				date = undefined;
			}

			date = date ? new Date(date) : new Date();
			if (isNaN(date))
				throw "invalid date";
			
			mask   = String(RSFormPro.YUICalendar.masks[mask] || mask || RSFormPro.YUICalendar.masks["default"]);

			var	d = date.getDate(),
				D = date.getDay(),
				m = date.getMonth(),
				y = date.getFullYear(),
				H = date.getHours(),
				M = date.getMinutes(),
				s = date.getSeconds(),
				L = date.getMilliseconds(),
				o = date.getTimezoneOffset(),
				flags = {
					d:    d,
					dd:   pad(d),
					ddd:  RSFormPro.YUICalendar.settings.WEEKDAYS_MEDIUM[D],//dF.i18n.dayNames[D],
					dddd: RSFormPro.YUICalendar.settings.WEEKDAYS_LONG[D],//dF.i18n.dayNames[D + 7],
					m:    m + 1,
					mm:   pad(m + 1),
					mmm:  RSFormPro.YUICalendar.settings.MONTHS_SHORT[m],//dF.i18n.monthNames[m],
					mmmm: RSFormPro.YUICalendar.settings.MONTHS_LONG[m],//dF.i18n.monthNames[m + 12],
					yy:   String(y).slice(2),
					yyyy: y,
					h:    H % 12 || 12,
					hh:   pad(H % 12 || 12),
					H:    H,
					HH:   pad(H),
					M:    M,
					MM:   pad(M),
					s:    s,
					ss:   pad(s),
					l:    pad(L, 3),
					L:    pad(L > 99 ? Math.round(L / 10) : L),
					t:    H < 12 ? "a"  : "p",
					tt:   H < 12 ? "am" : "pm",
					T:    H < 12 ? "A"  : "P",
					TT:   H < 12 ? "AM" : "PM",
					Z:    (String(date).match(timezone) || [""]).pop().replace(timezoneClip, ""),
					o:    (o > 0 ? "-" : "+") + pad(Math.floor(Math.abs(o) / 60) * 100 + Math.abs(o) % 60, 4)
				};

			return mask.replace(token, function ($0) {
				return ($0 in flags) ? flags[$0] : $0.slice(1, $0.length - 1);
			});
		};
	}(),
	
	// Some common format strings
	masks:{
		"default":       "yyyy-mm-dd",
		shortDate:       "m/d/yy",
		mediumDate:      "mmm d, yyyy",
		longDate:        "mmmm d, yyyy",
		fullDate:        "dddd, mmmm d, yyyy",
		shortTime:       "h:MM TT",
		mediumTime:      "h:MM:ss TT",
		longTime:        "h:MM:ss TT Z",
		isoDate:         "yyyy-mm-dd",
		isoTime:         "HH:MM:ss",
		isoDateTime:     "yyyy-mm-dd'T'HH:MM:ss",
		isoFullDateTime: "yyyy-mm-dd'T'HH:MM:ss.lo"
	}
};