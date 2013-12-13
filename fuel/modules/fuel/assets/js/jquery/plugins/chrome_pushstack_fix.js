(function ($) {
    var pushStackOrig, pushStackChrome;

    pushStackOrig = $.fn.pushStack;

    pushStackChrome = function ( elems, name, selector ) {
        // Build a new jQuery matched element set

        // Invoke the correct constructor directly when the bug manifests in Chrome.
        //var ret = this.constructor();
        var ret = new jQuery.fn.init(); 

        if ( jQuery.isArray( elems ) ) {
            push.apply( ret, elems );

        } else {
            jQuery.merge( ret, elems );
        }

        // Add the old object onto the stack (as a reference)
        ret.prevObject = this;

        ret.context = this.context;

        if ( name === "find" ) {
            ret.selector = this.selector + ( this.selector ? " " : "" ) + selector;
        } else if ( name ) {
            ret.selector = this.selector + "." + name + "(" + selector + ")";
        }

        // Return the newly-formed element set
        return ret;
    };

    $.fn.pushStack = function (elems, name, selector) {
        var ret;

        try {
            ret = pushStackOrig.call(this, elems, name, selector);
            return ret;
        } 
        catch (e) {
            if (e instanceof TypeError) {
                if (!(ret instanceof jQuery.fn.init)) {
                    ret = pushStackChrome.call(this, elems, name, selector);
                    return ret;
                }
            }

            throw e;
        }
    };

}).call(this, jQuery);