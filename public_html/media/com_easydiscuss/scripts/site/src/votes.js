ed.define('site/src/votes', ['edq', 'easydiscuss', 'abstract'], function($, EasyDiscuss, Abstract){

    return new Abstract(function(self){
        return {
            opts: {
            	id: null,
                "{voteButton}": "[data-ed-vote-button]",
            	"{counter}" : "[data-ed-vote-counter]"
            },

            init: function() {
				self.options.id = self.element.data('id');
            },

            vote: function(direction) {
				EasyDiscuss.ajax('site.views.votes.add', {
					'id': self.options.id,
					'type': direction
				}).done(function(total) {
					
                    self.counter().text(total);

				}).fail(function(message) {
                    EasyDiscuss.dialog({
                        "content": message
                    });
				});
            },

            "{voteButton} click": function(el) {
                var direction = el.data('direction');

                self.vote(direction);
            }
        }
    });

});