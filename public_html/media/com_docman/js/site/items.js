"use strict";

kQuery(function($) {

    //delete items button
    var delete_items_btn = '#toolbar-delete';

    //use the toolbar delete button data for all delete buttons
    var request_params = $(delete_items_btn).data('params');
    var request_prompt = $(delete_items_btn).data('prompt');

    //delete item button
    var delete_item_btn = 'a[data-action="delete-item"]';

    //checkboxes
    var item_checkbox = 'input[name="item-select"]';

    var deletable, deletable_container;

    var getTreeDeletableContainers = function(elem)
    {
        var containers = [];

        if (elem.data('document'))
        {
            var uuid = elem.data('document');

            if ($('tr.footable-row-detail [data-document="' + uuid + '"]').length) {
                containers.push($('tr.footable-row-detail [data-document="' + uuid + '"]').closest('tr'));
            }

            if ($('tr.docman_item').find('[data-document="' + uuid + '"]').length) {
                containers.push($('tr.docman_item').filter('[data-document="' + uuid + '"]'));
            }
        }

        return containers;
    };

    //gallery view
    if($('.koowa_media--gallery').length) {
        deletable = '.koowa_media__item__content';
        deletable_container = '.koowa_media__item';
    // tree table layout
    } else if($('.docman_list_layout--tree .docman_table_layout').length) {
        deletable = delete_item_btn;
        deletable_container = getTreeDeletableContainers;
        // table layout
    } else if ($('.docman_table_layout').length) {
        deletable = delete_item_btn;
        deletable_container = 'tr.docman_item';
    } else {
        deletable = delete_item_btn;
        deletable_container = null;
    }

    $(delete_items_btn).addClass('k-is-disabled disabled').data('prompt', false);

    var deleteItem = function(element) {

        var elem   = $(element),
            path   = elem.data('url')    || elem.find(item_checkbox).data('url'),
            data   = elem.data('params') || request_params;

        if (path) {
            if (elem.data('ajax') === false) {
                new Koowa.Form({
                    'method': 'post',
                    'url'   : path,
                    'params': data
                }).submit();
            } else {
                $.ajax({
                    method : 'post',
                    url : path,
                    data : data,
                    beforeSend : function () {
                        elem.addClass('k-is-disabled disabled');
                    }
                }).done(function()
                {
                    var containers = [];

                    if(deletable_container)
                    {
                        if (typeof deletable_container !== 'function')
                        {
                            var selectors = deletable_container;

                            if (!Array.isArray(selectors)) {
                                selectors = [selectors];
                            }

                            $.each(selectors, function(idx, selector) {
                                containers.push(elem.closest(selector));
                            });
                        }
                        else containers = deletable_container(elem);
                    }
                    else containers.push(elem);

                    $.each(containers, function (idx, container) {
                        container.fadeOut(300, function () {
                            container.remove();
                        });
                    });
                });
            }
        }
  };

  //checkbox event handler
  $('body').on('click', item_checkbox, function( event ){

      $(this).closest(deletable).toggleClass('selected');

      if($(item_checkbox + ':checked').length) {
         $(delete_items_btn).removeClass('k-is-disabled disabled');
      } else {
         $(delete_items_btn).addClass('k-is-disabled disabled');
      }
  }).on('click', delete_item_btn, function( event ){
      //delete item event handler

      event.preventDefault();

      var $this = $(this),
          elem = $this.closest(deletable),
          prompt = request_prompt || $this.data('prompt');

      if ($this.hasClass('k-is-disabled') || $this.hasClass('disabled')) {
          return;
      }

      if (confirm(prompt)) {
          deleteItem(elem);
      }
  }).on('click', delete_items_btn, function ( event ) {

      event.preventDefault();

      var items = $(item_checkbox + ':checked');

      if(items.length && confirm(request_prompt)) {
          $.each(items, function(index, checkbox){
              var elem;
              if (deletable_container)
              {
                  if (typeof deletable_container === 'function') {
                      elem = $(checkbox).closest('tr');
                  } else {
                      elem = $(checkbox).parents(deletable_container).find(deletable);
                  }
              }
              else elem = $(checkbox).closest(deletable);

              deleteItem(elem);
          });
      }

      $(delete_items_btn).addClass('k-is-disabled disabled');
  });
});
