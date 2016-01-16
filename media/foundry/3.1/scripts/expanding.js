(function(){

// module factory: start

var moduleFactory = function($) {
// module body: start

var module = this; 
var exports = function() { 

// Expanding Textareas
// https://github.com/bgrins/ExpandingTextareas

    $.expandingTextarea = $.extend({
        autoInitialize: true,
        initialSelector: "textarea.expanding",
        opts: {
            resize: function() { }
        }
    }, $.expandingTextarea || {});
    
    var cloneCSSProperties = [
        'lineHeight', 'textDecoration', 'letterSpacing',
        'fontSize', 'fontFamily', 'fontStyle', 
        'fontWeight', 'textTransform', 'textAlign', 
        'direction', 'wordSpacing', 'fontSizeAdjust', 
        'wordWrap', 'word-break',
        'borderLeftWidth', 'borderRightWidth',
        'borderTopWidth','borderBottomWidth',
        'paddingLeft', 'paddingRight',
        'paddingTop','paddingBottom',
        'marginLeft', 'marginRight',
        'marginTop','marginBottom',
        'boxSizing', 'webkitBoxSizing', 'mozBoxSizing', 'msBoxSizing'
    ];
    
    var textareaCSS = {
        position: "absolute",
        height: "100%",
        resize: "none"
    };
    
    var preCSS = {
        visibility: "hidden",
        border: "0 solid",
        whiteSpace: "pre-wrap" 
    };
    
    var containerCSS = {
        position: "relative"
    };
    
    function resize() {

        var clone = $(this).data("textareaClone");
        clone.find("div").text(this.value.replace(/\r\n/g, "\n") + ' ');
        $(this).trigger("resize.expanding");
    }
    
    $.fn.expandingTextarea = function(o) {
        
        var opts = $.extend({ }, $.expandingTextarea.opts, o);
        
        if (o === "resize") {
            return this.trigger("input.expanding");
        }
        
        if (o === "destroy") {
            this.filter(".expanding-init").each(function() {
                // TODO: Restore container position value
                var textarea = $(this).removeClass('expanding-init');
                textarea
                    .attr('style', textarea.data('expanding-styles') || '')
                    .removeData('expanding-styles');
            });
            
            return this;
        }
        
        this.filter("textarea").not(".expanding-init").addClass("expanding-init").each(function() {

            var textarea  = $(this),
                container = textarea.parent(),
                clone     = $($.parseHTML("<pre class='textareaClone'><div></div></pre>"));

            textarea
                .after(clone)
                .data("textareaClone", clone);

            // Container
            container.css(containerCSS);
            
            // Store the original styles in case of destroying.
            textarea.data('expanding-styles', textarea.attr('style'));
            textarea.css(textareaCSS);

            // Clone
            clone.css(preCSS);
            
            $.each(cloneCSSProperties, function(i, p) {
                var val = textarea.css(p);
                
                // Only set if different to prevent overriding percentage css values.
                if (clone.css(p) !== val) {
                    clone.css(p, val);
                }
            });
            
            textarea.bind("input.expanding propertychange.expanding keyup.expanding", resize);
            resize.apply(this);
            
            if (opts.resize) {
                textarea.bind("resize.expanding", opts.resize);
            }
        });
        
        return this;
    };
    
    $(function () {
        if ($.expandingTextarea.autoInitialize) {
            $($.expandingTextarea.initialSelector).expandingTextarea();
        }
    });


}; 

exports(); 
module.resolveWith(exports); 

// module body: end

}; 
// module factory: end

FD31.module("expanding", moduleFactory);

}());