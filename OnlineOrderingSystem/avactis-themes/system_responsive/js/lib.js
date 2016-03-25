ASCLIB = {
    getCurrentScroll: function(){
        return {
            left: document.body.scrollLeft
                                || document.documentElement.scrollLeft
                                || window.pageXOffset
                                || $(window).scrollLeft()
                                || 0,
            top: document.body.scrollTop
                                || document.documentElement.scrollTop
                                || window.pageYOffset
                                || $(window).scrollTop()
                                || 0
           }
    },
    
    getObjectSize: function(jObject){
        var size = {width: 0, height: 0}
        var children = jObject.children();
        var getSize = function(elem, size){
            var size = size || {width: 0, height: 0};
            size.width = size.width + ($.browser.opera ? $(elem).innerWidth() : $(elem).width())
                                    + (parseInt($(elem).css('margin-left')) || 0)
                                    + (parseInt($(elem).css('margin-right')) || 0)
                                    + (parseInt($(elem).css('padding-left')) || 0)
                                    + (parseInt($(elem).css('padding-right')) || 0);
            size.height = size.height + ($.browser.opera ? $(elem).innerHeight() : $(elem).height())
                                    + (parseInt($(elem).css('margin-top')) || 0)
                                    + (parseInt($(elem).css('margin-bottom')) || 0)
                                    + (parseInt($(elem).css('padding-top')) || 0)
                                    + (parseInt($(elem).css('padding-bottom')) || 0);
            return size;
        }

        if(children.length) $.each(jObject.children(), function(i,child){ size=getSize(child, size); });
        else size = getSize(jObject[0]);

        return size;
    },

    centerizeX: function(selector, addTopScroll, adjustToBottom){
        var scr = this.getCurrentScroll();
        $(selector).css('left', scr.left 
                                    + Math.round($(window).width()/2) 
                                    - Math.round(this.getObjectSize($(selector)).width/2) + 'px');
        var top = scr.top;
        if(adjustToBottom) 
            top = top + $(window).height() - this.getObjectSize($(selector)).height - 15;
        if(addTopScroll) 
            $(selector).css('top', top+'px');
    },

    centerizeY: function(selector, addLeftScroll, adjustToRight){
        var scr = this.getCurrentScroll();
        $(selector).css('top', scr.top 
                                    + Math.round($(window).height()/2) 
                                    - Math.round(this.getObjectSize($(selector)).height/2)+'px');
        var left = scr.left;
        if(adjustToRight) 
            left = left + $(window).width() - this.getObjectSize($(selector)).width;
        if(addLeftScroll) 
            $(selector).css('left', left+'px');
    },

    centerizeRelative: function(selector, relative){
        var offset = relative.offset();
        $(selector).css({
            left: offset.left+Math.round(relative.width()/2)-Math.round($(selector).width()/2)+'px',
            top: offset.top +Math.round(relative.height()/2)-Math.round($(selector).height()/2)+'px'
        });
    },

    centerize: function(selector){
        this.centerizeX(selector);
        this.centerizeY(selector);
    },

    jCollection2array: function(jObj){
        var arr = new Array();
        if(jObj && jObj.length)
            for(var i=0;i<jObj.length;i++)
                arr[i] = jObj[i];
        return arr;
    }
}
