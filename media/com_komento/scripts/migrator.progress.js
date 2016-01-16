Komento.module('migrator.progress', function($) {
var module = this;
Komento.require().script('admin.language','komento.common').done(function() {
Komento.Controller(
	'Migrator.Progress',
	{
		defaults: {
			'{progressBar}': '.progressBar',
			'{progressStatus}': '.progressStatus',
			'{progressPercentage}': '.progressPercentage',
			'{logList}': '.logList',
			'{clearLog}': '.clearLog',
			'{totalComments}': '.totalComments',
			'{totalPosts}': '.totalPosts',
			'{migratedComments}': '.migratedComments'
		}
	},
	function(self)
	{ return {
		init: function() {
		},

		'{clearLog} click': function() {
			self.logList().html('');
		},

		setTotalPosts: function(data) {
			self.totalPosts().text(data);
		},

		setTotalComments: function(data) {
			self.totalComments().text(data);
		},

		updateMigratedComments: function(data) {
			var current = self.migratedComments().text();
			var newcount = parseInt(current) + parseInt(data);

			self.migratedComments().text( newcount );

			var totalComments = parseInt(self.totalComments().eq(0).text());
			var progress = Math.ceil((newcount / totalComments) * 100);

			self.progressBar().animate({
				width: progress.toString() + '%'
			});
			self.progressPercentage().text( progress );
		},

		log: function(data) {
			var time = new Date();
			var hour = time.getHours() > 9 ? time.getHours() : '0' + time.getHours();
			var minute = time.getMinutes() > 9 ? time.getMinutes() : '0' + time.getMinutes();
			var seconds = time.getSeconds() > 9 ? time.getSeconds() : '0' + time.getSeconds();
			var now = '[' + hour + ':' + minute + ':' + seconds + ']';

			var html = '<li>' + now + ' ' + data + '</li>';

			var height = self.logList()[0].scrollHeight;
			self.logList().append(html).scrollTop(height);
		}
	}}
);
module.resolve();
});
});
