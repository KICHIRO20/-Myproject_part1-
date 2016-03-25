jQuery.fn.DetailedProductInfo = function (settings) {
	var current_modifiers = {price: 0, weight: 0};
	var product_quantity_limit = undefined;
	var options_quantity_limit = undefined;
	var is_options_combination_available = true;
	
	var can_add_to_cart = true;
	var can_add_to_wishlist = true;
	
	var $product_info = $(this);
	var $quantity_selector = $product_info.find('select[name=quantity_in_cart]');
	var $options_error = $product_info.find('.options_error');
	
	$product_info.submit(function (event) {
		var action = $product_info.find('input[name=asc_action]').val();
		if ((action == 'AddToCart' && ! can_add_to_cart) || (action == 'AddToWishlist' && ! can_add_to_wishlist)) {
			event.preventDefault();
		}
		return true;
	});
	
	// Product options changing handler
	$product_info.find('.product_options').bind('options_change', function (event, parameters) {
		current_modifiers = parameters.modifiers;
		options_quantity_limit = parameters.quantity;
		is_options_combination_available = parameters.combination;
		
		if (current_modifiers) {
            var total_price = settings.sale_price + current_modifiers.price;
            if(total_price<0) total_price = 0;
			$product_info.find('.product_sale_price .value')
				.html(formatPrice(total_price, settings.currency_settings));
			if (current_modifiers.price && total_price >= settings.list_price) { 
				$product_info.find('.product_list_price, .discount_star').hide();
			}
			else {
				$product_info.find('.product_list_price, .discount_star').show();
			}
		}
		
		checkErrorState();
	});
	
	// Quantity dropdown changing handler
	$quantity_selector.change(function () {
		checkErrorState();
	});
	
	function checkErrorState()
	{
		$options_error.hide();
		can_add_to_cart = true;
		can_add_to_wishlist = true;
		
		if (! is_options_combination_available) {
			can_add_to_cart = false;
			can_add_to_wishlist = false;
			$options_error.html(settings.labels.inv_unavailable).show();
		}
		else if (options_quantity_limit == null) {
			// not in inventory
			if (settings.aanic != 'Y') {
				can_add_to_cart = false;
				can_add_to_wishlist = false;
				$options_error.html(settings.labels.comb_unavailable).show();
			}
		}
		else if (options_quantity_limit != undefined && options_quantity_limit < parseInt($quantity_selector.val())) {
			// out of stock
			if (settings.aanis != 'Y') {
    			can_add_to_cart = false;
    			can_add_to_wishlist = true;
    			if (options_quantity_limit > 0) {
    				$options_error.html(settings.labels.comb_limit_stock.replace('%quantity%', options_quantity_limit));
    			}
    			else {
    				$options_error.html(settings.labels.comb_out_of_stock);
    			}
    			$options_error.show();
			}
		}
		
		setButtonDisabled($product_info.find('.button_add_to_cart'), ! can_add_to_cart)
		setButtonDisabled($product_info.find('.add_to_wishlist'), ! can_add_to_wishlist)
	}
	
	function setButtonDisabled($button, disabled)
	{
		$button.attr('disabled', disabled);
		if (disabled) {
			$button.addClass('disabled');
		}
		else {
			$button.removeClass('disabled');
		}
	}
	
};
