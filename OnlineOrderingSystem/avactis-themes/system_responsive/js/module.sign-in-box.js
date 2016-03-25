$(function(){
    new AjaxManager({
        iSelector: '.button_signin',
        instance: '.CustomerSignInBox',
        updateAllInstances: true
    });

    new AjaxManager({
        iSelector: '.sign_out_link',
        instance: '.CustomerSignInBox',
        method: 'GET',
        updateAllInstances: true
    });
});
