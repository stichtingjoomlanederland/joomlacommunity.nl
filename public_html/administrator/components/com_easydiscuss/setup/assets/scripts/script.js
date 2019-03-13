
$(document).ready(function(){

	$('.hasTooltip').tooltip();
	
	loading = $('[data-installation-loading]'),
	submit = $('[data-installation-submit]'),
	retry = $('[data-installation-retry]'),
	form = $('[data-installation-form]'),
	completed = $('[data-installation-completed]'),
	source = $('[data-source]'),
	installAddons = $('[data-installation-install-addons]'),
	steps = $('[data-installation-steps]');
});

var ed = {

	init: function() {
	},

	options: {
		"apikey": "<?php echo $input->get('apikey', '');?>",
		"path": null,
		"controller": "install"
	},
	ajaxUrl: "<?php echo JURI::root();?>administrator/index.php?option=com_easydiscuss&ajax=1",
	
	ajax: function(task, properties, callback) {

		var prop = $.extend(ed.options, properties);

		var dfd = $.Deferred();

		$.ajax({
			type: "POST",
			url: ed.ajaxUrl + "&controller=" + prop.controller + "&task=" + task,
			data: prop
		}).done(function(result) {
			callback && callback.apply(this, [result]);

			dfd.resolve(result);
		});

		return dfd;
	},

	addons: {

		installModule: function(element, path) {

			return ed.ajax('installModule', {
				"controller": "addons",
				"path": path,
				"module": element
			});

		},

		installPlugin: function(plugin, path) {
			return ed.ajax('installPlugin', {
				"controller": "addons",
				"path": path,
				"element": plugin.element,
				"group": plugin.group
			});
		},

		runScript: function(script) {
			// Run the maintenace scripts
			return $.ajax({
				type: 'POST',
				url: ed.ajaxUrl + '&controller=maintenance&task=execute',
				data: {
					script: script
				}
			});
		},

		retrieveList: function() {

			var progress = $('[data-addons-progress]');
			var selection = $('[data-addons-container]');
			var syncProgress = $('[data-sync-progress]');

			// Show loading
			loading.removeClass('hide');

			// Hide submit
			submit.addClass('hide');

			ed.ajax('list', {"controller": "addons", "path": ed.options.path}, function(result){

				// Hide the retrieving message
				$('[data-addons-retrieving]').addClass('hide');

				loading.addClass('hide');
				installAddons.removeClass('hide');

				selection.html(result.html);

				// Get files for maintenance
				var scripts = result.scripts;
				var maintenanceMsg = result.maintenanceMsg;

				// Set the submit
				installAddons.on('click', function() {

					// Hide the container
					selection.addClass('hide');

					// Show the installation progress
					progress.removeClass('hide');
					syncProgress.removeClass('hide');

					// Install the selected items
					var modules = [];
					var plugins = [];

					$('[data-checkbox-module]:checked').each(function(i, el) {
						modules.push($(el).val());
					});

					$('[data-checkbox-plugin]:checked').each(function(i, el) {
						var plugin = {
										"element": $(el).val(),
										"group": $(el).data('group')
									};

						plugins.push(plugin);
					});

					var total = modules.length + plugins.length;
					var each = 100 / total;
					var progressBar = $('[data-progress-bar]');
					var progressBarResult = $('[data-progress-bar-result]');

					var totalScripts = scripts.length;
					var eachScript = 100 / totalScripts;
					var syncProgressBar = $('[data-sync-progress-bar]');
					var syncProgressBarResult = $('[data-sync-progress-bar-result]');

					var runMaintenance = function() {

						var frame = $('[data-progress-execscript]');

						frame.addClass('active')
							.removeClass('pending');

						var item = $('<li>');
						item.addClass('text-success').html(maintenanceMsg);

						$('[data-progress-execscript-items]').append(item);

						var scriptIndex = 0,
							dfd = $.Deferred();

						var runNextScript = function() {
							if (scripts[scriptIndex] == undefined) {

								$.ajax({
									type: 'POST',
									url: ed.ajaxUrl + '&controller=maintenance&task=finalize'
								}).done(function(result) {
									var item = $('<li>');
									item.addClass('text-success').html(result.message);
									$('[data-progress-execscript-items]').append(item);

									$('[data-progress-execscript]')
										.find('.progress-state')
										.html(result.stateMessage)
										.addClass('text-success')
										.removeClass('text-info');
								});

								dfd.resolve();
								return;
							}

							ed.addons
								.runScript(scripts[scriptIndex])
								.done(function(data) {
									scriptIndex++;

									// update the progress bar here
									var currentWidth = parseInt(syncProgressBar[0].style.width);
									var percentage = Math.round(currentWidth + eachScript);
									
									syncProgressBar.css('width', percentage + '%');
									syncProgressBarResult.html(percentage + '%');

									var item = $('<li>'),
										className = data.state ? 'text-success' : 'text-error';

									item.addClass(className).html(data.message);

									$('[data-progress-execscript-items]').append(item);

									runNextScript();
								});

						};

						runNextScript();

						return dfd;
					};

					var installModules = function() {

						var moduleIndex = 0,
							dfd = $.Deferred();

						var installNextModule = function() {
							if (modules[moduleIndex] == undefined) {

								dfd.resolve();
								return;
							}

							ed.addons
								.installModule(modules[moduleIndex], result.modulePath)
								.done(function(data) {
									moduleIndex++;

									var currentWidth = parseInt(progressBar[0].style.width);
									var percentage = Math.round(currentWidth + each);

									$('[data-progress-active-message]').html(data.message);

									progressBar.css('width', percentage + '%');
									progressBarResult.html(percentage + '%');

									installNextModule();
								});
						};

						installNextModule();

						return dfd;
					};

					var installPlugins = function() {

						var pluginIndex = 0;
						var dfd = $.Deferred();


						var installNextPlugin = function() {

							if (plugins[pluginIndex] == undefined) {

								dfd.resolve();
								return;
							}

							ed.addons.installPlugin(plugins[pluginIndex], result.pluginPath)
								.done(function(data) {

									pluginIndex++;

									var progressBarResult = $('[data-progress-bar-result]');
									var currentWidth = parseInt(progressBar[0].style.width);
									var percentage = Math.round(currentWidth + each) + '%';

									$('[data-progress-active-message]').html(data.message);
										
									// Update the width of the progress bar
									progressBar.css('width', percentage);

									// We need to update the progress bar here
									progressBarResult.html(percentage);

									installNextPlugin();
								});
						};

						installNextPlugin();

						return dfd;
					};

					// Show loading indicator
					loading.removeClass('hide');
					installAddons.addClass('hide');

					// Install Modules
					installModules().done(function() {
						installPlugins().done(function() {
							
							// Show complete
							$('[data-progress-active-message]').addClass('hide');
							$('[data-progress-complete-message]').removeClass('hide');
							$('[data-progress-bar]').css('width', '100%');
							$('[data-progress-bar-result]').html('100%');

							runMaintenance().done(function() {

								// When everything is done, update the submit button
								loading.addClass('hide');
								submit.removeClass('hide');

								$('[data-sync-progress-active-message]').addClass('hide');
								$('[data-sync-progress-complete-message]').removeClass('hide');
								$('[data-sync-progress-bar]').css('width', '100%');
								$('[data-sync-progress-bar-result]').html('100%');

								submit.on('click', function() {
									form.submit();
								});
							})
						});
					});
				});
			});
		}
	},

	installation: {
		path: null,

		showRetry: function(step) {

			steps.addClass('error');

			retry
				.data('retry-step', step)
				.removeClass('hide');

			// Hide the submit
			submit.addClass('hide');

			// Hide the loading
			loading.addClass('hide');
		},

		extract: function() {

			ed.installation.setActive('data-progress-extract');
			
			ed.ajax('extract', {}, function(result) {

				// Update the progress
				ed.installation.update('data-progress-extract', result, '10%');

				if (!result.state) {
					ed.installation.showRetry('extract');
					return false;
				}

				// Set the path
				ed.options.path = result.path;

				// Run the next command
				ed.installation.runSQL();
			});
		},

		download: function() {

			ed.installation.setActive('data-progress-download');

			ed.ajax('download', {}, function(result) {

				// Set the progress
				ed.installation.update('data-progress-download', result, '10%');

				if (!result.state) {
					ed.installation.showRetry('download');
					return false;
				}

				// Set the installation path
				ed.options.path = result.path;

				ed.installation.runSQL();
			});
		},

		runSQL: function() {
			
			// Install the SQL stuffs
			ed.installation.setActive('data-progress-sql');

			ed.ajax('sql', {}, function(result) {

				// Update the progress
				ed.installation.update('data-progress-sql', result, '15%');

				if (!result.state) {
					ed.installation.showRetry('runSQL');
					return false;
				}

				// Run the next command
				ed.installation.installAdmin();
			});
		},

		installAdmin: function() {

			// Install the admin stuffs
			ed.installation.setActive('data-progress-admin');

			// Run the ajax calls now
			ed.ajax('copy', {"type": "admin"}, function(result) {
				
				// Update the progress
				ed.installation.update('data-progress-admin', result, '20%');

				if (!result.state) {
					ed.installation.showRetry('installAdmin');
					return false;
				}

				ed.installation.installSite();
			});
		},

		installSite : function() {
			
			// Install the admin stuffs
			ed.installation.setActive('data-progress-site');

			ed.ajax('copy', { "type" : "site" }, function(result) {


				// Update the progress
				ed.installation.update('data-progress-site', result, '25%');

				if (!result.state) {
					ed.installation.showRetry('installSite');
					return false;
				}

				ed.installation.installLanguages();
			});
		},

		installLanguages : function() {
			// Install the admin stuffs
			ed.installation.setActive('data-progress-languages');

			ed.ajax('copy', {"type": "languages"}, function(result) {
				
				// Set the progress
				ed.installation.update('data-progress-languages', result, '30%');

				if (!result.state) {
					ed.installation.showRetry('installLanguages');
					return false;
				}

				ed.installation.installMedia();
			});

		},
		
		installMedia : function() {
			
			// Install the admin stuffs
			ed.installation.setActive('data-progress-media');

			ed.ajax('copy', {"type": "media"}, function(result) {
				// Set the progress
				ed.installation.update('data-progress-media', result, '35%');

				if (!result.state) {
					ed.installation.showRetry('installMedia');
					return false;
				}

				ed.installation.syncDB();
			});
		},

		syncDB: function() {

			// Synchronize the database
			ed.installation.setActive('data-progress-syncdb');

			ed.ajax('sync', {}, function(result) {
				ed.installation.update('data-progress-syncdb', result, '45%');

				if (!result.state) {
					ed.installation.showRetry('syncDB');
					return false;
				}

				ed.installation.postInstall();
			});
		},

		postInstall : function() {
			
			// Perform post installation stuffs here
			ed.installation.setActive('data-progress-postinstall');

			ed.ajax('post', {}, function(result) {
				
				// Set the progress
				ed.installation.update('data-progress-postinstall', result, '100%');

				if (!result.state) {
					ed.installation.showRetry('postInstall');
					return false;
				}

				completed
					.removeClass('hide')
					.show();

				loading
					.addClass('hide');

				submit
					.removeClass('hide');

				submit.on('click', function() {

					source.val(ed.options.path);

					form.submit();
				});

			});
		},

		update: function(element, obj, progress) {
			var className = obj.state ? ' text-success' : ' text-error',
				stateMessage = obj.state ? 'Success' : 'Failed';
				stateIcon = obj.state ? 'icon-checkmark text-success' : 'icon-warning text-error';

			// Update the icon
			$('[' + element + ']')
				.find('.progress-icon > i')
				.removeClass('loader')
				.addClass(stateIcon);

			// Update the state
			$('[' + element + ']')
				.find('.progress-state')
				.html(stateMessage)
				.removeClass('text-info')
				.addClass(className);

			// Update the message
			$('[' + element + ']')
				.find('.notes')
				.html(obj.message)
				.removeClass('text-info')
				.addClass(className);

			$('[' + element + ']').removeClass('is-loading');
		},

		setActive: function(item) {
			$('[data-progress-active-message]').html($('[' + item + ']').find('.split__title').html() + ' ...');
			$('[' + item + ']').removeClass('pending').addClass('active is-loading');
			$('[' + item + ']').find('.progress-icon > i') .removeClass('icon-radio-unchecked') .addClass('loader');
		}
	}
}