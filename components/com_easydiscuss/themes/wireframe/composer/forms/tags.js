ed.require(['edq', 'chosen'], function($) {

    var selector = $('[data-ed-tags-select]');

    var checkCount = function(n) {
        var count = $('[data-ed-tags-list] .chosen-choices .search-choice').length;

        $('[data-ed-used-tags]').html(count);
    };

    // Checks if a tag already exist in the list
    var tagExists = function(tag) {
        var selected = [];

        selector
            .children(':selected')
            .each(function() {
                var val = $(this).val();

                if (val == "") {
                    return;
                }

                selected.push($(this).val());
            });

        var exists = $.inArray(tag, selected);

        return exists !== -1;
    }

    selector.chosen({
        no_results_text: "<?php echo JText::_('COM_EASYDISCUSS_NO_RESULTS', true);?>",
        max_selected_options: <?php echo $this->config->get('max_tags_allowed');?>
    });

    // Bind chosen tags
    selector.on('change', checkCount);

    <?php if ($this->config->get('main_allowcreatetag') && $this->acl->allowed('add_tag')) { ?>
    // Bind the search field so user's can add custom tags that don't exist.
    $('.search-field').bind('keyup', function(e) {

        var code = e.keyCode ? e.keyCode : e.which;

        // User hit the enter key
        if (code == 13) {
            var tag = $('.search-field :input').val();

            // Check if the tag really exists first.
            if (tagExists(tag)) {
                return;
            }

            $('[data-ed-tags-select]').append('<option value="' + tag + '" selected="selected">' + tag + '</option>');
            $('[data-ed-tags-select]').trigger('chosen:updated');
            
            // Update the counter
            checkCount();
        }
    });
    <?php } ?>
});