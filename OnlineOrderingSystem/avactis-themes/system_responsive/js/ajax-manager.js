function AjaxManager(settings) {

    this.method = 'POST';
    this.getURL = '';
    this.actionScript = window.location.href;     // target server script for AJAX requests

    // additional POST params
    this.postData = {};
    this.extPostData = {};

    // CSS selector which initiates AJAX requests
    this.iSelector = '';
    // CSS selector which is parent of iSelector
    this.instance = '';
    // update all instances on page
    this.updateAllInstances = true;

    // Array of DOM handling events for DOM-element (AJAX request initiator)
    this.domEvents = {
        click: null,                    // don't propogate event
        change: 'propogate'             // propogate event
    }; 

    this.enableMessages = true;         // enables after request message blobs
    this.enableAjaxLoaderImage = true;  // enables AJAX-loader animated image
    this.enableCoverlet = false;        // enables AJAX-coverlet

    this.beforeCB = null;               // before AJAX request 
    this.afterCB = null;                // after AJAX request

    this.ajaxTimeout = 10;              // timeout for AJAX request (seconds)

    // custom or overriding settings
    var self = this;
    if(typeof(settings) == 'object')
        $.each(settings, function(k, v){ 
            self[k] = v; 
        });

    this.postData.asc_ajax_req = 1;
    this.instance = this.instance.toLowerCase();

    window.AjaxManagerInstances = window.AjaxManagerInstances || {};
    var iid = this.instance + this.iSelector;
    window.AjaxManagerInstances[iid] = window.AjaxManagerInstances[this.iid] || [];
    window.AjaxManagerInstances[iid].push(this);
    this.init();
}

AjaxManager.prototype.init = function(){
    var self = this;

    if(!Array.indexOf){ // fix for stupid IE
        Array.prototype.indexOf = function(obj){ 
            for(var i=0; i<this.length; i++)
                if(this[i]===obj) return i; 
            return -1; 
        } 
    }

    if(this.enableAjaxLoaderImage) this.getLoader();
    if(this.enableMessages) this.getMessageBox();
    if(this.enableCoverlet) this.getCoverlet();

    this.toggleCtrls(true); // add event handlers

    if(!this.enableCoverlet)
        $(window).scroll(function(){
            if($('.ajax_loader').css('display')!='none') 
                ASCLIB.centerizeX('.ajax_loader', true);
            if($('.ajax_message_box').css('display')!='none') 
                ASCLIB.centerizeX('.ajax_message_box', true, true);
        });

    return this;
}

AjaxManager.prototype.getLoader = function(){
    $(".ajax_loader").appendTo("body");
    return this;
}

AjaxManager.prototype.getMessageBox = function(){
    $(".ajax_message_box").appendTo("body");
    return this;
}

AjaxManager.prototype.getCoverlet = function(){
    $(".ajax_coverlet").appendTo("body");
    return this;
}

AjaxManager.prototype.enableLoader = function(elem2cover){
    $('.ajax_loader').fadeIn(1,function(){
        if(elem2cover) ASCLIB.centerizeRelative('.ajax_loader', elem2cover);
        else ASCLIB.centerizeX('.ajax_loader', true);
    });
    return this;
}

AjaxManager.prototype.disableLoader = function(){
    var self = this;
    $('.ajax_loader').fadeOut(1, function(){
        if(self.enableCoverlet) $(".ajax_coverlet").fadeOut(200);
    });
    return this;
}

AjaxManager.prototype.toggleCoverlet = function(elem2cover){
    var self = this;
    $(".ajax_coverlet")
        .css({
            left: elem2cover.offset().left+'px',
            top: elem2cover.offset().top+'px',
            width: elem2cover.width()+'px',
            height: elem2cover.height()+'px'
        })
        .fadeTo(100,0.5,function(){ self.enableLoader(elem2cover);});
}

AjaxManager.prototype.showMessageBox = function(message, status){
    if(status == 'success') $('.ajax_message_box').removeClass('ajax_message_error');
    else $('.ajax_message_box').addClass('ajax_message_error');
    $('.ajax_message_box_text').html(message);
    
    $('.ajax_message_box').fadeIn(1, function(){
        ASCLIB.centerizeX('.ajax_message_box', true, true);
        $('.ajax_message_box_cross').click(function(){ $(this).parent().hide(); });
    });
    if(status == 'success') setTimeout(function(){$('.ajax_message_box').fadeOut(700);}, 4000);
    return this;
}

AjaxManager.prototype.getInstance = function(jElem) {
    var result = {instance:null, pos:null};

    if(jElem && jElem.parents && typeof(jElem.parents)=='function'){
        var all_instances = ASCLIB.jCollection2array($(this.instance));
        var cur_instance = ASCLIB.jCollection2array(jElem.parents(this.instance))[0];
        result.instance = $(cur_instance);
        result.pos = all_instances.indexOf(cur_instance);
    }

    return result;
}

AjaxManager.prototype.toggleCtrls = function(enable){
    var self = this;
    var domElements = $(this.instance).find(this.iSelector);
    if(domElements && domElements.length>0) {
        for(var i=0; i<domElements.length; i++) {
            var domElement = domElements[i];
            if(!domElement) continue;
            for(var domEvent in this.domEvents){
                var hdlr = function(e){
                    if(!self.domEvents.domEvent) {
                        e.stopPropagation ? e.stopPropagation() : e.cancelBubble = true;
                    }
                    e.preventDefault ? e.preventDefault() : e.returnValue = false;
                    self.initiator = $(this);
                    // dropdown handling
                    var onchangeurl = $(this).attr('onchangeurl');
                    if(onchangeurl) eval('self.getURL = ' + onchangeurl); 
                    // links handling
                    if(self.method.toLowerCase()=='get' && this.href) self.getURL = this.href;
                    // forms handling
                    self.post(this.form || $(this).parents('form')[0]);
                    return false;
                };
                $(domElement).unbind(domEvent).bind(domEvent, function(){return false}, false);
                if(enable) $(domElement).unbind(domEvent).bind(domEvent, hdlr);
            }
        }
    }
}

// action that should be done before POST request (ex. enable loader image)
AjaxManager.prototype.beforeRequest = function(){
    var self = this;

    this.tid = window.setTimeout(function(){
        self.postCB({status:'error', message:'<p>Server connection timeout. Please, try again later.</p>', data:{}}, true);
    }, this.ajaxTimeout*1000);

    if(this.beforeCB && typeof(this.beforeCB)=='function') this.beforeCB(this);
    this.toggleCtrls();  // disable ctrls
    if(this.enableAjaxLoaderImage && !this.enableCoverlet) this.enableLoader();
    return this;
}

// action that should be done after POST request (ex. disable loader image, show popup-message)
AjaxManager.prototype.afterRequest = function(){
    if(this.enableAjaxLoaderImage) this.disableLoader();

    // re-initialize all AjaxManager instances
    for(var iid in window.AjaxManagerInstances) 
        if(window.AjaxManagerInstances[iid].length)
            for(var i=0; i<window.AjaxManagerInstances[iid].length; i++)
                if(typeof(window.AjaxManagerInstances[iid][i].toggleCtrls)=='function')
                    window.AjaxManagerInstances[iid][i].toggleCtrls(true);

    if(this.afterCB && typeof(this.afterCB)=='function') this.afterCB(this);

    // add CSS classes (copied from avactis-themes/system/pages/templates/part.header.tpl.html)
    $("input[type='checkbox']").addClass("input_checkbox");
    $("input[type='radio']").addClass("input_radio");
    $("input[type='hidden']").addClass("input_hidden");
    $("input[type='password']").addClass("input_password");
    $("input[type='file']").addClass("input_file");
    $("input[type='text']").addClass("input_text");
    $("input[type='submit']").addClass("input_submit");
    return this;
}

AjaxManager.prototype.addPostData = function(obj){
    if(typeof(obj)=='object')
        for(var i in obj)
            if(typeof(i)=='string')
                this.postData[i] = obj[i];
    return this;
}

AjaxManager.prototype.customPostRequest = function(serverScript, postData, callback, format){
    if(!(serverScript && typeof(serverScript) == 'string')) return this;
    $.post(serverScript, postData, callback, format||'json');
    return this;
}

AjaxManager.prototype.postCB = function(response, died){
    $('.ajax_message_box, .ajax_loader, .ajax_coverlet').hide();
    window.clearTimeout(this.tid);
    if((this.enableMessages && response.message) || died)
        this.showMessageBox(response.message, response.status);
    if(!died) this.renderData(response.data);
    this.afterRequest();
    this.postData = { asc_ajax_req: 1 }; // truncate postData
}

AjaxManager.prototype.post = function(form){
    var self = this;
    var data = {};
    
    this.addPostData(this.extPostData);
    this.beforeRequest();
    if(this.enableCoverlet) {
        var instance = this.getInstance(this.initiator).instance;
        instance ? this.toggleCoverlet(instance) : this.enableLoader();
    }

    if(form && this.method.toLowerCase()=='post') {
        var serializedData = this.serializePostData(form);
        for(var i=0; i<serializedData.length; i++)
        {
            var SDi = serializedData[i];
            if(this.postData[SDi.name] && this.postData[SDi.name] !== SDi.value)
            {
                if(typeof(this.postData[SDi.name])!='array')
                    this.postData[SDi.name] = [this.postData[SDi.name], SDi.value];
                else this.postData[SDi.name].push(SDi.value);
            }
            else this.postData[SDi.name] = SDi.value;
        }

    }
    for(var i in this.postData) data[i] = this.postData[i];
    
    if(this.method.toLowerCase()=='get' && this.getURL)
        $.get(this.getURL, data, function(resp){ self.postCB(resp);}, 'json');
    else if(form && typeof(form.submit == 'function') && this.postData['asc_ajax_upload']) 
        $.browser == 'msie' ? form.submit() : this.iFramePost(form, data);
    else 
        $.post(this.actionScript, data, function(resp){ self.postCB(resp);}, 'json');

    return this;
}

AjaxManager.prototype.iFramePost = function(form, extPostData){
    var self = this;
    var id = new Date().getTime()
    $('.asc_ifr, .asc_iform').remove();

    var ifr = $('<iframe>', {name: 'ifr_'+id, className: 'asc_ifr'})
        .css({position: 'absolute', left: '-1000px', top: '-1000px'});

    ifr.load(function(){
        var content = this.contentWindow || this.contentDocument;
        eval($(content.document).find('body > script').text());
        if(resp) self.postCB(resp);
    });

    ifr.appendTo('body');

    var iform = $('<form>', {
            className: 'asc_iform', 
            method:'POST', 
            action: this.actionScript, 
            target: 'ifr_'+id,
            enctype: 'multipart/form-data'
    }).css({position: 'absolute', left: '-1000px', top: '-1000px'});

    var real = $(form).find('input[type=file]');
    var cloned = real.clone(true);
    real.hide();
    cloned.insertAfter(real)
    real.appendTo(iform);

    if(typeof(extPostData) == 'object')
    $.each(extPostData, function(k, v){
	     ktemp = k.replace(/\[/g,"\\[");
	     ktemp = ktemp.replace(/\]/g,"\\]");	
        if(iform.find('*[name='+ktemp+']').length==0)
            $('<input type="hidden" name="'+k+'" value="'+v+'" />').appendTo(iform);
    });

    iform.appendTo('body');
    iform.submit();
}

AjaxManager.prototype.serializePostData = function(form){
     var data = $(form).serializeArray();
     if(form.action){
        var m = form.action.match(/asc_action=([0-9a-zA-Z_]+)/);
        if(m && m[1]) data.push({name:'asc_action', value: m[1]});
     }

     var files = $(form).find('input[type=file]');     
     if(files.length) this.postData['asc_ajax_upload'] = 1;

     return data;
}

AjaxManager.prototype.renderData = function(data){
    for(var block in data) {
        var blocks = $('.'+block);
        if(!this.updateAllInstances) {
            var instanceData = this.getInstance(this.initiator);
            if(instanceData.instance) blocks = instanceData.instance;
        }
        blocks.fadeTo(200, 0.5).replaceWith(data[block]).fadeTo(200, 1);
    }

    return this;
}

AjaxManager.prototype.debug = function(data){
    $('<div>').text(data.toString()).appendTo('body');
}
