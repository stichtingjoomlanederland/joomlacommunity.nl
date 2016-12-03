ed.require(['edq', 'easydiscuss', 'selectize'], function($, EasyDiscuss) {

    var inputTags = $('[data-search-tags-label]');
    var inputCats = $('[data-search-categories-label]');

    $('[data-search-button]').click(function(){
        if ($('[data-search-text]').val().length == 0) {
            alert('please enter something to search.');
            return false;
        }

        $('[data-search-form]').submit();
    })

    inputTags.selectize({
        persist: false,
        createOnBlur: false,
        create: false,
        valueField: 'id',
        labelField: 'title',
        searchField: 'title',
        hideSelected: true,
        closeAfterSelect: true,
        selectOnTab: true,
        options: [],
        load: function(query, callback) {

            // If the query was empty, don't do anything here
            if (!query.length) {
                return callback();
            }

            // Search for users
            EasyDiscuss.ajax('site/views/search/filter', {
                "type": "tag",
                "query": query,
                "exclude": ""
            }).done(function(items) {
                callback(items);
            }).fail(function(msg) {
                // display message?
            });
        },

        onItemAdd: function(value, item) {

            var id = $(item).data('value');
            var text = $(item).text();

            var val = id + ":" + escape(text);

            var input = '<input type="hidden" name="tags[]" value="' + val  + '" data-id="' + id + '" data-title="' + escape(text) + '" data-search-tags />';

            $('[data-tags-container]').append(input);

        },

        onItemRemove: function(value, item) {
            $('[data-search-tags][data-title="' + value + '"]').remove();
        },

        render: {
            option: function(item, escape) {

                return '<div>' +
                    '<span class="title">' +
                        '<span class="name">' + escape(item.title) + '</span>' +
                    '</span>' +
                '</div>';
            }
        }
    });


    inputCats.selectize({
        persist: false,
        createOnBlur: false,
        create: false,
        valueField: 'id',
        labelField: 'title',
        searchField: 'title',
        hideSelected: true,
        closeAfterSelect: true,
        selectOnTab: true,
        options: [],
        load: function(query, callback) {

            // If the query was empty, don't do anything here
            if (!query.length) {
                return callback();
            }

            // Search for users
            EasyDiscuss.ajax('site/views/search/filter', {
                "type": "category",
                "query": query,
                "exclude": ""
            }).done(function(items) {
                callback(items);
            }).fail(function(msg) {
                // display message?
            });
        },

        onItemAdd: function(value, item) {

            var id = $(item).data('value');
            var text = $(item).text();

            var val = id + ":" + escape(text);

            var input = '<input type="hidden" name="categories[]" value="' + val  + '" data-id="' + id + '" data-title="' + escape(text) + '" data-search-categories />';

            $('[data-categories-container]').append(input);

        },

        onItemRemove: function(value, item) {
            $('[data-search-categories][data-title="' + value + '"]').remove();
        },

        render: {
            option: function(item, escape) {

                return '<div>' +
                    '<span class="title">' +
                        '<span class="name">' + escape(item.title) + '</span>' +
                    '</span>' +
                '</div>';
            }
        }
    });


});
