/**
 * @package         Regular Labs Extension Manager
 * @version         7.4.2
 * 
 * @author          Peter van Westen <info@regularlabs.com>
 * @link            http://www.regularlabs.com
 * @copyright       Copyright © 2019 Regular Labs All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

(function($) {
	if (typeof RegularLabsManager !== 'undefined') {
		return;
	}

	RegularLabsManager = {
		has_pro_installed: false,
		show_key_invalid : true,
		params           : Joomla.getOptions('rl_extensionmanager'),

		refreshData: function(external) {
			RegularLabsManager.hide('loaded, data');
			RegularLabsManager.show('progress');

			$('div#rlem').find('tr').removeClass();
			$('div#rlem').find('td.ext_new').removeClass('disabled');

			var url = 'index.php?option=com_regularlabsmanager&task=update&' + new Date().getTime();

			$.getJSON(url, function(data) {
				$.each(data, function(key, val) {
					RegularLabsManager.setDataByExtension(key, val);
				});

				if (external) {
					RegularLabsManager.hide('progress', $('.ext_types'));
					RegularLabsManager.show('loaded, no_external', $('.ext_types'));
					RegularLabsManager.refreshExternalData();

					return;
				}

				RegularLabsManager.hide('progress');
				RegularLabsManager.show('loaded, no_external');
			}).fail(function() {
				alert(Joomla.JText._('RLEM_ALERT_NO_DATA'));

				RegularLabsManager.hide('progress');
				RegularLabsManager.show('loaded, no_external');
			});
		},

		refreshExternalData: function() {
			var url = 'download.regularlabs.com/extensions.json?j=3';
			if (this.params.updatesource == 'dev') {
				url += '&dev=1';
			}
			if (this.params.key) {
				url += '&k=' + this.params.key;
			}

			RegularLabsScripts.loadajax(url, 'RegularLabsManager.setExternalData(data)', 'RegularLabsManager.setExternalData(0)', '', Joomla.JText._('RLEM_TIMEOUT'), 'json');
		},

		setExternalData: function(data) {
			if (!(data)) {
				alert(Joomla.JText._('RLEM_ALERT_FAIL'));

				RegularLabsManager.hide('progress');
				RegularLabsManager.show('loaded, no_external');

				return;
			}

			var toolbar = $('div#toolbar');

			// reset stuff
			toolbar.removeClass('has_install').removeClass('has_reinstall').removeClass('has_update');

			$.each(data, function(key, val) {
				RegularLabsManager.setExternalDataByExtension(key, val);
			});

			if (this.has_pro_installed && this.show_key_invalid) {
				$('#rl_key_text_empty').hide();
				$('#rl_key_text_invalid').show();
				$('#rl_key .well').addClass('well-danger');
				$('#rl_key').show();
			}

			RegularLabsManager.hide('progress');
			RegularLabsManager.show('loaded');

			RegularLabsManager.updateCheckboxes();
		},

		setDataByExtension: function(extension, data) {
			if (!this.params.ids.indexOf(extension) < 0) {
				return;
			}

			var tr = $('tr#row_' + extension);

			if (!tr) {
				return;
			}

			RegularLabsManager.show('pro_not_installed', tr);

			if (!data || typeof data['version'] === 'undefined' || !data['version']) {
				tr.find('span.current_version').text('');
				RegularLabsManager.hide('installed', tr);
				RegularLabsManager.show('not_installed', tr);

				return;
			}

			tr.find('span.current_version').text(data['version']);

			if (data['pro'] == 1) {
				RegularLabsManager.hide('pro_not_installed', tr)
				RegularLabsManager.show('pro_installed', tr);
				RegularLabsManager.show('uptodate', tr);
			} else {
				RegularLabsManager.show('free_installed', tr);
				RegularLabsManager.show('uptodate', tr);
			}

			if (data['missing'].length) {
				tr.find('.missing span').attr('data-content', data['missing']);
				RegularLabsManager.show('has_missing', tr);
			}

			RegularLabsManager.hide('not_installed', tr);
			RegularLabsManager.show('installed', tr);
		},

		setExternalDataByExtension: function(extension, data) {
			if (!this.params.ids.indexOf(extension) < 0) {
				return;
			}

			var div     = $('div#rlem');
			var toolbar = $('div#toolbar');
			var tr      = div.find('tr#row_' + extension);

			if (!tr) {
				return;
			}

			// reset stuff
			tr.find('td.ext_new').removeClass('disabled');
			tr.find('.changelog, .changelog > span').removeClass('disabled');

			if (!data) {
				return;
			}

			// Changelog
			if (typeof data['changelog'] !== 'undefined' && data['changelog'] != '') {
				changelog = data['changelog'].replace('font-size:1.2em;', '');
				tr.find('.changelog a').attr('data-content', changelog);
				RegularLabsManager.show('changelog', tr);
			}

			// Install buttons
			if (typeof data['version'] === 'undefined' || data['version'] == '') {
				return;
			}

			var v_new = String(data['version']).trim();

			if (!v_new || v_new == '0') {
				// no new version fond: show refresh button
				RegularLabsManager.show('no_external', tr);

				return;
			}

			var version_current = String(tr.find('.current_version').first().text()).trim();

			var pro_installed = tr.hasClass('pro_installed');
			if (pro_installed) {
				this.has_pro_installed = true;
			}

			var pro_access = (data['pro'] == 1);
			if (pro_access) {
				RegularLabsManager.show('pro_access', tr);
				$('#url_' + extension).val(data['downloadurl_pro']);
				this.show_key_invalid = false;
			} else {
				RegularLabsManager.show('pro_no_access', tr);
				$('#url_' + extension).val(data['downloadurl']);
			}

			var pro_available = (data['has_pro'] == 1);
			if (pro_available) {
				RegularLabsManager.show('pro_available', tr);
			} else {
				RegularLabsManager.show('pro_not_available', tr);
			}

			tr.find('.new_version').text(v_new);
			RegularLabsManager.show('changelog', tr);
			RegularLabsManager.hide('uptodate', tr);

			// No current version found
			if (!version_current || version_current == '0') {
				toolbar.addClass('has_install');
				RegularLabsManager.show('selectable', tr);
				RegularLabsManager.show('install', tr);

				return;
			}

			var compare = RegularLabsScripts.compareVersions(version_current, v_new);
			var is_dev  = version_current.indexOf('-dev') > -1;

			if (is_dev) {
				RegularLabsManager.show('install_stable', tr);
			}

			// Current version is newer (dev version)
			if (compare == '>') {
				RegularLabsManager.show('downgrade', tr);
				RegularLabsManager.show('ext_install .downgrade .pro_access', tr);
				tr.find('.changelog, .changelog > span').addClass('disabled');
				if (pro_installed && pro_available && !pro_access) {
					RegularLabsManager.hide('pro_no_access', tr);
					RegularLabsManager.hide('ext_installed .pro_no_access', tr);
					RegularLabsManager.hide('ext_install .downgrade .pro_access', tr);

					RegularLabsManager.show('ext_install .downgrade .pro_no_access', tr);
				}
				return;
			}

			// Pro installed, but no access (Download Key invalid)
			if (pro_installed && pro_available && !pro_access) {
				if ($('#key_hidden').val()) {
					RegularLabsManager.show('pro_key_invalid', tr);
				}

				return;
			}

			// Current version is older or free installed and access to pro
			if (compare == '<' || (!pro_installed && pro_access)) {
				toolbar.addClass('has_update');
				RegularLabsManager.show('selectable', tr);
				RegularLabsManager.show('update', tr);

				return;
			}

			// Extension has missing parts
			if (tr.hasClass('has_missing')) {
				toolbar.addClass('has_install');
				RegularLabsManager.show('selectable', tr);

				return;
			}

			// All is uptodate
			tr.find('.changelog, .changelog > span').addClass('disabled');
			if (!pro_installed || pro_access) {
				RegularLabsManager.show('uptodate', tr);
				if (!is_dev) {
					RegularLabsManager.show('reinstall', tr);
					toolbar.addClass('has_reinstall');
				}
			}
		},

		updateCheckboxes: function() {
			var div = $('div#rlem');

			// hide select boxes
			RegularLabsManager.hide('select');

			// reset hidden checkboxes
			div.find('table tr.not_installed').each(function(i, tr) {
				if (tr.hasClass('xselectable')) {
					tr.addClass('selectable').removeClass('xselectable');
				}
			});

			// make hidden rows unselectable
			div.find('table.hide_not_installed tr.not_installed').each(function(i, tr) {
				if (tr.hasClass('selectable')) {
					tr.addClass('xselectable').removeClass('selectable');
				}
			});

			// show select boxes of selectable rows
			RegularLabsManager.show('selectable .select');
		},

		install: function(task, id) {
			var url = $('#url_' + id).val();
			if (task == 'install_stable') {
				url = url.replace(/&(amp;)?dev=1/, '');
			}
			RegularLabsManager.openModal(task, [id], [url]);
		},

		installMultiple: function(task) {
			var ids  = [];
			var urls = [];

			switch (task) {
				case 'reinstallall':
					type = 'reinstall';
					msg  = Joomla.JText._('RLEM_ALERT_NO_ITEMS_TO_UPDATE');
					clss = 'reinstall';
					break;
				case 'updateall':
					type = 'update';
					msg  = Joomla.JText._('RLEM_ALERT_NO_ITEMS_TO_UPDATE');
					clss = 'selectable.update';
					break;
				default:
					type = 'install';
					msg  = Joomla.JText._('RLEM_ALERT_NO_ITEMS_SELECTED');
					clss = 'selectable';
					break;
			}

			$('div#rlem tr.' + clss).each(function() {
				var tr = $(this);
				var el = tr.find('td.ext_checkbox input');
				var id = el.val();
				if (id) {
					var url = $('#url_' + id).val();

					var pass = 0;
					switch (task) {
						case 'reinstallall':
							pass = url.indexOf('dev=1') < 0;
							break;

						case 'updateall':
							pass = true;
							break;

						default:
							pass = el.is(':checked');
							break;
					}

					if (pass) {
						ids.push(id);
						urls.push(url);
					}
				}
			});

			if (!ids.length) {
				alert(msg);
			} else {
				RegularLabsManager.openModal(type, ids, urls);
			}
		},

		openModal: function(task, ids, urls) {
			var url_ids = [];

			for (var i = 0; i < ids.length; i++) {
				url_ids.push('ids[]=' + escape(ids[i]));
			}

			url_ids = url_ids.join('&');

			var url_urls = [];

			for (var j = 0; j < urls.length; j++) {
				url = urls[j].replace('http://', '');
				url_urls.push('urls[]=' + escape(url));
			}

			url_urls = url_urls.join('&');

			var url    = 'index.php?option=com_regularlabsmanager&view=process&tmpl=component&task=' + task + '&' + url_ids + '&' + url_urls;
			var height = 78 + (ids.length * 37);

			SqueezeBox.open(url, {handler: 'iframe', size: {x: 480, y: height}, classWindow: 'rlem_modal'});
		},

		show: function(classes, parent) {
			this.toggle(classes, parent, 1);
		},

		hide: function(classes, parent) {
			this.toggle(classes, parent, 0);
		},

		toggle: function(classes, parent, show) {
			var classes = classes.split(',');
			$(classes).each(function(i, el) {
				c = classes[i].trim();
				if (!parent) {
					parent = $('div#rlem');
				} else {
					if (c != 'progress' && c != 'loaded') {
						if (show) {
							parent.addClass(c);
						} else {
							parent.removeClass(c);
						}
					}
				}
				if (show) {
					parent.find('.' + c).removeClass('hide');
				} else {
					parent.find('.' + c).addClass('hide');
				}
			});
		}
	}
})(jQuery);

function rlem_function(task, id) {
	if (!task) {
		return;
	}

	switch (task) {
		case 'refresh':
			RegularLabsManager.refreshData(1);
			break;
		case 'reinstallall':
		case 'updateall':
		case 'installselected':
			RegularLabsManager.installMultiple(task);
			break;
		case 'install':
		case 'update':
		case 'reinstall':
		case 'install_stable':
		case 'downgrade':
		case 'uninstall':
			RegularLabsManager.install(task, id);
			break;
	}
}
