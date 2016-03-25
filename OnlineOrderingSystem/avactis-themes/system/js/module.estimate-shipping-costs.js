$(function(){
    new AjaxManager({
        iSelector: '.button_calculate',
        instance: '.ShippingCalculator',
        updateAllInstances: true,
        beforeCB: function(instance){
            $('#subaction_id').val('calculate');
        },
        afterCB: function(instance){
            try {
                refreshStatesList('DstCountry', 'DstState_menu_select', 'stub_state_text_input');
                $("input[type='text']").addClass("input_text");
            } 
            catch(ex) {};    
        }
    });
    new AjaxManager({
        iSelector: '.button_remember',
        instance: '.ShippingCalculator',
        updateAllInstances: true,
        beforeCB: function(instance){
            $('#subaction_id').val('remember');
        },
        afterCB: function(instance){
            try {
                refreshStatesList('DstCountry', 'DstState_menu_select', 'stub_state_text_input');
                $("input[type='text']").addClass("input_text");
            } 
            catch(ex) {};    
        }
    });
});
