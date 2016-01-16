EasyDiscuss.module('polls', function($)
    {
	var module = this;

	EasyDiscuss.require()
	.view('field.form.polls.answer')
	.done(function($) {

		EasyDiscuss.Controller('Polls.Answers' ,
		{
			defaultOptions:
			{
				pollId: null,
				'{voteCount}' : '.voteCount',
				'{votersAvatar}': '.votersAvatar',
				'{votePoll}'	: '.votePoll',
				'{unvotePoll}'	: '.unvotePoll',
				'{pollGraph}'	: '.pollGraph'
			}
		},
		function(self)
		{
			return {
				init: function()
				{
					self.options.pollId	= self.element.data('id');
				},

				'{voteCount} click' : function()
				{
					disjax.load('polls' , 'getvoters' , self.options.pollId.toString());
				},

				'{votePoll} change' : function()
				{
					EasyDiscuss.ajax('site.views.polls.vote' ,
					{
						'id'	: self.options.pollId
					})
					.done(function(pollItems ) {


						$(pollItems).each(function(index , item ) {

							// Update the graph with the percentage.
							$('.pollAnswerItem-' + item.id).find('.pollPercentage').html(item.percentage);
							$('.pollAnswerItem-' + item.id).find('.pollGraph').css('width' , item.percentage + '%');

							// Update vote count.
							$('.pollAnswerItem-' + item.id).find('.voteCount').html(item.votes);

							// Update voters avatar
							$('.pollAnswerItem-' + item.id).find('.votersList').html(item.voters);
						});

					});
				}
			};
		});

		EasyDiscuss.Controller('Polls.Form',
			{
				defaultOptions:
				{
					// Poll answers
					'{pollAnswers}'	: '.pollAnswers',
					'{pollAnswersList}' : '.pollAnswersList',
					'{insertPollAnswer}'	: '.insertPollAnswer',

					// Enable poll checkbox
					'{pollCheckbox}'	: '.pollCheckbox',

					// Poll form wrapper
					'{pollForm}'	: '.pollForm',
					'{deletedPolls}'	: '#pollsremove',

					view:
					{
						answerItem: 'field.form.polls.answer'
					}
				}
			},
			function(self)
			{
				return {

					init: function()
					{

						// Implement subcontroller on each poll answer.
						self.pollAnswers().implement(EasyDiscuss.Controller.Polls.Form.Answer,
							{
								pollController: self
							});

						// If there's no answers on the page yet, append the default template on the page.
						if (self.pollAnswers().length == 0)
						{
							self.insertNewPollAnswer();
						}

					},

					resetPollForm: function(element)
					{
						self.pollAnswers(':not(:first)').remove();
						self.pollForm().hide();
						self.pollCheckbox().prop('checked', false);
					},

					insertNewPollAnswer: function(shiftFocus )
					{
						self.view.answerItem(
							{
								showRemove: self.pollAnswers().length > 0
							})
							.implement(EasyDiscuss.Controller.Polls.Form.Answer,
							{
								pollController: self,
								shiftFocus: shiftFocus
							})
							.appendTo(self.pollAnswersList());
					},

					updateDeletedPoll: function(id )
					{
						var current	= self.deletedPolls().val();

						if (current != '')
						{
							current += ',';
						}

						self.deletedPolls().val(current + id);
					},

					showPollForm: function(element )
					{
						self.pollForm().show();
					},

					'{insertPollAnswer} click' : function()
					{
						self.insertNewPollAnswer(true);
					},

					'{pollCheckbox} change' : function()
					{
						// Show multiple poll items.
						self.pollForm().toggle();
					}
				};
			});

		EasyDiscuss.Controller('Polls.Form.Answer',
		{
			defaultOptions:
			{
				'{answerText}'	: '.answerText',
				'{removeItem}'	: '.removeItem',

				pollController: null,
				shiftFocus: false
			}

		},
		function(self)
		{
			return {

				init: function()
				{
					if (self.options.shiftFocus)
					{
						self.answerText().focus();
					}
				},

				'{removeItem} click'	: function(element )
				{
					var id = $(element).data('pollid');

					if (id != null)
					{
						self.options.pollController.updateDeletedPoll(id);
					}

					self.element.remove();
				},

				'{answerText} keyup'	: function(element , event )
				{
					if (event.keyCode == $.ui.keyCode.ENTER)
					{
						self.options.pollController.insertNewPollAnswer(true);
					}
				}
			};
		});

		module.resolve();

	});

    });
