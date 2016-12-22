"use strict";

kQuery(function($) {

    $.fn.downloadLabel = function ( options ) {

        if (navigator.mimeTypes == null || navigator.mimeTypes.length == 0) {
            return;
        }

        var settings = $.extend({
            'container' : '.docman_download_label',
            'label_play' : Koowa.translate('Play'),
            'label_view' : Koowa.translate('View'),
            'label_open' : Koowa.translate('Open'),
            'gdocs_preview' : 0,
            'supported_image_extensions' : ['jpg', 'jpeg', 'gif', 'png'],
            'gdocs_supported_extensions' : [
                'jpg',
                'jpeg',
                'gif',
                'png',
                'tiff',
                'tif',
                'xbm',
                'bmp'
            ]
        }, options);

        this.each(function(index, el){

            var label = $(el).find(settings.container);
            var mimetype = $(el).data('mimetype');
            var extension = $(el).data('extension');

            if (mimetype) {

                var tmp = mimetype.split("/");
                var content_type = tmp[0];

                if (content_type == 'image' && settings.supported_image_extensions.indexOf(extension) !== -1) {

                    label.text(settings.label_view);

                } else if (content_type == 'video' || content_type == 'audio') {

                    var media = document.createElement(content_type);
                    var can_play = media.canPlayType( mimetype );

                    if( can_play == 'maybe' || can_play == 'probably' ) {
                        label.text(settings.label_play);
                    }

                } else if (content_type == 'application') {

                    if (settings.gdocs_preview && settings.gdocs_supported_extensions.indexOf(extension) != -1) {
                        label.text(settings.label_view);
                    } else {

                        $.each(navigator.mimeTypes, function(index, mt) {
                            if ( mt.type == mimetype ) {
                                label.text( mt.type === 'application/pdf' ? settings.label_view : settings.label_open );
                                return false;
                            }
                        });

                    }
                }
            }

        });
    };
});
