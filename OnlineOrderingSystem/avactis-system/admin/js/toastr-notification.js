function showToastMsg(shortCutFunction, msgOptions,msg,title){
	//If not defined shortcutfunction then set by default to info
	var defaultoptions = {
                    closeButton:true,
                    debug: false,
                    positionClass: 'toast-top-right',
                    onclick: null,
		    showDuration: 1000,
                    hideDuration: 1000,
		    timeOut: 5000,
	            showEasing: true,
                    extendedTimeOut: '1000',
                    showEasing: 'swing',
                    hideEasing: 'linear',
                    showMethod: 'fadeIn',
                    hideMethod: 'fadeOut',
               };

	toastr.options = jQuery.extend(defaultoptions, msgOptions);

       var toast =toastr[shortCutFunction](msg); // Wire up an event handler to a button in the toast, if it exists
}
