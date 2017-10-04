
kQuery(function($) {
    var options = {
            history: false,
            shareEl: true,
            shareButtons: [
                {id:'download', label: Koowa.translate('Download'), url:'{{raw_image_url}}', download:true}
            ],
            closeOnScroll: false,
            showAnimationDuration: 0,
            hideAnimationDuration: 0
        },
        pswpElement = document.querySelectorAll('.pswp')[0],
        openGallery = function(items, index) {
            options.index = index;

            var instance = new PhotoSwipe(pswpElement, PhotoSwipeUI_Default, items, options);
            instance.options.getImageURLForShare = function() {
                return instance.currItem.download_link || instance.currItem.src;
            };

            // Get data just in time for faster startup
            instance.listen('gettingData', function(index, item) {
                if (!item.src && !item.html) {
                    var element = item.el;

                    item.track = {
                        id: element.data('id'),
                        title: element.data('title')
                    };

                    if (element.hasClass('koowa_media__item__link--html')) {
                        item.html ='<iframe height="100%" width="100%" src="'+element.attr('href')+'"></iframe>';
                        item.download_link = element.attr('href');
                    } else {
                        item.src = element.attr('href');
                        item.w   = parseInt(element.data('width'), 10);
                        item.h   = parseInt(element.data('height'), 10);
                    }

                    if (element.find('.koowa_header__item')) {
                        item.title = $.trim(element.find('.koowa_header__item--title_container').text());
                    }
                }
            });

            instance.listen('imageLoadComplete', function(index, item) {
                if (item.src) {
                    $(document).trigger('photoswipeImageView', [item]);
                }
            });

            instance.init();
        },
        getGalleryItems = function(gallery) {
            var items = [];

            $(gallery).find('.k-js-gallery-item').each(function(i, element) {
                element = $(element);
                element.data('index', i);

                items.push({
                    el: element // save link to element for getThumbBoundsFn
                });
            });

            return items;
        };

        $('a.k-js-gallery-item').click(function( event ) {

            event.preventDefault();

            if ($(this).length) {

                var elements = getGalleryItems($(this).parents('.koowa_media--gallery'));

                if (elements) {
                    openGallery(elements, $(this).data('index'));
                }
            }
        });
});
