$(function(){    
    new AjaxManager({
        iSelector: '.button_add_to_cart',
        instance: '.ProductList .product_item',
        updateAllInstances: true
    });
    new AjaxManager({
        iSelector: '.button_add_to_cart',
        instance: '.ProductInfo',
        updateAllInstances: true
        /*beforeCB: function(instance){
            instance.initiator.hide();
        },
        afterCB: function(instance){
            instance.initiator.show();
        }*/
    });
    new AjaxManager({
        iSelector: '.del_mini_prod',
        instance: '.MiniCart',
        updateAllInstances: true
    });
});
