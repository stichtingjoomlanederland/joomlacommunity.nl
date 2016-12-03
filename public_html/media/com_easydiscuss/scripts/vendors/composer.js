ed.define('composer', ['edq', 'abstract', 'markitup', 'lodash', 'jquery.expanding', 'jquery.atwho'], function($, Abstract, markitup, _){

    var Composer = new Abstract(function(self) {

        return {
            opts: {
                editorType: null,
                operation: null,
                mentions: null,
                '{editor}': '[data-ed-editor]'
            },

            init: function() {

                // Composer operation
                self.options.operation = self.element.data('operation');

                // Render the markitup editor
                if (self.options.editorType == 'bbcode') {
                    self.editor()
                        .markItUp(self.options.bbcodeSettings)
                        .expandingTextarea();

                    // Apply mentions
                    if (self.options.mentions) {
                        self.editor()
                            .atwho({
                                at: "@",
                                highlightFirst: false,
                                minLen: 2,
                                delay: 200,
                                data: [],
                                limit: 10,
                                    callbacks: {
                                        remoteFilter: function(query, callback) {

                                            EasyDiscuss.ajax('site/views/users/search', {
                                                "query": query
                                            }).done(function(result) {

                                                var users = [];

                                                $.each(result, function(i, item) {
                                                    users.push(item.title);
                                                });

                                                callback(users);
                                            });

                                        },

                                        afterMatchFailed: function(at, el) {
                                            // 32 is spacebar
                                            if (at == '#') {
                                                tags.push(el.text().trim().slice(1));
                                                this.model.save(tags);
                                                this.insert(el.text().trim());
                                                return false;
                                            }
                                        }
                                    }
                            });
                    }
                }
            }
        }
    });

    return Composer;
});
