ed.define('site/src/profile', ['edq', 'easydiscuss', 'abstract'], function($, EasyDiscuss, Abstract){

    var Profile = new Abstract(function(self) { return {
        opts: {
            id: null,
            activefiltertype: null,
            activeSortType: null,
            '{tabs}': '[data-profile-tab]',
            '{tabContents}': '.tabContents',
            '{loader}': '.loader',
            '{itemList}': '[data-list-item]',
            '{emptyList}': '[data-list-empty]',
            '{emptyText}': '[data-list-empty-text]',
            '{pagination}': '[data-profile-pagination]',
            '{tabsTitle}': '[data-ed-tabs-content-title]',
            '{tabsTitleHidden}': '[data-ed-tabs-content-title-hidden]'
        },

        init: function() {
            
            this.options.userid = this.element.data('id');

            this.loader().hide();
            // Initialize tabs.
            this.initializeTabs();
        },

        initializeTabs: function() {
            // Find default tab.
            var defaultTab = this.options.defaultTab;

            // Check if there's an anchor already.
            var anchor  = $.uri(window.location).anchor();

            if (anchor) {
                defaultTab = anchor.charAt(0).toUpperCase() + anchor.slice(1);
            }

            // Set the default click
            //this.tabs('.tab' + defaultTab).click();
        },

        loadTabContents: function(filterType, content) {

            var list = this.itemList();
            var loader = this.loader();
            var emptyList = this.emptyList();
            var emptyText = this.emptyText();
            var pagination = this.pagination();
            var tabsTitle = this.tabsTitle();
            var tabsTitleHidden = this.tabsTitleHidden().text();

            this.tabs()
                .removeClass('active')
                .filterBy("filterType", filterType)
                .addClass("active");

            // show loading-bar
            loader.show();

            // Update the tabs title
            tabsTitle.empty();
            tabsTitle.append(tabsTitleHidden + ' - ' + content);

            // hide empty div if there is any
            emptyList.addClass('t-hidden');

            // list.children('li').remove();
            list.empty();

            EasyDiscuss.ajax('site.views.profile.tab', {
                    'type'  : filterType,
                    'id' : this.options.userid
                }).done(function(contents, paginationHTML, pageTitle, emptyString){

                    // Update page title
                    $(document).attr("title", pageTitle);

                    if (!contents) {
                        
                        // If there is a custom empty string passed, use it instead
                        if (emptyString) {
                            emptyText.html(emptyString);
                        }

                        emptyList.removeClass('t-hidden');
                        pagination.html('');
                    } else {
                        list.append(contents);
                        pagination.html(paginationHTML);
                    }
                    

                })
                .always(function(){
                    loader.hide();
                });
        },
 
        "{tabs} click": function(el) {

            var type = el.data('filterType');
            var content = el.text();
            
            this.loadTabContents(type, content);
            
        }
    }});

    return Profile;
});
