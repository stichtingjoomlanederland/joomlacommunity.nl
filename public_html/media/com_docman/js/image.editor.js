
var Docman = Docman || {};

kQuery(function($) {

    Docman.ImageEditorFactory = {
        getEditor: function(type, options) {
            return new Docman.ImageEditor(options);
        }
    };

    Docman.ImageEditor = Koowa.Class.extend({
        loadedFile: {},
        getOptions: function() {
            return {
                site: null,
                baseurl: null,
                editorUrl: 'https://static.api.joomlatools.com/editor/',
                fileUrl: '?option=com_docman&view=documents&connect=1&image=1',
                connectToken: null,

                onBeforeInitialize: function (instance) {
                },
                onAfterInitialize: function () {
                },
                onSaveImage: function (data, response) {
                    console.log(data, response);
                },
            }
        },

        initialize: function(options) {
            this.supr();

            this.setOptions(options);

            if (!this.options.baseurl) {
                this.options.baseurl = this.options.site+'/'+this.options.fileUrl;
            }

            if (this.options.onBeforeInitialize) {
                this.options.onBeforeInitialize.call(this, this);
            }

            this.attachEvents();

            if (this.options.onAfterInitialize) {
                this.options.onAfterInitialize(this);
            }
        },
        closeModal: function() {
            if (typeof $.magnificPopup !== 'undefined' && $.magnificPopup.instance) {
                $.magnificPopup.close();
            }
        },
        openModal: function() {
            var editorUrl = this.options.editorUrl;

            $.magnificPopup.open({
                items: {
                    src: editorUrl,
                    type: 'iframe'
                },
                closeOnBgClick: false,
                mainClass: 'koowa_dialog_modal'
            });
        },
        checkOrigin: function(origin) {
            var parser = document.createElement('a');
            parser.href = this.options.editorUrl;
            return origin.indexOf(parser.origin) === 0;
        },
        saveImage: function(data) {
            var self = this;

            return new Promise(function(resolve, reject) {
                try {
                    var formdata = new FormData();
                    formdata.append("file", data.blob);
                    formdata.append("path", data.context.path);

                    var xhr = new XMLHttpRequest();
                    xhr.open('POST', self.options.baseurl+'&token='+data.context.token, true);
                    xhr.onerror = function() {reject("Network error.")};
                    xhr.onload = function() {
                        if (xhr.status === 200) {
                            self.options.onSaveImage(data, JSON.parse(xhr.response));
                            resolve(xhr.response);
                        }
                        else {
                            reject("Loading error:" + xhr.statusText)
                        }
                    };
                    xhr.send(formdata);
                }
                catch(err) {reject(err.message)}
            });
        },
        loadImage: function(path) {
            var self = this;

            var readyHandler = function(event) {
                console.log(event);
                if (self.checkOrigin(event.origin)) {
                    if (event.data.operation === 'ready') {
                        var image_url = self.options.baseurl+'&path='+path+'&token='+self.options.connectToken;

                        self.loadedFile = {
                            path: path,
                            token: self.options.connectToken,
                            site: self.options.site
                        };

                        self.convertImageToBlob(image_url).then(function(blob) {
                            document.querySelector('.mfp-iframe').contentWindow.postMessage({
                                operation: 'load',
                                //url: image_url,
                                blob: blob,
                                context: self.loadedFile
                            }, '*');

                            window.removeEventListener('message', readyHandler);
                        });

                    }
                }
            };

            window.addEventListener("message", readyHandler);

            this.closeModal();
            this.openModal();
        },

        convertImageToBlob: function(url) {
            return new Promise(function(resolve, reject) {
                try {
                    var xhr = new XMLHttpRequest();
                    xhr.open("GET", url);
                    xhr.responseType = "blob";
                    xhr.onerror = function() {reject("Network error.")};
                    xhr.onload = function() {
                        if (xhr.status === 200) {resolve(xhr.response)}
                        else {reject("Loading error:" + xhr.statusText)}
                    };
                    xhr.send();
                }
                catch(err) {reject(err.message)}
            });
        },

        attachEvents: function() {
            var self = this;

            window.addEventListener("message", function(event) {
                if (self.checkOrigin(event.origin)) {
                    if (event.data.operation === 'save') {
                        if (self.loadedFile && event.data.context && self.loadedFile.path === event.data.context.path) {
                            self.closeModal();

                            self.saveImage(event.data);
                        }
                    }
                }
            }, false);
        },

        encodeFilename: function(value) {
            value = encodeURI(value);

            var replacements = {'\\?': '%3F', '#': '%23'};

            for(var key in replacements)
            {   var regexp = new RegExp(key, 'g');
                value = value.replace(regexp, replacements[key]);
            }

            return value;
        },
        decodeFilename: function(value) {
            value = decodeURI(value);

            var replacements = {'%3F': '\\?', '%23': '#'};

            for(var key in replacements)
            {   var regexp = new RegExp(key, 'g');
                value = value.replace(regexp, replacements[key]);
            }

            return value;
        },
        forceReloadImage: function (src) {
            if (src instanceof $) {
                src = src.attr('src');
            }
            else if (src instanceof HTMLElement) {
                src = src.getAttribute('src');
            }

            function restoreImages(srcUrl, imgList) {
                for (var i = 0; i < imgList.length; i++) {
                    imgList[i].src = srcUrl;
                }
            }
            function prepareImagesForReload(srcUrl) {
                var result = $("img[src='" + srcUrl + "']").get();

                for (var i = 0; i < result.length; i++) {
                    /*
                    * Set the image to a reloading image, in this case an animated "reloading" svg
                    * Ideally this wont be displayed long enough to matter.
                        */
                    result[i].src = "data:image/svg+xml;charset=utf8,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100' preserveAspectRatio='xMidYMid' class='uil-reload'%3E%3Cpath fill='none' class='bk' d='M0 0h100v100H0z'/%3E%3Cg%3E%3Cpath d='M50 15a35 35 0 1 0 24.787 10.213' fill='none' stroke='%23777' stroke-width='12'/%3E%3Cpath d='M50 0v30l16-15L50 0' fill='%23777'/%3E%3CanimateTransform attributeName='transform' type='rotate' from='0 50 50' to='360 50 50' dur='1s' repeatCount='indefinite'/%3E%3C/g%3E%3C/svg%3E";
                }

                return result;
            }

            return new Promise(function(resolve, reject) {
                var imgList;
                var step = 0;
                var iframe = window.document.createElement("iframe");   // Hidden iframe, in which to perform the load+reload.

                /* Callback function, called after iframe load+reload completes (or fails).
                    Will be called TWICE unless twostage-mode process is cancelled. (Once after load, once after reload). */
                var iframeLoadCallback = function(e) {

                    if (step === 0) {
                        // initial load just completed.  Note that it doesn't actually matter if this load succeeded or not.

                        step = 1;
                        imgList = prepareImagesForReload(src);
                        iframe.contentWindow.location.reload(true); // initiate forced-reload!


                    } else if (step === 1) {
                        // forced re-load is done

                        restoreImages(src, imgList);
                        if (iframe.parentNode) iframe.parentNode.removeChild(iframe);

                        if ((e||window.event).type==="error") {
                            reject(e);
                        } else {
                            resolve(e);
                        }

                    }
                };

                iframe.style.display = "none";
                window.parent.document.body.appendChild(iframe); /* NOTE: if this is done AFTER setting src, Firefox MAY fail to fire the load event! */
                iframe.addEventListener("load",  iframeLoadCallback, false);
                iframe.addEventListener("error", iframeLoadCallback, false);
                iframe.src = src;
            });

        }
    });
});
