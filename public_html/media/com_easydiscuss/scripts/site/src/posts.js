ed.define('site/src/posts', function(){

    var opts = {

        allPostsFilter: '.allPostsFilter',
        newPostsFilter: '.newPostsFilter',
        unResolvedFilter: '.unResolvedFilter',

    }
        EasyDiscuss.Controller(
            'PostItems',
            {
                defaultOptions: {
                    activefiltertype: null,

                    '{allPostsFilter}'      : '.allPostsFilter',
                    '{newPostsFilter}'      : '.newPostsFilter',
                    '{unResolvedFilter}'    : '.unResolvedFilter',
                    '{unreadFilter}'        : '.unreadFilter',
                    '{resolvedFilter}'      : '.resolvedFilter',
                    '{unAnsweredFilter}'    : '.unAnsweredFilter',

                    '{sortLatest}'          : '.sortLatest',
                    '{sortPopular}'         : '.sortPopular',

                    '{ulList}'              : 'ul.normal',
                    '{emptyList}'           : 'div.empty',
                    '{loader}'              : '.loader',
                    '{pagination}'          : '.dc-pagination',

                    '{filterTab}'           : '[data-filter-tab]',
                    '{sortTab}'             : '[data-sort-tab]'
                }
            },
            function(self) { return {
                init: function() {
                },

                doSort: function(sortType ) {
                    self.sortTab()
                        .removeClass('active')
                        .filterBy("sortType", sortType)
                        .addClass("active");

                    filterType = self.options.activefiltertype;

                    self.doFilter(filterType, sortType);
                },

                doFilter: function(filterType, sortType) {

                    self.filterTab()
                        .removeClass('active')
                        .filterBy("filterType", filterType)
                        .addClass("active");


                    self.options.activefiltertype = filterType;

                    if (sortType === undefined) sortType = 'latest';

                    // show loading-bar
                    self.loader().show();

                    // hide empty div if there is any
                    self.emptyList().hide();


                    self.ulList().children('li').remove();

                    EasyDiscuss.ajax('site.views.index.filter' ,
                    {
                        'filter': filterType,
                        'sort'  : sortType,
                        'id'    : self.element.data('id'),
                        'view'  : self.element.data('view')
                    })
                    .done(function( str, pagination ){
                        // hide loading-bar
                        self.loader().hide();
                        self.ulList().append(str);
                        self.pagination().html(pagination);
                    })
                    .fail(function(){
                        // hide loading-bar
                        self.loader().hide();
                    })
                    .always(function(){

                    });
                },

                '{filterTab} click' : function(element)
                {
                    var filterType = element.data('filterType');
                    self.doFilter(filterType);
                },
                '{sortTab} click' : function(element)
                {
                    //$('.filterItem.secondary-nav').removeClass( 'active' );
                    //element.parent().addClass('active');
                    var sortType = element.data('sortType');
                    self.doSort( sortType );
                }

            }; //end return
            } //end function(self)

        );


});
