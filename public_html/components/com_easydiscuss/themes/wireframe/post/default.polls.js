ed.require(['edq', 'easydiscuss'], function($, EasyDiscuss) {


    // Update the polls layout when something changes
    var updatePollsLayout = function(choices,totalVotes) {

        var items = $('[data-ed-poll-choice-item]');

        $.each(choices, function(i, choice) {

            var item = items.filter('[data-id=' + choice.id + ']');

            // Update the percentage
            item.find('[data-ed-poll-choice-percentage]')
                .css('width', choice.percentage + '%');

            // Update the count
            item.find('[data-ed-poll-choice-counter]')
                .html(choice.count);

            item.find('[data-ed-poll-choice-show-voters]')
                .data('count', choice.count);

            // Update the total vote as well
            $('[data-ed-post-poll-total-votes]').text(totalVotes);
        });
    };

    // When a checkbox value is changed
    var pollChoice = $('[data-ed-poll-choice-checkbox]');

    pollChoice.on('change.ed.poll.choice', function() {

        var item = $(this);
        var id = item.data('id');
        var postId = $('[data-ed-polls]').data('post-id');

        EasyDiscuss.ajax('site/views/polls/vote', {
            "id": id,
            "postId" : postId
        }).done(function(choices, totalVotes) {

            // Update the layout
            updatePollsLayout(choices, totalVotes);

        });

    });

    // When user clicks on vote count, display the voters
    var showVotersButton = $('[data-ed-poll-choice-show-voters]');

    showVotersButton.on('click.ed.show.voters', function() {

        var item = $(this);
        var parent = item.parents('[data-ed-poll-choice-item]');
        var id = parent.data('id');
        var wrapper = parent.find('[data-ed-poll-choice-voters]');
        var count = item.data('count');

        // Nothing to do here if there is nobody voted
        if (count == 0) {
            return;
        }

        // Add loading indicator
        parent.addClass('is-loading');

        EasyDiscuss.ajax('site/views/polls/getVoters', {
            "id": id
        }).done(function(output) {

            // Append the users
            wrapper.html(output)

            // Update the hidden
            wrapper.removeClass('t-hidden');
        }).always(function() {
            parent.removeClass('is-loading');
        });

    });

});
