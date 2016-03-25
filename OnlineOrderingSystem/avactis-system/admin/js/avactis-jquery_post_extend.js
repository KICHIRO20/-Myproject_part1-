jQuery.extend({
post: function( url, data, callback, type ) {
                /* shift arguments if data argument was omited*/
                if ( jQuery.isFunction( data ) ) {
                        type = type || callback;
                        callback = data;
                        data = {};
                }

        data.__ASC_FORM_ID__ = __ASC_FORM_ID__;
                return jQuery.ajax({
                        type: "POST",
                        url: url,
                        data: data,
                        success: callback,
                        dataType: type
                });
        },
});
